@extends('admin.master')

@section('title', 'Detail Penerima Bantuan - TataDesa')

@section('content_header')
    <h1 class="m-0 text-dark">Detail Penerima Bantuan</h1>
@stop

@section('content_main')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detail Pengajuan Penerima Bantuan untuk {{ $kategoriBantuan->nama_kategori }}</h3>
            <div class="card-tools">
                <a href="{{ route('kategori-bantuan.penerima.index', $kategoriBantuan) }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="row">
                <div class="col-md-6">
                    <p><strong>Kategori Bantuan:</strong> {{ $kategoriBantuan->nama_kategori }}</p>
                    <p><strong>Nama Penerima:</strong> 
                        @if ($penerimaBantuan->warga)
                            {{ $penerimaBantuan->warga->nama_lengkap }} (NIK: {{ $penerimaBantuan->warga->nik }})
                        @elseif ($penerimaBantuan->kartuKeluarga)
                            KK: {{ $penerimaBantuan->kartuKeluarga->nomor_kk }} (Kepala: {{ $penerimaBantuan->kartuKeluarga->kepalaKeluarga->nama_lengkap ?? '-' }})
                        @endif
                    </p>
                    <p><strong>Diajukan Oleh:</strong> {{ $penerimaBantuan->diajukanOleh->name ?? '-' }} ({{ ucfirst(str_replace('_', ' ', $penerimaBantuan->diajukanOleh->user_type ?? '-')) }})</p>
                    <p><strong>Tanggal Pengajuan:</strong> {{ $penerimaBantuan->tanggal_menerima->format('d M Y') }}</p>
                    <p><strong>Keterangan:</strong> {{ $penerimaBantuan->keterangan ?? '-' }}</p>
                    <p><strong>Status Permohonan:</strong> 
                        @php
                            $badgeClass = 'secondary';
                            if ($penerimaBantuan->status_permohonan == 'Diajukan') $badgeClass = 'warning';
                            elseif ($penerimaBantuan->status_permohonan == 'Disetujui') $badgeClass = 'success';
                            elseif ($penerimaBantuan->status_permohonan == 'Ditolak') $badgeClass = 'danger';
                            elseif ($penerimaBantuan->status_permohonan == 'Diverifikasi RT' || $penerimaBantuan->status_permohonan == 'Diverifikasi RW') $badgeClass = 'info';
                        @endphp
                        <span class="badge badge-{{ $badgeClass }}">{{ $penerimaBantuan->status_permohonan }}</span>
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>RW/RT:</strong> 
                        @if ($penerimaBantuan->warga)
                            RW {{ $penerimaBantuan->warga->rw->nomor_rw ?? '-' }}/RT {{ $penerimaBantuan->warga->rt->nomor_rt ?? '-' }}
                        @elseif ($penerimaBantuan->kartuKeluarga)
                            RW {{ $penerimaBantuan->kartuKeluarga->rw->nomor_rw ?? '-' }}/RT {{ $penerimaBantuan->kartuKeluarga->rt->nomor_rt ?? '-' }}
                        @endif
                    </p>
                    <p><strong>Tanggal Verifikasi:</strong> {{ $penerimaBantuan->tanggal_verifikasi ? $penerimaBantuan->tanggal_verifikasi->format('d M Y H:i') : '-' }}</p>
                    <p><strong>Diverifikasi Oleh:</strong> {{ $penerimaBantuan->disetujuiOleh->name ?? '-' }}</p>
                    <p><strong>Catatan Verifikasi:</strong> {{ $penerimaBantuan->catatan_persetujuan_penolakan ?? '-' }}</p>
                </div>
            </div>

            @php
                $canUpdateStatus = false;
                $allowedStatuses = [];

                if (Auth::user()->isAdminDesa() && $penerimaBantuan->status_permohonan !== 'Disetujui' && $penerimaBantuan->status_permohonan !== 'Ditolak') {
                    $canUpdateStatus = true;
                    $allowedStatuses = ['Disetujui', 'Ditolak'];
                } elseif (Auth::user()->isAdminRw() && ($penerimaBantuan->status_permohonan === 'Diajukan' || $penerimaBantuan->status_permohonan === 'Diverifikasi RT')) {
                    // Admin RW bisa verifikasi jika statusnya Diajukan atau Diverifikasi RT
                    $canUpdateStatus = true;
                    $allowedStatuses = ['Diverifikasi RW'];
                } elseif (Auth::user()->isAdminRt() && $penerimaBantuan->status_permohonan === 'Diajukan') {
                    // Admin RT hanya bisa verifikasi jika statusnya Diajukan
                    $canUpdateStatus = true;
                    $allowedStatuses = ['Diverifikasi RT'];
                }
            @endphp

            @if ($canUpdateStatus)
                <hr>
                <h4>Verifikasi Penerima Bantuan</h4>
                <form action="{{ route('kategori-bantuan.penerima.update-status', [$kategoriBantuan, $penerimaBantuan]) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="status">Pilih Status</label>
                        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                            <option value="">-- Pilih Status --</option>
                            @foreach($allowedStatuses as $statusOption)
                                <option value="{{ $statusOption }}" {{ old('status') == $statusOption ? 'selected' : '' }}>{{ $statusOption }}</option>
                            @endforeach
                        </select>
                        @error('status') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group" id="catatan_group" style="display: {{ old('status') == 'Ditolak' ? 'block' : 'none' }};">
                        <label for="catatan">Catatan (Opsional, Wajib jika Ditolak)</label>
                        <textarea name="catatan" id="catatan" class="form-control @error('catatan') is-invalid @enderror" rows="3">{{ old('catatan') }}</textarea>
                        @error('catatan') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </form>
            @else
                <p class="text-muted mt-4">Penerima bantuan ini sudah diverifikasi atau Anda tidak memiliki hak akses untuk memperbarui status.</p>
            @endif

        </div>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const statusSelect = document.getElementById('status');
            const catatanGroup = document.getElementById('catatan_group');
            const catatanTextarea = document.getElementById('catatan');

            function toggleCatatanField() {
                if (statusSelect.value === 'Ditolak') {
                    catatanGroup.style.display = 'block';
                    catatanTextarea.setAttribute('required', 'required');
                } else {
                    catatanGroup.style.display = 'none';
                    catatanTextarea.removeAttribute('required');
                }
            }

            if (statusSelect) {
                statusSelect.addEventListener('change', toggleCatatanField);
                // Panggil saat halaman dimuat untuk inisialisasi awal
                toggleCatatanField(); 
            }
        });
    </script>
@stop
