@extends('admin.master')
@section('title', 'Dashboard')
@section('content_header')
    <h1 class="m-0 text-dark">Dashboard Utama</h1>
@stop

@section('content_main')
<div class="row">
    {{-- Notifikasi Langganan --}}
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-exclamation-triangle"></i> Peringatan Langganan!</h5>
            {{ session('warning') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-ban"></i> Akses Dibatasi!</h5>
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <p class="mb-0">Selamat datang di Dashboard Desa Cerdas!</p>
                    <p>Anda login sebagai **{{ Auth::user()->name }}** ({{ ucfirst(str_replace('_', ' ', Auth::user()->user_type)) }}).</p>
                    @if(Auth::user()->desa)
                        <p>Desa Anda: **{{ Auth::user()->desa->nama_desa }}**</p>
                        <p>Status Langganan Desa: 
                            @php
                                $badgeClass = 'secondary';
                                if (Auth::user()->desa->isSubscriptionActive()) $badgeClass = 'success';
                                elseif (Auth::user()->desa->isInTrial()) $badgeClass = 'info';
                                elseif (Auth::user()->desa->isSubscriptionInactive()) $badgeClass = 'danger';
                            @endphp
                            <span class="badge badge-{{ $badgeClass }}">{{ ucfirst(Auth::user()->desa->subscription_status) }}</span>
                            @if(Auth::user()->desa->subscription_ends_at)
                                (Berakhir pada: {{ Auth::user()->desa->subscription_ends_at->format('d M Y') }})
                            @elseif(Auth::user()->desa->trial_ends_at)
                                (Trial Berakhir pada: {{ Auth::user()->desa->trial_ends_at->format('d M Y') }})
                            @endif
                        </p>
                    @else
                        <p>Anda belum terhubung dengan desa manapun.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- Kolom Kiri (Lebih Besar) --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header border-0">
                <h3 class="card-title"><i class="fas fa-users mr-1"></i> tataWarga - Statistik Kependudukan</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-6"><div class="small-box bg-info"><div class="inner"><h3>{{ $stats['tataWarga']['total_warga'] }}</h3><p>Total Warga</p></div><div class="icon"><i class="fas fa-user-friends"></i></div></div></div>
                    <div class="col-md-3 col-6"><div class="small-box bg-success"><div class="inner"><h3>{{ $stats['tataWarga']['total_kk'] }}</h3><p>Total KK</p></div><div class="icon"><i class="fas fa-id-card"></i></div></div></div>
                    <div class="col-md-3 col-6"><div class="small-box bg-primary"><div class="inner"><h3>{{ $stats['tataWarga']['total_laki'] }}</h3><p>Laki-laki</p></div><div class="icon"><i class="fas fa-male"></i></div></div></div>
                    <div class="col-md-3 col-6"><div class="small-box bg-danger"><div class="inner"><h3>{{ $stats['tataWarga']['total_perempuan'] }}</h3><p>Perempuan</p></div><div class="icon"><i class="fas fa-female"></i></div></div></div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Berdasarkan Usia</strong>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between align-items-center">Balita (0-5 th)<span class="badge bg-primary rounded-pill">{{ $stats['tataWarga']['usia_balita'] }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Anak-anak (6-12 th)<span class="badge bg-info rounded-pill">{{ $stats['tataWarga']['usia_anak'] }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Remaja (13-17 th)<span class="badge bg-secondary rounded-pill">{{ $stats['tataWarga']['usia_remaja'] }}</span></li>
                             <li class="list-group-item d-flex justify-content-between align-items-center">Muda (18-35 th)<span class="badge bg-warning rounded-pill">{{ $stats['tataWarga']['usia_muda'] }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Lansia (60+ th)<span class="badge bg-danger rounded-pill">{{ $stats['tataWarga']['usia_lansia'] }}</span></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <strong>Berdasarkan Status</strong>
                        <ul class="list-group list-group-flush mb-3">
                             <li class="list-group-item d-flex justify-content-between align-items-center">Janda<span class="badge bg-purple rounded-pill">{{ $stats['tataWarga']['status_janda'] }}</span></li>
                             <li class="list-group-item d-flex justify-content-between align-items-center">Duda<span class="badge bg-purple rounded-pill">{{ $stats['tataWarga']['status_duda'] }}</span></li>
                             <li class="list-group-item d-flex justify-content-between align-items-center">Yatim<span class="badge bg-purple rounded-pill">{{ $stats['tataWarga']['status_yatim'] }}</span></li>
                             <li class="list-group-item d-flex justify-content-between align-items-center">Penduduk Asli<span class="badge bg-success rounded-pill">{{ $stats['tataWarga']['domisili_asli'] }}</span></li>
                             <li class="list-group-item d-flex justify-content-between align-items-center">Pendatang<span class="badge bg-warning rounded-pill">{{ $stats['tataWarga']['domisili_pendatang'] }}</span></li>
                             <li class="list-group-item d-flex justify-content-between align-items-center">Tidak Bekerja<span class="badge bg-danger rounded-pill">{{ $stats['tataWarga']['pengangguran'] }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Kolom Kanan (Lebih Kecil) --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-landmark mr-1"></i> tataAdministrasi</h3></div>
            <div class="card-body">
                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item"><b>Nama Desa</b> <a class="float-right">{{ $stats['tataAdministrasi']['nama_desa'] }}</a></li>
                    <li class="list-group-item"><b>Kepala Desa</b> <a class="float-right">{{ $stats['tataAdministrasi']['nama_kades'] }}</a></li>
                    <li class="list-group-item"><b>Jumlah Perangkat</b> <a class="float-right">{{ $stats['tataAdministrasi']['jumlah_perangkat'] }}</a></li>
                </ul>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-hands-helping mr-1"></i> tataBantuan</h3></div>
            <div class="card-body" style="max-height: 250px; overflow-y: auto;">
                 @forelse($stats['tataWarga']['bantuan'] as $bantuan)
                    <div class="info-box mb-2">
                        <span class="info-box-icon bg-info"><i class="fas fa-gift"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ $bantuan->nama_kategori }}</span>
                            <span class="info-box-number">{{ $bantuan->penerimas_count }} Penerima</span>
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center">Belum ada kategori bantuan.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- tataSurat --}}
    <div class="col-md-4">
        <div class="card"><div class="card-header"><h3 class="card-title"><i class="fas fa-envelope-open-text mr-1"></i> tataSurat</h3></div>
        <div class="card-body">
            <div class="info-box"><span class="info-box-icon bg-secondary"><i class="fas fa-file-alt"></i></span><div class="info-box-content"><span class="info-box-text">Total Pengajuan</span><span class="info-box-number">{{ $stats['tataSurat']['total'] }}</span></div></div>
            <div class="info-box"><span class="info-box-icon bg-warning"><i class="fas fa-hourglass-half"></i></span><div class="info-box-content"><span class="info-box-text">Perlu Diproses</span><span class="info-box-number">{{ $stats['tataSurat']['diproses'] }}</span></div></div>
        </div></div>
    </div>
     {{-- tataLembaga --}}
    <div class="col-md-4">
        <div class="card"><div class="card-header"><h3 class="card-title"><i class="fas fa-sitemap mr-1"></i> tataLembaga</h3></div>
        <div class="card-body">
            <div class="info-box"><span class="info-box-icon bg-purple"><i class="fas fa-university"></i></span><div class="info-box-content"><span class="info-box-text">Total Lembaga</span><span class="info-box-number">{{ $stats['tataLembaga']['total'] }}</span></div></div>
            <div class="info-box"><span class="info-box-icon bg-secondary"><i class="fas fa-calendar-check"></i></span><div class="info-box-content"><span class="info-box-text">Total Kegiatan</span><span class="info-box-number">{{ $stats['tataLembaga']['kegiatan'] }}</span></div></div>
        </div></div>
    </div>
     {{-- tataFasum --}}
    <div class="col-md-4">
        <div class="card"><div class="card-header"><h3 class="card-title"><i class="fas fa-hospital-alt mr-1"></i> tataFasum</h3></div>
        <div class="card-body">
            <div class="info-box"><span class="info-box-icon bg-primary"><i class="fas fa-building"></i></span><div class="info-box-content"><span class="info-box-text">Total Fasilitas</span><span class="info-box-number">{{ $stats['tataFasum']['total'] }}</span></div></div>
            <div class="info-box"><span class="info-box-icon bg-success"><i class="fas fa-thumbs-up"></i></span><div class="info-box-content"><span class="info-box-text">Kondisi Baik</span><span class="info-box-number">{{ $stats['tataFasum']['baik'] }}</span></div></div>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card"><div class="card-header"><h3 class="card-title"><i class="fas fa-hospital-alt mr-1"></i> tataFasum & tataKesehatan</h3></div>
        <div class="card-body">
            <div class="info-box"><span class="info-box-icon bg-primary"><i class="fas fa-building"></i></span><div class="info-box-content"><span class="info-box-text">Total Fasilitas</span><span class="info-box-number">{{ $stats['tataFasum']['total'] }}</span></div></div>
            <div class="info-box"><span class="info-box-icon bg-pink"><i class="fas fa-heartbeat"></i></span><div class="info-box-content"><span class="info-box-text">Ibu Hamil</span><span class="info-box-number">{{ $stats['tataKesehatan']['total_bumil'] }}</span></div></div>
            <div class="info-box"><span class="info-box-icon bg-lightblue"><i class="fas fa-baby-carriage"></i></span><div class="info-box-content"><span class="info-box-text">Balita Terpantau</span><span class="info-box-number">{{ $stats['tataKesehatan']['total_balita'] }}</span></div></div>
        </div></div>
    </div>
</div>
@stop
