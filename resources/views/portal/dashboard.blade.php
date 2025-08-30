@extends('layouts.portal') {{-- Menggunakan layout portal yang sudah kita buat --}}
@section('title', 'Portal Layanan')

@section('content')
<div class="container">
    <div class="text-center mb-4">
        <h3>Selamat Datang, {{ $user->name }}!</h3>
        <p class="text-muted">Pilih layanan yang ingin Anda gunakan.</p>
    </div>
    @hasrole('kepala_desa')
    <div class="row mb-3">
        <div class="col-12">
            <h3 class="mb-3">Rekapitulasi Aset & Fisik</h3>
            <hr class="mt-0">
        </div>
    </div>
    <div class="col mb-3">
        <div class="card h-100 text-center">
            <a href="{{ route('portal.aset.index', ['subdomain' => $subdomain]) }}"
                class="card text-decoration-none h-100 text-center text-primary">
                <div class="card-body">
                    <h1 class="display-5 text-primary"><i class="fas fa-cubes"></i></h1>
                    <h3 class="card-title">{{ $stats['total_aset'] ?? 0 }}</h3>
                    <p class="card-text">Total Aset Desa</p>
                </div>
            </a>
        </div>
    </div>
    <div class="row mb-3">
        <div class="card col-12 px-0">
            <div class="card-header text-center bg-primary">
                <h5 class="text-white">FASUM & FASOS</h5>
            </div>
            <div class="card-body row row-cols-3 row-cols-md-3 row-cols-lg-3 g-3">
                <div class="col">
                    <a href="{{ route('portal.fasum.index', ['subdomain' => $subdomain, 'status_kondisi' => 'Baik']) }}"
                        class="card text-decoration-none h-100 text-center text-white bg-success">
                        <div class="card-body">
                            <h1 class="display-5"><i class="fas fa-thumbs-up"></i></h1>
                            <h3 class="card-title">{{ $stats['fasum_baik'] ?? 0 }}</h3>
                            <p class="card-text">Baik</p>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="{{ route('portal.fasum.index', ['subdomain' => $subdomain, 'status_kondisi' => 'Sedang']) }}"
                        class="card text-decoration-none h-100 text-center text-white bg-warning">
                        <div class="card-body">
                            <h1 class="display-5"><i class="fas fa-exclamation-triangle"></i></h1>
                            <h3 class="card-title">{{ $stats['fasum_sedang'] ?? 0 }}</h3>
                            <p class="card-text">Sedang</p>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="{{ route('portal.fasum.index', ['subdomain' => $subdomain, 'status_kondisi' => 'Rusak']) }}"
                        class="card text-decoration-none h-100 text-center text-white bg-danger">
                        <div class="card-body">
                            <h1 class="display-5"><i class="fas fa-tools"></i></h1>
                            <h3 class="card-title">{{ $stats['fasum_rusak'] ?? 0 }}</h3>
                            <p class="card-text">Rusak</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4 mb-3">
        <div class="col-12">
            <h3 class="mb-3">Rekapitulasi Kependudukan & Sosial</h3>
            <hr class="mt-0">
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Data Wilayah</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Jumlah Warga
                        <span class="badge bg-primary rounded-pill">{{ $stats['jumlahWarga'] ?? 0 }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Jumlah KK
                        <span class="badge bg-primary rounded-pill">{{ $stats['jumlahKk'] ?? 0 }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4 mb-3">
        <div class="col">
            <div class="card h-100 text-center">
                <a href="{{ route('portal.laporan.demografi', ['subdomain' => $subdomain, 'jenis' => 'lahir']) }}"
                    class="col text-decoration-none">
                    <div class="card-body">
                        <h1 class="display-5 text-teal"><i class="fas fa-baby-carriage"></i></h1>
                        <h3 class="card-title">{{ $stats['warga_lahir_bulan_ini'] ?? 0 }}</h3>
                        <p class="card-text">Kelahiran Bulan Ini</p>
                    </div>
                </a>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 text-center">
                <a href="{{ route('portal.laporan.demografi', ['subdomain' => $subdomain, 'jenis' => 'meninggal']) }}"
                    class="col text-decoration-none">
                    <div class="card-body">
                        <h1 class="display-5 text-secondary"><i class="fas fa-cross"></i></h1>
                        <h3 class="card-title">{{ $stats['warga_meninggal_bulan_ini'] ?? 0 }}</h3>
                        <p class="card-text">Kematian Bulan Ini</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <div class="row row-cols-3 row-cols-md-3 row-cols-lg-3 g-3 mb-3">
        <div class="col">
            <div class="card h-100 text-center">
                <a href="{{ route('portal.laporan.demografi', ['subdomain' => $subdomain, 'jenis' => 'pindah']) }}"
                    class="col text-decoration-none">
                    <div class="card-body">
                        <h1 class="display-5 text-info"><i class="fas fa-plane-departure"></i></h1>
                        <h3 class="card-title">{{ $stats['warga_pindah_bulan_ini'] ?? 0 }}</h3>
                        <p class="card-text">Pindah</p>
                    </div>
                </a>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 text-center">
                <a href="{{ route('portal.laporan.demografi', ['subdomain' => $subdomain, 'jenis' => 'datang']) }}"
                    class="col text-decoration-none">
                    <div class="card-body">
                        <h1 class="display-5 text-success"><i class="fas fa-plane-arrival"></i></h1>
                        <h3 class="card-title">{{ $stats['warga_datang_bulan_ini'] ?? 0 }}</h3>
                        <p class="card-text">Datang</p>
                    </div>
                </a>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 text-center">
                <a href="{{ route('portal.laporan.demografi', ['subdomain' => $subdomain, 'jenis' => 'sementara']) }}"
                    class="col text-decoration-none">
                    <div class="card-body">
                        <h1 class="display-5 text-muted"><i class="fas fa-suitcase"></i></h1>
                        <h3 class="card-title">{{ $stats['warga_sementara'] ?? 0 }}</h3>
                        <p class="card-text">Sementara</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <div class="row row-cols-3 row-cols-md-3 row-cols-lg-3 g-3 mb-3">
        <div class="col">
            <div class="card h-100 text-center">
                <a href="{{ route('portal.laporan.demografi', ['subdomain' => $subdomain, 'jenis' => 'janda']) }}"
                    class="col text-decoration-none">
                    <div class="card-body">
                        <h1 class="display-5" style="color: #6f42c1;"><i class="fas fa-female"></i></h1>
                        <h3 class="card-title">{{ $stats['jumlah_janda'] ?? 0 }}</h3>
                        <p class="card-text">Janda</p>
                    </div>
                </a>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 text-center">
                <a href="{{ route('portal.laporan.demografi', ['subdomain' => $subdomain, 'jenis' => 'yatim']) }}"
                    class="col text-decoration-none">
                    <div class="card-body">
                        <h1 class="display-5" style="color: #fd7e14;"><i class="fas fa-child"></i></h1>
                        <h3 class="card-title">{{ $stats['jumlah_yatim'] ?? 0 }}</h3>
                        <p class="card-text">Yatim</p>
                    </div>
                </a>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 text-center">
                <a href="{{ route('portal.laporan.demografi', ['subdomain' => $subdomain, 'jenis' => 'piatu']) }}"
                    class="col text-decoration-none">
                    <div class="card-body">
                        <h1 class="display-5" style="color: #20c997;"><i class="fas fa-child"></i></h1>
                        <h3 class="card-title">{{ $stats['jumlah_piatu'] ?? 0 }}</h3>
                        <p class="card-text">Piatu</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    {{-- Chart Klasifikasi Keluarga --}}
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-warning text-white text-center">
                    <h5 class="card-title">Klasifikasi Kesejahteraan Keluarga</h5>
                </div>
                <div class="card-body" style="height: 400px;">
                    <canvas id="klasifikasiChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4 mb-3">
        <div class="col-12">
            <h3 class="mb-3">Rekapitulasi Kesehatan Anak</h3>
            <hr class="mt-0">
        </div>
    </div>
    <div class="row row-cols-3 row-cols-md-3 row-cols-lg-3 g-3 mb-3">
        <div class="col">
            <a href="{{ route('portal.laporan.kesehatan_anak', ['subdomain' => $subdomain, 'status' => 'stunting']) }}"
                class="card text-decoration-none h-100 text-center">
                <div class="card-body">
                    <h1 class="display-5 text-danger"><i class="fas fa-child"></i></h1>
                    <h3 class="card-title">{{ $stats['anak_stunting'] ?? 0 }}</h3>
                    <p class="card-text">Stunting</p>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('portal.laporan.kesehatan_anak', ['subdomain' => $subdomain, 'status' => 'wasting']) }}"
                class="card text-decoration-none h-100 text-center">
                <div class="card-body">
                    <h1 class="display-5 text-warning"><i class="fas fa-child"></i></h1>
                    <h3 class="card-title">{{ $stats['anak_wasting'] ?? 0 }}</h3>
                    <p class="card-text">Wasting</p>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('portal.laporan.kesehatan_anak', ['subdomain' => $subdomain, 'status' => 'underweight']) }}"
                class="card text-decoration-none h-100 text-center">
                <div class="card-body">
                    <h1 class="display-5 text-info"><i class="fas fa-child"></i></h1>
                    <h3 class="card-title">{{ $stats['anak_underweight'] ?? 0 }}</h3>
                    <p class="card-text">Underweight</p>
                </div>
            </a>
        </div>
    </div>
    @endhasrole
    {{-- ======================================================== --}}
    {{-- TAMPILAN DASHBOARD UNTUK RT & RW --}}
    {{-- ======================================================== --}}
    @if($user->hasRole(roles: 'admin_rw') || $user->hasRole('admin_rt'))
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Rekapitulasi Data Wilayah Anda</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">Jumlah Warga <span
                                    class="badge bg-primary rounded-pill">{{ $jumlahWarga ?? 0 }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Jumlah KK <span
                                    class="badge bg-primary rounded-pill">{{ $jumlahKk ?? 0 }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Jumlah Balita Terekam di Posyandu
                                <span class="badge bg-info rounded-pill">{{ $jumlahAnakBalita }}</span>
                            </li>
                            <a href="{{ route('portal.laporan.demografi', ['subdomain' => $subdomain, 'jenis' => 'lahir']) }}"
                                class="col text-decoration-none">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Kelahiran Bulan Ini <span class="badge bg-info rounded-pill">{{ $warga_lahir_bulan_ini ?? 0 }}</span>
                                </li>
                            </a>
                            <a href="{{ route('portal.laporan.demografi', ['subdomain' => $subdomain, 'jenis' => 'meninggal']) }}"
                                class="col text-decoration-none">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Kematian Bulan Ini
                                    <span class="badge bg-secondary rounded-pill">{{ $warga_meninggal_bulan_ini ?? 0 }}</span>
                                </li>
                            </a>
                            <a href="{{ route('portal.laporan.demografi', ['subdomain' => $subdomain, 'jenis' => 'sementara']) }}"
                                class="col text-decoration-none">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Warga Pindah Datang <span class="badge bg-info rounded-pill">{{ $warga_datang_bulan_ini ?? 0 }}</span>
                                </li>
                            </a>
                            <a href="{{ route('portal.laporan.demografi', ['subdomain' => $subdomain, 'jenis' => 'pindah']) }}"
                                class="col text-decoration-none">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Warga Pindah Keluar
                                    <span class="badge bg-secondary rounded-pill">{{ $warga_meninggal_bulan_ini ?? 0 }}</span>
                                </li>
                            </a>
                            <a href="{{ route('portal.laporan.demografi', ['subdomain' => $subdomain, 'jenis' => 'janda']) }}"
                                class="col text-decoration-none">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Janda <span class="badge rounded-pill"
                                        style="background-color: #6f42c1;">{{ $jumlah_janda ?? 0 }}</span></li>
                            </a>
                            <a href="{{ route('portal.laporan.demografi', ['subdomain' => $subdomain, 'jenis' => 'yatim']) }}"
                                class="col text-decoration-none">
                                <li class="list-group-item d-flex justify-content-between align-items-center">Anak Yatim
                                    <span class="badge rounded-pill"
                                        style="background-color: #fd7e14;">{{ $jumlah_yatim ?? 0 }}</span>
                                </li>
                            </a>
                            <a href="{{ route('portal.laporan.demografi', ['subdomain' => $subdomain, 'jenis' => 'piatu']) }}"
                                class="col text-decoration-none">
                                <li class="list-group-item d-flex justify-content-between align-items-center">Anak Piatu
                                    <span class="badge rounded-pill"
                                        style="background-color: #20c997;">{{ $jumlah_piatu ?? 0 }}
                                    </span>
                                </li>
                            </a>
                            <a href="{{ route('portal.laporan.kesehatan_anak', ['subdomain' => $subdomain, 'jenis' => 'stunting']) }}"
                                class="col text-decoration-none">
                                <li
                                    class="list-group-item d-flex justify-content-between align-items-center text-danger fw-bold">
                                    Stunting
                                    <span class="badge bg-danger rounded-pill">{{ $anak_stunting_wilayah ?? 0 }}</span>
                                </li>
                            </a>
                            <a href="{{ route('portal.laporan.kesehatan_anak', ['subdomain' => $subdomain, 'jenis' => 'wasting']) }}"
                                class="col text-decoration-none">
                                <li
                                    class="list-group-item d-flex justify-content-between align-items-center text-danger fw-bold">
                                    Wasting
                                    <span class="badge bg-danger rounded-pill">{{ $anak_wasting_wilayah ?? 0 }}</span>
                                </li>
                            </a>
                            <a href="{{ route('portal.laporan.kesehatan_anak', ['subdomain' => $subdomain, 'jenis' => 'underweight']) }}"
                                class="col text-decoration-none">
                                <li
                                    class="list-group-item d-flex justify-content-between align-items-center text-danger fw-bold">
                                    Underweight
                                    <span class="badge bg-danger rounded-pill">{{ $anak_underweight_wilayah ?? 0 }}</span>
                                </li>
                            </a>
                        </ul>
                    </div>
                </div>
            </div>
            {{-- Klasifikasi Warga --}}
            <div class="col-md-6 mb-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Klasifikasi Kesejahteraan</h5>
                        <ul class="list-group list-group-flush">
                            @forelse($klasifikasiWarga as $status => $jumlah)
                                <a href="{{ route('portal.laporan.kesejahteraan', ['subdomain' => $subdomain, 'klasifikasi' => $status]) }}"
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    {{ $status }}
                                    <span class="badge bg-secondary rounded-pill">{{ $jumlah }}</span>
                                </a>
                            @empty
                                <li class="list-group-item text-muted">Data belum tersedia.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Bantuan Sosial</h5>
                        <ul class="list-group list-group-flush">
                            @foreach($penerimaBantuan as $bantuan => $jumlah)
                                <a href="{{ route('portal.laporan.bantuan', ['subdomain' => $subdomain, 'nama_kategori' => $bantuan]) }}"
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    Penerima {{ $bantuan }}
                                    <span class="badge bg-success rounded-pill">{{ $jumlah }}</span>
                                </a>
                            @endforeach
                        </ul>
                        <hr>
                        <h6 class="card-subtitle mb-2 text-muted">Pengajuan Bantuan Dibuka:</h6>
                        @forelse($bantuanDibuka as $bantuan)
                            <span class="badge bg-info me-1">{{ $bantuan->nama_kategori }}</span>
                        @empty
                            <p class="card-text">Tidak ada pengajuan bantuan yang sedang dibuka.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        {{-- Data Belum Terverifikasi --}}
        <div class="row">
            <div class="col-md-6 mb-4">
                {{-- UBAH href DI SINI --}}
                <a href="{{ route('portal.laporan.belum_verifikasi', ['subdomain' => $subdomain]) }}"
                    class="card text-decoration-none h-100 bg-danger text-white">
                    <div class="card-body text-center d-flex flex-column justify-content-center">
                        <h1 class="display-4 fw-bold">{{ $jumlahBelumVerifikasi }}</h1>
                        <p class="mb-0">Warga Belum Terverifikasi</p>
                        <small>(Klik untuk melengkapi)</small>
                    </div>
                </a>
            </div>
            <div class="col-md-6 mb-4">
                <a href="{{ route('portal.laporan.tidak_lengkap', ['subdomain' => $subdomain]) }}"
                    class="card text-decoration-none h-100 bg-warning text-white">
                    <div class="card-body text-center d-flex flex-column justify-content-center">
                        <h1 class="display-4 fw-bold">{{ $jumlahTidakLengkap }}</h1>
                        <p class="mb-0">Warga Data Tidak Lengkap</p>
                        <small>(Klik untuk melengkapi)</small>
                    </div>
                </a>
            </div>
        </div>
    @endif
    {{-- ======================================================== --}}
    {{-- TAMPILAN DASHBOARD UNTUK KADER POSYANDU --}}
    {{-- ======================================================== --}}
    @if($user->hasRole('kader_posyandu'))
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <h1 class="display-4 fw-bold">{{ $jumlahAnakBalita }}</h1>
                        <p class="mb-0">Total Balita Terpantau</p>
                    </div>
                </div>
            </div>
            <div class="col-md-8 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Tren Gizi 12 Bulan Terakhir</h5>
                        <canvas id="grafikTrenGizi"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row row-cols-3 row-cols-md-3 row-cols-lg-3 g-3 mb-3">
            <div class="col">
                <a href="{{ route('portal.laporan.kesehatan_anak', ['subdomain' => app('tenant')->subdomain, 'status' => 'stunting']) }}"
                    class="card text-decoration-none h-100 text-center">
                    <div class="card-body">
                        <h1 class="display-5 text-danger"><i class="fas fa-child"></i></h1>
                        <h3 class="card-title">{{ $trendData['anak_stunting'] ?? 0 }}</h3>
                        <p class="card-text">Stunting</p>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="{{ route('portal.laporan.kesehatan_anak', ['subdomain' => app('tenant')->subdomain, 'status' => 'wasting']) }}"
                    class="card text-decoration-none h-100 text-center">
                    <div class="card-body">
                        <h1 class="display-5 text-warning"><i class="fas fa-child"></i></h1>
                        <h3 class="card-title">{{ $trendData['anak_wasting'] ?? 0 }}</h3>
                        <p class="card-text">Wasting</p>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="{{ route('portal.laporan.kesehatan_anak', ['subdomain' => app('tenant')->subdomain, 'status' => 'underweight']) }}"
                    class="card text-decoration-none h-100 text-center">
                    <div class="card-body">
                        <h1 class="display-5 text-info"><i class="fas fa-child"></i></h1>
                        <h3 class="card-title">{{ $trendData['anak_underweight'] ?? 0 }}</h3>
                        <p class="card-text">Underweight</p>
                    </div>
                </a>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Menu Layanan Cepat</h5>
            </div>
            <div class="card-body row row-cols-2 row-cols-md-2 row-cols-lg-2 g-2">
                @hasanyrole('admin_rt|admin_rw')
                <div class="col-md-6 col-lg-4 mb-4">
                    <a href="{{ route('portal.fasum.index', ['subdomain' => $subdomain]) }}"
                        class="card text-decoration-none h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-building fa-3x text-primary mb-3"></i>
                            <h6 class="card-title">Kelola Fasum/Fasos</h6>
                            <p class="card-text text-muted"><small>Tambah atau perbarui data fasilitas umum di
                                    wilayah Anda.</small></p>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <a href="{{ route('portal.surat.index', ['subdomain' => $subdomain]) }}"
                        class="card text-decoration-none h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-envelope-open-text fa-3x text-success mb-3"></i>
                            <h6 class="card-title">Ajukan Surat Warga</h6>
                            <p class="card-text text-muted"><small>Buat pengajuan surat keterangan untuk warga
                                    di wilayah Anda.</small></p>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <a href="{{ route('portal.buat.pengantar', ['subdomain' => $subdomain]) }}"
                        class="card text-decoration-none h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-file-invoice fa-3x text-warning mb-3"></i>
                            <h6 class="card-title">Buat Surat Pengantar</h6>
                            <p class="card-text text-muted"><small>Buat pengajuan surat Pengantar RT/RW.</small>
                            </p>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <a href="{{ route('portal.warga.index', ['subdomain' => $subdomain]) }}"
                        class="card text-decoration-none h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-users-cog fa-3x text-info mb-3"></i>
                            <h6 class="card-title">Update Status Warga</h6>
                            <p class="card-text text-muted"><small>Laporkan perubahan status warga (pindah,
                                    meninggal, dll).</small></p>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <a href="{{ route('portal.kartuKeluarga.index', ['subdomain' => $subdomain]) }}"
                        class="card text-decoration-none h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-3x text-danger mb-3"></i>
                            <h6 class="card-title">Update Status Keluarga</h6>
                            <p class="card-text text-muted"><small>Perbarui Status Eknomoi dan Sosial
                                    Keluarga.</small></p>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <a href="{{ route('portal.bantuan.pilihBantuan', ['subdomain' => $subdomain]) }}"
                        class="card text-decoration-none h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-hand-holding-dollar fa-3x text-warning mb-3"></i>
                            <h6 class="card-title">Penerima Bantuan</h6>
                            <p class="card-text text-muted"><small>Ajukan Warga untuk Menerima Bantuan.</small>
                            </p>
                        </div>
                    </a>
                </div>
                @endhasanyrole
                @hasrole('kader_posyandu')
                <div class="col-md-6 col-lg-4 mb-4">
                    <a href="{{ route('portal.posyandu.index', ['subdomain' => app('tenant')->subdomain]) }}"
                        class="card text-decoration-none h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-baby fa-3x text-warning mb-3"></i>
                            <h5 class="card-title">Pemeriksaan Anak</h5>
                            <p class="card-text">Input data penimbangan dan pengukuran balita.</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <a href="{{ route('portal.posyandu.laporan.index', ['subdomain' => app('tenant')->subdomain]) }}"
                        class="card text-decoration-none h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-file-invoice fa-3x text-danger mb-3"></i>
                            <h5 class="card-title">Laporan Posyandu</h5>
                            <p class="card-text">Lihat dan generate laporan bulanan Posyandu Anda.</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <a href="{{ route('portal.posyandu.rekam_medis.search', ['subdomain' => app('tenant')->subdomain]) }}"
                        class="card text-decoration-none h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-line fa-3x text-info mb-3"></i>
                            <h5 class="card-title">Rekam Medis Anak</h5>
                            <p class="card-text">Lihat Rekam Pemeriksaan Setiap Anak.</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4 mb-4 feature-coming-soon">
                    <a href="#" class="card text-decoration-none h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-person-pregnant fa-3x text-success mb-3"></i>
                            <h5 class="card-title">Periksa Ibu Hamil</h5>
                            <p class="card-text">Lihat Rekam Pemeriksaan Setiap Ibu Hamil.</p>
                            <p class="card-text text-muted fw-bold">(SEGERA HADIR)</p>
                        </div>
                    </a>
                </div>
                @endhasrole
            </div>
        </div>
    </div>

    @stop
    @push('js')
        @hasrole('kepala_desa')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            $(function () {
                var ctx = document.getElementById('klasifikasiChart').getContext('2d');
                var klasifikasiData = @json($stats['klasifikasi_keluarga'] ?? []);

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(klasifikasiData),
                        datasets: [{
                            label: 'Jumlah Keluarga',
                            data: Object.values(klasifikasiData),
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(255, 159, 64, 0.2)',
                                'rgba(255, 205, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(54, 162, 235, 0.2)'
                            ],
                            borderColor: [
                                'rgb(255, 99, 132)',
                                'rgb(255, 159, 64)',
                                'rgb(255, 205, 86)',
                                'rgb(75, 192, 192)',
                                'rgb(54, 162, 235)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: { beginAtZero: true }
                        },
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            });
        </script>
        @endhasrole
        @if(Auth::user()->hasRole('kader_posyandu'))
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                const ctx = document.getElementById('grafikTrenGizi');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($trendData['labels']) !!},
                        datasets: [
                            {
                                label: 'Stunting',
                                data: {!! json_encode($trendData['stunting']) !!},
                                borderColor: 'rgb(255, 99, 132)',
                                tension: 0.1
                            },
                            {
                                label: 'Wasting',
                                data: {!! json_encode($trendData['wasting']) !!},
                                borderColor: 'rgb(255, 205, 86)',
                                tension: 0.1
                            },
                            {
                                label: 'Underweight',
                                data: {!! json_encode($trendData['underweight']) !!},
                                borderColor: 'rgb(54, 162, 235)',
                                tension: 0.1
                            }
                        ]
                    },
                });
            </script>
        @endif
    @endpush