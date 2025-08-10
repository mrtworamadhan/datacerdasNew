@extends('layouts.portal')
@section('title', 'Input Pemeriksaan')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Pemeriksaan untuk: {{ $anak->warga->nama_lengkap }}</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('portal.posyandu.pemeriksaan.store', ['subdomain' => app('tenant')->subdomain]) }}" method="POST">
                @csrf
                <input type="hidden" name="data_kesehatan_anak_id" value="{{ $anak->id }}">

                {{-- Di sini kita letakkan semua field input yang ada di pemeriksaan.blade.php lama --}}
                {{-- Contoh: --}}
                <div class="mb-3">
                    <label for="tanggal_pemeriksaan" class="form-label">Tanggal Pemeriksaan</label>
                    <input type="date" name="tanggal_pemeriksaan" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="berat_badan" class="form-label">Berat Badan (kg)</label>
                        <input type="number" step="0.1" name="berat_badan" id="berat_badan" class="form-control" placeholder="Contoh: 8.5" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tinggi_badan" class="form-label">Tinggi/Panjang Badan (cm)</label>
                        <input type="number" step="0.1" name="tinggi_badan" id="tinggi_badan" class="form-control" placeholder="Contoh: 72.5" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="lingkar_lengan_atas" class="form-label">Lingkar Lengan Atas (cm)</label>
                        <input type="number" step="0.1" name="lingkar_lengan_atas" id="lingkar_lengan_atas" class="form-control" placeholder="(Opsional)">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="lingkar_kepala" class="form-label">Lingkar Kepala (cm)</label>
                        <input type="number" step="0.1" name="lingkar_kepala" id="lingkar_kepala" class="form-control" placeholder="(Opsional)">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Intervensi yang Diberikan:</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="dapat_vitamin_a" value="1" id="dapat_vitamin_a">
                        <label class="form-check-label" for="dapat_vitamin_a">Vitamin A</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="dapat_imunisasi_polio" value="1" id="dapat_imunisasi_polio">
                        <label class="form-check-label" for="dapat_imunisasi_polio">Imunisasi Polio</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="catatan" class="form-label">Catatan Tambahan</label>
                    <textarea name="catatan" id="catatan" class="form-control" rows="3" placeholder="(Opsional)"></textarea>
                </div>

                <div class="d-grid gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                    <a href="javascript:history.back()" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop