@extends('admin.master')

@section('title', 'Manajemen Perangkat Desa - TataDesa')

@section('content_header')
    <h1 class="m-0 text-dark">Manajemen Perangkat Desa</h1>
@stop

@section('content_main')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Perangkat Desa</h3>
            <div class="card-tools">
                <a href="{{ route('perangkat-desa.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Perangkat Desa
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            @if (session('success'))
                <div class="alert alert-success m-3">
                    {{ session('success') }}
                </div>
            @endif
            <table class="table table-striped table-valign-middle">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>User Akun</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($perangkatDesas as $perangkat)
                        <tr>
                            <td>{{ $perangkat->nama }}</td>
                            <td>{{ $perangkat->jabatan }}</td>
                            <td>{{ $perangkat->user->name ?? '-' }}</td>
                            <td>
                                <a href="{{ route('perangkat-desa.edit', $perangkat) }}" class="btn btn-warning btn-xs">Edit</a>
                                <form action="{{ route('perangkat-desa.destroy', $perangkat) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Yakin ingin menghapus perangkat desa ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Belum ada perangkat desa terdaftar di desa ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection