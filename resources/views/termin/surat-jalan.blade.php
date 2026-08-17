@extends('layouts.app')

@section('content')
    <div class="mb-4 animate-fade-in-up">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Surat Jalan - {{ $termin->spk->perusahaan->nama_perusahaan }}
        </h2>
    </div>

    <!-- Tailwind Config injected for preview colors from template -->
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "surface-container-low": "#f3f3fe",
                        "surface-container-highest": "#e1e2ed",
                        "surface-container-lowest": "#ffffff",
                        "surface-neutral": "#F8FAFC",
                        "on-surface": "#191b23",
                        "on-surface-variant": "#434655",
                        "outline-variant": "#c3c6d7",
                        "primary": "#004ac6",
                        "on-primary": "#ffffff",
                        "primary-container": "#2563eb",
                        "secondary": "#505f76",
                        "border-hairline": "#E2E8F0",
                        "success-green": "#22C55E",
                        "danger-red": "#EF4444",
                    },
                    fontFamily: {
                        "label-sm": ["Inter", "sans-serif"],
                        "headline-h2": ["Inter", "sans-serif"],
                        "data-mono": ["JetBrains Mono", "monospace"],
                        "headline-h3": ["Inter", "sans-serif"],
                        "body-main": ["Inter", "sans-serif"]
                    },
                }
            }
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            .print-canvas { box-shadow: none !important; border: none !important; margin: 0 !important; padding: 0 !important; }
        }
    </style>

    <div class="py-6" x-data="{
        perusahaan: '{{ strtoupper(trim($termin->spk->perusahaan->nama_perusahaan)) }}',
        noSuratJalan: '{{ $termin->suratJalan->no_surat_jalan ?? '' }}',
        tanggal: '{{ $termin->suratJalan?->tanggal_kirim?->format('Y-m-d') ?? date('Y-m-d') }}',
        
        get formatTanggal() {
            if(!this.tanggal) return '';
            const d = new Date(this.tanggal);
            const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <form action="{{ route('termin.storeSuratJalan', $termin->id) }}" method="POST">
                @csrf
                
                <!-- Action Bar & Form Input -->
                <div class="max-w-4xl mx-auto flex flex-col md:flex-row justify-between items-center mb-6 gap-4 no-print">
                    <div class="flex items-center gap-2 text-gray-500 hover:text-gray-900 transition-colors">
                        <a href="{{ route('arsip.index') }}" class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                            Kembali ke Arsip
                        </a>
                    </div>

                    <!-- Input Controls -->
                    <div class="flex items-center gap-4 bg-white p-3 rounded-lg shadow-sm border border-gray-200">
                        <div>
                            <label class="block text-xs font-medium text-gray-700">No. Surat Jalan</label>
                            <input type="text" name="no_surat_jalan" x-model="noSuratJalan" required class="mt-1 block w-48 border-gray-300 focus:border-primary focus:ring-primary sm:text-sm rounded-md h-9">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Tanggal</label>
                            <input type="date" name="tanggal_kirim" x-model="tanggal" required class="mt-1 block w-40 border-gray-300 focus:border-primary focus:ring-primary sm:text-sm rounded-md h-9">
                        </div>
                    </div>
                    
                    <div class="flex gap-3">
                        <button type="button" onclick="window.print()" class="flex items-center gap-2 bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-all duration-200 ease-out active:scale-[0.97] text-sm font-medium shadow-sm">
                            <span class="material-symbols-outlined text-[20px]">print</span>
                            Cetak
                        </button>
                        <button type="submit" class="flex items-center gap-2 bg-success-green text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-all duration-200 ease-out active:scale-[0.97] text-sm font-medium shadow-sm hover:shadow-md hover:brightness-110">
                            <span class="material-symbols-outlined text-[20px]">download</span>
                            Simpan & Unduh Excel
                        </button>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="max-w-4xl mx-auto mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative no-print">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- CANVAS WRAPPER -->
                <div class="max-w-4xl mx-auto bg-white border border-gray-200 rounded shadow-sm p-8 md:p-12 text-gray-900 relative print-canvas">
                    
                    <div class="absolute top-0 right-0 bg-gray-100 text-gray-500 text-xs px-3 py-1 rounded-bl border-b border-l border-gray-200 no-print">
                        Preview Layout: <span x-text="perusahaan" class="font-bold"></span>
                    </div>

                    <!-- ============================================== -->
                    <!-- TEMPLATE WTM -->
                    <!-- ============================================== -->
                    <template x-if="perusahaan === 'WTM'">
                        <div class="w-full pt-32"> <!-- pt-32 memberikan ruang kosong untuk kop -->
                            <!-- Header -->
                            <div class="flex justify-between items-start mb-10">
                                <div>
                                    <h1 class="text-[22px] font-medium text-gray-900 mb-1 uppercase tracking-wide" style="font-family: 'Inter', sans-serif;">SURAT JALAN</h1>
                                    <p class="text-sm text-gray-900" style="font-family: 'JetBrains Mono', monospace;">No. <span x-text="noSuratJalan || '[Ketik Nomor]'"></span></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-900" style="font-family: 'Inter', sans-serif;">Denpasar, <span x-text="formatTanggal || '...'"></span></p>
                                </div>
                            </div>
                            
                            <!-- Kepada -->
                            <div class="mb-8 text-sm text-gray-900" style="font-family: 'Inter', sans-serif;">
                                <p class="mb-1">Kepada :</p>
                                <p class="mb-1">{{ $termin->spk->nama_dinas }}</p>
                                <div class="flex">
                                    <span class="mr-2">di -</span>
                                    <span class="ml-4">Mangupura</span>
                                </div>
                            </div>
                            
                            <!-- Opening -->
                            <div class="mb-6 space-y-4 text-sm text-gray-900" style="font-family: 'Inter', sans-serif;">
                                <p>Dengan hormat,</p>
                                <p class="leading-relaxed text-justify">
                                    Mohon dapat diterima barang - barang sesuai dengan Surat Pesanan Nomor : <span style="font-family: 'JetBrains Mono', monospace;">{{ $termin->spk->no_spk }}</span>, tanggal {{ \Carbon\Carbon::parse($termin->spk->tanggal_spk ?? now())->translatedFormat('j F Y') }} , sebagai berikut :
                                </p>
                            </div>
                            
                            <!-- Data Table -->
                            <div class="mb-12 border border-gray-200 rounded-sm overflow-hidden">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="bg-gray-50 border-b border-gray-200">
                                            <th class="p-3 text-sm font-medium border-r border-gray-200 w-16 text-center">No</th>
                                            <th class="p-3 text-sm font-medium border-r border-gray-200 text-center">Nama Barang</th>
                                            <th class="p-3 text-sm font-medium border-r border-gray-200 w-32 text-center">Volume</th>
                                            <th class="p-3 text-sm font-medium w-48 text-center">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-sm">
                                        @forelse($termin->itemTermins as $index => $item)
                                        <tr class="border-b border-gray-200 {{ $index % 2 == 1 ? 'bg-gray-50' : '' }}">
                                            <td class="p-3 border-r border-gray-200 text-center" style="font-family: 'JetBrains Mono', monospace;">{{ $index + 1 }}</td>
                                            <td class="p-3 border-r border-gray-200">{{ $item->nama_barang }}</td>
                                            <td class="p-3 border-r border-gray-200 text-right flex justify-end gap-2" style="font-family: 'JetBrains Mono', monospace;">
                                                <span class="w-12 text-right">{{ $item->jumlah }}</span> 
                                                <span class="w-8 text-left text-gray-500">{{ $item->satuan }}</span>
                                            </td>
                                            <td class="p-3">{{ $item->catatan }}</td>
                                        </tr>
                                        @empty
                                        <tr class="border-b border-gray-200">
                                            <td colspan="4" class="p-3 text-center text-gray-500">Tidak ada item</td>
                                        </tr>
                                        @endforelse
                                        <!-- Empty rows to fill space -->
                                        <tr class="border-b border-gray-200 h-10"><td class="border-r border-gray-200"></td><td class="border-r border-gray-200"></td><td class="border-r border-gray-200"></td><td></td></tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Footer Signatures -->
                            <div class="flex justify-between items-start mt-24 px-4 text-sm" style="font-family: 'Inter', sans-serif;">
                                <div class="flex flex-col items-start w-64">
                                    <p class="mb-24">Yang Menerima,</p>
                                    <p class="w-full border-b border-dashed border-gray-400 pb-1 text-center text-transparent select-none">(.........................)</p>
                                    <p class="w-full text-center -mt-5">(.........................)</p>
                                </div>
                                <div class="flex flex-col items-start w-64">
                                    <p class="mb-1">Yang Menyerahkan</p>
                                    <p class="font-medium mb-24 uppercase">CV. WAHANA TATA MANDIRI</p>
                                    <p class="underline font-medium w-full">I Made Suastika, SE</p>
                                    <p class="w-full mt-1">Direktur</p>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- ============================================== -->
                    <!-- TEMPLATE WKB -->
                    <!-- ============================================== -->
                    <template x-if="perusahaan === 'WKB' || perusahaan === 'CV. WIJAYA KARYA BUANA'">
                        <div class="w-full">
                            <!-- Header: Company Info -->
                            <div class="flex items-center justify-center gap-6 mb-4">
                                <div class="w-20 h-20 rounded-full bg-blue-600 flex items-center justify-center overflow-hidden shrink-0">
                                    <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuC1WPJr7WCP5JPyoxNbPkBZIFx0o2G1Ij4vuY71wkYWn5rrIeFwxrdz5gs1bVipgj1jTuHebU5AcIPwPMg-sRUKcHKIiRv0j3j_s-dCtHUmP9vfCIQqjveP6vcEf9-hTCKLlJluRVeQcf8er3ZO-JzWltdTI9KvCqs5YQ9-F6Kaps8fwBX3zuY8pVgp7gEP4UyNHon6cKhwWRVTkob4YCt-CUYIJh0AcHEPrJf6EQd5eZrzqxW7JV_y" class="w-full h-full object-cover opacity-90" alt="Logo">
                                </div>
                                <div class="text-center" style="font-family: 'Inter', sans-serif;">
                                    <h1 class="text-[28px] leading-tight font-medium text-blue-700 uppercase tracking-wide">
                                        CV. WIJAYA KARYA BUANA
                                    </h1>
                                    <p class="text-gray-600 mt-2">
                                        Jln. Gunung Andakasa Perum Cempaka Indah Blok. A/2 Denpasar
                                    </p>
                                    <p class="text-gray-600">
                                        Telp. : (0361) 429509
                                    </p>
                                </div>
                            </div>
                            
                            <hr class="border-t-[3px] border-blue-700 mb-8 mt-2 w-full"/>
                            
                            <!-- Meta Data Row -->
                            <div class="flex justify-between items-start mb-8">
                                <!-- Left Side -->
                                <div>
                                    <h2 class="text-[18px] font-medium text-gray-900 uppercase mb-1" style="font-family: 'Inter', sans-serif;">SURAT JALAN</h2>
                                    <div class="text-gray-900" style="font-family: 'JetBrains Mono', monospace;">
                                        No. <span x-text="noSuratJalan || '[Ketik Nomor]'"></span>
                                    </div>
                                </div>
                                <!-- Right Side -->
                                <div class="max-w-xs text-sm text-gray-900" style="font-family: 'Inter', sans-serif;">
                                    <div class="font-medium mb-1 uppercase">KEPADA YTH:</div>
                                    <div class="leading-relaxed">
                                        {{ $termin->spk->nama_dinas }}<br/>
                                        di -<br/>
                                        Mangupura
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Data Table -->
                            <div class="overflow-x-auto mb-16">
                                <table class="w-full border-collapse border border-gray-400 text-left">
                                    <thead>
                                        <tr>
                                            <th class="border border-gray-400 p-3 text-sm font-medium text-center w-16">No</th>
                                            <th class="border border-gray-400 p-3 text-sm font-medium text-center">Nama Barang</th>
                                            <th class="border border-gray-400 p-3 text-sm font-medium text-center w-24">Jumlah</th>
                                            <th class="border border-gray-400 p-3 text-sm font-medium text-center w-32">Satuan</th>
                                            <th class="border border-gray-400 p-3 text-sm font-medium text-center w-48">Merk</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($termin->itemTermins as $index => $item)
                                        <tr>
                                            <td class="border border-gray-400 p-3 text-center" style="font-family: 'JetBrains Mono', monospace;">{{ $index + 1 }}</td>
                                            <td class="border border-gray-400 p-3 text-sm">{{ $item->nama_barang }}</td>
                                            <td class="border border-gray-400 p-3 text-right" style="font-family: 'JetBrains Mono', monospace;">{{ $item->jumlah }}</td>
                                            <td class="border border-gray-400 p-3 text-center text-sm">{{ $item->satuan }}</td>
                                            <td class="border border-gray-400 p-3 text-center text-sm">{{ $item->merk }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="border border-gray-400 p-3 text-center text-gray-500">Tidak ada item</td>
                                        </tr>
                                        @endforelse
                                        <tr class="h-12">
                                            <td class="border border-gray-400 p-3"></td>
                                            <td class="border border-gray-400 p-3"></td>
                                            <td class="border border-gray-400 p-3"></td>
                                            <td class="border border-gray-400 p-3"></td>
                                            <td class="border border-gray-400 p-3"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Signatures Footer -->
                            <div class="flex justify-between items-end mt-12 px-4 text-sm" style="font-family: 'Inter', sans-serif;">
                                <!-- Receiver Signature -->
                                <div class="text-center">
                                    <p class="mb-1">Yang Menerima,</p>
                                    <p class="mb-24">{{ $termin->spk->nama_dinas }}</p>
                                    <p class="text-gray-600">(........................................)</p>
                                </div>
                                <!-- Sender Signature -->
                                <div class="text-center relative">
                                    <p class="mb-1">Yang Menyerahkan</p>
                                    <p class="mb-4">CV. WIJAYA KARYA BUANA</p>
                                    
                                    <div class="h-24 w-40 mx-auto relative mb-2 flex items-center justify-center">
                                        <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDZoismo3mQ2ug3hh11y5VuNkGa0EOf8olzWDB-bqQWg60Nhb3hebGSz_D6nAW6REfxRpuybrk5k-fhGn6bbEYV7IXMBvZTWpGLrLxS7OZfoNUSrLCrXaAI-bnJ0OhzjF6_B3EDPqbDpq701NUwyJhiXw3Avv_Mhi8XFt0Ee2nyExMenGxmJCrcxmms5io5mMJl9TRqk7OvMH5GwYRsRRblXrmP651VUlRohehGuWmCo3h1vSqWsszt" class="absolute inset-0 w-full h-full object-contain mix-blend-multiply opacity-80 pointer-events-none" alt="Signature">
                                    </div>
                                    <p class="underline underline-offset-4 font-medium">
                                        (I Wayan Kastawa. SE)
                                    </p>
                                    <p class="mt-1">Direktur</p>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- ============================================== -->
                    <!-- TEMPLATE WAM -->
                    <!-- ============================================== -->
                    <template x-if="perusahaan === 'WAHANA AGRO MANDIRI' || perusahaan === 'WAM'">
                        <div class="w-full pt-32"> <!-- pt-32 memberikan ruang kosong untuk kop -->
                            <!-- Top Section: Header & Date -->
                            <div class="flex justify-between items-start mb-12 text-gray-900" style="font-family: 'Inter', sans-serif;">
                                <div>
                                    <h1 class="text-[18px] font-medium mb-2">SURAT JALAN</h1>
                                    <p class="text-sm">No. <span style="font-family: 'JetBrains Mono', monospace;" x-text="noSuratJalan || '[Ketik Nomor]'"></span></p>
                                </div>
                                <div class="text-right sm:text-left sm:w-64 text-sm">
                                    <p class="mb-6">Denpasar, <span style="font-family: 'JetBrains Mono', monospace;" x-text="formatTanggal || '...'"></span></p>
                                    <div>
                                        <p class="font-medium mb-1">KEPADA YTH:</p>
                                        <p>{{ $termin->spk->nama_dinas }}</p>
                                        <p>di -</p>
                                        <p>Mangupura</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Middle Section: Opening Text -->
                            <div class="mb-6 text-sm text-gray-900 leading-relaxed" style="font-family: 'Inter', sans-serif;">
                                <p>Dengan hormat,</p>
                                <p class="mt-4 text-justify">
                                    Mohon dapat diterima barang - barang sesuai dengan Surat Pesanan Nomor : 
                                    <span style="font-family: 'JetBrains Mono', monospace;">{{ $termin->spk->no_spk }}</span>, 
                                    Tanggal <span style="font-family: 'JetBrains Mono', monospace;">{{ \Carbon\Carbon::parse($termin->spk->tanggal_spk ?? now())->translatedFormat('j F Y') }}</span> sebagai berikut :
                                </p>
                            </div>
                            
                            <!-- Data Table -->
                            <div class="mb-16">
                                <table class="w-full border-collapse border border-gray-900">
                                    <thead>
                                        <tr>
                                            <th class="border border-gray-900 py-3 px-4 text-sm font-medium text-center w-16">No</th>
                                            <th class="border border-gray-900 py-3 px-4 text-sm font-medium text-center">Nama Barang</th>
                                            <th class="border border-gray-900 py-3 px-4 text-sm font-medium text-center w-24">Jumlah</th>
                                            <th class="border border-gray-900 py-3 px-4 text-sm font-medium text-center w-32">Satuan</th>
                                            <th class="border border-gray-900 py-3 px-4 text-sm font-medium text-center w-40">Merk</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-sm">
                                        @forelse($termin->itemTermins as $index => $item)
                                        <tr>
                                            <td class="border border-gray-900 py-3 px-4 text-center" style="font-family: 'JetBrains Mono', monospace;">{{ $index + 1 }}</td>
                                            <td class="border border-gray-900 py-3 px-4">{{ $item->nama_barang }}</td>
                                            <td class="border border-gray-900 py-3 px-4 text-right" style="font-family: 'JetBrains Mono', monospace;">{{ $item->jumlah }}</td>
                                            <td class="border border-gray-900 py-3 px-4 text-center">{{ $item->satuan }}</td>
                                            <td class="border border-gray-900 py-3 px-4 text-center">{{ $item->merk }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="border border-gray-900 py-3 px-4 text-center text-gray-500">Tidak ada item</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Bottom Section: Signatures -->
                            <div class="mt-auto flex justify-between text-sm text-gray-900" style="font-family: 'Inter', sans-serif;">
                                <!-- Receiver Sign -->
                                <div class="w-64 flex flex-col items-start">
                                    <p class="mb-1">Yang Menerima,</p>
                                    <p>{{ $termin->spk->nama_dinas }}</p>
                                    <div class="h-24"></div>
                                    <p>(..........................................)</p>
                                </div>
                                <!-- Sender Sign -->
                                <div class="w-64 flex flex-col items-center text-center">
                                    <p class="mb-1">Yang Menyerahkan</p>
                                    <p>CV. WAHANA AGRO MANDIRI</p>
                                    <div class="h-24"></div>
                                    <p class="font-medium underline underline-offset-2">(Drh. I Made Alit Neker)</p>
                                    <p>Direktur</p>
                                </div>
                            </div>
                        </div>
                    </template>

                </div>
            </form>

        </div>
    </div>
@endsection
