@extends('layouts.portal')
@section('title', 'Rekam Medis: ' . $kesehatanAnak->warga->nama_lengkap)

@section('content')
<div class="container">
    <a href="{{ route('portal.posyandu.rekam_medis.search', ['subdomain' => $subdomain]) }}" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Kembali ke Pencarian
    </a>

    {{-- Info Dasar Anak --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Profil Anak</h5>
            <span class="badge 
                @if($statusTerakhir === 'Normal') bg-success 
                @elseif($statusTerakhir === 'Berisiko (Kuning)') bg-warning text-dark
                @else bg-danger 
                @endif">
                Status Terakhir: {{ $statusTerakhir }}
            </span>
        </div>
        <div class="card-body">
            <h4>{{ $kesehatanAnak->warga->nama_lengkap }}</h4>
            <hr>
            <dl class="row">
                <dt class="col-sm-4">NIK</dt>
                <dd class="col-sm-8">{{ $kesehatanAnak->warga->nik ?? 'Belum terdata' }}</dd>

                <dt class="col-sm-4">No. KK</dt>
                <dd class="col-sm-8">{{ $kesehatanAnak->warga->kartuKeluarga->nomor_kk ?? 'Belum terdata' }}</dd>

                <dt class="col-sm-4">Usia</dt>
                <dd class="col-sm-8">{{ \Carbon\Carbon::parse($kesehatanAnak->warga->tanggal_lahir)->diffForHumans(null, true) }}</dd>

                <dt class="col-sm-4">Nama Ibu</dt>
                <dd class="col-sm-8">{{ $kesehatanAnak->warga->nama_ibu_kandung ?? '-' }}</dd>
                
                <dt class="col-sm-4">Nama Ayah</dt>
                <dd class="col-sm-8">{{ $kesehatanAnak->warga->nama_ayah_kandung ?? '-' }}</dd>
            </dl>
        </div>
    </div>
    
    {{-- Grafik Pertumbuhan --}}
    <div class="card mb-4">
        <div class="card-header">Grafik Pertumbuhan</div>
        <div class="card-body">
            <canvas id="grafikPertumbuhan"></canvas>
        </div>
    </div>
    @php
        function getBadgeColor($status)
        {
            if (stripos($status, 'Normal') !== false || stripos($status, 'Gizi Baik') !== false) {
                return 'bg-success'; // Hijau
            }
            if (
                stripos($status, 'Pendek') !== false ||
                stripos($status, 'Kurang') !== false ||
                stripos($status, 'Lebih') !== false
            ) {
                return 'bg-warning text-dark'; // Kuning
            }
            if (
                stripos($status, 'Berat') !== false ||
                stripos($status, 'Buruk') !== false ||
                stripos($status, 'Obesitas') !== false
            ) {
                return 'bg-danger'; // Merah
            }
            return 'bg-secondary'; // Default abu-abu
        }
    @endphp

    {{-- Tabel Riwayat Pemeriksaan --}}
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Riwayat Pemeriksaan</h5>
        </div>
        <div class="card-body">
            @forelse($riwayatPemeriksaan as $pemeriksaan)
                <div class="card mb-3"> {{-- Satu kartu untuk satu riwayat --}}
                    <div class="card-header bg-light">
                        <strong>{{ $pemeriksaan->tanggal_pemeriksaan->isoFormat('dddd, D MMMM YYYY') }}</strong>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Info Dasar Pengukuran --}}
                            <div class="col-12 mb-3">
                                <h6 class="card-title">Pengukuran Fisik</h6>
                                <p class="card-text mb-0">
                                    <i class="fas fa-ruler-vertical text-muted"></i> Tinggi: <strong>{{ $pemeriksaan->tinggi_badan }} cm</strong>
                                </p>
                                <p class="card-text mb-0">
                                    <i class="fas fa-weight-hanging text-muted"></i> Berat: <strong>{{ $pemeriksaan->berat_badan }} kg</strong>
                                </p>
                                <p class="card-text">
                                    <i class="far fa-user-circle text-muted"></i> Usia: <strong>{{ $pemeriksaan->usia_formatted }}</strong>
                                </p>
                            </div>

                            {{-- Info Status Gizi --}}
                            <div class="d-flex flex-wrap justify-content-center">

                                {{-- Item Stunting --}}
                                <div class="text-center mx-2 mb-2">
                                    <small class="text-muted d-block">Stunting</small>
                                    <span class="badge {{ getBadgeColor($pemeriksaan->status_stunting) }}">
                                        {{ $pemeriksaan->status_stunting }}
                                    </span>
                                </div>

                                {{-- Item Underweight --}}
                                <div class="text-center mx-2 mb-2">
                                    <small class="text-muted d-block">Underweight</small>
                                    <span class="badge {{ getBadgeColor($pemeriksaan->status_underweight) }}">
                                        {{ $pemeriksaan->status_underweight }}
                                    </span>
                                </div>

                                {{-- Item Wasting --}}
                                <div class="text-center mx-2 mb-2">
                                    <small class="text-muted d-block">Wasting</small>
                                    <span class="badge {{ getBadgeColor($pemeriksaan->status_wasting) }}">
                                        {{ $pemeriksaan->status_wasting }}
                                    </span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-light text-center">
                    Belum ada riwayat pemeriksaan untuk ditampilkan.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('grafikPertumbuhan');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [
                {
                    label: 'Berat Badan (kg)',
                    data: {!! json_encode($chartDataBeratBadan) !!},
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                },
                {
                    label: 'Tinggi Badan (cm)',
                    data: {!! json_encode($chartDataTinggiBadan) !!},
                    borderColor: 'rgb(255, 99, 132)',
                    tension: 0.1
                }
            ]
        },
    });
</script>
@endpush