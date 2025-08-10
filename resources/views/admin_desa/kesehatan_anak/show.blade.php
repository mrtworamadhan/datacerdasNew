@extends('admin.master')
@section('title', 'Riwayat Kesehatan Anak')
@section('content_header')<h1 class="m-0 text-dark">Riwayat Kesehatan: {{ $kesehatanAnak->warga->nama_lengkap }}</h1>@stop

@section('content_main')
{{-- BARIS ATAS: FORM INPUT DAN PROFIL ANAK --}}
<div class="row">
    {{-- Kolom Kiri: Form Input Pemeriksaan Baru --}}
    <div class="col-md-5">
        <form action="{{ route('pemeriksaan-anak.store', $kesehatanAnak) }}" method="POST">
            @csrf
            <div class="card card-purple card-outline">
                <div class="card-header"><h3 class="card-title">Input Pemeriksaan Baru</h3></div>
                <div class="card-body">
                    {{-- Form inputmu di sini (tidak ada yang berubah, sudah benar) --}}
                    <div class="form-group">
                        <label for="tanggal_pemeriksaan">Tanggal Pemeriksaan</label>
                        <input type="date" name="tanggal_pemeriksaan" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <label for="berat_badan">Berat Badan (kg)</label>
                            <input type="number" step="0.1" name="berat_badan" class="form-control @error('berat_badan') is-invalid @enderror" value="{{ old('berat_badan') }}" required>
                            @error('berat_badan')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-6 form-group">
                            <label for="tinggi_badan">Tinggi Badan (cm)</label>
                            <input type="number" step="0.1" name="tinggi_badan" class="form-control @error('tinggi_badan') is-invalid @enderror" value="{{ old('tinggi_badan') }}" required>
                            @error('tinggi_badan')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="lila">Lingkar Lengan Atas (LILA) (cm)</label>
                        <input type="number" step="0.1" name="lila" class="form-control @error('lila') is-invalid @enderror" value="{{ old('lila') }}">
                        @error('lila')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <hr>
                    <label>Intervensi Diberikan:</label>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="dapat_vitamin_a" name="dapat_vitamin_a" value="1">
                            <label class="custom-control-label" for="dapat_vitamin_a">Diberi Vitamin A</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="dapat_obat_cacing" name="dapat_obat_cacing" value="1">
                            <label class="custom-control-label" for="dapat_obat_cacing">Diberi Obat Cacing</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="dapat_imunisasi_polio" name="dapat_imunisasi_polio" value="1">
                            <label class="custom-control-label" for="dapat_imunisasi_polio">Diberi Imunisasi Polio</label>
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

    {{-- Kolom Kanan: Detail Anak & Grafik --}}
    <div class="col-md-7">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Data Diri Anak</h3></div>
            <div class="card-body">
                <table class="table table-sm">
                    {{-- Tabel profil anak di sini (tidak ada yang berubah) --}}
                    <tr><th style="width: 30%">Nama</th><td>: {{ $kesehatanAnak->warga->nama_lengkap }}</td></tr>
                    <tr><th>NIK</th><td>: {{ $kesehatanAnak->warga->nik ?? 'Belum Ada' }}</td></tr>
                    <tr><th>Usia</th><td>: {{ \Carbon\Carbon::parse($kesehatanAnak->warga->tanggal_lahir)->diff(now())->format('%y tahun, %m bulan, %d hari') }}</td></tr>
                    <tr><th>Nama Ibu</th><td>: {{ $kesehatanAnak->nama_ibu }}</td></tr>
                    <tr><th>Nama Ayah</th><td>: {{ $kesehatanAnak->nama_ayah }}</td></tr>
                </table>
            </div>
        </div>
        <div class="card card-info card-outline">
            <div class="card-header"><h3 class="card-title">Grafik Pertumbuhan Anak</h3></div>
            <div class="card-body">
                <canvas id="growthChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- BARIS BAWAH: TABEL RIWAYAT LENGKAP --}}
<div class="row">
    {{-- PERBAIKAN: Bungkus card tabel di dalam <div class="col-12"> --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Tabel Riwayat Pemeriksaan</h3></div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped table-valign-middle">
                    {{-- Tabel riwayatmu di sini (tidak ada yang berubah) --}}
                    <thead class="text-center">
                        <tr>
                            <th rowspan="2" style="vertical-align: middle;">Tgl Periksa</th>
                            <th rowspan="2" style="vertical-align: middle;">Usia</th>
                            <th colspan="3">Antropometri</th>
                            <th colspan="3">Status Gizi (Z-Score)</th>
                            <th colspan="3">Intervensi</th>
                            <th rowspan="2" style="vertical-align: middle;">Aksi</th>
                        </tr>
                        <tr>
                            <th>BB (kg)</th>
                            <th>TB (cm)</th>
                            <th>LILA (cm)</th>
                            <th>TB/U (Stunting)</th>
                            <th>BB/TB (Wasting)</th>
                            <th>BB/U (Underweight)</th>
                            <th>Vit. A</th>
                            <th>Obat Cacing</th>
                            <th>Polio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayatPemeriksaan as $pemeriksaan)
                        <tr class="text-center">
                            <td>{{ $pemeriksaan->tanggal_pemeriksaan->translatedFormat('d M Y') }}</td>
                            <td>{{ $pemeriksaan->usia_saat_periksa }} bln</td>
                            <td>{{ $pemeriksaan->berat_badan ?? '-' }}</td>
                            <td>{{ $pemeriksaan->tinggi_badan ?? '-' }}</td>
                            <td>{{ $pemeriksaan->lila ?? '-' }}</td>
                            <td>
                                @php
                                    $status = $pemeriksaan->status_stunting;
                                    $badgeColor = 'success';
                                    if (str_contains($status, 'Berat')) { $badgeColor = 'danger'; } 
                                    elseif (str_contains($status, 'Pendek')) { $badgeColor = 'warning'; }
                                @endphp
                                <span class="badge badge-{{ $badgeColor }}">{{ $status ?? 'N/A' }}</span><br>
                                <small>Z: {{ $pemeriksaan->zscore_tb_u ?? 'N/A' }}</small>
                            </td>
                            <td>
                                @php
                                    $status = $pemeriksaan->status_wasting;
                                    $badgeColor = 'success';
                                    if (str_contains($status, 'Sangat Kurus')) { $badgeColor = 'danger'; } 
                                    elseif (str_contains($status, 'Kurus')) { $badgeColor = 'warning'; }
                                    elseif (str_contains($status, 'Gizi Lebih')) { $badgeColor = 'primary'; }
                                @endphp
                                <span class="badge badge-{{ $badgeColor }}">{{ $status ?? 'N/A' }}</span><br>
                                <small>Z: {{ $pemeriksaan->zscore_bb_tb ?? 'N/A' }}</small>
                            </td>
                            <td>
                                @php
                                    $status = $pemeriksaan->status_underweight;
                                    $badgeColor = 'success';
                                    if (str_contains($status, 'Sangat Kurang')) { $badgeColor = 'danger'; } 
                                    elseif (str_contains($status, 'Kurang')) { $badgeColor = 'warning'; }
                                @endphp
                                <span class="badge badge-{{ $badgeColor }}">{{ $status ?? 'N/A' }}</span><br>
                                <small>Z: {{ $pemeriksaan->zscore_bb_u ?? 'N/A' }}</small>
                            </td>
                            <td>{!! $pemeriksaan->dapat_vitamin_a ? '<span class="text-success">✔️</span>' : '<span class="text-danger">❌</span>' !!}</td>
                            <td>{!! $pemeriksaan->dapat_obat_cacing ? '<span class="text-success">✔️</span>' : '<span class="text-danger">❌</span>' !!}</td>
                            <td>{!! $pemeriksaan->dapat_imunisasi_polio ? '<span class="text-success">✔️</span>' : '<span class="text-danger">❌</span>' !!}</td>
                            <td>
                                <button type="button" class="btn btn-xs btn-warning edit-pemeriksaan-btn" 
                                        data-id="{{ $pemeriksaan->id }}" 
                                        data-url="{{ route('pemeriksaan-anak.edit', $pemeriksaan->id) }}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="11" class="text-center">Belum ada riwayat pemeriksaan.</td></tr>
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
{{-- MODAL UNTUK EDIT PEMERIKSAAN --}}
<div class="modal fade" id="editPemeriksaanModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div id="editModalContent">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Memuat Data...</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-center">Silakan tunggu...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

{{-- Pastikan @section('js') diubah menjadi @push('js') jika master layout-mu menggunakan @stack('js') --}}
@push('js')
{{-- Panggil library Chart.js. Jika sudah ada di master layout, baris ini tidak perlu --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(function () {
    // Ambil data dari controller yang sudah di-encode ke JSON
    var chartLabels = @json($chartLabels);
    var chartDataBeratBadan = @json($chartDataBeratBadan);
    var chartDataTinggiBadan = @json($chartDataTinggiBadan);
    var chartDataHaz = @json($chartDataHaz);

    var growthChartCanvas = $('#growthChart').get(0).getContext('2d');

    var growthChartData = {
        labels: chartLabels,
        datasets: [
            {
                label: 'Tinggi Badan (cm)',
                backgroundColor: 'rgba(60,141,188,0.9)',
                borderColor: 'rgba(60,141,188,0.8)',
                data: chartDataTinggiBadan,
                yAxisID: 'y', // Gunakan sumbu Y kiri
            },
            {
                label: 'Berat Badan (kg)',
                backgroundColor: 'rgba(210, 214, 222, 1)',
                borderColor: 'rgba(210, 214, 222, 1)',
                data: chartDataBeratBadan,
                yAxisID: 'y', // Gunakan sumbu Y kiri
            },
            {
                label: 'Z-Score Stunting (TB/U)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                data: chartDataHaz,
                yAxisID: 'y1', // Gunakan sumbu Y kanan
                type: 'line', // Jadikan ini sebagai diagram garis
                borderWidth: 2,
                pointRadius: 5,
            }
        ]
    };

    var growthChartOptions = {
        maintainAspectRatio: false,
        responsive: true,
        scales: {
            x: {
                grid: { display: false, }
            },
            y: { // Sumbu Y kiri untuk BB dan TB
                type: 'linear',
                display: true,
                position: 'left',
                title: { display: true, text: 'Kg / Cm' }
            },
            y1: { // Sumbu Y kanan untuk Z-Score
                type: 'linear',
                display: true,
                position: 'right',
                title: { display: true, text: 'Z-Score' },
                grid: { drawOnChartArea: false, } // Jangan gambar grid dari sumbu ini
            }
        }
    };

    // Buat diagram batang
    new Chart(growthChartCanvas, {
        type: 'bar', // Tipe utama adalah diagram batang
        data: growthChartData,
        options: growthChartOptions
    });
})
</script>
<script>
    $(document).ready(function () {
        // Event listener untuk semua tombol dengan class .edit-pemeriksaan-btn
        $('.edit-pemeriksaan-btn').on('click', function () {
            var url = $(this).data('url');
            var modalContent = $('#editModalContent');
            var modal = $('#editPemeriksaanModal');

            // Tampilkan pesan loading di modal
            modalContent.html('<div class="modal-body text-center"><p>Memuat data...</p></div>');
            modal.modal('show');

            // Ambil konten form dari server via AJAX
            $.get(url, function (data) {
                // Masukkan konten form yang diterima ke dalam modal
                modalContent.html(data);
            }).fail(function() {
                // Jika gagal, tampilkan pesan error
                modalContent.html('<div class="modal-body text-center"><p class="text-danger">Gagal memuat data. Silakan coba lagi.</p></div>');
            });
        });
    });
</script>
@endpush