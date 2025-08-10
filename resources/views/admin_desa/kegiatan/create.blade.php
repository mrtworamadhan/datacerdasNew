@extends('admin.master')
@section('title', 'Tambah Kegiatan Baru')
@section('content_header')<h1 class="m-0 text-dark">Ajukan Kegiatan Baru</h1>@stop
@section('content_main')
<form action="{{ route('kegiatans.store') }}" method="POST" enctype="multipart/form-data">
    @include('admin_desa.kegiatan._form')
</form>
@stop
@push('js')
    {{-- Panggil script resizer --}}
    @include('admin_desa.kegiatan._resizer_js')
    @include('admin_desa.kegiatan._ai_js')
    <script>
        $(document).ready(function () {
            // Siapkan data dari controller
            const lembagas = @json($lembagas->pluck('nama_lembaga', 'id'));
            const kelompoks = @json($kelompoks->pluck('nama_kelompok', 'id'));

            const penyelenggaraTypeSelect = $('#penyelenggara_type');
            const penyelenggaraIdSelect = $('#penyelenggara_id');
            const idWrapper = $('#penyelenggara_id_wrapper');

            function updatePenyelenggaraOptions() {
                const type = penyelenggaraTypeSelect.val();
                penyelenggaraIdSelect.empty().append('<option value="">-- Pilih Nama --</option>'); // Kosongkan dan tambahkan opsi default

                let options = {};
                if (type === 'lembaga') {
                    options = lembagas;
                } else if (type === 'kelompok') {
                    options = kelompoks;
                }

                if (type) {
                    $.each(options, function (id, name) {
                        penyelenggaraIdSelect.append(new Option(name, id));
                    });
                    idWrapper.show();
                } else {
                    idWrapper.hide();
                }
            }

            // Panggil fungsi saat halaman dimuat (untuk menangani old input)
            updatePenyelenggaraOptions();

            // Panggil fungsi setiap kali jenis penyelenggara berubah
            penyelenggaraTypeSelect.on('change', updatePenyelenggaraOptions);
        });
    </script>
@endpush