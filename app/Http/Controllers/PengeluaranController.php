<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use App\Http\Requests\UpdatePengeluaranRequest;
use Illuminate\Support\Facades\Storage;

class PengeluaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $subdomain)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $subdomain)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $subdomain)
    {
        // 1. Validasi data umum
        $validated = $request->validate([
            'kegiatan_id' => 'required|exists:kegiatans,id',
            'tipe_pengeluaran' => 'required|string',
            'tanggal_transaksi' => 'required|date',
            'uraian' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        // 2. Validasi kondisional berdasarkan tipe
        if ($request->tipe_pengeluaran == 'Pembelian Pesanan') {
            $validated = array_merge($validated, $request->validate([
                'tanggal_pesanan' => 'nullable|date',
                'penyedia' => 'nullable|string|max:255',
                'nama_pemesan' => 'nullable|string|max:255', // <-- Validasi baru
                'nama_penerima' => 'nullable|string|max:255', // <-- Validasi baru
            ]));
        } elseif ($request->tipe_pengeluaran == 'Upah Kerja') {
            $validated = array_merge($validated, $request->validate([
                'nama_pekerja' => 'nullable|string|max:255',
                'tanda_tangan_path' => 'nullable|image|max:1024', // Validasi file gambar
            ]));
        }

        // 3. Handle upload file jika ada
        if ($request->hasFile('tanda_tangan_path')) {
            $path = $request->file('tanda_tangan_path')->store('tanda_tangan_upah', 'public');
            $validated['tanda_tangan_path'] = $path;
        }

        // 4. Simpan ke database
        $pengeluaran = Pengeluaran::create($validated);

        if ($request->tipe_pengeluaran == 'Pembelian Pesanan' && $request->has('detail_barang')) {
            foreach ($request->detail_barang as $item) {
                // Hanya simpan jika nama barang diisi
                if (!empty($item['nama_barang'])) {
                    $pengeluaran->detailBarangs()->create([
                        'nama_barang' => $item['nama_barang'],
                        'volume' => $item['volume'] ?? 0,
                        'satuan' => $item['satuan'] ?? '',
                        'harga_satuan' => $item['harga_satuan'] ?? 0,
                    ]);
                }
            }
        }

        return back()->with('success', 'Catatan pengeluaran berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $subdomain, Pengeluaran $pengeluaran)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $subdomain, Pengeluaran $pengeluaran)
    {
        // Muat relasi detailBarangs untuk diinspeksi
        $pengeluaran->load('detailBarangs');


        // Kode di bawah ini tidak akan berjalan untuk sementara
        return view('admin_desa.kegiatan.partials.edit_pengeluaran_modal', compact('pengeluaran'))->render();
    }

    /**
     * Update the specified resource in storage.
     */

    // Di dalam PengeluaranController.php

    public function update(UpdatePengeluaranRequest $request, string $subdomain, Pengeluaran $pengeluaran)
    {
        // 1. Ambil semua data yang sudah lolos validasi
        $validated = $request->validated();

        // 2. Pisahkan data untuk tabel 'pengeluarans' dari data untuk 'detail_barangs'
        $pengeluaranData = collect($validated)->except('detail_barang')->toArray();
        $detailBarangData = $validated['detail_barang'] ?? [];

        // 3. Handle upload file baru (jika ada)
        if ($request->hasFile('tanda_tangan_path')) {
            if ($pengeluaran->tanda_tangan_path) {
                Storage::disk('public')->delete($pengeluaran->tanda_tangan_path);
            }
            $path = $request->file('tanda_tangan_path')->store('tanda_tangan_upah', 'public');
            $pengeluaranData['tanda_tangan_path'] = $path;
        }

        // 4. Update record PENGELUARAN (data induk) terlebih dahulu
        $pengeluaran->update($pengeluaranData);

        // =================================================================
        // === LOGIKA BARU: Sinkronisasi Rincian Barang (data anak) ===
        // =================================================================
        if ($pengeluaran->tipe_pengeluaran == 'Pembelian Pesanan') {
            $existingIds = [];
            foreach ($detailBarangData as $itemData) {
                // Lewati baris kosong
                if (empty($itemData['nama_barang'])) continue;

                // Jika ada ID, update. Jika tidak ada ID, buat baru.
                $detail = $pengeluaran->detailBarangs()->updateOrCreate(
                    ['id' => $itemData['id'] ?? null], // Kunci untuk mencari
                    [ // Data untuk diisi atau di-update
                        'nama_barang' => $itemData['nama_barang'],
                        'volume' => $itemData['volume'],
                        'satuan' => $itemData['satuan'],
                        'harga_satuan' => $itemData['harga_satuan'],
                    ]
                );
                $existingIds[] = $detail->id;
            }
            // Hapus rincian barang yang ID-nya tidak ada lagi di form (sudah dihapus oleh user)
            $pengeluaran->detailBarangs()->whereNotIn('id', $existingIds)->delete();
        }
        // =================================================================

        return redirect()->route('kegiatans.show', $pengeluaran->kegiatan_id)
                        ->with('success', 'Data pengeluaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $subdomain, Pengeluaran $pengeluaran)
    {

        // Hapus file terkait jika ada
        if ($pengeluaran->tanda_tangan_path) {
            Storage::disk('public')->delete($pengeluaran->tanda_tangan_path);
        }

        $pengeluaran->delete();

        return back()->with('success', 'Catatan pengeluaran berhasil dihapus.');
    }
}
