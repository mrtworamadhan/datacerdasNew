@extends('admin.master')

@section('title', 'Daftar Semua Warga - Desa Cerdas')
@section('content_header')
<h1 class="m-0 text-dark">Dasbor Kependudukan</h1>
@stop

@section('content_main')

    {{-- Filter Bulan dan Tahun --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Data</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('warga.index', ['subdomain' => request()->subdomain]) }}" method="GET"
                class="form-inline">
                <div class="form-group mb-2">
                    <label for="bulan" class="mr-2">Bulan</label>
                    <select name="bulan" id="bulan" class="form-control">
                        @for ($b = 1; $b <= 12; $b++)
                            <option value="{{ $b }}" {{ $bulan == $b ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($b)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="form-group mx-sm-3 mb-2">
                    <label for="tahun" class="mr-2">Tahun</label>
                    <select name="tahun" id="tahun" class="form-control">
                        @for ($t = now()->year; $t >= 2020; $t--)
                            <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endfor
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mb-2"><i class="fas fa-filter"></i> Terapkan Filter</button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calendar-alt"></i> Peristiwa Bulan Ini</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Kelahiran <span
                                class="badge badge-info badge-pill">{{ $statistik['peristiwa']['lahir'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Meninggal <span
                                class="badge badge-secondary badge-pill">{{ $statistik['peristiwa']['meninggal'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Pendatang <span
                                class="badge badge-primary badge-pill">{{ $statistik['peristiwa']['datang'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Pindah Keluar <span
                                class="badge badge-warning badge-pill">{{ $statistik['peristiwa']['pindah'] }}</span>
                        </li>
                    </ul>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('laporan.kependudukan.index', ['subdomain' => request()->subdomain, 'bulan' => $bulan, 'tahun' => $tahun]) }}"
                        class="btn btn-sm btn-info">
                        Lihat Laporan Lengkap <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-venus-mars"></i> Jenis Kelamin</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Laki-laki
                            <span
                                class="badge badge-success badge-pill">{{ $statistik['jenis_kelamin']['laki_laki'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Perempuan
                            <span
                                class="badge badge-success badge-pill">{{ $statistik['jenis_kelamin']['perempuan'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Total Warga Aktif</strong>
                            <strong>{{ $statistik['jenis_kelamin']['laki_laki'] + $statistik['jenis_kelamin']['perempuan'] }}</strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card card-warning card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-bar"></i> Kelompok Usia</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Balita (0-5 Thn)
                            <span class="badge badge-warning badge-pill">{{ $statistik['kelompok_usia']['balita'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Anak (5-12 Thn)
                            <span class="badge badge-warning badge-pill">{{ $statistik['kelompok_usia']['anak'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Remaja (12-17 Thn)
                            <span class="badge badge-warning badge-pill">{{ $statistik['kelompok_usia']['remaja'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Dewasa (17-40 Thn)
                            <span class="badge badge-warning badge-pill">{{ $statistik['kelompok_usia']['dewasa'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Pra-Lansia (40-60 Thn)
                            <span
                                class="badge badge-warning badge-pill">{{ $statistik['kelompok_usia']['pralansia'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Lansia (60+ Thn)
                            <span class="badge badge-warning badge-pill">{{ $statistik['kelompok_usia']['lansia'] }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card card-danger card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-heart"></i> Status Khusus</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a
                                href="{{ route('laporan.status-khusus.index', ['subdomain' => request()->subdomain, 'jenis' => 'janda']) }}">Janda</a>
                            <span class="badge badge-danger badge-pill">{{ $statistik['status_khusus']['janda'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a
                                href="{{ route('laporan.status-khusus.index', ['subdomain' => request()->subdomain, 'jenis' => 'yatim']) }}">Yatim</a>
                            <span class="badge badge-danger badge-pill">{{ $statistik['status_khusus']['yatim'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a
                                href="{{ route('laporan.status-khusus.index', ['subdomain' => request()->subdomain, 'jenis' => 'piatu']) }}">Piatu</a>
                            <span class="badge badge-danger badge-pill">{{ $statistik['status_khusus']['piatu'] }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Daftar Warga (Tetap Ada) --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Semua Warga Aktif</h3>
            <div class="card-tools">
                <form action="{{ route('warga.index', ['subdomain' => request()->subdomain]) }}" method="GET">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" name="search" class="form-control float-right" placeholder="Cari Nama / NIK..."
                            value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nama Lengkap</th>
                        <th>NIK</th>
                        <th>No. KK</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($wargas as $warga)
                        <tr>
                            <td>{{ $warga->nama_lengkap }}</td>
                            <td>{{ $warga->nik }}</td>
                            <td>{{ $warga->kartuKeluarga->nomor_kk ?? '-' }}</td>
                            <td>RW {{ $warga->rw->nomor_rw ?? '-' }} / RT {{ $warga->rt->nomor_rt ?? '-' }}</td>
                            <td>
                                <a href="{{ route('warga.show', ['subdomain' => request()->subdomain, 'warga' => $warga->id]) }}"
                                    class="btn btn-info btn-xs"><i class="fas fa-eye"></i> Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Data warga tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $wargas->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
    </div>

@endsection