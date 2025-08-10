@extends('admin.master')

@section('title', 'Edit Kelompok Desa - Data Cerdas')

@section('content_header')
    <h1 class="m-0 text-dark">Edit Kelompok Desa</h1>
@stop

@section('content_main')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Edit kelompok Desa</h3>
        </div>
        <form action="{{ route('kelompok.update', $kelompok) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label for="nama_kelompok">Nama kelompok</label>
                    <input type="text" name="nama_kelompok" class="form-control @error('nama_kelompok') is-invalid @enderror" id="nama_kelompok" placeholder="Contoh: BPD, LPM, Karang Taruna" value="{{ old('nama_kelompok', $kelompok->nama_kelompok) }}" required>
                    @error('nama_kelompok') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" rows="3" placeholder="Deskripsi singkat tentang kelompok">{{ old('deskripsi', $kelompok->deskripsi) }}</textarea>
                    @error('deskripsi') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="sk_kepala_desa">SK Kepala Desa (PDF)</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" name="sk_kepala_desa" class="custom-file-input @error('sk_kepala_desa') is-invalid @enderror" id="sk_kepala_desa" accept="application/pdf">
                            <label class="custom-file-label" for="sk_kepala_desa">{{ $kelompok->sk_kepala_desa_path ? basename($kelompok->sk_kepala_desa_path) : 'Pilih file PDF' }}</label>
                        </div>
                    </div>
                    @error('sk_kepala_desa') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                    <small class="form-text text-muted">Ukuran maksimal 2MB, format PDF. Biarkan kosong jika tidak ingin mengubah.</small>
                    @if ($kelompok->sk_kepala_desa_path)
                        <small class="form-text text-info">File SK saat ini: <a href="{{ $kelompok->sk_kepala_desa_path }}" target="_blank">Lihat SK</a></small>
                    @endif
                </div>

                <hr>
                <h4>Pengurus kelompok</h4>
                <div id="pengurus-container">
                    @php
                        $oldPengurus = old('pengurus', $kelompok->pengurus->toArray());
                    @endphp
                    @forelse($oldPengurus as $index => $pengurus)
                        <div class="form-row mb-2 pengurus-item">
                            <div class="col-md-5">
                                <input type="text" name="pengurus[{{ $index }}][nama_pengurus]" class="form-control @error('pengurus.'.$index.'.nama_pengurus') is-invalid @enderror" placeholder="Nama Pengurus" value="{{ $pengurus['nama_pengurus'] }}" required>
                                @error('pengurus.'.$index.'.nama_pengurus') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="pengurus[{{ $index }}][jabatan]" class="form-control @error('pengurus.'.$index.'.jabatan') is-invalid @enderror" placeholder="Jabatan (contoh: Ketua, Sekretaris)" value="{{ $pengurus['jabatan'] }}" required>
                                @error('pengurus.'.$index.'.jabatan') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm remove-pengurus">Hapus</button>
                            </div>
                        </div>
                    @empty
                        <div class="form-row mb-2 pengurus-item">
                            <div class="col-md-5">
                                <input type="text" name="pengurus[0][nama_pengurus]" class="form-control" placeholder="Nama Pengurus" required>
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="pengurus[0][jabatan]" class="form-control" placeholder="Jabatan (contoh: Ketua, Sekretaris)" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm remove-pengurus">Hapus</button>
                            </div>
                        </div>
                    @endforelse
                </div>
                {{-- FIELD BARU UNTUK UPLOAD KOP SURAT --}}
                <div class="form-group">
                    <label for="path_kop_surat">Upload Kop Surat (Opsional)</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="path_kop_surat" name="path_kop_surat">
                            <label class="custom-file-label" for="path_kop_surat">Pilih file gambar...</label>
                        </div>
                    </div>
                    <small class="form-text text-muted">Gunakan gambar (JPG/PNG) dengan rasio landscape untuk hasil terbaik. Jika sudah ada, mengunggah file baru akan menggantikan yang lama.</small>

                    {{-- Tampilkan kop surat yang sudah ada --}}
                    @if($kelompok->path_kop_surat)
                        <div class="mt-2">
                            <p>Kop surat saat ini:</p>
                            <img src="{{ asset('storage/' . $kelompok->path_kop_surat) }}" alt="Kop Surat" class="img-thumbnail" style="max-width: 400px;">
                        </div>
                    @endif
                </div>
                <button type="button" class="btn btn-success btn-sm" id="add-pengurus">Tambah Pengurus</button>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Update kelompok</button>
                <a href="{{ route('kelompok.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let pengurusIndex = {{ old('pengurus') ? count(old('pengurus')) : ($kelompok->pengurus->count() > 0 ? $kelompok->pengurus->count() : 1) }};

            document.getElementById('add-pengurus').addEventListener('click', function () {
                const container = document.getElementById('pengurus-container');
                const newItem = document.createElement('div');
                newItem.classList.add('form-row', 'mb-2', 'pengurus-item');
                newItem.innerHTML = `
                    <div class="col-md-5">
                        <input type="text" name="pengurus[${pengurusIndex}][nama_pengurus]" class="form-control" placeholder="Nama Pengurus" required>
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="pengurus[${pengurusIndex}][jabatan]" class="form-control" placeholder="Jabatan (contoh: Ketua, Sekretaris)" required>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-sm remove-pengurus">Hapus</button>
                    </div>
                `;
                container.appendChild(newItem);
                pengurusIndex++;
            });

            document.getElementById('pengurus-container').addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-pengurus')) {
                    if (document.querySelectorAll('.pengurus-item').length > 1) {
                        e.target.closest('.pengurus-item').remove();
                    } else {
                        alert('Minimal harus ada satu pengurus.');
                    }
                }
            });

            // Untuk menampilkan nama file yang dipilih di input file
            document.getElementById('sk_kepala_desa').addEventListener('change', function() {
                var fileName = this.files[0] ? this.files[0].name : 'Pilih file PDF';
                this.nextElementSibling.innerText = fileName;
            });
        });
    </script>
    <script>
        // Script untuk menampilkan nama file di custom file input
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
    </script>
@endsection