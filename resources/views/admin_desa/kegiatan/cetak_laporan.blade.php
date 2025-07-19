<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kegiatan - {{ $kegiatan->nama_kegiatan }}</title>
    <style>
        @page {
            margin-left: 1in;
            margin-right: 1in;
            margin-top: 1in;
            margin-bottom: 1in;
        }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt;  }
        h1, h2, h3 { text-align: center; margin: 0; padding: 0; }
        h1 { font-size: 16pt; }
        h2 { font-size: 14pt; }
        hr { border: 1px solid #000; margin: 20px 0; }
        .section { margin-bottom: 20px; }
        .section-title { font-weight: bold; margin-bottom: 10px; }
        .content { text-align: justify; line-height: 1.5; }
        .photos { margin-top: 20px; }
        .photos img { max-width: 45%; margin: 5px; border: 1px solid #ccc; }
    </style>
</head>
<body>
    <header>
        <h1>LAPORAN PERTANGGUNGJAWABAN KEGIATAN</h1>
        <h2>{{ strtoupper($kegiatan->nama_kegiatan) }}</h2>
        <h3>{{ strtoupper($lembaga->nama_lembaga) }}</h3>
        <h3>DESA {{ strtoupper($lembaga->desa->nama_desa) }}</h3>
    </header>
    <hr>
    <main>
        <div class="section">
            <p class="section-title">A. PENDAHULUAN</p>
            <div class="content">
                <strong>1. Latar Belakang</strong><br>
                {!! nl2br(e($kegiatan->latar_belakang)) !!}<br><br>
                <strong>2. Tujuan dan Sasaran</strong><br>
                {!! nl2br(e($kegiatan->tujuan_kegiatan)) !!}<br><br>
                <strong>3. Waktu dan Tempat</strong><br>
                Kegiatan ini dilaksanakan pada hari {{ $kegiatan->tanggal_kegiatan->translatedFormat('l, d F Y') }} bertempat di {{ $kegiatan->lokasi_kegiatan }}.
            </div>
        </div>
        <div class="section">
            <p class="section-title">B. PELAKSANAAN KEGIATAN</p>
            <div class="content">{!! nl2br(e($kegiatan->deskripsi_kegiatan)) !!}</div>
        </div>
        <div class="section">
            <p class="section-title">C. ANGGARAN BIAYA</p>
            <div class="content">
                Anggaran biaya untuk kegiatan ini sebesar <strong>Rp {{ number_format($kegiatan->anggaran_biaya, 0, ',', '.') }}</strong>, bersumber dari <strong>{{ $kegiatan->sumber_dana ?? '-' }}</strong>.
            </div>
            <div class="content">{!! nl2br(e($kegiatan->laporan_dana)) !!}</div>
        </div>
        <div class="section">
            <p class="section-title">D. PENUTUP</p>
            <div class="content">{!! nl2br(e($kegiatan->penutup)) !!}</div>
        </div>
        <div class="section">
            <p class="section-title">E. LAMPIRAN DOKUMENTASI</p>
            <div class="photos">
                @foreach($kegiatan->photos as $photo)
                    <img src="{{ public_path('storage/' . $photo->path) }}" alt="Dokumentasi">
                @endforeach
            </div>
        </div>
    </main>
</body>
</html>