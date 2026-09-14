<?php

namespace App\Controllers;

use App\Models\LaporanModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanadminController extends BaseController
{
    protected $laporanModel;

    public function __construct()
    {
        $this->laporanModel = new LaporanModel();
    }

    public function index()
    {
        [$tglMulai, $tglAkhir] = $this->resolveRentang();

        $data      = $this->laporanModel->rekapByRentang($tglMulai, $tglAkhir);
        $akumulasi = $this->laporanModel->akumulasiByRentang($tglMulai, $tglAkhir);

        return view('admin/laporan', [
            'data'      => $data,
            'akumulasi' => $akumulasi,
            'tglMulai'  => $tglMulai,
            'tglAkhir'  => $tglAkhir,
        ]);
    }

    public function export()
    {
        [$tglMulai, $tglAkhir] = $this->resolveRentang();

        $data      = $this->laporanModel->rekapByRentang($tglMulai, $tglAkhir);
        $akumulasi = $this->laporanModel->akumulasiByRentang($tglMulai, $tglAkhir);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Penyewaan');

        // ── JUDUL & PERIODE ──
        $sheet->setCellValue('A1', 'LAPORAN REKAPITULASI PENYEWAAN - TENDATIM OUTDOOR');
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Periode: ' . date('d M Y', strtotime($tglMulai)) . ' - ' . date('d M Y', strtotime($tglAkhir)));
        $sheet->mergeCells('A2:J2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2')->getFont()->setItalic(true);

        $sheet->setCellValue('A4', 'Total Pendapatan');
        $sheet->setCellValue('B4', 'Rp ' . number_format($akumulasi['total_pendapatan'], 0, ',', '.'));
        $sheet->setCellValue('A5', 'Total Transaksi Selesai');
        $sheet->setCellValue('B5', $akumulasi['total_transaksi']);
        $sheet->setCellValue('A6', 'Total Unit Dikembalikan');
        $sheet->setCellValue('B6', $akumulasi['total_unit'] . ' unit');
        $sheet->getStyle('A4:A6')->getFont()->setBold(true);

        $headerRow = 8;
        $headers = [
            'A' => 'ID Sewa',
            'B' => 'Penyewa',
            'C' => 'Perlengkapan',
            'D' => 'Jumlah',
            'E' => 'Tgl Sewa',
            'F' => 'Tgl Rencana Kembali',
            'G' => 'Tgl Kembali Aktual',
            'H' => 'Durasi (Hari)',
            'I' => 'Total Biaya',
            'J' => 'Status Bayar',
        ];

        foreach ($headers as $col => $label) {
            $sheet->setCellValue($col . $headerRow, $label);
        }

        $headerRange = 'A' . $headerRow . ':J' . $headerRow;
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('53CBF3');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row = $headerRow + 1;
        foreach ($data as $l) {
            $sheet->setCellValue('A' . $row, $l['id_sewa']);
            $sheet->setCellValue('B' . $row, $l['username']);
            $sheet->setCellValue('C' . $row, $l['nama_perlengkapan']);
            $sheet->setCellValue('D' . $row, $l['jumlah'] . ' unit');
            $sheet->setCellValue('E' . $row, $l['tgl_sewa']);
            $sheet->setCellValue('F' . $row, $l['tgl_kembali_rencana']);
            $sheet->setCellValue('G' . $row, $l['tgl_kembali_aktual']);
            $sheet->setCellValue('H' . $row, $l['durasi_hari']);
            $sheet->setCellValue('I' . $row, (float) $l['total_biaya']);
            $sheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->setCellValue('J' . $row, $l['status_bayar'] ?? '-');
            $row++;
        }

        if (empty($data)) {
            $sheet->setCellValue('A' . $row, 'Tidak ada data pada rentang tanggal ini.');
            $sheet->mergeCells('A' . $row . ':J' . $row);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $lastRow = max($row - 1, $headerRow);
        $sheet->getStyle('A' . $headerRow . ':J' . $lastRow)
            ->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $namaFile = 'Laporan_Penyewaan_' . $tglMulai . '_sd_' . $tglAkhir . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $namaFile . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    private function resolveRentang(): array
    {
        $tglMulai = $this->request->getGet('tanggal_mulai');
        $tglAkhir = $this->request->getGet('tanggal_akhir');

        if (! $tglMulai || ! $tglAkhir) {
            $tglMulai = date('Y-m-01');
            $tglAkhir = date('Y-m-d');
        }

        if (strtotime($tglMulai) > strtotime($tglAkhir)) {
            [$tglMulai, $tglAkhir] = [$tglAkhir, $tglMulai];
        }

        return [$tglMulai, $tglAkhir];
    }
}
