@extends('admin.master')

@section('title', 'Detail RW ' . $rw->nomor_rw)

@section('content_header')
<h1 class="m-0 text-dark">Detail Wilayah RW {{ $rw->nomor_rw }}</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Rukun Tetangga (RT) di RW {{ $rw->nomor_rw }}</h3>
                <div class="card-tools">
                    <a href="{{ route('wilayah.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar RW
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>No. RT</th>
                                <th>Ketua RT</th>
                                <th class="text-center">Jumlah KK</th>
                                <th class="text-center">Jumlah Warga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rts as $rt)
                                <tr>
                                    <td>{{ $rt->nomor_rt }}</td>
                                    <td>{{ $rt->nama_ketua ?? '-' }}</td>
                                    <td class="text-center">{{ $rt->kartu_keluargas_count }}</td>
                                    <td class="text-center">{{ $rt->wargas_count }}</td>
                                    <td>
                                        <a href="{{ route('wilayah.showRt', $rt) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-users"></i> Lihat Warga
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">
                                        Belum ada data RT yang terdaftar di RW ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $rts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@stop