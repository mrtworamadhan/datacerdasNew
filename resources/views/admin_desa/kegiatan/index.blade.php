@extends('admin.master')
@section('title', 'Manajemen Kegiatan Desa')
@section('content_header')
    <h1 class="m-0 text-dark">
        @if($penyelenggaraSpesifik)
            Manajemen Kegiatan: {{ $penyelenggaraSpesifik }}
        @else
            Manajemen Kegiatan Desa (Semua)
        @endif
    </h1>
@stop

@section('content_main')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Semua Proposal & Laporan Pertanggungjawaban (LPJ)</h3>
        <div class="card-tools">
            <a href="{{ route('kegiatans.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Buat Proposal Kegiatan Baru
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nama Kegiatan</th>
                    <th>Penyelenggara</th>
                    <th>Anggaran Diajukan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kegiatans as $kegiatan)
                <tr>
                    <td>{{ $kegiatan->tanggal_kegiatan->translatedFormat('d M Y') }}</td>
                    <td>{{ $kegiatan->nama_kegiatan }}</td>
                    <td>
                        @if($kegiatan->kegiatanable_type == 'App\Models\Lembaga')
                            <span class="badge badge-info">Lembaga</span> {{ $kegiatan->kegiatanable->nama_lembaga }}
                        @elseif($kegiatan->kegiatanable_type == 'App\Models\Kelompok')
                            <span class="badge badge-success">Kelompok</span> {{ $kegiatan->kegiatanable->nama_kelompok }}
                        @endif
                    </td>
                    <td>Rp {{ number_format($kegiatan->anggaran_biaya, 0, ',', '.') }}</td>
                    <td>
                        {{-- Logika badge untuk status --}}
                        @php
                            $status = $kegiatan->status;
                            $badgeColor = 'light';
                            if ($status == 'Proposal Diajukan') $badgeColor = 'info';
                            if ($status == 'Disetujui') $badgeColor = 'primary';
                            if ($status == 'Selesai') $badgeColor = 'success';
                        @endphp
                        <span class="badge badge-{{ $badgeColor }}">{{ $status }}</span>
                    </td>
                    <td>
                        <div class="btn-group">
                            {{-- Tombol Detail/Show yang universal --}}
                            <a href="{{ route('kegiatans.show', $kegiatan->id) }}" class="btn btn-xs btn-info" title="Lihat Detail & Keuangan">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                            
                            {{-- Tombol Edit & Hapus --}}
                            <a href="{{ route('kegiatans.edit', $kegiatan->id) }}" class="btn btn-xs btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('kegiatans.destroy', $kegiatan->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus proposal kegiatan ini?')"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">Belum ada kegiatan yang dibuat.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $kegiatans->links() }}
    </div>
</div>
@stop