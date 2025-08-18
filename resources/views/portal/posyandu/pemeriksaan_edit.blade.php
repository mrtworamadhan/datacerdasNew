@extends('layouts.portal')
@section('title', 'Edit Pemeriksaan Anak')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header">
            {{-- Mengambil nama anak dari relasi yang berlapis --}}
            <h4 class="card-title mb-0">Edit Pemeriksaan: <strong>{{ optional($pemeriksaan->dataAnak)->warga->nama_lengkap ?? 'Data Anak Tidak Ditemukan' }}</strong></h4>
            <small class="text-muted">Pemeriksaan tanggal: {{ $pemeriksaan->tanggal_pemeriksaan->isoFormat('D MMMM YYYY') }}</small>
        </div>
        <div class="card-body">
            {{-- 1. Action form diubah ke route 'update', dengan parameter 'pemeriksaan' --}}
            <form action="{{ route('portal.posyandu.pemeriksaan.update', ['subdomain' => $subdomain, 'pemeriksaan' => $pemeriksaan->id]) }}" method="POST">
                @csrf
                @method('PUT') {{-- 2. Method diubah menjadi PUT untuk proses update --}}

                {{-- Input Tanggal Pemeriksaan --}}
                <div class="mb-3">
                    <label for="tanggal_pemeriksaan" class="form-label">Tanggal Pemeriksaan</label>
                    {{-- 3. Value diisi dengan data lama, diformat ke Y-m-d --}}
                    <input type="date" name="tanggal_pemeriksaan" class="form-control" value="{{ old('tanggal_pemeriksaan', $pemeriksaan->tanggal_pemeriksaan->format('Y-m-d')) }}" required>
                </div>

                {{-- Pengukuran Fisik --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="berat_badan" class="form-label">Berat Badan (kg)</label>
                        {{-- 4. Value diisi dengan data lama dari $pemeriksaan --}}
                        <input type="number" step="0.1" name="berat_badan" id="berat_badan" class="form-control" value="{{ old('berat_badan', $pemeriksaan->berat_badan) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tinggi_badan" class="form-label">Tinggi/Panjang Badan (cm)</label>
                        <input type="number" step="0.1" name="tinggi_badan" id="tinggi_badan" class="form-control" value="{{ old('tinggi_badan', $pemeriksaan->tinggi_badan) }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="lila" class="form-label">Lingkar Kepala (cm)</label>
                        <input type="number" step="0.1" name="lila" id="lila" class="form-control" value="{{ old('lila', $pemeriksaan->lingkar_kepala) }}" placeholder="(Opsional)">
                    </div>
                </div>

                {{-- Intervensi --}}
                <div class="mb-3">
                    <label class="form-label">Intervensi yang Diberikan:</label>
                    <div class="form-check">
                        {{-- 5. Untuk checkbox, kita gunakan kondisi 'checked' --}}
                        <input class="form-check-input" type="checkbox" name="dapat_vitamin_a" value="1" id="dapat_vitamin_a" @if(old('dapat_vitamin_a', $pemeriksaan->dapat_vitamin_a)) checked @endif>
                        <label class="form-check-label" for="dapat_vitamin_a">Vitamin A</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="dapat_imunisasi_polio" value="1" id="dapat_imunisasi_polio" @if(old('dapat_imunisasi_polio', $pemeriksaan->dapat_imunisasi_polio)) checked @endif>
                        <label class="form-check-label" for="dapat_imunisasi_polio">Imunisasi Polio</label>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="mb-3">
                    <label for="catatan" class="form-label">Catatan Tambahan</label>
                    {{-- 6. Untuk textarea, value ditaruh di antara tag --}}
                    <textarea name="catatan" id="catatan" class="form-control" rows="3" placeholder="(Opsional)">{{ old('catatan', $pemeriksaan->catatan) }}</textarea>
                </div>

                {{-- Tombol Aksi --}}
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-sync-alt"></i> Perbarui Data</button>
                    <a href="javascript:history.back()" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
