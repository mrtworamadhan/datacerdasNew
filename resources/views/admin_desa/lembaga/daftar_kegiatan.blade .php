@extends('admin.master')
@section('title', 'Kegiatan Lembaga')
@section('content_header')<h1 class="m-0 text-dark">Manajemen Kegiatan: {{ $lembaga->nama_lembaga }}</h1>@stop
@section('content_main')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Kegiatan</h3>
        <div class="card-tools">
            <a href="{{ route('lembaga.kegiatan.create', $lembaga) }}" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Tambah Kegiatan</a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-hover">
            <thead><tr><th>Tanggal</th><th>Nama Kegiatan</th><th>Lokasi</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($kegiatans as $kegiatan)
                <tr>
                    <td>{{ $kegiatan->tanggal_kegiatan->translatedFormat('d M Y') }}</td>
                    <td>
                        <img src="{{ $kegiatan->photos->first() ? asset('storage/' . $kegiatan->photos->first()->path) : '[https://placehold.co/40x40?text=](https://placehold.co/40x40?text=)...' }}" alt="Foto" class="img-circle img-sm mr-2">
                        {{ $kegiatan->nama_kegiatan }}
                    </td>
                    <td>{{ $kegiatan->lokasi_kegiatan }}</td>
                    <td>
                        <a href="{{ route('lembaga.kegiatan.show', [$lembaga, $kegiatan]) }}" class="btn btn-xs btn-info" title="Detail"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('lembaga.kegiatan.edit', [$lembaga, $kegiatan]) }}" class="btn btn-xs btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('lembaga.kegiatan.destroy', [$lembaga, $kegiatan]) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus kegiatan ini?')"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center">Belum ada kegiatan yang dicatat.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop
