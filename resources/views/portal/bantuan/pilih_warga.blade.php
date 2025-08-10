@extends('layouts.portal')
@section('title', 'Pilih Warga Penerima Bantuan')

@section('content')
<div class="container">
    <div class="card mb-3">
        <div class="card-header bg-primary text-white">
            <h4 class="card-title mb-0">Langkah 2: Pilih Warga Penerima</h4>
            <small>Program: <strong>{{ $kategoriBantuan->nama_bantuan }}</strong></small>
        </div>
        <div class="card-body row">
            {{-- FORM --}}
            <div class="col-lg-7 mb-3 border-end">
                <form action="{{ route('portal.bantuan.store', ['subdomain' => app('tenant')->subdomain, 'kategoriBantuan' => $kategoriBantuan]) }}"
                    id="bantuanForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="kategori_bantuan_id" value="{{ $kategoriBantuan->kategori_bantuan_id }}">
                    <div class="card-body">
                        @foreach (['success', 'error'] as $msg)
                            @if (session($msg))
                                <div class="alert alert-{{ $msg == 'error' ? 'danger' : $msg }} alert-dismissible fade show" role="alert">
                                    {{ session($msg) }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                        @endforeach
                        <div class="form-group">
                            <label for="tanggal_menerima">Tanggal Pengajuan</label>
                            <input type="date" name="tanggal_menerima"
                                class="form-control @error('tanggal_menerima') is-invalid @enderror"
                                id="tanggal_menerima" value="{{ old('tanggal_menerima', date('Y-m-d')) }}" required>
                            @error('tanggal_menerima') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <hr>
                        <h4>Pilih Calon Penerima</h4>
                        <p class="text-muted">Daftar di bawah ini sudah difilter berdasarkan kriteria kategori bantuan
                            ini.</p>
                        @if ($kategoriBantuan->allow_multiple_recipients_per_kk)
                            <p class="text-info">Kategori ini mengizinkan beberapa penerima dari satu Kartu Keluarga.
                            </p>
                        @else
                            <p class="text-info ">Kategori ini hanya mengizinkan satu penerima per Kartu Keluarga.
                                Warga/KK yang sudah menjadi penerima akan otomatis tidak muncul di daftar.</p>
                        @endif

                        <div class="form-group">
                            <label for="recipient_type">Tipe Penerima:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="recipient_type" id="type_warga"
                                    value="warga" {{ old('recipient_type', 'warga') == 'warga' ? 'checked' : '' }}>
                                <label class="form-check-label" for="type_warga">Individu Warga</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="recipient_type" id="type_kk"
                                    value="kk" {{ old('recipient_type') == 'kk' ? 'checked' : '' }}>
                                <label class="form-check-label" for="type_kk">Kartu Keluarga (KK)</label>
                            </div>
                        </div>

                        {{-- Bagian Pilih Warga (dengan AJAX Search) --}}
                        <div class="form-group" id="warga_select_group">
                            <label for="warga_ids">Pilih Individu Warga:</label>
                            <select name="warga_ids[]" id="warga_ids"
                                class="form-control select2-warga-ajax @error('warga_ids') is-invalid @enderror"
                                multiple="multiple" style="width: 100%;">
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
                        </div>

                        {{-- Bagian Pilih Kartu Keluarga (dengan AJAX Search) --}}
                        <div class="form-group" id="kk_select_group" style="display: none;">
                            <label for="kartu_keluarga_ids">Pilih Kartu Keluarga:</label>
                            <select name="kartu_keluarga_ids[]" id="kartu_keluarga_ids"
                                class="form-control select2-kk-ajax @error('kartu_keluarga_ids') is-invalid @enderror"
                                multiple="multiple" style="width: 100%;">
                                {{-- Opsi akan dimuat via AJAX --}}
                                @if(old('kartu_keluarga_ids'))
                                    @foreach(old('kartu_keluarga_ids') as $oldKkId)
                                        @php
                                            $oldKk = App\Models\KartuKeluarga::find($oldKkId);
                                        @endphp
                                        @if($oldKk)
                                            <option value="{{ $oldKk->id }}" selected>
                                                KK: {{ $oldKk->nomor_kk }} (Kepala:
                                                {{ $oldKk->kepalaKeluarga->nama_lengkap ?? '-' }})
                                            </option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                            @error('kartu_keluarga_ids') <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div id="dynamic-additional-fields">
                            @if(old('additional_fields'))
                                <h4>Data Verifikasi Lapangan / Tambahan</h4>
                                <p class="text-muted">Isi field-field tambahan yang diperlukan untuk kategori bantuan ini.</p>

                                @foreach(old('additional_fields') as $index => $fieldData)
                                    @php
                                        $fieldConfig = $requiredAdditionalFields[$index] ?? null; // Ambil config asli
                                        $fieldNameSlug = \Illuminate\Support\Str::slug($fieldConfig['name'] ?? 'unknown_field', '_');
                                    @endphp
                                    @if($fieldConfig)
                                        <div class="form-group">
                                            <label for="additional_fields_{{ $fieldNameSlug }}">{{ $fieldConfig['name'] }}
                                                @if($fieldConfig['required'])<span class="text-danger">*</span>@endif</label>
                                            @if($fieldConfig['type'] === 'textarea')
                                                <textarea name="additional_fields[{{ $fieldNameSlug }}]"
                                                    id="additional_fields_{{ $fieldNameSlug }}"
                                                    class="form-control @error('additional_fields.' . $fieldNameSlug) is-invalid @enderror"
                                                    rows="3" @if($fieldConfig['required']) required @endif>{{ $fieldData }}</textarea>
                                            @elseif($fieldConfig['type'] === 'checkbox')
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" name="additional_fields[{{ $fieldNameSlug }}]"
                                                        id="additional_fields_{{ $fieldNameSlug }}" class="custom-control-input"
                                                        value="1" {{ $fieldData ? 'checked' : '' }}>
                                                    <label class="custom-control-label"
                                                        for="additional_fields_{{ $fieldNameSlug }}">Ya</label>
                                                </div>
                                            @elseif($fieldConfig['type'] === 'file')
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="file" name="{{ $fieldNameSlug }}"
                                                            class="custom-file-input @error($fieldNameSlug) is-invalid @enderror"
                                                            id="{{ $fieldNameSlug }}" accept="image/*,application/pdf"
                                                            @if($fieldConfig['required']) required @endif>
                                                        <label class="custom-file-label" for="{{ $fieldNameSlug }}">Pilih file</label>
                                                    </div>
                                                </div>
                                            @else
                                                <input type="{{ $fieldConfig['type'] }}" name="additional_fields[{{ $fieldNameSlug }}]"
                                                    id="additional_fields_{{ $fieldNameSlug }}"
                                                    class="form-control @error('additional_fields.' . $fieldNameSlug) is-invalid @enderror"
                                                    value="{{ $fieldData }}" @if($fieldConfig['required']) required @endif>
                                            @endif
                                            @error('additional_fields.' . $fieldNameSlug) <span
                                            class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                            @error($fieldNameSlug) <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror {{-- For file uploads --}}
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
                                            <label for="additional_fields_{{ $fieldNameSlug }}">{{ $fieldConfig['name'] }}
                                                @if($fieldConfig['required'])<span class="text-danger">*</span>@endif</label>
                                            @if($fieldConfig['type'] === 'textarea')
                                                <textarea name="additional_fields[{{ $fieldNameSlug }}]"
                                                    id="additional_fields_{{ $fieldNameSlug }}" class="form-control" rows="3"
                                                    @if($fieldConfig['required']) required @endif></textarea>
                                            @elseif($fieldConfig['type'] === 'checkbox')
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" name="additional_fields[{{ $fieldNameSlug }}]"
                                                        id="additional_fields_{{ $fieldNameSlug }}" class="custom-control-input"
                                                        value="1">
                                                    <label class="custom-control-label"
                                                        for="additional_fields_{{ $fieldNameSlug }}">Ya</label>
                                                </div>
                                            @elseif($fieldConfig['type'] === 'file')
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="file" name="{{ $fieldNameSlug }}" class="custom-file-input"
                                                            id="{{ $fieldNameSlug }}" accept="image/*,application/pdf"
                                                            @if($fieldConfig['required']) required @endif>
                                                        <label class="custom-file-label" for="{{ $fieldNameSlug }}">Pilih file</label>
                                                    </div>
                                                </div>
                                            @else
                                                <input type="{{ $fieldConfig['type'] }}" name="additional_fields[{{ $fieldNameSlug }}]"
                                                    id="additional_fields_{{ $fieldNameSlug }}" class="form-control"
                                                    @if($fieldConfig['required']) required @endif>
                                            @endif
                                            @error('additional_fields.' . $fieldNameSlug) <span
                                            class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                            @error($fieldNameSlug) <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror {{-- For file uploads --}}
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted"></p>
                                @endif
                            @endif
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Ajukan</button>
                        <a href="{{ route('portal.bantuan.pilihBantuan', ['subdomain' => app('tenant')->subdomain]) }}"
                            class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>

            {{-- PREVIEW --}}
            <div class="col-lg-5">
                <h6>Preview Data</h6>
                <div class="border rounded p-3 bg-light" id="previewCanvas">
                    <p class="text-muted">Isi form di kiri untuk melihat preview di sini.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Daftar Penerima yang Sudah Diajukan</h5>
        </div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                @forelse($penerimaBantuans as $penerima)
                    <li class="list-group-item">
                        <strong>
                            @if ($penerima->warga)
                                {{ $penerima->warga->nama_lengkap }}
                            @elseif ($penerima->kartuKeluarga)
                                Kepala Keluarga: {{ $penerima->kartuKeluarga->kepalaKeluarga->nama_lengkap ?? '-' }}
                            @else
                                -
                            @endif
                        </strong>
                        <br>
                        <small class="text-muted">
                            @if ($penerima->warga)
                                NIK: {{ $penerima->warga->nik ?? '-'}}
                            @elseif ($penerima->kartuKeluarga)
                                No KK: {{ $penerima->kartuKeluarga->nomor_kk ?? '-'}}
                            @else
                                -
                            @endif
                        </small>
                        <br>
                        <span class="badge 
                                            @if($penerima->status_permohonan == 'Disetujui') bg-success
                                            @elseif($penerima->status_permohonan == 'Ditolak') bg-danger
                                            @else bg-warning text-dark
                                            @endif">
                            {{ $penerima->status_permohonan }}
                        </span>
                    </li>
                @empty
                    <li class="list-group-item text-center text-muted">
                        Belum ada warga yang diajukan.
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@stop

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            // Select2 untuk cari warga
            $('#warga_ids').select2({
                theme: 'bootstrap-5',
                placeholder: "Ketik Nama atau NIK Warga...",
                minimumInputLength: 3,
                ajax: {
                    url: "{{ route('search.warga', ['subdomain' => app('tenant')->subdomain]) }}",
                    dataType: 'json',
                    delay: 250,
                    processResults: data => ({ results: data.results }),
                }
            });


            // Inisialisasi Select2 untuk dropdown Kartu Keluarga (AJAX Search)
            $('#kartu_keluarga_ids').select2({
                theme: 'bootstrap-5',
                placeholder: 'Ketik Nomor KK atau Nama Kepala Keluarga...',
                allowClear: true,
                minimumInputLength: 3,
                ajax: {
                    url: "{{ route('search.kk', ['subdomain' => app('tenant')->subdomain]) }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            term: params.term, // Query pencarian
                            kategori_id: {{ $kategoriBantuan->id }} // Kirim ID kategori bantuan
                                };
                    },
                    processResults: function (data) {
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
                        }).done(function (data) { callback(data.results[0]); });
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

            // Preview foto per file input
            $(document).on('change', '.file-preview', function (e) {
                let file = e.target.files[0];
                let label = $(this).closest('.mb-3').find('label').text().trim();

                if (file) {
                    if (file.type.startsWith('image/')) {
                        let reader = new FileReader();
                        reader.onload = function (ev) {
                            $('#previewCanvas').append(`
                                                                <div class="mb-2">
                                                                    <strong>${label}</strong><br>
                                                                    <img src="${ev.target.result}" class="img-fluid rounded border" style="max-height:150px;">
                                                                </div>
                                                            `);
                        }
                        reader.readAsDataURL(file);
                    } else {
                        $('#previewCanvas').append(`
                                                            <div class="mb-2">
                                                                <strong>${label}:</strong> ${file.name}
                                                            </div>
                                                        `);
                    }
                }
            });

            document.querySelectorAll(".file-preview").forEach(function (input) {
                input.addEventListener("change", function () {
                    const previewContainer = document.getElementById(this.dataset.previewContainer);
                    const file = this.files[0];

                    if (!file) return;

                    if (file.type.startsWith("image/")) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            const img = document.createElement("img");
                            img.src = e.target.result;
                            img.classList.add("img-thumbnail", "me-2", "mb-2");
                            img.style.maxHeight = "150px";
                            previewContainer.appendChild(img); // append, bukan replace
                        };
                        reader.readAsDataURL(file);
                    } else if (file.type === "application/pdf") {
                        const pdfIcon = document.createElement("div");
                        pdfIcon.innerHTML = `<span class="badge bg-secondary">${file.name}</span>`;
                        previewContainer.appendChild(pdfIcon);
                    }
                });
            });
        });
    </script>

@endpush