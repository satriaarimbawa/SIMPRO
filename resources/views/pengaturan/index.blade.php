@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 animate-fade-in-up">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Pengaturan Sistem</h1>
    </div>

    <form action="{{ route('pengaturan.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Card 1: Pengingat Tenggat Kirim -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <span class="material-symbols-outlined mr-2 text-blue-600">alarm</span>
                Pengingat Tenggat Kirim
            </h2>
            <div class="max-w-md">
                <label class="block text-sm font-medium text-gray-700 mb-2">Ambang Hari Pengingat (H-)</label>
                <div class="flex items-center space-x-3" x-data="{ count: {{ old('ambang_pengingat_hari', $pengaturan->ambang_pengingat_hari ?? 7) }} }">
                    <button type="button" @click="count > 1 ? count-- : count" class="p-2 border border-gray-300 rounded-md text-gray-500 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <span class="material-symbols-outlined text-sm">remove</span>
                    </button>
                    <input type="number" name="ambang_pengingat_hari" x-model="count" min="1" class="w-20 text-center border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono">
                    <button type="button" @click="count++" class="p-2 border border-gray-300 rounded-md text-gray-500 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <span class="material-symbols-outlined text-sm">add</span>
                    </button>
                </div>
                <p class="mt-2 text-sm text-gray-500">Pengingat akan muncul H-<span x-text="count"></span> sebelum batas akhir kirim</p>
            </div>
        </div>

        <!-- Card 2: Tarif Pajak -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <span class="material-symbols-outlined mr-2 text-blue-600">request_quote</span>
                Tarif Pajak
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-2xl">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">PPN (%)</label>
                    <input type="number" step="0.01" name="tarif_ppn_persen" value="{{ old('tarif_ppn_persen', $pengaturan->tarif_ppn_persen ?? 11) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">PPh (%)</label>
                    <input type="number" step="0.01" name="tarif_pph_persen" value="{{ old('tarif_pph_persen', $pengaturan->tarif_pph_persen ?? 1.5) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono">
                </div>
            </div>
            
            <div class="mt-4 p-4 bg-yellow-50 rounded-md flex items-start">
                <span class="material-symbols-outlined text-yellow-400 mr-2">warning</span>
                <div>
                    <p class="text-sm text-yellow-700 font-medium">Perhatian</p>
                    <p class="text-sm text-yellow-700 mt-1">Perubahan tarif hanya berlaku untuk perhitungan baru, tidak mengubah data lama.</p>
                </div>
            </div>
            
            <div class="mt-4">
                <p class="text-sm text-gray-500">Berlaku Sejak: <span class="font-medium text-gray-900">{{ $pengaturan->berlaku_sejak ?? '-' }}</span></p>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <span class="material-symbols-outlined text-sm mr-2">save</span>
                Simpan Pengaturan
            </button>
        </div>
    </form>

    <hr class="my-8 border-gray-200">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Card 3: Akun Admin -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <span class="material-symbols-outlined mr-2 text-gray-600">manage_accounts</span>
                Akun Admin
            </h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" value="{{ auth()->user()->username ?? 'admin' }}" readonly class="w-full bg-gray-50 border-gray-300 rounded-md shadow-sm text-gray-500 cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
                    <input type="password" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                    <input type="password" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                    <input type="password" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="pt-2">
                    <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Ubah Password
                    </button>
                </div>
            </div>
        </div>

        <!-- Card 4: Backup Database -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <span class="material-symbols-outlined mr-2 text-gray-600">database</span>
                Backup Database
            </h2>
            <div class="flex items-center space-x-2 mb-4">
                <span class="h-3 w-3 bg-green-500 rounded-full inline-block"></span>
                <span class="text-sm font-medium text-gray-700">Aktif</span>
            </div>
            <div class="bg-gray-50 rounded p-4 mb-4">
                <p class="text-sm text-gray-500">Backup Terakhir:</p>
                <p class="text-sm font-medium text-gray-900 mt-1">14 Agustus 2026 12:00:00 (otomatis)</p>
            </div>
            <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                <span class="material-symbols-outlined text-sm mr-2">cloud_download</span>
                Backup Sekarang
            </button>
        </div>
    </div>
</div>
@endsection
