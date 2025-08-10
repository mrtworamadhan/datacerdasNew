<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePemeriksaanAnakRequest;
use App\Services\StuntingCalculatorService;
use App\Models\DataKesehatanAnak;
use App\Models\PemeriksaanAnak;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PemeriksaanAnakController extends Controller
{
    /**
     * Menyimpan data pemeriksaan baru dan menghitung semua status gizi.
     */
    public function store(StorePemeriksaanAnakRequest $request, string $subdomain, DataKesehatanAnak $dataKesehatanAnak, StuntingCalculatorService $calculator)
    {
        // Ambil data yang sudah divalidasi dan "dibersihkan" (koma jadi titik)
        $validated = $request->validated();

        // Siapkan data dasar untuk kalkulator
        $warga = $dataKesehatanAnak->warga; // Relasi ini aman karena diambil dari data induk
        $jenisKelamin = $warga->jenis_kelamin;
        $tanggalLahir = Carbon::parse($warga->tanggal_lahir);
        $tanggalPengukuran = Carbon::parse($validated['tanggal_pemeriksaan']);
        $umurDalamHari = $tanggalLahir->diffInDays($tanggalPengukuran);
        $beratBadan = $validated['berat_badan'];
        $tinggiBadan = $validated['tinggi_badan'];

        // --- PANGGIL SEMUA KALKULATOR ---
        $zscore_tb_u = $calculator->calculateHaz($jenisKelamin, $umurDalamHari, $tinggiBadan);
        $zscore_bb_u = $calculator->calculateWaz($jenisKelamin, $umurDalamHari, $beratBadan);
        $zscore_bb_tb = $calculator->calculateWhz($jenisKelamin, $tinggiBadan, $beratBadan);

        // --- TENTUKAN SEMUA STATUS GIZI ---
        $status_stunting = $this->getStatusStunting($zscore_tb_u);
        $status_underweight = $this->getStatusUnderweight($zscore_bb_u);
        $status_wasting = $this->getStatusWasting($zscore_bb_tb);

        $age = Carbon::parse($tanggalLahir)->diff($tanggalPengukuran);
        $usiaBulan = $age->y * 12 + $age->m;
        $usiaHari = $age->d;
        $usiaFormatted = "{$usiaBulan} bulan, {$usiaHari} hari";
        // Buat record pemeriksaan baru dengan semua data lengkap
        $dataKesehatanAnak->riwayatPemeriksaan()->create([
            'tanggal_pemeriksaan'   => $validated['tanggal_pemeriksaan'],
            'posyandu_id'           => $dataKesehatanAnak->posyandu_id,
            'berat_badan'           => $beratBadan,
            'tinggi_badan'          => $tinggiBadan,
            'lila'                  => $validated['lila'] ?? null,
            
            'zscore_tb_u'           => $zscore_tb_u,
            'status_stunting'       => $status_stunting,
            'zscore_bb_u'           => $zscore_bb_u,
            'status_underweight'    => $status_underweight,
            'zscore_bb_tb'          => $zscore_bb_tb,
            'status_wasting'        => $status_wasting,
            
            'dapat_vitamin_a'       => $request->boolean('dapat_vitamin_a'),
            'dapat_obat_cacing'     => $request->boolean('dapat_obat_cacing'),
            'dapat_imunisasi_polio' => $request->boolean('dapat_imunisasi_polio'),
            'catatan_kader'         => $validated['catatan_kader'] ?? null,
            'usia_saat_periksa'     => $usiaFormatted,
        ]);
        
        return back()->with('status', 'Data pemeriksaan berhasil disimpan!');
    }

    /**
     * Menampilkan form untuk mengedit record di dalam modal.
     */
    public function edit( string $subdomain,PemeriksaanAnak $pemeriksaanAnak)
    {
        return view('admin_desa.kesehatan_anak.partials.edit_form_modal', [
            'pemeriksaan' => $pemeriksaanAnak
        ])->render();
    }

    /**
     * Update data pemeriksaan yang sudah ada dan hitung ulang semua status gizi.
     */
    public function update(StorePemeriksaanAnakRequest $request, string $subdomain, PemeriksaanAnak $pemeriksaanAnak, StuntingCalculatorService $calculator)
    {
        $validated = $request->validated();
        $pemeriksaanAnak->load('warga'); // Pastikan relasi warga dimuat

        $warga = $pemeriksaanAnak->warga;
        $jenisKelamin = $warga->jenis_kelamin;
        $tanggalLahir = Carbon::parse($warga->tanggal_lahir);
        $tanggalPengukuran = Carbon::parse($pemeriksaanAnak->tanggal_pemeriksaan); // Tanggal pemeriksaan tidak bisa diubah saat edit
        $umurDalamHari = $tanggalLahir->diffInDays($tanggalPengukuran);
        $beratBadan = $validated['berat_badan'];
        $tinggiBadan = $validated['tinggi_badan'];

        // --- PANGGIL SEMUA KALKULATOR (LAGI) ---
        $zscore_tb_u = $calculator->calculateHaz($jenisKelamin, $umurDalamHari, $tinggiBadan);
        $zscore_bb_u = $calculator->calculateWaz($jenisKelamin, $umurDalamHari, $beratBadan);
        $zscore_bb_tb = $calculator->calculateWhz($jenisKelamin, $tinggiBadan, $beratBadan);

        // --- TENTUKAN SEMUA STATUS GIZI (LAGI) ---
        $status_stunting = $this->getStatusStunting($zscore_tb_u);
        $status_underweight = $this->getStatusUnderweight($zscore_bb_u);
        $status_wasting = $this->getStatusWasting($zscore_bb_tb);

        // Gabungkan semua data untuk di-update
        $pemeriksaanAnak->update([
            'berat_badan'           => $beratBadan,
            'tinggi_badan'          => $tinggiBadan,
            'lila'                  => $validated['lila'] ?? null,
            
            'zscore_tb_u'           => $zscore_tb_u,
            'status_stunting'       => $status_stunting,
            'zscore_bb_u'           => $zscore_bb_u,
            'status_underweight'    => $status_underweight,
            'zscore_bb_tb'          => $zscore_bb_tb,
            'status_wasting'        => $status_wasting,

            'dapat_vitamin_a'       => $request->boolean('dapat_vitamin_a'),
            'dapat_obat_cacing'     => $request->boolean('dapat_obat_cacing'),
            'dapat_imunisasi_polio' => $request->boolean('dapat_imunisasi_polio'),
            'catatan_kader'         => $validated['catatan_kader'] ?? null,
        ]);
        
        return redirect()->route('kesehatan-anak.show', $pemeriksaanAnak->data_kesehatan_anak_id)
                         ->with('status', 'Data pemeriksaan berhasil di-update!');
    }

    // --- FUNGSI BANTUAN UNTUK MENENTUKAN STATUS (AGAR KODE TIDAK BERULANG) ---
    private function getStatusStunting($zscore) {
        if ($zscore === null) return 'N/A';
        if ($zscore < -3) return 'Sangat Pendek (Stunting Berat)';
        if ($zscore < -2) return 'Pendek (Stunting)';
        return 'Normal';
    }

    private function getStatusUnderweight($zscore) {
        if ($zscore === null) return 'N/A';
        if ($zscore < -3) return 'Berat Badan Sangat Kurang';
        if ($zscore < -2) return 'Berat Badan Kurang';
        return 'Berat Badan Normal';
    }

    private function getStatusWasting($zscore) {
        if ($zscore === null) return 'N/A';
        if ($zscore < -3) return 'Gizi Buruk (Sangat Kurus)';
        if ($zscore < -2) return 'Gizi Kurang (Kurus)';
        if ($zscore > 2) return 'Gizi Lebih (Overweight)';
        if ($zscore > 3) return 'Obesitas';
        return 'Gizi Baik (Normal)';
    }
}