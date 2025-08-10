<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Anak {{ $tipeMasalah }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 1px solid #000; padding-bottom: 10px;}
        .header h4, .header p { margin: 0; }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table th, .table td { border: 1px solid #ddd; padding: 5px; text-align: left; }
        .table th { background-color: #f2f2f2; text-align: center; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 9px; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h4>DAFTAR NAMA ANAK DENGAN STATUS GIZI: {{ strtoupper($tipeMasalah) }}</h4>
        <p>Posyandu: {{ $posyandu->nama_posyandu }} | Wilayah: RW {{ $posyandu->rws->nomor ?? 'N/A' }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Anak</th>
                <th>Usia</th>
                <th>Alamat</th>
                <th>RT/RW</th>
                <th>Nama Ibu</th>
                <th>Status Terakhir</th>
                <th>Z-Score</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dataAnak as $anak)
            <tr>
                <td style="text-align: center;">{{ $loop->iteration }}</td>
                <td>{{ $anak->warga->nama_lengkap ?? 'N/A' }}</td>
                <td>{{ $anak->warga ? $anak->warga->tanggal_lahir->diffInMonths(now()) . ' bln' : 'N/A' }}</td>
                <td>{{ $anak->warga->alamat_lengkap ?? 'N/A' }}</td>
                <td>{{ $anak->warga->rt->nomor_rt ?? '' }}/{{ $anak->warga->rw->nomor_rw ?? '' }}</td>
                <td>{{ $anak->nama_ibu ?? 'N/A' }}</td>
                <td style="text-align: center;">
                    @php
                        $status = '';
                        if ($tipeMasalah == 'Stunting') $status = $anak->pemeriksaanTerakhir->status_stunting;
                        elseif ($tipeMasalah == 'Wasting') $status = $anak->pemeriksaanTerakhir->status_wasting;
                        elseif ($tipeMasalah == 'Underweight') $status = $anak->pemeriksaanTerakhir->status_underweight;
                    @endphp
                    {{ $status }}
                </td>
                <td style="text-align: center;">
                    @php
                        $zscore = '';
                        if ($tipeMasalah == 'Stunting') $zscore = $anak->pemeriksaanTerakhir->zscore_tb_u;
                        elseif ($tipeMasalah == 'Wasting') $zscore = $anak->pemeriksaanTerakhir->zscore_bb_tb;
                        elseif ($tipeMasalah == 'Underweight') $zscore = $anak->pemeriksaanTerakhir->zscore_bb_u;
                    @endphp
                    {{ $zscore }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center;">Tidak ada data anak dengan status {{ $tipeMasalah }}.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ $tanggalCetak }}
    </div>
</body>
</html>