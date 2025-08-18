@extends('layouts.portal')
@section('title', 'Edit Data Warga')

@section('content')
    <div class="container">
        @if (session('error'))
            <div class="alert alert-danger m-3">
                {{ session('error') }}
            </div>
        @endif
        <div class="card shadow-sm">
            <div class="card-header">
                <h4 class="mb-0">Edit Data: <strong>{{ $warga->nama_lengkap }}</strong></h4>
                @if($warga->status_data == 'Data Sementara')
                    <span class="badge bg-warning text-dark">Data Belum Terverifikasi</span>
                @endif
            </div>
            <div class="card-body">
                <form action="{{ route('portal.warga.update', ['subdomain' => $subdomain, 'warga' => $warga->id]) }}"
                    method="POST">
                    @csrf
                    @method('PUT')
                    {{-- NIK --}}
                    <div class="form-group">
                        <label for="nik">NIK</label>
                        <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" id="nik"
                            value="{{ old('nik', $warga->nik) }}" required maxlength="16">
                        @error('nik') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="nama_lengkap">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap"
                            class="form-control @error('nama_lengkap') is-invalid @enderror" id="nama_lengkap"
                            value="{{ old('nama_lengkap', $warga->nama_lengkap) }}" required>
                        @error('nama_lengkap') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tempat_lahir">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir"
                                    class="form-control @error('tempat_lahir') is-invalid @enderror" id="tempat_lahir"
                                    value="{{ old('tempat_lahir', $warga->tempat_lahir) }}" required>
                                @error('tempat_lahir') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tanggal_lahir">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir"
                                    class="form-control @error('tanggal_lahir') is-invalid @enderror" id="tanggal_lahir"
                                    value="{{ old('tanggal_lahir', $warga->tanggal_lahir->format('Y-m-d')) }}" required>
                                @error('tanggal_lahir') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="jenis_kelamin">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror"
                            id="jenis_kelamin" required>
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            @foreach($jenisKelaminOptions as $option)
                                <option value="{{ $option }}" {{ old('jenis_kelamin', $warga->jenis_kelamin) == $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                        @error('jenis_kelamin') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="agama_id">Agama</label>
                        <select name="agama_id" id="agama_id" class="form-control" required>
                            <option value="">-- Pilih Agama --</option>
                            @foreach($agamaOptions as $id => $nama)
                                <option value="{{ $id }}" {{ old('agama_id', $warga->agama_id) == $id ? 'selected' : '' }}>
                                    {{ $nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status_perkawinan_id">Status Perkawinan</label>
                        <select name="status_perkawinan_id" id="status_perkawinan_id" class="form-control" required>
                            <option value="">-- Pilih Status Perkawinan --</option>
                            @foreach($statusPerkawinanOptions as $id => $nama)
                                <option value="{{ $id }}" {{ old('status_perkawinan_id', $warga->status_perkawinan_id) == $id ? 'selected' : '' }}>
                                    {{ $nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="pekerjaan_id">Pekerjaan</label>
                        <select name="pekerjaan_id" id="pekerjaan_id" class="form-control" required>
                            <option value="">-- Pilih Pekerjaan --</option>
                            @foreach($pekerjaanOptions as $id => $nama)
                                <option value="{{ $id }}" {{ old('pekerjaan_id', $warga->pekerjaan_id) == $id ? 'selected' : '' }}>
                                    {{ $nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="pendidikan_id">Pendidikan</label>
                        <select name="pendidikan_id" id="pendidikan_id" class="form-control" required>
                            <option value="">-- Pilih Pendidikan --</option>
                            @foreach($pendidikanOptions as $id => $nama)
                                <option value="{{ $id }}" {{ old('pendidikan_id', $warga->pendidikan_id) == $id ? 'selected' : '' }}>
                                    {{ $nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="kewarganegaraan">Kewarganegaraan</label>
                        <select name="kewarganegaraan" class="form-control @error('kewarganegaraan') is-invalid @enderror"
                            id="kewarganegaraan" required>
                            <option value="">-- Pilih Kewarganegaraan --</option>
                            @foreach($kewarganegaraanOptions as $option)
                                <option value="{{ $option }}" {{ old('kewarganegaraan', $warga->kewarganegaraan) == $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                        @error('kewarganegaraan') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="golongan_darah_id">Golongan Darah</label>
                        <select name="golongan_darah_id" id="golongan_darah_id" class="form-control">
                            <option value="">-- Pilih Golongan Darah --</option>
                            @foreach($golonganDarahOptions as $id => $nama)
                                <option value="{{ $id }}" {{ old('golongan_darah_id', $warga->golongan_darah_id) == $id ? 'selected' : '' }}>
                                    {{ $nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="alamat_lengkap">Alamat Lengkap (Sesuai KTP)</label>
                        <textarea name="alamat_lengkap" class="form-control @error('alamat_lengkap') is-invalid @enderror"
                            id="alamat_lengkap" rows="3"
                            required>{{ old('alamat_lengkap', $warga->alamat_lengkap) }}</textarea>
                        @error('alamat_lengkap') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="hubungan_keluarga_id">Hubungan Keluarga</label>
                        <select name="hubungan_keluarga_id" id="hubungan_keluarga_id" class="form-control" required>
                            <option value="">-- Pilih Hubungan --</option>
                            @foreach($hubunganKeluargaOptions as $id => $nama)
                                <option value="{{ $id }}" {{ old('hubungan_keluarga_id', $warga->hubungan_keluarga_id) == $id ? 'selected' : '' }}>
                                    {{ $nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nama_ayah_kandung">Ayah kandung</label>
                        <input type="text" name="nama_ayah_kandung"
                            class="form-control @error('nama_ayah_kandung') is-invalid @enderror" id="nama_ayah_kandung"
                            value="{{ old('nama_ayah_kandung', $warga->nama_ayah_kandung) }}" required>
                        @error('nama_ayah_kandung') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="nama_ibu_kandung">Ibu Kandung</label>
                        <input type="text" name="nama_ibu_kandung"
                            class="form-control @error('nama_ibu_kandung') is-invalid @enderror" id="nama_ibu_kandung"
                            value="{{ old('nama_ibu_kandung', $warga->nama_ibu_kandung) }}" required>
                        @error('nama_ibu_kandung') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="status_kependudukan_id">Status Kependudukan</label>
                        <select name="status_kependudukan_id" id="status_kependudukan_id" class="form-control" required>
                            <option value="">-- Pilih Status Kependudukan --</option>
                            @foreach($statusKependudukanOptions as $id => $nama)
                                <option value="{{ $id }}" {{ old('status_kependudukan_id', $warga->status_kependudukan_id) == $id ? 'selected' : '' }}>
                                    {{ $nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="javascript:history.back()" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection