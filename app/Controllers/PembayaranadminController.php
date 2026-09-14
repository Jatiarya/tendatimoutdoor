<?php

namespace App\Controllers;

use App\Models\PembayaranModel;
use App\Models\RentalModel;

class PembayaranadminController extends BaseController
{
    protected $pembayaranModel;
    protected $rentalModel;

    public function __construct()
    {
        $this->pembayaranModel = new PembayaranModel();
        $this->rentalModel     = new RentalModel();
    }

    public function index()
    {
        $identifier = $this->request->getGet('identifier');

        if ($identifier) {
            $tglMulai = '2000-01-01';
            $tglAkhir = date('Y-m-d');
        } else {
            [$tglMulai, $tglAkhir] = $this->resolveRentang();
        }

        $data      = $this->pembayaranModel->allForAdminByRentang($tglMulai, $tglAkhir);
        $akumulasi = $this->pembayaranModel->akumulasiByRentang($tglMulai, $tglAkhir);

        return view('admin/pembayaran', [
            'data'                => $data,
            'akumulasi'           => $akumulasi,
            'tglMulai'            => $tglMulai,
            'tglAkhir'            => $tglAkhir,
            'highlightIdentifier' => $identifier,
        ]);
    }

    public function konfirmasi($idPembayaran)
    {
        $bayar = $this->pembayaranModel->find($idPembayaran);

        if (! $bayar) {
            return redirect()->to('/pembayaran')->with('error', 'Data pembayaran tidak ditemukan.');
        }

        $this->pembayaranModel->update($idPembayaran, [
            'status_bayar' => 'lunas',
            'metode_bayar' => $bayar['metode_bayar'] ?? 'Midtrans',
        ]);

        $berhasil = $this->sinkronkanStatusSewa($bayar, 'disewa');

        if (! $berhasil) {
            log_message('error', "[PembayaranadminController::konfirmasi] gagal sinkronkan status sewa untuk id_pembayaran={$idPembayaran}");
            return redirect()->to('/pembayaran')->with('error', 'Pembayaran ditandai lunas, tapi gagal memperbarui status sewa. Cek log.');
        }

        return redirect()->to('/pembayaran')->with('success', 'Pembayaran ditandai lunas, status sewa diperbarui menjadi Disewa.');
    }

    public function tolak($idPembayaran)
    {
        $bayar = $this->pembayaranModel->find($idPembayaran);

        if (! $bayar) {
            return redirect()->to('/pembayaran')->with('error', 'Data pembayaran tidak ditemukan.');
        }

        $this->pembayaranModel->update($idPembayaran, ['status_bayar' => 'gagal']);

        $berhasil = $this->sinkronkanStatusSewa($bayar, 'dibatalkan');

        if (! $berhasil) {
            log_message('error', "[PembayaranadminController::tolak] gagal sinkronkan status sewa untuk id_pembayaran={$idPembayaran}");
            return redirect()->to('/pembayaran')->with('error', 'Pembayaran ditolak, tapi gagal memperbarui status sewa. Cek log.');
        }

        return redirect()->to('/pembayaran')->with('success', 'Pembayaran ditolak, status sewa diperbarui menjadi Dibatalkan.');
    }

    private function sinkronkanStatusSewa(array $bayar, string $statusBaru): bool
    {
        if (! empty($bayar['kode_transaksi'])) {
            $itemsTerkait = $this->rentalModel->historyByKodeTransaksi($bayar['kode_transaksi']);

            if (empty($itemsTerkait)) {
                return false;
            }

            $semuaBerhasil = true;
            foreach ($itemsTerkait as $item) {
                $ok = $this->rentalModel->ubahStatusManual((int) $item['id_sewa'], $statusBaru);
                if (! $ok) {
                    $semuaBerhasil = false;
                }
            }

            return $semuaBerhasil;
        }

        if (! empty($bayar['id_sewa'])) {
            return $this->rentalModel->ubahStatusManual((int) $bayar['id_sewa'], $statusBaru);
        }
        return false;
    }

    private function resolveRentang(): array
    {
        $tglMulai = $this->request->getGet('tanggal_mulai');
        $tglAkhir = $this->request->getGet('tanggal_akhir');

        if (! $tglMulai || ! $tglAkhir) {
            $tglMulai = date('Y-m-d');
            $tglAkhir = date('Y-m-d');
        }

        if (strtotime($tglMulai) > strtotime($tglAkhir)) {
            [$tglMulai, $tglAkhir] = [$tglAkhir, $tglMulai];
        }

        return [$tglMulai, $tglAkhir];
    }
}
