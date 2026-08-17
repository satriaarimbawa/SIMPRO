@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-3xl animate-fade-in-up">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Konfirmasi Pembayaran</h1>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded shadow-sm">
            <div class="flex items-center">
                <span class="material-symbols-outlined text-red-500 mr-2">error</span>
                <p class="font-bold text-red-800 text-sm">Gagal Menyimpan Konfirmasi:</p>
            </div>
            <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Termin Info Summary -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-slate-500 mb-1">No. SPK</p>
                <p class="font-mono font-medium text-slate-900">{{ $termin->spk->no_spk }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500 mb-1">Termin</p>
                <p class="font-medium text-slate-900">Termin ke-{{ $termin->no_termin }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-sm text-slate-500 mb-1">Nama Dinas</p>
                <p class="font-medium text-slate-900">{{ $termin->spk->nama_dinas ?? '-' }} <span class="text-slate-400 font-normal">({{ $termin->spk->kabupaten ?? '-' }})</span></p>
            </div>
            <div class="md:col-span-2 mt-2 pt-4 border-t border-slate-100">
                <p class="text-sm text-slate-500 mb-1">Nilai Tagihan</p>
                <p class="font-mono text-2xl font-bold text-slate-900">Rp {{ number_format($termin->nilai_termin, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Confirmation Form -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
        <form action="{{ route('pembayaran.konfirmasiStore', $termin->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="space-y-6">
                <!-- Nilai Diterima -->
                <div>
                    <label for="nilai_diterima" class="block text-sm font-medium text-slate-700 mb-1">Nilai Diterima <span class="text-red-500">*</span></label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-slate-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" step="1" name="nilai_diterima" id="nilai_diterima" value="{{ old('nilai_diterima', $nominal ?? $termin->nilai_termin) }}" class="pl-10 block w-full rounded-md border-slate-300 font-mono text-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    </div>
                </div>

                <!-- Catatan Selisih -->
                <div>
                    <label for="catatan_selisih" class="block text-sm font-medium text-slate-700 mb-1">Catatan Selisih (Opsional)</label>
                    <textarea name="catatan_selisih" id="catatan_selisih" rows="3" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Berikan catatan jika terdapat selisih (misal: potongan pajak, biaya transfer, dll)">{{ old('catatan_selisih') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Jenis Dokumen -->
                    <div>
                        <label for="jenis_dokumen" class="block text-sm font-medium text-slate-700 mb-1">Jenis Dokumen</label>
                        <select name="jenis_dokumen" id="jenis_dokumen" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">Pilih Jenis Dokumen</option>
                            <option value="bukti_potong_pajak" {{ old('jenis_dokumen') == 'bukti_potong_pajak' ? 'selected' : '' }}>Bukti Potong Pajak</option>
                            <option value="faktur" {{ old('jenis_dokumen') == 'faktur' ? 'selected' : '' }}>Faktur</option>
                            <option value="konfirmasi_transfer" {{ old('jenis_dokumen') == 'konfirmasi_transfer' ? 'selected' : '' }}>Konfirmasi Transfer</option>
                            <option value="lainnya" {{ old('jenis_dokumen') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>

                    <!-- Lampiran Dokumen -->
                    <div>
                        <label for="lampiran" class="block text-sm font-medium text-slate-700 mb-1">Lampiran Dokumen (Opsional)</label>
                        <input type="file" name="lampiran" id="lampiran" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-300 rounded-md">
                    </div>
                </div>

                <!-- Warning Box -->
                <div class="rounded-md bg-amber-50 p-4 border border-amber-200 mt-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <span class="material-symbols-outlined text-amber-500">warning</span>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-amber-800">Perhatian</h3>
                            <div class="mt-2 text-sm text-amber-700">
                                <p>Status termin akan berubah menjadi <strong>LUNAS</strong> setelah konfirmasi. Pastikan data yang dimasukkan sudah benar.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="pt-5 border-t border-slate-200 flex justify-end gap-3">
                    <a href="{{ url()->previous() }}" class="inline-flex justify-center py-2 px-4 border border-slate-300 shadow-sm text-sm font-medium rounded-md text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Konfirmasi Pembayaran
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
