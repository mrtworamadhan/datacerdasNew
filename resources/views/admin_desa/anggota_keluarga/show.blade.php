@extends('admin.master')

@section('title', 'Detail Anggota Keluarga - ' . $anggota->nama_lengkap)

@section('content_header')
    <h1 class="m-0 text-dark">Detail Anggota Keluarga</h1>
@stop

@section('content_main')
    <div class="row">
        <div class="col-12">
            <div class="card">
                 <div class="card-header">
                     <a href="{{ route('kartu-keluarga.anggota.index', ['subdomain' => request()->subdomain, 'kartuKeluarga' => $kartuKeluarga->id]) }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Anggota
                    </a>
                 </div>
                <div class="card-body">
                   <div class="row">
                        <div class="col-md-6">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-user"></i> Profil Lengkap
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <dl class="row">
                                        <dt class="col-sm-5">Nama Lengkap</dt>
                                        <dd class="col-sm-7">{{ $anggota->nama_lengkap }}</dd>

                                        <dt class="col-sm-5">NIK</dt>
                                        <dd class="col-sm-7">{{ $anggota->nik }}</dd>
                                        
                                        <dt class="col-sm-5">Hubungan Keluarga</dt>
                                        <dd class="col-sm-7"><span class="badge badge-primary">{{ $anggota->hubunganKeluarga->nama ?? '-' }}</span></dd>
                                        
                                        <dt class="col-sm-5">Status Kependudukan</dt>
                                        <dd class="col-sm-7"><span class="badge badge-info">{{ $anggota->statusKependudukan->nama ?? '-' }}</span></dd>
                                        
                                        <dt class="col-sm-5">Tempat, Tgl Lahir</dt>
                                        <dd class="col-sm-7">{{ $anggota->tempat_lahir }}, {{ $anggota->tanggal_lahir->format('d M Y') }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                             <div class="card card-success card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-history"></i> Riwayat Peristiwa Kependudukan
                                    </h3>
                                </div>
                                <div class="card-body p-0 table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th style="width: 130px">Tanggal</th>
                                                <th>Peristiwa</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($anggota->logKependudukan as $log)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($log->tanggal_peristiwa)->format('d M Y') }}</td>
                                                    <td><span class="badge bg-success">{{ $log->jenis_peristiwa }}</span></td>
                                                    <td>{{ $log->keterangan }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center">Belum ada riwayat peristiwa.</td>
                                                </tr>
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
    </div>
@stop