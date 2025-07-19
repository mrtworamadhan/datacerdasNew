@extends('admin.master')
@section('title', 'Daftar Pengajuan Surat')
@section('content_header')
    <h1 class="m-0 text-dark">Dashboard Pengajuan Surat</h1>
@stop

@section('content')
{{-- BAGIAN INFO CARD BARU --}}
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['total'] }}</h3>
                <p>Total Pengajuan</p>
            </div>
            <div class="icon">
                <i class="fas fa-file-alt"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['diproses'] }}</h3>
                <p>Perlu Diproses</p>
            </div>
            <div class="icon">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <a href="#perlu-diproses" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['selesai'] }}</h3>
                <p>Selesai (Disetujui)</p>
            </div>
            <div class="icon">
                <i class="fas fa-check"></i>
            </div>
             <a href="#riwayat-selesai" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $stats['ditolak'] }}</h3>
                <p>Ditolak</p>
            </div>
            <div class="icon">
                <i class="fas fa-times"></i>
            </div>
             <a href="#riwayat-selesai" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>
{{-- AKHIR BAGIAN INFO CARD --}}

<div class="row">
    <div class="col-12">
        {{-- TABEL 1: PERMOHONAN PERLU DIPROSES --}}
        <div class="card" id="perlu-diproses">
            <div class="card-header bg-warning">
                <h3 class="card-title">Permohonan Perlu Diproses</h3>
                 <div class="card-tools">
                    <a href="{{ route('pengajuan-surat.create') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-plus"></i> Buat Pengajuan Baru
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Pemohon</th>
                            <th>Jenis Surat</th>
                            <th>Tgl. Pengajuan</th>
                            <th>Diajukan Oleh</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengajuansPending as $pengajuan)
                            <tr>
                                <td>{{ $loop->iteration + $pengajuansPending->firstItem() - 1 }}</td>
                                <td>
                                    <strong>{{ $pengajuan->warga->nama_lengkap ?? 'N/A' }}</strong><br>
                                    <small>NIK: {{ $pengajuan->warga->nik ?? 'N/A' }}</small>
                                </td>
                                <td>{{ $pengajuan->jenisSurat->nama_surat ?? 'N/A' }}</td>
                                <td>{{ $pengajuan->tanggal_pengajuan->translatedFormat('d F Y') }}</td>
                                <td>{{ $pengajuan->diajukanOleh->name ?? 'N/A' }}</td>
                                <td>
                                    @if($pengajuan->status_permohonan == 'Diajukan')
                                        <span class="badge badge-primary">Diajukan</span>
                                    @elseif($pengajuan->status_permohonan == 'Diproses Desa')
                                        <span class="badge badge-info">Diproses Desa</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('pengajuan-surat.show', $pengajuan) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> Proses
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada permohonan yang perlu diproses saat ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                 <div class="mt-3">
                    {{ $pengajuansPending->links() }}
                </div>
            </div>
        </div>

        {{-- TABEL 2: RIWAYAT PERMOHONAN SELESAI --}}
        <div class="card mt-4" id="riwayat-selesai">
            <div class="card-header bg-purple">
                <h3 class="card-title">Riwayat Permohonan Selesai</h3>
                {{-- FORM PENCARIAN BARU --}}
                <div class="card-tools">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" id="search-nik-input" name="search_nik" class="form-control float-right" placeholder="Cari berdasarkan NIK...">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-default"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Container untuk tabel yang akan di-update oleh AJAX --}}
            <div id="riwayat-table-container">
                @include('admin_desa.pengajuan_surat._riwayat_table')
            </div>
        </div>
    </div>
</div>
@stop
@push('js')
<script>
$(document).ready(function() {
    let searchTimer;
    const delay = 500; // Jeda 500ms setelah user berhenti mengetik

    // Fungsi untuk melakukan pencarian via AJAX
    function performSearch(query = '') {
        $.ajax({
            url: "{{ route('search.warga') }}",
            type: "GET",
            data: { 'search_nik': query },
            success: function(data) {
                $('#riwayat-table-container').html(data);
            }
        });
    }

    // Event listener untuk input pencarian
    $('#search-nik-input').on('keyup', function() {
        clearTimeout(searchTimer);
        const query = $(this).val();
        searchTimer = setTimeout(function() {
            performSearch(query);
        }, delay);
    });

    // Handle paginasi via AJAX
    $(document).on('click', '#riwayat-table-container .pagination a', function(event) {
        event.preventDefault();
        var pageUrl = $(this).attr('href');
        $.ajax({
            url: pageUrl,
            type: "GET",
            data: { 'search_nik': $('#search-nik-input').val() },
            success: function(data) {
                $('#riwayat-table-container').html(data);
            }
        });
    });
});
</script>
@endpush
