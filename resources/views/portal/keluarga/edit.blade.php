<hr>
<h5 class="mt-4">Edit Status untuk: {{ $kartuKeluarga->nomor_kk }} - {{$kartuKeluarga->kepalaKeluarga->nama_lengkap}}
</h5>
<form action="{{ route('portal.kartuKeluarga.update', ['subdomain' => app('tenant')->subdomain, 'kartuKeluarga' => $kartuKeluarga->id]) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="card-body">
        <h4>Data Kartu Keluarga</h4>
        <div class="form-group">
            <label for="nomor_kk">Nomor KK</label>
            <input type="text" name="nomor_kk" class="form-control @error('nomor_kk') is-invalid @enderror"
                id="nomor_kk" value="{{ old('nomor_kk', $kartuKeluarga->nomor_kk) }}" required>
            @error('nomor_kk') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
        <div class="form-group">
            <label for="alamat_lengkap_kk">Alamat Lengkap KK</label>
            <textarea name="alamat_lengkap_kk" class="form-control @error('alamat_lengkap_kk') is-invalid @enderror"
                id="alamat_lengkap_kk" rows="3"
                required>{{ old('alamat_lengkap_kk', $kartuKeluarga->alamat_lengkap) }}</textarea>
            @error('alamat_lengkap_kk') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
        <div class="form-group">
            <label for="klasifikasi">Klasifikasi Keluarga</label>
            <select name="klasifikasi" class="form-control @error('klasifikasi') is-invalid @enderror" id="klasifikasi"
                required>
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
            <small class="form-text text-muted">Untuk mengubah data Kepala Keluarga atau anggota lainnya, silakan masuk
                ke menu Manajemen Anggota Keluarga.</small>
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-primary">Update Kartu Keluarga</button>
        <a href="{{ route('portal.kartuKeluarga.index') }}" class="btn btn-secondary">Batal</a>
    </div>
</form>