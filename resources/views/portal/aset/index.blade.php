@extends('layouts.portal')
@section('title', 'Daftar Aset Desa')

@push('css')
<style>
    /* Style untuk memastikan gambar di dalam kartu tidak pecah dan mengisi ruang */
    .aset-photo {
        width: 100%;
        height: 100%;
        object-fit: cover; /* Membuat gambar menutupi area tanpa distorsi */
        min-height: 150px; /* Jaga tinggi minimum agar tidak terlalu pendek di mobile */
    }
    .card-photo-container {
        min-height: 150px; /* Tinggi minimum untuk container foto */
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Daftar Aset Desa</h4>
        <a href="javascript:history.back()" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Dasbor
        </a>
    </div>

    @forelse ($asets as $aset)
        <div class="card mb-3 shadow-sm">
            <div class="row g-0">
                {{-- --- PERBAIKAN DI SINI --- --}}
                {{-- KOLOM KIRI (diubah dari col-md-8 menjadi col-7) --}}
                <div class="col-7">
                    <div class="card-body">
                        <h5 class="card-title">{{ $aset->nama_aset }}</h5>
                        <p class="card-text mb-1"><small class="text-muted">Kode: {{ $aset->kode_aset }}</small></p>
                        
                        <ul class="list-group list-group-flush mt-3">
                            <li class="list-group-item px-0">
                                <strong>Lokasi:</strong> {{ $aset->lokasi ?? '-' }}
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between">
                                <span><strong>Tahun:</strong> {{ $aset->tahun_perolehan }}</span>
                                <span>
                                    <strong>Kondisi:</strong> 
                                    <span class="badge bg-{{ $aset->kondisi == 'Baik' ? 'success' : ($aset->kondisi == 'Rusak Ringan' ? 'warning text-dark' : 'danger') }}">
                                        {{ $aset->kondisi }}
                                    </span>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
                {{-- KOLOM KANAN (diubah dari col-md-4 menjadi col-5) --}}
                <div class="col-5 d-flex align-items-center justify-content-center bg-light card-photo-container">
                    @if($aset->foto_aset)
                        <img src="{{ Storage::url($aset->foto_aset) }}" class="aset-photo rounded-end" alt="Foto {{ $aset->nama_aset }}">
                    @else
                        {{-- Placeholder jika tidak ada foto --}}
                        <div class="text-center text-muted p-3">
                            <i class="fas fa-camera fa-2x"></i>
                            <p class="mb-0 mt-2 small">Tidak Ada Foto</p>
                        </div>
                    @endif
                </div>
                {{-- --- AKHIR PERBAIKAN --- --}}
            </div>
        </div>
    @empty
        <div class="alert alert-light text-center">
            <p class="mb-0">Belum ada data aset yang diinput untuk desa ini.</p>
        </div>
    @endforelse

    {{-- Link Paginasi --}}
    <div class="mt-4">
        {{ $asets->links() }}
    </div>
</div>
@endsection