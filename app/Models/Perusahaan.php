<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perusahaan extends Model
{
    use HasFactory;

    protected $table = 'perusahaans';

    protected $fillable = [
        'nama_perusahaan',
        'nama_direktur',
        'jabatan_penandatangan',
        'pakai_kertas_kop_fisik',
        'file_template_surat_jalan',
        'peta_sel_template',
    ];

    protected $casts = [
        'pakai_kertas_kop_fisik' => 'boolean',
        'peta_sel_template' => 'array',
    ];

    /**
     * Relasi ke model Spk (Satu perusahaan memiliki banyak SPK).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function spks(): HasMany
    {
        return $this->hasMany(Spk::class);
    }

    /**
     * Accessor alias untuk nama_perusahaan.
     */
    public function getNamaAttribute(): string
    {
        return $this->nama_perusahaan ?? '';
    }
}
