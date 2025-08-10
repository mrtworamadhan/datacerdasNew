@extends('admin.master')
@section('title', 'Daftar Aset Desa')
@section('content_header')<h1 class="m-0 text-dark">Daftar Aset Desa</h1>@stop

@section('content_main')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Semua Aset Terdata</h3>
        <div class="card-tools">
            <a href="{{ route('export.asets.excel') }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel"></i> Ekspor Excel
            </a>
            <a href="{{ route('export.asets.pdf') }}" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf"></i> Ekspor PDF
            </a>    
            <a href="{{ route('asets.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Aset Baru
            </a>
        </div>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Kode Aset</th>
                    <th>Nama Aset</th>
                    <th>Tahun</th>
                    <th>Jumlah</th>
                    <th>Kondisi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($asets as $aset)
                <tr>
                    <td>{{ $loop->iteration + $asets->firstItem() - 1 }}</td>
                    <td>{{ $aset->kode_aset }}</td>
                    <td>
                        <strong>{{ $aset->nama_aset }}</strong><br>
                        <small class="text-muted">
                            {{-- Menampilkan hirarki lengkap --}}
                            {{ $aset->subSubKelompok->subKelompok->kelompok->bidang->golongan->nama_golongan }} > 
                            {{ $aset->subSubKelompok->subKelompok->kelompok->bidang->nama_bidang }} > ...
                        </small>
                    </td>
                    <td>{{ $aset->tahun_perolehan }}</td>
                    <td>{{ $aset->jumlah }}</td>
                    <td>{{ $aset->kondisi }}</td>
                    <td>
                        <form action="{{ route('asets.destroy', $aset->id) }}" method="POST" class="d-inline">
                            <a href="{{ route('asets.show', $aset->id) }}" class="btn btn-xs btn-info">Detail</a>
                            <a href="{{ route('asets.edit', $aset->id) }}" class="btn btn-xs btn-warning">Edit</a>
                            
                            {{-- ====================================================== --}}
                            {{-- === INI PERBAIKANNYA === --}}
                            {{-- ====================================================== --}}
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Yakin ingin menghapus aset ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">Belum ada data aset yang diinput.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer clearfix">
        {{ $asets->links() }}
    </div>
</div>
@stop