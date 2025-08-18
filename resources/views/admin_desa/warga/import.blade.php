@extends('admin.master')
@section('title', 'Impor Data Warga')
@section('content_header')
    <h1 class="m-0 text-dark">Impor Data Warga</h1>
@stop

@section('content_main')
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">Unggah File Data Warga</h3>
    </div>
    <div class="card-body">

        {{-- Pesan error umum --}}
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Ringkasan hasil validasi --}}
        @if(session('import_summary'))
            @php $summary = session('import_summary'); @endphp
            <div class="alert alert-default-secondary">
                <h5><i class="icon fas fa-check-circle"></i> Proses Validasi Selesai!</h5>
                <ul>
                    <li class="text-success"><strong>{{ $summary['success'] }} baris data valid.</strong></li>
                    <li class="text-danger"><strong>{{ count($summary['errors']) }} baris data tidak valid.</strong></li>
                </ul>

                @if(count($summary['errors']) > 0)
                    <hr>
                    <p><strong>Detail Kesalahan:</strong></p>
                    <div style="max-height: 200px; overflow-y: auto; background-color: #fff; padding: 10px; border-radius: 5px;">
                        <ul class="list-unstyled mb-0">
                            @foreach($summary['errors'] as $error)
                                <li><small><i class="fas fa-times-circle text-danger"></i> {{ $error }}</small></li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            {{-- Kalau tidak ada error, tampilkan tombol lanjut simpan --}}
            @if(count($summary['errors']) === 0 && isset($summary['temp_file']))
                <form action="{{ route('warga.import.save') }}" method="POST" class="mb-3">
                    @csrf
                    <input type="hidden" name="temp_file" value="{{ $summary['temp_file'] }}">
                    <input type="hidden" name="rw_id" value="{{ old('rw_id') }}">
                    <input type="hidden" name="rt_id" value="{{ old('rt_id') }}">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Lanjut Simpan ke Database
                    </button>
                </form>
            @endif
        @endif

        {{-- Petunjuk penting --}}
        <div class="alert alert-info">
            <h5><i class="icon fas fa-info"></i> Petunjuk Penting!</h5>
            <ol>
                <li>Unduh template Excel yang sudah disediakan untuk memastikan format data sudah benar.</li>
                <li>Isi data warga sesuai dengan kolom yang ada di template. Jangan mengubah nama header.</li>
                <li>Pastikan kolom <strong>NO_KK</strong>, <strong>NIK</strong>, <strong>NAMA_LENGKAP</strong>,
                    <strong>HUBUNGAN_KELUARGA</strong>, <strong>ALAMAT_LENGKAP</strong> (untuk Kepala Keluarga),
                    <strong>RT</strong>, dan <strong>RW</strong> sudah terisi dengan benar.</li>
                <li>Simpan file dalam format <strong>.xlsx</strong> atau <strong>.csv</strong>.</li>
            </ol>
            <a href="{{ route('warga.import.template') }}" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Unduh Template Impor
            </a>
        </div>

        {{-- Form upload untuk validasi --}}
        <form action="{{ route('warga.import.process') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Pilih RW --}}
            <div class="form-group">
                <label for="rw_id">RW</label>
                <select name="rw_id" class="form-control @error('rw_id') is-invalid @enderror" id="rw_id" required>
                    <option value="">-- Pilih RW --</option>
                    @foreach($rws as $rw)
                        <option value="{{ $rw->id }}" {{ old('rw_id') == $rw->id ? 'selected' : '' }}>
                            RW {{ $rw->nomor_rw }}
                        </option>
                    @endforeach
                </select>
                @error('rw_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            {{-- Pilih RT --}}
            <div class="form-group">
                <label for="rt_id">RT</label>
                <select name="rt_id" class="form-control @error('rt_id') is-invalid @enderror" id="rt_id" required>
                    <option value="">-- Pilih RT --</option>
                </select>
                @error('rt_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            {{-- Upload file --}}
            <div class="form-group">
                <label for="file_warga">Pilih File untuk Diunggah</label>
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input @error('file_warga') is-invalid @enderror"
                               id="file_warga" name="file_warga" required>
                        <label class="custom-file-label" for="file_warga">Pilih file...</label>
                    </div>
                </div>
                @error('file_warga') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Mulai Validasi
            </button>
        </form>
    </div>
</div>
@stop

@push('js')
<script>
    $(document).ready(function() {
        // Tampilkan nama file di custom file input
        $('.custom-file-input').on('change', function () {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        const rwSelect = $('#rw_id');
        const rtSelect = $('#rt_id');
        const initialRwId = rwSelect.val();
        const initialRtId = "{{ old('rt_id') }}";

        function loadRts(rwId, selectedRtId = null) {
            rtSelect.empty().append('<option value="">-- Pilih RT --</option>');
            rtSelect.prop('disabled', true);

            if (rwId) {
                $.ajax({
                    url: "{{ route('api.rts-by-rw') }}",
                    type: 'GET',
                    data: { rw_id: rwId },
                    success: function(rts) {
                        rtSelect.html('<option value="">-- Pilih RT --</option>');
                        $.each(rts, function(_, value) {
                            rtSelect.append('<option value="' + value.id + '">RT ' + value.nomor_rt + '</option>');
                        });
                        if (selectedRtId) {
                            rtSelect.val(selectedRtId);
                        }
                        rtSelect.prop('disabled', false);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error loading RTs:", error);
                        alert('Gagal memuat data RT. Silakan coba lagi.');
                        rtSelect.html('<option value="">Gagal memuat RT</option>').prop('disabled', true);
                    }
                });
            } else {
                rtSelect.html('<option value="">-- Pilih RW Terlebih Dahulu --</option>').prop('disabled', true);
            }
        }

        if (initialRwId) {
            loadRts(initialRwId, initialRtId);
        }

        rwSelect.on('change', function() {
            loadRts($(this).val());
        });
    });
</script>
@endpush
