@extends('admin.master')
@section('title', 'Worksheet Kesehatan Anak')
@section('content_header')<h1 class="m-0 text-dark">Worksheet Kesehatan Anak (Balita)</h1>@stop
@section('content_main')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Statistik Usia</h3></div>
            <div class="card-body row">
                <div class="col-lg-3 col-6"><div class="small-box bg-primary"><div class="inner"><h3>{{ $stats['total_balita'] }}</h3><p>Total Balita</p></div><div class="icon"><i class="fas fa-baby"></i></div></div></div>
                <div class="col-lg-3 col-6"><div class="small-box bg-info"><div class="inner"><h3>{{ $stats['usia_0_12'] }}</h3><p>Usia 0-12 bln</p></div><div class="icon"><i class="fas fa-child"></i></div></div></div>
                <div class="col-lg-3 col-6"><div class="small-box bg-info"><div class="inner"><h3>{{ $stats['usia_13_36'] }}</h3><p>Usia 13-36 bln</p></div><div class="icon"><i class="fas fa-child"></i></div></div></div>
                <div class="col-lg-3 col-6"><div class="small-box bg-info"><div class="inner"><h3>{{ $stats['usia_37_60'] }}</h3><p>Usia 37-60 bln</p></div><div class="icon"><i class="fas fa-child"></i></div></div></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Statistik Gizi Terakhir</h3></div>
            <div class="card-body row">
                <div class="col-lg-3 col-6"><div class="small-box bg-success"><div class="inner"><h3>{{ $stats['gizi_baik'] }}</h3><p>Gizi Baik</p></div><div class="icon"><i class="fas fa-smile"></i></div></div></div>
                <div class="col-lg-3 col-6"><div class="small-box bg-warning"><div class="inner"><h3>{{ $stats['gizi_kurang'] }}</h3><p>Gizi Kurang</p></div><div class="icon"><i class="fas fa-frown"></i></div></div></div>
                <div class="col-lg-3 col-6"><div class="small-box bg-danger"><div class="inner"><h3>{{ $stats['bgm'] }}</h3><p>BGM</p></div><div class="icon"><i class="fas fa-exclamation-triangle"></i></div></div></div>
            </div>
        </div>
    </div>
</div>
{{-- Filter Usia --}}
<div class="card">
    <div class="card-body">
        <form action="{{ route('kesehatan-anak.index') }}" method="GET" class="form-inline">
            <label class="mr-2">Filter Usia:</label>
            {{-- PERUBAHAN: Opsi filter baru --}}
            <select name="usia" class="form-control mr-2" onchange="this.form.submit()">
                <option value="">Semua Balita (0-60 bln)</option>
                <option value="0-12" {{ request('usia') == '0-12' ? 'selected' : '' }}>0 - 12 Bulan</option>
                <option value="13-36" {{ request('usia') == '13-36' ? 'selected' : '' }}>13 - 36 Bulan</option>
                <option value="37-60" {{ request('usia') == '37-60' ? 'selected' : '' }}>37 - 60 Bulan</option>
            </select>
            <a href="{{ route('kesehatan-anak.index') }}" class="btn btn-secondary">Reset</a>
        </form>
    </div>
</div>

{{-- Tabel 1: Anak Baru --}}
<form action="{{ route('kesehatan-anak.store') }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-header bg-info">
            <h3 class="card-title">Anak Baru (Belum Masuk Pemantauan)</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="check-all-baru"></th>
                        <th>No.</th>
                        <th>Nama Anak</th>
                        <th>Usia</th>
                        <th>Nama Ibu Kandung</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($anakBaru as $warga)
                    <tr>
                        <td><input type="checkbox" name="warga_ids[]" class="check-item-baru" value="{{ $warga->id }}"></td>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <strong>{{ $warga->nama_lengkap }}</strong><br>
                            <small>NIK: {{ $warga->nik ?? 'Belum Ada' }} | No. KK: {{ $warga->kartuKeluarga->nomor_kk ?? 'N/A' }}</small>
                        </td>
                        <td>
                            @php
                                $age = \Carbon\Carbon::parse($warga->tanggal_lahir)->diff(now());
                                $usiaBulan = $age->y * 12 + $age->m;
                                $usiaHari = $age->d;
                            @endphp
                            {{ $usiaBulan }} bulan, {{ $usiaHari }} hari
                        </td>
                        <td>{{ $warga->nama_ibu_kandung ?? 'N/A' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center">Tidak ada data anak baru yang sesuai filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($anakBaru->isNotEmpty())
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Tambahkan yang Dipilih ke Pemantauan</button>
        </div>
        @endif
    </div>
</form>

{{-- Tabel 2: Anak Terpantau --}}
<div class="card mt-4">
    <div class="card-header bg-success">
        <h3 class="card-title">Anak dalam Pemantauan</h3>
        {{-- PERUBAHAN: Form Pencarian --}}
        <div class="card-tools">
            <form action="{{ route('kesehatan-anak.index') }}" method="GET">
                <div class="input-group input-group-sm" style="width: 300px;">
                    <input type="text" name="search" class="form-control float-right" placeholder="Cari NIK, Nama Anak/Ibu..." value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-sm table-hover">
            <thead><tr><th><input type="checkbox" id="check-all-terpantau"></th><th>Nama Anak</th><th>Usia</th><th>BB/TB Terakhir</th><th>Status Gizi</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse ($anakTerpantau as $data)
                <tr>
                    <td><input type="checkbox" class="check-item-terpantau" value="{{ $data->id }}"></td>
                    <td>
                        <strong>{{ $data->warga->nama_lengkap ?? 'N/A' }}</strong><br>
                        <small>NIK: {{ $data->warga->nik ?? 'Belum Ada' }} | No. KK: {{ $data->warga->kartuKeluarga->nomor_kk ?? 'N/A' }}</small>
                    </td>
                    <td>
                        @php
                            $age = \Carbon\Carbon::parse($data->warga->tanggal_lahir)->diff(now());
                            $usiaBulan = $age->y * 12 + $age->m;
                            $usiaHari = $age->d;
                        @endphp
                        {{ $usiaBulan }} bulan, {{ $usiaHari }} hari
                    </td>
                    <td>-</td> {{-- Nanti diisi dari riwayat terakhir --}}
                    <td>-</td> {{-- Nanti diisi dari riwayat terakhir --}}
                    <td>
                        <a href="{{ route('kesehatan-anak.show', $data) }}" class="btn btn-xs btn-info" title="Input & Lihat Riwayat">
                            <i class="fas fa-file-medical-alt"></i> Riwayat
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">Tidak ada anak dalam pemantauan yang cocok dengan pencarian.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">{{ $anakTerpantau->appends(request()->query())->links() }}</div>
    </div>
</div>
@stop

@push('js')
<script>
$(document).ready(function() {
    // Fungsi untuk checkbox "Pilih Semua" di tabel Anak Baru
    $('#check-all-baru').on('click', function() {
        $('.check-item-baru').prop('checked', $(this).prop('checked'));
    });

    // Fungsi untuk checkbox "Pilih Semua" di tabel Anak Terpantau
    $('#check-all-terpantau').on('click', function() {
        $('.check-item-terpantau').prop('checked', $(this).prop('checked'));
    });
});
</script>
@endpush
