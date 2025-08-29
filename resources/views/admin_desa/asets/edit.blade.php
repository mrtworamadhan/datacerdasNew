@extends('admin.master')
@section('title', 'Edit Aset Desa')
@section('content_header')<h1 class="m-0 text-dark">Edit Aset: {{ $aset->nama_aset }}</h1>@stop

@section('content')
<div class="card card-warning card-outline">
    <form action="{{ route('asets.update', $aset->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card-body">
            {{-- BAGIAN 1: INFO KODE ASET --}}
            <fieldset class="border p-2 mb-3">
                <legend class="w-auto px-2 h6">Informasi Kode Aset</legend>
                <div class="form-group">
                    <label>Kode Aset</label>
                    <input type="text" class="form-control" value="{{ $aset->kode_aset }}" disabled>
                    <small class="form-text text-muted">Kode aset tidak dapat diubah.</small>
                </div>
                <div class="form-group">
                    <label>Jenis Aset</label>
                    {{-- Menampilkan hirarki jenis aset --}}
                    <input type="text" class="form-control" 
                           value="{{ $aset->subSubKelompok->subKelompok->kelompok->bidang->golongan->nama_golongan }} > {{ $aset->subSubKelompok->subKelompok->kelompok->bidang->nama_bidang }} > {{ $aset->subSubKelompok->subKelompok->kelompok->nama_kelompok }} > {{ $aset->subSubKelompok->subKelompok->nama_sub_kelompok }} > {{ $aset->subSubKelompok->nama_sub_sub_kelompok }}" 
                           disabled>
                </div>
            </fieldset>

            {{-- BAGIAN 2: DETAIL ASET (Meniru struktur dari create.blade.php) --}}
            <fieldset class="border p-2 mb-3">
                <legend class="w-auto px-2 h6">Edit Detail Aset</legend>
                <div class="form-group">
                    <label for="nama_aset">Nama Aset</label>
                    <input type="text" id="nama_aset" name="nama_aset" class="form-control @error('nama_aset') is-invalid @enderror" value="{{ old('nama_aset', $aset->nama_aset) }}" required>
                    @error('nama_aset') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="tahun_perolehan">Tahun Perolehan</label>
                        <input type="number" name="tahun_perolehan" class="form-control @error('tahun_perolehan') is-invalid @enderror" value="{{ old('tahun_perolehan', $aset->tahun_perolehan) }}" required>
                        @error('tahun_perolehan') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="nilai_perolehan">Nilai Perolehan (Rp)</label>
                        <input type="number" name="nilai_perolehan" class="form-control @error('nilai_perolehan') is-invalid @enderror" value="{{ old('nilai_perolehan', $aset->nilai_perolehan) }}" required>
                        @error('nilai_perolehan') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="jumlah">Jumlah</label>
                        <input type="number" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror" value="{{ old('jumlah', $aset->jumlah) }}" required>
                        @error('jumlah') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="kondisi">Kondisi</label>
                        <select name="kondisi" class="form-control @error('kondisi') is-invalid @enderror" required>
                            <option value="Baik" @if(old('kondisi', $aset->kondisi) == 'Baik') selected @endif>Baik</option>
                            <option value="Rusak Ringan" @if(old('kondisi', $aset->kondisi) == 'Rusak Ringan') selected @endif>Rusak Ringan</option>
                            <option value="Rusak Berat" @if(old('kondisi', $aset->kondisi) == 'Rusak Berat') selected @endif>Rusak Berat</option>
                        </select>
                        @error('kondisi') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                     <div class="col-md-6 form-group">
                        <label for="sumber_dana">Sumber Dana</label>
                        <input type="text" name="sumber_dana" class="form-control @error('sumber_dana') is-invalid @enderror" value="{{ old('sumber_dana', $aset->sumber_dana) }}">
                        @error('sumber_dana') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label for="lokasi">Lokasi</label>
                    <textarea name="lokasi" class="form-control @error('lokasi') is-invalid @enderror" rows="2">{{ old('lokasi', $aset->lokasi) }}</textarea>
                    @error('lokasi') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="penanggung_jawab">Penanggung Jawab</label>
                    <input type="text" name="penanggung_jawab" class="form-control @error('penanggung_jawab') is-invalid @enderror" value="{{ old('penanggung_jawab', $aset->penanggung_jawab) }}">
                    @error('penanggung_jawab') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                 <div class="form-group">
                    <label for="foto_aset">Foto Aset (Upload baru untuk mengganti)</label>
                    @if($aset->foto_aset)
                        <div class="mb-2">
                            <img src="{{ Storage::url($aset->foto_aset) }}" alt="Foto Aset" class="img-thumbnail" width="200">
                        </div>
                    @endif
                    <input type="file" name="foto_aset" class="form-control @error('foto_aset') is-invalid @enderror">
                    @error('foto_aset') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="keterangan">Keterangan</label>
                    <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3">{{ old('keterangan', $aset->keterangan) }}</textarea>
                    @error('keterangan') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </fieldset>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-warning">Update Aset</button>
            <a href="{{ route('asets.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@stop