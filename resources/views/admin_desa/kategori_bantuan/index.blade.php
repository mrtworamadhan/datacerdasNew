@extends('admin.master')

@section('title', 'Manajemen Kategori Bantuan - TataDesa')

@section('content_header')
    <h1 class="m-0 text-dark">Manajemen Kategori Bantuan</h1>
@stop

@section('content_main')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Kategori Bantuan</h3>
            <div class="card-tools">
                @if (Auth::user()->isAdminDesa() || Auth::user()->isSuperAdmin())
                    <a href="{{ route('kategori-bantuan.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Kategori Bantuan
                    </a>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
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
            <table class="table table-striped table-valign-middle">
                <thead>
                    <tr>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th>Kriteria</th>
                        <th>Banyak Penerima per KK</th>
                        <th>Status Pengajuan</th> {{-- Kolom baru --}}
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kategoriBantuans as $kategori)
                        <tr>
                            <td>{{ $kategori->nama_kategori }}</td>
                            <td>{{ Str::limit($kategori->deskripsi, 50) }}</td>
                            <td>
                                @php
                                    // Pastikan kriteria_json adalah array. Jika string, decode. Jika null, default ke array kosong.
                                    $kriteria = $kategori->kriteria_json;
                                    if (is_string($kriteria)) {
                                        $kriteria = json_decode($kriteria, true);
                                    }
                                    if (!is_array($kriteria)) {
                                        $kriteria = [];
                                    }
                                @endphp

                                @if (!empty($kriteria))
                                    @foreach($kriteria as $key => $value)
                                        @if(is_array($value))
                                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ implode(', ', $value) }}<br>
                                        @elseif(is_bool($value))
                                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value ? 'Ya' : 'Tidak' }}<br>
                                        @else
                                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}<br>
                                        @endif
                                    @endforeach
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($kategori->allow_multiple_recipients_per_kk)
                                    <span class="badge badge-success">Ya</span>
                                @else
                                    <span class="badge badge-secondary">Tidak</span>
                                @endif
                            </td>
                            <td> {{-- Kolom baru --}}
                                @if ($kategori->is_active_for_submission)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-danger">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('kategori-bantuan.penerima.index', $kategori) }}" class="btn btn-info btn-xs">Lihat Pengajuan</a> {{-- Tombol baru --}}
                                @if (Auth::user()->isAdminDesa() || Auth::user()->isSuperAdmin())
                                    <a href="{{ route('kategori-bantuan.edit', $kategori) }}" class="btn btn-warning btn-xs">Edit</a>
                                    <form action="{{ route('kategori-bantuan.destroy', $kategori) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Yakin ingin menghapus kategori bantuan ini? Semua penerima bantuan di bawah kategori ini juga akan terhapus.')">Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada kategori bantuan yang terdaftar di desa ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
