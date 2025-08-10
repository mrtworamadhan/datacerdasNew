@extends('layouts.anjungan')

@section('content')
    <div class="text-center">
        <h4>Formulir Pengajuan</h4>
        <h3 class="mb-3">{{ $jenisSurat->nama_surat }}</h3>
    </div>

    <form action="{{ route('anjungan.prosesSurat', $jenisSurat->id) }}" method="POST">
        @csrf
        <div class="alert alert-info">
            <p class="mb-0">Data Anda sebagai <strong>{{ $warga->nama_lengkap }}</strong> akan digunakan secara otomatis. Silakan lengkapi isian di bawah ini.</p>
        </div>

        {{-- ====================================================== --}}
        {{-- === INI ADALAH "MESIN" DINAMIS DARI FORM ADMIN === --}}
        {{-- ====================================================== --}}
        <div id="dynamic-fields-container" class="mt-4">
            {{-- Checklist Persyaratan --}}
            @if($jenisSurat->persyaratan && count($jenisSurat->persyaratan) > 0)
                <div class="form-group">
                    <label>Mohon Siapkan dan Bawa Dokumen Berikut:</label>
                    @foreach($jenisSurat->persyaratan as $syarat)
                    <div class="custom-control custom-checkbox">
                       <input type="checkbox" class="custom-control-input" id="syarat_{{ $loop->index }}" required>
                       <label class="custom-control-label" for="syarat_{{ $loop->index }}">{{ $syarat }}</label>
                    </div>
                    @endforeach
                </div>
            @endif

            {{-- Field Kustom --}}
            @if($jenisSurat->custom_fields && count($jenisSurat->custom_fields) > 0)
                <div class="form-group mt-3">
                    <label>Isian Tambahan yang Diperlukan:</label>
                    @foreach($jenisSurat->custom_fields as $field)
                        @if($field !== 'tabel ahli waris')
                            @php
                                $fieldName = 'custom_fields[' . Str::slug($field, '_') . ']';
                            @endphp
                            <div class="form-group">
                                <label for="{{ $fieldName }}">{{ $field }}</label>
                                <input type="text" name="{{ $fieldName }}" class="form-control form-control-lg" placeholder="Masukkan {{ $field }}..." required>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
        {{-- ====================================================== --}}
        @if(Str::contains(strtolower($jenisSurat->nama_surat), 'ahli waris'))
        <div id="ahli-waris-section" class="card card-outline card-info mt-4">
            <div class="card-header"><h3 class="card-title">Data Ahli Waris</h3></div>
            <div class="card-body">
                <div id="ahli-waris-wrapper">
                    <div class="row ahli-waris-item mb-2 align-items-center">
                        <div class="col-md-4"><input type="text" name="ahli_waris[0][nama]" class="form-control form-control-lg" placeholder="Nama Lengkap"></div>
                        <div class="col-md-4"><input type="text" name="ahli_waris[0][nik]" class="form-control form-control-lg" placeholder="NIK"></div>
                        <div class="col-md-4"><input type="text" name="ahli_waris[0][hubungan]" class="form-control form-control-lg" placeholder="Hubungan Keluarga"></div>
                    </div>
                </div>
                <button type="button" id="tambah-ahli-waris-btn" class="btn btn-secondary mt-2"><i class="fas fa-plus"></i> Tambah Ahli Waris</button>
            </div>
        </div>
        @endif
        <div class="form-group mt-4">
            <label for="keperluan">Keperluan</label>
            <textarea name="keperluan" id="keperluan" class="form-control form-control-lg" rows="3" placeholder="Contoh: Untuk melamar pekerjaan" required></textarea>
        </div>

        <hr>
        <p class="text-muted">
            <strong>Penting:</strong> Pastikan semua data yang Anda masukkan sudah benar. Surat yang sudah dicetak akan tercatat dalam sistem.
        </p>

        <div class="row mt-4">
            <div class="col-6">
                <a href="{{ route('anjungan.pilihSurat') }}" class="btn btn-secondary btn-block btn-lg">Kembali</a>
            </div>
            <div class="col-6">
                <button type="submit" class="btn btn-success btn-block btn-lg">Lihat & Cetak Surat</button>
            </div>
        </div>
    </form>
@stop
@push('js')
<script>
$(document).ready(function() {
    let ahliWarisIndex = 1;

    $('#tambah-ahli-waris-btn').on('click', function() {
        const newRow = `
            <div class="row ahli-waris-item mb-2 align-items-center">
                <div class="col-md-4"><input type="text" name="ahli_waris[${ahliWarisIndex}][nama]" class="form-control form-control-lg" placeholder="Nama Lengkap"></div>
                <div class="col-md-4"><input type="text" name="ahli_waris[${ahliWarisIndex}][nik]" class="form-control form-control-lg" placeholder="NIK"></div>
                <div class="col-md-3"><input type="text" name="ahli_waris[${ahliWarisIndex}][hubungan]" class="form-control form-control-lg" placeholder="Hubungan"></div>
                <div class="col-md-1"><button type="button" class="btn btn-danger remove-ahli-waris-btn">&times;</button></div>
            </div>
        `;
        $('#ahli-waris-wrapper').append(newRow);
        ahliWarisIndex++;
    });

    $('#ahli-waris-wrapper').on('click', '.remove-ahli-waris-btn', function() {
        $(this).closest('.ahli-waris-item').remove();
    });
});
</script>
@endpush