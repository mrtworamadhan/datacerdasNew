<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>LPJ - {{ $kegiatan->nama_kegiatan }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 14px;
            line-height: 1.6;
            margin-top: 75px;
            margin-bottom: 30px;
            margin-left: 30px;
            margin-right: 30px;
        }

        .cover {
            text-align: center;
            page-break-after: always;
        }

        .cover h1 {
            font-size: 20px;
            margin-top: 50px;
        }

        .cover h2 {
            font-size: 18px;
        }

        .cover p {
            text-align: center;
            font-size: 14px;
        }

        .cover img {
            width: 100px;
            margin-top: 30px;
        }

        .page-break {
            page-break-after: always;
        }

        h2 {
            text-align: center;
            font-size: 14px;
            margin-top: 25px;
        }

        .kop {
            width: 100%;
            margin-top: 5px;
            margin-bottom: 25px;
        }

        .kop-table {
            width: 100%;
            border-bottom: 3px double #000;
        }

        .kop-table td {
            vertical-align: middle;
        }

        .kop-table .logo {
            width: 80px;
        }

        .kop-table .logo img {
            width: 100%;
        }

        .kop-table .kop-text {
            text-align: center;
        }

        .header {
            text-align: center;
            font-weight: bold;
        }

        .sub-header {
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 11px;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top;
        }

        .table th {
            background-color: #f2f2f2;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .signature-table {
            margin-top: 50px;
            width: 100%;
            border: none;
        }

        .signature-table td {
            border: none;
            text-align: center;
        }

        .signature-name {
            margin-top: 60px;
            font-weight: bold;
            text-decoration: underline;
        }

        p {
            text-align: justify;
        }
    </style>
</head>

<body>
    {{-- Halaman Cover --}}
    <div class="cover">
        <img src="{{ public_path('storage/' . $penyelenggara->path_kop_surat) }}" alt="Logo"
            style="width:150px; height:auto;">
        <h1 style="margin-top: 150px;">LAPORAN PERTANGGUNGJAWABAN (LPJ)</h1>
        <h2>KEGIATAN {{ strtoupper($kegiatan->nama_kegiatan) }}</h2>
        <h3>{{ strtoupper($kegiatan->lokasi_kegiatan) }}</h3>
        <p style="text-align: center; margin-top: 50px;">Diselenggarakan oleh:</p>
        <h3>{{ strtoupper($penyelenggara->nama_lembaga ?? $penyelenggara->nama_kelompok) }}</h3>
        <p style="margin-top: 100px;">DESA {{ strtoupper($desa->nama_desa) }} <br> KECAMATAN
            {{ strtoupper($desa->kecamatan) }} - {{ strtoupper($desa->kota) }} <br> TAHUN
            {{ $kegiatan->tanggal_kegiatan->format('Y') }}</p>
    </div>

    {{-- Halaman Isi Laporan --}}
    <div class="kop mb-2">
        <table class="kop-table">
            <tr>
                <td class="logo">
                    @if($penyelenggara->path_kop_surat)
                        <img src="{{ public_path('storage/' . $penyelenggara->path_kop_surat) }}" alt="Logo">
                    @else
                        <img src="https://placehold.co/80x80?text=Logo" alt="Logo">
                    @endif
                </td>
                <td class="kop-text">
                    <h3 style="margin: 0; font-size: 16pt;">{{ strtoupper($penyelenggara->deskripsi) }}</h3>
                    <h3 style="margin: 0; font-size: 12pt;" class="text-bold">DESA {{ strtoupper($desa->nama_desa) }}
                    </h3>
                    <h3 style="margin: 0; font-size: 12pt;">KECAMATAN {{ strtoupper($desa->kecamatan) }} -
                        {{ strtoupper($desa->kota) }}
                    </h3>
                </td>
            </tr>
        </table>
    </div>

    <div class="sub-header">A. PENDAHULUAN</div>
    <p><strong>1. Latar Belakang</strong></p>
    <p>{!! nl2br(e($lpj->latar_belakang_lpj)) !!}</p>
    <p><strong>2. Tujuan Kegiatan</strong></p>
    <div>{!! nl2br(e($kegiatan->tujuan_kegiatan)) !!}</div>
    <p><strong>3. Ringkasan Kegiatan</strong></p>
    <table class="table">
        <tr>
            <td style="width: 30%;"><strong>Nama Kegiatan</strong></td>
            <td>{{ $kegiatan->nama_kegiatan }}</td>
        </tr>
        <tr>
            <td><strong>Penyelenggara</strong></td>
            <td>{{ $penyelenggara->nama_lembaga ?? $penyelenggara->nama_kelompok }}</td>
        </tr>
        <tr>
            <td><strong>Tanggal Pelaksanaan</strong></td>
            <td>{{ $kegiatan->tanggal_kegiatan->isoFormat('D MMMM YYYY') }}</td>
        </tr>
        <tr>
            <td><strong>Lokasi</strong></td>
            <td>{{ $kegiatan->lokasi_kegiatan }}</td>
        </tr>
    </table>

    <div class="sub-header">B. RINCIAN PELAKSANAAN KEGIATAN</div>
    <p>{!! nl2br(e($lpj->hasil_kegiatan)) !!}</p>

    <div class="sub-header">C. LAPORAN KEUANGAN</div>
    <table class="table">
        <tr>
            <td style="width: 50%;"><strong>Anggaran Diajukan (Proposal)</strong></td>
            <td class="text-right"><strong>Rp {{ number_format($kegiatan->anggaran_biaya, 2, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td><strong>Total Realisasi Anggaran (Pengeluaran)</strong></td>
            <td class="text-right"><strong>Rp {{ number_format($totalRealisasi, 2, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td style="background-color: #f2f2f2;"><strong>Sisa Anggaran</strong></td>
            <td class="text-right" style="background-color: #f2f2f2;"><strong>Rp
                    {{ number_format($sisaAnggaran, 2, ',', '.') }}</strong></td>
        </tr>
    </table>
    <p>Terbilang: <i>{{ ucwords($terbilangRealisasi) }} Rupiah</i></p>

    <div class="page-break"></div>

    <div class="sub-header">D. RINCIAN PENGELUARAN DANA</div>
    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Uraian</th>
                <th>Tipe</th>
                <th>Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengeluarans as $pengeluaran)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">{{ $pengeluaran->tanggal_transaksi->format('d/m/Y') }}</td>
                    <td>
                        {{ $pengeluaran->uraian }}
                        @if($pengeluaran->tipe_pengeluaran == 'Pembelian Pesanan')
                            <small style="display: block;">Penyedia: {{ $pengeluaran->penyedia }}</small>
                        @elseif($pengeluaran->tipe_pengeluaran == 'Upah Kerja')
                            <small style="display: block;">Penerima: {{ $pengeluaran->nama_pekerja }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $pengeluaran->tipe_pengeluaran }}</td>
                    <td class="text-right">{{ number_format($pengeluaran->jumlah, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada catatan pengeluaran.</td>
                </tr>
            @endforelse
            <tr>
                <th colspan="4" class="text-center">TOTAL REALISASI</th>
                <th class="text-right">Rp {{ number_format($totalRealisasi, 2, ',', '.') }}</th>
            </tr>
        </tbody>
    </table>

    <div class="sub-header">E. Evaluasi</div>
    <p>{!! nl2br(e($lpj->evaluasi_kendala)) !!}</p>

    <div class="sub-header">F. PENUTUP</div>
    <p>{!! nl2br(e($lpj->rekomendasi_lanjutan)) !!}</p>

    {{-- Halaman Pengesahan --}}
    <table class="signature-table">
        <tr>
            <td></td>
            <td>Desa, {{ $tanggalCetak->isoFormat('D MMMM YYYY') }}</td>
        </tr>
        <tr>
            <td>Menyetujui,<br>Kepala Desa</td>
            <td>Hormat kami,<br>Ketua Pelaksana</td>
        </tr>
        <tr>
            <td>
                <p class="signature-name">({{ strtoupper($desa->nama_kades) }})</p>
            </td>
            <td>
                <p class="signature-name">
                    {{-- Di sini bisa kita isi nama ketua lembaga/kelompok jika ada --}}
                    ({{ $ketua->nama_pengurus ?? '........................................' }})
                </p>
            </td>
        </tr>
    </table>
</body>

</html>