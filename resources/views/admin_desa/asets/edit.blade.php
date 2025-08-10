@extends('admin.master')
@section('title', 'Edit Aset Desa')
@section('content_header')<h1 class="m-0 text-dark">Edit Aset: {{ $aset->nama_aset }}</h1>@stop

@section('content_main')
<div class="card card-warning card-outline">
    <form action="{{ route('asets.update', $aset->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="form-group">
                <label>Kode Aset</label>
                <input type="text" class="form-control" value="{{ $aset->kode_aset }}" disabled>
                <small class="form-text text-muted">Kode aset tidak dapat diubah.</small>
            </div>
            {{-- Di sini kamu bisa copy-paste semua field dari form create.blade.php --}}
            {{-- Jangan lupa isi valuenya dengan data yang ada, contoh: --}}
            <div class="form-group">
                <label for="nama_aset">Nama Aset</label>
                <input type="text" name="nama_aset" class="form-control" value="{{ old('nama_aset', $aset->nama_aset) }}" required>
            </div>
            {{-- Lanjutkan untuk semua field lainnya... --}}
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-warning">Update Aset</button>
            <a href="{{ route('asets.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@stop