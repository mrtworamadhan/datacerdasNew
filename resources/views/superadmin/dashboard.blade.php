@extends('admin.master')

@section('title', 'Dashboard Super Admin - Desa Cerdas')

@section('content_header')
    <h1 class="m-0 text-dark">Dashboard Super Admin</h1>
@stop

@section('content_main')
    {{-- Notifikasi Langganan (Opsional, jika Super Admin perlu melihat notifikasi umum) --}}
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-exclamation-triangle"></i> Peringatan!</h5>
            {{ session('warning') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-ban"></i> Error!</h5>
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <p class="mb-0">Selamat datang di Dashboard Super Admin Desa Cerdas!</p>
                    <p>Anda login sebagai **{{ Auth::user()->name }}** ({{ ucfirst(str_replace('_', ' ', Auth::user()->user_type)) }}).</p>
                    <p>Sebagai Super Admin, Anda memiliki kontrol penuh atas platform Desa Cerdas.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik Global Platform --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $totalDesa }}</h3>
                    <p>Total Desa Terdaftar</p>
                </div>
                <div class="icon">
                    <i class="fas fa-city"></i>
                </div>
                <a href="{{ route('desas.index') }}" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $totalAdminDesa }}</h3>
                    <p>Total Admin Desa</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <a href="{{ route('admin.users.index') }}" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $totalActiveSubscriptions }}</h3>
                    <p>Langganan Aktif</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="{{ route('desas.index') }}" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $totalInactiveSubscriptions }}</h3>
                    <p>Langganan Non-Aktif</p>
                </div>
                <div class="icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <a href="{{ route('desas.index') }}" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    {{-- Statistik Pengguna per Tipe --}}
    <div class="card card-info card-outline">
        <div class="card-header">
            <h3 class="card-title">Pengguna Platform per Tipe</h3>
        </div>
        <div class="card-body p-0">
            <ul class="nav nav-pills flex-column">
                @foreach ($usersByType as $type => $count)
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            {{ ucfirst(str_replace('_', ' ', $type)) }}
                            <span class="badge bg-secondary float-right">{{ $count }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@stop
