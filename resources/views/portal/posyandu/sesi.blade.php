@extends('layouts.portal')
@section('title', 'Sesi ' . $tanggalSesi->isoFormat('MMMM YYYY'))

@section('content')
<div class="container">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Alert untuk pesan ERROR validasi atau pesan kustom --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Gagal!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Menampilkan SEMUA error validasi dari form --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <h5 class="alert-heading">Ada beberapa kesalahan input:</h5>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Sesi Posyandu</h4>
            <p class="text-muted">{{ $tanggalSesi->isoFormat('MMMM YYYY') }}</p>
        </div>
        <a href="{{ route('portal.posyandu.index', ['subdomain' => app('tenant')->subdomain]) }}"
            class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="row mb-4">
        <div class="col-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">{{ $totalAnakTerdata }}</h5>
                    <p class="card-text text-muted">Total Anak</p>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card text-center bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">{{ $jumlahHadir }}</h5>
                    <p class="card-text">Hadir</p>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card text-center bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">{{ $jumlahBelumHadir }}</h5>
                    <p class="card-text">Belum Hadir</p>
                </div>
            </div>
        </div>
    </div>

    @if($isSesiSaatIni)
        {{-- Tampilan "Ruang Kerja" untuk Sesi Bulan Ini --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Input Pemeriksaan</h5>
            </div>
            <div class="card-body">
                <p>Pilih anak yang akan diperiksa dari daftar di bawah ini.</p>
                <form action="#" method="GET">
                    <select id="pilih_anak_select" class="form-select form-select-lg"
                        data-find-url="{{ route('portal.posyandu.findAnakBySesi', ['subdomain' => $subdomain, 'tahun' => $tanggalSesi->year, 'bulan' => $tanggalSesi->month]) }}">
                        {{-- KOSONGKAN SAJA, TIDAK PERLU @foreach LAGI --}}
                    </select>
                </form>
            </div>
            <div class="text-center mt-3">
                <p class="text-muted">Tidak menemukan anak? <br>
                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal"
                        data-bs-target="#tambahAnakModal">
                        <i class="fas fa-plus-circle"></i> Tambah Data Anak Baru
                    </button>
                </p>
            </div>
        </div>
    @else
        {{-- Tampilan "Laporan" untuk Sesi Bulan Lalu --}}
        <div class="alert alert-info">
            Anda sedang melihat arsip laporan untuk bulan {{ $tanggalSesi->isoFormat('MMMM YYYY') }}.
        </div>
    @endif
    @php
        function getBadgeColor($status)
        {
            if (stripos($status, 'Normal') !== false || stripos($status, 'Gizi Baik') !== false) {
                return 'bg-success'; // Hijau
            }
            if (
                stripos($status, 'Pendek') !== false ||
                stripos($status, 'Kurang') !== false ||
                stripos($status, 'Lebih') !== false
            ) {
                return 'bg-warning text-dark'; // Kuning
            }
            if (
                stripos($status, 'Berat') !== false ||
                stripos($status, 'Buruk') !== false ||
                stripos($status, 'Obesitas') !== false
            ) {
                return 'bg-danger'; // Merah
            }
            return 'bg-secondary'; // Default abu-abu
        }
    @endphp

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Anak Sudah Diperiksa ({{ $pemeriksaanBulanIni->count() }})</h5>
        </div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                @forelse($pemeriksaanBulanIni as $pemeriksaan)
                    <li class="list-group-item">
                        <strong>{{ $pemeriksaan->warga->nama_lengkap }}</strong><br>
                        <small class="text-muted">
                            Usia : {{$pemeriksaan->usia_formatted}} ({{ $pemeriksaan->usia_saat_periksa }} hari) |
                            BB: {{ $pemeriksaan->berat_badan }} kg |
                            TB: {{ $pemeriksaan->tinggi_badan }} cm
                        </small>

                        <div class="mt-2 mb-2">
                            <div>
                                <small>Stunting:</small><br>
                                <span class="badge {{ getBadgeColor($pemeriksaan->status_stunting) }}">
                                    {{ $pemeriksaan->zscore_tb_u }} | {{ $pemeriksaan->status_stunting }}
                                </span>
                            </div>
                            <div class="mt-1">
                                <small>Underweight:</small><br>
                                <span class="badge {{ getBadgeColor($pemeriksaan->status_underweight) }}">
                                    {{ $pemeriksaan->zscore_bb_u }} | {{ $pemeriksaan->status_underweight }}
                                </span>
                            </div>
                            <div class="mt-1">
                                <small>Wasting:</small><br>
                                <span class="badge {{ getBadgeColor($pemeriksaan->status_wasting) }}">
                                    {{ $pemeriksaan->zscore_bb_tb }} | {{ $pemeriksaan->status_wasting }}
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('portal.posyandu.pemeriksaan.edit', ['subdomain' => $subdomain, 'pemeriksaan' => $pemeriksaan->id]) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </li>
                @empty
                    <li class="list-group-item text-center text-muted">
                        Belum ada anak yang diperiksa di sesi ini.
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
<div class="modal fade" id="tambahAnakModal" tabindex="-1" aria-labelledby="tambahAnakModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="tambahAnakModalLabel">Formulir Anak Baru</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('portal.posyandu.store_anak_baru', ['subdomain' => $subdomain]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small">Data ini akan tercatat sebagai warga baru dan dapat dilihat oleh RT/RW terkait.</p>
                    <div class="mb-3">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap Anak <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                    </div>
                    <div class="mb-3">
                        <label for="tempat_lahir" class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" required placeholder="Contoh: Bogor">
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required>
                    </div>
                    <div class="mb-3">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="nama_ibu_kandung" class="form-label">Nama Ibu Kandung <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_ibu_kandung" required>
                    </div>

                    {{-- === TAMBAHKAN INPUT INI === --}}
                    <div class="mb-3">
                        <label for="nama_ayah_kandung" class="form-label">Nama Ayah Kandung</label>
                        <input type="text" class="form-control" id="nama_ayah_kandung" name="nama_ayah_kandung" placeholder="Opsional">
                    </div>

                    {{-- Input NIK Ibu tetap di sini --}}
                    <div class="mb-3">
                        <label for="nik_ibu" class="form-label">NIK Ibu Kandung <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nik_ibu" required placeholder="Masukkan NIK ibu untuk mencari KK">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan dan Lanjutkan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            // Ambil elemen select
            var anakSelect = $('#pilih_anak_select');

            // Ambil URL dari atribut data-*
            var findUrl = anakSelect.data('find-url');

            anakSelect.select2({
                theme: 'bootstrap-5',
                placeholder: '-- Cari Nama atau NIK Anak --',
                ajax: {
                    // Gunakan variabel URL yang sudah kita ambil
                    url: findUrl,
                    dataType: 'json',
                    delay: 250,
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                }
            });

            // Event saat anak dipilih (tidak ada perubahan di sini)
            anakSelect.on('change', function () {
                var url = $(this).val();
                if (url) {
                    window.location = url;
                }
            });
        });
    </script>
@endpush