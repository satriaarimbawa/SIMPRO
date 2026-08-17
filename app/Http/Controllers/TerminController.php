<?php

namespace App\Http\Controllers;

use App\Models\Termin;
use App\Models\SuratJalan;
use App\Models\Pengaturan;
use Illuminate\Http\Request;

class TerminController extends Controller
{
    /**
     * Memperbarui status termin (misal: belum_kirim -> terkirim -> lunas).
     * Dipanggil via form dropdown di halaman detail SPK / Arsip.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:belum_kirim,proses_kirim,terkirim,tagihan_dibuat,menunggu_konfirmasi,lunas,lunas_selisih'
        ]);

        $termin = Termin::findOrFail($id);
        $termin->update([
            'status' => $request->status,
            'bukti_potong_diterima' => $request->has('bukti_potong_diterima')
        ]);

        return back()->with('success', 'Status termin berhasil diperbarui.');
    }

    /**
     * Menampilkan halaman kanvas preview & form input Surat Jalan.
     * Template yang ditampilkan menyesuaikan perusahaan (WTM, WKB, WAM).
     */
    public function suratJalan($id)
    {
        $termin = Termin::with('spk.perusahaan', 'suratJalan')->findOrFail($id);
        return view('termin.surat-jalan', compact('termin'));
    }

    /**
     * Menyimpan data Surat Jalan ke database dan mengunduh file Excel.
     *
     * Menggunakan updateOrCreate agar data bisa diupdate jika sudah pernah disimpan.
     * Template Excel yang di-generate menyesuaikan perusahaan (WTM, WKB, WAM).
     * Untuk WTM dan WAM, ditambahkan 6 baris kosong di atas untuk kop surat bawaan kertas.
     */
    public function storeSuratJalan(Request $request, $id)
    {
        $request->validate([
            'no_surat_jalan' => 'required|string',
            'tanggal_kirim' => 'required|date',
            'pengemudi' => 'nullable|string',
            'no_polisi' => 'nullable|string',
            'penerima' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        $termin = Termin::with('spk.perusahaan', 'itemTermins')->findOrFail($id);
        
        SuratJalan::updateOrCreate(
            ['termin_id' => $termin->id],
            [
                'no_surat_jalan' => $request->no_surat_jalan,
                'tanggal_kirim' => $request->tanggal_kirim,
                'pengemudi' => $request->pengemudi,
                'no_polisi' => $request->no_polisi,
                'penerima' => $request->penerima,
                'keterangan' => $request->keterangan,
            ]
        );

        if ($termin->status == 'belum_kirim') {
            $termin->update(['status' => 'terkirim']);
        }

        // Generate Excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $perusahaan = $termin->spk->perusahaan;
        $dinas = $termin->spk->nama_dinas;
        $namaPerusahaan = strtoupper(trim($perusahaan->nama_perusahaan));

        // Format tanggal (contoh: 3 Juni 2026)
        $tglKirim = date('j F Y', strtotime($request->tanggal_kirim));
        // Array bulan Indonesia
        $bulanIndo = [
            'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
            'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
            'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
            'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
        ];
        $tglKirim = str_replace(array_keys($bulanIndo), array_values($bulanIndo), $tglKirim);

        if ($namaPerusahaan == 'WTM') {
            // ================= WTM TEMPLATE =================
            $sheet->setCellValue('A1', 'SURAT JALAN');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->setCellValue('A2', 'No. ' . $request->no_surat_jalan);
            $sheet->setCellValue('D1', 'Denpasar, ' . $tglKirim);
            $sheet->getStyle('D1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            $sheet->setCellValue('A4', 'Kepada :');
            $sheet->setCellValue('A5', $dinas);
            $sheet->setCellValue('A6', 'di -');
            $sheet->setCellValue('B6', 'Mangupura');

            $sheet->setCellValue('A8', 'Dengan hormat,');
            $sheet->setCellValue('A9', 'Mohon dapat diterima barang - barang sesuai dengan Surat Pesanan Nomor : ' . $termin->spk->no_spk . ', tanggal ' . date('d/m/Y', strtotime($termin->spk->tanggal_spk ?? now())) . ' sebagai berikut :');
            $sheet->mergeCells('A9:D9');

            $sheet->setCellValue('A11', 'No');
            $sheet->setCellValue('B11', 'Nama Barang');
            $sheet->setCellValue('C11', 'Volume');
            $sheet->setCellValue('D11', 'Keterangan');
            $sheet->getStyle('A11:D11')->getFont()->setBold(true);
            $sheet->getStyle('A11:D11')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $row = 12;
            if ($termin->itemTermins && $termin->itemTermins->count() > 0) {
                foreach ($termin->itemTermins as $index => $item) {
                    $sheet->setCellValue('A' . $row, $index + 1);
                    $sheet->setCellValue('B' . $row, $item->nama_barang);
                    $sheet->setCellValue('C' . $row, $item->jumlah . ' ' . $item->satuan);
                    $sheet->setCellValue('D' . $row, $item->catatan);
                    $sheet->getStyle("A{$row}:D{$row}")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $row++;
                }
            } else {
                $sheet->setCellValue('A' . $row, '1');
                $sheet->setCellValue('B' . $row, '-');
                $sheet->getStyle("A{$row}:D{$row}")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $row++;
            }

            $row += 3;
            $sheet->setCellValue('A' . $row, 'Yang Menerima,');
            $sheet->setCellValue('D' . $row, 'Yang Menyerahkan');
            $sheet->setCellValue('D' . ($row+1), 'CV. WAHANA TATA MANDIRI');
            
            $row += 4;
            $sheet->setCellValue('A' . $row, '(.........................)');
            $sheet->setCellValue('D' . $row, 'I Made Suastika, SE');
            $sheet->getStyle('D' . $row)->getFont()->setUnderline(true);
            $sheet->setCellValue('D' . ($row+1), 'Direktur');

            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(40);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(25);

            // Beri jarak 6 baris kosong di atas untuk Kop Surat bawaan kertas
            $sheet->insertNewRowBefore(1, 6);

        } elseif ($namaPerusahaan == 'WKB') {
            // ================= WKB TEMPLATE =================
            $sheet->setCellValue('A1', 'CV. WIJAYA KARYA BUANA');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(18);
            $sheet->mergeCells('A1:E1');
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            $sheet->setCellValue('A2', 'Jln. Gunung Andakasa Perum Cempaka Indah Blok. A/2 Denpasar');
            $sheet->mergeCells('A2:E2');
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('A3', 'Telp. : (0361) 429509');
            $sheet->mergeCells('A3:E3');
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Garis bawah tebal (simulasi)
            $sheet->getStyle('A3:E3')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);

            $sheet->setCellValue('A5', 'SURAT JALAN');
            $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(12);
            $sheet->setCellValue('A6', 'No. ' . $request->no_surat_jalan);

            $sheet->setCellValue('D5', 'KEPADA YTH:');
            $sheet->setCellValue('D6', $dinas);
            $sheet->setCellValue('D7', 'di -');
            $sheet->setCellValue('D8', 'Mangupura');

            $sheet->setCellValue('A10', 'No');
            $sheet->setCellValue('B10', 'Nama Barang');
            $sheet->setCellValue('C10', 'Jumlah');
            $sheet->setCellValue('D10', 'Satuan');
            $sheet->setCellValue('E10', 'Merk');
            $sheet->getStyle('A10:E10')->getFont()->setBold(true);
            $sheet->getStyle('A10:E10')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $row = 11;
            if ($termin->itemTermins && $termin->itemTermins->count() > 0) {
                foreach ($termin->itemTermins as $index => $item) {
                    $sheet->setCellValue('A' . $row, $index + 1);
                    $sheet->setCellValue('B' . $row, $item->nama_barang);
                    $sheet->setCellValue('C' . $row, $item->jumlah);
                    $sheet->setCellValue('D' . $row, $item->satuan);
                    $sheet->setCellValue('E' . $row, $item->merk);
                    $sheet->getStyle("A{$row}:E{$row}")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $row++;
                }
            } else {
                $sheet->setCellValue('A' . $row, '1');
                $sheet->setCellValue('B' . $row, '-');
                $sheet->getStyle("A{$row}:E{$row}")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $row++;
            }

            $row += 3;
            $sheet->setCellValue('B' . $row, 'Yang Menerima,');
            $sheet->setCellValue('D' . $row, 'Yang Menyerahkan');
            
            $sheet->setCellValue('B' . ($row+1), $dinas);
            $sheet->setCellValue('D' . ($row+1), 'CV. WIJAYA KARYA BUANA');
            
            $row += 5;
            $sheet->setCellValue('B' . $row, '(........................................)');
            $sheet->setCellValue('D' . $row, '(I Wayan Kastawa. SE)');
            $sheet->getStyle('D' . $row)->getFont()->setUnderline(true);
            $sheet->setCellValue('D' . ($row+1), 'Direktur');

            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(40);
            $sheet->getColumnDimension('C')->setWidth(10);
            $sheet->getColumnDimension('D')->setWidth(15);
            $sheet->getColumnDimension('E')->setWidth(20);

        } else {
            // ================= WAM TEMPLATE =================
            $sheet->setCellValue('A1', 'SURAT JALAN');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->setCellValue('A2', 'No. ' . $request->no_surat_jalan);
            
            $sheet->setCellValue('E1', 'Denpasar, ' . $tglKirim);
            $sheet->setCellValue('E2', 'KEPADA YTH:');
            $sheet->setCellValue('E3', $dinas);
            $sheet->setCellValue('E4', 'di -');
            $sheet->setCellValue('E5', 'Mangupura');

            $sheet->setCellValue('A7', 'Dengan hormat,');
            $sheet->setCellValue('A8', 'Mohon dapat diterima barang - barang sesuai dengan Surat Pesanan Nomor : ' . $termin->spk->no_spk . ', Tanggal ' . date('d/m/Y', strtotime($termin->spk->tanggal_spk ?? now())) . ' sebagai berikut :');
            $sheet->mergeCells('A8:E8');

            $sheet->setCellValue('A10', 'No');
            $sheet->setCellValue('B10', 'Nama Barang');
            $sheet->setCellValue('C10', 'Jumlah');
            $sheet->setCellValue('D10', 'Satuan');
            $sheet->setCellValue('E10', 'Merk');
            $sheet->getStyle('A10:E10')->getFont()->setBold(true);
            $sheet->getStyle('A10:E10')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $row = 11;
            if ($termin->itemTermins && $termin->itemTermins->count() > 0) {
                foreach ($termin->itemTermins as $index => $item) {
                    $sheet->setCellValue('A' . $row, $index + 1);
                    $sheet->setCellValue('B' . $row, $item->nama_barang);
                    $sheet->setCellValue('C' . $row, $item->jumlah);
                    $sheet->setCellValue('D' . $row, $item->satuan);
                    $sheet->setCellValue('E' . $row, $item->merk);
                    $sheet->getStyle("A{$row}:E{$row}")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $row++;
                }
            } else {
                $sheet->setCellValue('A' . $row, '1');
                $sheet->setCellValue('B' . $row, '-');
                $sheet->getStyle("A{$row}:E{$row}")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $row++;
            }

            $row += 3;
            $sheet->setCellValue('A' . $row, 'Yang Menerima,');
            $sheet->setCellValue('D' . $row, 'Yang Menyerahkan');
            
            $sheet->setCellValue('A' . ($row+1), $dinas);
            $sheet->setCellValue('D' . ($row+1), 'CV. WAHANA AGRO MANDIRI');
            
            $row += 5;
            $sheet->setCellValue('A' . $row, '(..........................................)');
            $sheet->setCellValue('D' . $row, '(Drh. I Made Alit Neker)');
            $sheet->getStyle('D' . $row)->getFont()->setUnderline(true);
            $sheet->setCellValue('D' . ($row+1), 'Direktur');

            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(40);
            $sheet->getColumnDimension('C')->setWidth(10);
            $sheet->getColumnDimension('D')->setWidth(15);
            $sheet->getColumnDimension('E')->setWidth(20);

            // Beri jarak 6 baris kosong di atas untuk Kop Surat bawaan kertas
            $sheet->insertNewRowBefore(1, 6);
        }

        $fileName = 'Surat_Jalan_' . str_replace('/', '_', $request->no_surat_jalan) . '.xlsx';

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function perincian($id)
    {
        $termin = Termin::with('spk.perusahaan', 'itemTermins')->findOrFail($id);
        
        $pengaturan = Pengaturan::first();
        $persenPpn = $pengaturan ? $pengaturan->tarif_ppn_persen : 11;
        $persenPph = $pengaturan ? $pengaturan->tarif_pph_persen : 1.5;
        
        $nilaiTermin = $termin->nilai_termin;
        
        if ($termin->kena_ppn) {
            $dpp = $nilaiTermin / (1 + ($persenPpn / 100));
            $nilaiPpn = $nilaiTermin - $dpp;
        } else {
            $dpp = $nilaiTermin;
            $nilaiPpn = 0;
        }

        $nilaiPph = $dpp * ($persenPph / 100);
        $totalDenganPajak = $nilaiTermin; 
        $totalBersih = $nilaiTermin - $nilaiPpn - $nilaiPph;

        return view('termin.perincian', compact('termin', 'persenPpn', 'persenPph', 'dpp', 'nilaiPpn', 'nilaiPph', 'totalDenganPajak', 'totalBersih'));
    }

    public function downloadPerincian($id)
    {
        $termin = Termin::with('spk.perusahaan', 'itemTermins')->findOrFail($id);
        
        $pengaturan = Pengaturan::first();
        $persenPpn = $pengaturan ? $pengaturan->tarif_ppn_persen : 11;
        $persenPph = $pengaturan ? $pengaturan->tarif_pph_persen : 1.5;
        
        $nilaiTermin = $termin->nilai_termin;
        
        if ($termin->kena_ppn) {
            $dpp = $nilaiTermin / (1 + ($persenPpn / 100));
            $nilaiPpn = $nilaiTermin - $dpp;
        } else {
            $dpp = $nilaiTermin;
            $nilaiPpn = 0;
        }
        
        $nilaiPph = $dpp * ($persenPph / 100);
        $total = $nilaiTermin;
        $totalBersih = $total - $nilaiPpn - $nilaiPph;

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $perusahaan = $termin->spk->perusahaan;

        $sheet->setCellValue('A1', 'PERINCIAN PEMBAYARAN / REKONSILIASI');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->mergeCells('A1:D1');

        $sheet->setCellValue('A3', 'No. SPK');
        $sheet->setCellValue('B3', ': ' . ($termin->spk->no_spk ?? '-'));
        $sheet->setCellValue('A4', 'Perusahaan');
        $sheet->setCellValue('B4', ': ' . ($perusahaan->nama_perusahaan ?? '-'));
        $sheet->setCellValue('A5', 'Dinas');
        $sheet->setCellValue('B5', ': ' . ($termin->spk->nama_dinas ?? '-'));
        $sheet->setCellValue('A6', 'Termin');
        $sheet->setCellValue('B6', ': ' . ($termin->nama_termin ?? $termin->no_termin));

        $sheet->setCellValue('A8', 'RINCIAN');
        $sheet->setCellValue('B8', 'NILAI (Rp)');
        $sheet->getStyle('A8:B8')->getFont()->setBold(true);

        $sheet->setCellValue('A9', 'Total Kwitansi');
        $sheet->setCellValue('B9', $total);
        $sheet->getStyle('A9:B9')->getFont()->setBold(true);
        
        $sheet->setCellValue('A10', 'Dasar Pengenaan Pajak (DPP)');
        $sheet->setCellValue('B10', $dpp);
        
        $sheet->setCellValue('A11', 'PPN (' . $persenPpn . '%)');
        $sheet->setCellValue('B11', $nilaiPpn);
        
        $sheet->setCellValue('A12', 'PPh (' . $persenPph . '%)');
        $sheet->setCellValue('B12', $nilaiPph);
        
        $sheet->setCellValue('A13', 'Pembayaran Bersih Diterima');
        $sheet->setCellValue('B13', $totalBersih);
        $sheet->getStyle('A13:B13')->getFont()->setBold(true);

        $sheet->getStyle('B9:B13')->getNumberFormat()->setFormatCode('#,##0.00');
        
        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(25);

        $fileName = 'Perincian_Termin_' . $termin->no_termin . '_' . date('Ymd_His') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function uploadLampiran(Request $request, $id)
    {
        $request->validate([
            'jenis_dokumen' => 'required|string',
            'file_lampiran' => 'required|file|max:10240', // max 10MB
        ]);

        $termin = Termin::with('spk')->findOrFail($id);

        if ($request->hasFile('file_lampiran')) {
            $file = $request->file('file_lampiran');
            
            // Sanitasi no_spk agar aman sebagai nama folder Windows
            $noSpkSanitasi = preg_replace('/[\/\\\\ ]/', '_', $termin->spk->no_spk);
            $noSpkSanitasi = preg_replace('/[:\*\?"<>\|]/', '', $noSpkSanitasi);

            // Struktur folder: lampiran/{no_spk}/Termin-{no_termin}/
            $folder = 'lampiran/' . $noSpkSanitasi . '/Termin-' . $termin->no_termin;

            // Simpan dengan nama file asli dari komputer pengguna
            $path = $file->storeAs($folder, $file->getClientOriginalName(), 'public');

            \App\Models\LampiranDokumen::create([
                'termin_id' => $termin->id,
                'jenis_dokumen' => $request->jenis_dokumen,
                'nama_file' => $file->getClientOriginalName(),
                'file' => $path,
                'tanggal_unggah' => now(),
            ]);
        }

        return back()->with('success', 'Dokumen lampiran berhasil diunggah.');
    }
}
