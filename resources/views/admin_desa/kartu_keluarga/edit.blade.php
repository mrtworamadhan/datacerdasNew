@extends('admin.master')

@section('title', 'Edit Kartu Keluarga - DataCerdas')

@section('content_header')
    <h1 class="m-0 text-dark">Edit Kartu Keluarga</h1>
@stop

@section('content_main')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Edit Kartu Keluarga</h3>
        </div>
        <form action="{{ route('kartu-keluarga.update', $kartuKeluarga) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <h4>Data Kartu Keluarga</h4>
                <div class="form-group">
                    <label for="nomor_kk">Nomor KK</label>
                    <input type="text" name="nomor_kk" class="form-control @error('nomor_kk') is-invalid @enderror" id="nomor_kk" value="{{ old('nomor_kk', $kartuKeluarga->nomor_kk) }}" required>
                    @error('nomor_kk') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="rw_id">RW</label>
                    <select name="rw_id" class="form-control @error('rw_id') is-invalid @enderror" id="rw_id" required>
                        <option value="">-- Pilih RW --</option>
                        @foreach($rws as $rw)
                            <option value="{{ $rw->id }}" {{ old('rw_id', $kartuKeluarga->rw_id) == $rw->id ? 'selected' : '' }}>RW {{ $rw->nomor_rw }}</option>
                        @endforeach
                    </select>
                    @error('rw_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="rt_id">RT</label>
                    <select name="rt_id" class="form-control @error('rt_id') is-invalid @enderror" id="rt_id" required>
                        <option value="">-- Pilih RT --</option>
                        {{-- Opsi RT akan dimuat dinamis oleh JavaScript --}}
                    </select>
                    @error('rt_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="alamat_lengkap_kk">Alamat Lengkap KK</label>
                    <textarea name="alamat_lengkap_kk" class="form-control @error('alamat_lengkap_kk') is-invalid @enderror" id="alamat_lengkap_kk" rows="3" required>{{ old('alamat_lengkap_kk', $kartuKeluarga->alamat_lengkap) }}</textarea>
                    @error('alamat_lengkap_kk') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="klasifikasi">Klasifikasi Keluarga</label>
                    <select name="klasifikasi" class="form-control @error('klasifikasi') is-invalid @enderror" id="klasifikasi" required>
                        <option value="">-- Pilih Klasifikasi --</option>
                        @foreach($klasifikasiOptions as $option)
                            <option value="{{ $option }}" {{ old('klasifikasi', $kartuKeluarga->klasifikasi) == $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                    @error('klasifikasi') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <hr>
                <h4>Kepala Keluarga Saat Ini</h4>
                <div class="form-group">
                    <label>Nama Kepala Keluarga:</label>
                    <p>{{ $kartuKeluarga->kepalaKeluarga->nama_lengkap ?? 'Belum ditentukan' }}</p>
                    <small class="form-text text-muted">Untuk mengubah data Kepala Keluarga atau anggota lainnya, silakan masuk ke menu Manajemen Anggota Keluarga.</small>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Update Kartu Keluarga</button>
                <a href="{{ route('kartu-keluarga.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection


@section('js')
    {{-- Select2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi Select2 untuk dropdown pekerjaan (jika ada di halaman ini)
            $('#pekerjaan_kk').select2({
                theme: 'bootstrap4',
                placeholder: "-- Pilih Pekerjaan --",
                allowClear: true // Opsi untuk menghapus pilihan
            });

            // --- Logika Dinamis Dropdown RT berdasarkan RW ---
            const rwSelect = $('#rw_id');
            const rtSelect = $('#rt_id');
            const initialRwId = rwSelect.val(); // RW yang terpilih saat ini
            const initialRtId = "{{ old('rt_id', $kartuKeluarga->rt_id) }}"; // RT yang terpilih saat ini

            function loadRts(rwId, selectedRtId = null) {
                rtSelect.empty().append('<option value="">-- Pilih RT --</option>'); // Kosongkan dan tambahkan default

                if (rwId) {
                    $.ajax({
                        url: "{{ route('api.rts-by-rw') }}", // Rute API baru yang akan kita buat
                        type: 'GET',
                        data: { rw_id: rwId },
                        success: function(rts) {
                            $.each(rts, function(key, value) {
                                rtSelect.append('<option value="' + value.id + '">' + 'RT ' + value.nomor_rt + '</option>');
                            });
                            // Pilih RT yang sebelumnya terpilih (untuk mode edit)
                            if (selectedRtId) {
                                rtSelect.val(selectedRtId);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error loading RTs:", error);
                            alert('Gagal memuat data RT. Silakan coba lagi.');
                        }
                    });
                }
            }

            // Panggil saat halaman pertama kali dimuat (untuk mengisi RT berdasarkan RW yang sudah ada)
            if (initialRwId) {
                loadRts(initialRwId, initialRtId);
            }

            // Panggil saat pilihan RW berubah
            rwSelect.on('change', function() {
                loadRts($(this).val()); // Panggil tanpa selectedRtId, karena ini perubahan manual
            });
        });
    </script>
@stop
