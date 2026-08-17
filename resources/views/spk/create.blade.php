@extends('layouts.app')

@section('content')
<div class="space-y-6 animate-fade-in-up" x-data="spkForm()">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900 font-inter">Input SPK Baru</h1>
        <p class="text-sm text-gray-500 mt-1">Silakan isi formulir di bawah ini atau unggah file PDF SPK untuk pengisian otomatis.</p>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-start" x-data="{ show: true }" x-show="show" x-transition>
            <span class="material-symbols-outlined text-red-500 mr-3">error</span>
            <div class="flex-1">
                <h3 class="text-sm font-medium text-red-800 font-inter">Gagal Menyimpan Data SPK</h3>
                <ul class="list-disc list-inside text-xs text-red-700 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" @click="show = false" class="text-red-500 hover:text-red-700 ml-3">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>
    @endif

    <!-- Dropzone -->
    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:bg-gray-50 transition-colors cursor-pointer relative" id="pdf-dropzone" :class="{'bg-gray-100 border-primary': isUploading}">
        <input type="file" @change="handleFileUpload" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept=".pdf" :disabled="isUploading">
        <span class="material-symbols-outlined text-4xl mb-2" :class="isUploading ? 'text-primary animate-bounce' : 'text-gray-400'">upload_file</span>
        <p class="text-sm font-inter" :class="isUploading ? 'text-primary font-medium' : 'text-gray-600'" x-text="isUploading ? 'Sedang Membaca PDF...' : 'Tarik & lepas file PDF SPK ke sini atau klik untuk memilih file'"></p>
    </div>

    <!-- Success Banner (Hidden by default) -->
    <div x-show="parseSuccess" x-transition class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-start" style="display: none;">
        <span class="material-symbols-outlined text-green-500 mr-3">check_circle</span>
        <div>
            <h3 class="text-sm font-medium text-green-800 font-inter">Berhasil membaca dokumen SPK</h3>
            <p class="text-xs text-green-700 mt-1">Silakan periksa kembali data yang telah diisi secara otomatis sebelum menyimpan.</p>
        </div>
    </div>
    
    <div x-show="parseError" x-transition class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-start" style="display: none;">
        <span class="material-symbols-outlined text-red-500 mr-3">error</span>
        <div>
            <h3 class="text-sm font-medium text-red-800 font-inter">Gagal membaca PDF</h3>
            <p class="text-xs text-red-700 mt-1" x-text="errorMessage"></p>
        </div>
    </div>

    <form action="{{ route('spk.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Master SPK Form -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 font-inter">Data Master SPK</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. SPK</label>
                    <input type="text" 
                           x-model="formData.no_spk" 
                           @blur="checkNoSpk" 
                           @input="noSpkExists = false"
                           name="no_spk" 
                           :class="noSpkExists ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-primary focus:ring-primary'"
                           class="w-full rounded-md shadow-sm focus:ring-opacity-50 font-jetbrains">
                    <p x-show="noSpkExists" x-transition class="text-xs text-red-600 mt-1 flex items-center" style="display: none;">
                        <span class="material-symbols-outlined text-xs mr-1">warning</span> Nomor SPK ini sudah terdaftar di sistem.
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Perusahaan</label>
                    <select x-model="formData.perusahaan_id" name="perusahaan_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <option value="">Pilih Perusahaan</option>
                        @foreach($perusahaans ?? [] as $p)
                            <option value="{{ $p->id }}">{{ $p->nama_perusahaan }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Dinas</label>
                    <input type="text" x-model="formData.nama_dinas" name="nama_dinas" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kabupaten</label>
                    <input type="text" x-model="formData.kabupaten" name="kabupaten" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NPWP Dinas</label>
                    <input type="text" x-model="formData.npwp_dinas" name="npwp_dinas" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 font-jetbrains">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Dinas</label>
                    <textarea x-model="formData.alamat_dinas" name="alamat_dinas" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama PPK</label>
                    <input type="text" x-model="formData.nama_ppk" name="nama_ppk" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan PPK</label>
                    <input type="text" x-model="formData.jabatan_ppk" name="jabatan_ppk" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal SPK</label>
                    <input type="date" x-model="formData.tanggal_spk" name="tanggal_spk" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                </div>
            </div>
        </div>

        <!-- Termin Tabs Section -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="bg-gray-50 border-b border-gray-200 px-4 py-3 flex space-x-2 overflow-x-auto">
                <template x-for="(termin, index) in termins" :key="index">
                    <button type="button" 
                            @click="activeTab = index"
                            :class="activeTab === index ? 'bg-white border-gray-200 text-primary border-t-2 border-t-primary shadow-sm' : 'bg-transparent text-gray-500 hover:text-gray-700'"
                            class="px-4 py-2 text-sm font-medium rounded-t-lg border border-transparent whitespace-nowrap font-inter">
                        Termin <span x-text="index + 1"></span>
                    </button>
                </template>
                <button type="button" @click="addTermin()" class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-primary flex items-center bg-gray-100 rounded-lg">
                    <span class="material-symbols-outlined text-sm mr-1">add</span> Tambah Termin
                </button>
            </div>

            <div class="p-6">
                <template x-for="(termin, index) in termins" :key="index">
                    <div x-show="activeTab === index" class="space-y-6">
                        <div class="flex justify-end" x-show="termins.length > 1">
                            <button type="button" @click="removeTermin(index)" class="text-red-500 hover:text-red-700 flex items-center text-sm font-medium">
                                <span class="material-symbols-outlined text-sm mr-1">delete</span> Hapus Termin
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai Kirim</label>
                                <input type="date" :name="`termins[${index}][tanggal_mulai]`" x-model="termin.tanggal_mulai" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-amber-600 mb-1 flex items-center">
                                    <span class="material-symbols-outlined text-sm mr-1">warning</span> Tanggal Akhir Kirim
                                </label>
                                <input type="date" :name="`termins[${index}][tanggal_akhir]`" x-model="termin.tanggal_akhir" class="w-full rounded-md border-amber-300 bg-amber-50 shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-500 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Termin</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="number" :name="`termins[${index}][nilai]`" x-model="termin.nilai" class="pl-10 w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 font-jetbrains">
                                </div>
                                <p class="text-xs text-gray-500 mt-1 text-right" x-show="termin.nilai">Format: <span class="font-bold text-gray-700" x-text="new Intl.NumberFormat('id-ID').format(termin.nilai)"></span></p>
                            </div>
                            <div class="flex items-end pb-2">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" :name="`termins[${index}][kena_ppn]`" x-model="termin.kena_ppn" class="rounded border-gray-300 text-primary shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700 font-medium">Kena PPN</span>
                                </label>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="mt-6">
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="text-sm font-semibold text-gray-900 font-inter">Daftar Barang</h3>
                                <button type="button" @click="addItem(index)" class="text-xs bg-primary text-white px-3 py-1.5 rounded-md hover:bg-[#003899] flex items-center">
                                    <span class="material-symbols-outlined text-sm mr-1">add</span> Tambah Baris
                                </button>
                            </div>
                            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">No</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Jumlah</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Satuan</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Merk</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">Harga Satuan</th>
                                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <template x-for="(item, itemIndex) in termin.items" :key="itemIndex">
                                            <tr>
                                                <td class="px-4 py-2 text-sm text-gray-900 font-jetbrains" x-text="itemIndex + 1"></td>
                                                <td class="px-4 py-2">
                                                    <input type="text" :name="`termins[${index}][items][${itemIndex}][nama_barang]`" x-model="item.nama_barang" class="w-full text-sm rounded border-gray-300 p-1.5 focus:ring-primary focus:border-primary">
                                                </td>
                                                <td class="px-4 py-2">
                                                    <input type="number" :name="`termins[${index}][items][${itemIndex}][jumlah]`" x-model="item.jumlah" class="w-full text-sm rounded border-gray-300 p-1.5 focus:ring-primary focus:border-primary font-jetbrains">
                                                </td>
                                                <td class="px-4 py-2">
                                                    <input type="text" :name="`termins[${index}][items][${itemIndex}][satuan]`" x-model="item.satuan" class="w-full text-sm rounded border-gray-300 p-1.5 focus:ring-primary focus:border-primary">
                                                </td>
                                                <td class="px-4 py-2">
                                                    <input type="text" :name="`termins[${index}][items][${itemIndex}][merk]`" x-model="item.merk" class="w-full text-sm rounded border-gray-300 p-1.5 focus:ring-primary focus:border-primary">
                                                </td>
                                                <td class="px-4 py-2 relative">
                                                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none" style="top: 8px; height: 34px;">
                                                        <span class="text-gray-500 text-xs">Rp</span>
                                                    </div>
                                                    <input type="number" :name="`termins[${index}][items][${itemIndex}][harga_satuan]`" x-model="item.harga_satuan" class="w-full text-sm pl-8 rounded border-gray-300 p-1.5 focus:ring-primary focus:border-primary font-jetbrains">
                                                    <p class="text-[10px] text-gray-500 mt-1 text-right" x-show="item.harga_satuan" x-text="new Intl.NumberFormat('id-ID').format(item.harga_satuan)"></p>
                                                </td>
                                                <td class="px-4 py-2 text-center">
                                                    <button type="button" @click="removeItem(index, itemIndex)" class="text-red-500 hover:text-red-700" :disabled="termin.items.length === 1">
                                                        <span class="material-symbols-outlined text-sm">delete</span>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('dashboard') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Batal
            </a>
            <button type="submit" 
                    :disabled="noSpkExists" 
                    :class="noSpkExists ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'bg-primary hover:bg-[#003899]'" 
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                Simpan SPK
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('spkForm', () => ({
            activeTab: 0,
            isUploading: false,
            parseSuccess: false,
            parseError: false,
            errorMessage: '',
            noSpkExists: false,
            formData: {
                no_spk: '{{ old('no_spk', '') }}',
                perusahaan_id: '{{ old('perusahaan_id', '') }}',
                nama_dinas: '{{ old('nama_dinas', '') }}',
                kabupaten: '{{ old('kabupaten', '') }}',
                npwp_dinas: '{{ old('npwp_dinas', '') }}',
                alamat_dinas: '{{ old('alamat_dinas', '') }}',
                nama_ppk: '{{ old('nama_ppk', '') }}',
                jabatan_ppk: '{{ old('jabatan_ppk', '') }}',
                tanggal_spk: '{{ old('tanggal_spk', '') }}'
            },
            termins: (() => {
                const oldTermins = @json(old('termins'));
                if (oldTermins && oldTermins.length > 0) {
                    return oldTermins.map(termin => ({
                        tanggal_mulai: termin.tanggal_mulai || '',
                        tanggal_akhir: termin.tanggal_akhir || '',
                        nilai: termin.nilai || '',
                        kena_ppn: termin.kena_ppn === 'on' || termin.kena_ppn === true || termin.kena_ppn === 1,
                        items: (termin.items || []).map(item => ({
                            nama_barang: item.nama_barang || '',
                            jumlah: item.jumlah || '',
                            satuan: item.satuan || '',
                            merk: item.merk || '',
                            harga_satuan: item.harga_satuan || ''
                        }))
                    }));
                }
                return [
                    {
                        tanggal_mulai: '',
                        tanggal_akhir: '',
                        nilai: '',
                        kena_ppn: true,
                        items: [
                            { nama_barang: '', jumlah: '', satuan: '', merk: '', harga_satuan: '' }
                        ]
                    }
                ];
            })(),
            async handleFileUpload(event) {
                const file = event.target.files[0];
                if (!file) return;
                
                this.isUploading = true;
                this.parseSuccess = false;
                this.parseError = false;
                
                const formData = new FormData();
                formData.append('file', file);
                
                try {
                    const response = await fetch('{{ route('spk.parsePdf') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    
                    const result = await response.json();
                    
                    if (response.ok && result.success) {
                        this.noSpkExists = false;
                        // Populate form data
                        this.formData.no_spk = result.data.no_spk || this.formData.no_spk;
                        if (result.data.perusahaan_id) this.formData.perusahaan_id = result.data.perusahaan_id;
                        this.formData.tanggal_spk = result.data.tanggal_spk || this.formData.tanggal_spk;
                        this.formData.nama_dinas = result.data.nama_dinas || this.formData.nama_dinas;
                        this.formData.kabupaten = result.data.kabupaten || this.formData.kabupaten;
                        this.formData.npwp_dinas = result.data.npwp_dinas || this.formData.npwp_dinas;
                        this.formData.alamat_dinas = result.data.alamat_dinas || this.formData.alamat_dinas;
                        this.formData.nama_ppk = result.data.nama_ppk || this.formData.nama_ppk;
                        this.formData.jabatan_ppk = result.data.jabatan_ppk || this.formData.jabatan_ppk;
                        
                        // Populate termins
                        if (result.data.termins && result.data.termins.length > 0) {
                            this.termins = result.data.termins.map(termin => {
                                return {
                                    tanggal_mulai: termin.tanggal_mulai || '',
                                    tanggal_akhir: termin.tanggal_akhir || '',
                                    nilai: termin.nilai_termin || '',
                                    kena_ppn: termin.kena_ppn !== undefined ? termin.kena_ppn : true,
                                    items: termin.items && termin.items.length > 0 ? termin.items.map(item => ({
                                        nama_barang: item.nama_barang || '',
                                        jumlah: item.jumlah || '',
                                        satuan: item.satuan || '',
                                        merk: item.merk || '',
                                        harga_satuan: item.harga_satuan || ''
                                    })) : [{ nama_barang: '', jumlah: '', satuan: '', merk: '', harga_satuan: '' }]
                                };
                            });
                        }

                        this.parseSuccess = true;
                    } else {
                        this.parseError = true;
                        this.errorMessage = result.message || 'Terjadi kesalahan saat memproses file.';
                    }
                } catch (error) {
                    this.parseError = true;
                    this.errorMessage = 'Gagal terhubung ke server.';
                } finally {
                    this.isUploading = false;
                    event.target.value = ''; // Reset file input
                }
            },
            async checkNoSpk() {
                if (!this.formData.no_spk) {
                    this.noSpkExists = false;
                    return;
                }
                try {
                    const response = await fetch(`/spk/check-duplicate?no_spk=${encodeURIComponent(this.formData.no_spk)}`);
                    const result = await response.json();
                    this.noSpkExists = result.exists;
                } catch (error) {
                    console.error('Gagal mengecek nomor SPK:', error);
                }
            },
            addTermin() {
                this.termins.push({
                    tanggal_mulai: '',
                    tanggal_akhir: '',
                    nilai: '',
                    kena_ppn: true,
                    items: [
                        { nama_barang: '', jumlah: '', satuan: '', merk: '', harga_satuan: '' }
                    ]
                });
                this.activeTab = this.termins.length - 1;
            },
            removeTermin(index) {
                if(this.termins.length > 1) {
                    this.termins.splice(index, 1);
                    this.activeTab = Math.max(0, this.activeTab - 1);
                }
            },
            addItem(terminIndex) {
                this.termins[terminIndex].items.push({ nama_barang: '', jumlah: '', satuan: '', merk: '', harga_satuan: '' });
            },
            removeItem(terminIndex, itemIndex) {
                if(this.termins[terminIndex].items.length > 1) {
                    this.termins[terminIndex].items.splice(itemIndex, 1);
                }
            }
        }))
    })
</script>
@endsection
