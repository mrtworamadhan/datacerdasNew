<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Buku Inventaris Aset Desa</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 9px; }
        .header { text-align: center; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table th, .table td { border: 1px solid #000; padding: 4px; text-align: left; }
        .table th { background-color: #f2f2f2; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .signature-table { margin-top: 40px; width: 100%; border: none; }
        .signature-table td { border: none; text-align: center; }
        .signature-name { margin-top: 50px; font-weight: bold; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="header">
        <h3>BUKU INVENTARIS ASET DESA</h3>
        <p>DESA {{ strtoupper ($desa->nama_desa) }} KECAMATAN {{ strtoupper ($desa->kecamatan) }}</p>
        <p>{{ strtoupper($desa->kota) }} PROVINSI {{ strtoupper($desa->provinsi) }}</p>
    </div>
    
    <table class="table">
        <thead>
            <tr>
                <th>No Urut</th>
                <th>Jenis Barang / Nama Barang</th>
                <th>Kode Barang</th>
                <th>Nomor Register</th>
                <th>Tahun Pembelian</th>
                <th>Asal Usul</th>
                <th>Harga (Rp.)</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($asets as $aset)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $aset->nama_aset }}</td>
                <td class="text-center">{{ $aset->kode_aset }}</td>
                <td class="text-center">{{ last(explode('.', $aset->kode_aset)) }}</td>
                <td class="text-center">{{ $aset->tahun_perolehan }}</td>
                <td>{{ $aset->sumber_dana }}</td>
                <td class="text-right">{{ number_format($aset->nilai_perolehan, 0, ',', '.') }}</td>
                <td>Kondisi: {{ $aset->kondisi }}. {{ $aset->keterangan }}</td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center">Tidak ada data aset.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="signature-table">
        <tr>
            <td style="width: 70%;"></td>
            <td>Desa, {{ $tanggalCetak->isoFormat('D MMMM YYYY') }}</td>
        </tr>
        <tr>
            <td>Mengetahui,<br>Kepala Desa</td>
            <td>KAUR UMUM/KAUR KEUANGAN</td>
        </tr>
        <tr>
            <td><p class="signature-name">({{ strtoupper ($desa->nama_kades) }})</p></td>
            <td><p class="signature-name">(........................................)</p></td>
        </tr>
    </table>
</body>
</html>