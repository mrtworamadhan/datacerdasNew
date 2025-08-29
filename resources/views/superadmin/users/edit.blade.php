@extends('admin.master')

@section('title', 'Edit Pengguna - TataDesa')

@section('content_header')
    <h1 class="m-0 text-dark">Edit Pengguna</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Edit Pengguna</h3>
        </div>
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                {{-- Input Nama, Email, Password (Tidak berubah) --}}
                <div class="form-group">
                    <label for="name">Nama Pengguna</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name', $user->name) }}" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="email" value="{{ old('email', $user->email) }}" required>
                    @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="password">Password (Biarkan kosong jika tidak ingin diubah)</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password">
                    @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control" id="password_confirmation">
                </div>

                {{-- PERUBAHAN DI SINI: DARI user_type MENJADI role --}}
                <div class="form-group">
                    <label for="role">Role Pengguna</label>
                    <select name="role" class="form-control @error('role') is-invalid @enderror" id="role" required>
                        <option value="">Pilih Role Pengguna</option>
                        @foreach($roles as $role)
                            {{-- Menggunakan method Spatie untuk mengecek role saat ini --}}
                            <option value="{{ $role }}" {{ old('role', $user->getRoleNames()->first()) == $role ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $role)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                {{-- Input Desa (Tidak berubah) --}}
                <div class="form-group">
                    <label for="desa_id">Desa</label>
                    <select name="desa_id" class="form-control @error('desa_id') is-invalid @enderror" id="desa_id" required>
                        <option value="">Pilih Desa</option>
                        @foreach($desas as $desa)
                            <option value="{{ $desa->id }}" {{ old('desa_id', $user->desa_id) == $desa->id ? 'selected' : '' }}>{{ $desa->nama_desa }}</option>
                        @endforeach
                    </select>
                    @error('desa_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Update Pengguna</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@stop