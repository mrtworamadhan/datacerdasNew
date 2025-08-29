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
        'jenis_kelamin', // Tetap enum/string
        'agama_id',
        'status_perkawinan_id',
        'pekerjaan_id',
        'pendidikan_id',
        'kewarganegaraan', // Bisa enum/string
        'nama_ayah_kandung',
        'nama_ibu_kandung',
        'golongan_darah_id',
        'alamat_lengkap',
        'hubungan_keluarga_id',
        'status_kependudukan_id',
        'status_khusus', // JSON, bisa simpan banyak ID
        'status_data',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'status_khusus' => 'array', // Otomatis cast ke array
        'jenis_kelamin' => 'string',
    ];
    public function agama()
    {
        return $this->belongsTo(Agama::class);
    }

    public function statusPerkawinan()
    {
        return $this->belongsTo(StatusPerkawinan::class);
    }

    public function pekerjaan()
    {
        return $this->belongsTo(Pekerjaan::class);
    }

    public function pendidikan()
    {
        return $this->belongsTo(Pendidikan::class);
    }

    public function golonganDarah()
    {
        return $this->belongsTo(GolonganDarah::class);
    }

    public function hubunganKeluarga()
    {
        return $this->belongsTo(HubunganKeluarga::class);
    }

    public function statusKependudukan()
    {
        return $this->belongsTo(StatusKependudukan::class);
    }

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

    public function logKependudukan()
    {
        return $this->hasMany(LogKependudukan::class)->latest();
    }
}