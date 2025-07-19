@extends('admin.master')
@section('title', 'Edit Data Anak')
@section('content_header')<h1 class="m-0 text-dark">Edit Data Dasar Anak</h1>@stop
@section('content_main')
<form action="{{ route('kesehatan-anak.update', $kesehatanAnak) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="card">
        <div class="card-body">
            <div class="form-group">
                <label>Nama Anak</label>
                <input type="text" class="form-control" value="{{ $kesehatanAnak->warga->nama_lengkap }}" disabled>
            </div>
            {{-- ... (form input lain sama seperti create, tapi dengan value dari $kesehatanAnak) ... --}}
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Update Data</button>
            <a href="{{ route('kesehatan-anak.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </div>
</form>
@stop
