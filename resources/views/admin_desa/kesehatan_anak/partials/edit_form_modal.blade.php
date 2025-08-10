{{-- Ini adalah isi dari Modal kita yang sudah lengkap --}}
<form action="{{ route('pemeriksaan-anak.update', $pemeriksaan) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Edit Pemeriksaan (Tgl: {{ $pemeriksaan->tanggal_pemeriksaan->format('d M Y') }})</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        {{-- Grup Antropometri --}}
        <div class="row">
            <div class="col-md-6 form-group">
                <label for="berat_badan">Berat Badan (kg)</label>
                <input type="number" step="0.1" name="berat_badan" class="form-control" value="{{ old('berat_badan', $pemeriksaan->berat_badan) }}" required>
            </div>
            <div class="col-md-6 form-group">
                <label for="tinggi_badan">Tinggi Badan (cm)</label>
                <input type="number" step="0.1" name="tinggi_badan" class="form-control" value="{{ old('tinggi_badan', $pemeriksaan->tinggi_badan) }}" required>
            </div>
        </div>
        <div class="form-group">
            <label for="lila">Lingkar Lengan Atas (LILA) (cm)</label>
            <input type="number" step="0.1" name="lila" class="form-control" value="{{ old('lila', $pemeriksaan->lila) }}">
        </div>

        <hr>
        {{-- Grup Intervensi --}}
        <label>Intervensi yang Diberikan:</label>
        <div class="form-group">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="edit_dapat_vitamin_a" name="dapat_vitamin_a" value="1" {{ old('dapat_vitamin_a', $pemeriksaan->dapat_vitamin_a) ? 'checked' : '' }}>
                <label class="custom-control-label" for="edit_dapat_vitamin_a">Diberi Vitamin A</label>
            </div>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="edit_dapat_obat_cacing" name="dapat_obat_cacing" value="1" {{ old('dapat_obat_cacing', $pemeriksaan->dapat_obat_cacing) ? 'checked' : '' }}>
                <label class="custom-control-label" for="edit_dapat_obat_cacing">Diberi Obat Cacing</label>
            </div>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="edit_dapat_imunisasi_polio" name="dapat_imunisasi_polio" value="1" {{ old('dapat_imunisasi_polio', $pemeriksaan->dapat_imunisasi_polio) ? 'checked' : '' }}>
                <label class="custom-control-label" for="edit_dapat_imunisasi_polio">Diberi Imunisasi Polio</label>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-warning">Update Data</button>
    </div>
</form>