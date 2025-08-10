<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProposalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_kegiatan' => 'required|string|max:255',
            'tipe_kegiatan' => 'required|string|max:255',
            'tanggal_kegiatan' => 'required|date',
            'lokasi_kegiatan' => 'required|string|max:255',
            'deskripsi_kegiatan' => 'required|string',
            'latar_belakang' => 'nullable|string',
            'tujuan_kegiatan' => 'nullable|string',
            'anggaran_biaya' => 'nullable|numeric|min:0',
            'sumber_dana' => 'nullable|string',
            'penutup' => 'nullable|string',
            
            // Validasi untuk Rencana Anggaran
            'laporan_dana' => 'nullable|string', 

            // Validasi untuk penyelenggara (Lembaga atau Kelompok)
            'penyelenggara_type' => ['required', Rule::in(['lembaga', 'kelompok'])],
            'penyelenggara_id' => 'required|integer',
        ];
    }
}