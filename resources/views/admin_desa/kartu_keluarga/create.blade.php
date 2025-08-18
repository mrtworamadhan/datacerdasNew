@extends('admin.master')

@section('title', 'Tambah Kartu Keluarga - TataDesa')

@section('content_header')
<h1 class="m-0 text-dark">Tambah Kartu Keluarga</h1>
@stop

@section('content_main')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Tambah Kartu Keluarga & Kepala Keluarga</h3>
    </div>
    <form action="{{ route('kartu-keluarga.store') }}" method="POST">
        @csrf
        <div class="card-body">
            @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif

            <h4>Data Kartu Keluarga</h4>
            <div class="form-group">
                <label for="nomor_kk">Nomor KK</label>
                <input type="text" name="nomor_kk" class="form-control @error('nomor_kk') is-invalid @enderror"
                    id="nomor_kk" value="{{ old('nomor_kk') }}" required>
                @error('nomor_kk') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="rw_id">RW</label>
                <select name="rw_id" class="form-control @error('rw_id') is-invalid @enderror" id="rw_id" required>
                    <option value="">-- Pilih RW --</option>
                    @foreach($rws as $rw)
                    <option value="{{ $rw->id }}" {{ old('rw_id') == $rw->id ? 'selected' : '' }}>RW {{ $rw->nomor_rw }}
                    </option>
                    @endforeach
                </select>
                @error('rw_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="rt_id">RT</label>
                <select name="rt_id" class="form-control @error('rt_id') is-invalid @enderror" id="rt_id" required>
                    <option value="">-- Pilih RT --</option>
                    {{-- Opsi RT akan dimuat dinamis oleh JavaScript --}}
                </select>
                @error('rt_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="alamat_lengkap_kk">Alamat Lengkap KK</label>
                <textarea name="alamat_lengkap_kk" class="form-control @error('alamat_lengkap_kk') is-invalid @enderror"
                    id="alamat_lengkap_kk" rows="3" required>{{ old('alamat_lengkap_kk') }}</textarea>
                @error('alamat_lengkap_kk') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="klasifikasi">Klasifikasi Keluarga</label>
                <select name="klasifikasi" class="form-control @error('klasifikasi') is-invalid @enderror"
                    id="klasifikasi" required>
                    <option value="">-- Pilih Klasifikasi --</option>
                    @foreach($klasifikasiOptions as $option)
                    <option value="{{ $option }}" {{ old('klasifikasi') == $option ? 'selected' : '' }}>{{ $option }}
                    </option>
                    @endforeach
                </select>
                @error('klasifikasi') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <hr>
            <h4>Data Kepala Keluarga</h4>

            {{-- Bagian OCR --}}
            <div class="form-group">
                <label for="ktp_image_kk">Scan KTP Kepala Keluarga (Opsional)</label>
                <div class="input-group mb-3">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="ktp_image_kk" accept="image/*">
                        <label class="custom-file-label" for="ktp_image_kk">Pilih gambar KTP</label>
                    </div>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="scan_ktp_kk">Scan KTP</button>
                    </div>
                </div>
                <div id="ktp_preview_container" class="mb-3" style="display: none; max-width: 300px;">
                    <img id="ktp_preview_kk" src="#" alt="KTP Preview" style="max-width: 100%; height: auto; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div id="ocr_loading_kk" class="text-info" style="display: none;">
                    <i class="fas fa-spinner fa-spin"></i> Memproses KTP ...
                </div>
                <div id="ocr_result_text_kk" class="mt-2 p-2 bg-light border" style="display: none; white-space: pre-wrap; font-size: 0.8em;"></div>
                <button type="button" class="btn btn-sm btn-danger mt-2" id="removePreview">Hapus Gambar</button>
                <div class="overlay" id="loading-overlay" style="display: none;">
                    <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                </div>
            </div>
            {{-- End Bagian OCR --}}
            <!-- Modal Cropper -->
            <div class="modal fade" id="cropperModal" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel" aria-hidden="true">
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
                <label for="nik_kk">NIK Kepala Keluarga</label>
                <input type="text" name="nik_kk" class="form-control @error('nik_kk') is-invalid @enderror" id="nik_kk"
                    value="{{ old('nik_kk') }}" required maxlength="16">
                @error('nik_kk') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="nama_lengkap_kk">Nama Lengkap Kepala Keluarga</label>
                <input type="text" name="nama_lengkap_kk"
                    class="form-control @error('nama_lengkap_kk') is-invalid @enderror" id="nama_lengkap_kk"
                    value="{{ old('nama_lengkap_kk') }}" required>
                @error('nama_lengkap_kk') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tempat_lahir_kk">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir_kk"
                            class="form-control @error('tempat_lahir_kk') is-invalid @enderror" id="tempat_lahir_kk"
                            value="{{ old('tempat_lahir_kk') }}" required>
                        @error('tempat_lahir_kk') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tanggal_lahir_kk">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir_kk"
                            class="form-control @error('tanggal_lahir_kk') is-invalid @enderror" id="tanggal_lahir_kk"
                            value="{{ old('tanggal_lahir_kk') }}" required>
                        @error('tanggal_lahir_kk') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="jenis_kelamin_kk">Jenis Kelamin</label>
                <select name="jenis_kelamin_kk" class="form-control @error('jenis_kelamin_kk') is-invalid @enderror"
                    id="jenis_kelamin_kk" required>
                    <option value="">-- Pilih Jenis Kelamin --</option>
                    @foreach($jenisKelaminOptions as $option)
                    <option value="{{ $option }}" {{ old('jenis_kelamin_kk') == $option ? 'selected' : '' }}>{{ $option }}
                    </option>
                    @endforeach
                </select>
                @error('jenis_kelamin_kk') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="agama_kk">Agama</label>
                <select name="agama_kk" class="form-control @error('agama_kk') is-invalid @enderror" id="agama_kk"
                    required>
                    <option value="">-- Pilih Agama --</option>
                    @foreach($agamaOptions as $option)
                    <option value="{{ $option }}" {{ old('agama_kk') == $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
                @error('agama_kk') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="status_perkawinan_kk">Status Perkawinan</label>
                <select name="status_perkawinan_kk"
                    class="form-control @error('status_perkawinan_kk') is-invalid @enderror" id="status_perkawinan_kk"
                    required>
                    <option value="">-- Pilih Status Perkawinan --</option>
                    @foreach($statusPerkawinanOptions as $option)
                    <option value="{{ $option }}" {{ old('status_perkawinan_kk') == $option ? 'selected' : '' }}>
                        {{ $option }}
                    </option>
                    @endforeach
                </select>
                @error('status_perkawinan_kk') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="pekerjaan_kk">Pekerjaan</label>
                <select name="pekerjaan_kk" class="form-control select2 @error('pekerjaan_kk') is-invalid @enderror"
                    id="pekerjaan_kk" required>
                    <option value="">-- Pilih Pekerjaan --</option>
                    @foreach($pekerjaanOptions as $option)
                    <option value="{{ $option }}" {{ old('pekerjaan_kk') == $option ? 'selected' : '' }}>{{ $option }}
                    </option>
                    @endforeach
                </select>
                @error('pekerjaan_kk') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="pendidikan_kk">Pendidikan</label>
                <select name="pendidikan_kk" class="form-control @error('pendidikan_kk') is-invalid @enderror"
                    id="pendidikan_kk">
                    <option value="">-- Pilih Pendidikan --</option>
                    @foreach($pendidikanOptions as $option)
                    <option value="{{ $option }}" {{ old('pendidikan_kk') == $option ? 'selected' : '' }}>{{ $option }}
                    </option>
                    @endforeach
                </select>
                @error('pendidikan_kk') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="kewarganegaraan_kk">Kewarganegaraan</label>
                <select name="kewarganegaraan_kk" class="form-control @error('kewarganegaraan_kk') is-invalid @enderror"
                    id="kewarganegaraan_kk" required>
                    <option value="">-- Pilih Kewarganegaraan --</option>
                    @foreach($kewarganegaraanOptions as $option)
                    <option value="{{ $option }}" {{ old('kewarganegaraan_kk') == $option ? 'selected' : '' }}>
                        {{ $option }}
                    </option>
                    @endforeach
                </select>
                @error('kewarganegaraan_kk') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="golongan_darah_kk">Golongan Darah (Opsional)</label>
                <select name="golongan_darah_kk" class="form-control @error('golongan_darah_kk') is-invalid @enderror"
                    id="golongan_darah_kk">
                    <option value="">-- Pilih Golongan Darah --</option>
                    @foreach($golonganDarahOptions as $option)
                    <option value="{{ $option }}" {{ old('golongan_darah_kk') == $option ? 'selected' : '' }}>
                        {{ $option }}
                    </option>
                    @endforeach
                </select>
                @error('golongan_darah_kk') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="alamat_lengkap_warga_kk">Alamat Lengkap Kepala Keluarga (Sesuai KTP)</label>
                <textarea name="alamat_lengkap_warga_kk"
                    class="form-control @error('alamat_lengkap_warga_kk') is-invalid @enderror"
                    id="alamat_lengkap_warga_kk" rows="3" required>{{ old('alamat_lengkap_warga_kk') }}</textarea>
                @error('alamat_lengkap_warga_kk') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="status_kependudukan_kk">Status Kependudukan</label>
                <select name="status_kependudukan_kk"
                    class="form-control @error('status_kependudukan_kk') is-invalid @enderror"
                    id="status_kependudukan_kk" required>
                    <option value="">-- Pilih Status Kependudukan --</option>
                    @foreach($statusKependudukanOptions as $option)
                    <option value="{{ $option }}" {{ old('status_kependudukan_kk') == $option ? 'selected' : '' }}>
                        {{ $option }}
                    </option>
                    @endforeach
                </select>
                @error('status_kependudukan_kk') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            {{-- Status Khusus bisa ditambahkan di sini jika diperlukan di awal --}}
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Simpan KK & Kepala Keluarga</button>
            <a href="{{ route('kartu-keluarga.index') }}" class="btn btn-secondary">Batal</a>
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
    $(document).ready(function() {
        $('#pekerjaan_kk').select2({
            theme: 'bootstrap4',
            placeholder: "-- Pilih Pekerjaan --",
            allowClear: true
        });

        
        let cropper;
        let rawImageFile;

        $('#ktp_image_kk').on('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            rawImageFile = file;

            const reader = new FileReader();
            reader.onload = function(event) {
                // Preview langsung di bawah input
                $('#ktp_preview_kk').attr('src', event.target.result);
                $('#ktp_preview_container').show();

                // Juga tampilkan modal cropper
                $('#cropperImage').attr('src', event.target.result);
                $('#cropperModal').modal('show');
            };
            reader.readAsDataURL(file);
        });

        $('#cropImageButton').on('click', function() {
            const canvas = cropper.getCroppedCanvas({
                width: 1000,
                imageSmoothingQuality: 'high'
            });

            $('#ktp_preview_kk').attr('src', canvas.toDataURL());
            $('#ktp_preview_container').show();
            $('#ocr_result_text_kk').hide();

            canvas.toBlob(function(blob) {

                const newFile = new File([blob], rawImageFile.name, {
                    type: 'image/jpeg'
                });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(newFile);
                document.getElementById('ktp_image_kk').files = dataTransfer.files;
            }, 'image/jpeg');

            $('#cropperModal').modal('hide');
        });

        const ktpImageInput = $('#ktp_image_kk');
        const ktpPreview = $('#ktp_preview_kk');
        const ktpPreviewContainer = $('#ktp_preview_container');
        const scanKtpButton = $('#scan_ktp_kk');
        const ocrLoading = $('#ocr_loading_kk');
        const ocrResultText = $('#ocr_result_text_kk');
        const loadingOverlay = $('#loading-overlay');

        ktpImageInput.on('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    ktpPreview.attr('src', e.target.result);
                    ktpPreviewContainer.show();
                    ocrResultText.hide().empty();
                };
                reader.readAsDataURL(file);
            } else {
                ktpPreview.attr('src', '#');
                ktpPreviewContainer.hide();
                ocrResultText.hide().empty();
            }
        });

        scanKtpButton.on('click', function() {
            const file = ktpImageInput[0].files[0];
            if (!file) {
                alert('Silakan pilih gambar KTP terlebih dahulu.');
                return;
            }

            loadingOverlay.show();
            ocrLoading.show();
            ocrResultText.hide().empty();
            scanKtpButton.prop('disabled', true).text('Scanning...');

            const formData = new FormData();
            formData.append('ktp_image', file);
            formData.append('_token', '{{ csrf_token() }}'); // Tambahkan CSRF token

            $.ajax({
                url: "{{ route('api.ocr.ktp') }}", // Endpoint API OCR di Laravel
                type: 'POST',
                data: formData,
                processData: false, // Penting: Jangan memproses data FormData
                contentType: false, // Penting: Jangan mengatur Content-Type
                success: function(response) {
                    loadingOverlay.hide();
                    ocrLoading.hide();
                    if (response.success) {
                        fillFormFields(response.parsed_data); // Isi form dengan data yang sudah di-parsing
                        alert('Scan Selesai. Harap periksa dan koreksi data yang terisi otomatis.');
                    } else {
                        alert('Scan gagal: ' + (response.error || 'Terjadi kesalahan.'));
                        ocrResultText.text('Error: ' + (response.error || 'Unknown error')).show();
                    }
                    scanKtpButton.prop('disabled', false).text('Scan KTP');
                },
                error: function(xhr, status, error) {
                    loadingOverlay.hide();
                    ocrLoading.hide();
                    scanKtpButton.prop('disabled', false).text('Scan KTP');
                    let errorMessage = 'Gagal memproses KTP. Silakan coba lagi.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = 'Error: ' + xhr.responseJSON.message;
                    } else if (error) {
                        errorMessage = 'Error: ' + error;
                    }
                    alert(errorMessage);
                    ocrResultText.text(errorMessage).show();
                    console.error("AJAX Error:", xhr.responseText);
                }
            });
        });

        // Fungsi untuk mengisi field form
        function fillFormFields(data) {
            if (data.nik) $('#nik_kk').val(data.nik);
            if (data.nama_lengkap) $('#nama_lengkap_kk').val(data.nama_lengkap);
            if (data.tempat_lahir) $('#tempat_lahir_kk').val(data.tempat_lahir);
            if (data.tanggal_lahir) $('#tanggal_lahir_kk').val(data.tanggal_lahir); // Format YYYY-MM-DD
            if (data.jenis_kelamin) $('#jenis_kelamin_kk').val(data.jenis_kelamin).trigger('change');
            if (data.agama) $('#agama_kk').val(data.agama).trigger('change');
            if (data.status_perkawinan) $('#status_perkawinan_kk').val(data.status_perkawinan).trigger('change');
            if (data.pekerjaan) $('#pekerjaan_kk').val(data.pekerjaan).trigger('change');
            if (data.kewarganegaraan) $('#kewarganegaraan_kk').val(data.kewarganegaraan).trigger('change');
            if (data.golongan_darah) $('#golongan_darah_kk').val(data.golongan_darah).trigger('change');
            if (data.alamat_lengkap) $('#alamat_lengkap_warga_kk').val(data.alamat_lengkap);


            if (data.rw) {

                $('#rw_id').val(data.rw).trigger('change');

                setTimeout(() => {
                    if (data.rt) {
                        $('#rt_id').val(data.rt).trigger('change');
                    }
                }, 500);
            }
        }

        $('#removePreview').on('click', function() {
            $('#ktp_preview_kk').attr('src', '#');
            $('#ktp_preview_container').hide();
            $('#ktp_image_kk').val('');
        });

    });
</script>
@stop