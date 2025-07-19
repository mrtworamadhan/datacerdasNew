@extends('admin.master')
@section('title', 'Edit Template Surat')
@section('plugins.Select2', true)
@section('plugins.Summernote', true)
@push('css')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        #preview-container table {
            width: 100%;
            border-collapse: collapse;
            border: none !important;
            font-size: 10px;
        }

        #preview-container td p {
            margin: 0;
            padding: 0;
            line-height: 1.5;
        }

        #preview-container table tr td:first-child {
            width: 25%;
        }

        #preview-container table tr td:nth-child(2) {
            width: 3%;
        }

        #preview-container td,
        #preview-container th {
            word-break: break-word;
            white-space: normal;
            padding: 1.5px;
            line-height: 1;
            vertical-align: top;
            height: 25px;
            border: none !important;
        }
    </style>
@endpush
@section('content_header')<h1 class="m-0 text-dark">Edit Template Surat</h1>@stop
@section('content')
<form action="{{ route('jenis-surat.update', $jenisSurat) }}" method="POST">
    @method('PUT')
    @include('admin_desa.jenis_surat._form')
</form>
@stop
@push('js')
    <script>
        $(document).ready(function () {
            $('.select2').select2();

            var baseVariables = [
                '[nama_warga]', '[nik_warga]', '[tempat_lahir]', '[tanggal_lahir]',
                '[jenis_kelamin]', '[alamat_lengkap]', '[agama]', '[status_perkawinan]',
                '[pekerjaan]', '[kewarganegaraan]', '[nama_kepala_keluarga]', '[nomor_kk]',
                '[alamat_kk]', '[tanggal_surat]', '[jabatan_kades]', '[nama_kades]', '[nama_desa]', '[kecamatan]'
            ];

            // Awal: inisialisasi Summernote dengan hanya baseVariables
            initializeSummernote([]);

            function initializeSummernote(customVariables) {
                const allVariables = baseVariables.concat(customVariables);

                var VariableButton = function (context) {
                    var ui = $.summernote.ui;
                    return ui.buttonGroup([
                        ui.button({
                            className: 'dropdown-toggle',
                            contents: '<i class="fas fa-user-circle"></i> Input Data Warga <span class="caret"></span>',
                            tooltip: 'Sisipkan Variabel Dinamis',
                            data: { toggle: 'dropdown' }
                        }),
                        ui.dropdown({
                            className: 'dropdown-style',
                            items: allVariables,
                            click: function (e) {
                                var variable = $(e.target).text();
                                context.invoke('editor.insertText', variable);
                            }
                        })
                    ]).render();
                };

                if ($('.summernote').data('summernote')) {
                    $('.summernote').summernote('destroy');
                }

                $('.summernote').summernote({
                    height: 450,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
                        ['fontname', ['fontname']],
                        ['fontsize', ['fontsize']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['height', ['height']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture', 'video', 'hr']],
                        ['misc', ['undo', 'redo']],
                        ['view', ['fullscreen', 'codeview', 'help']],
                        ['mybutton', ['variable']],
                    ],
                    buttons: { variable: VariableButton },
                    callbacks: {
                        onChange: function () {
                            updatePreview();
                        }
                    }
                });
            }

            // Fungsi perbarui berdasarkan custom field input
            function processCustomFields() {
                const customFieldsText = $('#custom_fields_text').val();
                const customFieldsArray = customFieldsText.split('\n')
                    .filter(Boolean)
                    .map(f => '[custom_' + f.trim().replace(/\s+/g, '_').toLowerCase() + ']');
                initializeSummernote(customFieldsArray);
            }

            // Trigger perubahan custom field
            $('#custom_fields_text').on('keyup', processCustomFields);

            // Dummy data dan preview...
            const suratSetting = @json($suratSetting);
            const desa = @json($desa);
            const dummyData = {
                '[nama_warga]': '<span style="word-break: break-word;">Budi Santoso</span>',
                '[nik_warga]': '<span style="word-break: break-word;">3201234567890001</span>',
                '[tempat_lahir]': '<span style="word-break: break-word;">Bogor</span>',
                '[tanggal_lahir]': '<span style="word-break: break-word;">01 Januari 1990</span>',
                '[jenis_kelamin]': '<span style="word-break: break-word;">Laki-laki</span>',
                '[alamat_lengkap]': '<span style="word-break: break-word;">Kp. Contoh RT 01/RW 01, Desa Contoh, Kecamatan Contoh, Kab. Bogor</span>',
                '[agama]': '<span style="word-break: break-word;">Islam</span>',
                '[status_perkawinan]': 'Kawin',
                '[pekerjaan]': 'Wiraswasta',
                '[kewarganegaraan]': 'WNI',
                '[nama_kepala_keluarga]': 'Ahmad Subarjo',
                '[nomor_kk]': '3201234567890002',
                '[alamat_kk]': 'Kp. Contoh RT 01/RW 01, Desa Contoh, Kecamatan Contoh, Kab. Bogor',
                '[tanggal_surat]': new Date().toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                }),
                '[jabatan_kades]': suratSetting.penanda_tangan_jabatan || 'Kepala Desa',
                '[nama_kades]': suratSetting.penanda_tangan_nama || 'Nama Kepala Desa',
                '[nama_desa]': desa.nama_desa,
                '[kecamatan]': desa.kecamatan,
            };

            function updatePreview() {
                let content = $('#isi_template').summernote('code');
                let judul = $('#judul_surat').val();
                let kode = $('#klasifikasi_surat_id').find('option:selected').text().split(' - ')[0] || '...';
                $('#preview-judul').text(judul?.toUpperCase() || '');
                $('#preview-nomor-surat').text(`Nomor : ${kode} / ... / ... / {{ date('Y') }}`);

                for (const [key, val] of Object.entries(dummyData)) {
                    content = content.replaceAll(key, val);
                }
                $('#preview-content').html(content);
            }

            $('#judul_surat, #klasifikasi_surat_id').on('keyup change', updatePreview);
        });

    </script>
@endpush