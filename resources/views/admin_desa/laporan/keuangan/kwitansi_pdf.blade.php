<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kwitansi Pembayaran</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            margin: 0;
            padding: 5px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .outer-table td {
            vertical-align: top;
            border: 1px dashed black;
            padding: 15px;
        }

        .left-box {
            width: 20%;
            text-align: center;
            height: 400px;
        }

        .right-box {
            width: 80%;
        }

        .kwitansi-header {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            text-decoration: underline;
            margin-bottom: 15px;
        }

        .kwitansi-table {
            width: 100%;
            margin-bottom: 10px;
        }

        .kwitansi-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        .terbilang-box {
            background: #ccc;
            font-style: italic;
            font-weight: bold;
            text-align: center;
            padding: 10px;
            margin: 10px 0;
            transform: skewX(-10deg);
        }

        .terbilang-box span {
            display: inline-block;
            transform: skewX(10deg);
        }

        .jumlah-box {
            font-size: 14px;
            font-weight: bold;
            background-color: #e3e3e3;
            border: 1px solid #000;
            padding: 10px 15px;
            display: inline-block;
            transform: skewX(-20deg);
            margin-top: 15px;
        }

        .jumlah-box span {
            transform: skewX(20deg);
            display: inline-block;
        }

        .signature-row {
            width: 100%;
            margin-top: 40px;
        }

        .signature-cell {
            text-align: center;
            width: 33%;
        }

        .signature-name {
            margin-top: 50px;
            text-decoration: underline;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <table class="outer-table">
        <tr>
            <!-- Kolom kiri -->
            <td class="left-box">
                <div style="margin-top: 40px;">
                    <strong>KETUA PELAKSANA</strong>
                </div>
                <div style="margin-top: 180px;">
                    <strong>({{ $ketua->nama_pengurus ?? '........................' }})</strong>
                </div>
            </td>

            <!-- Kolom kanan -->
            <td class="right-box">
                <div class="kwitansi-header">KWITANSI</div>

                <table class="kwitansi-table">
                    <tr>
                        <td style="width: 35%;">No.</td>
                        <td>: ....................................</td>
                    </tr>
                    <tr>
                        <td>Telah terima dari</td>
                        <td>: Bendahara Pelaksana {{ $kegiatan->nama_kegiatan }}</td>
                    </tr>
                    <tr>
                        <td>Uang Sejumlah</td>
                        <td>:</td>
                    </tr>
                </table>

                <div class="terbilang-box">
                    <span>{{ ucwords($terbilang) }} Rupiah</span>
                </div>

                <table class="kwitansi-table">
                    <tr>
                        <td style="width: 35%;">Untuk Pembayaran</td>
                        <td>: {{ $pengeluaran->uraian }}</td>
                    </tr>
                </table>

                <div class="row margin-left: 5px;">
                    <div class="jumlah-box col">
                        <span>Rp. {{ number_format($pengeluaran->jumlah, 0, ',', '.') }},-</span>
                    </div>
                    <div style="text-align: right; margin-top: 10px;">
                        Karacak, {{ \Carbon\Carbon::parse($pengeluaran->tanggal_transaksi)->isoFormat('D MMMM YYYY') }}<br>
                    </div>
                </div>

                <table style="width: 100%; margin-top: 5px;">
                    <tr>
                        <td style="width: 70%;">
                            <table class="signature-table">
                                <tr>
                                    <td style="width: 50%;">
                                        {{-- PERBAIKAN: Tambahkan Bendahara --}}
                                        Bendahara,<br><br>
                                        <p class="signature-name">({{ $bendahara->nama_pengurus ?? '........................' }})</p>
                                    </td>
                                    <td style="width: 50%;">
        
                                        Penerima,<br><br>
                                        <p class="signature-name">{{ $pengeluaran->penyedia }}</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
