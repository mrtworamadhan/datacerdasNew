<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Pesanan</title>
    <style>
        body { 
            font-family: 'Times New Roman', Times, serif; 
            font-size: 12px; 
            line-height: 1.5; 
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
        
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table th, .table td { border: 1px solid #000; padding: 5px; }
        .table th { background-color: #f2f2f2; text-align: center; }
        .header-info { width: 100%; margin-top: 5px; }
        .header-info td { vertical-align: top; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="kop mb-2">
        <table class="kop-table">
            <tr>
                <td class="logo">
                    @if($penyelenggara->path_kop_surat)
                        <img src="{{ asset('storage/' . $penyelenggara->path_kop_surat) }}" alt="Logo" >
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
    <p class="text-right">Karacak, {{ \Carbon\Carbon::parse($pengeluaran->tanggal_pesanan)->isoFormat('D MMMM YYYY') }}</p>

    <table class="header-info">
        <tr>
            <td style="width: 60%;">
                Nomor : .........................<br>
                Lampiran : -<br>
                Perihal : <strong>Surat Pesanan</strong>
            </td>
            <td style="width: 40%;">
                Kepada Yth.<br>
                <strong>{{ $pengeluaran->penyedia }}</strong><br>
                Di –<br>
                Tempat
            </td>
        </tr>
    </table>

    <p style="margin-top: 20px;">
        Berdasarkan Nota Dinas dari Sub Kegiatan Penyediaan Barang, dalam rangka tertib administrasi keuangan, 
        pada kegiatan {{ $kegiatan->nama_kegiatan }} yang dilaksanakan di {{ $kegiatan->lokasi_kegiatan }}, 
        dengan ini kami mengajukan pesanan untuk tanggal {{ \Carbon\Carbon::parse($pengeluaran->tanggal_pesanan)->isoFormat('D MMMM YYYY') }} dengan rincian sebagai berikut:
    </p>

    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Volume</th>
                <th>Satuan</th>
                <th>Harga Satuan (Rp)</th>
                <th>Jumlah Harga (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detailBarangs as $item)
            <tr>
                <td style="text-align: center;">{{ $loop->iteration }}</td>
                <td>{{ $item->nama_barang }}</td>
                <td style="text-align: center;">{{ $item->volume }}</td>
                <td style="text-align: center;">{{ $item->satuan }}</td>
                <td class="text-right">{{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item->volume * $item->harga_satuan, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr>
                <th colspan="5" class="text-right">TOTAL HARGA</th>
                <th class="text-right">{{ number_format($pengeluaran->jumlah, 0, ',', '.') }}</th>
            </tr>
        </tbody>
    </table>

    <p style="margin-top: 20px;">Demikian Surat Pesanan ini disampaikan, atas kerjasama yang baik diucapkan terima kasih.</p>

    <div style="width: 50%; float: right; text-align: center; margin-top: 30px;">
        <p>Pengguna Anggaran<br>PELAKSANA KEGIATAN</p>
        <br><br><br>
        <p style="font-weight: bold; text-decoration: underline;">({{ $pengeluaran->nama_pemesan ?? '........................................' }})</p>    </div>
</body>
</html>