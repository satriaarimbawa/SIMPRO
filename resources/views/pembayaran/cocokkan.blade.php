@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl animate-fade-in-up">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Cocokkan Uang Masuk</h1>
        <p class="text-slate-600 mt-1">Masukkan nominal uang masuk untuk mencari termin yang cocok</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded shadow-sm flex items-center justify-between">
            <div class="flex items-center">
                <span class="material-symbols-outlined text-green-600 mr-2">check_circle</span>
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Search Form -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 mb-8">
        <form action="{{ route('pembayaran.cocokkan') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-start sm:items-end">
            <div class="flex-1 w-full">
                <label for="nominal" class="block text-sm font-medium text-slate-700 mb-1">Nominal Uang Masuk</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-slate-500 sm:text-sm">Rp</span>
                    </div>
                    <input type="number" step="1" name="nominal" id="nominal" value="{{ old('nominal', request('nominal')) }}" class="pl-10 block w-full rounded-md border-slate-300 font-mono text-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="0" required>
                </div>
            </div>
            <div class="w-full sm:w-auto mt-2 sm:mt-0">
                <button type="submit" class="w-full inline-flex justify-center items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                    <span class="material-symbols-outlined mr-2">search</span> Cari Termin
                </button>
            </div>
        </form>
    </div>

    @if(isset($results))
    <!-- Results Section -->
    <div>
        <div class="flex items-center mb-4">
            <span class="material-symbols-outlined text-amber-500 mr-2">info</span>
            <p class="text-sm text-slate-600">Menampilkan termin berstatus "Menunggu Konfirmasi" dengan nilai terdekat</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">No. SPK & Indikator</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Termin / Dinas</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Gross (Kwitansi)</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Nett (Setelah Pajak)</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse($results as $index => $termin)
                            <tr class="{{ $index === 0 ? 'bg-blue-50/30' : 'hover:bg-slate-50' }} transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-slate-900">
                                    <div class="font-bold">{{ $termin->spk->no_spk }}</div>
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold border {{ $termin->badge_color }}">
                                            {{ $termin->label_match }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                                    <div class="font-medium">Termin {{ $termin->no_termin }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5">{{ $termin->spk->nama_dinas ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-mono text-slate-900">
                                    Rp {{ number_format($termin->gross, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-mono font-semibold text-emerald-700">
                                    Rp {{ number_format($termin->nett, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="{{ route('pembayaran.konfirmasi', ['terminId' => $termin->id, 'nominal' => $nominal]) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md text-xs font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                        Pilih Termin
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">search_off</span>
                                        Tidak ada termin yang cocok dengan nominal Rp {{ number_format($nominal, 0, ',', '.') }}
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4 p-4 bg-slate-50 rounded-lg border border-slate-200 text-xs text-slate-600 space-y-1">
            <p class="font-bold text-slate-700">💡 Cara Membaca Indikator Pencocokan:</p>
            <p>• 🟢 <strong class="text-green-700">Pembayaran Penuh (Gross):</strong> Nominal uang masuk cocok 100% dengan Total Kwitansi (Dinas belum memotong pajak).</p>
            <p>• 🔵 <strong class="text-blue-700">Dipotong Pajak Langsung (Nett):</strong> Nominal uang masuk cocok dengan nilai setelah dipotong PPN (11%) & PPh (1.5%) oleh Bendahara Dinas.</p>
        </div>
    </div>
    @endif
</div>
@endsection
