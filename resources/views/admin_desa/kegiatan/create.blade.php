@extends('admin.master')
@section('title', 'Tambah Kegiatan Baru')
@section('content_header')<h1 class="m-0 text-dark">Tambah Kegiatan Baru untuk {{ $lembaga->nama_lembaga }}</h1>@stop
@section('content_main')
<form action="{{ route('lembaga.kegiatan.store', $lembaga) }}" method="POST" enctype="multipart/form-data">
    @include('admin_desa.kegiatan._form')
</form>
@stop
@push('js')
    {{-- Panggil script resizer --}}
    @include('admin_desa.kegiatan._resizer_js')
    @include('admin_desa.kegiatan._ai_js')
@endpush