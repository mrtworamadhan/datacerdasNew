@extends('admin.master')

@section('title', 'Laporan Peristiwa Kependudukan')

@section('content_header')
    <h1 class="m-0 text-dark">Laporan Peristiwa Kependudukan</h1>
@stop

@section('content_main')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                Laporan untuk Periode: <strong>{{ \Carbon\Carbon::create()->month($bulan)->format('F') }} {{ $tahun }}</strong>
            </h3>
            <div class="card-tools">
                <a href="{{ route('laporan.kependudukan.export-excel', ['subdomain' => request()->subdomain, 'bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel"></i> Export Semua ke Excel
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        @php
            $configs = [
                'lahir' => ['title' => 'Kelahiran', 'color' => 'info'],
                'meninggal' => ['title' => 'Meninggal Dunia', 'color' => 'secondary'],
                'datang' => ['title' => 'Pendatang Baru', 'color' => 'primary'],
                'pindah' => ['title' => 'Pindah Keluar', 'color' => 'warning'],
            ];
        @endphp

        @foreach ($laporan as $jenis => $logs)
        <div class="col-lg-6">
            <div class="card card-{{ $configs[$jenis]['color'] }} card-outline">
                <div class="card-header">
                    <h3 class="card-title">{{ $configs[$jenis]['title'] }} ({{ $logs->count() }})</h3>
                </div>
                <div class="card-body p-0 table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-striped table-head-fixed">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama Warga</th>
                                <th>NIK</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $log)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($log->tanggal_peristiwa)->format('d M Y') }}</td>
                                <td>{{ $log->warga->nama_lengkap ?? 'N/A' }}</td>
                                <td>{{ $log->warga->nik ?? 'N/A' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center">Tidak ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <a href="{{ route('warga.index', ['subdomain' => request()->subdomain, 'bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-secondary mt-3">
        <i class="fas fa-arrow-left"></i> Kembali ke Dasbor
    </a>

@stop