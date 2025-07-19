@extends('admin.master')

@section('title', 'Tambah Kategori Bantuan - TataDesa')

@section('content_header')
    <h1 class="m-0 text-dark">Tambah Kategori Bantuan</h1>
@stop

@section('content_main')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Tambah Kategori Bantuan</h3>
        </div>
        <form action="{{ route('kategori-bantuan.store') }}" method="POST">
            @csrf
            <div class="card-body">
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="form-group">
                    <label for="nama_kategori">Nama Kategori Bantuan</label>
                    <input type="text" name="nama_kategori" class="form-control @error('nama_kategori') is-invalid @enderror" id="nama_kategori" placeholder="Contoh: Bantuan Anak Yatim, BLT Dana Desa" value="{{ old('nama_kategori') }}" required>
                    @error('nama_kategori') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" rows="3" placeholder="Deskripsi singkat tentang kategori bantuan ini">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="allow_multiple_recipients_per_kk" name="allow_multiple_recipients_per_kk" value="1" {{ old('allow_multiple_recipients_per_kk') ? 'checked' : '' }}>
                        <label class="custom-control-label" for="allow_multiple_recipients_per_kk">Izinkan lebih dari satu penerima per Kartu Keluarga?</label>
                    </div>
                    <small class="form-text text-muted">Centang jika satu KK boleh memiliki beberapa anggota yang menerima bantuan ini (misal: bantuan anak yatim, setiap anak yatim di KK yang sama bisa menerima).</small>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="is_active_for_submission" name="is_active_for_submission" value="1" {{ old('is_active_for_submission', true) ? 'checked' : '' }}> {{-- Default true --}}
                        <label class="custom-control-label" for="is_active_for_submission">Aktifkan untuk Pengajuan?</label>
                    </div>
                    <small class="form-text text-muted">Centang untuk mengizinkan RT/RW mengajukan penerima untuk kategori ini.</small>
                </div>

                <hr>
                <h4>Kriteria Penerima (Opsional)</h4>
                <p class="text-muted">Tentukan kriteria agar sistem bisa membantu memfilter calon penerima.</p>

                @php
                    // Pastikan old input selalu berupa array untuk input array
                    $kriteriaStatusKeluarga = (array) old('kriteria_status_keluarga', []);
                    $kriteriaMemilikiBalita = (bool) old('kriteria_memiliki_balita', false);
                    $kriteriaHubunganKeluarga = (array) old('kriteria_hubungan_keluarga', []);
                    $kriteriaMinUsia = old('kriteria_min_usia', '');
                    $kriteriaMaxUsia = old('kriteria_max_usia', '');
                    $kriteriaJenisKelamin = old('kriteria_jenis_kelamin', '');
                    $kriteriaStatusKhusus = (array) old('kriteria_status_khusus', []);
                @endphp

                <div class="form-group">
                    <label>Status Keluarga:</label>
                    <div class="row">
                        @foreach($klasifikasiOptions as $option)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="kriteria_status_keluarga[]" value="{{ $option }}" id="kriteria_status_keluarga_{{ Str::slug($option) }}" {{ in_array($option, $kriteriaStatusKeluarga) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="kriteria_status_keluarga_{{ Str::slug($option) }}">
                                        {{ $option }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('kriteria_status_keluarga') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Hubungan Keluarga:</label>
                    <div class="row">
                        @foreach($hubunganKeluargaOptions as $option)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="kriteria_hubungan_keluarga[]" value="{{ $option }}" id="kriteria_hubungan_keluarga_{{ Str::slug($option) }}" {{ in_array($option, $kriteriaHubunganKeluarga) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="kriteria_hubungan_keluarga_{{ Str::slug($option) }}">
                                        {{ $option }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('kriteria_hubungan_keluarga') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="kriteria_memiliki_balita" name="kriteria_memiliki_balita" value="1" {{ $kriteriaMemilikiBalita ? 'checked' : '' }}>
                        <label class="custom-control-label" for="kriteria_memiliki_balita">Keluarga harus memiliki Balita?</label>
                    </div>
                    @error('kriteria_memiliki_balita') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Jenis Kelamin:</label>
                    <select name="kriteria_jenis_kelamin" class="form-control @error('kriteria_jenis_kelamin') is-invalid @enderror">
                        <option value="">-- Semua Jenis Kelamin --</option>
                        @foreach($jenisKelaminOptions as $option)
                            <option value="{{ $option }}" {{ $kriteriaJenisKelamin == $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                    @error('kriteria_jenis_kelamin') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="kriteria_min_usia">Usia Minimal (Tahun)</label>
                            <input type="number" name="kriteria_min_usia" class="form-control @error('kriteria_min_usia') is-invalid @enderror" id="kriteria_min_usia" value="{{ old('kriteria_min_usia', $kriteriaMinUsia) }}" min="0">
                            @error('kriteria_min_usia') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="kriteria_max_usia">Usia Maksimal (Tahun)</label>
                            <input type="number" name="kriteria_max_usia" class="form-control @error('kriteria_max_usia') is-invalid @enderror" id="kriteria_max_usia" value="{{ old('kriteria_max_usia', $kriteriaMaxUsia) }}" min="0">
                            @error('kriteria_max_usia') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Status Khusus Warga:</label>
                    <div class="row">
                        @foreach($statusKhususOptions as $option)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="kriteria_status_khusus[]" value="{{ $option }}" id="kriteria_status_khusus_{{ Str::slug($option) }}" {{ in_array($option, $kriteriaStatusKhusus) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="kriteria_status_khusus_{{ Str::slug($option) }}">
                                        {{ $option }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('kriteria_status_khusus') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                </div>

                <hr>
                <h4>Field Tambahan untuk Verifikasi Lapangan / Pengajuan</h4>
                <p class="text-muted">Definisikan field-field tambahan yang harus diisi saat pengajuan untuk kategori ini.</p>
                <div id="additional-fields-container">
                    @if(old('additional_fields'))
                        @foreach(old('additional_fields') as $index => $field)
                            <div class="form-row mb-2 additional-field-item">
                                <div class="col-md-5">
                                    <input type="text" name="additional_fields[{{ $index }}][name]" class="form-control" placeholder="Nama Field (contoh: Foto Depan Rumah)" value="{{ $field['name'] ?? '' }}" required>
                                </div>
                                <div class="col-md-3">
                                    <select name="additional_fields[{{ $index }}][type]" class="form-control" required>
                                        <option value="">Tipe</option>
                                        @foreach($additionalFieldTypes as $type)
                                            <option value="{{ $type }}" {{ ($field['type'] ?? '') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" name="additional_fields[{{ $index }}][required]" value="1" class="custom-control-input" id="add_field_required_{{ $index }}" {{ ($field['required'] ?? false) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="add_field_required_{{ $index }}">Wajib</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger btn-sm remove-additional-field">Hapus</button>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
                <button type="button" class="btn btn-success btn-sm" id="add-additional-field">Tambah Field Tambahan</button>

            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan Kategori Bantuan</button>
                <a href="{{ route('kategori-bantuan.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let additionalFieldIndex = {{ old('additional_fields') ? count(old('additional_fields')) : (isset($storedAdditionalFields) ? count($storedAdditionalFields) : 0) }};
            const additionalFieldTypes = @json($additionalFieldTypes); // Ambil dari PHP

            document.getElementById('add-additional-field').addEventListener('click', function () {
                const container = document.getElementById('additional-fields-container');
                const newItem = document.createElement('div');
                newItem.classList.add('form-row', 'mb-2', 'additional-field-item');
                
                let typeOptions = '';
                additionalFieldTypes.forEach(type => {
                    typeOptions += `<option value="${type}">${type.charAt(0).toUpperCase() + type.slice(1)}</option>`;
                });

                newItem.innerHTML = `
                    <div class="col-md-5">
                        <input type="text" name="additional_fields[${additionalFieldIndex}][name]" class="form-control" placeholder="Nama Field (contoh: Luas Kerusakan)" required>
                    </div>
                    <div class="col-md-3">
                        <select name="additional_fields[${additionalFieldIndex}][type]" class="form-control" required>
                            <option value="">Tipe</option>
                            ${typeOptions}
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="additional_fields[${additionalFieldIndex}][required]" value="1" class="custom-control-input" id="add_field_required_${additionalFieldIndex}" checked>
                            <label class="custom-control-label" for="add_field_required_${additionalFieldIndex}">Wajib</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-sm remove-additional-field">Hapus</button>
                    </div>
                `;
                container.appendChild(newItem);
                additionalFieldIndex++;
            });

            document.getElementById('additional-fields-container').addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-additional-field')) {
                    e.target.closest('.additional-field-item').remove();
                }
            });
        });
    </script>
@endsection
