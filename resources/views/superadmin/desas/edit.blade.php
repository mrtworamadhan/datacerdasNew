@extends('admin.master')

@section('title', 'Edit Desa - Desa Cerdas')

@section('content_header')
    <h1 class="m-0 text-dark">Edit Desa</h1>
@stop

@section('content_main')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Edit Desa: {{ $desa->nama_desa }}</h3>
        </div>
        <form action="{{ route('desas.update', $desa) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <h4>Data Desa</h4>
                <div class="form-group">
                    <label for="nama_desa">Nama Desa</label>
                    <input type="text" name="nama_desa" class="form-control @error('nama_desa') is-invalid @enderror" id="nama_desa" value="{{ old('nama_desa', $desa->nama_desa) }}" required>
                    @error('nama_desa') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="nama_kades">Nama Kepala Desa</label>
                    <input type="text" name="nama_kades" class="form-control @error('nama_kades') is-invalid @enderror" id="nama_kades" value="{{ old('nama_kades', $desa->nama_kades) }}" placeholder="Contoh: Budi Santoso">
                    @error('nama_kades') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="alamat_desa">Alamat Desa Lengkap</label>
                    <textarea name="alamat_desa" class="form-control @error('alamat_desa') is-invalid @enderror" id="alamat_desa" rows="3">{{ old('alamat_desa', $desa->alamat_desa) }}</textarea>
                    @error('alamat_desa') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="kecamatan">Kecamatan</label>
                            <input type="text" name="kecamatan" class="form-control @error('kecamatan') is-invalid @enderror" id="kecamatan" value="{{ old('kecamatan', $desa->kecamatan) }}">
                            @error('kecamatan') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="kota">Kota/Kabupaten</label>
                            <input type="text" name="kota" class="form-control @error('kota') is-invalid @enderror" id="kota" value="{{ old('kota', $desa->kota) }}">
                            @error('kota') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="provinsi">Provinsi</label>
                            <input type="text" name="provinsi" class="form-control @error('provinsi') is-invalid @enderror" id="provinsi" value="{{ old('provinsi', $desa->provinsi) }}">
                            @error('provinsi') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="kode_pos">Kode Pos</label>
                            <input type="text" name="kode_pos" class="form-control @error('kode_pos') is-invalid @enderror" id="kode_pos" value="{{ old('kode_pos', $desa->kode_pos) }}">
                            @error('kode_pos') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <hr>
                <h4>Pengaturan Langganan</h4>
                <div class="form-group">
                    <label for="subscription_status">Status Langganan</label>
                    <select name="subscription_status" class="form-control @error('subscription_status') is-invalid @enderror" id="subscription_status" required>
                        @foreach($subscriptionStatusOptions as $option)
                            <option value="{{ $option }}" {{ old('subscription_status', $desa->subscription_status) == $option ? 'selected' : '' }}>{{ ucfirst($option) }}</option>
                        @endforeach
                    </select>
                    @error('subscription_status') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="trial_ends_at">Tanggal Berakhir Masa Percobaan (Opsional)</label>
                    <input type="date" name="trial_ends_at" class="form-control @error('trial_ends_at') is-invalid @enderror" id="trial_ends_at" value="{{ old('trial_ends_at', $desa->trial_ends_at ? $desa->trial_ends_at->format('Y-m-d') : '') }}">
                    @error('trial_ends_at') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="subscription_ends_at">Tanggal Berakhir Langganan (Opsional)</label>
                    <input type="date" name="subscription_ends_at" class="form-control @error('subscription_ends_at') is-invalid @enderror" id="subscription_ends_at" value="{{ old('subscription_ends_at', $desa->subscription_ends_at ? $desa->subscription_ends_at->format('Y-m-d') : '') }}">
                    @error('subscription_ends_at') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Update Desa</button>
                <a href="{{ route('desas.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
