<?php

namespace App\Http\Controllers;

use App\Models\Termin;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PembayaranController extends Controller
{
    /**
     * Menampilkan form pencarian dan hasil pencocokan uang masuk dengan termin.
     *
     * Mengambil semua termin berstatus menunggu_konfirmasi/terkirim,
     * lalu mengurutkan berdasarkan selisih terkecil dengan nominal input.
     * Kalkulasi pajak (DPP, PPN, PPh, Nett) didelegasikan ke Termin accessor.
     */
    public function cocokkan(Request $request): View
    {
        $results = null;
        $nominal = (float) $request->input('nominal');

        if ($nominal > 0) {
            $results = Termin::with('spk.perusahaan')
                ->whereIn('status', ['menunggu_konfirmasi', 'terkirim'])
                ->get()
                ->map(function (Termin $termin) use ($nominal) {
                    // Gunakan accessor dari model: $termin->dpp, ->ppn, ->pph, ->nett
                    $selisihGross = abs((float) $termin->nilai_termin - $nominal);
                    $selisihNett  = abs($termin->nett - $nominal);

                    // Tentukan tipe pencocokan berdasarkan selisih terkecil
                    if ($selisihGross <= 500) {
                        $tipePencocokan = 'gross';
                        $labelMatch     = 'Pembayaran Penuh (Gross)';
                        $badgeColor     = 'bg-green-100 text-green-800 border-green-200';
                        $selisih        = (float) $termin->nilai_termin - $nominal;
                    } elseif ($selisihNett <= 500) {
                        $tipePencocokan = 'nett';
                        $labelMatch     = 'Dipotong Pajak Langsung (Nett)';
                        $badgeColor     = 'bg-blue-100 text-blue-800 border-blue-200';
                        $selisih        = $termin->nett - $nominal;
                    } else {
                        $tipePencocokan = $selisihGross <= $selisihNett ? 'gross_selisih' : 'nett_selisih';
                        $labelMatch     = 'Ada Selisih Lain';
                        $badgeColor     = 'bg-amber-100 text-amber-800 border-amber-200';
                        $selisih        = (float) $termin->nilai_termin - $nominal;
                    }

                    // Lampirkan data hasil pencocokan ke objek termin
                    $termin->selisih        = $selisih;
                    $termin->min_diff       = min($selisihGross, $selisihNett);
                    $termin->tipe_pencocokan = $tipePencocokan;
                    $termin->label_match    = $labelMatch;
                    $termin->badge_color    = $badgeColor;

                    return $termin;
                })
                ->sortBy('min_diff')
                ->values();
        }

        return view('pembayaran.cocokkan', compact('results', 'nominal'));
    }

    /**
     * Menampilkan halaman konfirmasi pembayaran untuk satu termin.
     */
    public function showKonfirmasi(Request $request, int $terminId): View
    {
        $termin  = Termin::with('spk.perusahaan')->findOrFail($terminId);
        $nominal = $request->input('nominal');

        return view('pembayaran.konfirmasi', compact('termin', 'nominal'));
    }

    /**
     * Menyimpan konfirmasi pembayaran: rekonsiliasi, lampiran dokumen, dan update status termin.
     *
     * Mendukung format angka dengan pemisah ribuan titik (contoh: 10.500.000).
     */
    public function konfirmasi(Request $request, int $terminId)
    {
        // Sanitasi input nominal — hapus pemisah ribuan jika ada
        if ($request->has('nilai_diterima')) {
            $raw   = (string) $request->nilai_diterima;
            $parts = explode('.', $raw);

            // Deteksi apakah titik adalah pemisah ribuan (bukan desimal)
            if (count($parts) > 2 || (count($parts) === 2 && strlen(end($parts)) === 3)) {
                $raw = str_replace('.', '', $raw);
            } else {
                $raw = str_replace(['.', ','], ['', '.'], $raw);
            }

            $request->merge(['nilai_diterima' => $raw]);
        }

        $request->validate([
            'nilai_diterima' => 'required|numeric|min:0',
            'catatan_selisih' => 'nullable|string',
            'jenis_dokumen'   => 'nullable|string',
            'lampiran'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $termin = Termin::with('spk')->findOrFail($terminId);
        $selisih     = $termin->nilai_termin - $request->nilai_diterima;
        $statusBayar = $selisih == 0 ? 'lunas' : 'lunas_selisih';

        // Simpan rekonsiliasi pembayaran
        \App\Models\RekonsiliasiPembayaran::create([
            'termin_id'      => $termin->id,
            'nilai_diterima' => $request->nilai_diterima,
            'selisih'        => $selisih,
            'catatan_selisih' => $request->catatan_selisih,
            'status_bayar'   => $statusBayar,
        ]);

        // Simpan lampiran dokumen jika ada
        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');

            // Sanitasi no_spk agar aman sebagai nama folder Windows
            // Ganti / dan spasi → underscore, hapus karakter tidak valid lainnya
            $noSpkSanitasi = preg_replace('/[\/\\\\ ]/', '_', $termin->spk->no_spk);
            $noSpkSanitasi = preg_replace('/[:\*\?"<>\|]/', '', $noSpkSanitasi);

            // Struktur folder: lampiran/{no_spk}/Termin-{no_termin}/
            $folder = 'lampiran/' . $noSpkSanitasi . '/Termin-' . $termin->no_termin;

            // Simpan dengan nama file asli dari komputer pengguna
            $path = $file->storeAs($folder, $file->getClientOriginalName(), 'public');

            \App\Models\LampiranDokumen::create([
                'termin_id'      => $termin->id,
                'jenis_dokumen'  => $request->jenis_dokumen ?? 'lainnya',
                'nama_file'      => $file->getClientOriginalName(),
                'file'           => $path,
                'tanggal_unggah' => now(),
            ]);
        }

        // Update status termin menjadi lunas atau lunas_selisih
        $termin->update(['status' => $statusBayar]);

        return redirect()->route('pembayaran.cocokkan')
            ->with('success', 'Pembayaran dan dokumen bukti berhasil disimpan.');
    }
}
