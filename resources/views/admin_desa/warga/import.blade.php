@extends('admin.master')
@section('title', 'Impor Data Warga')
@section('content_header')<h1 class="m-0 text-dark">Impor Data Warga</h1>@stop

@section('content_main')
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">Unggah File Data Warga</h3>
    </div>
    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(session('import_summary'))
            @php $summary = session('import_summary'); @endphp
            <div class="alert alert-default-secondary">
                <h5><i class="icon fas fa-check-circle"></i> Proses Impor Selesai!</h5>
                <p><strong>Ringkasan:</strong></p>
                <ul>
                    <li class="text-success"><strong>{{ $summary['success'] }} baris data berhasil diimpor.</strong></li>
                    <li class="text-danger"><strong>{{ count($summary['errors']) }} baris data gagal diimpor.</strong></li>
                </ul>

                @if(count($summary['errors']) > 0)
                    <hr>
                    <p><strong>Detail Kesalahan:</strong></p>
                    <div style="max-height: 200px; overflow-y: auto; background-color: #fff; padding: 10px; border-radius: 5px;">
                        <ul class="list-unstyled">
                            @foreach($summary['errors'] as $error)
                                <li><small><i class="fas fa-times-circle text-danger"></i> {{ $error }}</small></li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif
        <div class="alert alert-info">
            <h5><i class="icon fas fa-info"></i> Petunjuk Penting!</h5>
            <ol>
                <li>Unduh template Excel yang sudah disediakan untuk memastikan format data sudah benar.</li>
                <li>Isi data warga sesuai dengan kolom yang ada di template. Jangan mengubah nama header.</li>
                <li>Pastikan kolom **NO_KK**, **NIK**, **NAMA_LENGKAP**, **HUBUNGAN_KELUARGA**, **ALAMAT_LENGKAP** (untuk Kepala Keluarga), **RT**, dan **RW** sudah terisi dengan benar.</li>
                <li>Simpan file dalam format **.xlsx** atau **.csv**.</li>
            </ol>
            <a href="{{ route('warga.import.template') }}" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Unduh Template Impor
            </a>
        </div>

        <form action="{{ route('warga.import.process') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="file_warga">Pilih File untuk Diunggah</label>
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input @error('file_warga') is-invalid @enderror" id="file_warga" name="file_warga" required>
                        <label class="custom-file-label" for="file_warga">Pilih file...</label>
                    </div>
                </div>
                @error('file_warga')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Mulai Proses Impor</button>
        </form>
    </div>
</div>
@stop
@push('js')
<script>
    // Script untuk menampilkan nama file di custom file input
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
</script>
@endpush