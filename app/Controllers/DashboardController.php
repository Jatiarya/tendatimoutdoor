<?php

namespace App\Controllers;

use App\Models\RentalModel;
use App\Models\PerlengkapanModel;
use App\Models\PembayaranModel;
use App\Models\NotifikasiModel;

class DashboardController extends BaseController
{
    protected $rentalModel;
    protected $perlengkapanModel;
    protected $pembayaranModel;
    protected $notifikasiModel;

    public function __construct()
    {
        $this->rentalModel       = new RentalModel();
        $this->perlengkapanModel = new PerlengkapanModel();
        $this->pembayaranModel   = new PembayaranModel();
        $this->notifikasiModel   = new NotifikasiModel();
    }

    public function dashboard()
    {
        $db = \Config\Database::connect();

        // ── DATA MENTAH ──
        $penyewaan    = $this->rentalModel->findAll();
        $perlengkapan = $this->perlengkapanModel->findAll();
        $user         = $db->table('user')->get()->getResultArray();

        $bulanIni  = (int) date('n');
        $tahunIni  = (int) date('Y');
        $bulanLalu = $bulanIni === 1 ? 12 : $bulanIni - 1;
        $tahunLalu = $bulanIni === 1 ? $tahunIni - 1 : $tahunIni;

        // ── TOTAL PENYEWAAN + PERBANDINGAN BULAN LALU ──
        $penyewaanBulanIni  = $this->rentalModel->totalByBulan($bulanIni, $tahunIni);
        $penyewaanBulanLalu = $this->rentalModel->totalByBulan($bulanLalu, $tahunLalu);
        $persenPenyewaan    = $this->hitungPersentase($penyewaanBulanIni, $penyewaanBulanLalu);

        // ── TOTAL PENGGUNA + PERBANDINGAN BULAN LALU (user baru daftar) ──
        $userBulanIni = $db->table('user')
            ->where('MONTH(created_at)', $bulanIni)
            ->where('YEAR(created_at)', $tahunIni)
            ->countAllResults();
        $userBulanLalu = $db->table('user')
            ->where('MONTH(created_at)', $bulanLalu)
            ->where('YEAR(created_at)', $tahunLalu)
            ->countAllResults();
        $persenUser = $this->hitungPersentase($userBulanIni, $userBulanLalu);

        // ── PERLENGKAPAN AKTIF (stok > 0) ──
        $perlengkapanAktif = $this->perlengkapanModel->countActive();

        // ── TOTAL PENDAPATAN + PERBANDINGAN BULAN LALU ──
        $totalPendapatan     = $this->pembayaranModel->totalPendapatan();
        $pendapatanBulanIni  = $this->pembayaranModel->pendapatanByBulan($bulanIni, $tahunIni);
        $pendapatanBulanLalu = $this->pembayaranModel->pendapatanByBulan($bulanLalu, $tahunLalu);
        $persenPendapatan    = $this->hitungPersentase($pendapatanBulanIni, $pendapatanBulanLalu);

        // ── DATA CHART (7 hari, Senin - Minggu minggu berjalan) ──
        // Dikirim dalam nilai RUPIAH ASLI (tidak dibagi/disingkat di PHP).
        // Formatting "rb"/"jt" dinamis dilakukan di JS berdasarkan skala data aktual.
        $chartRentalRaw  = $this->rentalModel->statistikMingguIni();
        $chartRevenueRaw = $this->pembayaranModel->pendapatanMingguIni();

        // ── NOTIFIKASI PESANAN BARU (widget di body dashboard) ──
        $notifikasiTerbaru = $this->notifikasiModel->belumDibaca(6);

        return view('admin/dashboard', [
            'penyewaan'          => $penyewaan,
            'user'               => $user,
            'perlengkapan'       => $perlengkapan,
            'perlengkapanAktif'  => $perlengkapanAktif,
            'totalPendapatan'    => $totalPendapatan,
            'persenPenyewaan'    => $persenPenyewaan,
            'persenUser'         => $persenUser,
            'persenPendapatan'   => $persenPendapatan,
            'chartRevenue'       => $chartRevenueRaw,
            'chartRental'        => $chartRentalRaw,
            'notifikasiTerbaru'  => $notifikasiTerbaru,
        ]);
    }

    /**
     * GET /dashboard/chart-data?period=week|month|year&metric=revenue|rental
     * Dipanggil AJAX dari dropdown filter chart di dashboard, supaya ganti
     * periode tidak perlu reload seluruh halaman.
     */
    public function chartData()
    {
        $period = $this->request->getGet('period') ?? 'week';
        $metric = $this->request->getGet('metric') ?? 'revenue';

        if (! in_array($period, ['week', 'month', 'year'], true)) {
            $period = 'week';
        }
        if (! in_array($metric, ['revenue', 'rental'], true)) {
            $metric = 'revenue';
        }

        [$labels, $data] = $metric === 'revenue'
            ? $this->dataPendapatan($period)
            : $this->dataPenyewaan($period);

        return $this->response->setJSON([
            'labels' => $labels,
            'data'   => $data,
        ]);
    }

    /**
     * @return array{0: list<string>, 1: list<float>}
     */
    private function dataPendapatan(string $period): array
    {
        return match ($period) {
            'month' => [$this->labelHarianBulanIni(), $this->pembayaranModel->pendapatanBulanIniHarian()],
            'year'  => [$this->labelBulanan(), $this->pembayaranModel->pendapatanTahunIniBulanan()],
            default => [$this->labelMingguan(), $this->pembayaranModel->pendapatanMingguIni()],
        };
    }

    /**
     * @return array{0: list<string>, 1: list<int>}
     */
    private function dataPenyewaan(string $period): array
    {
        return match ($period) {
            'month' => [$this->labelHarianBulanIni(), $this->rentalModel->statistikBulanIni()],
            'year'  => [$this->labelBulanan(), $this->rentalModel->statistikTahunIni()],
            default => [$this->labelMingguan(), $this->rentalModel->statistikMingguIni()],
        };
    }

    private function labelMingguan(): array
    {
        return ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
    }

    private function labelBulanan(): array
    {
        return ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    }

    private function labelHarianBulanIni(): array
    {
        $totalHari = (int) date('t');

        return array_map(static fn($h) => (string) $h, range(1, $totalHari));
    }

    /**
     * Hitung persentase perubahan dari nilai lama ke nilai baru.
     * Aman dari pembagian oleh nol.
     */
    private function hitungPersentase(float $nilaiSekarang, float $nilaiLalu): array
    {
        if ($nilaiLalu == 0) {
            $persen = $nilaiSekarang > 0 ? 100 : 0;
        } else {
            $persen = (($nilaiSekarang - $nilaiLalu) / $nilaiLalu) * 100;
        }

        return [
            'nilai' => round($persen, 1),
            'naik'  => $persen >= 0,
        ];
    }
}
