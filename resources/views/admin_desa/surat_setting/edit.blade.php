@extends('admin.master')
@section('title', 'Pengaturan Surat')
@section('content_header')<h1 class="m-0 text-dark">Pengaturan Surat</h1>@stop
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('surat-setting.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="penanda_tangan_nama">Nama Penanda Tangan Default</label>
                                <input type="text" name="penanda_tangan_nama" class="form-control"
                                    value="{{ old('penanda_tangan_nama', $setting->penanda_tangan_nama) }}"
                                    placeholder="Contoh: John Doe">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="penanda_tangan_jabatan">Jabatan Penanda Tangan Default</label>
                                <input type="text" name="penanda_tangan_jabatan" class="form-control"
                                    value="{{ old('penanda_tangan_jabatan', $setting->penanda_tangan_jabatan) }}"
                                    placeholder="Contoh: Kepala Desa">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="path_kop_surat">Upload Kop Surat (Kosongkan jika tidak ingin mengubah)</label>
                        <input type="file" name="path_kop_surat" class="form-control">
                        @if($setting->path_kop_surat)
                            <small class="form-text text-muted">Kop surat saat ini:</small><br>
                            <img src="{{ asset('storage/' . $setting->path_kop_surat) }}" alt="Kop Surat"
                                class="img-fluid mt-2" style="max-height: 150px; border: 1px solid #ddd;">
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="path_logo_pemerintah">Upload Logo Pemerintah (Garuda/Pemda)</label>
                        <input type="file" name="path_logo_pemerintah" class="form-control">
                        @if($setting->path_logo_pemerintah)
                            <small class="form-text text-muted">Logo saat ini:</small><br>
                            <img src="{{ asset('storage/' . $setting->path_logo_pemerintah) }}" alt="Logo Pemerintah"
                                class="img-fluid mt-2"
                                style="max-height: 100px; border: 1px solid #ddd; background: #eee; padding: 5px;">
                        @endif
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop