<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Cetak Surat - {{ $jenisSurat->nama_surat }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        /* CSS untuk tampilan surat */
        .surat-body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            max-width: 21cm; /* Lebar kertas A4 */
            margin: 2cm auto;
            padding: 1.5cm;
            border: 1px solid #ccc;
        }
        .kop-surat { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; }
        .kop-surat h4, .kop-surat p { margin: 0; }
        .judul-surat { text-align: center; margin: 30px 0; }
        .judul-surat h5 { font-weight: bold; text-decoration: underline; }
        .isi-surat { text-align: justify; }
        .data-warga { padding-left: 50px; }
        .penutup { margin-top: 30px; }
        .tanda-tangan { margin-top: 50px; }
        .no-print { margin-top: 30px; }

        /* CSS untuk menyembunyikan tombol saat dicetak */
        @media print {
            .no-print { display: none; }
            .surat-body { border: none; margin: 0; padding: 0;}
        }
    </style>
</head>
<body>
    <div class="surat-body" id="surat-content">
        {{-- KOP SURAT --}}
        <div class="kop-surat">
            <h4>PEMERINTAH KABUPATEN [NAMA KABUPATEN]</h4>
            <h4>KECAMATAN [NAMA KECAMATAN]</h4>
            <h4>DESA [NAMA DESA]</h4>
            <p>Alamat: .........................................</p>
        </div>

        {{-- JUDUL SURAT --}}
        <div class="judul-surat">
            <h5>{{ strtoupper($jenisSurat->nama_surat) }}</h5>
            <p>Nomor: .........................................</p>
        </div>

        {{-- ISI SURAT --}}
        <div class="isi-surat">
            <p>Yang bertanda tangan di bawah ini Kepala Desa [Nama Desa], Kecamatan [Nama Kecamatan], Kabupaten [Nama Kabupaten], menerangkan dengan sebenarnya bahwa:</p>

            <table class="data-warga">
                <tr><td style="width: 200px;">Nama Lengkap</td><td>: <strong>{{ $warga->nama_lengkap }}</strong></td></tr>
                <tr><td>NIK</td><td>: {{ $warga->nik }}</td></tr>
                <tr><td>Jenis Kelamin</td><td>: {{ $warga->jenis_kelamin }}</td></tr>
                <tr><td>Tempat, Tanggal Lahir</td><td>: {{ $warga->tempat_lahir }}, {{ $warga->tanggal_lahir->format('d-m-Y') }}</td></tr>
                <tr><td>Alamat</td><td>: {{ $warga->alamat_lengkap }}</td></tr>
            </table>

            {{-- Tampilkan isian field kustom --}}
            @foreach($custom_fields as $label => $value)
            <p><strong>{{ $label }}:</strong> {{ $value }}</p>
            @endforeach

            <p class="penutup">Surat keterangan ini dibuat untuk keperluan: <strong>{{ $keperluan }}</strong>. Demikian surat keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
        </div>

        {{-- TANDA TANGAN --}}
        <div class="tanda-tangan">
            <div class="row">
                <div class="col-6"></div>
                <div class="col-6 text-center">
                    <p>Desa, {{ $tanggalCetak->isoFormat('D MMMM YYYY') }}</p>
                    <p>Kepala Desa</p>
                    <br><br><br>
                    <p><strong><u>{{ $kepalaDesa }}</u></strong></p>
                </div>
            </div>
        </div>
    </div>

    {{-- TOMBOL AKSI (TIDAK AKAN IKUT TERCETAK) --}}
    <div class="container text-center no-print">
        <button onclick="window.print()" class="btn btn-primary btn-lg">
            <i class="fas fa-print"></i> CETAK SURAT
        </button>
        <a href="{{ route('anjungan.pilihSurat') }}" class="btn btn-secondary btn-lg">Kembali</a>
    </div>
</body>
</html>