<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RekonsiliasiPembayaran extends Model
{
    use HasFactory;

    protected $table = 'rekonsiliasi_pembayarans';

    protected $fillable = [
        'termin_id',
        'nilai_diterima',
        'selisih',
        'catatan_selisih',
        'status_bayar',
    ];

    protected $casts = [
        'nilai_diterima' => 'decimal:2',
        'selisih' => 'decimal:2',
    ];

    /**
     * Relasi ke model Termin.
     */
    public function termin(): BelongsTo
    {
        return $this->belongsTo(Termin::class);
    }
}
