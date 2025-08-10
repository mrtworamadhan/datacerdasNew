<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\WargaImport;
use Maatwebsite\Excel\Validators\ValidationException; // <-- Tambahkan ini

class WargaImportController extends Controller
{
    /**
     * Menampilkan halaman form untuk impor data warga.
     */
    public function showImportForm()
    {
        return view('admin_desa.warga.import');
    }

    /**
     * Memproses file Excel/CSV yang diunggah.
     */
    public function import(Request $request)
    {
        $request->validate([ 'file_warga' => 'required|mimes:xlsx,csv' ]);

        // Buat instance dari "mesin" impor kita
        $importer = new WargaImport();
        
        try {
            // Jalankan proses impor
            Excel::import($importer, $request->file('file_warga'));

            // Siapkan laporan ringkas untuk ditampilkan
            $summary = [
                'success' => $importer->successRowCount,
                'errors' => $importer->getErrors(),
            ];
            
            return redirect()->route('warga.import.form')
                            ->with('import_summary', $summary);

        } catch (ValidationException $e) {
            // Tangkap error dari validasi Maatwebsite dan format agar mudah dibaca
            $errors = [];
            foreach ($e->failures() as $failure) {
                $errors[] = "Baris {$failure->row()}: Kolom '{$failure->attribute()}' -> " . implode(', ', $failure->errors());
            }
            $summary = ['success' => 0, 'errors' => $errors];
            return redirect()->route('warga.import.form')
                            ->with('import_summary', $summary);
        }
    }

    public function downloadTemplate()
    {
        // Path ke file template di dalam folder public
        $filePath = public_path('templates/template_warga.xlsx');

        // Cek jika file ada untuk menghindari error
        if (!file_exists($filePath)) {
            return back()->with('error', 'File template tidak ditemukan.');
        }

        // Siapkan nama file yang akan diunduh oleh pengguna
        $fileName = 'Template Impor Data Warga - ' . now()->format('Y-m-d') . '.xlsx';

        // Kembalikan file sebagai respons unduhan
        return response()->download($filePath, $fileName);
    }
}