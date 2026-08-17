@extends('layouts.app')

@section('content')
<div class="space-y-10 pb-16 animate-fade-in-up">
    <!-- Header -->
    <div class="flex flex-col gap-2">
        <div class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50/50 text-blue-600 text-[10px] uppercase tracking-[0.2em] font-bold w-max border border-blue-100/50">
            Overview
        </div>
        <h1 class="text-4xl font-bold text-gray-900 tracking-tight font-inter">Dashboard Utama</h1>
    </div>

    <!-- Section 1: KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Card 1 -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-600">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <span class="material-symbols-outlined text-blue-600 text-3xl">assignment</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 font-inter">Total SPK Aktif</p>
                    <p class="text-2xl font-semibold text-gray-900 font-jetbrains">{{ $totalSpkAktif ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-amber-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <span class="material-symbols-outlined text-amber-500 text-3xl">pending_actions</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 font-inter">Menunggu Konfirmasi</p>
                    <p class="text-2xl font-semibold text-gray-900 font-jetbrains">{{ $menungguKonfirmasi ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <span class="material-symbols-outlined text-green-500 text-3xl">payments</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 font-inter">Lunas Bulan Ini</p>
                    <p class="text-2xl font-semibold text-gray-900 font-jetbrains">{{ $lunasBulanIni ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Keuangan & Tenggat Pengiriman -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Financial Status Card (Progress Bar) -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-emerald-500 lg:col-span-1 flex flex-col justify-center">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-500">account_balance_wallet</span>
                    <h2 class="text-sm font-bold text-gray-900 font-inter">Status Keuangan</h2>
                </div>
            </div>
            
            @php
                $tTerbayar = $totalTerbayar ?? 0;
                $tBelum = $totalBelumTerbayar ?? 0;
                $tTotal = $tTerbayar + $tBelum;
                $pctTerbayar = $tTotal > 0 ? round(($tTerbayar / $tTotal) * 100) : 0;
            @endphp

            <div class="space-y-4">
                <!-- Progress Bar -->
                <div>
                    <div class="flex justify-between text-xs font-medium mb-2">
                        <span class="text-emerald-600">Terbayar ({{ $pctTerbayar }}%)</span>
                        <span class="text-amber-500">Belum</span>
                    </div>
                    <div class="w-full bg-amber-100 rounded-full h-2.5 overflow-hidden flex">
                        <div class="bg-emerald-500 h-full rounded-full transition-all duration-1000 ease-out" style="width: {{ $pctTerbayar }}%"></div>
                    </div>
                </div>

                <!-- Nominal Values -->
                <div class="grid grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase tracking-widest mb-1">Sudah Cair</p>
                        <p class="text-sm font-jetbrains font-bold text-gray-900">Rp {{ number_format($tTerbayar, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase tracking-widest mb-1">Piutang</p>
                        <p class="text-sm font-jetbrains font-bold text-gray-900">Rp {{ number_format($tBelum, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tenggat Pengiriman Ribbon -->
        <div class="bg-white rounded-lg shadow p-6 lg:col-span-2">

            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900 tracking-tight font-inter">Tenggat Pengiriman</h2>
                <div class="px-3 py-1 rounded-full bg-gray-100 text-gray-600 text-[10px] uppercase tracking-[0.2em] font-bold">
                    Prioritas
                </div>
            </div>
            
            <div class="flex overflow-x-auto space-x-4 pb-2">
                @forelse ($deadlines ?? [] as $deadline)
                    @php
                        $sisaHari = $deadline->sisa_hari ?? 0;
                        $isCritical = $sisaHari <= 0;
                        $isWarning = $sisaHari > 0 && $sisaHari <= 7;
                        $bgColor = $isCritical ? 'bg-red-50' : ($isWarning ? 'bg-amber-50' : 'bg-gray-50');
                        $textColor = $isCritical ? 'text-red-700' : ($isWarning ? 'text-amber-700' : 'text-gray-700');
                        $borderColor = $isCritical ? 'border-red-200' : ($isWarning ? 'border-amber-200' : 'border-gray-200');
                        $iconColor = $isCritical ? 'text-red-500' : ($isWarning ? 'text-amber-500' : 'text-gray-400');
                    @endphp
                    <div class="flex-shrink-0 border {{ $borderColor }} {{ $bgColor }} rounded-xl p-4 min-w-[220px] flex gap-3">
                        <div class="{{ $iconColor }} pt-0.5">
                            <span class="material-symbols-outlined text-xl">{{ $isCritical ? 'warning' : 'schedule' }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-bold {{ $textColor }}">
                                {{ $isCritical ? 'Melewati Tenggat!' : $sisaHari . ' Hari Lagi' }}
                            </p>
                            <p class="text-xs font-jetbrains text-gray-500 mt-1">{{ $deadline->spk->no_spk ?? 'No SPK' }}</p>
                            <p class="text-xs text-gray-900 mt-1 truncate max-w-[150px] font-medium" title="{{ $deadline->spk->nama_dinas ?? 'Dinas' }}">{{ $deadline->spk->nama_dinas ?? 'Dinas' }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-gray-500 text-sm italic py-2 flex items-center h-full">Tidak ada tenggat mendekat</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Section 3: SPK Terbaru Table (Elegant & Clean) -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h2 class="text-lg font-bold text-gray-900 font-inter tracking-tight">SPK Terbaru</h2>
            <a href="{{ route('spk.create') }}" class="group inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium transition-colors shadow-sm">
                <span>Buat SPK Baru</span>
                <span class="material-symbols-outlined text-sm transition-transform group-hover:translate-x-1">arrow_forward</span>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead>
                    <tr class="bg-white">
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">No. SPK</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Nama Dinas</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 bg-white">
                    @forelse ($latestSpks ?? [] as $spk)
                        <tr class="hover:bg-gray-50/80 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 font-jetbrains">{{ $spk->no_spk }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ optional($spk->tanggal_spk)->format('d M Y') ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $spk->nama_dinas }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if(($spk->status ?? 'aktif') === 'aktif')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                        Selesai
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <a href="{{ route('spk.show', $spk->hashed_id) }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm inline-flex items-center transition-colors">
                                    Detail <span class="material-symbols-outlined text-sm ml-1 transition-transform group-hover:translate-x-1">chevron_right</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">Belum ada data SPK terbaru.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .animate-fade-in-up {
        animation: fadeInUp 0.8s cubic-bezier(0.32, 0.72, 0, 1) forwards;
    }
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(24px);
            filter: blur(4px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
            filter: blur(0);
        }
    }
</style>
@endsection
