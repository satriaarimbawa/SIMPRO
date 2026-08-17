?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
        'tanggal_mulai_kirim'   => 'date',
        'tanggal_akhir_kirim'   => 'date',
        'nilai_termin'          => 'decimal:2',
        'kena_ppn'              => 'boolean',
        'bukti_potong_diterima' => 'boolean',
    ];

    // =========================================================================
    // RELASI
    // =========================================================================

    /** Relasi ke model Spk (Many-to-One). */
    public function spk(): BelongsTo
    {
        return $this->belongsTo(Spk::class);
    }

    /** Relasi ke model ItemTermin (One-to-Many). */
    public function itemTermins(): HasMany
    {
        return $this->hasMany(ItemTermin::class);
    }

    /** Relasi ke model SuratJalan (One-to-One). */
    public function suratJalan(): HasOne
    {
        return $this->hasOne(SuratJalan::class);
    }

    /** Relasi ke model PerhitunganPajak (One-to-One). */
    public function perhitunganPajak(): HasOne
    {
        return $this->hasOne(PerhitunganPajak::class);
    }

    /** Relasi ke model RekonsiliasiPembayaran (One-to-One). */
    public function rekonsiliasiPembayaran(): HasOne
    {
        return $this->hasOne(RekonsiliasiPembayaran::class);
    }

    /** Relasi ke model LampiranDokumen (One-to-Many). */
    public function lampiranDokumens(): HasMany
    {
        return $this->hasMany(LampiranDokumen::class);
    }

    // =========================================================================
    // QUERY SCOPES
    // =========================================================================

    /**
     * Scope: Filter pencarian dinamis untuk halaman Arsip.
     * Mendukung: q (global), no_spk, nama_dinas, status, nilai_min, nilai_max, filter_bupot.
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['q'] ?? null, fn ($q, $v) =>
                $q->whereHas('spk', fn ($s) =>
                    $s->where('no_spk', 'like', "%{$v}%")
                      ->orWhere('nama_dinas', 'like', "%{$v}%")
                      ->orWhere('kabupaten', 'like', "%{$v}%")
                )
            )
            ->when($filters['no_spk'] ?? null, fn ($q, $v) =>
                $q->whereHas('spk', fn ($s) => $s->where('no_spk', 'like', "%{$v}%"))
            )
            ->when($filters['nama_dinas'] ?? null, fn ($q, $v) =>
                $q->whereHas('spk', fn ($s) => $s->where('nama_dinas', 'like', "%{$v}%"))
            )
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['nilai_min'] ?? null, fn ($q, $v) => $q->where('nilai_termin', '>=', $v))
            ->when($filters['nilai_max'] ?? null, fn ($q, $v) => $q->where('nilai_termin', '<=', $v))
            ->when($filters['filter_bupot'] ?? null, function ($q, $v) {
                if ($v === 'belum') {
                    $q->where('kena_ppn', true)->where('bukti_potong_diterima', false);
                } elseif ($v === 'sudah') {
                    $q->where('kena_ppn', true)->where('bukti_potong_diterima', true);
                }
            });
    }

    // =========================================================================
    // ACCESSORS — KALKULASI PAJAK
    // =========================================================================

    /**
     * Accessor: DPP (Dasar Pengenaan Pajak).
     * Jika kena PPN: DPP = nilai_termin / 1.11. Jika tidak: DPP = nilai_termin.
     */
    public function getDppAttribute(): float
    {
        return $this->kena_ppn
            ? (float) $this->nilai_termin / 1.11
            : (float) $this->nilai_termin;
    }

    /**
     * Accessor: PPN (11% dari DPP). Nol jika tidak kena PPN.
     */
    public function getPpnAttribute(): float
    {
        return $this->kena_ppn ? $this->dpp * 0.11 : 0.0;
    }

    /**
     * Accessor: PPh Pasal 22 (1.5% dari DPP). Selalu dihitung.
     */
    public function getPphAttribute(): float
    {
        return $this->dpp * 0.015;
    }

    /**
     * Accessor: Nilai Nett setelah dipotong PPN dan PPh.
     * Formula: Nett = nilai_termin - PPN - PPh
     */
    public function getNettAttribute(): float
    {
        return (float) $this->nilai_termin - $this->ppn - $this->pph;
    }

    // =========================================================================
    // ACCESSORS — LAINNYA
    // =========================================================================

    /**
     * Accessor: Sisa hari hingga tanggal_akhir_kirim. Negatif jika sudah lewat.
     */
    public function getSisaHariAttribute(): int
    {
        if (!$this->tanggal_akhir_kirim) {
            return 0;
        }
        $now    = Carbon::now()->startOfDay();
        $target = Carbon::parse($this->tanggal_akhir_kirim)->startOfDay();
        return (int) $now->diffInDays($target, false);
    }

    /**
     * Accessor: Label status dalam Bahasa Indonesia.
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'belum_kirim'         => 'Belum Kirim',
            'proses_kirim'        => 'Proses Kirim',
            'terkirim'            => 'Terkirim',
            'tagihan_dibuat'      => 'Tagihan Dibuat',
            'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
            'lunas'               => 'Lunas',
            'lunas_selisih'       => 'Lunas dengan Selisih',
        ];
        return $labels[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    /** Accessor: Alias untuk tanggal_mulai_kirim. */
    public function getTanggalMulaiAttribute()
    {
        return $this->tanggal_mulai_kirim;
    }

    /** Accessor: Alias untuk tanggal_akhir_kirim. */
    public function getTanggalAkhirAttribute()
    {
        return $this->tanggal_akhir_kirim;
    }
}
