@extends('admin.master')

@section('title', 'Detail Fasilitas Umum - Data Cerdas')

@section('content_header')
    <h1 class="m-0 text-dark">Detail Fasilitas Umum</h1>
@stop

@section('content_main')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detail Fasilitas Umum: {{ $fasum->nama_fasum }}</h3>
            <div class="card-tools">
                <a href="{{ route('fasum.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Nama Fasum:</strong> {{ $fasum->nama_fasum }}</p>
                    <p><strong>Jenis Fasum:</strong> {{ $fasum->kategori ?? '-' }}</p> {{-- Menggunakan kategori --}}
                    <p><strong>Deskripsi:</strong> {{ $fasum->deskripsi ?? '-' }}</p>
                    <p><strong>Alamat Lengkap:</strong> {{ $fasum->alamat_lengkap ?? '-' }}</p> {{-- Kolom baru --}}
                    <p><strong>Lokasi RW/RT:</strong> RW {{ $fasum->rw->nomor_rw ?? '-' }}/RT {{ $fasum->rt->nomor_rt ?? '-' }}</p>
                    <p><strong>Kondisi:</strong> 
                        @php
                            $badgeClass = 'secondary';
                            if ($fasum->status_kondisi == 'Baik') $badgeClass = 'success'; 
                            elseif ($fasum->status_kondisi == 'Sedang') $badgeClass = 'warning';
                            elseif ($fasum->status_kondisi == 'Rusak') $badgeClass = 'danger';
                        @endphp
                        <span class="badge badge-{{ $badgeClass }}">{{ $fasum->status_kondisi ?? '-' }}</span> 
                    </p>
                    <p><strong>Panjang:</strong> {{ $fasum->panjang ?? '-' }}</p> {{-- Kolom baru --}}
                    <p><strong>Lebar:</strong> {{ $fasum->lebar ?? '-' }}</p> {{-- Kolom baru --}}
                </div>
                <div class="col-md-6">
                    <p><strong>Luas Area:</strong> {{ $fasum->luas_area ?? '-' }}</p> {{-- Kolom baru --}}
                    <p><strong>Kapasitas:</strong> {{ $fasum->kapasitas ?? '-' }}</p> {{-- Kolom baru --}}
                    <p><strong>Kontak Pengelola:</strong> {{ $fasum->kontak_pengelola ?? '-' }}</p> {{-- Kolom baru --}}
                    <p><strong>Status Kepemilikan:</strong> {{ $fasum->status_kepemilikan ?? '-' }}</p> {{-- Kolom baru --}}
                    <p><strong>Koordinat (Latitude, Longitude):</strong> 
                        @if($fasum->latitude && $fasum->longitude)
                            {{ $fasum->latitude }}, {{ $fasum->longitude }}
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $fasum->latitude }},{{ $fasum->longitude }}" target="_blank" class="btn btn-info btn-xs ml-2">Lihat di Peta</a>
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>

            <hr>
            <h4>Foto-foto Fasilitas Umum</h4>
            @if($fasum->photos->isNotEmpty())
                <div class="row">
                    @foreach($fasum->photos as $photo)
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                {{-- Pastikan path gambar benar untuk public access --}}
                                <img src="{{ Storage::url(str_replace('public/', '', $photo->path)) }}" class="card-img-top" alt="Foto Fasum" style="max-height: 200px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $photo->photo_name ?? 'Foto' }}</h5>
                                    <a href="{{ Storage::url(str_replace('public/', '', $photo->path)) }}" target="_blank" class="btn btn-sm btn-primary">Lihat Ukuran Penuh</a>
                                    <form action="{{ route('fasum.destroyPhoto', $photo) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus foto ini?')">Hapus Foto</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted">Tidak ada foto terlampir untuk fasilitas ini.</p>
            @endif

            <hr>
            <div class="d-flex justify-content-end">
                @if (Auth::user()->isAdminDesa() || Auth::user()->isAdminRw() || Auth::user()->isAdminRt())
                    <a href="{{ route('fasum.edit', $fasum) }}" class="btn btn-warning mr-2">Edit Fasum</a>
                    <form action="{{ route('fasum.destroy', $fasum) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus Fasilitas Umum ini?')">Hapus Fasum</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
