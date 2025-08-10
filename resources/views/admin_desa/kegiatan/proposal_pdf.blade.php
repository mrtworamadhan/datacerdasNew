<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Proposal Kegiatan: {{ $kegiatan->nama_kegiatan }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 14px;
            line-height: 1.5;
            margin-top: 30px;
            margin-bottom: 30px;
            margin-left: 30px;
            margin-right: 30px;
        }

        .header,
        .footer {
            text-align: center;
        }

        .content {
            margin-top: 15px;
            margin-bottom: 30px;
            margin-left: 30px;
            margin-right: 30px;
        }

        h1 {
            text-align: center;
            font-size: 16px;
        }

        h2 {
            text-align: left;
            font-size: 14px;
            margin-top: 25px;
        }
        .kop-table { width: 100%; border-bottom: 3px double #000; }
        .kop-table td { vertical-align: middle; }
        .kop-table .logo { width: 80px; }
        .kop-table .logo img { width: 100%; }
        .kop-table .kop-text { text-align: center; }

        p {
            text-align: justify;
        }

        .signature-table {
            margin-top: 50px;
            width: 100%;
        }

        .signature-table td {
            border: none;
            text-align: center;
            width: 50%;
        }

        .signature-name {
            margin-top: 60px;
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="header mb-2">
        <table class="kop-table">
            <tr>
                <td class="logo">
                    @if($penyelenggara->path_kop_surat)
                        <img src="{{ public_path('storage/' . $penyelenggara->path_kop_surat) }}" alt="Logo" >
                    @else
                        <img src="https://placehold.co/80x80?text=Logo" alt="Logo">
                    @endif
                </td>
                <td class="kop-text">
                    <h3 style="margin: 0; font-size: 16pt;">{{ strtoupper($penyelenggara->deskripsi) }}</h3>
                    <h3 style="margin: 0; font-size: 12pt;" class="text-bold">DESA {{ strtoupper($desa->nama_desa) }}
                    </h3>
                    <h3 style="margin: 0; font-size: 12pt;">KECAMATAN {{ strtoupper($desa->kecamatan) }} -
                        {{ strtoupper($desa->kota) }}</h3>
                </td>
            </tr>
        </table>
    </div>
    <div class = "title">
        <h1 style="margin: 0; margin-top: 3; font-size: 16pt;" class="text-bold">
            PROPOSAL KEGIATAN
        </h1>
        <h1 style="margin: 0; margin-top: 3; font-size: 16pt;" class="text-bold">
            {{ strtoupper($kegiatan->nama_kegiatan) }} -  {{ strtoupper($kegiatan->lokasi_kegiatan) }}    
        </h1>
    </div>
    
    <div class="content">
        <h2>A. LATAR BELAKANG</h2>
        <p>{!! nl2br(e($kegiatan->latar_belakang)) !!}</p>

        <h2>B. TUJUAN KEGIATAN</h2>
        <p>{!! nl2br(e($kegiatan->tujuan_kegiatan)) !!}</p>

        <h2>C. DESKRIPSI KEGIATAN</h2>
        <p>{!! nl2br(e($kegiatan->deskripsi_kegiatan)) !!}</p>

        <h2>D. WAKTU DAN TEMPAT</h2>
        <p>
            Hari, Tanggal : {{ $kegiatan->tanggal_kegiatan->isoFormat('dddd, D MMMM YYYY') }} <br>
            Tempat : {{ $kegiatan->lokasi_kegiatan }}
        </p>

        <h2>E. RENCANA ANGGARAN BIAYA (RAB)</h2>
        <p>Total Estimasi Anggaran: <strong>Rp {{ number_format($kegiatan->anggaran_biaya, 0, ',', '.') }}</strong></p>
        <p>Sumber Dana: <strong>{{ $kegiatan->sumber_dana }}</strong></p>
        <div>{!! \Illuminate\Support\Str::markdown($kegiatan->laporan_dana) !!}</div>

        <h2>F. PENUTUP</h2>
        <p>{!! nl2br(e($kegiatan->penutup)) !!}</p>
    </div>

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