<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Spk extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_spk',
        'perusahaan_id',
        'nama_dinas',
        'kabupaten',
        'npwp_dinas',
        'alamat_dinas',
        'nama_ppk',
        'jabatan_ppk',
        'tanggal_spk',
        'jumlah_termin',
        'file_spk_asli',
    ];

    protected $casts = [
        'tanggal_spk' => 'date',
    ];

    /**
     * Accessor for hashed ID used in URLs.
     */
    public function getHashedIdAttribute(): string
    {
        return \App\Helpers\HashId::encode($this->id);
    }

    /**
     * Override getRouteKey for route URL generation.
     */
    public function getRouteKey(): string
    {
        return \App\Helpers\HashId::encode($this->id);
    }

    /**
     * Override resolveRouteBinding for decoding URL hash back to Eloquent model.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $decodedId = \App\Helpers\HashId::decode($value);
        return $this->where('id', $decodedId)->firstOrFail();
    }

    /**
     * Relasi ke model Perusahaan (Satu SPK dimiliki oleh satu perusahaan).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(Perusahaan::class);
    }

    /**
     * Relasi ke model Termin (Satu SPK memiliki banyak termin).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function termins(): HasMany
    {
        return $this->hasMany(Termin::class);
    }

    /**
     * Accessor untuk mendapatkan status SPK.
     * Mengembalikan 'selesai' jika semua termin 'lunas' atau 'lunas_selisih', selain itu 'aktif'.
     *
     * @return string
     */
    public function getStatusAttribute(): string
    {
        if ($this->termins->isEmpty()) {
            return 'aktif';
        }

        $isSelesai = $this->termins->every(function ($termin) {
            return in_array($termin->status, ['lunas', 'lunas_selisih']);
        });

        return $isSelesai ? 'selesai' : 'aktif';
    }
}
