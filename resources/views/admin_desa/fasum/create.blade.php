@extends('admin.master')

@section('title', 'Tambah Fasilitas Umum - Data Cerdas')

@section('content_header')
    <h1 class="m-0 text-dark">Tambah Fasilitas Umum</h1>
@stop

@section('content_main')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Tambah Fasilitas Umum</h3>
        </div>
        <form action="{{ route('fasum.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="form-group">
                    <label for="nama_fasum">Nama Fasilitas Umum</label>
                    <input type="text" name="nama_fasum" class="form-control @error('nama_fasum') is-invalid @enderror" id="nama_fasum" value="{{ old('nama_fasum') }}" required>
                    @error('nama_fasum') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="kategori">Jenis Fasilitas Umum</label> {{-- Menggunakan 'kategori' sesuai DB --}}
                    <select name="kategori" class="form-control @error('kategori') is-invalid @enderror" id="kategori" required>
                        <option value="">-- Pilih Jenis Fasum --</option>
                        @foreach($jenisFasumOptions as $option)
                            <option value="{{ $option }}" {{ old('kategori') == $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                    @error('kategori') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="deskripsi">Deskripsi (Opsional)</label>
                    <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="alamat_lengkap">Alamat Lengkap</label> {{-- Kolom baru --}}
                    <textarea name="alamat_lengkap" class="form-control @error('alamat_lengkap') is-invalid @enderror" id="alamat_lengkap" rows="3" required>{{ old('alamat_lengkap') }}</textarea>
                    @error('alamat_lengkap') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="rw_id">Lokasi RW</label>
                            <select name="rw_id" id="rw_id" class="form-control @error('rw_id') is-invalid @enderror" required>
                                <option value="">-- Pilih RW --</option>
                                @foreach($rws as $rw)
                                    <option value="{{ $rw->id }}" {{ old('rw_id') == $rw->id ? 'selected' : '' }}>RW {{ $rw->nomor_rw }}</option>
                                @endforeach
                            </select>
                            @error('rw_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="rt_id">Lokasi RT</label>
                            <select name="rt_id" id="rt_id" class="form-control @error('rt_id') is-invalid @enderror" required disabled>
                                <option value="">-- Pilih RW Terlebih Dahulu --</option>
                            </select>
                            @error('rt_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="latitude">Latitude (Opsional)</label>
                            <input type="text" name="latitude" class="form-control @error('latitude') is-invalid @enderror" id="latitude" value="{{ old('latitude') }}" placeholder="-6.2088">
                            @error('latitude') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="longitude">Longitude (Opsional)</label>
                            <input type="text" name="longitude" class="form-control @error('longitude') is-invalid @enderror" id="longitude" value="{{ old('longitude') }}" placeholder="106.8456">
                            @error('longitude') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="status_kondisi">Kondisi Fasilitas</label> {{-- Menggunakan 'status_kondisi' sesuai DB --}}
                    <select name="status_kondisi" class="form-control @error('status_kondisi') is-invalid @enderror" id="status_kondisi">
                        <option value="">-- Pilih Kondisi --</option>
                        @foreach($kondisiOptions as $option)
                            <option value="{{ $option }}" {{ old('status_kondisi') == $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                    @error('status_kondisi') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="panjang">Panjang (Opsional)</label> {{-- Kolom baru --}}
                            <input type="text" name="panjang" class="form-control @error('panjang') is-invalid @enderror" id="panjang" value="{{ old('panjang') }}" placeholder="Contoh: 10 meter">
                            @error('panjang') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="lebar">Lebar (Opsional)</label> {{-- Kolom baru --}}
                            <input type="text" name="lebar" class="form-control @error('lebar') is-invalid @enderror" id="lebar" value="{{ old('lebar') }}" placeholder="Contoh: 5 meter">
                            @error('lebar') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tinggi">Tinggi (Opsional, untuk Detail Spesifikasi)</label> {{-- Untuk JSON --}}
                            <input type="text" name="tinggi" class="form-control @error('tinggi') is-invalid @enderror" id="tinggi" value="{{ old('tinggi') }}" placeholder="Contoh: 3 meter">
                            @error('tinggi') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="luas_bangunan">Luas Bangunan (Opsional, untuk Detail Spesifikasi)</label> {{-- Untuk JSON --}}
                            <input type="text" name="luas_bangunan" class="form-control @error('luas_bangunan') is-invalid @enderror" id="luas_bangunan" value="{{ old('luas_bangunan') }}" placeholder="Contoh: 150 m²">
                            @error('luas_bangunan') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="tahun_berdiri">Tahun Berdiri (Opsional)</label>
                    <input type="number" name="tahun_berdiri" class="form-control @error('tahun_berdiri') is-invalid @enderror" id="tahun_berdiri" value="{{ old('tahun_berdiri') }}" min="1900" max="{{ date('Y') }}">
                    @error('tahun_berdiri') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="luas_area">Luas Area (m², Opsional)</label> {{-- Kolom baru --}}
                    <input type="text" name="luas_area" class="form-control @error('luas_area') is-invalid @enderror" id="luas_area" value="{{ old('luas_area') }}" placeholder="Contoh: 1000 m²">
                    @error('luas_area') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="kapasitas">Kapasitas (Opsional)</label> {{-- Kolom baru --}}
                    <input type="text" name="kapasitas" class="form-control @error('kapasitas') is-invalid @enderror" id="kapasitas" value="{{ old('kapasitas') }}" placeholder="Contoh: 200 jamaah, 50 siswa">
                    @error('kapasitas') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="kontak_pengelola">Kontak Pengelola (Opsional)</label> {{-- Kolom baru --}}
                    <input type="text" name="kontak_pengelola" class="form-control @error('kontak_pengelola') is-invalid @enderror" id="kontak_pengelola" value="{{ old('kontak_pengelola') }}" placeholder="Nama atau Nomor Telepon">
                    @error('kontak_pengelola') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="status_kepemilikan">Status Kepemilikan</label> {{-- Kolom baru --}}
                    <select name="status_kepemilikan" class="form-control @error('status_kepemilikan') is-invalid @enderror" id="status_kepemilikan">
                        <option value="">-- Pilih Status Kepemilikan --</option>
                        @foreach($statusKepemilikanOptions as $option)
                            <option value="{{ $option }}" {{ old('status_kepemilikan') == $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                    @error('status_kepemilikan') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="path_dokumen_legal">Legal Dokumen</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" name="path_dokumen_legal" class="custom-file-input @error('path_dokumen_legal') is-invalid @enderror" id="path_dokumen_legal" accept="application/pdf">
                            <label class="custom-file-label" for="path_dokumen_legal">Pilih file PDF</label>
                        </div>
                    </div>
                    @error('path_dokumen_legal') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                    <small class="form-text text-muted">Ukuran maksimal 2MB, format PDF.</small>
                </div>

                <div class="form-group">
                    <label for="photos">Foto Fasilitas Umum (Opsional, Multiple)</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" name="photos[]" class="custom-file-input @error('photos.*') is-invalid @enderror" id="photos" accept="image/*" multiple>
                            <label class="custom-file-label" for="photos">Pilih beberapa file gambar</label>
                        </div>
                    </div>
                    @error('photos.*') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                    <small class="form-text text-muted">Ukuran maksimal 2MB per foto, format gambar.</small>
                </div>
                <div id="photos_preview" class="mt-2 row"></div>

            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan Fasilitas Umum</button>
                <a href="{{ route('fasum.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            // Logika dropdown RT dinamis berdasarkan RW
            const rwSelect = $('#rw_id');
            const rtSelect = $('#rt_id');
            const initialRwId = rwSelect.val();
            const initialRtId = "{{ old('rt_id') }}";

            function loadRts(rwId, selectedRtId = null) {
                rtSelect.empty().append('<option value="">-- Pilih RT --</option>');
                rtSelect.prop('disabled', true);

                if (rwId) {
                    $.ajax({
                        url: "{{ route('api.rts-by-rw') }}",
                        type: 'GET',
                        data: { rw_id: rwId },
                        success: function (data) {
                            $.each(data, function (key, value) {
                                rtSelect.append('<option value="' + value.id + '">' + 'RT ' + value.nomor_rt + '</option>');
                            });
                            if (selectedRtId) {
                                rtSelect.val(selectedRtId);
                            }
                            rtSelect.prop('disabled', false);
                        },
                        error: function (xhr, status, error) {
                            console.error("Error loading RTs:", error);
                            alert('Gagal memuat data RT. Silakan coba lagi.');
                            rtSelect.html('<option value="">Gagal memuat RT</option>').prop('disabled', true);
                        }
                    });
                } else {
                    rtSelect.html('<option value="">-- Pilih RW Terlebih Dahulu --</option>').prop('disabled', true);
                }
            }

            if (initialRwId) {
                loadRts(initialRwId, initialRtId);
            }
            rwSelect.on('change', function () {
                loadRts($(this).val());
            });

            // Preview multiple images for photos
            $('#photos').on('change', function() {
                const previewContainer = $('#photos_preview');
                previewContainer.empty(); // Clear previous previews

                if (this.files && this.files[0]) {
                    $.each(this.files, function(index, file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const col = $('<div class="col-md-3 mb-2"></div>');
                            const img = $('<img class="img-thumbnail" style="max-height: 150px; object-fit: cover;">').attr('src', e.target.result);
                            col.append(img);
                            previewContainer.append(col);
                        };
                        reader.readAsDataURL(file);
                    });
                    $(this).next('.custom-file-label').html(this.files.length + ' file(s) selected');
                } else {
                    $(this).next('.custom-file-label').html('Pilih beberapa file gambar');
                }
            });
            
            document.getElementById('path_dokumen_legal').addEventListener('change', function() {
                var fileName = this.files[0] ? this.files[0].name : 'Pilih file PDF';
                this.nextElementSibling.innerText = fileName;
            });

            $('.custom-file-input').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });
        });
    </script>
@stop
