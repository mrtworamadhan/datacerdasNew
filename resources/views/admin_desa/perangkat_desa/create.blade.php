@extends('admin.master')

@section('title', 'Tambah Perangkat Desa - TataDesa')

@section('content_header')
<h1 class="m-0 text-dark">Tambah Perangkat Desa</h1>
@stop

@section('content_main')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Tambah Perangkat Desa</h3>
        </div>
        <form action="{{ route('perangkat-desa.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="nama">Nama Perangkat Desa</label>
                    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" id="nama"
                        placeholder="Masukkan Nama Perangkat Desa" value="{{ old('nama') }}" required>
                    @error('nama')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="jabatan">Jabatan</label>
                    <input type="text" name="jabatan" class="form-control @error('jabatan') is-invalid @enderror"
                        id="jabatan" placeholder="Masukkan Jabatan (contoh: Sekretaris Desa, Kaur Keuangan)"
                        value="{{ old('jabatan') }}" required>
                    @error('jabatan')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="foto_path">Upload Foto (Opsional)</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="foto_path" name="foto_path">
                            <label class="custom-file-label" for="foto_path">Pilih file gambar...</label>
                        </div>
                    </div>
                    <small class="form-text text-muted">Gunakan foto formal.</small>
                </div>
                {{-- Opsi tautkan akun pengguna dihilangkan dan akan otomatis tertaut ke user yang login di controller --}}
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan Perangkat Desa</button>
                <a href="{{ route('perangkat-desa.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
@push('js')
<script>
    // Script untuk menampilkan nama file di custom file input
    $('.custom-file-input').on('change', function() {
        // Ambil nama file dari path lengkapnya
        let fileName = $(this).val().split('\\').pop(); 
        // Tampilkan nama file tersebut di label di sebelahnya
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
</script>
@endpush