<?php

namespace App\Http\Controllers;

use App\Models\Spk;
use App\Models\Termin;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSpkAktif = Spk::where('status', '!=', 'selesai')->count();
        $menungguKonfirmasi = Termin::where('status', 'menunggu_konfirmasi')->count();
        $lunasBulanIni = Termin::where('status', 'lunas')
            ->whereMonth('updated_at', Carbon::now()->month)
            ->whereYear('updated_at', Carbon::now()->year)
            ->count();
            
        $approachingDeadlines = Termin::whereIn('status', ['belum_kirim', 'terkirim'])
            ->whereNotNull('tanggal_jatuh_tempo')
            ->orderBy('tanggal_jatuh_tempo', 'asc')
            ->take(5)
            ->get();
            
        $latestSpks = Spk::with('perusahaan')->latest()->take(5)->get();
        
        $totalTerbayar = Termin::whereIn('status', ['lunas', 'lunas_selisih'])->sum('nilai_termin');
        $totalBelumTerbayar = Termin::whereNotIn('status', ['lunas', 'lunas_selisih'])->sum('nilai_termin');

        return view('dashboard', compact(
            'totalSpkAktif',
            'menungguKonfirmasi',
            'lunasBulanIni',
            'approachingDeadlines',
            'latestSpks',
            'totalTerbayar',
            'totalBelumTerbayar'
        ));
    }
}
