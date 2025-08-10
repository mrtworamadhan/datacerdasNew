@extends('layouts.portal') {{-- Menggunakan layout portal yang sudah kita buat --}}
@section('title', 'Portal Layanan')

@section('content')
<div class="container">
    <div class="text-center mb-4">
        <h3>Selamat Datang, {{ $user->name }}!</h3>
        <p class="text-muted">Pilih layanan yang ingin Anda gunakan.</p>
    </div>

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
        @endif
    </div>
</div>
@stop