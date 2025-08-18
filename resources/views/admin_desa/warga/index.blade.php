@extends('admin.master')

@section('title', 'Daftar Semua Warga - Desa Cerdas')

@section('content_header')
    <h1 class="m-0 text-dark">Daftar Semua Warga</h1>
@stop

@section('content_main')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Seluruh Warga</h3>
            <div class="card-tools">
                <a href="{{ route('warga.import.form') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-file-import"></i> Impor Data Warga
                </a>
                {{-- Tombol untuk ekspor data warga (jika ada route ekspor) --}}
                {{-- <a href="{{ route('warga.export') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-file-export"></i> Ekspor Data Warga
                {{-- Tombol tambah warga bisa diarahkan ke form tambah anggota keluarga di KK --}}
                {{-- Atau jika ada form tambah warga standalone, bisa kesana --}}
                {{-- <a href="{{ route('kartu-keluarga.anggota.create', /* ID KK default atau pilihan */) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Warga
                </a> --}}
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

            {{-- Form Pencarian --}}
            <form action="{{ route('warga.index') }}" method="GET" class="p-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari NIK, Nama Warga, atau Nomor KK..." value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-info" type="submit">
                            <i class="fas fa-search"></i> Cari
                        </button>
                        @if(request('search'))
                            <a href="{{ route('warga.index') }}" class="btn btn-secondary">Reset</a>
                        @endif
                    </div>
                </div>
            </form>

            <table class="table table-striped table-valign-middle">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>NIK</th>
                        <th>Nama Lengkap</th>
                        <th>No. KK</th>
                        <th>Hubungan Keluarga</th>
                        <th>RW/RT</th>
                        <th>Pendidikan</th>
                        <th>Pekerjaan</th>
                        <th>Status Kependudukan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($wargas as $index => $warga)
                        <tr>
                            <td>{{ $wargas->firstItem() + $index }}</td>
                            <td>{{ $warga->nik }}</td>
                            <td>{{ $warga->nama_lengkap }}</td>
                            <td>{{ $warga->kartuKeluarga->nomor_kk ?? '-' }}</td>
                            <td>{{ $warga->hubunganKeluarga->nama ?? '-' }}</td>
                            <td>RW {{ $warga->rw->nomor_rw ?? '-' }}/RT {{ $warga->rt->nomor_rt ?? '-' }}</td>
                            <td>{{ $warga->pendidikan->nama ?? '-' }}</td>
                            <td>{{ $warga->pekerjaan->nama ?? '-' }}</td>
                            <td><span class="badge badge-info">{{ $warga->statusKependudukan->nama }}</span></td>
                            <td>
                                {{-- Tombol Edit Warga (jika ada route edit) --}}
                                {{-- <a href="{{ route('warga.edit', $warga) }}" class="btn btn-warning btn-xs">Edit</a> --}}
                                {{-- Tombol Lihat Detail KK --}}
                                @if($warga->kartuKeluarga)
                                    <a href="{{ route('kartu-keluarga.anggota.index', $warga->kartuKeluarga) }}" class="btn btn-primary btn-xs" target="_blank">Lihat KK</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">Tidak ada data warga yang ditemukan di wilayah Anda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $wargas->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection
