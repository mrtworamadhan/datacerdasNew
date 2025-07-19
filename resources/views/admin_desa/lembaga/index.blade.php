@extends('admin.master')

@section('title', 'Manajemen Lembaga Desa - TataDesa')

@section('content_header')
    <h1 class="m-0 text-dark">Manajemen Lembaga Desa</h1>
@stop

@section('content_main')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Lembaga Desa</h3>
            <div class="card-tools">
                <a href="{{ route('lembaga.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Lembaga
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
                        <th>Nama Lembaga</th>
                        <th>Deskripsi</th>
                        <th>SK Kepala Desa</th>
                        <th>Jumlah Pengurus</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($lembagas as $lembaga)
                        <tr>
                            <td>{{ $lembaga->nama_lembaga }}</td>
                            <td>{{ Str::limit($lembaga->deskripsi, 50) }}</td>
                            <td>
                                @if ($lembaga->sk_kepala_desa_path)
                                    <a href="{{ $lembaga->sk_kepala_desa_path }}" target="_blank" class="btn btn-info btn-xs">Lihat SK</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $lembaga->pengurus->count() }}</td>
                            <td>    
                                <a href="{{ route('lembaga.kegiatan.index', $lembaga) }}" class="btn btn-xs btn-info" title="Kelola Kegiatan">
                                    <i class="fas fa-calendar-alt"></i> Kegiatan
                                </a>
                                <a href="{{ route('lembaga.edit', $lembaga) }}" class="btn btn-warning btn-xs">Edit</a>
                                <form action="{{ route('lembaga.destroy', $lembaga) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Yakin ingin menghapus lembaga ini dan semua pengurusnya?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada lembaga desa terdaftar di desa ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection