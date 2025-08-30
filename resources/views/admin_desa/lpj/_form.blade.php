@csrf
{{-- Input tersembunyi untuk memberikan konteks ke JavaScript --}}
<input type="hidden" id="nama_kegiatan" value="{{ $kegiatan->nama_kegiatan }}">
<input type="hidden" id="penyelenggara_nama" value="{{ $kegiatan->kegiatanable->nama_lembaga ?? $kegiatan->kegiatanable->nama_kelompok }}">
<input type="hidden" id="nama_desa" value="{{ $namaDesa }}">
<input type="hidden" id="lokasi_kegiatan" value="{{ $lokasiKegiatan }}">

<div class="card card-success card-outline">
    <div class="card-header"><h3 class="card-title">Isi Detail Laporan</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label>Latar Belakang (dari Proposal)</label>
            <div class="p-2 bg-light border rounded" style="white-space: pre-wrap;">{{ $kegiatan->latar_belakang }}</div>
            <button type="button" class="btn btn-sm btn-outline-info mt-2 ai-rewrite-btn" 
                    data-source-text="{{ e($kegiatan->latar_belakang) }}"
                    data-target="#latar_belakang_lpj"
                    data-section="Latar Belakang LPJ">
                <i class="fas fa-magic"></i> Tulis Ulang untuk LPJ dengan AI
            </button>
        </div>
        <div class="form-group">
            <label for="latar_belakang_lpj">Latar Belakang (Versi LPJ)</label>
            <textarea name="latar_belakang_lpj" id="latar_belakang_lpj" class="form-control" rows="5"></textarea>
        </div>
        <hr>
        <div class="form-group">
            <label>Deskripsi Kegiatan (Proposal)</label>
            <div class="p-2 bg-light border rounded" style="white-space: pre-wrap;">{{ $kegiatan->deskripsi_kegiatan }}</div>
            <button type="button" class="btn btn-sm btn-outline-info mt-2 ai-rewrite-btn" 
                    data-source-text="{{ e($kegiatan->deskripsi_kegiatan) }}"
                    data-target="#hasil_kegiatan"
                    data-section="Hasil Kegiatan LPJ">
                <i class="fas fa-magic"></i> Tulis Ulang untuk LPJ dengan AI
            </button>
        </div>
        <div class="form-group">
            <label for="hasil_kegiatan">Hasil Kegiatan (Versi LPJ)</label>
            <textarea name="hasil_kegiatan" id="hasil_kegiatan" class="form-control" rows="5"></textarea>
        </div>

        <div class="form-group">
            <label for="evaluasi_kendala">Evaluasi Kegiatan</label>
            <div class="input-group">
                <textarea name="evaluasi_kendala" id="evaluasi_kendala" class="form-control" rows="5" required>{{ old('evaluasi_kendala', $lpj->hasil_kegiatan ?? '') }}</textarea>
                
            </div>
            <button type="button" class="btn btn-sm btn-outline-info mt-2 ai-rewrite-btn" 
                    data-source-text="{{ e($kegiatan->deskripsi_kegiatan) }}"
                    data-target="#evaluasi_kendala"
                    data-section="Evaluasi Kegiatan LPJ">
                <i class="fas fa-magic"></i> Tulis Ulang untuk LPJ dengan AI
            </button>
        </div>

        <div class="form-group">
            <label for="rekomendasi_lanjutan">Rekomendasi Dan Penutup</label>
            <div class="input-group">
                <textarea name="rekomendasi_lanjutan" id="rekomendasi_lanjutan" class="form-control" rows="5" required>{{ old('rekomendasi_lanjutan', $lpj->rekomendasi_lanjutan ?? '') }}</textarea>
                
            </div>
             <button type="button" class="btn btn-sm btn-outline-info mt-2 ai-rewrite-btn" 
                    data-source-text="{{ e($kegiatan->deskripsi_kegiatan) }}"
                    data-target="#rekomendasi_lanjutan"
                    data-section="Rekomendasi LPJ">
                <i class="fas fa-magic"></i> Tulis Ulang untuk LPJ dengan AI
            </button>
        </div>
                
        <div class="form-group">
            <label for="tanggal_pelaporan">Tanggal Pelaporan</label>
            <input type="date" name="tanggal_pelaporan" class="form-control" value="{{ old('tanggal_pelaporan', ($lpj->tanggal_pelaporan ?? now())->format('Y-m-d')) }}" required>
        </div>
    </div>
    <div class="card-footer"><button type="submit" class="btn btn-success">Simpan LPJ</button></div>
</div>