@csrf
<div class="row">
    {{-- Kolom Kiri: Form Input --}}
    <div class="col-lg-7">
        <div class="card card-purple card-outline">
            <div class="card-header"><h3 class="card-title">Editor Template</h3></div>
            <div class="card-body">
                <div class="form-group">
                    <label for="nama_surat">Nama Surat (Internal)</label>
                    <input type="text" class="form-control @error('nama_surat') is-invalid @enderror" id="nama_surat" name="nama_surat" value="{{ old('nama_surat', $jenisSurat->nama_surat ?? '') }}" required>
                </div>
                <div class="form-group">
                    <label for="judul_surat">Judul di Kop Surat</label>
                    <input type="text" class="form-control @error('judul_surat') is-invalid @enderror" id="judul_surat" name="judul_surat" value="{{ old('judul_surat', $jenisSurat->judul_surat ?? '') }}" required>
                </div>
                <div class="form-group">
                    <label for="isi_template">Isi Template Surat</label>
                    <textarea name="isi_template" id="isi_template" class="form-control summernote" required>{{ old('isi_template', $jenisSurat->isi_template ?? '') }}</textarea>
                </div>
                 <div class="form-group">
                    <label for="klasifikasi_surat_id">Kode Klasifikasi Surat</label>
                    <select name="klasifikasi_surat_id" id="klasifikasi_surat_id" class="form-control select2 @error('klasifikasi_surat_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Kode --</option>
                        @foreach($klasifikasiSurats as $klasifikasi)
                            <option value="{{ $klasifikasi->id }}" {{ old('klasifikasi_surat_id', isset($jenisSurat) ? $jenisSurat->klasifikasi_surat_id : '') == $klasifikasi->id ? 'selected' : '' }}>
                                {{ $klasifikasi->kode }} - {{ $klasifikasi->deskripsi }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="persyaratan_text">Daftar Persyaratan (Satu per baris)</label>
                    <textarea name="persyaratan_text" class="form-control" rows="5">{{ old('persyaratan_text', isset($jenisSurat) && is_array($jenisSurat->persyaratan) ? implode("\n", $jenisSurat->persyaratan) : '') }}</textarea>
                </div>
                <div class="form-group">
                    <label for="custom_fields_text">Field Tambahan Kustom (Satu per baris)</label>
                    <textarea id="custom_fields_text" name="custom_fields_text" class="form-control" rows="5">{{ old('custom_fields_text', isset($jenisSurat) && is_array($jenisSurat->custom_fields) ? implode("\n", $jenisSurat->custom_fields) : '') }}</textarea>
                    <small class="form-text text-muted">Contoh: Nama Usaha, Alamat Tujuan, dll. Akan menjadi isian tambahan saat pengajuan.</small>
                </div>
                {{-- Contoh di dalam form create.blade.php --}}
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        {{-- PERBAIKAN: Gunakan old() dengan default dari $jenisSurat --}}
                        <input type="checkbox" class="custom-control-input" id="is_mandiri" name="is_mandiri" value="1" 
                            {{ old('is_mandiri', $jenisSurat->is_mandiri ?? false) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_mandiri">Izinkan Akses di Anjungan Mandiri</label>
                    </div>
                    <small class="form-text text-muted">Jika dicentang, warga dapat mencetak surat ini secara mandiri melalui anjungan.</small>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan Template</button>
                <a href="{{ route('jenis-surat.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Live Preview --}}
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-eye mr-1"></i> Live Preview</h3></div>
            <div class="card-body">
                {{-- PERUBAHAN: Menambahkan style untuk font standar surat --}}
                <div id="preview-container" style="background-color: #fff; border: 1px solid #ddd; padding: 2rem; min-height: 842px; font-family: 'Arial', serif; font-size: 8pt;">
                    {{-- Kop Surat (Dinamis) --}}
                    <div class="text-center mb-2 " style="padding-bottom: 10px;">
                        @if(isset($suratSetting) && $suratSetting->path_kop_surat)
                            <img src="{{ asset('storage/' . $suratSetting->path_kop_surat) }}" alt="Kop Surat" style="max-width: 100%; height: auto;">
                        @else
                            <p class="text-muted">[ KOP SURAT AKAN TAMPIL DI SINI ]</p>
                        @endif
                    </div>
                    {{-- Judul Surat (Dinamis) --}}
                    <p id="preview-judul" class="text-center font-weight-bold text-uppercase" style="text-decoration: underline; margin-bottom: 0.25rem;font-family: 'ArialBold', serif; font-size: 10pt;"></p>
                    {{-- NOMOR SURAT (BARU) --}}
                    <p id="preview-nomor-surat" class="text-center" style="margin-top: 0; margin-bottom: 2rem;"></p>
                    {{-- Isi Surat (Dinamis) --}}
                    <div id="preview-content" class="mt-4" style="line-height: 1.5; text-align: justify;">
                        {{-- Konten dari Summernote akan muncul di sini --}}
                    </div>
                    {{-- Tanda Tangan (Dinamis) --}}
                    <div id="preview-signature" class="mt-2" style="width: 50%; float: right; text-align: left;">
                        <p class="fs-6 lh-sm mb-0">Desa {{$desa->nama_desa}}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                        <p class="fs-6 lh-sm">{{$suratSetting->penanda_tangan_jabatan}} {{$desa->nama_desa}}</p>
                        <div class="text-center mb-2 " style="padding-bottom: 5px;">
                            @if(isset($suratSetting) && $suratSetting->path_ttd)
                                <img src="{{ asset('storage/' . $suratSetting->path_ttd) }}" alt="Kop Surat" style="max-width: 25%; height: auto;">
                            @else
                                <p class="text-muted">[ TTD AKAN TAMPIL DI SINI ]</p>
                            @endif
                        </div>
                        <p class="font-weight-bold fs-6 ">{{$suratSetting->penanda_tangan_nama}}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
