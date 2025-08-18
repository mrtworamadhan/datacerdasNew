@extends('admin.master')

@section('title', 'Tambah Anggota KK ' . $kartuKeluarga->nomor_kk . ' - TataDesa')

@section('content_header')
<h1 class="m-0 text-dark">Tambah Anggota Keluarga</h1>
@stop

@section('content_main')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Tambah Anggota Keluarga untuk KK {{ $kartuKeluarga->nomor_kk }}</h3>
        </div>
        <form action="{{ route('kartu-keluarga.anggota.store', $kartuKeluarga) }}" method="POST">
            @csrf
            <div class="card-body">
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="form-group">
                    <label>Kartu Keluarga:</label>
                    <p><strong>{{ $kartuKeluarga->nomor_kk }}</strong> (Kepala Keluarga:
                        {{ $kartuKeluarga->kepalaKeluarga->nama_lengkap ?? '-' }})
                    </p>
                    <p>RW {{ $rw->nomor_rw ?? '-' }} / RT {{ $rt->nomor_rt ?? '-' }}</p>
                </div>

                <hr>
                <h4>Data Anggota Keluarga</h4>
                <div class="form-group">
                    <label for="ktp_image">Scan KTP (Opsional)</label>
                    <div class="input-group mb-3">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="ktp_image" accept="image/*">
                            <label class="custom-file-label" for="ktp_image">Pilih gambar KTP</label>
                        </div>
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="scan_ktp">Scan KTP</button>
                        </div>
                    </div>
                    <div id="ktp_preview_container" class="mb-3" style="display: none; max-width: 300px;">
                        <img id="ktp_preview" src="#" alt="KTP Preview"
                            style="max-width: 100%; height: auto; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    <div id="ocr_loading" class="text-info" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i> Memproses KTP ...
                    </div>
                    <div class="overlay" id="loading-overlay" style="display: none;">
                        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                    </div>
                </div>
                <!-- Modal Cropper -->
                <div class="modal fade" id="cropperModal" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Sesuaikan Gambar KTP</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-center">
                                <img id="cropperImage" src="#" style="max-width: 100%;">
                            </div>
                            <div class="modal-footer">
                                <button type="button" id="cropImageButton" class="btn btn-primary">Gunakan Gambar</button>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <label for="nik">NIK</label>
                    <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" id="nik"
                        value="{{ old('nik') }}" required maxlength="16">
                    @error('nik') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="nama_lengkap">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror"
                        id="nama_lengkap" value="{{ old('nama_lengkap') }}" required>
                    @error('nama_lengkap') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tempat_lahir">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir"
                                class="form-control @error('tempat_lahir') is-invalid @enderror" id="tempat_lahir"
                                value="{{ old('tempat_lahir') }}" required>
                            @error('tempat_lahir') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tanggal_lahir">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir"
                                class="form-control @error('tanggal_lahir') is-invalid @enderror" id="tanggal_lahir"
                                value="{{ old('tanggal_lahir') }}" required>
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
                            <option value="{{ $option }}" {{ old('jenis_kelamin') == $option ? 'selected' : '' }}>{{ $option }}
                            </option>
                        @endforeach
                    </select>
                    @error('jenis_kelamin') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="agama_id">Agama</label>
                    <select name="agama_id" id="agama_id" class="form-control" required>
                        <option value="">-- Pilih Agama --</option>
                        @foreach($agamaOptions as $id => $nama)
                            <option value="{{ $id }}" {{ old('agama_id') == $id ? 'selected' : '' }}>
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
                            <option value="{{ $id }}" {{ old('status_perkawinan_id') == $id ? 'selected' : '' }}>
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
                            <option value="{{ $id }}" {{ old('pekerjaan_id') == $id ? 'selected' : '' }}>
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
                            <option value="{{ $id }}" {{ old('pendidikan_id') == $id ? 'selected' : '' }}>
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
                            <option value="{{ $option }}" {{ old('kewarganegaraan') == $option ? 'selected' : '' }}>{{ $option }}
                            </option>
                        @endforeach
                    </select>
                    @error('kewarganegaraan') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="golongan_darah_id">Golongan Darah</label>
                    <select name="golongan_darah_id" id="golongan_darah_id" class="form-control" required>
                        <option value="">-- Pilih Golongan Darah --</option>
                        @foreach($golonganDarahOptions as $id => $nama)
                            <option value="{{ $id }}" {{ old('golongan_darah_id') == $id ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="alamat_lengkap">Alamat Lengkap (Sesuai KTP)</label>
                    <textarea name="alamat_lengkap" class="form-control @error('alamat_lengkap') is-invalid @enderror"
                        id="alamat_lengkap" rows="3"
                        required>{{ old('alamat_lengkap', $kartuKeluarga->alamat_lengkap) }}</textarea>
                    @error('alamat_lengkap') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    <small class="form-text text-muted">Akan otomatis terisi dengan alamat KK, bisa diubah jika
                        berbeda.</small>
                </div>
                <div class="form-group">
                    <label for="hubungan_keluarga_id">Hubungan Keluarga</label>
                    <select name="hubungan_keluarga_id" id="hubungan_keluarga_id" class="form-control" required>
                        <option value="">-- Pilih Hubungan Keluarga --</option>
                        @foreach($hubunganKeluargaOptions as $id => $nama)
                            <option value="{{ $id }}" {{ old('hubungan_keluarga_id') == $id ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="nama_ayah_kandung">Ayah Kandung</label>
                    <input type="text" name="nama_ayah_kandung"
                        class="form-control @error('nama_ayah_kandung') is-invalid @enderror" id="nama_ayah_kandung"
                        value="{{ old('nama_ayah_kandung') }}" required>
                    @error('nama_ayah_kandung') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="nama_ibu_kandung">Ibu Kandung</label>
                    <input type="text" name="nama_ibu_kandung"
                        class="form-control @error('nama_ibu_kandung') is-invalid @enderror" id="nama_ibu_kandung"
                        value="{{ old('nama_ibu_kandung') }}" required>
                    @error('nama_ibu_kandung') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="status_kependudukan_id">Status Kependudukan</label>
                    <select name="status_kependudukan_id" id="status_kependudukan_id" class="form-control" required>
                        <option value="">-- Pilih Status Kependudukan --</option>
                        @foreach($statusKependudukanOptions as $id => $nama)
                            <option value="{{ $id }}" {{ old('status_kependudukan_id') == $id ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="status_khusus_id">Status Khusus</label>
                    <select name="status_khusus_id" id="status_khusus_id" class="form-control">
                        <option value="">-- Pilih Status Khusus --</option>
                        @foreach($statusKhususOptions as $id => $nama)
                            <option value="{{ $id }}" {{ old('status_khusus_id') == $id ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <!-- <div class="form-group">
                    <label>Status Khusus (Opsional)</label>
                    <div class="form-check">
                        @foreach($statusKhususOptions as $option)
                        <input class="form-check-input" type="checkbox" name="status_khusus[]" value="{{ $option }}"
                            id="status_khusus_{{ Str::slug($option) }}" {{ in_array($option, old('status_khusus', [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="status_khusus_{{ Str::slug($option) }}">
                            {{ $option }}
                        </label><br>
                        @endforeach
                    </div>
                    @error('status_khusus') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                </div> -->
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan Anggota Keluarga</button>
                <a href="{{ route('kartu-keluarga.anggota.index', $kartuKeluarga) }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection

@push('css')
    {{-- Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap4-theme/1.0.0/select2-bootstrap4.min.css"
        rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet" />
@endpush

@section('js')
{{-- Select2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
    $(document).ready(function () {
        // Inisialisasi Select2 untuk dropdown pekerjaan
        $('#pekerjaan').select2({
            theme: 'bootstrap4',
            placeholder: "-- Pilih Pekerjaan --",
            allowClear: true // Opsi untuk menghapus pilihan
        });

        // Otomatis mengisi alamat lengkap dengan alamat KK saat pertama kali load
        // Jika ada old input, gunakan old input
        if (!'{{ old('
            alamat_lengkap ') }}') {
            $('#alamat_lengkap').val('{{ $kartuKeluarga->alamat_lengkap }}');
        }
    });
</script>
<script>
    $(document).ready(function () {
        let cropper;
        let rawImageFile;

        const ktpImageInput = $('#ktp_image');
        const ktpPreview = $('#ktp_preview');
        const ktpPreviewContainer = $('#ktp_preview_container');
        const scanKtpButton = $('#scan_ktp');
        const ocrLoading = $('#ocr_loading');
        const loadingOverlay = $('#loading-overlay');

        ktpImageInput.on('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            rawImageFile = file;
            const reader = new FileReader();
            reader.onload = function (event) {
                $('#cropperImage').attr('src', event.target.result);
                $('#cropperModal').modal('show');
            };
            reader.readAsDataURL(file);
        });

        $('#cropperModal').on('shown.bs.modal', function () {
            cropper = new Cropper(document.getElementById('cropperImage'), {
                aspectRatio: 3 / 2,
                viewMode: 1
            });
        }).on('hidden.bs.modal', function () {
            cropper.destroy();
            cropper = null;
        });

        $('#cropImageButton').on('click', function () {
            const canvas = cropper.getCroppedCanvas({ width: 1000 });

            $('#ktp_preview').attr('src', canvas.toDataURL());
            $('#ktp_preview_container').show();

            canvas.toBlob(function (blob) {
                const newFile = new File([blob], rawImageFile.name, { type: 'image/jpeg' });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(newFile);
                ktpImageInput[0].files = dataTransfer.files;
            }, 'image/jpeg');

            $('#cropperModal').modal('hide');
        });

        scanKtpButton.on('click', function () {
            const file = ktpImageInput[0].files[0];
            if (!file) {
                alert('Silakan pilih gambar KTP terlebih dahulu.');
                return;
            }

            loadingOverlay.show();
            ocrLoading.show();
            scanKtpButton.prop('disabled', true).text('Scanning...');

            const formData = new FormData();
            formData.append('ktp_image', file);
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: "{{ route('api.ocr.ktp') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    loadingOverlay.hide();
                    ocrLoading.hide();
                    scanKtpButton.prop('disabled', false).text('Scan KTP');

                    if (response.success) {
                        fillFormFields(response.parsed_data);
                        alert('Scan selesai. Silakan periksa dan koreksi data.');
                    } else {
                        alert('Scan gagal: ' + (response.error || 'Terjadi kesalahan.'));
                    }
                },
                error: function (xhr) {
                    loadingOverlay.hide();
                    ocrLoading.hide();
                    scanKtpButton.prop('disabled', false).text('Scan KTP');
                    alert('Gagal memproses KTP. Silakan coba lagi.');
                }
            });
        });

        // Fungsi untuk mengisi field form
        function fillFormFields(data) {
            if (data.nik) $('#nik').val(data.nik);
            if (data.nama_lengkap) $('#nama_lengkap').val(data.nama_lengkap);
            if (data.tempat_lahir) $('#tempat_lahir').val(data.tempat_lahir);
            if (data.tanggal_lahir) $('#tanggal_lahir').val(data.tanggal_lahir); // Format YYYY-MM-DD
            if (data.jenis_kelamin) $('#jenis_kelamin').val(data.jenis_kelamin).trigger('change');
            if (data.agama) $('#agama').val(data.agama).trigger('change');
            if (data.status_perkawinan) $('#status_perkawinan').val(data.status_perkawinan).trigger('change');
            if (data.pekerjaan) $('#pekerjaan').val(data.pekerjaan).trigger('change');
            if (data.kewarganegaraan) $('#kewarganegaraan').val(data.kewarganegaraan).trigger('change');
            if (data.golongan_darah) $('#golongan_darah').val(data.golongan_darah).trigger('change');
            if (data.alamat_lengkap) $('#alamat_lengkap').val(data.alamat_lengkap);


            if (data.rw) {

                $('#rw_id').val(data.rw).trigger('change');

                setTimeout(() => {
                    if (data.rt) {
                        $('#rt_id').val(data.rt).trigger('change');
                    }
                }, 500);
            }
        }

        $('#removePreview').on('click', function () {
            $('#ktp_preview').attr('src', '#');
            $('#ktp_preview_container').hide();
            $('#ktp_image').val('');
        });
    });
</script>

@stop