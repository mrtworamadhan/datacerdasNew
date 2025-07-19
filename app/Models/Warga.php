<?php

namespace App\Models;

use App\Models\Traits\BelongsToDesa; // Tambahkan ini
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warga extends Model
{
    use HasFactory, BelongsToDesa; // Gunakan trait

    protected $table = 'wargas'; // Pastikan nama tabel benar

    protected $fillable = [
        'desa_id',
        'kartu_keluarga_id',
        'rw_id',
        'rt_id',
        'nik',
        'nama_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'status_perkawinan',
        'pekerjaan',
        'pendidikan',
        'kewarganegaraan',
        'nama_ayah_kandung',
        'nama_ibu_kandung',
        'golongan_darah',
        'alamat_lengkap',
        'hubungan_keluarga',
        'status_kependudukan',
        'status_khusus', // Akan disimpan sebagai JSON
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'status_khusus' => 'array', // Otomatis cast ke array
        'jenis_kelamin' => 'string',
        'status_perkawinan' => 'string',
        'status_kependudukan' => 'string',
        'pendidikan' => 'string',
    ];

    // Relasi ke Desa
    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    // Relasi ke Kartu Keluarga
    public function kartuKeluarga()
    {
        return $this->belongsTo(KartuKeluarga::class);
    }

    // Relasi ke RW
    public function rw()
    {
        return $this->belongsTo(RW::class);
    }

    // Relasi ke RT
    public function rt()
    {
        return $this->belongsTo(RT::class);
    }

    // Relasi ke data kesehatan (nanti)
    public function dataKesehatanAnak()
    {
        return $this->hasMany(DataKesehatanAnak::class);
    }

    public function dataKesehatanIbu()
    {
        return $this->hasMany(DataIbuHamil::class);
    }
}