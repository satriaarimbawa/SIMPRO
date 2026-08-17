<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function index()
    {
        $pengaturan = Pengaturan::firstOrCreate(
            ['id' => 1],
            ['ambang_hari_jatuh_tempo' => 7, 'tarif_pajak_persen' => 11]
        );
        return view('pengaturan.index', compact('pengaturan'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'ambang_hari_jatuh_tempo' => 'required|integer|min:1',
            'tarif_pajak_persen' => 'required|numeric|min:0',
        ]);

        $pengaturan = Pengaturan::first();
        if ($pengaturan) {
            $pengaturan->update($validated);
        } else {
            Pengaturan::create(array_merge(['id' => 1], $validated));
        }

        return back()->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
