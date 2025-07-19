@extends('admin.master')

@section('title', 'Detail Warga RT ' . $rt->nomor_rt)

@section('content_header')
    <h1 class="m-0 text-dark">Detail Warga RT {{ $rt->nomor_rt }} / RW {{ $rt->rw->nomor_rw }}</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Warga</h3>
                <div class="card-tools">
                    <a href="{{ route('wilayah.showRw', $rt->rw) }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar RT
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIK</th>
                                <th>Nama Lengkap</th>
                                <th>No. KK</th>
                                <th>Hubungan Keluarga</th>
                                <th>Jenis Kelamin</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($wargas as $warga)
                                <tr>
                                    <td>{{ $loop->iteration + $wargas->firstItem() - 1 }}</td>
                                    <td>{{ $warga->nik }}</td>
                                    <td>{{ $warga->nama_lengkap }}</td>
                                    <td>{{ $warga->kartuKeluarga->nomor_kk ?? '-' }}</td>
                                    <td>{{ $warga->hubungan_keluarga ?? '-' }}</td>
                                    <td>{{ $warga->jenis_kelamin}}</td>
                                    <td>
                                        {{-- Tombol untuk melihat detail warga --}}
                                        <a href="{{ route('kartu-keluarga.anggota.edit', ['kartu_keluarga' => $warga->kartu_keluarga_id, 'anggotum' => $warga->id]) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        Belum ada data warga yang terdaftar di RT ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                 <div class="mt-3">
                    {{ $wargas->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@stop