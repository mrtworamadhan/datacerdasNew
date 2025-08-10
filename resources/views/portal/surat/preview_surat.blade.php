@extends('layouts.portal')
@section('title', 'Pratinjau Surat')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Pratinjau Surat: {{ $pengajuanSurat->jenisSurat->nama_surat }}</h3>
    </div>
    <div class="card-body">
        <p class="text-center alert alert-warning">Mohon periksa kembali semua data Anda di bawah ini sebelum melanjutkan ke halaman cetak.</p>
        <div style="border: 1px solid #ddd; padding: 1rem; font-family: 'Times New Roman', serif;">
            <p><strong>Nama:</strong> {{ $pengajuanSurat->warga->nama_lengkap }}</p>
            <p><strong>NIK:</strong> {{ $pengajuanSurat->warga->nik }}</p>
            <p><strong>Keperluan:</strong> {{ $pengajuanSurat->detail_tambahan['keperluan'] ?? '' }}</p>
            @foreach($pengajuanSurat->detail_tambahan as $key => $value)
                @if($key === 'tabel_ahli_waris' && is_array($value))
                    <div class="mt-4">
                        <h5><strong>{{ Str::title(str_replace('_', ' ', $key)) }}:</strong></h5>
                        <table class="table table-bordered mt-2">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Hubungan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($value as $item)
                                    <tr>
                                        <td>{{ $item['nama'] ?? '-' }}</td>
                                        <td>{{ $item['hubungan'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @elseif($key !== 'keperluan' && !is_array($value))
                    <p><strong>{{ Str::title(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</p>
                @endif
            @endforeach
        </div>
    </div>
    <div class="card-footer text-center">
        <button id="final-print-btn" data-print-url="{{ route('portal.surat.store', ['subdomain' => app('tenant')->subdomain, 'pengajuanSurat' => $pengajuanSurat->id]) }}" class="btn btn-primary btn-lg">
            Data Sudah Benar, Ajukan Surat!
        </button>
        <a href="{{ route('portal.surat.pilihSurat', ['subdomain' => app('tenant')->subdomain]) }}" class="btn btn-secondary btn-lg">Batalkan & Kembali</a>
    </div>
</div>
@stop

@push('js')
<!-- <script>
$(document).ready(function() {
    $('#final-print-btn').on('click', function(e) {
        e.preventDefault();

        const printUrl = $(this).data('print-url');
        const backUrl = "{{ route('portal.surat.index', ['subdomain' => app('tenant')->subdomain]) }}";
        const button = $(this);

        button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menyiapkan dokumen...');

        // 1. Ambil konten HTML dari halaman cetak
        fetch(printUrl)
            .then(response => response.text())
            .then(html => {
                // 2. Buka jendela pop-up baru yang kosong
                const printWindow = window.open('', '_blank', 'height=800,width=900');
                
                // 3. Tulis konten HTML ke dalam jendela baru
                printWindow.document.write(html);
                printWindow.document.close(); // Penting untuk mengakhiri proses penulisan

                // 4. Tunggu sebentar agar semua konten (termasuk gambar) sempat dirender
                setTimeout(function () {
                    printWindow.focus(); // Fokus ke jendela baru
                    printWindow.print(); // Picu dialog print
                    
                    // Kita tidak bisa tahu pasti kapan user menutup dialog print,
                    // jadi kita asumsikan setelah beberapa saat, proses selesai.
                    setTimeout(function() {
                        printWindow.close(); // Tutup jendela pop-up
                        window.location.href = backUrl; // Arahkan halaman utama kembali
                    }, 2000); // Beri jeda 2 detik

                }, 500); // Jeda 0.5 detik untuk rendering
            })
            .catch(error => {
                console.error('Gagal mengambil halaman cetak:', error);
                alert('Gagal menyiapkan dokumen untuk dicetak. Silakan coba lagi.');
                button.prop('disabled', false).html('<i class="fas fa-print"></i> Data Sudah Benar, Lanjutkan Cetak');
            });
    });
});
</script> -->
@endpush