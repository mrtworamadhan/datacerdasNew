@extends('admin.master')
@section('title', 'Edit LPJ Kegiatan')
@section('content_header')
    <h1 class="m-0 text-dark">Edit Laporan Pertanggungjawaban (LPJ)</h1>
    <small>Untuk Kegiatan: {{ $kegiatan->nama_kegiatan }}</small>
@stop

@section('content_main')
<form action="{{ route('lpjs.update', $lpj->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="card card-warning card-outline">
        <div class="card-header"><h3 class="card-title">Edit Detail Laporan</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label for="hasil_kegiatan">Hasil Kegiatan</label>
                <div class="input-group">
                    <textarea name="hasil_kegiatan" id="hasil_kegiatan" class="form-control @error('hasil_kegiatan') is-invalid @enderror" rows="5" required>{{ old('hasil_kegiatan', $lpj->hasil_kegiatan) }}</textarea>
                </div>
                @error('hasil_kegiatan') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="evaluasi_kendala">Evaluasi & Kendala</label>
                <div class="input-group">
                    <textarea name="evaluasi_kendala" id="evaluasi_kendala" class="form-control" rows="4">{{ old('evaluasi_kendala', $lpj->evaluasi_kendala) }}</textarea>
                </div>
            </div>

            <div class="form-group">
                <label for="rekomendasi_lanjutan">Rekomendasi / Tindak Lanjut</label>
                <div class="input-group">
                    <textarea name="rekomendasi_lanjutan" id="rekomendasi_lanjutan" class="form-control" rows="3">{{ old('rekomendasi_lanjutan', $lpj->rekomendasi_lanjutan) }}</textarea>
                </div>
            </div>

            <div class="form-group">
                <label for="tanggal_pelaporan">Tanggal Pelaporan</label>
                <input type="date" name="tanggal_pelaporan" class="form-control" value="{{ old('tanggal_pelaporan', ($lpj->tanggal_pelaporan ?? now())->format('Y-m-d')) }}" required>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-warning">Update LPJ</button>
            <a href="{{ route('kegiatans.show', $kegiatan->id) }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@stop