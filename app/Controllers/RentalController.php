<?php

namespace App\Controllers;

use App\Models\RentalModel;
use App\Models\PerlengkapanModel;
use App\Models\PembayaranModel;
use App\Models\DiskonModel;
use App\Models\AuthModel;
use App\Libraries\Midtrans;
use Config\Midtrans as MidtransConfig;

class RentalController extends BaseController
{
    protected $rentalModel;
    protected $perlengkapanModel;
    protected $pembayaranModel;
    protected $diskonModel;
    protected $authModel;

    public function __construct()
    {
        $this->rentalModel       = new RentalModel();
        $this->perlengkapanModel = new PerlengkapanModel();
        $this->pembayaranModel   = new PembayaranModel();
        $this->diskonModel       = new DiskonModel();
        $this->authModel         = new AuthModel();
    }

    public function create($id_perlengkapan)
    {
        $perlengkapan = $this->perlengkapanModel->find($id_perlengkapan);

        if (! $perlengkapan) {
            return redirect()->to(base_url('/katalog'))->with('error', 'katalog tidak ditemukan.');
        }

        if ($perlengkapan['stok'] < 1) {
            return redirect()->to(base_url('/katalog'))->with('error', 'Maaf, stok perlengkapan ini sedang habis.');
        }

        $jumlah     = (int) ($this->request->getGet('jumlah') ?: (old('jumlah') ?: 1));
        $tglSewa    = $this->request->getGet('tgl_sewa') ?: old('tgl_sewa');
        $tglRencana = $this->request->getGet('tgl_kembali_rencana') ?: old('tgl_kembali_rencana');

        $estimasi = null;
        if ($tglSewa && $tglRencana && $jumlah > 0) {
            $durasiHari = (int) ceil((strtotime($tglRencana) - strtotime($tglSewa)) / 86400);
            if ($durasiHari > 0) {
                $estimasi = [
                    'durasi_hari' => $durasiHari,
                    'total'       => $durasiHari * $jumlah * $perlengkapan['harga_sewa'],
                ];
            }
        }

        $diskonOtomatis = $this->diskonModel->getOtomatisAktif((int) $id_perlengkapan, $estimasi['total'] ?? 0);

        $voucherTersedia = $this->diskonModel->getVoucherTersediaUntukProduk((int) $id_perlengkapan);

        $user = $this->authModel->findById(session()->get('id_user'));

        $midtransConfig = new MidtransConfig();

        return view('sewa', [
            'perlengkapan'        => $perlengkapan,
            'jumlah'              => $jumlah,
            'tgl_sewa'            => $tglSewa,
            'tgl_kembali_rencana' => $tglRencana,
            'estimasi'            => $estimasi,
            'diskonOtomatis'      => $diskonOtomatis,
            'voucherTersedia'     => $voucherTersedia,   // ← baru
            'user'                => $user,
            'clientKey'           => $midtransConfig->clientKey,
            'isProduction'        => $midtransConfig->isProduction,
        ]);
    }

    public function proses()
    {
        $rules = [
            'id_perlengkapan'     => 'required|integer',
            'jumlah'              => 'required|integer|greater_than[0]',
            'tgl_sewa'            => 'required|valid_date',
            'tgl_kembali_rencana' => 'required|valid_date',
        ];

        if (! $this->validate($rules)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'errors'  => $this->validator->getErrors(),
            ]);
        }

        $user = $this->authModel->findById(session()->get('id_user'));

        if (empty($user['foto_ktp'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'success'      => false,
                'message'      => 'Kamu perlu mengunggah foto KTP di halaman profil terlebih dahulu.',
                'redirect_url' => base_url('profile'),
            ]);
        }

        if (($user['status_ktp'] ?? null) === 'ditolak') {
            return $this->response->setStatusCode(422)->setJSON([
                'success'      => false,
                'message'      => 'Verifikasi KTP kamu ditolak admin' . (! empty($user['catatan_ktp']) ? ' (' . $user['catatan_ktp'] . ')' : '') . '. Silakan unggah ulang foto KTP di halaman profil sebelum menyewa.',
                'redirect_url' => base_url('profile'),
            ]);
        }

        $id_perlengkapan = (int) $this->request->getPost('id_perlengkapan');
        $jumlah = (int) $this->request->getPost('jumlah');

        $perlengkapan = $this->perlengkapanModel->find($id_perlengkapan);

        if (! $perlengkapan) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Produk tidak ditemukan.',
            ]);
        }

        if ($perlengkapan['stok'] < $jumlah) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => "Stok tidak mencukupi. Stok tersedia: {$perlengkapan['stok']} unit.",
            ]);
        }

        $tglSewa    = $this->request->getPost('tgl_sewa');
        $tglRencana = $this->request->getPost('tgl_kembali_rencana');

        if (strtotime($tglRencana) <= strtotime($tglSewa)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Tanggal rencana kembali harus setelah tanggal sewa.',
            ]);
        }

        $durasiHari = (int) ceil((strtotime($tglRencana) - strtotime($tglSewa)) / 86400);
        $subtotal   = (int) ($durasiHari * $jumlah * $perlengkapan['harga_sewa']);

        $diskonDipakai = null;
        $kodeVoucherInput = trim((string) $this->request->getPost('kode_voucher'));

        if (! empty($kodeVoucherInput)) {
            $diskonDipakai = $this->diskonModel->cariByKode($kodeVoucherInput, $id_perlengkapan, $subtotal);
        } else {
            $diskonDipakai = $this->diskonModel->getOtomatisAktif($id_perlengkapan, $subtotal);
        }

        $nilaiPotongan = $diskonDipakai ? $this->diskonModel->hitungPotongan($diskonDipakai, $subtotal) : 0;
        $totalBayar    = $subtotal - $nilaiPotongan;

        $idSewa = $this->rentalModel->insert([
            'id_user'             => session()->get('id_user'),
            'id_perlengkapan'     => $id_perlengkapan,
            'jumlah'              => $jumlah,
            'tgl_sewa'            => $tglSewa,
            'tgl_kembali_rencana' => $tglRencana,
            'status'              => 'menunggu_konfirmasi',
            'foto_ktp'            => $user['foto_ktp'],
            'nomor_wa'            => $user['nomor_wa'] ?? null,
        ], true);

        if (! $idSewa) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data penyewaan. Silakan coba lagi.',
            ]);
        }


        $orderId = 'TENDATIM-' . $idSewa . '-' . time();

        $dataPembayaranDasar = [
            'id_sewa'        => $idSewa,
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

            return $this->response->setJSON([
                'success'      => true,
                'has_snap'     => false,
                'metode_bayar' => 'cod',
                'id_sewa'      => $idSewa,
                'redirect_url' => base_url('pembayaran/' . $idSewa),
                'message'      => 'Penyewaan berhasil dibuat dengan metode COD. Admin akan menghubungimu untuk konfirmasi.',
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

            $this->rentalModel->ubahStatusManual($idSewa, 'dibatalkan');

            return $this->response->setJSON([
                'success'      => true,
                'has_snap'     => false,
                'id_sewa'      => $idSewa,
                'redirect_url' => base_url('pembayaran/' . $idSewa),
                'message'      => 'Penyewaan tersimpan, tapi gagal membuat transaksi pembayaran otomatis. Silakan hubungi admin.',
            ]);
        }

        $this->pembayaranModel->insert(array_merge($dataPembayaranDasar, [
            'status_bayar' => 'pending',
            'snap_token'   => $trx['token'],
        ]));

        return $this->response->setJSON([
            'success'      => true,
            'has_snap'     => true,
            'id_sewa'      => $idSewa,
            'snap_token'   => $trx['token'],
            'redirect_url' => base_url('pembayaran/' . $idSewa),
        ]);
    }
    public function cekVoucher()
    {
        $kode           = trim((string) $this->request->getPost('kode_voucher'));
        $idPerlengkapan = (int) $this->request->getPost('id_perlengkapan');
        $subtotal       = (float) $this->request->getPost('subtotal');

        if (empty($kode)) {
            return $this->response->setJSON(['valid' => false, 'message' => 'Kode voucher tidak boleh kosong.']);
        }

        $diskon = $this->diskonModel->cariByKode($kode, $idPerlengkapan, $subtotal);

        if (! $diskon) {
            $pesan = $this->diskonModel->pesanGagalVoucher($kode, $idPerlengkapan, $subtotal);
            return $this->response->setJSON(['valid' => false, 'message' => $pesan]);
        }

        $potongan = $this->diskonModel->hitungPotongan($diskon, $subtotal);

        return $this->response->setJSON([
            'valid'     => true,
            'nama'      => $diskon['nama'],
            'potongan'  => $potongan,
            'totalBaru' => $subtotal - $potongan,
            'message'   => 'Voucher "' . $diskon['nama'] . '" berhasil diterapkan!',
        ]);
    }

    public function pembayaran($idSewa)
    {
        $sewa = $this->rentalModel->findWithPerlengkapan($idSewa);

        if (! $sewa || $sewa['id_user'] != session()->get('id_user')) {
            return redirect()->to(base_url('/riwayatsewa'))->with('error', 'Data tidak ditemukan.');
        }

        $bayar = $this->pembayaranModel->byIdSewa($idSewa);

        if (! $bayar) {
            return redirect()->to(base_url('/riwayatsewa'))
                ->with('error', 'Data pembayaran untuk penyewaan ini belum tersedia. Silakan hubungi admin.');
        }

        $midtransConfig = new MidtransConfig();

        return view('pembayaran', [
            'sewa'         => $sewa,
            'bayar'        => $bayar,
            'clientKey'    => $midtransConfig->clientKey,
            'isProduction' => $midtransConfig->isProduction,
        ]);
    }

    public function pembayaranTransaksi($kodeTransaksi)
    {
        $items = $this->rentalModel->historyByKodeTransaksi($kodeTransaksi);

        if (empty($items) || $items[0]['id_user'] != session()->get('id_user')) {
            return redirect()->to(base_url('/riwayatsewa'))->with('error', 'Data transaksi tidak ditemukan.');
        }

        $bayar = $this->pembayaranModel->byKodeTransaksi($kodeTransaksi);

        if (! $bayar) {
            return redirect()->to(base_url('/riwayatsewa'))
                ->with('error', 'Data pembayaran untuk transaksi ini belum tersedia. Silakan hubungi admin.');
        }

        $midtransConfig = new MidtransConfig();

        return view('pembayaran_transaksi', [
            'items'        => $items,
            'kodeTransaksi' => $kodeTransaksi,
            'bayar'        => $bayar,
            'clientKey'    => $midtransConfig->clientKey,
            'isProduction' => $midtransConfig->isProduction,
        ]);
    }
    public function statusCheckTransaksi($kodeTransaksi)
    {
        $items = $this->rentalModel->historyByKodeTransaksi($kodeTransaksi);

        if (empty($items) || $items[0]['id_user'] != session()->get('id_user')) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Data tidak ditemukan']);
        }

        $bayar = $this->pembayaranModel->byKodeTransaksi($kodeTransaksi);

        return $this->response->setJSON([
            'status_bayar' => $bayar['status_bayar'] ?? 'pending',
        ]);
    }
    public function statusCheck($idSewa)
    {
        $sewa = $this->rentalModel->find($idSewa);

        if (! $sewa || $sewa['id_user'] != session()->get('id_user')) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Data tidak ditemukan']);
        }

        $bayar = $this->pembayaranModel->byIdSewa($idSewa);

        return $this->response->setJSON([
            'status_bayar' => $bayar['status_bayar'] ?? 'pending',
        ]);
    }

    public function callback()
    {
        $notif = $this->request->getJSON(true);

        if (! $notif || empty($notif['order_id'])) {
            return $this->response->setStatusCode(400)->setBody('Invalid payload');
        }

        $midtrans = new Midtrans();
        if (! $midtrans->verifySignature($notif)) {
            return $this->response->setStatusCode(403)->setBody('Invalid signature');
        }

        $orderId = $notif['order_id'];
        $bayar = $this->pembayaranModel->findByOrderId($orderId);

        if (! $bayar) {
            return $this->response->setStatusCode(404)->setBody('Order not found');
        }

        $transactionStatus = $notif['transaction_status'] ?? '';
        $fraudStatus        = $notif['fraud_status'] ?? '';

        $status = 'pending';
        if ($transactionStatus == 'capture' && $fraudStatus == 'accept') {
            $status = 'lunas';
        } elseif ($transactionStatus == 'settlement') {
            $status = 'lunas';
        } elseif ($transactionStatus == 'expire') {
            $status = 'expired';
        } elseif (in_array($transactionStatus, ['cancel', 'deny'])) {
            $status = 'gagal';
        }

        $this->pembayaranModel->update($bayar['id_pembayaran'], [
            'status_bayar'     => $status,
            'metode_bayar'     => $notif['payment_type'] ?? null,
            'payload_callback' => json_encode($notif),
        ]);

        $statusSewaBaru = match ($status) {
            'lunas'            => 'disewa',
            'gagal', 'expired' => 'dibatalkan',
            default            => null,
        };

        if ($statusSewaBaru !== null) {
            if (! empty($bayar['kode_transaksi'])) {
                $itemsTerkait = $this->rentalModel->historyByKodeTransaksi($bayar['kode_transaksi']);
                foreach ($itemsTerkait as $item) {
                    $this->rentalModel->ubahStatusManual((int) $item['id_sewa'], $statusSewaBaru);
                }
            } elseif (! empty($bayar['id_sewa'])) {
                $this->rentalModel->ubahStatusManual((int) $bayar['id_sewa'], $statusSewaBaru);
            }
        }

        return $this->response->setStatusCode(200)->setBody('OK');
    }

    // GET /riwayatsewa
    public function history()
    {
        $data = $this->rentalModel->historyByUser(session()->get('id_user'));
        return view('riwayatsewa', ['riwayat' => $data]);
    }
}