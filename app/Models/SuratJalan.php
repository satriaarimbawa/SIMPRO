<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratJalan extends Model
{
    use HasFactory;

    protected $table = 'surat_jalans';

    protected $fillable = [
        'termin_id',
        'no_surat_jalan',
        'tanggal_kirim',
        'pengemudi',
        'no_polisi',
        'penerima',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_kirim' => 'date',
    ];

    /**
     * Relasi ke model Termin.
     */
    public function termin(): BelongsTo
    {
        return $this->belongsTo(Termin::class);
    }
}
