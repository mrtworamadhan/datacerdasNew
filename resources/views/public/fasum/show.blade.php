@extends('layouts.public')
@section('title', $fasum->nama_fasum)

@section('content')

@if($fasum->photos->isNotEmpty())
    <div id="photoCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            @foreach($fasum->photos as $key => $photo)
            <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                <img src="{{ asset('storage/' . $photo->path) }}" class="d-block w-100" alt="Foto {{ $fasum->nama_fasum }}">
            </div>
            @endforeach
        </div>
        @if($fasum->photos->count() > 1)
        <button class="carousel-control-prev" type="button" data-bs-target="#photoCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
            <span class="visually-hidden">Sebelumnya</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#photoCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
            <span class="visually-hidden">Berikutnya</span>
        </button>
        @endif
    </div>
@else
    <img src="https://placehold.co/450x300?text=Fasum" class="d-block w-100" alt="Tidak ada foto">
@endif

<div class="p-3">
    <span class="badge bg-primary mb-2">{{ $fasum->kategori }}</span>
    <h1 class="h5 mb-1">{{ $fasum->nama_fasum }}</h1>
    <p class="text-muted mb-3">
        <i class="fas fa-map-marker-alt me-1"></i>
        RW {{ $fasum->rw->nomor_rw ?? '-' }} / RT {{ $fasum->rt->nomor_rt ?? '-' }}
    </p>

    <div class="mb-4">
        <h6>Deskripsi</h6>
        <p class="text-muted">{!! nl2br(e($fasum->deskripsi)) !!}</p>
    </div>

    <div class="card">
        <div class="card-header">
            Informasi Detail
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between">
                Kondisi:
                @if($fasum->status_kondisi == 'Baik')
                    <span class="badge bg-success">Baik</span>
                @elseif($fasum->status_kondisi == 'Dalam Perbaikan')
                    <span class="badge bg-info text-dark">Dalam Perbaikan</span>
                @elseif($fasum->status_kondisi == 'Rusak Ringan')
                    <span class="badge bg-warning text-dark">Rusak Ringan</span>
                @else
                    <span class="badge bg-danger">Rusak Berat</span>
                @endif
            </li>
            @if(is_array($fasum->detail_spesifikasi))
                @foreach($fasum->detail_spesifikasi as $key => $value)
                    @if($value)
                    <li class="list-group-item d-flex justify-content-between">
                        <span><i class="fas fa-ruler-combined me-2 text-muted"></i>{{ ucfirst(str_replace('_', ' ', $key)) }}</span>
                        <span>{{ $value }}</span>
                    </li>
                    @endif
                @endforeach
            @endif
        </ul>
    </div>
</div>

@endsection
