<form action="{{ route('pengeluarans.update', $pengeluaran->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="modal-header">
        <h5 class="modal-title">Edit Catatan Pengeluaran</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>
    <div class="modal-body">
        {{-- Form edit ini akan mirip dengan form tambah, tapi valuenya diisi --}}
        <div class="form-group">
            <label for="tanggal_transaksi">Tanggal Transaksi</label>
            <input type="date" name="tanggal_transaksi" class="form-control @error('tanggal_transaksi') is-invalid @enderror" value="{{ old('tanggal_transaksi', $pengeluaran->tanggal_transaksi->format('Y-m-d')) }}" required>
            @error('tanggal_transaksi') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
        <div class="form-group">
            <label>Uraian Pengeluaran</label>
            <input type="text" name="uraian" class="form-control" value="{{ $pengeluaran->uraian }}" required>
        </div>
        <div class="form-group">
            <label>Jumlah (Rp)</label>
            <input type="number" name="jumlah" class="form-control" value="{{ $pengeluaran->jumlah }}" required>
        </div>
        
        {{-- HANYA TAMPILKAN JIKA TIPENYA PEMBELIAN PESANAN --}}
        @if($pengeluaran->tipe_pengeluaran == 'Pembelian Pesanan')
        <hr>
        <h5>Detail Pembelian Pesanan</h5>
        <div class="form-group">
            <label for="penyedia">Penyedia (Nama Toko/Jasa)</label>
            <input type="text" name="penyedia" class="form-control" value="{{ $pengeluaran->penyedia }}">
        </div>
        <div class="row">
            <div class="col-md-6 form-group">
                <label for="nama_pemesan">Nama Pemesan</label>
                <input type="text" name="nama_pemesan" class="form-control" value="{{ old('nama_pemesan', $pengeluaran->nama_pemesan) }}" placeholder="Nama yang ttd di Surat Pesanan">
            </div>
            <div class="col-md-6 form-group">
                <label for="nama_penerima">Nama Penerima Barang</label>
                <input type="text" name="nama_penerima" class="form-control" value="{{ old('nama_penerima', $pengeluaran->nama_penerima) }}" placeholder="Nama yang ttd di Berita Acara">
            </div>
        </div>
        
        <h6>Rincian Barang/Jasa yang Dipesan</h6>
        <div id="edit-rincian-barang-wrapper">
            {{-- Loop untuk menampilkan rincian yang sudah ada --}}
            @forelse($pengeluaran->detailBarangs as $index => $item)
            <div class="row rincian-barang-item mb-2 align-items-center">
                <input type="hidden" name="detail_barang[{{ $index }}][id]" value="{{ $item->id }}">
                <div class="col-4"><input type="text" name="detail_barang[{{ $index }}][nama_barang]" class="form-control form-control-sm" placeholder="Nama Barang" value="{{ $item->nama_barang }}"></div>
                <div class="col-2"><input type="number" step="0.1" name="detail_barang[{{ $index }}][volume]" class="form-control form-control-sm" placeholder="Volume" value="{{ $item->volume }}"></div>
                <div class="col-2"><input type="text" name="detail_barang[{{ $index }}][satuan]" class="form-control form-control-sm" placeholder="Satuan" value="{{ $item->satuan }}"></div>
                <div class="col-3"><input type="number" name="detail_barang[{{ $index }}][harga_satuan]" class="form-control form-control-sm" placeholder="Harga Satuan" value="{{ $item->harga_satuan }}"></div>
                <div class="col-1"><button type="button" class="btn btn-sm btn-danger remove-rincian-btn">&times;</button></div>
            </div>
            @empty
            {{-- Baris pertama jika belum ada rincian --}}
            <div class="row rincian-barang-item mb-2 align-items-center">
                <div class="col-4"><input type="text" name="detail_barang[0][nama_barang]" class="form-control form-control-sm" placeholder="Nama Barang"></div>
                <div class="col-2"><input type="number" step="0.1" name="detail_barang[0][volume]" class="form-control form-control-sm" placeholder="Volume"></div>
                <div class="col-2"><input type="text" name="detail_barang[0][satuan]" class="form-control form-control-sm" placeholder="Satuan"></div>
                <div class="col-3"><input type="number" name="detail_barang[0][harga_satuan]" class="form-control form-control-sm" placeholder="Harga Satuan"></div>
                <div class="col-1"></div>
            </div>
            @endforelse
        </div>
        <button type="button" id="edit-tambah-rincian-btn" class="btn btn-sm btn-secondary mt-2">+ Tambah Baris</button>
        @endif
        
        {{-- Lanjutkan untuk field lain jika ada --}}
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-warning">Update</button>
    </div>
</form>
<script>
    // Inisialisasi indeks untuk baris baru, dihitung dari item yang sudah ada di modal ini
    let editRincianIndex = $('#edit-rincian-barang-wrapper .rincian-barang-item').length;
    
    // Fungsi kalkulasi yang menargetkan elemen di dalam modal ini
    function calculateTotalEdit() {
        let grandTotal = 0;
        $('#edit-rincian-barang-wrapper .rincian-barang-item').each(function() {
            const row = $(this);
            const volume = parseFloat(row.find('input[name*="[volume]"]').val()) || 0;
            const hargaSatuan = parseFloat(row.find('input[name*="[harga_satuan]"]').val()) || 0;
            grandTotal += (volume * hargaSatuan);
        });
        $('#jumlah_total_edit').val(grandTotal);
    }

    // Panggil kalkulasi saat pertama kali modal dimuat
    calculateTotalEdit();

    // Event listener untuk tombol tambah baris di modal ini
    $('#edit-tambah-rincian-btn').on('click', function() {
        const newRow = `
            <div class="row rincian-barang-item mb-2 align-items-center">
                <input type="hidden" name="detail_barang[${editRincianIndex}][id]" value="">
                <div class="col-4"><input type="text" name="detail_barang[${editRincianIndex}][nama_barang]" class="form-control form-control-sm" placeholder="Nama Barang"></div>
                <div class="col-2"><input type="number" step="0.1" name="detail_barang[${editRincianIndex}][volume]" class="form-control form-control-sm" placeholder="Volume"></div>
                <div class="col-2"><input type="text" name="detail_barang[${editRincianIndex}][satuan]" class="form-control form-control-sm" placeholder="Satuan"></div>
                <div class="col-3"><input type="number" name="detail_barang[${editRincianIndex}][harga_satuan]" class="form-control form-control-sm" placeholder="Harga Satuan"></div>
                <div class="col-1"><button type="button" class="btn btn-sm btn-danger remove-rincian-btn">&times;</button></div>
            </div>
        `;
        $('#edit-rincian-barang-wrapper').append(newRow);
        editRincianIndex++;
    });

    // Event listener yang hanya bekerja di dalam modal ini
    $('#edit-rincian-barang-wrapper').on('input', 'input', calculateTotalEdit);
    $('#edit-rincian-barang-wrapper').on('click', '.remove-rincian-btn', function() {
        $(this).closest('.rincian-barang-item').remove();
        calculateTotalEdit();
    });
</script>