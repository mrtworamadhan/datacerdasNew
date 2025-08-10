@extends('admin.master')
@section('title', 'Daftar Hadir Posyandu')
@section('content_header')
    <h1 class="m-0 text-dark">
        Daftar Hadir {{ $posyandu->nama_posyandu }} 
        <small>{{ now()->isoFormat('MMMM YYYY') }}</small>
    </h1>
@stop

@section('content_main')
{{-- Tabel 1: Anak yang BELUM Diperiksa --}}
<div class="card">
    <div class="card-header bg-warning"><h3 class="card-title">Anak Belum Diperiksa ({{ $anakBelumDiperiksa->count() }})</h3></div>
    <div class="card-body">
        <table class="table table-sm">
            <thead><tr><th>Nama Anak</th><th>Usia</th><th>Nama Ibu</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($anakBelumDiperiksa as $anak)
                <tr>
                    <td>{{ $anak->warga->nama_lengkap }}</td>
                    <td>
                        @php
                            $age = \Carbon\Carbon::parse($anak->warga->tanggal_lahir)->diff(now());
                            $usiaBulan = $age->y * 12 + $age->m;
                            $usiaHari = $age->d;
                        @endphp
                            {{ $usiaBulan }} bulan, {{ $usiaHari }} hari
                    </td>
                    <td>{{ $anak->nama_ibu }}</td>
                    <td>
                        <form action="{{ route('sesi-posyandu.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="warga_id" value="{{ $anak->warga_id }}">
                            <input type="hidden" name="posyandu_id" value="{{ $posyandu->id }}">
                            <input type="hidden" name="data_kesehatan_anak_id" value="{{ $anak->id }}">
                            <button type="submit" class="btn btn-sm btn-success">Tandai Hadir</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center">Semua anak sudah ditandai hadir.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Tabel 2: Anak yang SUDAH Diperiksa --}}
<div class="card mt-4">
    <div class="card-header bg-success"><h3 class="card-title">Anak Sudah Diperiksa ({{ $anakSudahDiperiksa->count() }})</h3></div>
    <div class="card-body">
         <table class="table table-sm">
            <thead><tr><th>Nama Anak</th><th>Usia</th><th>Nama Ibu</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($anakSudahDiperiksa as $anak)
                <tr>
                    <td>{{ $anak->warga->nama_lengkap }}</td>                    
                    <td>
                        @php
                            $age = \Carbon\Carbon::parse($anak->warga->tanggal_lahir)->diff(now());
                            $usiaBulan = $age->y * 12 + $age->m;
                            $usiaHari = $age->d;
                        @endphp
                            {{ $usiaBulan }} bulan, {{ $usiaHari }} hari
                    </td>
                    <td>{{ $anak->nama_ibu }}</td>
                    <td>
                        <a href="{{ route('kesehatan-anak.show', $anak->id) }}" class="btn btn-sm btn-info">Catat Data</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center">Belum ada anak yang diperiksa.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop