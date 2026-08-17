@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 animate-fade-in-up">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Laporan Realisasi SPK</h1>
        <span class="bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded shadow-sm">Layout Sementara</span>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6" x-data="{ status: '{{ request('status', 'semua') }}' }">
        <form action="{{ route('laporan.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Month/Year -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bulan & Tahun</label>
                    <input type="month" name="periode" value="{{ request('periode') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <!-- Kabupaten -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kabupaten</label>
                    <input type="text" name="kabupaten" value="{{ request('kabupaten') }}" placeholder="Cari kabupaten..." class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <!-- Status Toggle -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Pembayaran</label>
                    <div class="flex rounded-md shadow-sm">
                        <button type="button" 
                                @click="status = 'semua'"
                                :class="status === 'semua' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                                class="flex-1 px-4 py-2 text-sm font-medium border border-gray-300 rounded-l-md focus:z-10 focus:ring-2 focus:ring-blue-500">
                            Semua
                        </button>
                        <button type="button"
                                @click="status = 'belum_terbayar'"
                                :class="status === 'belum_terbayar' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                                class="flex-1 px-4 py-2 text-sm font-medium border-t border-b border-gray-300 focus:z-10 focus:ring-2 focus:ring-blue-500">
                            Belum Terbayar
                        </button>
                        <button type="button"
                                @click="status = 'sudah_terbayar'"
                                :class="status === 'sudah_terbayar' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                                class="flex-1 px-4 py-2 text-sm font-medium border border-gray-300 rounded-r-md focus:z-10 focus:ring-2 focus:ring-blue-500">
                            Sudah Terbayar
                        </button>
                        <input type="hidden" name="status" x-model="status">
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-4">
                <a href="{{ route('laporan.export', request()->all()) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <span class="material-symbols-outlined text-sm mr-2">download</span>
                    Export Excel (.xlsx)
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <span class="material-symbols-outlined text-sm mr-2">search</span>
                    Tampilkan
                </button>
            </div>
        </form>
    </div>

    <!-- Results Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-4 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No. SPK</th>
                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Termin</th>
                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Dinas / Instansi</th>
                        <th scope="col" class="px-4 py-3.5 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Nilai Termin (Gross)</th>
                        <th scope="col" class="px-4 py-3.5 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Nilai Diterima (Nett)</th>
                        <th scope="col" class="px-4 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @php
                        $totalNilaiTermin = 0;
                        $totalDiterima = 0;
                    @endphp
                    
                    @forelse($termins as $index => $termin)
                        @php
                            $totalNilaiTermin += $termin->nilai_termin ?? 0;
                            $totalDiterima += $termin->computed_diterima ?? 0;
                        @endphp
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-4 py-3.5 text-center text-sm text-gray-400 font-mono">{{ $index + 1 }}</td>
                            <td class="px-4 py-3.5 text-sm font-semibold text-gray-900 font-jetbrains whitespace-nowrap">{{ $termin->spk->no_spk ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-sm text-gray-700 whitespace-nowrap font-medium">Termin {{ $termin->no_termin }}</td>
                            <td class="px-4 py-3.5 text-sm text-gray-900">
                                <div class="font-medium text-gray-900">{{ $termin->spk->nama_dinas ?? '-' }}</div>
                                @if($termin->spk->kabupaten)
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $termin->spk->kabupaten }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-sm text-right text-gray-900 font-mono whitespace-nowrap font-medium">Rp {{ number_format($termin->nilai_termin ?? 0, 0, ',', '.') }}</td>
                            <td class="px-4 py-3.5 text-sm text-right text-emerald-700 font-mono font-bold whitespace-nowrap">Rp {{ number_format($termin->computed_diterima ?? 0, 0, ',', '.') }}</td>
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                @if($termin->is_lunas)
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-50 text-green-700 border border-green-200">Sudah Terbayar</span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200">Belum Terbayar</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
                                Tidak ada data yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if(isset($termins) && count($termins) > 0)
                <tfoot class="bg-gray-100 border-t-2 border-gray-200">
                    <tr>
                        <th colspan="4" class="px-4 py-3.5 text-sm font-bold text-gray-900 text-right uppercase">TOTAL</th>
                        <th class="px-4 py-3.5 text-sm text-right font-bold text-gray-900 font-mono whitespace-nowrap">Rp {{ number_format($totalNilaiTermin, 0, ',', '.') }}</th>
                        <th class="px-4 py-3.5 text-sm text-right font-bold text-emerald-800 font-mono whitespace-nowrap">Rp {{ number_format($totalDiterima, 0, ',', '.') }}</th>
                        <th class="px-4 py-3.5"></th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
