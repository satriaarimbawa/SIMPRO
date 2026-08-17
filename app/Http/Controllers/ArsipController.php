<?php

namespace App\Http\Controllers;

use App\Models\Termin;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArsipController extends Controller
{
    /**
     * Menampilkan halaman daftar arsip termin dengan filter pencarian.
     *
     * Mendukung filter: q (global search), no_spk, nama_dinas,
     * status, nilai_min, nilai_max, filter_bupot.
     * Logika filter didelegasikan ke Termin::scopeFilter().
     */
    public function index(Request $request): View
    {
        $termins = Termin::with(['spk.perusahaan', 'itemTermins', 'lampiranDokumens'])
            ->filter($request->only(['q', 'no_spk', 'nama_dinas', 'status', 'nilai_min', 'nilai_max', 'filter_bupot']))
            ->paginate(15)
            ->withQueryString();

        return view('arsip.index', compact('termins'));
    }
}
