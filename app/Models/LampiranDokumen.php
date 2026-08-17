<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LampiranDokumen extends Model
{
    use HasFactory;

    protected $table = 'lampiran_dokumens';

    protected $fillable = [
        'termin_id',
        'jenis_dokumen',
        'nama_file',
        'file',
        'tanggal_unggah',
    ];

    protected $casts = [
        'tanggal_unggah' => 'date',
    ];

    /**
     * Relasi ke model Termin.
     */
    public function termin(): BelongsTo
    {
        return $this->belongsTo(Termin::class);
    }
}
