@extends('admin.master')
@section('title', 'Manajemen Jenis Surat')
@section('content_header')<h1 class="m-0 text-dark">Manajemen Jenis Surat (Template)</h1>@stop
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Template Surat</h3>
                <div class="card-tools">
                    <a href="{{ route('jenis-surat.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Tambah Template</a>
                </div>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-hover table-striped">
                    <thead><tr><th>Nama Surat</th><th>Kode Klasifikasi</th><th>Judul di Kop</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @forelse ($jenisSurats as $jenis)
                        <tr>
                            <td>{{ $jenis->nama_surat }}</td>
                            <td><span class="badge badge-info">{{ $jenis->klasifikasi->kode }}</span></td>
                            <td>{{ $jenis->judul_surat }}</td>
                            <td>
                                <a href="{{ route('jenis-surat.show', $jenis) }}" class="btn btn-xs btn-secondary" title="Preview"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('jenis-surat.edit', $jenis) }}" class="btn btn-xs btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('jenis-surat.destroy', $jenis) }}" method="POST" style="display:inline-block;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger" title="Hapus" onclick="return confirm('Yakin?')"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center">Belum ada template surat yang dibuat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                 <div class="mt-3">{{ $jenisSurats->links() }}</div>
            </div>
        </div>
    </div>
</div>
@stop