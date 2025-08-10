@extends('admin.master')
@section('title', 'Tambah Aset Desa Baru')
@section('content_header')<h1 class="m-0 text-dark">Tambah Aset Desa Baru</h1>@stop

@section('content_main')
<div class="row">
    <div class="col-12">
        <div class="card card-primary card-outline">
            {{-- Tambahkan enctype untuk upload file --}}
            <form action="{{ route('asets.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    {{-- BAGIAN 1: KODEFIKASI DENGAN AI --}}
                    <div class="row">
                        <div class="col-12">
                            <fieldset class="border p-2 mb-3">
                                <legend class="w-auto px-2 h6">Langkah 1: Tentukan Kode Aset</legend>
                                <div class="form-group">
                                    <label for="nama_aset_input">Nama Aset</label>
                                    <div class="input-group">
                                        <input type="text" id="nama_aset_input" name="nama_aset" class="form-control @error('nama_aset') is-invalid @enderror" value="{{ old('nama_aset') }}" placeholder="Contoh: Laptop Acer Aspire atau Mesin Pompa Air" required>
                                        <div class="input-group-append">
                                            <button type="button" id="find-code-btn" class="btn btn-info"><i class="fas fa-search"></i> Cari Kode (AI)</button>
                                        </div>
                                    </div>
                                    @error('nama_aset') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    <small class="form-text text-muted">Ketik nama aset sejelas mungkin, lalu klik tombol untuk mendapatkan rekomendasi kode.</small>
                                </div>
                                
                                <div id="ai-result-area" style="display: none;">
                                    <div class="callout callout-success">
                                        <h5><i class="icon fas fa-check"></i> Kode Ditemukan!</h5>
                                        <p id="ai-result-text"></p>
                                    </div>
                                </div>
                                <div id="ai-loading-area" style="display: none;" class="text-center p-2"><p><i class="fas fa-spinner fa-spin"></i> AI sedang berpikir, mohon tunggu...</p></div>
                                <input type="hidden" name="aset_sub_sub_kelompok_id" id="aset_sub_sub_kelompok_id" value="{{ old('aset_sub_sub_kelompok_id') }}">
                                @error('aset_sub_sub_kelompok_id') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </fieldset>
                        </div>
                    </div>
                    
                    {{-- BAGIAN 2: DETAIL ASET --}}
                    <fieldset class="border p-2 mb-3">
                        <legend class="w-auto px-2 h6">Langkah 2: Isi Detail Aset</legend>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="tahun_perolehan">Tahun Perolehan</label>
                                <input type="number" name="tahun_perolehan" class="form-control @error('tahun_perolehan') is-invalid @enderror" value="{{ old('tahun_perolehan') }}" placeholder="{{ date('Y') }}" required>
                                @error('tahun_perolehan') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="nilai_perolehan">Nilai Perolehan (Rp)</label>
                                <input type="number" name="nilai_perolehan" class="form-control @error('nilai_perolehan') is-invalid @enderror" value="{{ old('nilai_perolehan') }}" placeholder="Contoh: 7500000" required>
                                @error('nilai_perolehan') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="jumlah">Jumlah</label>
                                <input type="number" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror" value="{{ old('jumlah', 1) }}" required>
                                @error('jumlah') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="kondisi">Kondisi</label>
                                <select name="kondisi" class="form-control @error('kondisi') is-invalid @enderror" required>
                                    <option value="Baik" @if(old('kondisi') == 'Baik') selected @endif>Baik</option>
                                    <option value="Rusak Ringan" @if(old('kondisi') == 'Rusak Ringan') selected @endif>Rusak Ringan</option>
                                    <option value="Rusak Berat" @if(old('kondisi') == 'Rusak Berat') selected @endif>Rusak Berat</option>
                                </select>
                                @error('kondisi') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                             <div class="col-md-6 form-group">
                                <label for="sumber_dana">Sumber Dana</label>
                                <input type="text" name="sumber_dana" class="form-control @error('sumber_dana') is-invalid @enderror" value="{{ old('sumber_dana') }}" placeholder="Contoh: Dana Desa 2024">
                                @error('sumber_dana') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="lokasi">Lokasi</label>
                            <textarea name="lokasi" class="form-control @error('lokasi') is-invalid @enderror" rows="2" placeholder="Contoh: Kantor Desa, Ruang Kepala Desa">{{ old('lokasi') }}</textarea>
                            @error('lokasi') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="penanggung_jawab">Penanggung Jawab</label>
                            <input type="text" name="penanggung_jawab" class="form-control @error('penanggung_jawab') is-invalid @enderror" value="{{ old('penanggung_jawab') }}" placeholder="Contoh: Sekretaris Desa">
                            @error('penanggung_jawab') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                         <div class="form-group">
                            <label for="foto_aset">Foto Aset</label>
                            <input type="file" name="foto_aset" class="form-control @error('foto_aset') is-invalid @enderror">
                            @error('foto_aset') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3">{{ old('keterangan') }}</textarea>
                            @error('keterangan') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </fieldset>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Simpan Aset</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@push('js')
<script>
    $(document).ready(function() {
        // Event listener saat tombol "Cari Kode Aset (AI)" diklik
        $('#find-code-btn').on('click', function() {
            let namaAset = $('#nama_aset_input').val();

            // Validasi sederhana: pastikan nama aset tidak kosong
            if (namaAset.length < 3) {
                alert('Silakan ketik nama aset yang lebih deskriptif (minimal 3 karakter).');
                return;
            }

            let btn = $(this);
            let resultArea = $('#ai-result-area');
            let loadingArea = $('#ai-loading-area');
            let resultText = $('#ai-result-text');
            let hiddenInputId = $('#aset_sub_sub_kelompok_id');

            // Tampilkan loading, sembunyikan hasil sebelumnya, dan disable tombol
            btn.prop('disabled', true);
            loadingArea.show();
            resultArea.hide();

            // Kirim permintaan ke controller via AJAX
            $.ajax({
                url: "{{ route('asets.findCodeByAI') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    nama_aset: namaAset
                },
                success: function(response) {
                    // Jika AI berhasil menemukan kode
                    if (response.success) {
                        resultText.text(response.kode_lengkap + ' - ' + response.nama_lengkap);
                        hiddenInputId.val(response.id);
                        resultArea.show();
                    } else {
                        // Jika AI tidak berhasil
                        alert('AI tidak dapat menemukan kode yang cocok. Coba gunakan nama yang lebih spesifik. Pesan dari AI: ' + response.message);
                    }
                },
                error: function() {
                    // Jika terjadi error teknis
                    alert('Terjadi kesalahan saat menghubungi AI. Silakan coba lagi.');
                },
                complete: function() {
                    // Apapun hasilnya, hentikan loading dan aktifkan kembali tombol
                    btn.prop('disabled', false);
                    loadingArea.hide();
                }
            });
        });
    });
</script>
@endpush