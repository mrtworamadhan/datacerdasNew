@extends('layouts.portal')
@section('title', 'Arsip Laporan Posyandu')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header"><h4 class="card-title">Arsip Laporan Bulanan</h4></div>
        <div class="card-body">
            <p>Berikut adalah daftar laporan bulanan berdasarkan data pemeriksaan yang sudah Anda input.</p>
            <div class="list-group">
                @forelse($daftarLaporan as $laporan)
                    @php
                        $tanggal = \Carbon\Carbon::createFromDate($laporan->tahun, $laporan->bulan);
                    @endphp
                    <a href="{{ route('portal.posyandu.laporan.generate', ['subdomain' => app('tenant')->subdomain, 'tahun' => $laporan->tahun, 'bulan' => $laporan->bulan]) }}" 
                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" target="_blank">
                        Laporan Bulan {{ $tanggal->isoFormat('MMMM YYYY') }}
                        <span class="btn btn-sm btn-danger"><i class="fas fa-file-pdf"></i> Generate PDF</span>
                    </a>
                @empty
                    <div class="alert alert-light text-center">
                        <p>Belum ada data pemeriksaan yang bisa dijadikan laporan.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@stop