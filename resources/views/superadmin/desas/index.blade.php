@extends('admin.master')

@section('title', 'Manajemen Desa - Desa Cerdas')

@section('content_header')
    <h1 class="m-0 text-dark">Manajemen Desa</h1>
@stop

@section('content_main')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Desa Terdaftar</h3>
            <div class="card-tools">
                <a href="{{ route('desas.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Desa
                </a>
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
                        <th style="width: 5%;">No.</th>
                        <th style="width: 15%;">Nama Desa</th>
                        <th style="width: 15%;">Kepala Desa</th>
                        <th style="width: 20%;">Alamat</th>
                        <th style="width: 10%;">Status Langganan</th>
                        <th style="width: 10%;">Berakhir</th>
                        <th style="width: 15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($desas as $index => $desa)
                        <tr>
                            <td>{{ $desas->firstItem() + $index }}</td>
                            <td>
                                <strong>{{ $desa->nama_desa }}</strong><br>
                                <small>{{ $desa->kecamatan ?? '-' }}, {{ $desa->kota ?? '-' }}</small>
                            </td>
                            <td>{{ $desa->nama_kades ?? '-' }}</td>
                            <td>{{ Str::limit($desa->alamat_desa, 50) ?? '-' }}</td>
                            <td>
                                @php
                                    $badgeClass = 'secondary';
                                    if ($desa->subscription_status == 'active') $badgeClass = 'success';
                                    elseif ($desa->subscription_status == 'trial') $badgeClass = 'info';
                                    elseif ($desa->subscription_status == 'inactive') $badgeClass = 'danger';
                                @endphp
                                <span class="badge badge-{{ $badgeClass }}">{{ ucfirst($desa->subscription_status) }}</span>
                            </td>
                            <td>
                                @if($desa->subscription_ends_at)
                                    {{ $desa->subscription_ends_at->format('d M Y') }}
                                @elseif($desa->trial_ends_at)
                                    Trial: {{ $desa->trial_ends_at->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('desas.edit', $desa) }}" class="btn btn-warning btn-xs"><i class="fas fa-edit"></i> Edit</a>
                                <form action="{{ route('desas.destroy', $desa) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Yakin ingin menghapus desa ini? Semua data terkait desa ini akan terhapus permanen.')"><i class="fas fa-trash"></i> Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada desa yang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $desas->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection
