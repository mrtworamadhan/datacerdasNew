<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Berita Acara Penerimaan Barang</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; 
            font-size: 12px; 
            line-height: 1.6; 
            margin-top: 5px;
            margin-bottom: 30px;
            margin-left: 30px; 
            margin-right: 30px;
        }
        h2 {
            text-align: left;
            font-size: 14px;
            margin-top: 25px;
        }
        .kop { width: 100%; margin-bottom: 25px; }
        .kop-table { width: 100%; border-bottom: 3px double #000; }
        .kop-table td { vertical-align: middle; }
        .kop-table .logo { width: 80px; }
        .kop-table .logo img { width: 100%; }
        .kop-table .kop-text { text-align: center; }

        .header { text-align: center; font-weight: bold; }
        .header h4 { margin: 0; text-decoration: underline; }
        .header p { margin: 0; }
        .content { margin-top: 30px; }
        .signature-table { margin-top: 40px; width: 100%; border: none; }
        .signature-table td { border: none; text-align: center; }
        .signature-name { margin-top: 60px; font-weight: bold; }
        .page-break { page-break-after: always; }
        .lampiran-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .lampiran-table th, .lampiran-table td { border: 1px solid #000; padding: 5px; text-align: center; }
    </style>
</head>
<body>
    <div class="kop mb-2">
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
    {{-- Halaman Pertama: Berita Acara Utama --}}
    <div class="header">
        <h4>BERITA ACARA PENERIMAAN BARANG / PEKERJAAN</h4>
        <p>Nomor : .........................................</p>
    </div>

    <div class="content">
        <p>Pada hari ini, {{ $tanggalBeritaAcara->isoFormat('dddd') }} Tanggal {{$terbilangHari}} Bulan {{ $tanggalBeritaAcara->isoFormat('MMMM') }} Tahun {{ ucwords(Terbilang::make($tanggalBeritaAcara->year)) }} yang bertanda tangan dibawah ini :</p>

        <table style="width: 100%; margin-left: 30px;">
            <tr><td style="width: 20%;">Nama</td><td>: {{ $pengeluaran->nama_penerima ?? '........................................' }}</td></tr>
            <tr><td>Jabatan</td><td>: Tim Pelaksana Kegiatan (TPK)<td></tr>
        </table>

        <p>Berdasarkan Surat Pemesanan barang kepada {{ $pengeluaran->penyedia }}, Tentang tanda terima Barang, yang telah menerima barang yang diserahkan oleh rekanan : <strong>{{ $pengeluaran->penyedia }}</strong> (daftar terlampir) sesuai dengan Surat Pesanan :</p>

        <table style="width: 100%; margin-left: 30px;">
            <tr><td style="width: 20%;">Nomor</td><td>: .........................................</td></tr>
            <tr><td>Tanggal</td><td>: {{ \Carbon\Carbon::parse($pengeluaran->tanggal_pesanan)->isoFormat('D MMMM YYYY') }}</td></tr>
        </table>

        <p>Demikian Berita Acara ini dibuat untuk dipergunakan sebagai mana mestinya.</p>
    </div>

    <table class="signature-table">
        <tr>
            <td>Yang Menyerahkan,<br><strong>{{ $pengeluaran->penyedia }}</strong></td>
            <td>Yang Menerima,<br>Yang Menyimpan Barang</td>
        </tr>
        <tr>
            <td><p class="signature-name">(........................................)</p></td>
            <td><p class="signature-name">({{ $pengeluaran->nama_penerima ?? '........................................' }})</p></td>
        </tr>
    </table>

    <div class="page-break"></div>


    <div class="kop mb-2">
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
    {{-- Halaman Kedua: Lampiran --}}
    <div class="header">
        <p>LAMPIRAN BERITA ACARA PENERIMAAN BARANG</p>
        <p>Nomor : .........................................</p>
    </div>

    <table class="lampiran-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Satuan</th>
                <th>Banyaknya</th>
                <th colspan="2">Keterangan</th>
            </tr>
             <tr>
                <th></th><th></th><th></th><th></th>
                <th>Sesuai</th>
                <th>Tidak Sesuai</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detailBarangs as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td style="text-align: left;">{{ $item->nama_barang }}</td>
                <td>{{ $item->satuan }}</td>
                <td>{{ $item->volume }}</td>
                <td></td>
                <td></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="signature-table">
        <tr>
            <td>Yang Menyerahkan,<br><strong>{{ $pengeluaran->penyedia }}</strong></td>
            {{-- PERBAIKAN: Gunakan nama penerima --}}
            <td>Yang Menerima,<br>Tim Pelaksana Kegiatan</td>
        </tr>
        <tr>
            <td><p class="signature-name">(........................................)</p></td>
            <td><p class="signature-name">({{ $pengeluaran->nama_penerima ?? '........................................' }})</p></td>
        </tr>
    </table>

</body>
</html>