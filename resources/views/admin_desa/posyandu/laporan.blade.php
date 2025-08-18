<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Bulanan Posyandu</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 5px; }
        .header h3, .header p { margin: 0; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .kop-table { width: 100%; border-bottom: 3px double #000; margin-bottom:20px; }
        .kop-table td { vertical-align: middle; }
        .kop-table .logo { width: 80px; }
        .kop-table .logo img { width: 100%; }
        .kop-table .kop-text { text-align: center; }
        .table th, .table td { border: 1px solid #333; padding: 4px; text-align: left; }
        .table th { background-color: #f2f2f2; text-align: center; }
        .sub-header { font-weight: bold; background-color: #e9ecef; padding: 5px; margin-top: 15px; text-align: center; }
        .text-center { text-align: center; }
        .page-break { page-break-after: always; }
        .signature-table { margin-top: 40px; width: 100%; }
        .signature-table td { border: none; text-align: center; width: 50%; }
        .signature-name { margin-top: 60px; font-weight: bold; }
    </style>
</head>
<body>
    <table class="kop-table">
        <tr>
            <td class="logo">
                <img src="{{ public_path('storage/kop_surat/logoposyandu.jpg') }}" alt="Logo">
            </td>
            <td class="kop-text">
                <h3 style="margin: 0; font-size: 16pt;">{{ strtoupper($posyandu->nama_posyandu) }} | RW {{ $posyandu->rws->nomor_rw ?? 'N/A' }}</h3>
                <h2 style="margin: 0; font-size: 14pt;" class="text-bold">DESA {{ strtoupper($desa->nama_desa) }}</h2>
                <h3 style="margin: 0; font-size: 12pt;">KECAMATAN {{ strtoupper($desa->kecamatan) }} - {{ strtoupper($desa->kota) }}</h3>
            </td>
        </tr>
    </table>
    <div class="header">
        <h3>LAPORAN BULANAN KEGIATAN POSYANDU</h3>
        <p>{{ strtoupper($posyandu->nama_posyandu) }} | RW {{ $posyandu->rws->nomor_rw ?? 'N/A' }}</p>
        <p>PERIODE: {{ strtoupper($periode) }}</p>
    </div>

    <div class="sub-header">A. PROFIL & PARTISIPASI</div>
    <table class="table">
        <tr>
            <td style="width: 25%;"><strong>Nama Posyandu</strong></td>
            <td style="width: 25%;">{{ $posyandu->nama_posyandu }}</td>
            <td style="width: 25%;"><strong>Total Balita di Wilayah (S)</strong></td>
            <td style="width: 25%;">{{ $partisipasi['total_balita'] }} Anak</td>
        </tr>
        <tr><td><strong>Alamat</strong></td><td>{{ $posyandu->alamat }}</td><td><strong>Balita Hadir (K)</strong></td><td>{{ $partisipasi['hadir'] }} Anak</td></tr>
        <tr><td><strong>Jumlah Kader</strong></td><td>{{ $posyandu->kaders->count() }} Orang</td><td><strong>Balita Tidak Hadir</strong></td><td>{{ $partisipasi['tidak_hadir'] }} Anak</td></tr>
    </table>

    <div class="sub-header">B. DAFTAR NAMA KADER POSYANDU</div>
    <table class="table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th>Nama Kader</th>
                <th>Jabatan/Peran</th>
            </tr>
        </thead>
        <tbody>
            @forelse($posyandu->kaders as $kader)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $kader->nama_lengkap }}</td>
                <td>Kader Posyandu</td>
            </tr>@empty<tr>
                <td colspan="3" class="text-center">Belum ada data kader.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="sub-header">C. REKAPITULASI STATUS GIZI & INTERVENSI (DARI ANAK YANG HADIR)</div>
    <table class="table"><tr class="text-center"><th>Stunting</th><th>Gizi Kurang (Wasting)</th><th>Berat Badan Kurang</th><th>Dapat Vitamin A</th><th>Dapat Obat Cacing</th></tr><tr class="text-center"><td>{{ $stats['stunting'] }} Anak</td><td>{{ $stats['wasting'] }} Anak</td><td>{{ $stats['underweight'] }} Anak</td><td>{{ $stats['dapat_vitamin_a'] }} Anak</td><td>{{ $stats['dapat_obat_cacing'] }} Anak</td></tr></table>

    <div class="sub-header">D. DAFTAR ANAK DENGAN PERHATIAN KHUSUS</div>
    {{-- Tabel Stunting --}}
    <p style="font-weight:bold; margin-top:10px; margin-bottom:5px;">1. Anak dengan Status Stunting (Pendek / Sangat Pendek)</p>
    <table class="table"><thead><tr><th>No</th><th>Nama Anak</th><th>Usia</th><th>Status Stunting</th><th>Z-Score (TB/U)</th></tr></thead><tbody>@forelse($daftarAnakStunting as $p)<tr><td class="text-center">{{ $loop->iteration }}</td><td>{{ $p->warga->nama_lengkap ?? '' }}</td><td class="text-center">{{ $p->usia_saat_periksa }} bln</td><td class="text-center">{{ $p->status_stunting }}</td><td class="text-center">{{ $p->zscore_tb_u }}</td></tr>@empty<tr><td colspan="5" class="text-center">Tidak ada anak stunting.</td></tr>@endforelse</tbody></table>
    
    {{-- Tabel Wasting --}}
    <p style="font-weight:bold; margin-top:10px; margin-bottom:5px;">2. Anak dengan Status Gizi Kurang (Kurus / Sangat Kurus)</p>
    <table class="table"><thead><tr><th>No</th><th>Nama Anak</th><th>Usia</th><th>Status Gizi Kurang</th><th>Z-Score (BB/TB)</th></tr></thead><tbody>@forelse($daftarAnakWasting as $p)<tr><td class="text-center">{{ $loop->iteration }}</td><td>{{ $p->warga->nama_lengkap ?? '' }}</td><td class="text-center">{{ $p->usia_saat_periksa }} bln</td><td class="text-center">{{ $p->status_wasting }}</td><td class="text-center">{{ $p->zscore_bb_tb }}</td></tr>@empty<tr><td colspan="5" class="text-center">Tidak ada anak gizi kurang.</td></tr>@endforelse</tbody></table>

    {{-- Tabel Underweight --}}
    <p style="font-weight:bold; margin-top:10px; margin-bottom:5px;">3. Anak dengan Status Berat Badan Kurang</p>
    <table class="table"><thead><tr><th>No</th><th>Nama Anak</th><th>Usia</th><th>Status BB Kurang</th><th>Z-Score (BB/U)</th></tr></thead><tbody>@forelse($daftarAnakUnderweight as $p)<tr><td class="text-center">{{ $loop->iteration }}</td><td>{{ $p->warga->nama_lengkap ?? '' }}</td><td class="text-center">{{ $p->usia_saat_periksa }} bln</td><td class="text-center">{{ $p->status_underweight }}</td><td class="text-center">{{ $p->zscore_bb_u }}</td></tr>@empty<tr><td colspan="5" class="text-center">Tidak ada anak dengan berat badan kurang.</td></tr>@endforelse</tbody></table>

    <div class="page-break"></div>

    <div class="sub-header">E. RINCIAN DATA PEMERIKSAAN SEMUA ANAK YANG HADIR</div>
    <table class="table">
        <thead class="text-center">
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Nama Anak</th>
                <th rowspan="2">Usia (hari)</th>
                <th colspan="3">Antropometri</th>
                <th colspan="3">Status Gizi (Z-Score)</th>
                <th rowspan="2">Status</th>
            </tr>
            <tr>
                <th>BB</th>
                <th>TB</th>
                <th>LILA</th>
                <th>TB/U</th>
                <th>BB/TB</th>
                <th>BB/U</th>
            </tr>
        </thead>
        <tbody>
            @forelse($semuaPemeriksaan as $pemeriksaan)
            <tr class="text-center font-size: 9px;">
                <td>{{ $loop->iteration }}</td>
                <td class="text-left">{{ $pemeriksaan->warga->nama_lengkap ?? 'N/A' }}</td>
                <td>{{ $pemeriksaan->usia_saat_periksa }}</td>
                <td>{{ $pemeriksaan->berat_badan }}</td>
                <td>{{ $pemeriksaan->tinggi_badan }}</td>
                <td>{{ $pemeriksaan->lila ?? '-' }}</td>
                <td>{{ $pemeriksaan->zscore_tb_u }}</td>
                <td>{{ $pemeriksaan->zscore_bb_tb }}</td>
                <td>{{ $pemeriksaan->zscore_bb_u }}</td>
                <td>
                    <span class="badge bg-{{ $pemeriksaan->ringkasan_status_gizi_badge }}">
                        {{ $pemeriksaan->ringkasan_status_gizi }}
                    </span>
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center">Tidak ada data pemeriksaan pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>
    
    <table class="signature-table">
        <tr>
            <td>Mengetahui,<br>Kepala Desa</td>
            <td>{{ $tanggalCetak }}<br>Ketua Posyandu</td>
        </tr>
        <tr>
            <td><p class="signature-name">(........................................)</p></td>
            <td><p class="signature-name">(........................................)</p></td>
        </tr>
    </table>

</body>
</html>