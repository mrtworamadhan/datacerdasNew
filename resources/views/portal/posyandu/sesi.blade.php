@extends('layouts.portal')
@section('title', 'Sesi ' . $tanggalSesi->isoFormat('MMMM YYYY'))

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Sesi Posyandu</h4>
            <p class="text-muted">{{ $tanggalSesi->isoFormat('MMMM YYYY') }}</p>
        </div>
        <a href="{{ route('portal.posyandu.index', ['subdomain' => app('tenant')->subdomain]) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    @if($isSesiSaatIni)
        {{-- Tampilan "Ruang Kerja" untuk Sesi Bulan Ini --}}
        <div class="card mb-4">
            <div class="card-header"><h5 class="card-title">Input Pemeriksaan</h5></div>
            <div class="card-body">
                <p>Pilih anak yang akan diperiksa dari daftar di bawah ini.</p>
                <form action="#" method="GET" id="form-pilih-anak">
                    <select id="pilih_anak_select" class="form-select form-select-lg">
                        <option value="">-- Cari Nama atau NIK Anak --</option>
                        @foreach($anakBelumDiperiksa as $anak)
                            <option value="{{ route('portal.posyandu.pemeriksaan.create', ['subdomain' => app('tenant')->subdomain, 'anak' => $anak->id]) }}">
                                {{ $anak->warga->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    @else
        {{-- Tampilan "Laporan" untuk Sesi Bulan Lalu --}}
        <div class="alert alert-info">
            Anda sedang melihat arsip laporan untuk bulan {{ $tanggalSesi->isoFormat('MMMM YYYY') }}.
        </div>
    @endif
    @php
    function getBadgeColor($status) {
        if (stripos($status, 'Normal') !== false || stripos($status, 'Gizi Baik') !== false) {
            return 'bg-success'; // Hijau
        }
        if (stripos($status, 'Pendek') !== false || 
            stripos($status, 'Kurang') !== false || 
            stripos($status, 'Lebih') !== false) {
            return 'bg-warning text-dark'; // Kuning
        }
        if (stripos($status, 'Berat') !== false || 
            stripos($status, 'Buruk') !== false || 
            stripos($status, 'Obesitas') !== false) {
            return 'bg-danger'; // Merah
        }
        return 'bg-secondary'; // Default abu-abu
    }
    @endphp

    <div class="card">
    <div class="card-header">
        <h5 class="card-title">Anak Sudah Diperiksa ({{ $pemeriksaanBulanIni->count() }})</h5>
    </div>
    <div class="card-body p-0">
        <ul class="list-group list-group-flush">
            @forelse($pemeriksaanBulanIni as $pemeriksaan)
                <li class="list-group-item">
                    <strong>{{ $pemeriksaan->warga->nama_lengkap }}</strong><br>
                    <small class="text-muted">
                        BB: {{ $pemeriksaan->berat_badan }} kg | 
                        TB: {{ $pemeriksaan->tinggi_badan }} cm
                    </small>

                    <div class="mt-2">
                        <div>
                            <small>Stunting:</small><br>
                            <span class="badge {{ getBadgeColor($pemeriksaan->status_stunting) }}">
                                {{ $pemeriksaan->zscore_tb_u }} | {{ $pemeriksaan->status_stunting }}
                            </span>
                        </div>
                        <div class="mt-1">
                            <small>Underweight:</small><br>
                            <span class="badge {{ getBadgeColor($pemeriksaan->status_underweight) }}">
                                {{ $pemeriksaan->zscore_bb_u }} | {{ $pemeriksaan->status_underweight }}
                            </span>
                        </div>
                        <div class="mt-1">
                            <small>Wasting:</small><br>
                            <span class="badge {{ getBadgeColor($pemeriksaan->status_wasting) }}">
                                {{ $pemeriksaan->zscore_bb_tb }} | {{ $pemeriksaan->status_wasting }}
                            </span>
                        </div>
                    </div>
                </li>
            @empty
                <li class="list-group-item text-center text-muted">
                    Belum ada anak yang diperiksa di sesi ini.
                </li>
            @endforelse
        </ul>
    </div>
</div>

</div>
@stop

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#pilih_anak_select').select2({ theme: 'bootstrap-5' });

            $('#pilih_anak_select').on('change', function() {
                const url = $(this).val();
                if (url) {
                    window.location.href = url;
                }
            });
        });
    </script>
@endpush