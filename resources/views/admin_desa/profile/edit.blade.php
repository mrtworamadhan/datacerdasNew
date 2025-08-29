@extends('admin.master')

@section('title', 'Profil Desa - TataDesa')

@section('content_header')
<h1 class="m-0 text-dark">Profil Desa</h1>
@stop

@section('content_main')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Edit Profil Desa Anda ({{ $desa->nama_desa }})</h3>
        </div>
        <form action="{{ route('admin_desa.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="form-group">
                    <label for="nama_desa">Nama Desa</label>
                    <input type="text" class="form-control" id="nama_desa" value="{{ $desa->nama_desa }}" disabled>
                    <small class="form-text text-muted">Nama desa hanya bisa diubah oleh Super Admin.</small>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="kecamatan">Kecamatan</label>
                            <input type="text" name="kecamatan" class="form-control"
                                value="{{ old('kecamatan', $desa->kecamatan ?? '') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="kota">Kota / Kabupaten</label>
                            <input type="text" name="kota" class="form-control" value="{{ old('kota', $desa->kota ?? '') }}"
                                required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="provinsi">Provinsi</label>
                            <input type="text" name="provinsi" class="form-control"
                                value="{{ old('provinsi', $desa->provinsi ?? '') }}" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="nama_kades">Nama Kepala Desa</label>
                    <input type="text" name="nama_kades" class="form-control @error('nama_kades') is-invalid @enderror"
                        id="nama_kades" placeholder="Masukkan Nama Kepala Desa"
                        value="{{ old('nama_kades', $desa->nama_kades) }}">
                    @error('nama_kades') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="alamat_desa">Alamat Desa</label>
                    <textarea name="alamat_desa" class="form-control @error('alamat_desa') is-invalid @enderror"
                        id="alamat_desa" rows="3"
                        placeholder="Masukkan Alamat Desa">{{ old('alamat_desa', $desa->alamat_desa) }}</textarea>
                    @error('alamat_desa') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="kode_pos">Kode Pos</label>
                    <input type="text" name="kode_pos" class="form-control @error('kode_pos') is-invalid @enderror"
                        id="kode_pos" placeholder="Masukkan Kode Pos" value="{{ old('kode_pos', $desa->kode_pos) }}">
                    @error('kode_pos') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="jumlah_rw_display">Jumlah RW Terdaftar</label>
                            <input type="text" class="form-control" id="jumlah_rw_display" value="{{ $desa->rws->count() }}"
                                disabled>
                            <small class="form-text text-muted">Jumlah ini berdasarkan akun RW yang sudah
                                digenerate.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="jumlah_rt_display">Jumlah RT Terdaftar per RW</label>
                            <input type="text" class="form-control" id="jumlah_rt_display"
                                value="{{ $desa->rts->count() }}"
                                disabled>
                            <small class="form-text text-muted">Jumlah ini berdasarkan akun RT yang sudah
                                digenerate.</small>
                        </div>
                    </div>
                </div>
                <hr>
                <h5>Pengaturan Halaman Sambutan Publik</h5>

                <div class="form-group">
                    <label for="sambutan_kades">Teks Sambutan Kepala Desa</label>
                    <textarea name="sambutan_kades" class="form-control" rows="5"
                        placeholder="Tuliskan kata sambutan singkat untuk ditampilkan di halaman utama desa...">{{ old('sambutan_kades', $desa->sambutan_kades) }}</textarea>
                    <small class="form-text text-muted">Teks ini akan muncul di bawah nama desa pada halaman profil
                        publik.</small>
                </div>

                <div class="form-group">
                    <label for="foto_kades_path">Upload Foto Kepala Desa</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="foto_kades_path" name="foto_kades_path">
                            <label class="custom-file-label" for="foto_kades_path">Pilih file gambar...</label>
                        </div>
                    </div>
                    <small class="form-text text-muted">Gunakan foto formal. Jika sudah ada, mengunggah file baru akan
                        menggantikan yang lama.</small>

                    {{-- Tampilkan foto yang sudah ada --}}
                    @if($desa->foto_kades_path)
                        <div class="mt-2">
                            <p>Foto saat ini:</p>
                            <img src="{{ asset('storage/' . $desa->foto_kades_path) }}" alt="Foto Kepala Desa"
                                class="img-thumbnail" style="max-width: 200px;">
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <label for="path_logo">Upload Logo Desa</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="path_logo" name="path_logo">
                            <label class="custom-file-label" for="path_logo">Pilih file gambar...</label>
                        </div>
                    </div>
                    <small class="form-text text-muted">Gunakan format png transparan</small>

                    {{-- Tampilkan foto yang sudah ada --}}
                    @if($desa->path_logo)
                        <div class="mt-2">
                            <p>Foto saat ini:</p>
                            <img src="{{ asset('storage/' . $desa->path_logo) }}" alt="Foto Kepala Desa"
                                class="img-thumbnail" style="max-width: 200px;">
                        </div>
                    @endif
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan Profil Desa</button>
            </div>
        </form>
    </div>

    {{-- Bagian untuk menampilkan generated_accounts dihapus dari sini --}}
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