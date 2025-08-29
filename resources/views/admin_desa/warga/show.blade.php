@extends('admin.master')

@section('title', 'Detail Warga - ' . $warga->nama_lengkap)

@section('content_header')
<h1 class="m-0 text-dark">Detail Warga</h1>
@stop

@section('content_main')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <a href="javascript:history.back()"
                    class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
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
                                    <dd class="col-sm-7">{{ $warga->nama_lengkap }}</dd>

                                    <dt class="col-sm-5">NIK</dt>
                                    <dd class="col-sm-7">{{ $warga->nik }}</dd>

                                    <dt class="col-sm-5">No. Kartu Keluarga</dt>
                                    <dd class="col-sm-7">{{ $warga->kartuKeluarga->nomor_kk ?? '-' }}</dd>

                                    <dt class="col-sm-5">Status Kependudukan</dt>
                                    <dd class="col-sm-7"><span
                                            class="badge badge-info">{{ $warga->statusKependudukan->nama ?? '-' }}</span>
                                    </dd>

                                    <dt class="col-sm-5">Tempat, Tgl Lahir</dt>
                                    <dd class="col-sm-7">{{ $warga->tempat_lahir }},
                                        {{ $warga->tanggal_lahir->format('d M Y') }}</dd>

                                    <dt class="col-sm-5">Alamat</dt>
                                    <dd class="col-sm-7">{{ $warga->alamat_lengkap }}</dd>

                                    <dt class="col-sm-5">Wilayah</dt>
                                    <dd class="col-sm-7">RW {{ $warga->rw->nomor_rw ?? '-' }} / RT
                                        {{ $warga->rt->nomor_rt ?? '-' }}</dd>

                                    <dt class="col-sm-5">Pendidikan</dt>
                                    <dd class="col-sm-7">{{ $warga->pendidikan->nama ?? '-' }}</dd>

                                    <dt class="col-sm-5">Pekerjaan</dt>
                                    <dd class="col-sm-7">{{ $warga->pekerjaan->nama ?? '-' }}</dd>
                                </dl>
                                <a href="#" class="btn btn-primary btn-block" data-toggle="modal"
                                    data-target="#modalUpdateStatus">
                                    <b><i class="fas fa-edit"></i> Ubah Status Kependudukan</b>
                                </a>
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
                                            <th>Dicatat Oleh</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($warga->logKependudukan as $log)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($log->tanggal_peristiwa)->format('d M Y') }}
                                                </td>
                                                <td><span class="badge bg-success">{{ $log->jenis_peristiwa }}</span></td>
                                                <td>{{ $log->keterangan }}</td>
                                                <td>{{ $log->pencatat->name ?? 'Sistem' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">Belum ada riwayat peristiwa.</td>
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
<div class="modal fade" id="modalUpdateStatus" tabindex="-1" role="dialog" aria-labelledby="modalUpdateStatusLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUpdateStatusLabel">Ubah Status Kependudukan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form
                action="{{ route('warga.update-status', ['subdomain' => request()->subdomain, 'warga' => $warga->id]) }}"
                method="POST">
                @csrf
                <div class="modal-body">
                    <p>Pilih status baru untuk <strong>{{ $warga->nama_lengkap }}</strong>.</p>
                    <div class="form-group">
                        <label for="status_kependudukan_id">Status Kependudukan</label>
                        <select name="status_kependudukan_id" id="status_kependudukan_id" class="form-control" required>
                            @foreach ($semuaStatus as $status)
                                <option value="{{ $status->id }}" {{ $warga->status_kependudukan_id == $status->id ? 'selected' : '' }}>
                                    {{ $status->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop