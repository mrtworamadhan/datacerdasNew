<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Surat - {{ $pengajuanSurat->jenisSurat->nama_surat }}</title>
    <style>
        @page {
            margin-left: 1in;
            margin-right: 1in;
            margin-top: 0.2in;
            margin-bottom: 0.75in;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            margin-top: 0;
        }

        .kop-surat {
            text-align: center;
            border-bottom: 2px double #000;
            padding-bottom: 5px;
            margin-top: 0rem;
        }

        .kop-surat img {
            max-width: 100%;
            height: auto;
        }

        .judul-surat {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin-bottom: 0.25rem;
        }

        .nomor-surat {
            text-align: center;
            margin-top: 0;
            margin-bottom: 0.25rem;
        }

        .isi-surat {
            text-align: justify;
            margin-bottom: 0.25rem;
        }

        .tanda-tangan {
            margin-top: 50px;
            width: 40%;
            float: right;
            text-align: center;
        }

        .tanda-tangan .nama {
            font-weight: bold;
            text-decoration: underline;
        }
        
        .isi-surat table,
        .isi-surat th,
        .isi-surat td {
            border: none !important;
            padding: 2px 4px !important;
            line-height: 1.5 !important;
            vertical-align: middle !important;
        }
        .isi-surat table tr td:first-child {
            width: 25%;
            /* Kolom pertama fix 25% */
        }

        .isi-surat table tr td:nth-child(2) {
            width: 5%;
            /* Kolom kedua fix 5%, biasanya titik dua */
        }

        .isi-surat p {
            margin: 0 !important;
            padding: 0 !important;
            line-height: 1.5 !important;
        }
    </style>
</head>

<body>
    @if(isset($isReprint) && $isReprint)
        <div class="watermark">ARSIP - DICETAK ULANG</div>
    @endif
    <header class="kop-surat">
        @if($suratSetting->path_kop_surat)
            <img src="{{ public_path('storage/' . $suratSetting->path_kop_surat) }}" alt="Kop Surat">
        @else
            <p>[ KOP SURAT ]</p>
        @endif
    </header>

    <main>
        <h4 class="judul-surat">{{ $pengajuanSurat->jenisSurat->judul_surat }}</h4>
        <p class="nomor-surat">Nomor : {{ $pengajuanSurat->nomor_surat }}</p>

        <div class="isi-surat">
            {!! $processedContent !!}
        </div>

        <div class="tanda-tangan">
            <p>{{ $desa->nama_desa ?? 'Nama Desa' }}, {{ $pengajuanSurat->tanggal_selesai->translatedFormat('d F Y') }}
            </p>
            <p>{{ $suratSetting->penanda_tangan_jabatan ?? 'Kepala Desa' }} {{ $desa->nama_desa ?? 'Nama Desa' }}</p>
            <br><br><br>
            <p class="nama">{{ $suratSetting->penanda_tangan_nama ?? 'Nama Kepala Desa' }}</p>
        </div>
    </main>
</body>

</html>