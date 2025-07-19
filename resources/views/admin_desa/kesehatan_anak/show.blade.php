@extends('admin.master')
@section('title', 'Riwayat Kesehatan Anak')
@section('content_header')<h1 class="m-0 text-dark">Riwayat Kesehatan: {{ $kesehatanAnak->warga->nama_lengkap }}</h1>@stop

@section('content_main')
<div class="row">
    {{-- Kolom Kiri: Form Input Pemeriksaan Baru --}}
    <div class="col-md-5">
        <form action="{{ route('pemeriksaan-anak.store', $kesehatanAnak) }}" method="POST">
            @csrf
            <div class="card card-purple card-outline">
                <div class="card-header"><h3 class="card-title">Input Pemeriksaan Baru</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="tanggal_pemeriksaan">Tanggal Pemeriksaan</label>
                        <input type="date" name="tanggal_pemeriksaan" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <label for="berat_badan">Berat Badan (kg)</label>
                            <input type="number" step="0.1" name="berat_badan" class="form-control" required>
                        </div>
                        <div class="col-6 form-group">
                            <label for="tinggi_badan">Tinggi Badan (cm)</label>
                            <input type="number" step="0.1" name="tinggi_badan" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="status_gizi">Status Gizi</label>
                        <select name="status_gizi" class="form-control" required>
                            <option value="Naik">Naik</option>
                            <option value="Tetap">Tetap</option>
                            <option value="Turun">Turun</option>
                            <option value="BGM">BGM (Bawah Garis Merah)</option>
                        </select>
                    </div>
                     <div class="form-group">
                        <label for="imunisasi_diterima">Imunisasi yang Diberikan (Opsional)</label>
                        <input type="text" name="imunisasi_diterima" class="form-control" placeholder="Contoh: Polio 2">
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="vitamin_a" name="vitamin_a_diterima" value="1">
                            <label class="custom-control-label" for="vitamin_a">Diberi Vitamin A</label>
                        </div>
                         <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="obat_cacing" name="obat_cacing_diterima" value="1">
                            <label class="custom-control-label" for="obat_cacing">Diberi Obat Cacing</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="catatan_kader">Catatan Kader (Opsional)</label>
                        <textarea name="catatan_kader" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">Simpan Pemeriksaan</button>
                </div>
            </div>
        </form>
    </div>

    {{-- Kolom Kanan: Detail Anak & Riwayat Pemeriksaan --}}
    <div class="col-md-7">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Data Diri Anak</h3></div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr><th style="width: 30%">Nama</th><td>: {{ $kesehatanAnak->warga->nama_lengkap }}</td></tr>
                    <tr><th>NIK</th><td>: {{ $kesehatanAnak->warga->nik ?? 'Belum Ada' }}</td></tr>
                    <tr><th>Usia</th><td>: {{ \Carbon\Carbon::parse($kesehatanAnak->tanggal_lahir)->diff(now())->format('%y tahun, %m bulan, %d hari') }}</td></tr>
                    <tr><th>Nama Ibu</th><td>: {{ $kesehatanAnak->nama_ibu }}</td></tr>
                    <tr><th>Nama Ayah</th><td>: {{ $kesehatanAnak->nama_ayah }}</td></tr>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="card-title">Riwayat Pemeriksaan Terdahulu</h3></div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped table-valign-middle">
                    <thead><tr><th>Tgl Periksa</th><th>Usia</th><th>BB (kg)</th><th>TB (cm)</th><th>Status Gizi</th></tr></thead>
                    <tbody>
                        @forelse($kesehatanAnak->riwayatPemeriksaan()->latest('tanggal_pemeriksaan')->get() as $pemeriksaan)
                        <tr>
                            <td>{{ $pemeriksaan->tanggal_pemeriksaan->translatedFormat('d M Y') }}</td>
                            <td>{{ $pemeriksaan->usia_saat_periksa }} bln</td>
                            <td>{{ $pemeriksaan->berat_badan }}</td>
                            <td>{{ $pemeriksaan->tinggi_badan }}</td>
                            <td>
                                @if($pemeriksaan->status_gizi == 'Naik') <span class="badge badge-success">Naik</span>
                                @elseif($pemeriksaan->status_gizi == 'Tetap') <span class="badge badge-secondary">Tetap</span>
                                @elseif($pemeriksaan->status_gizi == 'Turun') <span class="badge badge-warning">Turun</span>
                                @else <span class="badge badge-danger">BGM</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center">Belum ada riwayat pemeriksaan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             <div class="card-footer">
                <a href="{{ route('kesehatan-anak.index') }}" class="btn btn-secondary">Kembali ke Worksheet</a>
            </div>
        </div>
    </div>
</div>
@stop
