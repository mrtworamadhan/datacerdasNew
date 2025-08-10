@extends('layouts.portal')
@section('title', 'Sesi Posyandu')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Pilih Sesi Pemeriksaan</h4>
        </div>
        <div class="card-body">
            <p>Silakan pilih periode bulan dan tahun untuk melihat laporan atau memulai pendataan.</p>
            <div class="list-group">
                @foreach($daftarBulan as $sesi)
                    <a href="{{ route('portal.posyandu.sesi.show', ['subdomain' => app('tenant')->subdomain, 'tahun' => $sesi['tahun'], 'bulan' => $sesi['bulan']]) }}" 
                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        {{ $sesi['nama'] }}
                        <i class="fas fa-chevron-right"></i>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
@stop