<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RentalModel;

class RentaladminController extends BaseController
{
    protected $rentalModel;
    public function __construct()
    {
        $this->rentalModel = new RentalModel();
    }
    public function index()
    {
        $this->rentalModel->tandaiTerlambat();

        $tglMulai = $this->request->getGet('tanggal_mulai');
        $tglAkhir = $this->request->getGet('tanggal_akhir');

        if (! $tglMulai || ! $tglAkhir) {
            $tglMulai = date('Y-m-d');
            $tglAkhir = date('Y-m-d');
        }

        if (strtotime($tglMulai) > strtotime($tglAkhir)) {
            [$tglMulai, $tglAkhir] = [$tglAkhir, $tglMulai];
        }

        $data              = $this->rentalModel->allForAdminByRentang($tglMulai, $tglAkhir);
        $ringkasan         = $this->rentalModel->ringkasanByRentang($tglMulai, $tglAkhir);
        $perlengkapanAktif = $this->rentalModel->perlengkapanAktifByRentang($tglMulai, $tglAkhir);

        return view('admin/penyewaan', [
            'data'              => $data,
            'ringkasan'         => $ringkasan,
            'perlengkapanAktif' => $perlengkapanAktif,
            'tglMulai'          => $tglMulai,
            'tglAkhir'          => $tglAkhir,
        ]);
    }


    public function ubah($id_sewa)
    {
        $sewa = $this->rentalModel->findWithPerlengkapan($id_sewa);

        if (! $sewa) {
            return redirect()->to('/penyewaan')->with('error', 'Data penyewaan tidak ditemukan.');
        }

        return view('admin/penyewaanubah', ['sewa' => $sewa]);
    }

    public function update($id_sewa)
    {
        $statusValid = ['menunggu_konfirmasi', 'disewa', 'ditolak', 'dibatalkan', 'dikembalikan', 'terlambat'];

        $statusBaru = $this->request->getPost('status');

        if (! in_array($statusBaru, $statusValid, true)) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }

        $berhasil = $this->rentalModel->ubahStatusManual((int) $id_sewa, $statusBaru);

        if (! $berhasil) {
            return redirect()->to('/penyewaan')->with('error', 'Gagal memperbarui status. Data tidak ditemukan.');
        }

        return redirect()->to('/penyewaan')->with('success', 'Status penyewaan berhasil diperbarui.');
    }

    public function hapus($identifier)
    {
        $this->rentalModel->hapusTransaksi($identifier);
        return redirect()->to('/penyewaan')->with('success', 'Pesanan berhasil dihapus.');
    }


    public function tolak($identifier)
    {
        $berhasil = $this->rentalModel->ubahStatusTransaksi($identifier, 'ditolak');

        if (! $berhasil) {
            return redirect()->to('/penyewaan')->with('error', 'Gagal menolak pesanan.');
        }

        return redirect()->to('/penyewaan')->with('success', 'Pesanan ditolak.');
    }

    public function dikembalikan($identifier)
    {
        $berhasil = $this->rentalModel->ubahStatusTransaksi($identifier, 'dikembalikan');

        if (! $berhasil) {
            return redirect()->to('/penyewaan')->with('error', 'Gagal memproses pengembalian.');
        }

        return redirect()->to('/penyewaan')->with('success', 'Barang ditandai sudah dikembalikan, stok diperbarui, dan data masuk ke laporan.');
    }
    public function lihatKtp($id_sewa)
    {
        $sewa = $this->rentalModel->find($id_sewa);

        if (! $sewa || empty($sewa['foto_ktp'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Foto KTP tidak ditemukan');
        }

        $path = WRITEPATH . 'uploads/ktp/' . $sewa['foto_ktp'];

        if (! is_file($path)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Foto KTP tidak ditemukan');
        }

        $mime = mime_content_type($path);

        return $this->response
            ->setContentType($mime)
            ->setBody(file_get_contents($path));
    }

    public function ubahTransaksi($identifier)
    {
        if (str_starts_with($identifier, 'SINGLE-')) {
            $idSewa = (int) str_replace('SINGLE-', '', $identifier);
            $items = [$this->rentalModel->findWithPerlengkapan($idSewa)];
        } else {
            $items = $this->rentalModel->historyByKodeTransaksi($identifier);
        }

        if (empty($items) || ! $items[0]) {
            return redirect()->to('/penyewaan')->with('error', 'Data penyewaan tidak ditemukan.');
        }

        return view('admin/penyewaan_ubah_transaksi', [
            'identifier' => $identifier,
            'items'      => $items,
            'statusSaatIni' => $items[0]['status'],
        ]);
    }

    public function updateTransaksi($identifier)
    {
        $statusValid = ['menunggu_konfirmasi', 'disewa', 'ditolak', 'dibatalkan', 'dikembalikan', 'terlambat'];
        $statusBaru = $this->request->getPost('status');

        if (! in_array($statusBaru, $statusValid, true)) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }

        $berhasil = $this->rentalModel->ubahStatusTransaksi($identifier, $statusBaru);

        if (! $berhasil) {
            return redirect()->to('/penyewaan')->with('error', 'Gagal memperbarui status pesanan.');
        }

        return redirect()->to('/penyewaan')->with('success', 'Status pesanan berhasil diperbarui.');
    }
}