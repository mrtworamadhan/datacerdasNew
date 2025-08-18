@extends('layouts.portal') {{-- Menggunakan layout portal yang sudah kita buat --}}
@section('title', 'Portal Layanan')

@section('content')
<div class="container">
    <div class="text-center mb-4">
        <h3>Selamat Datang, {{ $user->name }}!</h3>
        <p class="text-muted">Pilih layanan yang ingin Anda gunakan.</p>
    </div>
    {{-- ======================================================== --}}
    {{-- TAMPILAN DASHBOARD UNTUK RT & RW --}}
    {{-- ======================================================== --}}
    @if($user->isAdminRt() || $user->isAdminRw())
    <div class="row">
        {{-- Data Warga --}}
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Data Wilayah</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Jumlah Warga
                            <span class="badge bg-primary rounded-pill">{{ $jumlahWarga }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Jumlah KK
                            <span class="badge bg-primary rounded-pill">{{ $jumlahKk }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Jumlah Balita Terekam di Posyandu
                            <span class="badge bg-info rounded-pill">{{ $jumlahAnakBalita }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Data Belum Terverifikasi --}}
        <div class="col-md-6 mb-4">
            {{-- UBAH href DI SINI --}}
            <a href="{{ route('portal.laporan.belum_verifikasi', ['subdomain' => $subdomain]) }}" class="card text-decoration-none h-100 bg-light-warning">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <h1 class="display-4 fw-bold">{{ $jumlahBelumVerifikasi }}</h1>
                    <p class="mb-0">Warga Belum Terverifikasi</p>
                    <small>(Klik untuk melengkapi)</small>
                </div>
            </a>
        </div>
        <div class="col-md-6 mb-4">
            <a href="{{ route('portal.laporan.tidak_lengkap', ['subdomain' => $subdomain]) }}" class="card text-decoration-none h-100 bg-light-danger">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <h1 class="display-4 fw-bold">{{ $jumlahTidakLengkap }}</h1>
                    <p class="mb-0">Warga Data Tidak Lengkap</p>
                    <small>(Klik untuk melengkapi)</small>
                </div>
            </a>
        </div>

        {{-- Klasifikasi Warga --}}
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Klasifikasi Kesejahteraan</h5>
                    <ul class="list-group list-group-flush">
                        @forelse($klasifikasiWarga as $status => $jumlah)
                        <a href="{{ route('portal.laporan.kesejahteraan', ['subdomain' => $subdomain, 'klasifikasi' => $status]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            {{ $status }}
                            <span class="badge bg-secondary rounded-pill">{{ $jumlah }}</span>
                        </a>
                        @empty
                        <li class="list-group-item text-muted">Data belum tersedia.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        {{-- Bantuan Sosial --}}
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Bantuan Sosial</h5>
                    <ul class="list-group list-group-flush">
                        @foreach($penerimaBantuan as $bantuan => $jumlah)
                        <a href="{{ route('portal.laporan.bantuan', ['subdomain' => $subdomain, 'nama_kategori' => $bantuan]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            Penerima {{ $bantuan }}
                            <span class="badge bg-success rounded-pill">{{ $jumlah }}</span>
                        </a>
                        @endforeach
                    </ul>
                    <hr>
                    <h6 class="card-subtitle mb-2 text-muted">Pengajuan Bantuan Dibuka:</h6>
                    @forelse($bantuanDibuka as $bantuan)
                        <span class="badge bg-info me-1">{{ $bantuan->nama_kaetgori }}</span>
                    @empty
                        <p class="card-text">Tidak ada pengajuan bantuan yang sedang dibuka.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ======================================================== --}}
    {{-- TAMPILAN DASHBOARD UNTUK KADER POSYANDU --}}
    {{-- ======================================================== --}}
    @if($user->isKaderPosyandu())
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
    @endif

    <div class="row">
        {{-- Menu untuk RT & RW --}}
        @if($user->isAdminRt() || $user->isAdminRw())
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="{{ route('portal.fasum.index', ['subdomain' => app('tenant')->subdomain]) }}" class="card text-decoration-none h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-building fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">Kelola Fasum/Fasos</h5>
                        <p class="card-text">Tambah atau perbarui data fasilitas umum di wilayah Anda.</p>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="{{ route('portal.surat.index', ['subdomain' => app('tenant')->subdomain]) }}" class="card text-decoration-none h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-envelope-open-text fa-3x text-success mb-3"></i>
                        <h5 class="card-title">Ajukan Surat Warga</h5>
                        <p class="card-text">Buat pengajuan surat keterangan untuk warga di wilayah Anda.</p>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="{{ route('portal.buat.pengantar', ['subdomain' => app('tenant')->subdomain]) }}" class="card text-decoration-none h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-file-invoice fa-3x text-warning mb-3"></i>
                        <h5 class="card-title">Buat Surat Pengantar</h5>
                        <p class="card-text">Buat pengajuan surat Pengantar RT/RW.</p>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="{{ route('portal.warga.index', ['subdomain' => app('tenant')->subdomain]) }}" class="card text-decoration-none h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-users-cog fa-3x text-info mb-3"></i>
                        <h5 class="card-title">Update Status Warga</h5>
                        <p class="card-text">Laporkan perubahan status warga (pindah, meninggal, dll).</p>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="{{ route('portal.kartuKeluarga.index', ['subdomain' => app('tenant')->subdomain]) }}" class="card text-decoration-none h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-3x text-danger mb-3"></i>
                        <h5 class="card-title">Update Status Keluarga</h5>
                        <p class="card-text">Perbarui Status Eknomoi dan Sosial Keluarga.</p>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="{{ route('portal.bantuan.pilihBantuan', ['subdomain' => app('tenant')->subdomain]) }}" class="card text-decoration-none h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-hand-holding-dollar fa-3x text-warning mb-3"></i>
                        <h5 class="card-title">Penerima Bantuan</h5>
                        <p class="card-text">Ajukan Warga untuk Menerima Bantuan.</p>
                    </div>
                </a>
            </div>
        @endif

        {{-- Menu untuk Kader Posyandu --}}
        @if($user->isKaderPosyandu())
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="{{ route('portal.posyandu.index', ['subdomain' => app('tenant')->subdomain]) }}" class="card text-decoration-none h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-baby fa-3x text-warning mb-3"></i>
                        <h5 class="card-title">Pemeriksaan Anak</h5>
                        <p class="card-text">Input data penimbangan dan pengukuran balita.</p>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="{{ route('portal.posyandu.laporan.index', ['subdomain' => app('tenant')->subdomain]) }}" class="card text-decoration-none h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-file-invoice fa-3x text-danger mb-3"></i>
                        <h5 class="card-title">Laporan Posyandu</h5>
                        <p class="card-text">Lihat dan generate laporan bulanan Posyandu Anda.</p>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="{{ route('portal.posyandu.rekam_medis.search', ['subdomain' => app('tenant')->subdomain]) }}" class="card text-decoration-none h-100">
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
        @endif
    </div>
</div>
@stop
@push('js')
@if(Auth::user()->isKaderPosyandu())
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
