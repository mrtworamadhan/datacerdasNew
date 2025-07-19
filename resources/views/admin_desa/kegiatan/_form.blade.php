@csrf
<input type="hidden" id="nama_lembaga" value="{{ $lembaga->nama_lembaga }}">
<input type="hidden" id="nama_desa" value="{{ $lembaga->desa->nama_desa }}">
<div class="row">
    <div class="col-md-8">
        <div class="card card-purple card-outline">
            <div class="card-body">
                <div class="form-group">
                    <label for="nama_kegiatan">Nama Kegiatan</label>
                    <input type="text" name="nama_kegiatan" id="nama_kegiatan" class="form-control" value="{{ old('nama_kegiatan', $kegiatan->nama_kegiatan ?? '') }}" required>
                </div>
                <div class="form-group">
                    <label>Latar Belakang</label>
                    <div class="input-group">
                        <textarea name="latar_belakang" id="latar_belakang" class="form-control" rows="4">{{ old('latar_belakang', $kegiatan->latar_belakang ?? '') }}</textarea>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary ai-helper-btn" data-target="#latar_belakang" data-section="Latar Belakang"><i class="fas fa-magic"></i> Bantu Tulis</button>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Tujuan Kegiatan</label>
                     <div class="input-group">
                        <textarea name="tujuan_kegiatan" id="tujuan_kegiatan" class="form-control" rows="4">{{ old('tujuan_kegiatan', $kegiatan->tujuan_kegiatan ?? '') }}</textarea>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary ai-helper-btn" data-target="#tujuan_kegiatan" data-section="Tujuan Kegiatan"><i class="fas fa-magic"></i> Bantu Tulis</button>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Deskripsi Lengkap Kegiatan</label>
                    <div class="input-group">
                        <textarea name="deskripsi_kegiatan" id="deskripsi_kegiatan" class="form-control" rows="6" required>{{ old('deskripsi_kegiatan', $kegiatan->deskripsi_kegiatan ?? '') }}</textarea>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary ai-helper-btn" data-target="#deskripsi_kegiatan" data-section="Deskripsi Kegiatan"><i class="fas fa-magic"></i> Bantu Tulis</button>
                        </div>
                    </div>
                </div>
                {{-- TAMBAHAN: Textarea untuk Rincian Anggaran --}}
                <div class="form-group">
                    <label>Rincian Anggaran (Gunakan format Markdown)</label>
                     <div class="input-group">
                        <textarea name="laporan_dana" id="laporan_dana" class="form-control" rows="6">{{ old('laporan_dana', $kegiatan->laporan_dana ?? '') }}</textarea>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary ai-helper-btn" data-target="#laporan_dana" data-section="Rincian Anggaran"><i class="fas fa-magic"></i> Buat Tabel</button>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Penutup</label>
                     <div class="input-group">
                        <textarea name="penutup" id="penutup" class="form-control" rows="3">{{ old('penutup', $kegiatan->penutup ?? '') }}</textarea>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary ai-helper-btn" data-target="#penutup" data-section="Penutup"><i class="fas fa-magic"></i> Bantu Tulis</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-purple card-outline">
            <div class="card-body">
                <div class="form-group"><label for="tanggal_kegiatan">Tanggal Kegiatan</label><input type="date" id="tanggal" name="tanggal_kegiatan" class="form-control" value="{{ old('tanggal_kegiatan', isset($kegiatan) ? $kegiatan->tanggal_kegiatan->format('Y-m-d') : '') }}" required></div>
                <div class="form-group"><label for="lokasi_kegiatan">Lokasi Kegiatan</label><input type="text" id="lokasi_kegiatan" name="lokasi_kegiatan" class="form-control" value="{{ old('lokasi_kegiatan', $kegiatan->lokasi_kegiatan ?? '') }}" required></div>
                <div class="form-group"><label for="anggaran_biaya">Anggaran Biaya (Rp)</label><input type="number" id="anggaran" name="anggaran_biaya" class="form-control" value="{{ old('anggaran_biaya', $kegiatan->anggaran_biaya ?? '') }}"></div>
                <div class="form-group"><label for="sumber_dana">Sumber Dana</label><input type="text" name="sumber_dana" class="form-control" value="{{ old('sumber_dana', $kegiatan->sumber_dana ?? '') }}"></div>
                <div class="form-group">
                    <label for="photos">Upload Foto Dokumentasi</label>
                    <input type="file" name="photos[]" id="photos" class="form-control" multiple>
                    {{-- Container untuk preview gambar --}}
                    <div id="image-preview-container" class="mt-2 d-flex flex-wrap gap-2"></div>
                    <small id="image-info" class="form-text text-muted"></small>
                </div>
            </div>
            <div class="card-footer"><button type="submit" class="btn btn-primary btn-block">Simpan Kegiatan</button></div>
        </div>
    </div>
</div>