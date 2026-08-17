<?php

namespace App\Http\Controllers;

use App\Models\Termin;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    private function buildQuery(Request $request)
    {
        $query = Termin::with(['spk.perusahaan', 'perhitunganPajak', 'rekonsiliasiPembayaran']);

        // Periode (input type="month" sends YYYY-MM)
        if ($request->filled('periode')) {
            $parts = explode('-', $request->periode);
            if (count($parts) === 2) {
                $query->whereYear('updated_at', $parts[0])
                      ->whereMonth('updated_at', $parts[1]);
            }
        }

        // Kabupaten
        if ($request->filled('kabupaten')) {
            $query->whereHas('spk', function($q) use ($request) {
                $q->where('kabupaten', 'like', '%' . $request->kabupaten . '%');
            });
        }

        // Status Filter
        $status = $request->input('status', 'semua');
        if ($status === 'sudah_terbayar') {
            $query->whereIn('status', ['lunas', 'lunas_selisih']);
        } elseif ($status === 'belum_terbayar') {
            $query->whereNotIn('status', ['lunas', 'lunas_selisih']);
        }

        return $query;
    }

    private function mapTerminData($termin)
    {
        $gross = (float) ($termin->nilai_termin ?? 0);
        
        if ($termin->kena_ppn) {
            $dpp = $gross / 1.11;
            $ppn = $dpp * 0.11;
            $pph = $dpp * 0.015;
        } else {
            $dpp = $gross;
            $ppn = 0;
            $pph = $dpp * 0.015;
        }

        $isLunas = in_array($termin->status, ['lunas', 'lunas_selisih']);
        
        $nilaiDiterima = 0;
        if ($termin->rekonsiliasiPembayaran) {
            $nilaiDiterima = (float) $termin->rekonsiliasiPembayaran->nilai_diterima;
        } elseif ($isLunas) {
            $nilaiDiterima = $gross - $ppn - $pph;
        }

        $termin->computed_dpp = $dpp;
        $termin->computed_ppn = $ppn;
        $termin->computed_pph = $pph;
        $termin->computed_diterima = $nilaiDiterima;
        $termin->is_lunas = $isLunas;

        return $termin;
    }

    public function index(Request $request)
    {
        $termins = $this->buildQuery($request)->get()->map(function($termin) {
            return $this->mapTerminData($termin);
        });

        return view('laporan.index', compact('termins'));
    }

    public function export(Request $request)
    {
        $termins = $this->buildQuery($request)->get()->map(function($termin) {
            return $this->mapTerminData($termin);
        });

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'LAPORAN REALISASI SPK');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->mergeCells('A1:K1');
        
        $headers = ['No', 'No. SPK', 'Perusahaan', 'Dinas', 'Kabupaten', 'Termin', 'Nilai Termin (Rp)', 'DPP (Rp)', 'PPN (Rp)', 'PPh (Rp)', 'Nilai Diterima (Rp)', 'Status'];
        foreach (array_values($headers) as $index => $header) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($column . '3', $header);
            $sheet->getStyle($column . '3')->getFont()->setBold(true);
        }

        $row = 4;
        foreach ($termins as $index => $termin) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $termin->spk->no_spk ?? '-');
            $sheet->setCellValue('C' . $row, $termin->spk->perusahaan->nama_perusahaan ?? '-');
            $sheet->setCellValue('D' . $row, $termin->spk->nama_dinas ?? '-');
            $sheet->setCellValue('E' . $row, $termin->spk->kabupaten ?? '-');
            $sheet->setCellValue('F' . $row, 'Termin ' . $termin->no_termin);
            $sheet->setCellValue('G' . $row, $termin->nilai_termin);
            $sheet->setCellValue('H' . $row, round($termin->computed_dpp));
            $sheet->setCellValue('I' . $row, round($termin->computed_ppn));
            $sheet->setCellValue('J' . $row, round($termin->computed_pph));
            $sheet->setCellValue('K' . $row, $termin->computed_diterima);
            $sheet->setCellValue('L' . $row, $termin->is_lunas ? 'Sudah Terbayar' : 'Belum Terbayar');

            foreach (['G', 'H', 'I', 'J', 'K'] as $col) {
                $sheet->getStyle($col . $row)->getNumberFormat()->setFormatCode('#,##0');
            }
            $row++;
        }

        foreach (range('A', 'L') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'Laporan_Realisasi_' . date('Ymd_His') . '.xlsx';
        
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
