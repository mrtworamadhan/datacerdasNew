<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePengeluaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Asumsi otorisasi ditangani di controller
    }

    // Di dalam app/Http/Requests/UpdatePengeluaranRequest.php

    public function rules(): array
    {
        return [
            'uraian' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'tanggal_transaksi' => 'required|date',
            'keterangan' => 'nullable|string',

            // Field khusus Pembelian Pesanan
            'tanggal_pesanan' => 'nullable|date',
            'penyedia' => 'nullable|string|max:255',

            // ======================================================
            // === INI PERBAIKANNYA: Tambahkan aturan validasi baru ===
            // ======================================================
            'nama_pemesan' => 'nullable|string|max:255',
            'nama_penerima' => 'nullable|string|max:255',

            // Field untuk upah kerja (jika ada)
            'nama_pekerja' => 'nullable|string|max:255',
            'tanda_tangan_path' => 'nullable|image|max:1024',

            // Pastikan kita juga memvalidasi detail barang
            'detail_barang' => 'nullable|array',
            'detail_barang.*.nama_barang' => 'nullable|string',
            'detail_barang.*.volume' => 'nullable|numeric',
            'detail_barang.*.satuan' => 'nullable|string',
            'detail_barang.*.harga_satuan' => 'nullable|numeric',
        ];
    }
}