@extends('admin.master')

@section('title', 'Penerima Bantuan ' . $kategoriBantuan->nama_kategori . ' - TataDesa')

@section('content_header')
    <h1 class="m-0 text-dark">Penerima Bantuan: {{ $kategoriBantuan->nama_kategori }}</h1>
@stop

@section('content_main')
    {{-- Info Cards --}}
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
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['diajukan'] }}</h3>
                    <p>Pengajuan Diajukan</p>
                </div>
                <div class="icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['disetujui'] }}</h3>
                    <p>Pengajuan Disetujui</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['ditolak'] }}</h3>
                    <p>Pengajuan Ditolak</p>
                </div>
                <div class="icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Penerima Bantuan</h3>
            <div class="card-tools">
                {{-- Tombol Ajukan Penerima hanya untuk Admin Desa, RW, RT jika kategori aktif --}}
                @if ($kategoriBantuan->is_active_for_submission && (Auth::user()->isAdminDesa() || Auth::user()->isAdminRw() || Auth::user()->isAdminRt()))
                    <a href="{{ route('kategori-bantuan.penerima.create', $kategoriBantuan) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Ajukan Penerima
                    </a>
                @endif
                {{-- Tombol Export hanya untuk Admin Desa/Super Admin --}}
                @if (Auth::user()->isAdminDesa() || Auth::user()->isSuperAdmin())
                    <div class="btn-group ml-2">
                        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Export
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{ route('kategori-bantuan.penerima.exportPdf', $kategoriBantuan) }}">PDF</a>
                            <a class="dropdown-item" href="{{ route('kategori-bantuan.penerima.exportExcel', $kategoriBantuan) }}">Excel</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            @if (session('success'))
                <div class="alert alert-success m-3">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger m-3">
                    {{ session('error') }}
                </div>
            @endif
            <table class="table table-striped table-valign-middle">
                <thead>
                    <tr>
                        <th>Nama Penerima</th>
                        <th>NIK/Nomor KK</th>
                        <th>RW/RT</th>
                        <th>Diajukan Oleh</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($penerimaBantuans as $penerima)
                        <tr>
                            <td>
                                @if ($penerima->warga)
                                    {{ $penerima->warga->nama_lengkap }}
                                @elseif ($penerima->kartuKeluarga)
                                    KK: {{ $penerima->kartuKeluarga->nomor_kk }} (Kepala: {{ $penerima->kartuKeluarga->kepalaKeluarga->nama_lengkap ?? '-' }})
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($penerima->warga)
                                    {{ $penerima->warga->nik }}
                                @elseif ($penerima->kartuKeluarga)
                                    {{ $penerima->kartuKeluarga->nomor_kk }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($penerima->warga)
                                    RW {{ $penerima->warga->rw->nomor_rw ?? '-' }}/RT {{ $penerima->warga->rt->nomor_rt ?? '-' }}
                                @elseif ($penerima->kartuKeluarga)
                                    RW {{ $penerima->kartuKeluarga->rw->nomor_rw ?? '-' }}/RT {{ $penerima->kartuKeluarga->rt->nomor_rt ?? '-' }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $penerima->diajukanOleh->name ?? '-' }} ({{ ucfirst(str_replace('_', ' ', $penerima->diajukanOleh->user_type ?? '-')) }})</td>
                            <td>{{ $penerima->tanggal_menerima->format('d M Y') }}</td>
                            <td>
                                @php
                                    $badgeClass = 'secondary';
                                    if ($penerima->status_permohonan == 'Diajukan') $badgeClass = 'warning';
                                    elseif ($penerima->status_permohonan == 'Disetujui') $badgeClass = 'success';
                                    elseif ($penerima->status_permohonan == 'Ditolak') $badgeClass = 'danger';
                                    elseif ($penerima->status_permohonan == 'Diverifikasi RT' || $penerima->status_permohonan == 'Diverifikasi RW') $badgeClass = 'info';
                                @endphp
                                <span class="badge badge-{{ $badgeClass }}">{{ $penerima->status_permohonan }}</span>
                            </td>
                            <td>
                                {{-- Admin Desa bisa melihat detail untuk approve/reject --}}
                                @if (Auth::user()->isAdminDesa())
                                    <a href="{{ route('kategori-bantuan.penerima.show', [$kategoriBantuan, $penerima]) }}" class="btn btn-info btn-xs">Verifikasi</a>
                                @else
                                    {{-- Admin RW/RT hanya melihat detail --}}
                                    <a href="{{ route('kategori-bantuan.penerima.show', [$kategoriBantuan, $penerima]) }}" class="btn btn-info btn-xs">Detail</a>
                                @endif

                                {{-- Hanya Admin Desa/Super Admin yang bisa menghapus --}}
                                @if (Auth::user()->isAdminDesa() || Auth::user()->isSuperAdmin())
                                    <form action="{{ route('kategori-bantuan.penerima.destroy', [$kategoriBantuan, $penerima]) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Yakin ingin menghapus penerima bantuan ini?')">Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada penerima bantuan untuk kategori ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <a href="{{ route('kategori-bantuan.index') }}" class="btn btn-secondary">Kembali ke Daftar Kategori Bantuan</a>
        </div>
    </div>
@endsection
