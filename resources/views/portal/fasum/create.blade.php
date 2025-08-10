@extends('layouts.portal')
@section('title', 'Tambah Fasum Baru')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Tambah Fasilitas Umum</h3>
        </div>
        <form action="{{ route('portal.fasum.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="form-group mb-2">
                    <label for="nama_fasum">Nama Fasilitas Umum</label>
                    <input type="text" name="nama_fasum" class="form-control @error('nama_fasum') is-invalid @enderror"
                        id="nama_fasum" value="{{ old('nama_fasum') }}" required>
                    @error('nama_fasum') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group mb-2">
                    <label for="kategori">Jenis Fasilitas Umum</label> {{-- Menggunakan 'kategori' sesuai DB --}}
                    <select name="kategori" class="form-control @error('kategori') is-invalid @enderror" id="kategori"
                        required>
                        <option value="">-- Pilih Jenis Fasum --</option>
                        @foreach($jenisFasumOptions as $option)
                            <option value="{{ $option }}" {{ old('kategori') == $option ? 'selected' : '' }}>{{ $option }}
                            </option>
                        @endforeach
                    </select>
                    @error('kategori') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group mb-2">
                    <label for="deskripsi">Deskripsi (Opsional)</label>
                    <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi"
                        rows="3">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group mb-2">
                    <label for="alamat_lengkap">Alamat Lengkap</label> {{-- Kolom baru --}}
                    <textarea name="alamat_lengkap" class="form-control @error('alamat_lengkap') is-invalid @enderror"
                        id="alamat_lengkap" rows="3" required>{{ old('alamat_lengkap') }}</textarea>
                    @error('alamat_lengkap') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="row mb-2">
                    <div class="col-md-6 mb-3">
                        <label for="rw_id" class="form-label">Pilih RW</label>
                        <select name="rw_id" id="rw_id" class="form-select" required>
                            <option value="">-- Pilih RW --</option>
                            @foreach($rws as $rw)
                                <option value="{{ $rw->id }}" {{ Auth::user()->rw_id == $rw->id ? 'selected' : '' }}>
                                    {{ $rw->nomor_rw }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="rt_id" class="form-label">Pilih RT</label>
                        <select name="rt_id" id="rt_id" class="form-select" required>
                            <option value="">-- Pilih RW Terlebih Dahulu --</option>
                            @foreach($rts as $rt)
                                <option value="{{ $rt->id }}" {{ Auth::user()->rt_id == $rt->id ? 'selected' : '' }}>
                                    {{ $rt->nomor_rt }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="latitude">Latitude (Opsional)</label>
                            <input type="text" name="latitude" class="form-control @error('latitude') is-invalid @enderror"
                                id="latitude" value="{{ old('latitude') }}" placeholder="-6.2088">
                            @error('latitude') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="longitude">Longitude (Opsional)</label>
                            <input type="text" name="longitude"
                                class="form-control @error('longitude') is-invalid @enderror" id="longitude"
                                value="{{ old('longitude') }}" placeholder="106.8456">
                            @error('longitude') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group mb-2">
                    <label for="status_kondisi">Kondisi Fasilitas</label> {{-- Menggunakan 'status_kondisi' sesuai DB --}}
                    <select name="status_kondisi" class="form-control @error('status_kondisi') is-invalid @enderror"
                        id="status_kondisi">
                        <option value="">-- Pilih Kondisi --</option>
                        @foreach($kondisiOptions as $option)
                            <option value="{{ $option }}" {{ old('status_kondisi') == $option ? 'selected' : '' }}>{{ $option }}
                            </option>
                        @endforeach
                    </select>
                    @error('status_kondisi') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="row mb-2">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="panjang">Panjang (Opsional)</label> {{-- Kolom baru --}}
                            <input type="text" name="panjang" class="form-control @error('panjang') is-invalid @enderror"
                                id="panjang" value="{{ old('panjang') }}" placeholder="Contoh: 10 meter">
                            @error('panjang') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="lebar">Lebar (Opsional)</label> {{-- Kolom baru --}}
                            <input type="text" name="lebar" class="form-control @error('lebar') is-invalid @enderror"
                                id="lebar" value="{{ old('lebar') }}" placeholder="Contoh: 5 meter">
                            @error('lebar') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="form-group mb-2">
                    <label for="luas_area">Luas Bangunan (m², Opsional)</label> {{-- Kolom baru --}}
                    <input type="text" name="luas_area" class="form-control @error('luas_area') is-invalid @enderror"
                        id="luas_area" value="{{ old('luas_area') }}" placeholder="Contoh: 1000 m²">
                    @error('luas_area') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group mb-2">
                    <label for="kapasitas">Kapasitas (Opsional)</label> {{-- Kolom baru --}}
                    <input type="text" name="kapasitas" class="form-control @error('kapasitas') is-invalid @enderror"
                        id="kapasitas" value="{{ old('kapasitas') }}" placeholder="Contoh: 200 jamaah, 50 siswa">
                    @error('kapasitas') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group mb-2">
                    <label for="kontak_pengelola">Kontak Pengelola (Opsional)</label> {{-- Kolom baru --}}
                    <input type="text" name="kontak_pengelola"
                        class="form-control @error('kontak_pengelola') is-invalid @enderror" id="kontak_pengelola"
                        value="{{ old('kontak_pengelola') }}" placeholder="Nama atau Nomor Telepon">
                    @error('kontak_pengelola') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group mb-2">
                    <label for="status_kepemilikan">Status Kepemilikan</label> {{-- Kolom baru --}}
                    <select name="status_kepemilikan" class="form-control @error('status_kepemilikan') is-invalid @enderror"
                        id="status_kepemilikan">
                        <option value="">-- Pilih Status Kepemilikan --</option>
                        @foreach($statusKepemilikanOptions as $option)
                            <option value="{{ $option }}" {{ old('status_kepemilikan') == $option ? 'selected' : '' }}>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                    @error('status_kepemilikan') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="mb-2">
                    <label for="photos" class="form-label">Foto Fasilitas Umum (Opsional, Multiple)</label>
                    <input class="form-control @error('photos.*') is-invalid @enderror" type="file" name="photos[]"
                        id="photos" accept="image/*" multiple>
                    @error('photos.*') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                    <small class="form-text text-muted">Ukuran maksimal 2MB per foto, format gambar.</small>
                </div>
                <div id="photos_preview" class="mt-2 mb-2 row"></div>

            </div>
            <div class="card-footer mb-2">
                <button type="submit" class="btn btn-primary">Simpan Fasilitas Umum</button>
                <a href="{{ route('portal.fasum.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection

@push('js')
<script>
    $(document).ready(function () {
        // Logika dropdown RT dinamis berdasarkan RW
        function getRtByRw(rwId) {
            if (!rwId) {
                $('#rt_id').html('<option value="">-- Pilih RW Terlebih Dahulu --</option>');
                return;
            }
            // URL ini perlu kita buat di routes/web.php
            let url = `{{ route('api.rts-by-rw', ['subdomain' => app('tenant')->subdomain]) }}?rw_id=${rwId}`;

            $.get(url, function (data) {
                let rtSelect = $('#rt_id');
                rtSelect.empty().append('<option value="">-- Pilih RT --</option>');
                $.each(data, function (index, rt) {
                    rtSelect.append(new Option(rt.nomor_rt, rt.id));
                });

                // Jika user adalah admin RT, otomatis pilih RT-nya
                let userRtId = "{{ Auth::user()->rt_id }}";
                if (userRtId) {
                    rtSelect.val(userRtId);
                }
            });
        }

        $('#rw_id').on('change', function () {
            getRtByRw($(this).val());
        });

        // Panggil saat halaman dimuat untuk mengisi RT jika RW sudah terpilih
        if ($('#rw_id').val()) {
            getRtByRw($('#rw_id').val());
        }

        $('#photos').on('change', function () {
            const previewContainer = $('#photos_preview');
            previewContainer.empty(); // Clear previous previews

            if (this.files && this.files.length > 0) {
                $.each(this.files, function (index, file) {
                    if (!file.type.startsWith('image/')) return; // Skip non-image

                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const col = $('<div class="col-md-3 mb-2"></div>');
                        const img = $('<img class="img-thumbnail" style="max-height: 150px; object-fit: cover;">')
                            .attr('src', e.target.result);
                        col.append(img);
                        previewContainer.append(col);
                    };
                    reader.readAsDataURL(file);
                });
            }
        });
    });
</script>
@endpush