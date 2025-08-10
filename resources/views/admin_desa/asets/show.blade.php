@extends('admin.master')
@section('title', 'Detail Aset')
@section('content_header')<h1 class="m-0 text-dark">Detail Aset: {{ $aset->nama_aset }}</h1>@stop

@section('content_main')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <tr><th style="width: 30%;">Kode Aset</th><td>{{ $aset->kode_aset }}</td></tr>
                    <tr><th>Nama Aset</th><td>{{ $aset->nama_aset }}</td></tr>
                    <tr><th>Kategori</th><td>{{ $aset->subSubKelompok->subKelompok->kelompok->bidang->golongan->nama_golongan }} > {{ $aset->subSubKelompok->subKelompok->kelompok->bidang->nama_bidang }} > {{ $aset->subSubKelompok->subKelompok->kelompok->nama_kelompok }} > {{ $aset->subSubKelompok->subKelompok->nama_sub_kelompok }} > <strong>{{ $aset->subSubKelompok->nama_sub_sub_kelompok }}</strong></td></tr>
                    <tr><th>Tahun Perolehan</th><td>{{ $aset->tahun_perolehan }}</td></tr>
                    <tr><th>Nilai Perolehan</th><td>Rp {{ number_format($aset->nilai_perolehan, 2, ',', '.') }}</td></tr>
                    <tr><th>Jumlah</th><td>{{ $aset->jumlah }}</td></tr>
                    <tr><th>Kondisi</th><td>{{ $aset->kondisi }}</td></tr>
                    <tr><th>Sumber Dana</th><td>{{ $aset->sumber_dana ?? '-' }}</td></tr>
                    <tr><th>Lokasi</th><td>{{ $aset->lokasi ?? '-' }}</td></tr>
                    <tr><th>Penanggung Jawab</th><td>{{ $aset->penanggung_jawab ?? '-' }}</td></tr>
                    <tr><th>Keterangan</th><td>{{ $aset->keterangan ?? '-' }}</td></tr>
                </table>
            </div>
            <div class="card-footer">
                <a href="{{ route('asets.index') }}" class="btn btn-secondary">Kembali ke Daftar Aset</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Foto Aset</h3></div>
            <div class="card-body">
                @if($aset->foto_aset)
                    <img src="{{ Storage::url($aset->foto_aset) }}" class="img-fluid" alt="Foto Aset">
                @else
                    <p class="text-center">Tidak ada foto.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@stop