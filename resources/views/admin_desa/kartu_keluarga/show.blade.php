@extends('admin.master')

@section('title', 'Detail Kartu Keluarga - ' . $kartuKeluarga->nomor_kk)

@section('content_header')
    <h1 class="m-0 text-dark">Detail Kartu Keluarga</h1>
@stop

@section('content_main')
    <div class="row">
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <h3 class="profile-username text-center">No. {{ $kartuKeluarga->nomor_kk }}</h3>
                    <p class="text-muted text-center">Kepala Keluarga: {{ $kartuKeluarga->kepalaKeluarga->nama_lengkap ?? 'Belum Ditetapkan' }}</p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Alamat</b> <a class="float-right">{{ $kartuKeluarga->alamat }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Wilayah</b> <a class="float-right">RW {{ $kartuKeluarga->rw->nomor_rw ?? '-' }} / RT {{ $kartuKeluarga->rt->nomor_rt ?? '-' }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Jumlah Anggota</b> <a class="float-right">{{ $kartuKeluarga->wargas->count() }} Orang</a>
                        </li>
                    </ul>

                    <a href="{{ route('kartu-keluarga.anggota.index', ['subdomain' => request()->subdomain, 'kartu_keluarga' => $kartuKeluarga->id]) }}" class="btn btn-primary btn-block"><b><i class="fas fa-users"></i> Kelola Anggota Keluarga</b></a>
                    <a href="#" class="btn btn-warning btn-block mt-2" data-toggle="modal" data-target="#modalUpdateStatusMassal">
                        <b><i class="fas fa-people-arrows"></i> Ubah Status Massal</b>
                    </a>
                    <a href="{{ route('kartu-keluarga.index', ['subdomain' => request()->subdomain]) }}" class="btn btn-secondary btn-block mt-2"><b><i class="fas fa-arrow-left"></i> Kembali</b></a>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Anggota Keluarga</h3>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nama Lengkap</th>
                                <th>NIK</th>
                                <th>Hubungan</th>
                                <th>Status</th>
                                <th style="width: 40px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($kartuKeluarga->wargas as $anggota)
                                <tr>
                                    <td>{{ $anggota->nama_lengkap }}</td>
                                    <td>{{ $anggota->nik }}</td>
                                    <td><span class="badge badge-info">{{ $anggota->hubunganKeluarga->nama ?? '' }}</span></td>
                                    <td><span class="badge badge-success">{{ $anggota->statusKependudukan->nama ?? '' }}</span></td>
                                    <td>
                                        {{-- Link ke detail warga individu --}}
                                        <a href="{{ route('warga.show', ['subdomain' => request()->subdomain, 'warga' => $anggota->id]) }}" class="btn btn-xs btn-primary"><i class="fas fa-eye"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada anggota keluarga yang ditambahkan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalUpdateStatusMassal" tabindex="-1" role="dialog" aria-labelledby="modalUpdateStatusMassalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUpdateStatusMassalLabel">Ubah Status Seluruh Anggota Keluarga</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('kartu-keluarga.update-status-anggota', ['subdomain' => request()->subdomain, 'kartu_keluarga' => $kartuKeluarga->id]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <strong>Perhatian!</strong> Aksi ini akan mengubah status kependudukan <strong>semua anggota</strong> dalam Kartu Keluarga ini.
                    </div>
                    <p>Pilih status baru untuk semua anggota KK No. <strong>{{ $kartuKeluarga->nomor_kk }}</strong>.</p>
                    <div class="form-group">
                        <label for="status_kependudukan_id_massal">Status Kependudukan</label>
                        <select name="status_kependudukan_id" id="status_kependudukan_id_massal" class="form-control" required>
                             <option value="" disabled selected>-- Pilih Status Baru --</option>
                            @foreach ($semuaStatus as $status)
                                <option value="{{ $status->id }}">
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