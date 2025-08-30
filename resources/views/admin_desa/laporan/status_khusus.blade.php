@extends('admin.master')

@section('title', 'Laporan Status Khusus: ' . $judul)

@section('content_header')
    <h1 class="m-0 text-dark">Laporan Status Khusus</h1>
@stop

@section('content_main')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                Daftar Warga dengan Status: <strong>{{ $judul }}</strong>
            </h3>
            <div class="card-tools">
                <button class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Export Excel</button>
            </div>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>NIK</th>
                        <th>Jenis Kelamin</th>
                        <th>Alamat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($wargaList as $index => $warga)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $warga->nama_lengkap }}</td>
                            <td>{{ $warga->nik }}</td>
                            <td>{{ $warga->jenis_kelamin }}</td>
                            <td>{{ $warga->alamat_lengkap }} (RW {{ $warga->rw->nomor_rw ?? '' }}/RT {{ $warga->rt->nomor_rt ?? '' }})</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada warga dengan status ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
             <a href="{{ route('warga.index', ['subdomain' => request()->subdomain]) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Dasbor
            </a>
        </div>
    </div>
@stop