<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pengantar RT/RW</title>
    <style>
        body { font-family: 'Arial'; font-size: 12pt; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .text-underline { text-decoration: underline; }
        .kop-table { width: 100%; border-bottom: 3px double #000; }
        .kop-table td { vertical-align: middle; }
        .kop-table .logo { width: 80px; }
        .kop-table .logo img { width: 100%; }
        .kop-table .kop-text { text-align: center; }
        .mt-1 { margin-top: 0.25rem; }
        .mt-5 { margin-top: 3rem; }
        table.data-table { width: 100%; margin-top: 1.5rem; margin-bottom: 1.5rem; }
        table.data-table td { padding: 2px 0; vertical-align: top; }
        table.data-table td:nth-child(1) { width: 25%; }
        table.data-table td:nth-child(2) { width: 3%; }
        table.signature-table { width: 100%; margin-top: 50px; }
    </style>
</head>
<body>
    <table class="kop-table">
        <tr>
            <td class="logo">
                @if($kopSuratBase64)
                    <img src="{{ $kopSuratBase64 }}" alt="Kop Surat" style="max-width: 100%; height: auto;">
                @else
                    <img src="[https://placehold.co/80x80?text=Logo](https://placehold.co/80x80?text=Logo)" alt="Logo">
                @endif
            </td>
            <td class="kop-text">
                <h3 style="margin: 0; font-size: 14pt;">RUKUN TETANGGA (RT) {{ $pengajuan->warga->rt->nomor_rt }} / RUKUN WARGA (RW) {{ $pengajuan->warga->rw->nomor_rw }}</h3>
                <h2 style="margin: 0; font-size: 16pt;" class="text-bold">DESA {{ strtoupper($desa->nama_desa) }}</h2>
                <h3 style="margin: 0; font-size: 12pt;">KECAMATAN {{ strtoupper($desa->kecamatan) }} - {{ strtoupper($desa->kota) }}</h3>
            </td>
        </tr>
    </table>
    <div class="text-center">
        <h3 class="text-bold text-underline">SURAT PENGANTAR</h3>
        <p class="mt-1">Nomor: ...... / SP / RT.{{ $pengajuan->warga->rt->nomor_rt }}-RW.{{ $pengajuan->warga->rw->nomor_rw }} / {{ date('Y') }}</p>
    </div>

    <p class="mt-5">Yang bertanda tangan di bawah ini, Ketua RT. {{ $pengajuan->warga->rt->nomor_rt }} / RW. {{ $pengajuan->warga->rw->nomor_rw }} Desa {{ $desa->nama_desa }} Kecamatan {{ $desa->kecamatan }}, menerangkan dengan sebenarnya bahwa:</p>

    <table class="data-table">
        <tr><td>Nama</td><td>:</td><td>{{ $pengajuan->warga->nama_lengkap }}</td></tr>
        <tr><td>Tempat & Tanggal Lahir</td><td>:</td><td>{{ $pengajuan->warga->tempat_lahir }}, {{ $pengajuan->warga->tanggal_lahir->translatedFormat('d F Y') }}</td></tr>
        <tr><td>Jenis Kelamin</td><td>:</td><td>{{ $pengajuan->warga->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
        <tr><td>Status Perkawinan</td><td>:</td><td>{{ $pengajuan->warga->statusPerkawinan->nama ?? '-' }}</td></tr>
        <tr><td>No. KTP / KK</td><td>:</td><td>{{ $pengajuan->warga->nik }} / {{ $pengajuan->warga->kartuKeluarga->nomor_kk ?? '-' }}</td></tr>
        <tr><td>Kewarganegaraan</td><td>:</td><td>{{ $pengajuan->warga->kewarganegaraan }}</td></tr>
        <tr><td>Agama</td><td>:</td><td>{{ $pengajuan->warga->agama->nama ?? '-' }}</td></tr>
        <tr><td>Pekerjaan</td><td>:</td><td>{{ $pengajuan->warga->pekerjaan->nama ?? '-' }}</td></tr>
        <tr><td>Alamat</td><td>:</td><td>{{ $pengajuan->warga->alamat_lengkap }}</td></tr>
        <tr><td>Keperluan</td><td>:</td><td>{{ $pengajuan->keperluan }}</td></tr>
    </table>

    <p>Demikian Surat Keterangan ini dibuat dengan sebenarnya dan untuk dipergunakan sebagaimana mestinya.</p>

    <table class="signature-table">
        <tr>
            <td style="width: 50%;" class="text-center">
                <p>Mengetahui,</p>
                <p>Ketua RW. {{ $pengajuan->warga->rw->nomor_rw }}</p>
                <br><br><br><br>
                <p class="text-bold text-underline">({{ $pengajuan->warga->rw->nama_ketua ?? '.......................................' }} )</p>
            </td>
            <td style="width: 50%;" class="text-center">
                <p>{{ $desa->nama_desa }}, {{ now()->translatedFormat('d F Y') }}</p>
                <p>Ketua RT. {{ $pengajuan->warga->rt->nomor_rt }} / RW. {{ $pengajuan->warga->rw->nomor_rw }}</p>
                <br><br><br><br>
                <p class="text-bold text-underline">( {{ $pengajuan->warga->rt->nama_ketua ?? '.......................................' }} )</p>
            </td>
        </tr>
    </table>
</body>
</html>