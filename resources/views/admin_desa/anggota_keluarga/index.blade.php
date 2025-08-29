@extends('admin.master')

@section('title', 'Anggota Keluarga KK ' . $kartuKeluarga->nomor_kk . ' - TataDesa')

@section('content_header')
    <h1 class="m-0 text-dark">Anggota Keluarga KK {{ $kartuKeluarga->nomor_kk }}</h1>
@stop

@section('content_main')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Anggota Keluarga</h3>
            <div class="card-tools">
                <a href="{{ route('kartu-keluarga.anggota.create', $kartuKeluarga) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Anggota Keluarga
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
            <table class="table table-striped table-valign-middle">
                <thead>
                    <tr>
                        <th>NIK</th>
                        <th>Nama Lengkap</th>
                        <th>Hubungan</th>
                        <th>Jenis Kelamin</th>
                        <th>Tanggal Lahir</th>
                        <th>Pekerjaan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($anggotaKeluargas as $anggota)
                        <tr>
                            <td>{{ $anggota->nik }}</td>
                            <td>
                                {{ $anggota->nama_lengkap }}
                                @if ($kartuKeluarga->kepalaKeluarga && $kartuKeluarga->kepalaKeluarga->id === $anggota->id)
                                    <span class="badge badge-primary">Kepala Keluarga</span>
                                @endif
                            </td>
                            <td>{{ $anggota->hubunganKeluarga->nama ?? '-' }}</td>
                            <td>{{ $anggota->jenis_kelamin }}</td>
                            <td>{{ $anggota->tanggal_lahir->format('d M Y') }}</td>
                            <td>{{ $anggota->pekerjaan->nama ?? '-' }}</td>
                            <td>
                            <a href="{{ route('warga.show', ['subdomain' => request()->subdomain, 'warga' => $anggota->id]) }}" class="btn btn-info btn-xs">
                                <i class="fas fa-eye"></i> Detail
                            </a>    
                            <a href="{{ route('kartu-keluarga.anggota.edit', [$kartuKeluarga, $anggota]) }}" class="btn btn-warning btn-xs">Edit</a>
                                <form action="{{ route('kartu-keluarga.anggota.destroy', [$kartuKeluarga, $anggota]) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Yakin ingin menghapus anggota keluarga ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Belum ada anggota keluarga terdaftar untuk KK ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <a href="{{ route('kartu-keluarga.index') }}" class="btn btn-secondary">Kembali ke Daftar KK</a>
        </div>
    </div>
@endsection
