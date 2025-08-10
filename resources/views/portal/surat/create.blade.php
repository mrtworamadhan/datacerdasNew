@extends('layouts.portal')
@section('title', 'Buat Pengajuan Surat')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header"><h4 class="card-title">Formulir Pengajuan Surat</h4></div>
        <div class="card-body">
            <form action="{{ route('portal.surat.store', ['subdomain' => app('tenant')->subdomain]) }}" method="POST">
                @csrf
                {{-- Form ini akan kita isi dengan adaptasi dari form admin --}}
                <p class="text-center">Formulir pengajuan surat akan ditampilkan di sini.</p>
                <button type="submit" class="btn btn-primary">Ajukan Surat</button>
            </form>
        </div>
    </div>
</div>
@stop