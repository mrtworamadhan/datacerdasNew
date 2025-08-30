@extends('admin.master')

@section('title', 'Manajemen Kartu Keluarga - TataDesa')

@section('content_header')
    <h1 class="m-0 text-dark">Manajemen Kartu Keluarga</h1>
@stop

@section('content_main')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Kartu Keluarga</h3>
            <div class="card-tools">
                <a href="{{ route('kartu-keluarga.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Kartu Keluarga
                </a>
            </div>
        </div>
        <div class="card-body p-0 table-responsive">
            @if (session('success'))
                <div class="alert alert-success m-3">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger m-3">
                    {{ session('error') }}
                </div>
            @endif
            <form action="{{ route('kartu-keluarga.index') }}" method="GET" class="p-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari Nomor KK..." value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-info" type="submit">
                            <i class="fas fa-search"></i> Cari
                        </button>
                        @if(request('search'))
                            <a href="{{ route('kartu-keluarga.index') }}" class="btn btn-secondary">Reset</a>
                        @endif
                    </div>
                </div>
            </form>
            <table class="table table-striped table-valign-middle">
                <thead>
                    <tr>

                        <th>Nomor KK</th>
                        <th>Kepala Keluarga</th>
                        <th>RW/RT</th>
                        <th>Alamat</th>
                        <th>Klasifikasi</th>
                        <th>Jumlah Anggota</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kartuKeluargas as $index => $kk)
                        <tr>
            
                            <td>{{ $kk->nomor_kk }}</td>
                            <td>{{ $kk->kepalaKeluarga->nama_lengkap ?? '-' }}</td>
                            <td>RW {{ $kk->rw->nomor_rw ?? '-' }}/RT {{ $kk->rt->nomor_rt ?? '-' }}</td>
                            <td>{{ Str::limit($kk->alamat_lengkap, 50) }}</td>
                            <td><span class="badge badge-info">{{ $kk->klasifikasi }}</span></td>
                            <td>{{ $kk->wargas->count() }}</td>
                            <td>
                                <a href="{{ route('kartu-keluarga.edit', $kk) }}" class="btn btn-warning btn-xs">Edit KK</a>
                                {{-- Nanti akan ada tombol untuk kelola anggota --}}
                                <a href="{{ route('kartu-keluarga.show', ['subdomain' => request()->subdomain, 'kartu_keluarga' => $kk->id]) }}" class="btn btn-primary btn-xs">
                                    <i class="fas fa-eye"></i> Detail KK
                                </a>
                                <!-- <a href="{{ route('kartu-keluarga.anggota.index', $kk) }}" class="btn btn-info btn-xs">Lihat Anggota</a> -->
                                <form action="{{ route('kartu-keluarga.destroy', $kk) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Yakin ingin menghapus Kartu Keluarga ini dan semua anggotanya?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada Kartu Keluarga yang terdaftar di desa ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $kartuKeluargas->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection
