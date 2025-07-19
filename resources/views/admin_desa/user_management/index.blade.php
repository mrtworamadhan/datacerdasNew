    @extends('admin.master')

    @section('title', 'Manajemen Pengguna Desa - TataDesa')

    @section('content_header')
        <h1 class="m-0 text-dark">Manajemen Pengguna Desa</h1>
    @stop

    @section('content_main')
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Akun RW, RT, dan Kader Posyandu</h3>
                <div class="card-tools">
                    <a href="{{ route('admin_desa.user_management.show_generation_form') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Generate Akun
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
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Tipe Pengguna</th>
                            <th>RW</th>
                            <th>RT</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $user->user_type)) }}</td>
                                <td>{{ $user->rw->nomor_rw ?? '-' }}</td>
                                <td>{{ $user->rt->nomor_rt ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('admin_desa.user_management.edit', $user) }}" class="btn btn-warning btn-xs">Edit</a>
                                    <form action="{{ route('admin_desa.user_management.destroy', $user) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Yakin ingin menghapus pengguna ini?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada akun RW, RT, atau Kader Posyandu yang terdaftar di desa ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endsection
    