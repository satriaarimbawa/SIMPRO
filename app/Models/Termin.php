<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Termin extends Model
{
    use HasFactory;

    protected $fillable = [
        'spk_id',
        'no_termin',
        'tanggal_mulai_kirim',
        'tanggal_akhir_kirim',
        'nilai_termin',
        'kena_ppn',
        'status',
        'bukti_potong_diterima',
    ];

    protected $casts = [
        'tanggal_mulai_kirim' => 'date',
        'tanggal_akhir_kirim' => 'date',
        'nilai_termin' => 'decimal:2',
        'kena_ppn' => 'boolean',
        'bukti_potong_diterima' => 'boolean',
    ];

    /**
     * Relasi ke model Spk.
     */
    public function spk(): BelongsTo
    {
        return $this->belongsTo(Spk::class);
    }

    /**
     * Relasi ke model ItemTermin.
     */
    public function itemTermins(): HasMany
    {
        return $this->hasMany(ItemTermin::class);
    }

    /**
     * Relasi ke model SuratJalan.
     */
    public function suratJalan(): HasOne
    {
        return $this->hasOne(SuratJalan::class);
    }

    /**
     * Relasi ke model PerhitunganPajak.
     */
    public function perhitunganPajak(): HasOne
    {
        return $this->hasOne(PerhitunganPajak::class);
    }

    /**
     * Relasi ke model RekonsiliasiPembayaran.
     */
    public function rekonsiliasiPembayaran(): HasOne
    {
        return $this->hasOne(RekonsiliasiPembayaran::class);
    }

    /**
     * Relasi ke model LampiranDokumen.
     */
    public function lampiranDokumens(): HasMany
    {
        return $this->hasMany(LampiranDokumen::class);
    }

    /**
     * Accessor untuk sisa hari sampai batas akhir kirim.
     */
    public function getSisaHariAttribute(): int
    {
        if (!$this->tanggal_akhir_kirim) {
            return 0;
        }
        
        $now = Carbon::now()->startOfDay();
        $target = Carbon::parse($this->tanggal_akhir_kirim)->startOfDay();
        
        return (int) $now->diffInDays($target, false);
    }

    /**
     * Accessor untuk label status bahasa Indonesia.
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'belum_kirim' => 'Belum Kirim',
            'proses_kirim' => 'Proses Kirim',
            'terkirim' => 'Terkirim',
            'tagihan_dibuat' => 'Tagihan Dibuat',
            'lunas' => 'Lunas',
            'lunas_selisih' => 'Lunas dengan Selisih',
        ];

        return $labels[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    /**
     * Accessor alias untuk tanggal_mulai_kirim.
     */
    public function getTanggalMulaiAttribute()
    {
        return $this->tanggal_mulai_kirim;
    }

    /**
     * Accessor alias untuk tanggal_akhir_kirim.
     */
    public function getTanggalAkhirAttribute()
    {
        return $this->tanggal_akhir_kirim;
    }
}
