<?php

namespace App\Http\Controllers;

use App\Models\RW;
use App\Models\RT;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\WargaImport;
use Maatwebsite\Excel\Validators\ValidationException;

class WargaImportController extends Controller
{
    /**
     * Menampilkan halaman form untuk impor data warga.
     */
    public function showImportForm()
    {
        $user = Auth::user();

        // Ambil RW dan RT sesuai scope user yang login
        $rws = RW::all(); // Global scope akan memfilter RW sesuai desa/RW user
        $rts = RT::all(); // Global scope akan memfilter RT sesuai desa/RW/RT user
        return view('admin_desa.warga.import', compact('rws', 'rts'));
    }

    /**
     * Memproses file Excel/CSV yang diunggah.
     * Mode bisa 'validate' (cek error) atau 'save' (simpan ke DB).
     */
    public function import(Request $request)
    {
        $request->validate([
            'rw_id'      => 'required|exists:rws,id',
            'rt_id'      => 'required|exists:rts,id',
            'file_warga' => 'required|mimes:xlsx,csv',
        ]);

        // Ambil mode dari request, default = validasi
        $mode = $request->input('mode', 'validate');
        $validateOnly = ($mode === 'validate');

        // Kirim RW & RT ke importer
        $importer = new WargaImport(
            $validateOnly,
            $request->rw_id,
            $request->rt_id
        );

        try {
            Excel::import($importer, $request->file('file_warga'));

            $summary = [
                'mode'    => $mode,
                'success' => $importer->successRowCount,
                'errors'  => $importer->getErrors(),
            ];

            // Kalau mode validasi → simpan file sementara untuk proses simpan berikutnya
            if ($validateOnly && count($summary['errors']) === 0) {
                $tempPath = $request->file('file_warga')->storeAs(
                    'temp_imports',
                    'warga_import_' . time() . '.' . $request->file('file_warga')->getClientOriginalExtension()
                );
                $summary['temp_file'] = $tempPath;
            }

            return redirect()
                ->route('warga.import.form')
                ->with('import_summary', $summary);

        } catch (ValidationException $e) {
            $errors = [];
            foreach ($e->failures() as $failure) {
                $errors[] = "Baris {$failure->row()}: Kolom '{$failure->attribute()}' -> " . implode(', ', $failure->errors());
            }
            $summary = [
                'mode'    => $mode,
                'success' => 0,
                'errors'  => $errors
            ];
            return redirect()
                ->route('warga.import.form')
                ->with('import_summary', $summary);
        }
    }


    /**
     * Menyimpan data warga dari file yang sudah divalidasi.
     */
    public function saveFromTemp(Request $request)
    {
        $request->validate([
            'temp_file' => 'required|string',
        ]);

        $tempPath = storage_path('app/public/' . $request->temp_file);
        
        if (!file_exists($tempPath)) {
            return back()->with('error', 'File sementara tidak ditemukan. Silakan ulangi proses impor.');
        }

        $importer = new WargaImport(false); // Mode simpan data

        try {
            Excel::import($importer, $tempPath);

            $summary = [
                'mode'    => 'save',
                'success' => $importer->successRowCount,
                'errors'  => $importer->getErrors(),
            ];

            // Hapus file temp setelah selesai
            unlink($tempPath);

            return redirect()
                ->route('warga.import.form')
                ->with('import_summary', $summary);

        } catch (ValidationException $e) {
            $errors = [];
            foreach ($e->failures() as $failure) {
                $errors[] = "Baris {$failure->row()}: Kolom '{$failure->attribute()}' -> " . implode(', ', $failure->errors());
            }
            return redirect()
                ->route('warga.import.form')
                ->with('import_summary', [
                    'mode'    => 'save',
                    'success' => 0,
                    'errors'  => $errors
                ]);
        }
    }

    public function downloadTemplate()
    {
        $filePath = public_path('templates/template_warga.xlsx');

        if (!file_exists($filePath)) {
            return back()->with('error', 'File template tidak ditemukan.');
        }

        $fileName = 'Template Impor Data Warga - ' . now()->format('Y-m-d') . '.xlsx';
        return response()->download($filePath, $fileName);
    }
}
