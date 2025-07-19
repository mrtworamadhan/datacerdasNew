@extends('admin.master')
@section('title', 'Detail Laporan Kegiatan')
@section('content_header')
    <div>
        <h1 class="m-0 text-dark">Laporan Kegiatan: {{ $kegiatan->nama_kegiatan }}</h1>
        <small>Diselenggarakan oleh {{ $lembaga->nama_lembaga }}</small>
    </div>
@stop
@section('content_main')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detail Laporan</h3>
                <div class="card-tools">
                    <a href="{{ route('lembaga.kegiatan.cetak', [$lembaga, $kegiatan]) }}" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-print"></i> Cetak Laporan (PDF)</a>
                    <a href="{{ route('lembaga.kegiatan.edit', [$lembaga, $kegiatan]) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>
                </div>
            </div>
            <div class="card-body">
                {{-- Bagian Pendahuluan --}}
                <div class="mb-5">
                    <h4>1. Pendahuluan</h4>
                    <hr>
                    <dl class="row">
                        <dt class="col-sm-3">Nama Kegiatan</dt>
                        <dd class="col-sm-9">{{ $kegiatan->nama_kegiatan }}</dd>

                        <dt class="col-sm-3">Latar Belakang</dt>
                        <dd class="col-sm-9">{!! nl2br(e($kegiatan->latar_belakang)) !!}</dd>

                        <dt class="col-sm-3">Tujuan dan Sasaran</dt>
                        <dd class="col-sm-9">{!! nl2br(e($kegiatan->tujuan_kegiatan)) !!}</dd>

                        <dt class="col-sm-3">Waktu dan Tempat</dt>
                        <dd class="col-sm-9">{{ $kegiatan->tanggal_kegiatan->translatedFormat('l, d F Y') }} di {{ $kegiatan->lokasi_kegiatan }}</dd>
                    </dl>
                </div>

                {{-- Bagian Pelaksanaan --}}
                <div class="mb-5">
                    <h4>2. Pelaksanaan Kegiatan</h4>
                    <hr>
                    <dl class="row">
                         <dt class="col-sm-3">Uraian Kegiatan</dt>
                        <dd class="col-sm-9">{!! nl2br(e($kegiatan->deskripsi_kegiatan)) !!}</dd>
                    </dl>
                </div>
                
                 {{-- Bagian Anggaran --}}
                <div class="mb-5">
                    <h4>3. Rencana dan Realisasi Anggaran</h4>
                    <hr>
                    <dl class="row">
                        <dt class="col-sm-3">Sumber Dana</dt>
                        <dd class="col-sm-9">{{ $kegiatan->sumber_dana ?? '-' }}</dd>

                         <dt class="col-sm-3">Anggaran Biaya</dt>
                        <dd class="col-sm-9">Rp {{ number_format($kegiatan->anggaran_biaya, 2, ',', '.') }}</dd>
                    </dl>
                </div>

                {{-- Bagian Penutup --}}
                <div class="mb-5">
                    <h4>4. Penutup</h4>
                    <hr>
                    <p>{!! nl2br(e($kegiatan->penutup)) !!}</p>
                </div>

                {{-- Lampiran Dokumentasi --}}
                <div class="mb-5">
                    <h4>5. Lampiran: Dokumentasi Kegiatan</h4>
                    <hr>
                    <div class="row">
                         @forelse($kegiatan->photos as $photo)
                            <div class="col-md-4 mb-3">
                                <a href="{{ asset('storage/' . $photo->path) }}" data-toggle="lightbox" data-gallery="gallery-kegiatan">
                                    <img src="{{ asset('storage/' . $photo->path) }}" class="img-fluid rounded shadow-sm" alt="Dokumentasi Kegiatan">
                                </a>
                            </div>
                        @empty
                            <p class="col-12">Tidak ada dokumentasi foto.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@push('js')
    {{-- Script untuk mengaktifkan lightbox galeri foto --}}
    <script src="[https://cdn.jsdelivr.net/npm/bs5-lightbox@1.8.3/dist/index.bundle.min.js](https://cdn.jsdelivr.net/npm/bs5-lightbox@1.8.3/dist/index.bundle.min.js)"></script>
@endpush
