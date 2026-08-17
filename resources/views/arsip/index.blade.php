@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 animate-fade-in-up" x-data="{ 
    isModalOpen: false, 
    activeTermin: null,
    init() {
        this.$watch('isModalOpen', value => {
            document.body.style.overflow = value ? 'hidden' : '';
        });
    },
    openModal(terminData, spkHashedId) {
        this.activeTermin = terminData;
        this.activeTermin.spk_hashed_id = spkHashedId;
        this.isModalOpen = true;
    },
    closeModal() {
        this.isModalOpen = false;
        this.activeTermin = null;
    }
}" @keydown.escape.window="closeModal()">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 font-inter">Arsip & Pencarian SPK</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola data per termin, perbarui status, dan unggah dokumen pendukung.</p>
        </div>
    </div>

    <!-- Flash Message Notification -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 flex items-center justify-between text-green-800">
            <div class="flex items-center">
                <span class="material-symbols-outlined text-green-600 mr-2">check_circle</span>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Filter Panel -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4 mb-6">
        <form action="{{ route('arsip.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[150px]">
                <label for="no_spk" class="block text-sm font-medium text-slate-700 mb-1">No. SPK</label>
                <input type="text" name="no_spk" id="no_spk" value="{{ old('no_spk', request('no_spk')) }}" class="w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm font-jetbrains">
            </div>
            
            <div class="flex-1 min-w-[150px]">
                <label for="nama_dinas" class="block text-sm font-medium text-slate-700 mb-1">Nama Dinas</label>
                <input type="text" name="nama_dinas" id="nama_dinas" value="{{ old('nama_dinas', request('nama_dinas')) }}" class="w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>

            <div class="flex-1 min-w-[150px]">
                <label for="status" class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                <select name="status" id="status" class="w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">Semua</option>
                    <option value="belum_kirim" {{ request('status') == 'belum_kirim' ? 'selected' : '' }}>Belum Kirim</option>
                    <option value="proses_kirim" {{ request('status') == 'proses_kirim' ? 'selected' : '' }}>Proses Kirim</option>
                    <option value="terkirim" {{ request('status') == 'terkirim' ? 'selected' : '' }}>Terkirim</option>
                    <option value="menunggu_konfirmasi" {{ request('status') == 'menunggu_konfirmasi' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                    <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                    <option value="lunas_selisih" {{ request('status') == 'lunas_selisih' ? 'selected' : '' }}>Lunas (Ada Selisih)</option>
                </select>
            </div>

            <div class="flex-1 min-w-[150px]">
                <label for="filter_bupot" class="block text-sm font-medium text-slate-700 mb-1">Bukti Potong</label>
                <select name="filter_bupot" id="filter_bupot" class="w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">Semua</option>
                    <option value="belum" {{ request('filter_bupot') == 'belum' ? 'selected' : '' }}>Belum Diterima</option>
                    <option value="sudah" {{ request('filter_bupot') == 'sudah' ? 'selected' : '' }}>Sudah Diterima</option>
                </select>
            </div>

            <div class="flex-1 min-w-[120px]">
                <label for="nilai_min" class="block text-sm font-medium text-slate-700 mb-1">Nilai Min</label>
                <input type="number" name="nilai_min" id="nilai_min" value="{{ old('nilai_min', request('nilai_min')) }}" class="w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm font-jetbrains">
            </div>

            <div class="flex-1 min-w-[120px]">
                <label for="nilai_max" class="block text-sm font-medium text-slate-700 mb-1">Nilai Max</label>
                <input type="number" name="nilai_max" id="nilai_max" value="{{ old('nilai_max', request('nilai_max')) }}" class="w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm font-jetbrains">
            </div>

            <div>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-on-primary uppercase tracking-widest hover:bg-primary/90 active:scale-[0.97] transition-all duration-200 ease-out shadow-sm">
                    <span class="material-symbols-outlined text-sm mr-1">search</span> Cari
                </button>
            </div>
        </form>
    </div>

    <!-- Results Table -->
    <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-surface-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-surface-border">
                <thead class="bg-surface-container">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-on-surface-variant uppercase tracking-wider">No. SPK</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Termin</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Nama Dinas</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Kabupaten</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Nilai Termin</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-surface-container-lowest divide-y divide-surface-border">
                    @forelse($termins as $termin)
                        <tr class="hover:bg-surface-container transition-colors duration-200 ease-out group">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-on-surface font-jetbrains group-hover:text-primary transition-colors">
                                {{ $termin->spk->no_spk }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-on-surface font-semibold">
                                Termin {{ $termin->no_termin }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-on-surface">
                                {{ $termin->spk->nama_dinas ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-on-surface-variant">
                                {{ $termin->spk->kabupaten ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-jetbrains font-bold text-on-surface">
                                Rp {{ number_format($termin->nilai_termin ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($termin->status == 'belum_kirim')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">Belum Kirim</span>
                                @elseif($termin->status == 'proses_kirim')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Proses Kirim</span>
                                @elseif($termin->status == 'terkirim')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Terkirim</span>
                                @elseif($termin->status == 'menunggu_konfirmasi')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Menunggu Konfirmasi</span>
                                @elseif($termin->status == 'lunas')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Lunas</span>
                                @elseif($termin->status == 'lunas_selisih')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">Lunas (Ada Selisih)</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">{{ $termin->status }}</span>
                                @endif
                                
                                @if($termin->kena_ppn && in_array($termin->status, ['terkirim', 'tagihan_dibuat', 'menunggu_konfirmasi', 'lunas', 'lunas_selisih']))
                                    <div class="mt-1.5 flex justify-center">
                                        @if($termin->bukti_potong_diterima)
                                            <span class="inline-flex items-center text-[10px] text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100" title="Bukti Potong Sudah Diterima">
                                                <span class="material-symbols-outlined text-[12px] mr-0.5">check_circle</span> Bupot Ada
                                            </span>
                                        @else
                                            <span class="inline-flex items-center text-[10px] text-red-600 bg-red-50 px-1.5 py-0.5 rounded border border-red-100" title="Bukti Potong Belum Diterima dari Dinas">
                                                <span class="material-symbols-outlined text-[12px] mr-0.5">warning</span> Tagih Bupot
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <button type="button" 
                                        @click="openModal({{ json_encode($termin) }}, '{{ $termin->spk->hashed_id }}')" 
                                        class="px-3 py-1.5 bg-surface-container-high text-on-surface hover:bg-primary hover:text-on-primary rounded-md font-medium text-xs inline-flex items-center active:scale-[0.97] transition-all duration-200 ease-out shadow-sm">
                                    <span class="material-symbols-outlined text-sm mr-1">tune</span> Kelola
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-sm text-on-surface-variant">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-surface-container rounded-full flex items-center justify-center mb-4 shadow-sm">
                                        <span class="material-symbols-outlined text-3xl text-on-surface-variant">search_off</span>
                                    </div>
                                    <p class="font-semibold text-on-surface text-base">Tidak ada data ditemukan</p>
                                    <p class="text-xs mt-1">Coba sesuaikan filter pencarian Anda</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(isset($termins) && $termins->hasPages())
            <div class="px-6 py-4 border-t border-surface-border bg-surface-container-lowest">
                {{ $termins->withQueryString()->links() }}
            </div>
        @endif
    </div>

    <!-- Interactive Termin Modal Pop-up -->
    <template x-teleport="body">
        <div x-show="isModalOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[9999] overflow-hidden bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 sm:p-6"
             style="display: none;">
            
            <div @click.away="closeModal()" 
                 class="bg-surface-container-lowest rounded-2xl shadow-2xl ring-1 ring-surface-border border border-surface-border max-w-3xl w-full max-h-[85vh] flex flex-col transform transition-all overflow-hidden">
                
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-surface-container text-on-surface flex items-center justify-between border-b border-surface-border shrink-0">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center font-bold text-white shadow-inner">
                        T<span x-text="activeTermin?.no_termin"></span>
                    </div>
                    <div>
                        <h3 class="text-base font-bold font-inter flex items-center gap-2">
                            <span>No. SPK:</span>
                            <span class="font-jetbrains text-primary" x-text="activeTermin?.spk?.no_spk"></span>
                        </h3>
                        <p class="text-xs text-on-surface-variant font-inter" x-text="`${activeTermin?.spk?.nama_dinas ?? ''} (${activeTermin?.spk?.kabupaten ?? ''})`"></p>
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    <template x-if="activeTermin?.spk_hashed_id">
                        <a :href="`/spk/${activeTermin?.spk_hashed_id}`" 
                           class="px-3 py-1.5 bg-surface-container-highest hover:bg-surface-border text-on-surface text-xs font-medium rounded-lg flex items-center transition">
                            <span class="material-symbols-outlined text-sm mr-1">open_in_new</span> Lihat Data Master
                        </a>
                    </template>
                    <button type="button" @click="closeModal()" class="text-on-surface-variant hover:text-on-surface p-1.5 rounded-lg transition">
                        <span class="material-symbols-outlined text-xl">close</span>
                    </button>
                </div>
            </div>

            <!-- Modal Content (Scrollable inside modal body only) -->
            <div class="p-6 space-y-6 overflow-y-auto flex-1 bg-background min-h-0 modal-scrollbar">
                
                <!-- Quick Info Bar -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-surface-container-lowest p-4 rounded-xl border border-surface-border shadow-sm">
                    <div>
                        <p class="text-xs text-on-surface-variant font-medium uppercase tracking-wider">Nilai Termin</p>
                        <p class="text-lg font-bold text-on-surface font-jetbrains mt-0.5" x-text="`Rp ${new Intl.NumberFormat('id-ID').format(activeTermin?.nilai_termin ?? 0)}`"></p>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-variant font-medium uppercase tracking-wider">Pajak PPN</p>
                        <p class="text-sm font-semibold mt-1" :class="activeTermin?.kena_ppn ? 'text-green-600' : 'text-on-surface-variant'" x-text="activeTermin?.kena_ppn ? 'Kena PPN' : 'Bebas PPN'"></p>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-variant font-medium uppercase tracking-wider">Perusahaan</p>
                        <p class="text-sm font-semibold text-on-surface mt-1 truncate" x-text="activeTermin?.spk?.perusahaan?.nama_perusahaan ?? '-'"></p>
                    </div>
                </div>

                <!-- Update Status Form & Direct Actions -->
                <div class="bg-surface-container-lowest p-5 rounded-xl border border-surface-border shadow-sm space-y-4">
                    <h4 class="text-sm font-bold text-on-surface font-inter flex items-center">
                        <span class="material-symbols-outlined text-base mr-1.5 text-primary">published_with_changes</span> 
                        Ubah Status & Dokumen Cetak
                    </h4>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Status Update Form -->
                        <form :action="`/termin/${activeTermin?.id}/status`" method="POST">
                            @csrf
                            @method('PATCH')
                            <label class="block text-xs font-medium text-slate-600 mb-1">Status Termin Saat Ini</label>
                            <div class="flex gap-2">
                                <select name="status" x-model="activeTermin.status" class="w-full text-sm rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 bg-white">
                                    <option value="belum_kirim">Belum Kirim</option>
                                    <option value="proses_kirim">Proses Kirim</option>
                                    <option value="terkirim">Terkirim</option>
                                    <option value="menunggu_konfirmasi">Menunggu Konfirmasi</option>
                                    <option value="lunas">Lunas</option>
                                    <option value="lunas_selisih">Lunas (Ada Selisih)</option>
                                </select>
                            </div>
                              <div class="mt-3">
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="bukti_potong_diterima" class="rounded border-slate-300 text-primary focus:ring-primary h-4 w-4" :checked="activeTermin?.bukti_potong_diterima == 1">
                                    <span class="text-xs font-medium text-on-surface-variant">Bupot Telah Diterima (Khusus PPN)</span>
                                </label>
                            </div>
                            <div class="flex items-center space-x-2 mt-3">
                                <button type="submit" class="w-full px-4 py-2 bg-primary hover:bg-primary/90 text-on-primary rounded-lg text-xs font-semibold transition shadow-sm">
                                    Simpan Status
                                </button>
                            </div>
                        </form>

                        <!-- Quick Export Links -->
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Cetak / Generate File</label>
                            <div class="flex flex-wrap gap-2">
                                <a :href="`/termin/${activeTermin?.id}/surat-jalan`" 
                                   class="px-3 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-lg text-xs font-medium flex items-center shadow-sm">
                                    <span class="material-symbols-outlined text-sm mr-1 text-blue-600">local_shipping</span> Surat Jalan
                                </a>
                                <a :href="`/termin/${activeTermin?.id}/perincian/download`" 
                                   class="px-3 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-lg text-xs font-medium flex items-center shadow-sm">
                                    <span class="material-symbols-outlined text-sm mr-1 text-emerald-600">receipt_long</span> Perincian Pajak
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upload Dokumen / Lampiran Form & List -->
                <div class="bg-surface-container-lowest p-5 rounded-xl border border-surface-border shadow-sm space-y-4">
                    <h4 class="text-sm font-bold text-on-surface font-inter flex items-center">
                        <span class="material-symbols-outlined text-base mr-1.5 text-indigo-600">upload_file</span> 
                        Lampiran Dokumen (BAST, Kwitansi, dll)
                    </h4>

                    <!-- Upload Form -->
                    <form :action="`/termin/${activeTermin?.id}/lampiran`" method="POST" enctype="multipart/form-data" class="bg-surface-container p-3.5 rounded-lg border border-surface-border">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                            <div class="sm:col-span-4">
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Jenis Dokumen</label>
                                <select name="jenis_dokumen" required class="w-full text-xs rounded-md border-slate-300 focus:border-blue-500 focus:ring-blue-500 bg-white">
                                    <option value="">Pilih Jenis</option>
                                    <option value="bast">BAST</option>
                                    <option value="kwitansi">Kwitansi</option>
                                    <option value="surat_pernyataan">Surat Pernyataan</option>
                                    <option value="bukti_potong_pajak">Bukti Potong Pajak</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="sm:col-span-5">
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Pilih File (PDF/Gambar)</label>
                                <input type="file" name="file_lampiran" required class="w-full text-xs text-slate-600 bg-white border border-slate-300 rounded-md file:bg-slate-100 file:border-0 file:px-2 file:py-1 file:text-xs">
                            </div>
                            <div class="sm:col-span-3">
                                <button type="submit" class="w-full px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-xs font-semibold flex items-center justify-center transition shadow-sm">
                                    <span class="material-symbols-outlined text-sm mr-1">upload</span> Unggah
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Uploaded Lampiran Dokumen List -->
                    <div>
                        <p class="text-xs font-semibold text-on-surface-variant mb-2">Dokumen Terunggah:</p>
                        <div class="space-y-2">
                            <template x-if="activeTermin?.lampiran_dokumens && activeTermin.lampiran_dokumens.length > 0">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <template x-for="doc in activeTermin.lampiran_dokumens" :key="doc.id">
                                        <div class="flex items-center justify-between p-2.5 bg-surface-container rounded-lg border border-surface-border text-xs">
                                            <div class="flex items-center space-x-2 truncate">
                                                <span class="material-symbols-outlined text-indigo-500">description</span>
                                                <div class="truncate">
                                                    <p class="font-semibold text-on-surface truncate" x-text="doc.nama_file"></p>
                                                    <p class="text-[10px] text-on-surface-variant uppercase tracking-wider" x-text="doc.jenis_dokumen"></p>
                                                </div>
                                            </div>
                                            <a :href="`/storage/${doc.file}`" target="_blank" class="px-2 py-1 bg-surface-container-lowest border border-surface-border hover:bg-surface-container-high text-on-surface rounded text-[11px] font-medium transition flex items-center">
                                                <span class="material-symbols-outlined text-xs mr-1">visibility</span> Lihat
                                            </a>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="!activeTermin?.lampiran_dokumens || activeTermin.lampiran_dokumens.length === 0">
                                <p class="text-xs text-slate-400 italic py-1">Belum ada dokumen lampiran yang diunggah untuk termin ini.</p>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Daftar Barang / Items Table -->
                <div class="bg-surface-container-lowest p-5 rounded-xl border border-surface-border shadow-sm space-y-3">
                    <h4 class="text-sm font-bold text-on-surface font-inter flex items-center">
                        <span class="material-symbols-outlined text-base mr-1.5 text-emerald-600">inventory</span> 
                        Daftar Barang Termin
                    </h4>

                    <div class="overflow-x-auto border border-surface-border rounded-lg">
                        <table class="min-w-full divide-y divide-surface-border text-xs">
                            <thead class="bg-surface-container">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold text-on-surface-variant w-8">No</th>
                                    <th class="px-3 py-2 text-left font-semibold text-on-surface-variant">Nama Barang</th>
                                    <th class="px-3 py-2 text-right font-semibold text-on-surface-variant w-16">Jumlah</th>
                                    <th class="px-3 py-2 text-left font-semibold text-on-surface-variant w-20">Satuan</th>
                                    <th class="px-3 py-2 text-left font-semibold text-on-surface-variant w-24">Merk</th>
                                    <th class="px-3 py-2 text-right font-semibold text-on-surface-variant w-28">Harga Satuan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-border bg-surface-container-lowest">
                                <template x-if="activeTermin?.item_termins && activeTermin.item_termins.length > 0">
                                    <template x-for="(item, idx) in activeTermin.item_termins" :key="idx">
                                        <tr class="hover:bg-surface-container">
                                            <td class="px-3 py-2 font-jetbrains text-on-surface-variant" x-text="idx + 1"></td>
                                            <td class="px-3 py-2 font-medium text-on-surface" x-text="item.nama_barang"></td>
                                            <td class="px-3 py-2 text-right font-jetbrains font-bold text-on-surface" x-text="item.jumlah"></td>
                                            <td class="px-3 py-2 text-on-surface-variant" x-text="item.satuan"></td>
                                            <td class="px-3 py-2 text-on-surface-variant" x-text="item.merk || '-'"></td>
                                            <td class="px-3 py-2 text-right font-jetbrains font-semibold text-on-surface" x-text="`Rp ${new Intl.NumberFormat('id-ID').format(item.harga_satuan)}`"></td>
                                        </tr>
                                    </template>
                                </template>
                                <template x-if="!activeTermin?.item_termins || activeTermin.item_termins.length === 0">
                                    <tr>
                                        <td colspan="6" class="px-3 py-4 text-center text-on-surface-variant italic">Tidak ada item barang</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3.5 bg-surface-container border-t border-surface-border flex justify-end shrink-0">
                <button type="button" @click="closeModal()" class="px-4 py-2 bg-primary hover:bg-primary/90 text-on-primary rounded-lg text-xs font-semibold transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    </template>
</div>

<style>
    .modal-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .modal-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 8px;
    }
    .modal-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 8px;
    }
    .modal-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
@endsection
