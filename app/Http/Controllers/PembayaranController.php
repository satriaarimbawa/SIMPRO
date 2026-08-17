<?php

namespace App\Http\Controllers;

use App\Models\Termin;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function cocokkan(Request $request)
    {
        $results = null;
        $nominal = (float) $request->input('nominal');
        
        if ($nominal > 0) {
            $results = Termin::with('spk.perusahaan')
                ->whereIn('status', ['menunggu_konfirmasi', 'terkirim'])
                ->get()
                ->map(function($termin) use ($nominal) {
                    $gross = (float) $termin->nilai_termin;
                    
                    // Hitung Pajak (Inclusive)
                    if ($termin->kena_ppn) {
                        $dpp = $gross / 1.11;
                        $ppn = $dpp * 0.11;
                        $pph = $dpp * 0.015;
                    } else {
                        $dpp = $gross;
                        $ppn = 0;
                        $pph = $dpp * 0.015;
                    }
                    
                    $nett = $gross - $ppn - $pph;
                    
                    $selisihGross = abs($gross - $nominal);
                    $selisihNett = abs($nett - $nominal);
                    
                    if ($selisihGross <= 500) {
                        $tipePencocokan = 'gross';
                        $labelMatch = 'Pembayaran Penuh (Gross)';
                        $badgeColor = 'bg-green-100 text-green-800 border-green-200';
                        $selisih = $gross - $nominal;
                    } elseif ($selisihNett <= 500) {
                        $tipePencocokan = 'nett';
                        $labelMatch = 'Dipotong Pajak Langsung (Nett)';
                        $badgeColor = 'bg-blue-100 text-blue-800 border-blue-200';
                        $selisih = $nett - $nominal;
                    } else {
                        $tipePencocokan = $selisihGross <= $selisihNett ? 'gross_selisih' : 'nett_selisih';
                        $labelMatch = 'Ada Selisih Lain';
                        $badgeColor = 'bg-amber-100 text-amber-800 border-amber-200';
                        $selisih = $gross - $nominal;
                    }
                    
                    $termin->gross = $gross;
                    $termin->nett = $nett;
                    $termin->ppn = $ppn;
                    $termin->pph = $pph;
                    $termin->selisih = $selisih;
                    $termin->min_diff = min($selisihGross, $selisihNett);
                    $termin->tipe_pencocokan = $tipePencocokan;
                    $termin->label_match = $labelMatch;
                    $termin->badge_color = $badgeColor;
                    
                    return $termin;
                })
                ->sortBy('min_diff')
                ->values();
        }

        return view('pembayaran.cocokkan', compact('results', 'nominal'));
    }

    public function showKonfirmasi(Request $request, $terminId)
    {
        $termin = Termin::with('spk.perusahaan')->findOrFail($terminId);
        $nominal = $request->input('nominal');
        return view('pembayaran.konfirmasi', compact('termin', 'nominal'));
    }

    public function konfirmasi(Request $request, $terminId)
    {
        // Sanitize numeric inputs (strip thousand separators if typed by user)
        if ($request->has('nilai_diterima')) {
            $raw = (string) $request->nilai_diterima;
            // If it contains dots as thousand separators (e.g. 10.500.000)
            if (strpos($raw, '.') !== false && strpos($raw, ',') === false) {
                // Check if dot is thousand separator or decimal
                $parts = explode('.', $raw);
                if (count($parts) > 2 || strlen(end($parts)) === 3) {
                    $raw = str_replace('.', '', $raw);
                }
            } else {
                $raw = str_replace('.', '', $raw);
                $raw = str_replace(',', '.', $raw);
            }
            $request->merge(['nilai_diterima' => $raw]);
        }

        $request->validate([
            'nilai_diterima' => 'required|numeric|min:0',
            'catatan_selisih' => 'nullable|string',
            'jenis_dokumen' => 'nullable|string',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $termin = Termin::findOrFail($terminId);
        
        $selisih = $termin->nilai_termin - $request->nilai_diterima;
        $statusBayar = $selisih == 0 ? 'lunas' : 'lunas_selisih';

        \App\Models\RekonsiliasiPembayaran::create([
            'termin_id' => $termin->id,
            'nilai_diterima' => $request->nilai_diterima,
            'selisih' => $selisih,
            'catatan_selisih' => $request->catatan_selisih,
            'status_bayar' => $statusBayar,
        ]);

        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('lampiran', $filename, 'public');

            \App\Models\LampiranDokumen::create([
                'termin_id' => $termin->id,
                'jenis_dokumen' => $request->jenis_dokumen ?? 'lainnya',
                'nama_file' => $file->getClientOriginalName(),
                'file' => $path,
                'tanggal_unggah' => now(),
            ]);
        }

        $termin->update([
            'status' => $statusBayar
        ]);

        // Cek apakah semua termin SPK sudah lunas, jika ya update status SPK
        $spk = $termin->spk;
        if ($spk && $spk->termins()->whereNotIn('status', ['lunas', 'lunas_selisih'])->count() === 0) {
            // Because SPK status is dynamically computed via getStatusAttribute(), we don't need to update a column if it doesn't exist.
            // Let's just rely on the Accessor.
        }

        return redirect()->route('pembayaran.cocokkan')->with('success', 'Pembayaran dan dokumen bukti berhasil disimpan.');
    }
}
