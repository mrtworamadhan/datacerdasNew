<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Penerima Bantuan {{ $kategoriBantuan->nama_kategori }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            padding-top: 5px;
            color: #333;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header img {
            max-width: 100px;
            /* Sesuaikan ukuran logo jika ada */
            height: auto;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 14pt;
            /* Ukuran font judul utama */
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .header h2 {
            font-size: 12pt;
            margin-top: 0;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 10pt;
            margin: 0;
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

        .line {
            border-bottom: 2px solid black;
            margin-top: 5px;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 14pt;
            /* Ukuran font judul bagian */
            font-weight: bold;
            margin-top: 30px;
            margin-bottom: 15px;
            text-align: left;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10pt;
            /* Ukuran font body tabel */
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .footer {
            margin-top: 50px;
            font-size: 9pt;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        {{-- Kop Surat --}}
        <header class="kop-surat">
            @if($suratSetting->path_kop_surat)
            <img src="{{ public_path('storage/' . $suratSetting->path_kop_surat) }}" alt="Kop Surat">
            @else
            <p>[ KOP SURAT ]</p>
            @endif
        </head>

            <div class="header">
                <h1>DAFTAR PENERIMA BANTUAN</h1>
                <h2>Kategori: {{ $kategoriBantuan->nama_kategori }}</h2>
                <p>Desa: {{ $kategoriBantuan->desa->nama_desa ?? '-' }}</p>
                <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d F Y H:i') }}</p>
            </div>

            {{-- Tabel Pengajuan Diajukan --}}
            <div class="section-title">Status: Diajukan ({{ $penerimasDiajukan->count() }} Data)</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No.</th>
                        <th style="width: 20%;">Nama Penerima</th>
                        <th style="width: 15%;">NIK / No. KK</th>
                        <th style="width: 10%;">RW / RT</th>
                        <th style="width: 15%;">Tanggal Pengajuan</th>
                        <th style="width: 20%;">Keterangan</th>
                        <th style="width: 15%;">Diajukan Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($penerimasDiajukan as $index => $penerima)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            @if ($penerima->warga)
                            {{ $penerima->warga->nama_lengkap }}
                            @elseif ($penerima->kartuKeluarga)
                            {{ $penerima->kartuKeluarga->kepalaKeluarga->nama_lengkap ?? '-' }}
                            @else
                            -
                            @endif
                        </td>
                        <td>
                            @if ($penerima->warga)
                            {{ $penerima->warga->nik }}
                            @elseif ($penerima->kartuKeluarga)
                            {{ $penerima->kartuKeluarga->nomor_kk }}
                            @else
                            -
                            @endif
                        </td>
                        <td>
                            @if ($penerima->warga)
                            RW {{ $penerima->warga->rw->nomor_rw ?? '-' }}/RT {{ $penerima->warga->rt->nomor_rt ?? '-' }}
                            @elseif ($penerima->kartuKeluarga)
                            RW {{ $penerima->kartuKeluarga->rw->nomor_rw ?? '-' }}/RT {{ $penerima->kartuKeluarga->rt->nomor_rt ?? '-' }}
                            @else
                            -
                            @endif
                        </td>
                        <td>{{ $penerima->tanggal_menerima->format('d M Y') }}</td>
                        <td>{{ $penerima->keterangan ?? '-' }}</td>
                        <td>{{ $penerima->diajukanOleh->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center;">Tidak ada data pengajuan dalam status ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Tabel Pengajuan Disetujui --}}
            <div class="section-title">Status: Disetujui ({{ $penerimasDisetujui->count() }} Data)</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No.</th>
                        <th style="width: 20%;">Nama Penerima</th>
                        <th style="width: 15%;">NIK / No. KK</th>
                        <th style="width: 10%;">RW / RT</th>
                        <th style="width: 15%;">Tanggal Pengajuan</th>
                        <th style="width: 15%;">Tanggal Verifikasi</th>
                        <th style="width: 20%;">Diverifikasi Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($penerimasDisetujui as $index => $penerima)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            @if ($penerima->warga)
                            {{ $penerima->warga->nama_lengkap }}
                            @elseif ($penerima->kartuKeluarga)
                            {{ $penerima->kartuKeluarga->kepalaKeluarga->nama_lengkap ?? '-' }}
                            @else
                            -
                            @endif
                        </td>
                        <td>
                            @if ($penerima->warga)
                            {{ $penerima->warga->nik }}
                            @elseif ($penerima->kartuKeluarga)
                            {{ $penerima->kartuKeluarga->nomor_kk }}
                            @else
                            -
                            @endif
                        </td>
                        <td>
                            @if ($penerima->warga)
                            RW {{ $penerima->warga->rw->nomor_rw ?? '-' }}/RT {{ $penerima->warga->rt->nomor_rt ?? '-' }}
                            @elseif ($penerima->kartuKeluarga)
                            RW {{ $penerima->kartuKeluarga->rw->nomor_rw ?? '-' }}/RT {{ $penerima->kartuKeluarga->rt->nomor_rt ?? '-' }}
                            @else
                            -
                            @endif
                        </td>
                        <td>{{ $penerima->tanggal_menerima->format('d M Y') }}</td>
                        <td>{{ $penerima->tanggal_verifikasi ? $penerima->tanggal_verifikasi->format('d M Y H:i') : '-' }}</td>
                        <td>{{ $penerima->disetujuiOleh->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center;">Tidak ada data pengajuan dalam status ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Tabel Pengajuan Ditolak --}}
            <div class="section-title">Status: Ditolak ({{ $penerimasDitolak->count() }} Data)</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No.</th>
                        <th style="width: 20%;">Nama Penerima</th>
                        <th style="width: 15%;">NIK / No. KK</th>
                        <th style="width: 10%;">RW / RT</th>
                        <th style="width: 15%;">Tanggal Pengajuan</th>
                        <th style="width: 20%;">Catatan Penolakan</th>
                        <th style="width: 15%;">Diverifikasi Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($penerimasDitolak as $index => $penerima)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            @if ($penerima->warga)
                            {{ $penerima->warga->nama_lengkap }}
                            @elseif ($penerima->kartuKeluarga)
                            {{ $penerima->kartuKeluarga->kepalaKeluarga->nama_lengkap ?? '-' }}
                            @else
                            -
                            @endif
                        </td>
                        <td>
                            @if ($penerima->warga)
                            {{ $penerima->warga->nik }}
                            @elseif ($penerima->kartuKeluarga)
                            {{ $penerima->kartuKeluarga->nomor_kk }}
                            @else
                            -
                            @endif
                        </td>
                        <td>
                            @if ($penerima->warga)
                            RW {{ $penerima->warga->rw->nomor_rw ?? '-' }}/RT {{ $penerima->warga->rt->nomor_rt ?? '-' }}
                            @elseif ($penerima->kartuKeluarga)
                            RW {{ $penerima->kartuKeluarga->rw->nomor_rw ?? '-' }}/RT {{ $penerima->kartuKeluarga->rt->nomor_rt ?? '-' }}
                            @else
                            -
                            @endif
                        </td>
                        <td>{{ $penerima->tanggal_menerima->format('d M Y') }}</td>
                        <td>{{ $penerima->catatan_persetujuan_penolakan ?? '-' }}</td>
                        <td>{{ $penerima->disetujuiOleh->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center;">Tidak ada data pengajuan dalam status ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="footer">
                <p>Dokumen ini dicetak dari Sistem TataDesa pada {{ \Carbon\Carbon::now()->format('d F Y H:i') }}.</p>
            </div>
    </div>
</body>

</html>