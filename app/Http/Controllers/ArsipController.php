<?php

namespace App\Http\Controllers;

use App\Models\Termin;
use Illuminate\Http\Request;

class ArsipController extends Controller
{
    public function index(Request $request)
    {
        $query = Termin::with(['spk.perusahaan', 'itemTermins', 'lampiranDokumens']);

        if ($request->filled('no_spk')) {
            $query->whereHas('spk', function($q) use ($request) {
                $q->where('no_spk', 'like', '%' . $request->no_spk . '%');
            });
        }

        if ($request->filled('nama_dinas')) {
            $query->whereHas('spk', function($q) use ($request) {
                $q->where('nama_dinas', 'like', '%' . $request->nama_dinas . '%');
            });
        }

        if ($request->has('q')) {
            $searchTerm = $request->q;
            $query->whereHas('spk', function($q) use ($searchTerm) {
                $q->where('no_spk', 'like', "%{$searchTerm}%")
                  ->orWhere('nama_dinas', 'like', "%{$searchTerm}%")
                  ->orWhere('kabupaten', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('nilai_min')) {
            $query->where('nilai_termin', '>=', $request->nilai_min);
        }

        if ($request->filled('nilai_max')) {
            $query->where('nilai_termin', '<=', $request->nilai_max);
        }

        if ($request->filled('filter_bupot')) {
            if ($request->filter_bupot == 'belum') {
                $query->where('kena_ppn', true)->where('bukti_potong_diterima', false);
            } elseif ($request->filter_bupot == 'sudah') {
                $query->where('kena_ppn', true)->where('bukti_potong_diterima', true);
            }
        }

        $termins = $query->paginate(15)->withQueryString();

        return view('arsip.index', compact('termins'));
    }
}
