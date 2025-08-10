<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePemeriksaanAnakRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'berat_badan' => str_replace(',', '.', $this->berat_badan),
            'tinggi_badan' => str_replace(',', '.', $this->tinggi_badan),
            'lila' => str_replace(',', '.', $this->lila),
        ]);
    }

    /**
     * Dapatkan aturan validasi yang berlaku untuk request ini.
     */
    public function rules(): array
    {
        // Ambil semua aturan dasar
        $rules = [
            'berat_badan'           => 'required|numeric|min:0',
            'tinggi_badan'          => 'required|numeric|min:0',
            'lila'                  => 'nullable|numeric|min:0',
            'catatan_kader'         => 'nullable|string',
            'dapat_vitamin_a'       => 'nullable|boolean',
            'dapat_obat_cacing'     => 'nullable|boolean',
            'dapat_imunisasi_polio' => 'nullable|boolean',
        ];

        // --- INI PERBAIKANNYA ---
        // Cek apakah metode request adalah POST (artinya ini aksi 'store')
        if ($this->isMethod('post')) {
            // Jika 'store', maka tanggal_pemeriksaan wajib diisi
            $rules['tanggal_pemeriksaan'] = 'required|date';
        }

        return $rules;
    }
}