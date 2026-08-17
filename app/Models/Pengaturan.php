<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    use HasFactory;

    protected $table = 'pengaturans';

    protected $fillable = [
        'ambang_pengingat_hari',
        'tarif_ppn_persen',
        'tarif_pph_persen',
        'berlaku_sejak',
    ];

    protected $casts = [
        'berlaku_sejak' => 'date',
        'tarif_ppn_persen' => 'decimal:2',
        'tarif_pph_persen' => 'decimal:2',
    ];
}
