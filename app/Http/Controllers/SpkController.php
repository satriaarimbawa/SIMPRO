<?php

namespace App\Http\Controllers;

use App\Models\Perusahaan;
use App\Models\Spk;
use App\Models\SpkItem;
use App\Models\Termin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpkController extends Controller
{
    public function create()
    {
        $perusahaans = Perusahaan::all();
        return view('spk.create', compact('perusahaans'));
    }

    public function checkDuplicate(Request $request)
    {
        $noSpk = $request->query('no_spk');
        if (empty($noSpk)) {
            return response()->json(['exists' => false]);
        }
        $exists = Spk::where('no_spk', $noSpk)->exists();
        return response()->json(['exists' => $exists]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'perusahaan_id' => 'required|exists:perusahaans,id',
            'no_spk' => 'required|string|unique:spks,no_spk',
            'tanggal_spk' => 'required|date',
            'nama_dinas' => 'required|string',
            'kabupaten' => 'required|string',
            'npwp_dinas' => 'nullable|string',
            'alamat_dinas' => 'nullable|string',
            'nama_ppk' => 'nullable|string',
            'jabatan_ppk' => 'nullable|string',
            
            'termins' => 'required|array|min:1',
            'termins.*.tanggal_mulai' => 'nullable|date',
            'termins.*.tanggal_akhir' => 'required|date',
            'termins.*.nilai' => 'required|numeric',
            'termins.*.kena_ppn' => 'nullable', // Checkbox sends "on"
            
            'termins.*.items' => 'required|array|min:1',
            'termins.*.items.*.nama_barang' => 'required|string',
            'termins.*.items.*.jumlah' => 'required|numeric',
            'termins.*.items.*.satuan' => 'nullable|string',
            'termins.*.items.*.merk' => 'nullable|string',
            'termins.*.items.*.harga_satuan' => 'required|numeric',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $spk = Spk::create([
                'perusahaan_id' => $validated['perusahaan_id'],
                'no_spk' => $validated['no_spk'],
                'nama_dinas' => $validated['nama_dinas'],
                'kabupaten' => $validated['kabupaten'],
                'npwp_dinas' => $validated['npwp_dinas'],
                'alamat_dinas' => $validated['alamat_dinas'],
                'nama_ppk' => $validated['nama_ppk'],
                'jabatan_ppk' => $validated['jabatan_ppk'],
                'tanggal_spk' => $validated['tanggal_spk'],
                'jumlah_termin' => count($validated['termins']),
            ]);

            foreach ($validated['termins'] as $index => $terminData) {
                $termin = $spk->termins()->create([
                    'no_termin' => $index + 1,
                    'tanggal_mulai_kirim' => $terminData['tanggal_mulai'] ?? null,
                    'tanggal_akhir_kirim' => $terminData['tanggal_akhir'],
                    'nilai_termin' => $terminData['nilai'],
                    'kena_ppn' => isset($terminData['kena_ppn']),
                    'status' => 'belum_kirim',
                ]);

                if (isset($terminData['items'])) {
                    foreach ($terminData['items'] as $itemData) {
                        $termin->itemTermins()->create([
                            'nama_barang' => $itemData['nama_barang'],
                            'jumlah' => $itemData['jumlah'],
                            'satuan' => $itemData['satuan'] ?? '-',
                            'merk' => $itemData['merk'] ?? '-',
                            'harga_satuan' => $itemData['harga_satuan'],
                        ]);
                    }
                }
            }
        });

        return redirect()->route('dashboard')->with('success', 'SPK dan Termin berhasil ditambahkan.');
    }

    public function show($id)
    {
        $realId = \App\Helpers\HashId::decode($id);
        if (!$realId) {
            abort(404);
        }

        $spk = Spk::with(['perusahaan', 'termins.itemTermins'])->findOrFail($realId);
        return view('spk.show', compact('spk'));
    }

    public function parsePdf(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:10240',
        ]);

        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($request->file('file')->path());
            $text = str_replace("\0", "", $pdf->getText());

            if (empty(trim($text))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen PDF merupakan hasil scan (gambar) tanpa lapisan teks ter-OCR. Silakan isi data SPK secara manual.'
                ], 422);
            }
            
            $data = [
                'no_spk' => '',
                'nama_dinas' => '',
                'kabupaten' => '',
                'npwp_dinas' => '',
                'alamat_dinas' => '',
                'nama_ppk' => '',
                'jabatan_ppk' => 'Pejabat Pembuat Komitmen',
                'tanggal_spk' => date('Y-m-d'),
            ];

            // 1. Nomor SPK & Tanggal
            if (preg_match('/No\.\s*Surat\s*Pesanan:\s*([A-Za-z0-9\-]+?)(?:Tanggal|$)/i', $text, $matches)) {
                $data['no_spk'] = trim($matches[1]);
                
                // Cek apakah nomor SPK sudah ada di database untuk mencegah data ganda
                if (Spk::where('no_spk', $data['no_spk'])->exists()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Surat Pesanan dengan Nomor "' . $data['no_spk'] . '" sudah terdaftar di sistem. Anda tidak perlu memasukkannya kembali.'
                    ], 422);
                }
            }
            if (preg_match('/Tanggal\s*Surat\s*Pesanan:\s*(\d{1,2})\s*([A-Za-z]+)\s*(\d{4})/i', $text, $matches)) {
                $months = ['Jan' => '01', 'Feb' => '02', 'Mar' => '03', 'Apr' => '04', 'Mei' => '05', 'Jun' => '06', 'Jul' => '07', 'Agu' => '08', 'Sep' => '09', 'Okt' => '10', 'Nov' => '11', 'Des' => '12'];
                $month = $months[ucfirst(substr($matches[2], 0, 3))] ?? '01';
                $data['tanggal_spk'] = $matches[3] . '-' . $month . '-' . str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            }

            // 2. Dinas & Kabupaten
            if (preg_match('/Pemesan\s*([^\n\r]+?)(Kab\.|Kota)\s*([A-Za-z\s]+?)Nama/i', $text, $matches)) {
                $data['nama_dinas'] = trim($matches[1]);
                $data['kabupaten'] = trim($matches[3]);
            }

            // 3. Nama PPK
            if (preg_match('/Nama\s*Penanggung\s*Jawab:\s*([A-Za-z\s\.,]+?)Jabatan/i', $text, $matches)) {
                $data['nama_ppk'] = trim($matches[1]);
            }

            // 4. NPWP Dinas & Alamat
            if (preg_match('/NPWP\s*Pemesan:\s*([\d\.-]+)/i', $text, $matches)) {
                $data['npwp_dinas'] = trim($matches[1]);
            }
            if (preg_match('/Alamat\s*Pemesan:\s*(.*?)Informasi\s*Pembayaran/i', $text, $matches)) {
                $data['alamat_dinas'] = trim($matches[1]);
            }

            // 4.5. Penyedia (Perusahaan)
            if (preg_match('/Penyedia\s*([A-Za-z0-9\s]+?)(?:UMKK|Nama\s*Penanggung)/i', $text, $matches)) {
                $namaPenyedia = trim($matches[1]);
                $perusahaan = \App\Models\Perusahaan::where('nama_perusahaan', 'LIKE', '%' . $namaPenyedia . '%')->first();
                if ($perusahaan) {
                    $data['perusahaan_id'] = $perusahaan->id;
                }
            }

            // 5. Permintaan Tiba / Rentang Tanggal Kirim
            $tglMulai = $data['tanggal_spk'];
            $tglAkhir = date('Y-m-d', strtotime('+30 days', strtotime($tglMulai)));
            if (preg_match('/Permintaan\s*Tiba:\s*(\d{1,2}\s*[A-Za-z]+\s*\d{4})\s*-\s*(\d{1,2}\s*[A-Za-z]+\s*\d{4})/i', $text, $matches)) {
                $parseIndoDate = function($dateStr) {
                    $months = ['Jan' => '01', 'Feb' => '02', 'Mar' => '03', 'Apr' => '04', 'Mei' => '05', 'Jun' => '06', 'Jul' => '07', 'Agu' => '08', 'Sep' => '09', 'Okt' => '10', 'Nov' => '11', 'Des' => '12'];
                    if (preg_match('/(\d{1,2})\s*([A-Za-z]+)\s*(\d{4})/', $dateStr, $m)) {
                        $month = $months[ucfirst(substr($m[2], 0, 3))] ?? '01';
                        return $m[3] . '-' . $month . '-' . str_pad($m[1], 2, '0', STR_PAD_LEFT);
                    }
                    return date('Y-m-d');
                };
                $tglMulai = $parseIndoDate($matches[1]);
                $tglAkhir = $parseIndoDate($matches[2]);
            }

            // 6. Ekstraksi Items & Harga (Ringkasan Pesanan Inaproc V6)
            $items = [];

            if (preg_match('/Ringkasan\s*Pesanan(.*?)(?:Ringkasan\s*Pembayaran|Detail\s*Informasi|$)/is', $text, $summaryBlock)) {
                $block = $summaryBlock[1];
                $pattern = '/Barang(?:PDN|TKDN)?\s*(.*?)\s*(\d{1,3}(?:\.\d{3})*(?:,\d{1,2})?)\s*(liter|dus|unit|pcs|paket|buah|set|box|rem|rim|meter|kg|gram|gr|pasang|lembar|roll|botol|kardus|stik|satuan_ukur).*?Rp\s*([\d\.]+)/is';
                if (preg_match_all($pattern, $block, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $match) {
                        $rawName = trim($match[1]);

                        // Fix ligatures
                        $rawName = preg_replace('/\bRe\s*Gll\b|\bReGll\b/i', 'Refill', $rawName);
                        $rawName = preg_replace('/\bGll\b/i', 'Fill', $rawName);

                        // Only fallback if summary name is garbled font stream (e.g. meNHH)
                        if (preg_match('/^(meNHH)/i', $rawName)) {
                            if (preg_match('/No.{0,50}Jumlah\d+([A-Za-z0-9\s\-\/\(\)]+?)(?:Isi|Catatan|Varian|Layanan|\d{1,5}[\.,]\d{1,2}|$)/i', $text, $dMatch)) {
                                $cleanName = trim($dMatch[1]);
                                $cleanName = preg_replace('/\bRe\s*Gll\b|\bReGll\b/i', 'Refill', $cleanName);
                                $cleanName = preg_replace('/\bGll\b/i', 'Fill', $cleanName);
                                if (strlen($cleanName) >= 3) {
                                    $rawName = $cleanName;
                                }
                            }
                        }

                        $jumlahStr = str_replace('.', '', $match[2]);
                        $jumlahStr = str_replace(',', '.', $jumlahStr);
                        $jumlah = (float) $jumlahStr;

                        $satuan = trim($match[3]);
                        if (strtolower($satuan) === 'satuan_ukur') $satuan = 'Tabung';
                        $hargaSatuan = (float) str_replace('.', '', $match[4]);

                        $items[] = [
                            'nama_barang' => $rawName,
                            'jumlah' => $jumlah > 0 ? $jumlah : 1,
                            'satuan' => ucfirst($satuan) ?: 'Unit',
                            'merk' => '-',
                            'harga_satuan' => $hargaSatuan
                        ];
                    }
                }
            }

            // Fallback strategy: Extract from Detail Table if Summary Block was not found
            if (empty($items)) {
                if (preg_match_all('/No.{0,50}Jumlah(\d+.*?)(?=Harga Produk|Pembayaran|Surat Pesanan|$)/is', $text, $tableBlocks)) {
                    foreach ($tableBlocks[1] as $block) {
                        if (preg_match_all('/\d+([A-Za-z0-9\s\-\/\(\)]+?)(?:Isi|Catatan|Varian|Layanan|\d{1,5}[\.,]\d{1,2}|$)/i', $block, $rowMatches)) {
                            foreach ($rowMatches[1] as $rawName) {
                                $name = trim($rawName);
                                $name = preg_replace('/\bRe\s*Gll\b|\bReGll\b/i', 'Refill', $name);
                                $name = preg_replace('/\bGll\b/i', 'Fill', $name);
                                $name = trim(preg_replace('/^[\d\s\.\,\-]+/', '', $name));

                                if (strlen($name) >= 3 && !preg_match('/^(catatan|varian|jumlah|kapasitas|golongan|harga)/i', $name)) {
                                    $alreadyAdded = false;
                                    foreach ($items as $it) {
                                        if (strtolower($it['nama_barang']) === strtolower($name)) {
                                            $alreadyAdded = true;
                                            break;
                                        }
                                    }
                                    if (!$alreadyAdded) {
                                        $items[] = [
                                            'nama_barang' => $name,
                                            'jumlah' => 1,
                                            'satuan' => 'Unit',
                                            'merk' => '-',
                                            'harga_satuan' => 0,
                                        ];
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // Ultimate Fallback
            if (empty($items)) {
                $items[] = [
                    'nama_barang' => 'Barang Pesanan',
                    'jumlah' => 1,
                    'satuan' => 'Unit',
                    'merk' => '-',
                    'harga_satuan' => 0
                ];
            }
            $data['items'] = $items;

            // 7. Ekstraksi Termins & Alokasi Per Pengiriman (Mendukung 1 s/d N Termin)
            $blocks = preg_split('/Pembayaran\s*Termin\s*(\d+)/i', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
            $termins_map = [];

            if (count($blocks) > 1) {
                for ($i = 1; $i < count($blocks); $i += 2) {
                    $tNo = (int) $blocks[$i];
                    $tContent = $blocks[$i + 1] ?? '';

                    $nilai = 0;
                    if (preg_match('/Estimasi\s*Total\s*Pembayaran\s*Termin\s*' . $tNo . '\s*Rp\s*([\d\.]+)/i', $tContent, $nm)) {
                        $nilai = (float) str_replace('.', '', $nm[1]);
                    } elseif (preg_match('/Rp\s*([\d\.]+)/i', $tContent, $nm)) {
                        $nilai = (float) str_replace('.', '', $nm[1]);
                    }

                    $tQty = 1;
                    if (preg_match('/Harga\s*Produk\s*\(\s*(\d{1,3}(?:\.\d{3})*(?:,\d{1,2})?)\s*\)\s*Rp/i', $tContent, $qm)) {
                        $qStr = str_replace('.', '', $qm[1]);
                        $qStr = str_replace(',', '.', $qStr);
                        $tQty = (float) $qStr;
                    }

                    $terminItems = array_map(function($it) use ($tQty) {
                        $newItem = $it;
                        if ($tQty > 0) {
                            $newItem['jumlah'] = $tQty;
                        }
                        return $newItem;
                    }, $items);

                    $existingQty = $termins_map[$tNo]['items'][0]['jumlah'] ?? 0;
                    $existingNilai = $termins_map[$tNo]['nilai_termin'] ?? 0;

                    if (!isset($termins_map[$tNo]) || $nilai > $existingNilai || ($nilai == $existingNilai && $tQty > $existingQty)) {
                        $termins_map[$tNo] = [
                            'no_termin' => $tNo,
                            'tanggal_mulai' => $tglMulai,
                            'tanggal_akhir' => $tglAkhir,
                            'nilai_termin' => $nilai,
                            'kena_ppn' => true,
                            'items' => $terminItems
                        ];
                    }
                }
            } else {
                if (preg_match_all('/(?:Estimasi\s*Total\s*Pembayaran\s*Termin|Pembayaran\s*Termin)\s*(\d+).*?Rp\s*([\d\.]+)/i', $text, $tMatches, PREG_SET_ORDER)) {
                    foreach ($tMatches as $match) {
                        $tNo = (int) $match[1];
                        $tNilai = (float) str_replace('.', '', $match[2]);
                        if (!isset($termins_map[$tNo]) || $tNilai > $termins_map[$tNo]['nilai_termin']) {
                            $termins_map[$tNo] = [
                                'no_termin' => $tNo,
                                'tanggal_mulai' => $tglMulai,
                                'tanggal_akhir' => $tglAkhir,
                                'nilai_termin' => $tNilai,
                                'kena_ppn' => true,
                                'items' => $items
                            ];
                        }
                    }
                }
            }
            ksort($termins_map);
            $termins = array_values($termins_map);

            if (empty($termins)) {
                $termins[] = [
                    'no_termin' => 1,
                    'tanggal_mulai' => $tglMulai,
                    'tanggal_akhir' => $tglAkhir,
                    'nilai_termin' => 0,
                    'kena_ppn' => true,
                    'items' => $items
                ];
            }
            $data['termins'] = $termins;

            return response()->json([
                'success' => true,
                'data' => $data,
                'raw_text' => substr($text, 0, 500)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membaca PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}
