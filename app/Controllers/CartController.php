<?php

namespace App\Controllers;

use App\Libraries\CartService;
use App\Libraries\Midtrans;
use App\Models\PerlengkapanModel;
use App\Models\RentalModel;
use App\Models\PembayaranModel;
use App\Models\DiskonModel;
use App\Models\AuthModel;
use Config\Midtrans as MidtransConfig;

class CartController extends BaseController
{
    protected $cart;
    protected $perlengkapanModel;
    protected $rentalModel;
    protected $pembayaranModel;
    protected $diskonModel;
    protected $authModel;

    public function __construct()
    {
        $this->cart               = new CartService();
        $this->perlengkapanModel  = new PerlengkapanModel();
        $this->rentalModel        = new RentalModel();
        $this->pembayaranModel    = new PembayaranModel();
        $this->diskonModel        = new DiskonModel();
        $this->authModel          = new AuthModel();
    }

    public function tambah()
    {
        $idPerlengkapan = (int) $this->request->getPost('id_perlengkapan');
        $jumlah         = (int) $this->request->getPost('jumlah');
        $tglSewa        = $this->request->getPost('tgl_sewa');
        $tglKembali     = $this->request->getPost('tgl_kembali_rencana');

        if ($jumlah < 1 || ! $tglSewa || ! $tglKembali) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Lengkapi jumlah dan tanggal sewa terlebih dahulu.',
            ]);
        }

        if (strtotime($tglKembali) <= strtotime($tglSewa)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Tanggal kembali harus setelah tanggal sewa.',
            ]);
        }

        $perlengkapan = $this->perlengkapanModel->find($idPerlengkapan);

        if (! $perlengkapan) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Produk tidak ditemukan.',
            ]);
        }

        if ($perlengkapan['stok'] < $jumlah) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => "Stok tidak cukup. Tersedia: {$perlengkapan['stok']} unit.",
            ]);
        }

        $this->cart->tambah($idPerlengkapan, $jumlah, $tglSewa, $tglKembali);

        return $this->response->setJSON([
            'success'     => true,
            'message'     => 'Ditambahkan ke keranjang.',
            'jumlah_item' => $this->cart->jumlahItem(),
        ]);
    }

    public function index()
    {
        $items = $this->hidrasiKeranjang();
        $user  = $this->authModel->findById(session()->get('id_user'));

        $subtotalKeseluruhan = array_sum(array_column($items, 'subtotal'));

        $midtransConfig = new \Config\Midtrans();

        return view('keranjang', [
            'items'           => $items,
            'user'            => $user,
            'subtotal'        => $subtotalKeseluruhan,
            'voucherTersedia' => $this->diskonModel->getVoucherTersediaUmum(), // ← baru
            'clientKey'       => $midtransConfig->clientKey,
            'isProduction'    => $midtransConfig->isProduction,
        ]);
    }

    public function hapus()
    {
        $key = $this->request->getPost('key');

        if (empty($key)) {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Item tidak valid.']);
        }

        $this->cart->hapus($key);

        return $this->response->setJSON([
            'success'     => true,
            'jumlah_item' => $this->cart->jumlahItem(),
        ]);
    }

    public function checkout()
    {
        $items = $this->hidrasiKeranjang();

        if (empty($items)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Keranjang kosong.',
            ]);
        }

        $idUser = session()->get('id_user');
        $user   = $this->authModel->findById($idUser);

        if (empty($user['foto_ktp'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'success'      => false,
                'message'      => 'Unggah foto KTP di halaman profil sebelum checkout.',
                'redirect_url' => base_url('profile'),
            ]);
        }

        foreach ($items as $item) {
            if ($item['perlengkapan']['stok'] < $item['jumlah']) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => "Stok {$item['perlengkapan']['nama_perlengkapan']} tidak cukup (tersisa {$item['perlengkapan']['stok']}).",
                ]);
            }
        }

        $kodeVoucherInput = trim((string) $this->request->getPost('kode_voucher'));
        $kodeTransaksi    = 'TRX-' . $idUser . '-' . time();

        $totalSubtotal = 0;
        $idSewaList    = [];

        foreach ($items as $item) {
            $p = $item['perlengkapan'];
            $durasiHari   = (int) ceil((strtotime($item['tgl_kembali_rencana']) - strtotime($item['tgl_sewa'])) / 86400);
            $subtotalItem = $durasiHari * $item['jumlah'] * $p['harga_sewa'];
            $totalSubtotal += $subtotalItem;

            $idSewa = $this->rentalModel->insert([
                'id_user'             => $idUser,
                'kode_transaksi'      => $kodeTransaksi,
                'id_perlengkapan'     => $p['id_perlengkapan'],
                'jumlah'              => $item['jumlah'],
                'tgl_sewa'            => $item['tgl_sewa'],
                'tgl_kembali_rencana' => $item['tgl_kembali_rencana'],
                'status'              => 'menunggu_konfirmasi',
                'foto_ktp'            => $user['foto_ktp'],
                'nomor_wa'            => $user['nomor_wa'],
            ], true);

            if (! $idSewa) {
                foreach ($idSewaList as $id) {
                    $this->rentalModel->delete($id);
                }

                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan sebagian data penyewaan. Silakan coba lagi.',
                ]);
            }

            $idSewaList[] = $idSewa;
        }

        $diskonDipakai = null;
        if (! empty($kodeVoucherInput)) {
            $diskonDipakai = $this->diskonModel->cariByKode($kodeVoucherInput, 0, $totalSubtotal);
        }

        $nilaiPotongan = $diskonDipakai ? $this->diskonModel->hitungPotongan($diskonDipakai, $totalSubtotal) : 0;
        $totalBayar    = (int) ($totalSubtotal - $nilaiPotongan);

        $orderId = 'TENDATIM-' . $kodeTransaksi;

        $dataPembayaranDasar = [
            'kode_transaksi' => $kodeTransaksi,
            'order_id'       => $orderId,
            'jumlah_bayar'   => $totalBayar,
            'id_diskon'      => $diskonDipakai['id_diskon'] ?? null,
            'kode_voucher'   => $diskonDipakai['kode_voucher'] ?? null,
            'nilai_potongan' => $nilaiPotongan,
        ];
        $metodePembayaran = $this->request->getPost('metode_pembayaran') === 'cod' ? 'cod' : 'online';

        if ($metodePembayaran === 'cod') {
            $this->pembayaranModel->insert(array_merge($dataPembayaranDasar, [
                'status_bayar' => 'pending',
                'metode_bayar' => 'cod',
                'snap_token'   => null,
            ]));

            $this->cart->kosongkan();

            return $this->response->setJSON([
                'success'        => true,
                'has_snap'       => false,
                'metode_bayar'   => 'cod',
                'kode_transaksi' => $kodeTransaksi,
                'redirect_url'   => base_url('pembayaran-transaksi/' . $kodeTransaksi),
                'message'        => 'Pesanan berhasil dibuat dengan metode COD. Admin akan menghubungimu untuk konfirmasi.',
            ]);
        }

        $midtransUser = [
            'first_name' => session()->get('username'),
            'email'      => session()->get('email'),
        ];

        $midtrans = new Midtrans();
        $trx = $midtrans->createTransaction($orderId, $totalBayar, $midtransUser);

        if (! $trx) {
            $this->pembayaranModel->insert(array_merge($dataPembayaranDasar, [
                'status_bayar' => 'gagal',
                'snap_token'   => null,
            ]));

            foreach ($idSewaList as $idSewa) {
                $this->rentalModel->ubahStatusManual((int) $idSewa, 'dibatalkan');
            }

            $this->cart->kosongkan();

            return $this->response->setJSON([
                'success'        => true,
                'has_snap'       => false,
                'kode_transaksi' => $kodeTransaksi,
                'redirect_url'   => base_url('pembayaran-transaksi/' . $kodeTransaksi),
                'message'        => 'Pesanan tersimpan, tapi gagal membuat transaksi pembayaran otomatis. Silakan hubungi admin.',
            ]);
        }

        $this->pembayaranModel->insert(array_merge($dataPembayaranDasar, [
            'status_bayar' => 'pending',
            'snap_token'   => $trx['token'],
        ]));

        $this->cart->kosongkan();

        return $this->response->setJSON([
            'success'        => true,
            'has_snap'       => true,
            'snap_token'     => $trx['token'],
            'kode_transaksi' => $kodeTransaksi,
            'redirect_url'   => base_url('pembayaran-transaksi/' . $kodeTransaksi),
        ]);
    }

    private function hidrasiKeranjang(): array
    {
        $keranjangMentah = $this->cart->semua();
        $hasil = [];

        foreach ($keranjangMentah as $item) {
            $p = $this->perlengkapanModel->find($item['id_perlengkapan']);
            if (! $p) {
                continue;
            }

            $durasiHari = (int) ceil((strtotime($item['tgl_kembali_rencana']) - strtotime($item['tgl_sewa'])) / 86400);

            $hasil[] = array_merge($item, [
                'perlengkapan' => $p,
                'durasi_hari'  => $durasiHari,
                'subtotal'     => $durasiHari * $item['jumlah'] * $p['harga_sewa'],
            ]);
        }

        return $hasil;
    }
}