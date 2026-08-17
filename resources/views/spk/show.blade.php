@extends('layouts.app')

@section('content')
<div class="space-y-6 animate-fade-in-up">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-bold text-gray-900 font-jetbrains">No. SPK: {{ $spk->no_spk ?? '-' }}</h1>
                @if(($spk->status ?? 'aktif') === 'aktif')
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">Aktif</span>
                @else
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200">Selesai</span>
                @endif
            </div>
            <p class="text-sm text-gray-500 mt-1">Dibuat pada: {{ optional($spk->created_at)->format('d F Y') ?? '-' }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm flex items-center">
                <span class="material-symbols-outlined text-sm mr-2">arrow_back</span> Kembali
            </a>
            <!--
            <a href="#" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm flex items-center">
                <span class="material-symbols-outlined text-sm mr-2">edit</span> Edit SPK
            </a>
            -->
        </div>
    </div>

    <!-- Master SPK Info -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900 font-inter flex items-center">
                <span class="material-symbols-outlined mr-2 text-gray-500">info</span> Data Master SPK
            </h2>
        </div>
        <div class="p-6">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Perusahaan</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $spk->perusahaan->nama_perusahaan ?? $spk->perusahaan->nama ?? '-' }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Tanggal SPK</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ optional($spk->tanggal_spk)->format('d F Y') ?? '-' }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Nama Dinas</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $spk->nama_dinas ?? '-' }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Kabupaten</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $spk->kabupaten ?? '-' }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">NPWP Dinas</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-jetbrains">{{ $spk->npwp_dinas ?? '-' }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Alamat Dinas</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $spk->alamat_dinas ?? '-' }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Nama PPK</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $spk->nama_ppk ?? '-' }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Jabatan PPK</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $spk->jabatan_ppk ?? '-' }}</dd>
                </div>
                @php
                    $firstTermin = $spk->termins->first();
                    $tglMulai = $firstTermin ? ($firstTermin->tanggal_mulai_kirim ?? $firstTermin->tanggal_mulai) : null;
                    $tglAkhir = $firstTermin ? ($firstTermin->tanggal_akhir_kirim ?? $firstTermin->tanggal_akhir) : null;
                @endphp
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Permintaan Tiba (Jadwal Kirim)</dt>
                    <dd class="mt-1 text-sm font-medium text-amber-700 flex items-center">
                        <span class="material-symbols-outlined text-base mr-1">event</span>
                        {{ $tglMulai ? $tglMulai->format('d F Y') : '-' }} 
                        <span class="text-gray-400 mx-1.5">s/d</span> 
                        {{ $tglAkhir ? $tglAkhir->format('d F Y') : '-' }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Termins List -->
    <div class="space-y-4">
        <h2 class="text-xl font-bold text-gray-900 font-inter mb-4">Daftar Termin & Pengiriman</h2>
        
        @forelse ($spk->termins ?? [] as $index => $termin)
            <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-200">
                <!-- Termin Header -->
                <div class="px-6 py-4 border-b border-gray-200 bg-surface flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 font-inter">Termin {{ $index + 1 }}</h3>
                        <div class="flex items-center space-x-2 mt-1">
                            <span class="material-symbols-outlined text-sm text-gray-500">schedule</span>
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Permintaan Tiba:</span>
                            <span class="text-sm font-medium text-gray-900">{{ optional($termin->tanggal_mulai_kirim ?? $termin->tanggal_mulai)->format('d/m/Y') ?? '-' }}</span>
                            <span class="text-xs text-gray-400">s/d</span>
                            <span class="text-sm font-semibold text-amber-700">{{ optional($termin->tanggal_akhir_kirim ?? $termin->tanggal_akhir)->format('d/m/Y') ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-right mr-4">
                            <p class="text-xs text-gray-500">Nilai Termin</p>
                            <p class="text-lg font-bold text-gray-900 font-jetbrains">Rp {{ number_format($termin->nilai_termin ?? 0, 0, ',', '.') }}</p>
                        </div>
                        
                        <!-- Status Badge -->
                        @php
                            $status = $termin->status ?? 'belum_kirim';
                        @endphp
                        @if($status === 'belum_kirim')
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-gray-100 text-gray-800">Belum Kirim</span>
                        @elseif($status === 'terkirim')
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">Terkirim</span>
                        @elseif($status === 'menunggu_konfirmasi')
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-amber-100 text-amber-800">Menunggu Konfirmasi</span>
                        @elseif($status === 'lunas')
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">Lunas</span>
                        @elseif($status === 'lunas_selisih')
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800" title="Lunas dengan selisih">Lunas (Selisih)</span>
                        @endif
                    </div>
                </div>

                <!-- Termin Actions -->
                <div class="px-6 py-3 bg-gray-50 border-b border-gray-200 flex flex-wrap gap-3">
                    <a href="{{ route('termin.suratJalan', $termin->id) }}" class="px-3 py-1.5 bg-white border border-gray-300 rounded text-sm font-medium text-gray-700 hover:bg-gray-50 flex items-center shadow-sm">
                        <span class="material-symbols-outlined text-sm mr-1.5">local_shipping</span> Surat Jalan
                    </a>
                    <a href="{{ route('termin.downloadPerincian', $termin->id) }}" class="px-3 py-1.5 bg-white border border-gray-300 rounded text-sm font-medium text-gray-700 hover:bg-gray-50 flex items-center shadow-sm">
                        <span class="material-symbols-outlined text-sm mr-1.5">receipt_long</span> Perincian Pajak (Excel)
                    </a>
                    
                    <form action="{{ route('termin.uploadLampiran', $termin->id) }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2 border-l border-gray-300 pl-3 ml-2">
                        @csrf
                        <select name="jenis_dokumen" required class="text-sm border-gray-300 rounded py-1 pl-2 pr-6 h-8 text-gray-700">
                            <option value="">Pilih Jenis</option>
                            <option value="bast">BAST</option>
                            <option value="kwitansi">Kwitansi</option>
                            <option value="surat_pernyataan">Surat Pernyataan</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                        <input type="file" name="file_lampiran" required class="text-sm w-48 bg-white border border-gray-300 rounded file:bg-gray-100 file:border-0 file:px-2 file:py-1 file:text-sm text-gray-700 h-8">
                        <button type="submit" class="px-3 py-1.5 bg-white border border-gray-300 rounded text-sm font-medium text-gray-700 hover:bg-gray-50 flex items-center shadow-sm h-8">
                            <span class="material-symbols-outlined text-sm mr-1">upload</span> Upload
                        </button>
                    </form>
                    <form action="{{ route('termin.updateStatus', $termin->id) }}" method="POST" class="flex items-center gap-2 ml-auto">
                        @csrf
                        @method('PATCH')
                        <select name="status" onchange="this.form.submit()" class="text-sm border border-gray-300 rounded py-1 pl-2 pr-8 h-8 text-gray-700 font-medium focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm cursor-pointer">
                            <option value="belum_kirim" {{ ($termin->status ?? '') == 'belum_kirim' ? 'selected' : '' }}>Status: Belum Kirim</option>
                            <option value="proses_kirim" {{ ($termin->status ?? '') == 'proses_kirim' ? 'selected' : '' }}>Status: Proses Kirim</option>
                            <option value="terkirim" {{ ($termin->status ?? '') == 'terkirim' ? 'selected' : '' }}>Status: Terkirim</option>
                            <option value="menunggu_konfirmasi" {{ ($termin->status ?? '') == 'menunggu_konfirmasi' ? 'selected' : '' }}>Status: Menunggu Konfirmasi</option>
                            <option value="lunas" {{ ($termin->status ?? '') == 'lunas' ? 'selected' : '' }}>Status: Lunas</option>
                            <option value="lunas_selisih" {{ ($termin->status ?? '') == 'lunas_selisih' ? 'selected' : '' }}>Status: Lunas (Selisih)</option>
                        </select>
                    </form>
                </div>

                <!-- Termin Items -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">No</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merk</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Satuan</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Harga</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($termin->itemTermins ?? [] as $itemIndex => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900 font-jetbrains">{{ $itemIndex + 1 }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ $item->nama_barang ?? '-' }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900 text-right font-jetbrains">{{ $item->jumlah ?? 0 }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">{{ $item->satuan ?? '-' }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">{{ $item->merk ?? '-' }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900 text-right font-jetbrains">Rp {{ number_format($item->harga_satuan ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm font-semibold text-gray-900 text-right font-jetbrains">Rp {{ number_format(($item->jumlah ?? 0) * ($item->harga_satuan ?? 0), 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 italic">Tidak ada barang untuk termin ini</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg border-2 border-dashed border-gray-300 p-8 text-center">
                <span class="material-symbols-outlined text-4xl text-gray-400 mb-2">assignment_late</span>
                <p class="text-gray-500 font-medium">Belum ada termin untuk SPK ini</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
