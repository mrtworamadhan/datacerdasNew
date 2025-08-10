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
            width: 50%;
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
        @if($kopSuratBase64)
            <img src="{{ $kopSuratBase64 }}" alt="Kop Surat" style="max-width: 100%; height: auto;">
        @else
            <p style="font-weight: bold; font-size: 16px;">KOP SURAT DESA</p>
            <p>[ Silakan atur kop surat di menu Pengaturan Surat ]</p>
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
            <br>
            <img src="{{ $ttdBase64 }}" alt="ttd" style="max-width: 40%; height: auto;">
            <p class="nama">{{ $suratSetting->penanda_tangan_nama ?? 'Nama Kepala Desa' }}</p>
        </div>
    </main>
    @if(isset($isAnjungan) && $isAnjungan)
        <div class="container text-center no-print" style="margin-top: 30px;">
            <style>
                @media print { .no-print { display: none; } }
            </style>
            <p>Dokumen sedang disiapkan untuk dicetak...</p>
            <p>Jika dialog cetak tidak muncul, silakan klik tombol di bawah ini.</p>
            <button onclick="window.print()" class="btn btn-primary btn-lg">
                <i class="fas fa-print"></i> CETAK ULANG
            </button>
        </div>

        <!-- <script>
            // Fungsi untuk menutup tab
            function closeTab() {
                // Memberi pesan bahwa proses selesai sebelum tab ditutup
                document.body.innerHTML = '<div style="text-align: center; margin-top: 50px;"><h1>Proses Cetak Selesai.</h1><p>Anda bisa menutup jendela ini.</p></div>';
                // Menutup jendela/tab
                window.close();
            }

            // Memasang "pendengar" untuk event setelah mencetak
            window.addEventListener('afterprint', (event) => {
                closeTab();
            });

            // Jalankan fungsi print() setelah halaman selesai dimuat
            window.onload = function() {
                setTimeout(function() {
                    window.print();
                }, 500); // Beri jeda 0.5 detik agar semua elemen (kop surat) sempat termuat
            }
        </script> -->
    @endif
</body>

</html>