@extends('layouts.portal')
@section('title', 'Ajukan Penerima Bantuan')
@section('content_main')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Ajukan Penerima Bantuan untuk Kategori: {{ $kategoriBantuan->nama_kategori }}</h3>
        </div>
        <form action="{{ route('kategori-bantuan.penerima.store', $kategoriBantuan) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="form-group">
                    <label for="tanggal_menerima">Tanggal Pengajuan</label>
                    <input type="date" name="tanggal_menerima" class="form-control @error('tanggal_menerima') is-invalid @enderror" id="tanggal_menerima" value="{{ old('tanggal_menerima', date('Y-m-d')) }}" required>
                    @error('tanggal_menerima') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="keterangan">Keterangan Pengajuan (Opsional)</label>
                    <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" rows="3">{{ old('keterangan') }}</textarea>
                    @error('keterangan') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <hr>
                <h4>Pilih Calon Penerima</h4>
                <p class="text-muted">Daftar di bawah ini sudah difilter berdasarkan kriteria kategori bantuan ini.</p>
                @if ($kategoriBantuan->allow_multiple_recipients_per_kk)
                    <p class="text-info">Kategori ini mengizinkan **beberapa penerima** dari satu Kartu Keluarga.</p>
                @else
                    <p class="text-info">Kategori ini hanya mengizinkan **satu penerima** per Kartu Keluarga. Warga/KK yang sudah menjadi penerima akan otomatis tidak muncul di daftar.</p>
                @endif

                <div class="form-group">
                    <label for="recipient_type">Tipe Penerima:</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="recipient_type" id="type_warga" value="warga" {{ old('recipient_type', 'warga') == 'warga' ? 'checked' : '' }}>
                        <label class="form-check-label" for="type_warga">Individu Warga</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="recipient_type" id="type_kk" value="kk" {{ old('recipient_type') == 'kk' ? 'checked' : '' }}>
                        <label class="form-check-label" for="type_kk">Kartu Keluarga (KK)</label>
                    </div>
                </div>

                {{-- Bagian Pilih Warga (dengan AJAX Search) --}}
                <div class="form-group" id="warga_select_group">
                    <label for="warga_ids">Pilih Individu Warga:</label>
                    <select name="warga_ids[]" id="warga_ids" class="form-control select2-warga-ajax @error('warga_ids') is-invalid @enderror" multiple="multiple" style="width: 100%;">
                        {{-- Opsi akan dimuat via AJAX --}}
                        @if(old('warga_ids'))
                            @foreach(old('warga_ids') as $oldWargaId)
                                @php
                                    $oldWarga = App\Models\Warga::find($oldWargaId);
                                @endphp
                                @if($oldWarga)
                                    <option value="{{ $oldWarga->id }}" selected>
                                        {{ $oldWarga->nama_lengkap }} (NIK: {{ $oldWarga->nik }})
                                    </option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                    @error('warga_ids') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                    <small class="form-text text-muted">Ketik NIK atau Nama Warga untuk mencari. Hanya warga di wilayah Anda yang akan muncul.</small>
                </div>

                {{-- Bagian Pilih Kartu Keluarga (dengan AJAX Search) --}}
                <div class="form-group" id="kk_select_group" style="display: none;">
                    <label for="kartu_keluarga_ids">Pilih Kartu Keluarga:</label>
                    <select name="kartu_keluarga_ids[]" id="kartu_keluarga_ids" class="form-control select2-kk-ajax @error('kartu_keluarga_ids') is-invalid @enderror" multiple="multiple" style="width: 100%;">
                        {{-- Opsi akan dimuat via AJAX --}}
                        @if(old('kartu_keluarga_ids'))
                            @foreach(old('kartu_keluarga_ids') as $oldKkId)
                                @php
                                    $oldKk = App\Models\KartuKeluarga::find($oldKkId);
                                @endphp
                                @if($oldKk)
                                    <option value="{{ $oldKk->id }}" selected>
                                        KK: {{ $oldKk->nomor_kk }} (Kepala: {{ $oldKk->kepalaKeluarga->nama_lengkap ?? '-' }})
                                    </option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                    @error('kartu_keluarga_ids') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                    <small class="form-text text-muted">Ketik Nomor KK atau Nama Kepala Keluarga untuk mencari. Hanya KK di wilayah Anda yang akan muncul.</small>
                </div>

                <hr>
                <h4>Data Verifikasi Lapangan / Tambahan</h4>
                <p class="text-muted">Isi field-field tambahan yang diperlukan untuk kategori bantuan ini.</p>
                <div id="dynamic-additional-fields">
                    @if(old('additional_fields'))
                        @foreach(old('additional_fields') as $index => $fieldData)
                            @php
                                $fieldConfig = $requiredAdditionalFields[$index] ?? null; // Ambil config asli
                                $fieldNameSlug = \Illuminate\Support\Str::slug($fieldConfig['name'] ?? 'unknown_field', '_');
                            @endphp
                            @if($fieldConfig)
                                <div class="form-group">
                                    <label for="additional_fields_{{ $fieldNameSlug }}">{{ $fieldConfig['name'] }} @if($fieldConfig['required'])<span class="text-danger">*</span>@endif</label>
                                    @if($fieldConfig['type'] === 'textarea')
                                        <textarea name="additional_fields[{{ $fieldNameSlug }}]" id="additional_fields_{{ $fieldNameSlug }}" class="form-control @error('additional_fields.'.$fieldNameSlug) is-invalid @enderror" rows="3" @if($fieldConfig['required']) required @endif>{{ $fieldData }}</textarea>
                                    @elseif($fieldConfig['type'] === 'checkbox')
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="additional_fields[{{ $fieldNameSlug }}]" id="additional_fields_{{ $fieldNameSlug }}" class="custom-control-input" value="1" {{ $fieldData ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="additional_fields_{{ $fieldNameSlug }}">Ya</label>
                                        </div>
                                    @elseif($fieldConfig['type'] === 'file')
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" name="{{ $fieldNameSlug }}" class="custom-file-input @error($fieldNameSlug) is-invalid @enderror" id="{{ $fieldNameSlug }}" accept="image/*,application/pdf" @if($fieldConfig['required']) required @endif>
                                                <label class="custom-file-label" for="{{ $fieldNameSlug }}">Pilih file</label>
                                            </div>
                                        </div>
                                    @else
                                        <input type="{{ $fieldConfig['type'] }}" name="additional_fields[{{ $fieldNameSlug }}]" id="additional_fields_{{ $fieldNameSlug }}" class="form-control @error('additional_fields.'.$fieldNameSlug) is-invalid @enderror" value="{{ $fieldData }}" @if($fieldConfig['required']) required @endif>
                                    @endif
                                    @error('additional_fields.'.$fieldNameSlug) <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    @error($fieldNameSlug) <span class="invalid-feedback d-block">{{ $message }}</span> @enderror {{-- For file uploads --}}
                                </div>
                            @endif
                        @endforeach
                    @else
                        {{-- Dynamic fields will be loaded here via JS based on requiredAdditionalFields --}}
                        @if(count($requiredAdditionalFields) > 0)
                            @foreach($requiredAdditionalFields as $index => $fieldConfig)
                                @php
                                    $fieldNameSlug = \Illuminate\Support\Str::slug($fieldConfig['name'], '_');
                                @endphp
                                <div class="form-group">
                                    <label for="additional_fields_{{ $fieldNameSlug }}">{{ $fieldConfig['name'] }} @if($fieldConfig['required'])<span class="text-danger">*</span>@endif</label>
                                    @if($fieldConfig['type'] === 'textarea')
                                        <textarea name="additional_fields[{{ $fieldNameSlug }}]" id="additional_fields_{{ $fieldNameSlug }}" class="form-control" rows="3" @if($fieldConfig['required']) required @endif></textarea>
                                    @elseif($fieldConfig['type'] === 'checkbox')
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="additional_fields[{{ $fieldNameSlug }}]" id="additional_fields_{{ $fieldNameSlug }}" class="custom-control-input" value="1">
                                            <label class="custom-control-label" for="additional_fields_{{ $fieldNameSlug }}">Ya</label>
                                        </div>
                                    @elseif($fieldConfig['type'] === 'file')
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" name="{{ $fieldNameSlug }}" class="custom-file-input" id="{{ $fieldNameSlug }}" accept="image/*,application/pdf" @if($fieldConfig['required']) required @endif>
                                                <label class="custom-file-label" for="{{ $fieldNameSlug }}">Pilih file</label>
                                            </div>
                                        </div>
                                    @else
                                        <input type="{{ $fieldConfig['type'] }}" name="additional_fields[{{ $fieldNameSlug }}]" id="additional_fields_{{ $fieldNameSlug }}" class="form-control" @if($fieldConfig['required']) required @endif>
                                    @endif
                                    @error('additional_fields.'.$fieldNameSlug) <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    @error($fieldNameSlug) <span class="invalid-feedback d-block">{{ $message }}</span> @enderror {{-- For file uploads --}}
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">Tidak ada field tambahan yang didefinisikan untuk kategori ini.</p>
                        @endif
                    @endif
                </div>

            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Ajukan Penerima Bantuan</button>
                <a href="{{ route('kategori-bantuan.penerima.index', $kategoriBantuan) }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection

@push('css')
    {{-- Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    {{-- Select2 Bootstrap 5 Theme CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@section('js')
    {{-- Select2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi Select2 untuk dropdown warga (AJAX Search)
            $('#warga_ids').select2({
                theme: 'bootstrap-5',
                placeholder: "-- Ketik NIK atau Nama Warga --",
                allowClear: true,
                minimumInputLength: 3,
                ajax: {
                    url: "{{ route('search.penerimaWarga') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term, // Query pencarian
                            kategori_id: {{ $kategoriBantuan->id }} // Kirim ID kategori bantuan
                        };
                    },
                    processResults: function(data) {
                        return { results: data.results };
                    },
                    cache: true
                }
            });

            // Inisialisasi Select2 untuk dropdown Kartu Keluarga (AJAX Search)
            $('#kartu_keluarga_ids').select2({
                theme: 'bootstrap-5',
                placeholder: 'Ketik Nomor KK atau Nama Kepala Keluarga...',
                allowClear: true,
                minimumInputLength: 3,
                ajax: {
                    url: "{{ route('search.kk') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term, // Query pencarian
                            kategori_id: {{ $kategoriBantuan->id }} // Kirim ID kategori bantuan
                        };
                    },
                    processResults: function(data) {
                        return { results: data.results };
                    },
                    cache: true
                },
                // Untuk menampilkan pilihan yang sudah ada dari old input
                initSelection: function (element, callback) {
                    const id = $(element).val();
                    if (id !== "") {
                        $.ajax("{{ route('search.kk') }}", {
                            data: { id: id }, // Kirim ID untuk mendapatkan data spesifik
                            dataType: "json"
                        }).done(function(data) { callback(data.results[0]); });
                    }
                }
            });

            // Logika untuk menampilkan/menyembunyikan dropdown berdasarkan pilihan radio
            const recipientTypeRadios = $('input[name="recipient_type"]');
            const wargaSelectGroup = $('#warga_select_group');
            const kkSelectGroup = $('#kk_select_group');

            function toggleRecipientTypeFields() {
                if ($('#type_warga').is(':checked')) {
                    wargaSelectGroup.show();
                    kkSelectGroup.hide();
                    $('#kartu_keluarga_ids').val(null).trigger('change'); // Kosongkan pilihan KK
                } else {
                    wargaSelectGroup.hide();
                    kkSelectGroup.show();
                    $('#warga_ids').val(null).trigger('change'); // Kosongkan pilihan Warga
                }
            }

            recipientTypeRadios.on('change', toggleRecipientTypeFields);
            
            // Panggil saat halaman dimuat untuk menyesuaikan tampilan awal
            // Berdasarkan old input atau default ke 'warga'
            const oldRecipientType = "{{ old('recipient_type', 'warga') }}";
            if (oldRecipientType === "kk") {
                $('#type_kk').prop('checked', true);
            } else {
                $('#type_warga').prop('checked', true);
            }
            toggleRecipientTypeFields();

            // Logika untuk menampilkan nama file yang dipilih di input file
            $(document).on('change', 'input[type="file"]', function() {
                var fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').html(fileName);
            });
        });
    </script>
@stop
