<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemTermin extends Model
{
    use HasFactory;

    protected $fillable = [
        'termin_id',
        'nama_barang',
        'jumlah',
        'satuan',
        'merk',
        'harga_satuan',
        'catatan',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
    ];

    /**
     * Relasi ke model Termin.
     */
    public function termin(): BelongsTo
    {
        return $this->belongsTo(Termin::class);
    }
}
