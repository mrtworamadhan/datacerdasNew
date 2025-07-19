@extends('admin.master')

@section('title', 'Edit Perangkat Desa - TataDesa')

@section('content_header')
    <h1 class="m-0 text-dark">Edit Perangkat Desa</h1>
@stop

@section('content_main')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Edit Perangkat Desa</h3>
        </div>
        <form action="{{ route('perangkat-desa.update', $perangkatDesa) }}" method="POST">
            @csrf
            @method('PUT') {{-- Penting untuk method PUT/PATCH --}}
            <div class="card-body">
                <div class="form-group">
                    <label for="nama">Nama Perangkat Desa</label>
                    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" id="nama" placeholder="Masukkan Nama Perangkat Desa" value="{{ old('nama', $perangkatDesa->nama) }}" required>
                    @error('nama')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="jabatan">Jabatan</label>
                    <input type="text" name="jabatan" class="form-control @error('jabatan') is-invalid @enderror" id="jabatan" placeholder="Masukkan Jabatan (contoh: Sekretaris Desa, Kaur Keuangan)" value="{{ old('jabatan', $perangkatDesa->jabatan) }}" required>
                    @error('jabatan')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                {{-- Opsi tautkan akun pengguna dihilangkan dan akan otomatis tertaut ke user yang login di controller --}}
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Update Perangkat Desa</button>
                <a href="{{ route('perangkat-desa.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
