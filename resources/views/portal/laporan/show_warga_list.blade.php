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
        <div>
            <h4 class="mb-0">{{ $judul }}</h4>
            <p class="text-muted">{{ $deskripsi }}</p>
        </div>
        <a href="javascript:history.back()" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        @forelse($wargas as $warga)
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $warga->nama_lengkap }}</h5>
                        <p class="card-text text-muted mb-2">
                            No. KK: {{ optional($warga->kartuKeluarga)->nomor_kk ?? 'Belum ada' }}
                        </p>
                        <p class="card-text text-muted small">
                            NIK: {{ $warga->nik ?? 'Belum diisi' }}
                        </p>
                        <div class="mt-auto pt-2">
                            {{-- Tombol ini akan mengarah ke halaman edit warga yang sudah ada --}}
                            <a href="{{ route('portal.warga.edit', ['subdomain' => $subdomain, 'warga' => $warga->id]) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i> Lengkapi Data
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-success text-center">
                    <i class="fas fa-check-circle fa-3x mb-3"></i>
                    <h5 class="mb-0">Kerja Bagus!</h5>
                    <p>Tidak ada data warga yang perlu ditindaklanjuti saat ini.</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Link Paginasi --}}
    <div class="mt-4">
        {{ $wargas->links() }}
    </div>
</div>
@endsection
