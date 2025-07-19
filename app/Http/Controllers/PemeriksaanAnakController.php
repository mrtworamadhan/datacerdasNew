<?php

namespace App\Http\Controllers;

use App\Models\DataKesehatanAnak;
use App\Models\PemeriksaanAnak;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PemeriksaanAnakController extends Controller
{
    public function store(Request $request, DataKesehatanAnak $dataKesehatanAnak)
    {
        $validated = $request->validate([
            'tanggal_pemeriksaan' => 'required|date',
            'berat_badan' => 'required|numeric',
            'tinggi_badan' => 'required|numeric',
            'status_gizi' => 'required|string',
            'imunisasi_diterima' => 'nullable|string',
            'vitamin_a_diterima' => 'nullable|boolean',
            'obat_cacing_diterima' => 'nullable|boolean',
            'catatan_kader' => 'nullable|string',
        ]);

        // Hitung usia saat pemeriksaan
        $tglLahir = Carbon::parse($dataKesehatanAnak->tanggal_lahir);
        $tglPeriksa = Carbon::parse($validated['tanggal_pemeriksaan']);
        $validated['usia_saat_periksa'] = $tglLahir->diffInMonths($tglPeriksa);

        // Tambahkan data pemeriksaan baru
        $dataKesehatanAnak->riwayatPemeriksaan()->create($validated);

        return redirect()->back()->with('success', 'Data pemeriksaan berhasil ditambahkan.');
    }
}
