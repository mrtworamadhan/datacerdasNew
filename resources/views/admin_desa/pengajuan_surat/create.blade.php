@extends('admin.master')
@section('title', 'Buat Pengajuan Surat')
@section('plugins.Select2', true)
@section('content_header')<h1 class="m-0 text-dark">Formulir Pengajuan Surat</h1>@stop

@section('content')
<form action="{{ route('pengajuan-surat.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="card card-purple card-outline">
                <div class="card-body">
                    {{-- Bagian Utama --}}
                    <div class="form-group">
                        <label for="warga_id">Pilih Warga yang Mengajukan</label>
                        <select name="warga_id" id="warga_id"
                            class="form-control @error('warga_id') is-invalid @enderror" required
                            style="width: 100%;"></select>
                        @error('warga_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="jenis_surat_id">Pilih Jenis Surat</label>
                        <select name="jenis_surat_id" id="jenis_surat_id"
                            class="form-control select2 @error('jenis_surat_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Jenis Surat --</option>
                            @foreach($jenisSurats as $jenis)
                                <option value="{{ $jenis->id }}" data-url="{{ route('api.jenis-surat.details', $jenis) }}">
                                    {{ $jenis->nama_surat }}
                                </option>
                            @endforeach
                        </select>
                        @error('jenis_surat_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="keperluan">Keperluan</label>
                        <textarea name="keperluan" id="keperluan" class="form-control" rows="3"
                            placeholder="Contoh: Untuk mengurus SKCK di kepolisian"
                            required>{{ old('keperluan') }}</textarea>
                    </div>
                    <button type="button" id="generate-pengantar-btn" class="btn btn-warning mb-3"><i
                            class="fas fa-print"></i> Buat & Download Surat Pengantar RT/RW</button>
                    <div id="ahli-waris-section" class="card card-outline card-info mt-4" style="display: none;">
                        <div class="card-header">
                            <h3 class="card-title">Data Ahli Waris</h3>
                        </div>
                        <div class="card-body">
                            <div id="ahli-waris-wrapper">
                                {{-- Baris input pertama untuk ahli waris --}}
                                <div class="row ahli-waris-item mb-2 align-items-center">
                                    <div class="col-md-5"><input type="text" name="ahli_waris[0][nama]"
                                            class="form-control" placeholder="Nama Lengkap Ahli Waris"></div>
                                    <div class="col-md-4"><input type="text" name="ahli_waris[0][nik]"
                                            class="form-control" placeholder="NIK Ahli Waris"></div>
                                    <div class="col-md-3"><input type="text" name="ahli_waris[0][hubungan]"
                                            class="form-control" placeholder="Hubungan Keluarga"></div>
                                </div>
                            </div>
                            <button type="button" id="tambah-ahli-waris-btn" class="btn btn-sm btn-secondary mt-2">
                                <i class="fas fa-plus"></i> Tambah Ahli Waris
                            </button>
                        </div>
                    </div>
                    <div id="dynamic-fields-container" class="mt-4" style="display: none;">
                        {{-- Checklist Persyaratan --}}
                        <div id="persyaratan-container" class="mb-4"></div>
                        {{-- Field Kustom --}}
                        <div id="custom-fields-container"></div>
                    </div>

                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Ajukan Surat</button>
                    <a href="{{ route('pengajuan-surat.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </div>
        </div>
    </div>
</form>
@stop

@push('js')
    <script>
        $(document).ready(function () {
            // Inisialisasi Select2 untuk Jenis Surat
            $('.select2').select2();

            // Inisialisasi Select2 AJAX untuk Warga
            $('#warga_id').select2({
                theme: 'bootstrap-5',
                placeholder: "Ketik Nama atau NIK Warga...",
                minimumInputLength: 3,
                ajax: {
                    url: "{{ route('search.warga') }}",
                    dataType: 'json',
                    delay: 250,
                    processResults: function (data) {
                        return { results: data.results };
                    },
                    cache: true
                }
            });

            $('#jenis_surat_id').on('change', function () {
                var selectedOption = $(this).find('option:selected');
                var url = selectedOption.data('url');
                var container = $('#dynamic-fields-container');
                var persyaratanContainer = $('#persyaratan-container');
                var customFieldsContainer = $('#custom-fields-container');

                persyaratanContainer.empty();
                customFieldsContainer.empty();
                container.hide();

                if (url) {
                    $.get(url, function (data) {
                        container.show();

                        if (data.persyaratan && data.persyaratan.length > 0) {
                            var persyaratanHtml = '<div class="form-group"><label>Kelengkapan Persyaratan</label>';
                            $.each(data.persyaratan, function (index, item) {
                                persyaratanHtml += `<div class="custom-control custom-checkbox">
                                                          <input type="checkbox" class="custom-control-input" id="syarat_${index}" name="persyaratan_terpenuhi[]" value="${item}">
                                                          <label class="custom-control-label" for="syarat_${index}">${item}</label>
                                                       </div>`;
                            });
                            persyaratanHtml += '</div>';
                            persyaratanContainer.html(persyaratanHtml);
                        }

                        if (data.custom_fields && data.custom_fields.length > 0) {
                            var customFieldsHtml = '<div class="form-group"><label>Isian Tambahan</label>';
                            $.each(data.custom_fields, function (index, item) {
                                var fieldName = 'custom_fields[' + item.replace(/\s+/g, '_').toLowerCase() + ']';
                                customFieldsHtml += `<div class="form-group">
                                                            <label for="${fieldName}">${item}</label>
                                                            <input type="text" name="${fieldName}" class="form-control" placeholder="Masukkan ${item}...">
                                                         </div>`;
                            });
                            customFieldsHtml += '</div>';
                            customFieldsContainer.html(customFieldsHtml);
                        }
                    });
                }
            });

            const jenisSuratSelect = $('#jenis_surat_id');
            const ahliWarisSection = $('#ahli-waris-section');
            const ahliWarisWrapper = $('#ahli-waris-wrapper');
            let ahliWarisIndex = 1;

            // Fungsi untuk mengecek apakah form ahli waris harus ditampilkan
            function checkJenisSurat() {
                // Ambil teks dari opsi yang dipilih
                const selectedText = jenisSuratSelect.find('option:selected').text().toLowerCase();

                // Jika teks mengandung kata "ahli waris", tampilkan section-nya
                if (selectedText.includes('ahli waris')) {
                    ahliWarisSection.show();
                } else {
                    ahliWarisSection.hide();
                }
            }

            // Jalankan pengecekan saat dropdown berubah
            jenisSuratSelect.on('change', function() {
                checkJenisSurat();
            });

            // Jalankan pengecekan saat halaman pertama kali dimuat
            checkJenisSurat();

            // Event listener untuk tombol "+ Tambah Ahli Waris"
            $('#tambah-ahli-waris-btn').on('click', function() {
                const newRow = `
                    <div class="row ahli-waris-item mb-2 align-items-center">
                        <div class="col-md-5"><input type="text" name="ahli_waris[${ahliWarisIndex}][nama]" class="form-control" placeholder="Nama Lengkap Ahli Waris"></div>
                        <div class="col-md-4"><input type="text" name="ahli_waris[${ahliWarisIndex}][nik]" class="form-control" placeholder="NIK Ahli Waris"></div>
                        <div class="col-md-2"><input type="text" name="ahli_waris[${ahliWarisIndex}][hubungan]" class="form-control" placeholder="Hubungan"></div>
                        <div class="col-md-1"><button type="button" class="btn btn-sm btn-danger remove-ahli-waris-btn">&times;</button></div>
                    </div>
                `;
                ahliWarisWrapper.append(newRow);
                ahliWarisIndex++;
            });

            // Event listener untuk tombol hapus baris
            ahliWarisWrapper.on('click', '.remove-ahli-waris-btn', function() {
                $(this).closest('.ahli-waris-item').remove();
            });
        });
        $('#generate-pengantar-btn').on('click', function () {
            var wargaId = $('#warga_id').val();
            var keperluan = $('#keperluan').val();

            if (!wargaId || !keperluan) {
                alert('Silakan pilih warga dan isi keperluan terlebih dahulu.');
                return;
            }

            // Buat form sementara untuk kirim data ke tab baru
            var form = $('<form>', {
                'method': 'POST',
                'action': '{{ route("pengajuan-surat.generatePengantar") }}',
                'target': '_blank'
            });
            form.append($('<input>', { 'type': 'hidden', 'name': '_token', 'value': '{{ csrf_token() }}' }));
            form.append($('<input>', { 'type': 'hidden', 'name': 'warga_id', 'value': wargaId }));
            form.append($('<input>', { 'type': 'hidden', 'name': 'keperluan', 'value': keperluan }));

            $('body').append(form);
            form.submit();
            form.remove();
        });
    </script>
@endpush