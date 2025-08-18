@extends('layouts.portal')
@section('title', $judul)

@section('content')
<div class="container">
    @foreach (['success', 'error'] as $msg)
        @if (session($msg))
            <div class="alert alert-{{ $msg == 'error' ? 'danger' : $msg }} alert-dismissible fade show" role="alert">
                {{ session($msg) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    @endforeach
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">{{ $judul }}</h4>
        <a href="javascript:history.back()" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        @forelse($kartuKeluargas as $kk)
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-title">{{ optional($kk->kepalaKeluarga)->nama_lengkap ?? 'Belum Ditentukan' }}</h6>
                        <p class="card-text text-muted">No. KK: {{ $kk->nomor_kk }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light text-center">
                    Tidak ada data untuk ditampilkan.
                </div>
            </div>
        @endforelse
    </div>

    {{-- Link Paginasi --}}
    <div class="mt-4">
        {{ $kartuKeluargas->links() }}
    </div>
</div>
@endsection