@extends('layouts.portal')
@section('title', 'Riwayat Pengajuan Surat')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Riwayat Pengajuan</h4>
        <a href="{{ route('portal.surat.create', ['subdomain' => app('tenant')->subdomain]) }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Pengajuan
        </a>
    </div>
    @forelse($pengajuans as $pengajuan)
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">{{ $pengajuan->jenisSurat->nama_surat }}</h5>
                <p class="card-text mb-1">Untuk: <strong>{{ $pengajuan->warga->nama_lengkap }}</strong></p>
                <p class="card-text"><small class="text-muted">Tanggal: {{ $pengajuan->tanggal_pengajuan->format('d M Y') }} | Status: <span class="badge bg-info">{{ $pengajuan->status_permohonan }}</span></small></p>
            </div>
        </div>
    @empty
        <div class="alert alert-light text-center">
            <p>Anda belum pernah membuat pengajuan surat.</p>
        </div>
    @endforelse

    <div class="mt-4">{{ $pengajuans->links() }}</div>
</div>
@stop