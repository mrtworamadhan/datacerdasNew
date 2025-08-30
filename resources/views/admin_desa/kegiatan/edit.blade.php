@extends('admin.master')
@section('title', 'Edit Kegiatan')
@section('content_header')<h1 class="m-0 text-dark">Edit Kegiatan: {{ $kegiatan->nama_kegiatan }}</h1>@stop
@section('content_main')
<form action="{{ route('kegiatans.update', $kegiatan) }}" method="POST" enctype="multipart/form-data">
    @method('PUT')
    @include('admin_desa.kegiatan._form')
</form>
@stop
@push('js')
    @include('admin_desa.kegiatan._resizer_js')
    @include('admin_desa.kegiatan._ai_js')
    <script>
        $(document).ready(function () {
            // 1. Siapkan semua data yang dibutuhkan
            const lembagas = @json($lembagas->pluck('nama_lembaga', 'id'));
            const kelompoks = @json($kelompoks->pluck('nama_kelompok', 'id'));

            const penyelenggaraTypeSelect = $('#penyelenggara_type');
            const penyelenggaraIdSelect = $('#penyelenggara_id');

            // 2. Ambil nilai yang tersimpan dari database (INI YANG PENTING)
            const selectedType = @json(old('penyelenggara_type', $penyelenggaraType ?? ''));
            const selectedId = @json(old('penyelenggara_id', $kegiatan->kegiatanable_id ?? ''));

            // 3. Buat fungsi untuk mengisi dropdown kedua
            function populatePenyelenggaraId(type) {
                penyelenggaraIdSelect.empty(); // Kosongkan dulu

                let options = {};
                if (type === 'lembaga') {
                    options = lembagas;
                    penyelenggaraIdSelect.append('<option value="">-- Pilih Nama Lembaga --</option>');
                } else if (type === 'kelompok') {
                    options = kelompoks;
                    penyelenggaraIdSelect.append('<option value="">-- Pilih Nama Kelompok --</option>');
                } else {
                    penyelenggaraIdSelect.append('<option value="">-- Pilih Jenis Penyelenggara Dulu --</option>');
                    return; // Berhenti jika tidak ada tipe
                }

                // Isi dropdown dengan opsi yang sesuai
                $.each(options, function (id, name) {
                    penyelenggaraIdSelect.append(new Option(name, id));
                });
            }

            // 4. Logika saat halaman pertama kali dimuat
            if (selectedType) {
                // Pilih jenis penyelenggara yang benar
                penyelenggaraTypeSelect.val(selectedType);

                // Panggil fungsi untuk mengisi dropdown kedua berdasarkan jenis yang sudah terpilih
                populatePenyelenggaraId(selectedType);

                // Setelah diisi, BARU pilih nama penyelenggara yang benar
                if (selectedId) {
                    penyelenggaraIdSelect.val(selectedId);
                }
            }

            // 5. Atur event listener untuk perubahan di masa depan
            penyelenggaraTypeSelect.on('change', function () {
                populatePenyelenggaraId($(this).val());
            });
        });
    </script>

@endpush