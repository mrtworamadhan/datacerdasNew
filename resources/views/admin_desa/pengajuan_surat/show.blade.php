@extends('admin.master')
@section('title', 'Proses Pengajuan Surat')
@section('content_header')
<h1 class="m-0 text-dark">Proses Pengajuan Surat</h1>
@stop

@section('content')
<div class="row">
    {{-- Kolom Kiri: Detail Informasi --}}
    <div class="col-md-7">
        <div class="card card-purple card-outline">
            <div class="card-header">
                <h3 class="card-title">Detail Permohonan</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 30%;">Jenis Surat</th>
                        <td>{{ $pengajuanSurat->jenisSurat->nama_surat }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Pengajuan</th>
                        <td>{{ $pengajuanSurat->tanggal_pengajuan->translatedFormat('d F Y') }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($pengajuanSurat->status_permohonan == 'Diajukan') <span
                                class="badge badge-primary">Diajukan</span>
                            @elseif($pengajuanSurat->status_permohonan == 'Diproses Desa') <span
                                class="badge badge-info">Diproses Desa</span>
                            @elseif($pengajuanSurat->status_permohonan == 'Disetujui') <span
                                class="badge badge-success">Disetujui</span>
                            @elseif($pengajuanSurat->status_permohonan == 'Ditolak') <span
                                class="badge badge-danger">Ditolak</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Diajukan Oleh</th>
                        <td>{{ $pengajuanSurat->diajukanOleh->name ?? "" }} ({{ $pengajuanSurat->diajukanOleh->user_type ?? ""}})
                        </td>
                    </tr>
                </table>

                <h5 class="mt-4">Data Pemohon</h5>
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 30%;">Nama Lengkap</th>
                        <td>{{ $pengajuanSurat->warga->nama_lengkap }}</td>
                    </tr>
                    <tr>
                        <th>NIK</th>
                        <td>{{ $pengajuanSurat->warga->nik }}</td>
                    </tr>
                    <tr>
                        <th>No. KK</th>
                        <td>{{ $pengajuanSurat->warga->kartuKeluarga->nomor_kk ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>{{ $pengajuanSurat->warga->alamat_lengkap }}</td>
                    </tr>
                </table>

                {{-- Tampilkan isian tambahan jika ada --}}
                @if($pengajuanSurat->detail_tambahan)
                    <h5 class="mt-4">Isian Tambahan</h5>
                    <table class="table table-bordered">
                        @foreach($pengajuanSurat->detail_tambahan as $key => $value)
                            <tr>
                                <th style="width: 30%;">{{ ucwords(str_replace('_', ' ', $key)) }}</th>
                                <td>{{ $value }}</td>
                            </tr>
                        @endforeach
                    </table>
                @endif

                {{-- Tampilkan checklist persyaratan jika ada --}}
                @if($pengajuanSurat->jenisSurat->persyaratan)
                    <h5 class="mt-4">Kelengkapan Persyaratan</h5>
                    <ul class="list-group">
                        @foreach($pengajuanSurat->jenisSurat->persyaratan as $syarat)
                            <li class="list-group-item">
                                @if(is_array($pengajuanSurat->persyaratan_terpenuhi) && in_array($syarat, $pengajuanSurat->persyaratan_terpenuhi))
                                    <i class="fas fa-check-square text-success"></i>
                                @else
                                    <i class="far fa-square text-muted"></i>
                                @endif
                                {{ $syarat }}
                            </li>
                        @endforeach
                    </ul>
                @endif
                @if($pengajuanSurat->status_permohonan == 'Ditolak')
                <div class="alert alert-danger mt-4">
                    <h5><i class="icon fas fa-ban"></i> Alasan Penolakan</h5>
                    {{ $pengajuanSurat->catatan_penolakan }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Aksi & Preview Surat --}}
    <div class="col-md-5">
        <div class="card card-purple card-outline">
            <div class="card-header">
                <h3 class="card-title">Aksi & Preview</h3>
            </div>
            <div class="card-body">
                <p>Silakan review data di sebelah kiri. Jika semua sudah sesuai, Anda dapat menyetujui dan mencetak
                    surat.</p>

                <div class="mt-3">
                    {{-- Hanya tampilkan tombol jika statusnya belum disetujui/ditolak --}}
                    @if($pengajuanSurat->status_permohonan != 'Disetujui' && $pengajuanSurat->status_permohonan != 'Ditolak')
                        <form action="{{ route('pengajuan-surat.approve', $pengajuanSurat) }}" method="POST"
                            target="_blank">
                            @csrf
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-check"></i> Setujui & Cetak Surat
                            </button>
                        </form>
                        <button type="button" class="btn btn-danger btn-block mt-2" data-toggle="modal" data-target="#rejectModal">
                            <i class="fas fa-times"></i> Tolak Permohonan
                        </button>
                    @else
                        <p class="text-muted text-center">Permohonan ini sudah selesai diproses.</p>
                    @endif
                    <a href="{{ route('pengajuan-surat.index') }}" class="btn btn-secondary btn-block mt-2">Kembali ke
                        Daftar</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Tolak Permohonan Surat</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('pengajuan-surat.reject', $pengajuanSurat) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="catatan_penolakan">Alasan Penolakan</label>
                        <textarea name="catatan_penolakan" id="catatan_penolakan" class="form-control" rows="4" required placeholder="Contoh: Dokumen KTP tidak terbaca dengan jelas."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Permohonan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop