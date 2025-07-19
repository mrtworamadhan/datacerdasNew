<div class="card-body table-responsive p-0">
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Pemohon</th>
                <th>Jenis Surat</th>
                <th>Tgl. Selesai</th>
                <th>Nomor Surat</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pengajuansFinished as $pengajuan)
                <tr>
                    <td>{{ $loop->iteration + $pengajuansFinished->firstItem() - 1 }}</td>
                    <td>
                        <strong>{{ $pengajuan->warga->nama_lengkap ?? 'N/A' }}</strong><br>
                        <small>NIK: {{ $pengajuan->warga->nik ?? 'N/A' }}</small>
                    </td>
                    <td>{{ $pengajuan->jenisSurat->nama_surat ?? 'N/A' }}</td>
                    <td>{{ $pengajuan->tanggal_selesai ? $pengajuan->tanggal_selesai->translatedFormat('d F Y') : '-' }}</td>
                    <td>{{ $pengajuan->nomor_surat ?? '-' }}</td>
                    <td>
                        @if($pengajuan->status_permohonan == 'Disetujui') <span class="badge badge-success">Disetujui</span>
                        @elseif($pengajuan->status_permohonan == 'Ditolak') <span class="badge badge-danger">Ditolak</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('pengajuan-surat.show', $pengajuan) }}" class="btn btn-secondary btn-sm" title="Lihat Detail"><i class="fas fa-search"></i></a>
                        @if($pengajuan->status_permohonan == 'Disetujui')
                        <a href="{{ route('pengajuan-surat.reprint', $pengajuan) }}" target="_blank" class="btn btn-primary btn-sm" title="Cetak Ulang Arsip"><i class="fas fa-print"></i></a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada riwayat permohonan yang cocok.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="card-footer clearfix">
    {{ $pengajuansFinished->links() }}
</div>