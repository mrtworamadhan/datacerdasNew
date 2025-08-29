@extends('layouts.portal')
@section('title', 'Laporan Kesehatan Anak')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Laporan Kesehatan Anak</h4>
        <a href="javascript:history.back()" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Dasbor
        </a>
    </div>

    @forelse ($pemeriksaans as $pemeriksaan)
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">{{ $pemeriksaan->warga->nama_lengkap }}</h5>
                <p class="card-text mb-1"><small class="text-muted">NIK: {{ $pemeriksaan->warga->nik ?? 'Belum terdata' }} | Usia: {{ \Carbon\Carbon::parse($pemeriksaan->warga->tanggal_lahir)->diffForHumans(null, true) }}</small></p>
                <div class="mt-2">
                    @if(str_contains($pemeriksaan->status_stunting, 'Stunting'))
                        <span class="badge bg-danger">Stunting</span>
                    @endif
                    @if(str_contains($pemeriksaan->status_wasting, 'Kurang'))
                        <span class="badge bg-warning text-dark">Wasting</span>
                    @endif
                    @if(str_contains($pemeriksaan->status_underweight, 'Kurang'))
                        <span class="badge bg-info">Underweight</span>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-light text-center">
            <p class="mb-0">Tidak ada data anak dengan kondisi khusus yang tercatat.</p>
        </div>
    @endforelse
</div>
@endsection