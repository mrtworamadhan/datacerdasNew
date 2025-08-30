@extends('admin.master')
@section('title', 'Detail Kegiatan')
@section('content_header')<h1 class="m-0 text-dark">Detail Kegiatan: {{ $kegiatan->nama_kegiatan }}</h1>@stop

@section('content_main')
<div class="card card-primary card-tabs">
    <div class="card-header p-0 pt-1">
        <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
            <li class="nav-item"><a class="nav-link" id="tab-proposal-tab" data-toggle="pill" href="#tab-proposal" 
                    role="tab">Detail Proposal</a></li>
            <li class="nav-item"><a class="nav-link active" id="tab-keuangan-tab" data-toggle="pill" href="#tab-keuangan"
                    role="tab">Laporan Keuangan</a></li>
            <li class="nav-item"><a class="nav-link" id="tab-lpj-tab" data-toggle="pill" href="#tab-lpj"
                    role="tab">LPJ</a></li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="custom-tabs-one-tabContent">
            <div class="tab-pane fade" id="tab-proposal" role="tabpanel">
                <h4>{{ $kegiatan->nama_kegiatan }}</h4>
                <p><strong>Penyelenggara:</strong>
                    {{ $kegiatan->kegiatanable->nama_lembaga ?? $kegiatan->kegiatanable->nama_kelompok }}</p>
                <p><strong>Tanggal:</strong> {{ $kegiatan->tanggal_kegiatan->format('d M Y') }}</p>
                <a href="{{ route('kegiatans.cetakProposal', $kegiatan->id) }}" class="btn btn-danger mb-3"
                    target="_blank">
                    <i class="fas fa-file-pdf"></i> Cetak Proposal (PDF)
                </a>
                <hr>
                <strong>Latar Belakang:</strong>
                <p>{!! nl2br(e($kegiatan->latar_belakang)) !!}</p>
                <hr>
                <strong>Tujuan Kegiatan:</strong>
                <p>{!! nl2br(e($kegiatan->tujuan_kegiatan)) !!}</p>
                <hr>
                <strong>Deskripsi Kegiatan:</strong>
                <p>{!! nl2br(e($kegiatan->deskripsi_kegiatan)) !!}</p>
                <hr>
                <strong>Rencana Anggaran:</strong>
                <p>{!! nl2br(e($kegiatan->laporan_dana)) !!}</p>
                <hr>
                <strong>Penutup:</strong>
                <p>{!! nl2br(e($kegiatan->penutup)) !!}</p>
            </div>

            <div class="tab-pane fade show active" id="tab-keuangan" role="tabpanel">
                <button type="button" class="btn btn-success mb-3" data-toggle="modal"
                    data-target="#tambahPengeluaranModal">
                    <i class="fas fa-plus"></i> Tambah Catatan Pengeluaran
                </button>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Uraian</th>
                            <th>Tipe</th>
                            <th>Jumlah</th>
                            <th>Dokumen</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kegiatan->pengeluarans as $pengeluaran)
                            <tr>
                                <td>{{ $pengeluaran->tanggal_transaksi->format('d M Y') }}</td>
                                <td>{{ $pengeluaran->uraian }}</td>
                                <td><span class="badge badge-secondary">{{ $pengeluaran->tipe_pengeluaran }}</span></td>
                                <td class="text-right">Rp {{ number_format($pengeluaran->jumlah, 0, ',', '.') }}</td>
                                <td>
                                    @if($pengeluaran->tipe_pengeluaran == 'Pembelian Pesanan')
                                        <a href="{{ route('cetak.surat-pesanan', $pengeluaran->id) }}"
                                            class="btn btn-xs btn-info" target="_blank" title="Cetak Surat Pesanan">
                                            <i class="fas fa-file-invoice"></i>
                                        </a>
                                        <a href="{{ route('cetak.kwitansi', $pengeluaran->id) }}" class="btn btn-xs btn-success"
                                            target="_blank" title="Cetak Kwitansi">
                                            <i class="fas fa-receipt"></i>
                                        </a>
                                        <a href="{{ route('cetak.berita-acara', $pengeluaran->id) }}"
                                            class="btn btn-xs btn-primary" target="_blank" title="Cetak Berita Acara">
                                            <i class="fas fa-list-check"></i>
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" class="btn btn-xs btn-warning edit-pengeluaran-btn" 
                                                data-url="{{ route('pengeluarans.edit', $pengeluaran->id) }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <form action="{{ route('pengeluarans.destroy', $pengeluaran->id) }}" method="POST"
                                            style="margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Yakin ingin menghapus catatan pengeluaran ini?')">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada catatan pengeluaran.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- TAB 3: LPJ --}}
            <div class="tab-pane fade" id="tab-lpj" role="tabpanel">
                @if($kegiatan->lpj)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Laporan Pertanggungjawaban (LPJ)</h5>
                        <div>
                            <a href="{{ route('lpjs.edit', $kegiatan->lpj->id) }}" class="btn btn-warning"><i
                                    class="fas fa-edit"></i> Edit LPJ</a>
                            <a href="{{ route('lpj.generate', $kegiatan->id) }}" class="btn btn-danger" target="_blank"><i
                                    class="fas fa-file-pdf"></i> Cetak LPJ</a>
                        </div>
                    </div>
                    <hr>
                    <strong>Hasil Kegiatan:</strong>
                    <div class="p-2 bg-light border rounded mb-3" style="white-space: pre-wrap;">
                        {{ $kegiatan->lpj->hasil_kegiatan }}</div>

                    <strong>Evaluasi & Kendala:</strong>
                    <div class="p-2 bg-light border rounded" style="white-space: pre-wrap;">
                        {{ $kegiatan->lpj->evaluasi_kendala }}</div>
                @else
                    <div class="text-center p-4">
                        <p>Laporan Pertanggungjawaban (LPJ) untuk kegiatan ini belum dibuat. Silakan lengkapi laporan
                            keuangan terlebih dahulu.</p>
                        <a href="{{ route('lpjs.create', $kegiatan->id) }}" class="btn btn-lg btn-success">
                            <i class="fas fa-plus-circle"></i> Buat LPJ Sekarang
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="tambahPengeluaranModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('pengeluarans.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="kegiatan_id" value="{{ $kegiatan->id }}">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Catatan Pengeluaran</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tipe Pengeluaran</label>
                        <select name="tipe_pengeluaran" id="tipe_pengeluaran" class="form-control" required>
                            <option value="Biasa">Biasa (Contoh: ATK, konsumsi rapat)</option>
                            <option value="Pembelian Pesanan">Pembelian Pesanan (Barang/Jasa)</option>
                            <option value="Upah Kerja">Pembayaran Upah Kerja</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_transaksi">Tanggal Transaksi</label>
                        <input type="date" name="tanggal_transaksi" class="form-control" value="{{ date('Y-m-d') }}"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="uraian">Uraian Pengeluaran</label>
                        <input type="text" name="uraian" class="form-control"
                            placeholder="Contoh: Pembelian spanduk kegiatan" required>
                    </div>
                    <div class="form-group">
                        <label for="jumlah">Jumlah Total (Rp)</label>
                        <input type="number" name="jumlah" id="jumlah_total" class="form-control" required>
                    </div>
                    <hr>
                    <div id="form-pembelian-pesanan" style="display: none;">
                        <h5>Detail Pembelian Pesanan</h5>
                        <div class="form-group">
                            <label for="tanggal_pesanan">Tanggal Pesanan</label>
                            <input type="date" name="tanggal_pesanan" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="penyedia">Penyedia (Nama Toko/Jasa)</label>
                            <input type="text" name="penyedia" class="form-control"
                                placeholder="Contoh: Percetakan Jaya Abadi">
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="nama_pemesan">Nama Pemesan</label>
                                <input type="text" name="nama_pemesan" class="form-control" placeholder="Nama yang ttd di Surat Pesanan">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="nama_penerima">Nama Penerima Barang</label>
                                <input type="text" name="nama_penerima" class="form-control" placeholder="Nama yang ttd di Berita Acara">
                            </div>
                        </div>
                        {{-- BAGIAN BARU UNTUK RINCIAN BARANG --}}
                        <h6>Rincian Barang/Jasa yang Dipesan</h6>
                        <div id="rincian-barang-wrapper">
                            <div class="row rincian-barang-item mb-2 align-items-center">
                                <div class="col-4"><input type="text" name="detail_barang[0][nama_barang]"
                                        class="form-control form-control-sm" placeholder="Nama Barang"></div>
                                <div class="col-2"><input type="number" step="0.1" name="detail_barang[0][volume]"
                                        class="form-control form-control-sm" placeholder="Volume"></div>
                                <div class="col-2"><input type="text" name="detail_barang[0][satuan]"
                                        class="form-control form-control-sm" placeholder="Satuan"></div>
                                <div class="col-3"><input type="number" name="detail_barang[0][harga_satuan]"
                                        class="form-control form-control-sm" placeholder="Harga Satuan"></div>
                                <div class="col-1"><button type="button"
                                        class="btn btn-sm btn-danger remove-rincian-btn"
                                        style="display: none;">&times;</button></div>
                            </div>
                        </div>
                        <button type="button" id="tambah-rincian-btn" class="btn btn-sm btn-secondary mt-2">+ Tambah
                            Baris</button>
                    </div>
                    <div id="form-upah-kerja" style="display: none;">
                        <h5>Detail Upah Kerja</h5>
                        <div class="form-group">
                            <label for="nama_pekerja">Nama Penerima Upah</label>
                            <input type="text" name="nama_pekerja" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="tanda_tangan_path">Upload KTP</label>
                            <input type="file" name="tanda_tangan_path" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan (Opsional)</label>
                        <textarea name="keterangan" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="editPengeluaranModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            {{-- Konten form edit akan dimuat di sini oleh JavaScript --}}
        </div>
    </div>
</div>
@stop
@push('js')
    <script>
        $(document).ready(function () {
            // Fungsi untuk menampilkan form yang sesuai
            function toggleFormFields() {
                var selectedType = $('#tipe_pengeluaran').val();

                $('#form-pembelian-pesanan').hide();
                $('#form-upah-kerja').hide();

                if (selectedType === 'Pembelian Pesanan') {
                    $('#form-pembelian-pesanan').show();
                } else if (selectedType === 'Upah Kerja') {
                    $('#form-upah-kerja').show();
                }
            }

            $('#tipe_pengeluaran').on('change', function () {
                toggleFormFields();
            });

            $('#tambahPengeluaranModal').on('shown.bs.modal', function () {
                toggleFormFields();
            });

            let rincianIndex = 1; 

            $('#tambah-rincian-btn').on('click', function () {
                const newRow = `
                                    <div class="row rincian-barang-item mb-2 align-items-center">
                                        <div class="col-4"><input type="text" name="detail_barang[${rincianIndex}][nama_barang]" class="form-control form-control-sm" placeholder="Nama Barang"></div>
                                        <div class="col-2"><input type="number" step="0.1" name="detail_barang[${rincianIndex}][volume]" class="form-control form-control-sm" placeholder="Volume"></div>
                                        <div class="col-2"><input type="text" name="detail_barang[${rincianIndex}][satuan]" class="form-control form-control-sm" placeholder="Satuan"></div>
                                        <div class="col-3"><input type="number" name="detail_barang[${rincianIndex}][harga_satuan]" class="form-control form-control-sm" placeholder="Harga Satuan"></div>
                                        <div class="col-1"><button type="button" class="btn btn-sm btn-danger remove-rincian-btn">&times;</button></div>
                                    </div>
                                `;
                $('#rincian-barang-wrapper').append(newRow);
                rincianIndex++;
            });

            $('#rincian-barang-wrapper').on('click', '.remove-rincian-btn', function () {
                $(this).closest('.rincian-barang-item').remove();
            });

            const rincianWrapper = $('#rincian-barang-wrapper');
            const totalInput = $('#jumlah_total');

            function calculateTotal() {
                let grandTotal = 0;
                $('.rincian-barang-item').each(function () {
                    const row = $(this);
                    const volume = parseFloat(row.find('input[name*="[volume]"]').val()) || 0;
                    const hargaSatuan = parseFloat(row.find('input[name*="[harga_satuan]"]').val()) || 0;
                    const subTotal = volume * hargaSatuan;
                    grandTotal += subTotal;
                });
                totalInput.val(grandTotal);
            }

            rincianWrapper.on('input', 'input[name*="[volume]"], input[name*="[harga_satuan]"]', function () {
                calculateTotal();
            });

            $('#tambah-rincian-btn').on('click', function () {
            });

            rincianWrapper.on('click', '.remove-rincian-btn', function () {
                setTimeout(calculateTotal, 100); 
            });

            $('#tambahPengeluaranModal').on('shown.bs.modal', function () {
                calculateTotal();
            });

            $('.edit-pengeluaran-btn').on('click', function () {
                var url = $(this).data('url');
                var modal = $('#editPengeluaranModal'); 
                var modalContent = modal.find('.modal-content');

                modalContent.html('<div class="modal-body text-center"><p>Memuat data...</p></div>');
                modal.modal('show');

                $.get(url, function (data) {
                    modalContent.html(data);
                }).fail(function() {
                    modalContent.html('<div class="modal-body text-center"><p class="text-danger">Gagal memuat data.</p></div>');
                });
            });
        });
    </script>
    
@endpush