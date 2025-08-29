@extends('admin.master')

@section('title', 'Manajemen Hak Akses')

@section('content_header')
    <h1 class="m-0 text-dark">Manajemen Hak Akses Pengguna</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Pengguna Staf</h3>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role Bawaan</th>
                        <th style="width: 150px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $key => $user)
                        <tr>
                            <td>{{ $users->firstItem() + $key }}.</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->getRoleNames()->map(fn($role) => ucfirst(str_replace('_', ' ', $role)))->implode(', ') }}</td>
                            <td>
                                <a href="{{ route('permissions.edit', ['userId' => $user->id]) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-key"></i> Ubah Hak Akses
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada pengguna staf di desa ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $users->links() }}
        </div>
    </div>
@stop