<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerhitunganPajak extends Model
{
    use HasFactory;

    protected $table = 'perhitungan_pajaks';

    protected $fillable = [
        'termin_id',
        'dpp',
        'ppn',
        'pph',
        'nilai_tagihan',
        'tarif_ppn_persen',
        'tarif_pph_persen',
        'uraian',
    ];

    protected $casts = [
        'dpp' => 'decimal:2',
        'ppn' => 'decimal:2',
        'pph' => 'decimal:2',
        'nilai_tagihan' => 'decimal:2',
        'tarif_ppn_persen' => 'decimal:2',
        'tarif_pph_persen' => 'decimal:2',
    ];

    /**
     * Relasi ke model Termin.
     */
    public function termin(): BelongsTo
    {
        return $this->belongsTo(Termin::class);
    }
}
