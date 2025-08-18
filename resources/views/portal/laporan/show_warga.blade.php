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
            @forelse($penerimaBantuan as $penerima)
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            {{-- Cek apakah bantuan ini untuk individu (warga_id terisi) --}}
                            @if($penerima->warga)
                                <h5 class="card-title">{{ $penerima->warga->nama_lengkap }}</h5>
                                <p class="card-text text-muted">
                                    No. KK: {{ optional($penerima->warga->kartuKeluarga)->no_kk ?? '-' }}
                                </p>
                                <span class="badge bg-primary">Perorangan</span>

                                {{-- Jika bukan, cek apakah bantuan ini untuk keluarga (kartu_keluarga_id terisi) --}}
                            @elseif($penerima->kartuKeluarga)
                                <h5 class="card-title">
                                    KK. {{ optional($penerima->kartuKeluarga->kepalaKeluarga)->nama_lengkap ?? 'Tanpa Kepala KK' }}
                                </h5>
                                <p class="card-text text-muted">
                                    No. KK: {{ $penerima->kartuKeluarga->no_kk }}
                                </p>
                                <span class="badge bg-info">Per-Keluarga</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-light text-center">
                        Tidak ada data penerima bantuan untuk kategori ini di wilayah Anda.
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Link Paginasi --}}
        <div class="mt-4">
            {{ $penerimaBantuan->links() }}
        </div>
    </div>
@endsection