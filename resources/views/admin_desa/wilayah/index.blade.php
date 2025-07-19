@extends('admin.master')

@section('title', 'Manajemen Wilayah')

@section('content_header')
<h1 class="m-0 text-dark">Manajemen Wilayah Kependudukan</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Rukun Warga (RW)</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>No. RW</th>
                                <th>Ketua RW</th>
                                <th class="text-center">Jumlah RT</th>
                                <th class="text-center">Jumlah KK</th>
                                <th class="text-center">Jumlah Warga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rws as $rw)
                                <tr>
                                    <td>{{ $rw->nomor_rw }}</td>
                                    <td>{{ $rw->nama_ketua ?? '-' }}</td>
                                    <td class="text-center">{{ $rw->rts_count }}</td>
                                    <td class="text-center">{{ $rw->kartu_keluargas_count }}</td>
                                    <td class="text-center">{{ $rw->wargas_count }}</td>
                                    <td>
                                        <a href="{{ route('wilayah.showRw', $rw) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Lihat Detail RT
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">
                                        Belum ada data RW yang terdaftar. Silakan generate pengguna RW terlebih dahulu.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $rws->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@stop