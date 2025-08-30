<hr>
<h5 class="mt-4">Edit Status untuk: {{ $warga->nama_lengkap }}</h5>
<form action="{{ route('portal.warga.updateStatus', ['subdomain' => app('tenant')->subdomain, 'warga' => $warga->id]) }}"
    method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label for="status_kependudukan_id">Status Kependudukan</label>
        <select name="status_kependudukan_id" id="status_kependudukan_id" class="form-control mb-3" required>
            @foreach ($semuaStatus as $status)
                <option value="{{ $status->id }}" {{ $warga->status_kependudukan_id == $status->id ? 'selected' : '' }}>
                    {{ $status->nama }}
                </option>
            @endforeach
        </select>

        @foreach($statusKhususOptions as $option)
            <input class="form-check-input" type="checkbox" name="status_khusus[]" value="{{ $option }}"
                id="status_khusus_{{ Str::slug($option) }}" {{ in_array($option, old('status_khusus', $warga->status_khusus ?? [])) ? 'checked' : '' }}>
            <label class="form-check-label" for="status_khusus_{{ Str::slug($option) }}">
                {{ $option }}
            </label><br>
        @endforeach
    </div>

    <div class="d-grid">
        <button type="submit" class="btn btn-warning">Update Status Warga</button>
    </div>
</form>