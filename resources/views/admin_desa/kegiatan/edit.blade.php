@extends('admin.master')
@section('title', 'Edit Kegiatan')
@section('content_header')<h1 class="m-0 text-dark">Edit Kegiatan: {{ $kegiatan->nama_kegiatan }}</h1>@stop
@section('content_main')
<form action="{{ route('lembaga.kegiatan.update', [$lembaga, $kegiatan]) }}" method="POST" enctype="multipart/form-data">
    @method('PUT')
    @include('admin_desa.kegiatan._form')
</form>
{{-- Tambahkan bagian untuk menampilkan dan menghapus foto yang sudah ada --}}
@stop
@push('js')
    {{-- Panggil script resizer --}}
    @include('admin_desa.kegiatan._resizer_js')
    @include('admin_desa.kegiatan._ai_js')
@endpush