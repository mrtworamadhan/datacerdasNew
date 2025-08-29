@extends('layouts.portal')
@section('title', 'Kelola Fasum')
@push('css')
<style>
    .carousel img {
        max-height: 200px;
        object-fit: cover;
    }

    @media (max-width: 576px) {
        .carousel-control-prev,
        .carousel-control-next {
            width: 20%;
        }

        .form-select-sm {
            font-size: 14px;
        }
    }
</style>
@endpush
@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Daftar Fasilitas Umum</h4>
        @unless(Auth::user()->hasRole('kepala_desa'))
            <a href="{{ route('portal.fasum.create', ['subdomain' => app('tenant')->subdomain]) }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Baru
            </a>
        @endunless
    </div>
    <form method="GET" class="row mb-3">
        <div class="col-md-6 mb-2">
            <input type="text" name="q" class="form-control" placeholder="Cari nama fasum..." value="{{ request('q') }}">
        </div>
        <div class="col-md-4 mb-2">
            <select name="status_kondisi" class="form-select">
                <option value="">-- Semua Kondisi --</option>
                <option value="Baik" {{ request('status_kondisi') == 'Baik' ? 'selected' : '' }}>Baik</option>
                <option value="Sedang" {{ request('status_kondisi') == 'Sedang' ? 'selected' : '' }}>Rusak Ringan</option>
                <option value="Rusak" {{ request('status_kondisi') == 'Rusak' ? 'selected' : '' }}>Rusak Berat</option>
            </select>
        </div>
        <div class="col-md-2 mb-2">
            <button class="btn btn-secondary w-100" type="submit">Cari</button>
        </div>
    </form>

    @forelse($fasums as $fasum)
        <div class="card mb-3">
            <div class="card-body">
                @if($fasum->photos->count())
                <div id="carousel_{{ $fasum->id }}" class="carousel slide mb-3" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($fasum->photos as $key => $photo)
                            <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                <img src="{{ asset('storage/' . $photo->path) }}" class="d-block w-100" alt="Foto {{ $fasum->nama_fasum }}">
                            </div>
                        @endforeach
                    </div>

                    @if($fasum->photos->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#carousel_{{ $fasum->id }}" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                        <span class="visually-hidden">Sebelumnya</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carousel_{{ $fasum->id }}" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                        <span class="visually-hidden">Berikutnya</span>
                    </button>
                    @endif
                </div>
                @endif

                <h5 class="card-title">{{ $fasum->nama_fasum }}</h5>
                <p class="card-text text-muted">{{ $fasum->kategori }} - Kondisi: {{ $fasum->status_kondisi }}</p>
                <a href="#" class="btn btn-sm btn-outline-info">Detail</a>
                @unless(Auth::user()->hasRole('kepala_desa'))
                    <a href="{{ route('portal.fasum.edit', ['subdomain' => app('tenant')->subdomain, $fasum->id]) }}" class="btn btn-sm btn-outline-warning">Edit</a>
                    <form method="POST" action="{{ route('portal.fasum.updateStatus', ['subdomain' => app('tenant')->subdomain, $fasum->id]) }}">
                        @csrf
                        @method('PATCH')
                        <div class="input-group mt-2">
                            <select name="status_kondisi" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="Baik" {{ $fasum->status_kondisi == 'Baik' ? 'selected' : '' }}>Baik</option>
                                <option value="Sedang" {{ $fasum->status_kondisi == 'Sedang' ? 'selected' : '' }}>Rusak Ringan</option>
                                <option value="Rusak" {{ $fasum->status_kondisi == 'Rusak' ? 'selected' : '' }}>Rusak Berat</option>
                            </select>
                        </div>
                    </form>
                @endunless
            </div>
        </div>
    @empty
        <div class="alert alert-light text-center">
            <p>Belum ada data fasilitas umum yang diinput.</p>
        </div>
    @endforelse

    <div class="mt-4">
        {{ $fasums->links() }}
    </div>
</div>
@stop