@extends('layouts.anjungan')

@section('content')
    <div class="text-center">
        <h4>Selamat Datang,</h4>
        <h3 class="mb-3">{{ $warga->nama_lengkap }}</h3>
    </div>
    
    <div class="alert alert-light text-center border">
        <p class="mb-0">Silakan pilih jenis surat yang ingin Anda buat di bawah ini.</p>
    </div>

    <div class="row mt-3">
        @forelse($jenisSurats as $surat)
        <div class="col-6 mb-3">
            <a href="{{ route('anjungan.buatSurat', ['subdomain' => app('tenant')->subdomain, 'jenisSurat' => $surat->id]) }}" 
               class="btn btn-outline-primary btn-block p-3" 
               style="height: 100%; white-space: normal; font-size: 1.1rem;">
                <i class="fas fa-file-alt fa-2x mb-2"></i><br>
                {{ $surat->nama_surat }}
            </a>
        </div>
        @empty
        <div class="col-12">
            <p class="text-center text-muted">Saat ini belum ada surat yang tersedia untuk layanan mandiri.</p>
        </div>
        @endforelse
    </div>
    <hr>
    <a href="{{ route('anjungan.index') }}" class="btn btn-secondary btn-block">
        <i class="fas fa-sign-out-alt"></i> Selesai / Ganti Pengguna
    </a>
@stop