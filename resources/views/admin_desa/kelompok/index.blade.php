@extends('admin.master')

@section('title', 'Manajemen Kelompok Desa - TataDesa')

@section('content_header')
    <h1 class="m-0 text-dark">Manajemen Kelompok Desa</h1>
@stop

@section('content_main')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar kelompok Desa</h3>
            <div class="card-tools">
                <a href="{{ route('kelompok.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah kelompok
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
                        <th>Nama kelompok</th>
                        <th>Deskripsi</th>
                        <th>SK Kepala Desa</th>
                        <th>Jumlah Pengurus</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kelompoks as $kelompok)
                        <tr>
                            <td>{{ $kelompok->nama_kelompok }}</td>
                            <td>{{ Str::limit($kelompok->deskripsi, 50) }}</td>
                            <td>
                                @if ($kelompok->sk_kepala_desa_path)
                                    <a href="{{ $kelompok->sk_kepala_desa_path }}" target="_blank" class="btn btn-info btn-xs">Lihat SK</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $kelompok->pengurus->count() }}</td>
                            <td>    
                                <a href="{{ route('kegiatans.index', ['kelompok_id' => $kelompok->id]) }}" class="btn btn-xs btn-info" title="Kelola Kegiatan">
                                    <i class="fas fa-calendar-alt"></i> Kegiatan
                                </a>
                                <a href="{{ route('kelompok.edit', $kelompok) }}" class="btn btn-warning btn-xs">Edit</a>
                                <form action="{{ route('kelompok.destroy', $kelompok) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Yakin ingin menghapus kelompok ini dan semua pengurusnya?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada kelompok desa terdaftar di desa ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection