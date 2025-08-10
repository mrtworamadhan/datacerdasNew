@extends('admin.master')
@section('title', 'Dashboard Kesehatan Anak')
@section('content_header')
    <h1 class="m-0 text-dark">
        @if ($selectedPosyandu)
            Dashboard Posyandu {{ $selectedPosyandu->nama_posyandu }}
        @else
            Dashboard Kesehatan Anak
        @endif
    </h1>
@stop

@section('content_main')

{{-- KARTU BARU: STATISTIK PARTISIPASI KESELURUHAN --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><strong>Partisipasi Pemantauan Posyandu</strong></h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <strong>Total Balita di Wilayah:</strong>
                <p class="h4">{{ $totalBalitaWilayah }} Anak</p>
            </div>
            <div class="col-md-4">
                <strong>Sudah Dipantau:</strong>
                <p class="h4 text-success">{{ $totalBalitaTerpantau }} Anak ({{ $persenTerpantau }}%)</p>
                <div class="progress">
                    <div class="progress-bar bg-success" style="width: {{ $persenTerpantau }}%"></div>
                </div>
            </div>
            <div class="col-md-4">
                <strong>Belum Dipantau:</strong>
                <p class="h4 text-danger">{{ $totalBalitaBelumTerpantau }} Anak ({{ $persenBelumTerpantau }}%)</p>
                <div class="progress">
                    <div class="progress-bar bg-danger" style="width: {{ $persenBelumTerpantau }}%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- Filter Dropdown untuk Admin --}}
<div class="row">
    {{-- KARTU BARU: STATISTIK PARTISIPASI KESELURUHAN --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('kesehatan-anak.index') }}" method="GET" class="form-inline">
                    <label class="mr-2">Filter Usia:</label>
                    {{-- PERUBAHAN: Opsi filter baru --}}
                    <select name="usia" class="form-control mr-2 mb-2" onchange="this.form.submit()">
                        <option value="">Semua Balita (0-60 bln)</option>
                        <option value="0-12" {{ request('usia') == '0-12' ? 'selected' : '' }}>0 - 12 Bulan</option>
                        <option value="13-36" {{ request('usia') == '13-36' ? 'selected' : '' }}>13 - 36 Bulan</option>
                        <option value="37-60" {{ request('usia') == '37-60' ? 'selected' : '' }}>37 - 60 Bulan</option>
                    </select>
                    <a href="{{ route('kesehatan-anak.index') }}" class="btn btn-secondary">Reset</a>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        @if(Auth::user()->user_type == 'admin_desa')
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('kesehatan-anak.index') }}" method="GET" class="form-inline">
                        <label class="mr-2">Filter Posyandu:</label>
                        <select name="posyandu_id" class="form-control mr-2 mb-2" onchange="this.form.submit()">
                            <option value="">Semua Posyandu</option>
                            @foreach ($posyandus as $p)
                                <option value="{{ $p->id }}" {{ $selectedPosyandu && $selectedPosyandu->id == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_posyandu }} (RW {{ $p->rws->nomor }})
                                </option>
                            @endforeach
                        </select>

                        <label class="mr-2">Tahun:</label>
                        <select name="tahun" class="form-control mr-2" onchange="this.form.submit()">
                            @php
                                $tahunSekarang = now()->year;
                            @endphp
                            @for ($tahun = $tahunSekarang; $tahun >= $tahunSekarang - 4; $tahun--)
                                <option value="{{ $tahun }}" {{ $tahunYangDipilih == $tahun ? 'selected' : '' }}>
                                    {{ $tahun }}
                                </option>
                            @endfor
                        </select>
                        <a href="{{ route('kesehatan-anak.index') }}" class="btn btn-secondary">Reset</a>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>


{{-- BARIS 1: KARTU INDIKATOR KUNCI (KPI) --}}
<div class="row">
    <div class="col-lg-3 col-6"><div class="small-box bg-info"><div class="inner"><h3>{{ $stats['total_balita_terpantau'] }}</h3><p>Total Balita Dipantau</p></div><div class="icon"><i class="fas fa-baby"></i></div></div></div>
    <div class="col-lg-3 col-6"><div class="small-box bg-danger"><div class="inner"><h3>{{ $stats['stunting'] }}</h3><p>Kasus Stunting</p></div><div class="icon"><i class="fas fa-chart-line"></i></div></div></div>
    <div class="col-lg-3 col-6"><div class="small-box bg-warning"><div class="inner"><h3>{{ $stats['wasting'] }}</h3><p>Kasus Gizi Kurang</p></div><div class="icon"><i class="fas fa-weight"></i></div></div></div>
    <div class="col-lg-3 col-6"><div class="small-box bg-secondary"><div class="inner"><h3>{{ $stats['underweight'] }}</h3><p>Berat Badan Kurang</p></div><div class="icon"><i class="fas fa-balance-scale-left"></i></div></div></div>
</div>

{{-- BARIS 2: GRAFIK-GRAFIK --}}
<div class="row">
    {{-- Grafik Komposisi Status Gizi --}}
    <div class="col-md-6">
        <div class="card card-success card-outline">
            <div class="card-header"><h3 class="card-title">Komposisi Status Gizi (Berdasarkan Pemeriksaan Terakhir)</h3></div>
            <div class="card-body">
                {{-- PERBAIKAN: Ubah height di sini --}}
                <canvas id="statusGiziChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
    {{-- Grafik Cakupan Intervensi --}}
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header"><h3 class="card-title">Cakupan Intervensi (Berdasarkan Balita Terpantau)</h3></div>
            <div class="card-body">
                <canvas id="intervensiChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h3 class="card-title">Grafik Tren Kasus Gizi (Tahun {{ $tahunYangDipilih }})</h3>
            </div>
            <div class="card-body"><canvas id="trendGiziChart"></canvas></div>
        </div>
    </div>
</div>

{{-- BARIS BARU: TABEL DAFTAR ANAK DENGAN PERHATIAN KHUSUS --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex p-0">
                <h3 class="card-title p-3"><i class="fas fa-exclamation-circle text-danger"></i> Daftar Anak dengan Perhatian Khusus</h3>
                <ul class="nav nav-pills ml-auto p-2">
                    <li class="nav-item"><a class="nav-link active" href="#tab_stunting" data-toggle="tab">Stunting ({{ $anakStunting->count() }})</a></li>
                    <li class="nav-item"><a class="nav-link" href="#tab_wasting" data-toggle="tab">Gizi Kurang ({{ $anakWasting->count() }})</a></li>
                    <li class="nav-item"><a class="nav-link" href="#tab_underweight" data-toggle="tab">Berat Badan Kurang ({{ $anakUnderweight->count() }})</a></li>
                </ul>
            </div><div class="card-body">
                <div class="tab-content">
                    {{-- Tab 1: Daftar Anak Stunting --}}
                    <div class="tab-pane active" id="tab_stunting">
                        {{-- ====================================================== --}}
                        {{-- === PERBAIKAN: Tombol hanya muncul jika posyandu dipilih === --}}
                        {{-- ====================================================== --}}
                        @if($selectedPosyandu)
                        <div class="mb-2">
                            <a href="{{ route('export.anak-bermasalah', ['posyandu' => $selectedPosyandu->id, 'tipeMasalah' => 'stunting']) }}" class="btn btn-sm btn-success"><i class="fas fa-file-excel"></i> Ekspor Excel</a>
                            <a href="{{ route('export.anak-bermasalah.pdf', ['posyandu' => $selectedPosyandu->id, 'tipeMasalah' => 'stunting']) }}" class="btn btn-sm btn-danger"><i class="fas fa-file-pdf"></i> Ekspor PDF</a>
                        </div>
                        @endif
                        {{-- ====================================================== --}}
                        
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="text-center">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Anak</th>
                                        <th>Usia</th>
                                        <th>Nama Ibu</th>
                                        <th>Status Stunting</th>
                                        <th>Z-Score (TB/U)</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($anakStunting as $anak)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $anak->warga->nama_lengkap ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                $age = \Carbon\Carbon::parse($anak->warga->tanggal_lahir)->diff(now());
                                                $usiaBulan = $age->y * 12 + $age->m;
                                                $usiaHari = $age->d;
                                            @endphp
                                            {{ $usiaBulan }} bulan, {{ $usiaHari }} hari
                                        </td>
                                        <td>{{ $anak->nama_ibu ?? 'N/A' }}</td>
                                        <td class="text-center"><span class="badge badge-warning">{{ $anak->pemeriksaanTerakhir->status_stunting ?? 'N/A' }}</span></td>
                                        <td class="text-center">{{ $anak->pemeriksaanTerakhir->zscore_tb_u ?? 'N/A' }}</td>
                                        <td class="text-center"><a href="{{ route('kesehatan-anak.show', $anak->id) }}" class="btn btn-xs btn-info">Lihat Riwayat</a></td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="7" class="text-center">Tidak ada anak dengan status stunting.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Tab 2: Daftar Anak Wasting (Gizi Kurang) --}}
                    <div class="tab-pane" id="tab_wasting">
                        @if($selectedPosyandu)
                        <div class="mb-2">
                             <a href="{{ route('export.anak-bermasalah', ['posyandu' => $selectedPosyandu->id, 'tipeMasalah' => 'wasting']) }}" class="btn btn-sm btn-success"><i class="fas fa-file-excel"></i> Ekspor Excel</a>
                            <a href="{{ route('export.anak-bermasalah.pdf', ['posyandu' => $selectedPosyandu->id, 'tipeMasalah' => 'wasting']) }}" class="btn btn-sm btn-danger"><i class="fas fa-file-pdf"></i> Ekspor PDF</a>
                        </div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="text-center">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Anak</th>
                                        <th>Usia</th>
                                        <th>Nama Ibu</th>
                                        <th>Status Gizi Kurang</th>
                                        <th>Z-Score (BB/TB)</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     @forelse($anakWasting as $anak)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $anak->warga->nama_lengkap ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                $age = \Carbon\Carbon::parse($anak->warga->tanggal_lahir)->diff(now());
                                                $usiaBulan = $age->y * 12 + $age->m;
                                                $usiaHari = $age->d;
                                            @endphp
                                            {{ $usiaBulan }} bulan, {{ $usiaHari }} hari
                                        </td>
                                        <td>{{ $anak->nama_ibu ?? 'N/A' }}</td>
                                        <td class="text-center"><span class="badge badge-warning">{{ $anak->pemeriksaanTerakhir->status_wasting ?? 'N/A' }}</span></td>
                                        <td class="text-center">{{ $anak->pemeriksaanTerakhir->zscore_bb_tb ?? 'N/A' }}</td>
                                        <td class="text-center"><a href="{{ route('kesehatan-anak.show', $anak->id) }}" class="btn btn-xs btn-info">Lihat Riwayat</a></td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="7" class="text-center">Tidak ada anak dengan status gizi kurang.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Tab 3: Daftar Anak Underweight --}}
                    <div class="tab-pane" id="tab_underweight">
                        @if($selectedPosyandu)
                        <div class="mb-2">
                             <a href="{{ route('export.anak-bermasalah', ['posyandu' => $selectedPosyandu->id, 'tipeMasalah' => 'underweight']) }}" class="btn btn-sm btn-success"><i class="fas fa-file-excel"></i> Ekspor Excel</a>
                            <a href="{{ route('export.anak-bermasalah.pdf', ['posyandu' => $selectedPosyandu->id, 'tipeMasalah' => 'underweight']) }}" class="btn btn-sm btn-danger"><i class="fas fa-file-pdf"></i> Ekspor PDF</a>
                        </div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="text-center">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Anak</th>
                                        <th>Usia</th>
                                        <th>Nama Ibu</th>
                                        <th>Status BB Kurang</th>
                                        <th>Z-Score (BB/U)</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     @forelse($anakUnderweight as $anak)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $anak->warga->nama_lengkap ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                $age = \Carbon\Carbon::parse($anak->warga->tanggal_lahir)->diff(now());
                                                $usiaBulan = $age->y * 12 + $age->m;
                                                $usiaHari = $age->d;
                                            @endphp
                                            {{ $usiaBulan }} bulan, {{ $usiaHari }} hari
                                        </td>
                                        <td>{{ $anak->nama_ibu ?? 'N/A' }}</td>
                                        <td class="text-center"><span class="badge badge-warning">{{ $anak->pemeriksaanTerakhir->status_underweight ?? 'N/A' }}</span></td>
                                        <td class="text-center">{{ $anak->pemeriksaanTerakhir->zscore_bb_u ?? 'N/A' }}</td>
                                        <td class="text-center"><a href="{{ route('kesehatan-anak.show', $anak->id) }}" class="btn btn-xs btn-info">Lihat Riwayat</a></td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="7" class="text-center">Tidak ada anak dengan status berat badan kurang.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@if($selectedPosyandu)
    {{-- BARIS 5: TABEL AKSI (Hanya Tampil Jika Posyandu Dipilih) --}}
    <form action="{{ route('kesehatan-anak.store') }}" method="POST">
    @csrf
    @if(Auth::user()->user_type == 'kader_posyandu')
        <input type="hidden" name="posyandu_id" value="{{ Auth::user()->posyandu_id }}">
    @elseif($selectedPosyandu)
        <input type="hidden" name="posyandu_id" value="{{ $selectedPosyandu->id }}">
    @endif
    <div class="row">
        <div class="col-12">
            {{-- Letakkan kode untuk Card Tabel "Anak Baru" di sini --}}
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
                                    <td><input type="checkbox" name="warga_ids[]" class="check-item-baru" value="{{ $warga->id }}">
                                    </td>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $warga->nama_lengkap }}</strong><br>
                                        <small>NIK: {{ $warga->nik ?? 'Belum Ada' }} | No. KK:
                                            {{ $warga->kartuKeluarga->nomor_kk ?? 'N/A' }}</small>
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
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data anak baru yang sesuai filter.</td>
                                </tr>
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
        </div>
        <div class="col-12">
             {{-- Letakkan kode untuk Card Tabel "Anak dalam Pemantauan" di sini --}}
            <div class="card mt-4">
                <div class="card-header bg-success d-flex flex-wrap justify-content-between align-items-center">
                    <h3 class="card-title mb-2 mb-md-0">Anak dalam Pemantauan</h3>

                    <div class="card-tools">
                        <form action="{{ route('kesehatan-anak.index') }}" method="GET">
                            <div class="input-group input-group-sm" style="max-width: 500px;">
                                <input type="text" name="search" class="form-control" placeholder="Cari NIK, Nama Anak/Ibu..."
                                    value="{{ request('search') }}">
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
                        <thead class="text-center">
                            <tr>
                                <th>Nama Anak</th>
                                <th>Usia</th>
                                <th>Status Gizi Terakhir</th> {{-- <-- KOLOM BARU --}}
                                <th>Tingkat Kehadiran</th> {{-- <-- KOLOM BARU --}}
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($anakTerpantau as $data)
                            <tr>
                                <td>
                                    <strong>{{ $data->warga->nama_lengkap ?? 'N/A' }}</strong><br>
                                    <small>NIK: {{ $data->warga->nik ?? 'N/A' }}</small>
                                </td>                    
                                <td class="text-center">
                                    @php
                                        $age = \Carbon\Carbon::parse( $data->warga->tanggal_lahir)->diff(now());
                                        $usiaBulan = $age->y * 12 + $age->m;
                                        $usiaHari = $age->d;
                                    @endphp
                                        {{ $usiaBulan }} bulan, {{ $usiaHari }} hari
                                </td>
                                <td>
                                    {{-- Tampilkan status gizi dari pemeriksaan terakhir --}}
                                    @if($pemeriksaan = $data->pemeriksaanTerakhir)
                                        <span class="badge badge-warning">{{ $pemeriksaan->status_stunting ?? '' }}</span>
                                        <span class="badge badge-danger">{{ $pemeriksaan->status_wasting ?? '' }}</span>
                                        <span class="badge badge-secondary">{{ $pemeriksaan->status_underweight ?? '' }}</span>
                                    @else
                                        <span class="badge badge-light">Belum Diperiksa</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{-- Tampilkan rasio kehadiran --}}
                                    Hadir: <strong>{{ $data->riwayat_pemeriksaan_count }} / {{ $data->total_sesi }}</strong> Sesi
                                    @php
                                        $persenHadir = ($data->total_sesi > 0) ? round(($data->riwayat_pemeriksaan_count / $data->total_sesi) * 100) : 0;
                                    @endphp
                                    <div class="progress mt-1" style="height: 5px;">
                                        <div class="progress-bar bg-primary" style="width: {{ $persenHadir }}%"></div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('kesehatan-anak.show', $data) }}" class="btn btn-xs btn-info" title="Input & Lihat Riwayat">
                                        <i class="fas fa-file-medical-alt"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center">Tidak ada anak dalam pemantauan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-3">{{ $anakTerpantau->appends(request()->query())->links() }}</div>
                </div>
            </div>
        </div>
    </div>
    </form>
@else
    {{-- Pesan ini muncul jika admin belum memilih Posyandu --}}
    <div class="alert alert-light text-center">
        <h5><i class="icon fas fa-info-circle"></i> Tabel Aksi</h5>
        <p>Pilih salah satu Posyandu dari daftar di atas untuk menambahkan anak baru atau melihat daftar anak yang dipantau.</p>
    </div>
@endif

@stop

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script>
        $(document).ready(function () {
            // Fungsi untuk checkbox "Pilih Semua" di tabel Anak Baru
            $('#check-all-baru').on('click', function () {
                $('.check-item-baru').prop('checked', $(this).prop('checked'));
            });

            // Fungsi untuk checkbox "Pilih Semua" di tabel Anak Terpantau
            $('#check-all-terpantau').on('click', function () {
                $('.check-item-terpantau').prop('checked', $(this).prop('checked'));
            });
        });
    </script>
<script>
$(function () {
    // Data untuk Grafik Status Gizi (Pie Chart)
    Chart.register(ChartDataLabels);
    var statusGiziData = {
        labels: ['Normal', 'Stunting', 'Gizi Kurang (Wasting)', 'BB Kurang (Underweight)'],
        datasets: [{
            data: [@json($stats['normal']), @json($stats['stunting']), @json($stats['wasting']), @json($stats['underweight'])],
            backgroundColor: ['#28a745', '#dc3545', '#ffc107', '#6c757d'],
        }]
    };

    // Konfigurasi baru untuk menampilkan persentase
    var statusGiziOptions = {
        maintainAspectRatio: false,
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            // Konfigurasi untuk plugin datalabels
            datalabels: {
                formatter: (value, ctx) => {
                    let sum = 0;
                    let dataArr = ctx.chart.data.datasets[0].data;
                    dataArr.map(data => {
                        sum += data;
                    });
                    // Tampilkan persentase, jangan tampilkan jika 0%
                    let percentage = (value*100 / sum).toFixed(1) + '%';
                    return value > 0 ? percentage : '';
                },
                color: '#fff', // Warna teks persentase
                font: {
                    weight: 'bold'
                }
            }
        }
    };

    new Chart($('#statusGiziChart').get(0).getContext('2d'), { 
        type: 'doughnut', 
        data: statusGiziData,
        options: statusGiziOptions // Gunakan options baru
    });
    // Data untuk Grafik Intervensi (Bar Chart)
    var intervensiData = {
        labels: ['Cakupan'],
        datasets: [
            {
                label: 'Vitamin A',
                backgroundColor: 'rgba(60,141,188,0.9)',
                data: [@json($stats['persen_vit_a'])]
            },
            {
                label: 'Imunisasi Polio',
                backgroundColor: 'rgba(210, 214, 222, 1)',
                data: [@json($stats['persen_imunisasi'])]
            }
        ]
    };
    new Chart($('#intervensiChart').get(0).getContext('2d'), {
        type: 'bar',
        data: intervensiData,
        options: { scales: { y: { beginAtZero: true, max: 100, ticks: { callback: function(value) { return value + "%" } } } } }
    });

    var trendGiziCanvas = $('#trendGiziChart').get(0).getContext('2d');
    new Chart(trendGiziCanvas, {
        type: 'line',
        data: {
            labels: @json($trendData['labels']),
            datasets: [
                {
                    label: 'Gizi Normal', // <-- DATASET BARU
                    data: @json($trendData['normal']),
                    borderColor: '#28a745', // Warna hijau
                    tension: 0.1
                },
                {
                    label: 'Stunting',
                    data: @json($trendData['stunting']),
                    borderColor: '#dc3545',
                    tension: 0.1
                },
                {
                    label: 'Wasting',
                    data: @json($trendData['wasting']),
                    borderColor: '#ffc107',
                    tension: 0.1
                },
                {
                    label: 'Underweight',
                    data: @json($trendData['underweight']),
                    borderColor: '#6c757d',
                    tension: 0.1
                }
            ]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
});
</script>
@endpush
