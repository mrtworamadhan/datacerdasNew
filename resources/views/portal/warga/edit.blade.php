<hr>
<h5 class="mt-4">Edit Status untuk: {{ $warga->nama_lengkap }}</h5>
<form action="{{ route('portal.warga.update', ['subdomain' => app('tenant')->subdomain, 'warga' => $warga->id]) }}"
    method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label for="status_kependudukan" class="form-label">Status Kependudukan</label>
        <select name="status_kependudukan" id="status_kependudukan" class="form-select" required>
            <option value="Warga Asli" {{ $warga->status_kependudukan == 'Warga Asli' ? 'selected' : '' }}>Warga Asli
            </option>
            <option value="Pendatang" {{ $warga->status_kependudukan == 'Pendatang' ? 'selected' : '' }}>Pendatang
            </option>
            <option value="Pindah" {{ $warga->status_kependudukan == 'Pindah' ? 'selected' : '' }}>Pindah</option>
            <option value="Meninggal" {{ $warga->status_kependudukan == 'Meninggal' ? 'selected' : '' }}>Meninggal
            </option>
        </select>
    </div>

    {{-- Contoh untuk status khusus, sesuaikan dengan kebutuhanmu --}}
    @foreach($statusKhususOptions as $option)
        <input class="form-check-input" type="checkbox" name="status_khusus[]" value="{{ $option }}"
            id="status_khusus_{{ Str::slug($option) }}" {{ in_array($option, old('status_khusus', $warga->status_khusus ?? [])) ? 'checked' : '' }}>
        <label class="form-check-label" for="status_khusus_{{ Str::slug($option) }}">
            {{ $option }}
        </label><br>
    @endforeach


    <div class="d-grid">
        <button type="submit" class="btn btn-warning">Update Status Warga</button>
    </div>
</form>