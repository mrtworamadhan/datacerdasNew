<?php

namespace App\Models;

use App\Models\Traits\BelongsToDesa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanSurat extends Model
{
    use HasFactory, BelongsToDesa;

    protected $fillable = [
        'desa_id', 'jenis_surat_id', 'warga_id', 'diajukan_oleh_user_id', 'status_permohonan', 'keperluan', 'disetujui_rt_at', 'disetujui_rt_oleh', 'disetujui_rw_at', 'disetujui_rw_oleh',
        'nomor_surat', 'nomor_urut', 'tanggal_pengajuan', 'tanggal_selesai', 'file_pendukung', 'detail_tambahan',
        'jalur_pengajuan','file_pengantar_rt_rw', 'persyaratan_terpenuhi',
        'catatan_penolakan'
    ];

    protected $casts = [
        'detail_tambahan' => 'array',
        'persyaratan_terpenuhi' => 'array',
        'tanggal_pengajuan' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function jenisSurat() {
        return $this->belongsTo(JenisSurat::class);
    }
    public function warga() {
        return $this->belongsTo(Warga::class);
    }
    public function diajukanOleh() {
        return $this->belongsTo(User::class, 'diajukan_oleh_user_id');
    }
}