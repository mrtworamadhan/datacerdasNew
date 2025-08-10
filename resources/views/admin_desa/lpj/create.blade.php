@extends('admin.master')
@section('title', 'Buat LPJ Kegiatan')
@section('content_header')
    <h1 class="m-0 text-dark">Buat Laporan Pertanggungjawaban (LPJ)</h1>
    <small>Untuk Kegiatan: {{ $kegiatan->nama_kegiatan }}</small>
@stop
@section('content_main')
<form action="{{ route('lpjs.store', $kegiatan->id) }}" method="POST">
    @include('admin_desa.lpj._form', ['kegiatan' => $kegiatan, 'lpj' => new \App\Models\Lpj()])
</form>
@stop
@push('js')
    @include('admin_desa.lpj._ai_js')
@endpush