@extends('admin.master')
@section('title', 'Tambah Data Anak')
@section('plugins.Select2', true)
@section('content_header')<h1 class="m-0 text-dark">Tambah Data Anak Baru</h1>@stop
@section('content_main')
<form action="{{ route('kesehatan-anak.store') }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-body">
            <div class="form-group">
                <label for="warga_id">Pilih Anak (Warga)</label>
                <select name="warga_id" id="warga_id" class="form-control @error('warga_id') is-invalid @enderror" required style="width: 100%;"></select>
                <small class="form-text text-muted">Cari berdasarkan nama atau NIK anak. Pastikan data anak sudah terdaftar di data kependudukan.</small>
                @error('warga_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="row">
                <div class="col-md-6 form-group"><label for="bb_lahir">Berat Badan Lahir (kg)</label><input type="number" step="0.1" name="bb_lahir" class="form-control" value="{{ old('bb_lahir') }}"></div>
                <div class="col-md-6 form-group"><label for="tb_lahir">Tinggi Badan Lahir (cm)</label><input type="number" step="0.1" name="tb_lahir" class="form-control" value="{{ old('tb_lahir') }}"></div>
            </div>
            <div class="row">
                <div class="col-md-6 form-group"><label for="nama_ibu">Nama Ibu</label><input type="text" name="nama_ibu" class="form-control" value="{{ old('nama_ibu') }}" required></div>
                <div class="col-md-6 form-group"><label for="nama_ayah">Nama Ayah (Opsional)</label><input type="text" name="nama_ayah" class="form-control" value="{{ old('nama_ayah') }}"></div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Simpan Data Anak</button>
            <a href="{{ route('kesehatan-anak.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </div>
</form>
@stop
@push('js')
<script>
$(document).ready(function() {
    $('#warga_id').select2({
        theme: 'bootstrap-5',
        placeholder: "Ketik Nama atau NIK Anak...",
        minimumInputLength: 3,
        ajax: {
            url: "{{ route('search.warga') }}",
            dataType: 'json',
            delay: 250,
            processResults: function (data) { return { results: data.results }; },
            cache: true
        }
    });
});
</script>
@endpush
