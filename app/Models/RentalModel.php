<?php

namespace App\Models;

use CodeIgniter\Model;

class RentalModel extends Model
{
    protected $table            = 'penyewaan';
    protected $primaryKey       = 'id_sewa';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'id_user',
        'kode_transaksi',
        'id_perlengkapan',
        'jumlah',
        'tgl_sewa',
        'tgl_kembali_rencana',
        'tgl_kembali_aktual',
        'status',
        'foto_ktp',
        'nomor_wa'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'id_perlengkapan'     => 'required|integer',
        'jumlah'              => 'required|integer|greater_than[0]',
        'tgl_sewa'            => 'required|valid_date',
        'tgl_kembali_rencana' => 'required|valid_date',
    ];

    protected $allowCallbacks = true;
    protected $afterInsert    = ['buatNotifikasiPesananBaru'];

    /**
     * Dipanggil otomatis setiap kali 1 baris penyewaan baru ke-insert
     * (baik dari sewa langsung/RentalController maupun keranjang/CartController
     * -- keduanya sama-sama lewat $this->insert() di model ini, jadi cukup
     * di-hook di 1 tempat ini saja).
     *
     * Untuk transaksi multi-item (dari keranjang, berbagi 1 kode_transaksi),
     * kita CUMA mau 1 notifikasi per transaksi, bukan 1 per item. Makanya
     * dicek dulu ke NotifikasiModel apakah id_referensi (kode_transaksi)
     * ini sudah pernah dibuatkan notifikasinya.
     */
    protected function buatNotifikasiPesananBaru(array $eventData)
    {
        // Hanya untuk pesanan baru yang statusnya menunggu_konfirmasi.
        // (insert() lain di luar alur checkout normal, kalau ada, tidak akan match ini)
        if (empty($eventData['data']['status']) || $eventData['data']['status'] !== 'menunggu_konfirmasi') {
            return $eventData;
        }

        $idSewa = $eventData['id'] ?? null;
        if (! $idSewa) {
            return $eventData;
        }

        $row = $this->find($idSewa);
        if (! $row) {
            return $eventData;
        }

        $identifier = ! empty($row['kode_transaksi'])
            ? $row['kode_transaksi']
            : ('SINGLE-' . $row['id_sewa']);

        $notifikasiModel = new \App\Models\NotifikasiModel();

        // Sudah ada notifikasi untuk transaksi ini (item sebelumnya dalam
        // checkout keranjang yang sama)? Skip, jangan bikin duplikat.
        if ($notifikasiModel->sudahAdaUntukReferensi($identifier)) {
            return $eventData;
        }

        $user     = (new \App\Models\AuthModel())->find($row['id_user']);
        $username = $user['username'] ?? 'Pengguna';

        $notifikasiModel->insert([
            'tipe'         => 'penyewaan_baru',
            'judul'        => 'Pesanan baru masuk',
            'pesan'        => $username . ' mengajukan penyewaan baru dan menunggu konfirmasi pembayaran.',
            'id_referensi' => $identifier,
            // Arahkan ke halaman PEMBAYARAN (bukan ubah status penyewaan), karena
            // langkah pertama admin adalah konfirmasi apakah pembayarannya berhasil.
            // Query "identifier" dipakai PembayaranadminController untuk auto-scroll
            // & highlight baris yang relevan begitu admin klik notifikasi ini.
            'url'          => base_url('pembayaran?identifier=' . urlencode($identifier)),
            'is_read'      => 0,
        ]);

        return $eventData;
    }

    /**
     * Riwayat sewa milik 1 user, DIKELOMPOKKAN per transaksi/pesanan.
     * - Transaksi dari keranjang (multi-item) -> 1 grup berisi banyak item
     * - Transaksi sewa langsung -> 1 grup berisi 1 item
     */
    public function historyByUser(int $idUser): array
    {
        $rows = $this->select('penyewaan.*, perlengkapan.nama_perlengkapan, perlengkapan.foto_perlengkapan, 
                               perlengkapan.harga_sewa, pembayaran.status_bayar, pembayaran.jumlah_bayar,
                               pembayaran.kode_voucher, pembayaran.nilai_potongan')
            ->join('perlengkapan', 'perlengkapan.id_perlengkapan = penyewaan.id_perlengkapan')
            ->join(
                'pembayaran',
                '(pembayaran.kode_transaksi IS NOT NULL AND pembayaran.kode_transaksi = penyewaan.kode_transaksi) 
                 OR 
                 (pembayaran.kode_transaksi IS NULL AND pembayaran.id_sewa = penyewaan.id_sewa)',
                'left',
                false
            )
            ->where('penyewaan.id_user', $idUser)
            ->orderBy('penyewaan.created_at', 'DESC')
            ->findAll();

        $grouped = [];

        foreach ($rows as $r) {
            $groupKey = ! empty($r['kode_transaksi']) ? $r['kode_transaksi'] : ('SINGLE-' . $r['id_sewa']);

            if (! isset($grouped[$groupKey])) {
                $grouped[$groupKey] = [
                    'group_key'      => $groupKey,
                    'kode_transaksi' => $r['kode_transaksi'],
                    'is_multi'       => ! empty($r['kode_transaksi']),
                    'status'         => $r['status'],
                    'status_bayar'   => $r['status_bayar'],
                    'jumlah_bayar'   => $r['jumlah_bayar'],
                    'kode_voucher'   => $r['kode_voucher'],
                    'nilai_potongan' => $r['nilai_potongan'],
                    'created_at'     => $r['created_at'],
                    'items'          => [],
                ];
            }

            $grouped[$groupKey]['items'][] = $r;
        }

        return array_values($grouped);
    }

    public function findWithPerlengkapan(int $idSewa)
    {
        return $this->select('penyewaan.*, perlengkapan.nama_perlengkapan, perlengkapan.foto_perlengkapan, perlengkapan.harga_sewa')
            ->join('perlengkapan', 'perlengkapan.id_perlengkapan = penyewaan.id_perlengkapan')
            ->where('penyewaan.id_sewa', $idSewa)
            ->first();
    }

    public function allForAdmin()
    {
        return $this->select('penyewaan.*, perlengkapan.nama_perlengkapan, user.username, user.email')
            ->join('perlengkapan', 'perlengkapan.id_perlengkapan = penyewaan.id_perlengkapan')
            ->join('user', 'user.id_user = penyewaan.id_user')
            ->orderBy('penyewaan.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Semua data penyewaan untuk admin, DIKELOMPOKKAN per transaksi.
     * - Transaksi dari keranjang (multi-item) -> 1 grup berisi banyak item, key = kode_transaksi
     * - Transaksi sewa langsung (1 item, tanpa kode_transaksi) -> 1 grup berisi 1 item, key = 'SINGLE-{id_sewa}'
     */
    public function allForAdminByRentang(string $tglMulai, string $tglAkhir)
    {
        $sql = "
            SELECT 
                penyewaan.*,
                perlengkapan.nama_perlengkapan,
                perlengkapan.harga_sewa,
                user.username,
                user.email,
                pembayaran.jumlah_bayar,
                pembayaran.nilai_potongan,
                pembayaran.kode_voucher,
                pembayaran.status_bayar
            FROM penyewaan
            JOIN perlengkapan ON perlengkapan.id_perlengkapan = penyewaan.id_perlengkapan
            JOIN user ON user.id_user = penyewaan.id_user
            LEFT JOIN pembayaran ON 
                (penyewaan.kode_transaksi IS NOT NULL AND pembayaran.kode_transaksi = penyewaan.kode_transaksi)
                OR
                (penyewaan.kode_transaksi IS NULL AND pembayaran.id_sewa = penyewaan.id_sewa)
            WHERE penyewaan.created_at >= ?
                AND penyewaan.created_at <= ?
                AND penyewaan.status NOT IN ('dikembalikan', 'ditolak', 'dibatalkan')
            ORDER BY penyewaan.created_at DESC
        ";

        $rows = $this->db->query($sql, [$tglMulai . ' 00:00:00', $tglAkhir . ' 23:59:59'])->getResultArray();

        // ── KELOMPOKKAN per transaksi (sama seperti sebelumnya) ──
        $grouped = [];

        foreach ($rows as $r) {
            $groupKey = ! empty($r['kode_transaksi']) ? $r['kode_transaksi'] : ('SINGLE-' . $r['id_sewa']);

            if (! isset($grouped[$groupKey])) {
                $grouped[$groupKey] = [
                    'group_key'      => $groupKey,
                    'kode_transaksi' => $r['kode_transaksi'],
                    'is_multi'       => ! empty($r['kode_transaksi']),
                    'id_user'        => $r['id_user'],
                    'username'       => $r['username'],
                    'email'          => $r['email'],
                    'nomor_wa'       => $r['nomor_wa'],
                    'status'         => $r['status'],
                    'created_at'     => $r['created_at'],
                    // ── DATA PEMBAYARAN ASLI (level transaksi, bukan per item) ──
                    'jumlah_bayar'   => $r['jumlah_bayar'],
                    'nilai_potongan' => $r['nilai_potongan'],
                    'kode_voucher'   => $r['kode_voucher'],
                    'status_bayar'   => $r['status_bayar'],
                    'items'          => [],
                ];
            }

            $grouped[$groupKey]['items'][] = $r;
        }

        return array_values($grouped);
    }

    /**
     * Ubah status untuk SEMUA item dalam 1 transaksi sekaligus (dipakai admin
     * saat approve/tolak/kembalikan pesanan yang berisi banyak produk).
     * $identifier bisa berupa kode_transaksi (untuk pesanan multi-item)
     * atau id_sewa tunggal (untuk pesanan sewa-langsung).
     */
    public function ubahStatusTransaksi(string $identifier, string $statusBaru): bool
    {
        if (str_starts_with($identifier, 'SINGLE-')) {
            $idSewa = (int) str_replace('SINGLE-', '', $identifier);
            return $this->ubahStatusManual($idSewa, $statusBaru);
        }

        // Anggap sebagai kode_transaksi -> ambil semua id_sewa yang terkait
        $items = $this->where('kode_transaksi', $identifier)->findAll();

        if (empty($items)) {
            return false;
        }

        $semuaBerhasil = true;
        foreach ($items as $item) {
            $ok = $this->ubahStatusManual((int) $item['id_sewa'], $statusBaru);
            if (! $ok) {
                $semuaBerhasil = false;
            }
        }

        return $semuaBerhasil;
    }

    /**
     * Hapus semua baris penyewaan dalam 1 transaksi (dipakai admin untuk hapus pesanan).
     */
    public function hapusTransaksi(string $identifier): bool
    {
        if (str_starts_with($identifier, 'SINGLE-')) {
            $idSewa = (int) str_replace('SINGLE-', '', $identifier);
            return $this->delete($idSewa);
        }

        return (bool) $this->where('kode_transaksi', $identifier)->delete();
    }

    public function ringkasanByRentang(string $tglMulai, string $tglAkhir): array
    {
        $totalPenyewa = $this->builder()
            ->select('id_user')
            ->where('created_at >=', $tglMulai . ' 00:00:00')
            ->where('created_at <=', $tglAkhir . ' 23:59:59')
            ->groupBy('id_user')
            ->get()
            ->getNumRows();

        $totalAktif = $this->builder()
            ->select('id_user')
            ->where('created_at >=', $tglMulai . ' 00:00:00')
            ->where('created_at <=', $tglAkhir . ' 23:59:59')
            ->where('status', 'disewa')
            ->groupBy('id_user')
            ->get()
            ->getNumRows();

        return [
            'total_penyewa' => $totalPenyewa,
            'total_aktif'   => $totalAktif,
        ];
    }

    public function perlengkapanAktifByRentang(string $tglMulai, string $tglAkhir): array
    {
        $rows = $this->select('perlengkapan.nama_perlengkapan, penyewaan.jumlah')
            ->join('perlengkapan', 'perlengkapan.id_perlengkapan = penyewaan.id_perlengkapan')
            ->where('penyewaan.created_at >=', $tglMulai . ' 00:00:00')
            ->where('penyewaan.created_at <=', $tglAkhir . ' 23:59:59')
            ->where('penyewaan.status', 'disewa')
            ->findAll();

        $totalUnit = 0;
        $breakdown = [];

        foreach ($rows as $r) {
            $totalUnit += (int) $r['jumlah'];
            $nama = $r['nama_perlengkapan'];
            $breakdown[$nama] = ($breakdown[$nama] ?? 0) + (int) $r['jumlah'];
        }

        arsort($breakdown);

        return [
            'total_unit'    => $totalUnit,
            'jenis_berbeda' => count($breakdown),
            'breakdown'     => $breakdown,
        ];
    }

    public function tandaiTerlambat()
    {
        return $this->where('status', 'disewa')
            ->where('tgl_kembali_rencana <', date('Y-m-d'))
            ->where('tgl_kembali_aktual', null)
            ->set(['status' => 'terlambat'])
            ->update();
    }
    public function historyByKodeTransaksi(string $kodeTransaksi)
    {
        return $this->select('penyewaan.*, perlengkapan.nama_perlengkapan, perlengkapan.foto_perlengkapan, perlengkapan.harga_sewa')
            ->join('perlengkapan', 'perlengkapan.id_perlengkapan = penyewaan.id_perlengkapan')
            ->where('penyewaan.kode_transaksi', $kodeTransaksi)
            ->findAll();
    }

    public function ubahStatusManual(int $idSewa, string $statusBaru): bool
    {
        $sewa = $this->findWithPerlengkapan($idSewa);

        if (! $sewa) {
            log_message('error', "[ubahStatusManual] sewa id={$idSewa} TIDAK DITEMUKAN.");
            return false;
        }

        $statusLama = $sewa['status'];

        if ($statusLama === $statusBaru) {
            log_message('debug', "[ubahStatusManual] status sama, tidak ada perubahan.");
            return true;
        }

        $perlengkapanModel = new \App\Models\PerlengkapanModel();

        $statusStokTerpotong = ['disewa', 'terlambat'];
        $sedangTerpotongSebelumnya = in_array($statusLama, $statusStokTerpotong, true);
        $akanTerpotongSesudahnya   = in_array($statusBaru, $statusStokTerpotong, true);

        if (! $sedangTerpotongSebelumnya && $akanTerpotongSesudahnya) {
            $perlengkapanModel->kurangiStok($sewa['id_perlengkapan'], $sewa['jumlah']);
        }

        if ($sedangTerpotongSebelumnya && ! $akanTerpotongSesudahnya) {
            $perlengkapanModel->tambahStok($sewa['id_perlengkapan'], $sewa['jumlah']);
        }

        // Tetap UPDATE biasa untuk semua status, termasuk ditolak/dibatalkan (TIDAK dihapus permanen)
        $updateData = ['status' => $statusBaru];

        $tglKembaliKosong = empty($sewa['tgl_kembali_aktual']) || $sewa['tgl_kembali_aktual'] === '0000-00-00';

        if ($statusBaru === 'dikembalikan' && $tglKembaliKosong) {
            $updateData['tgl_kembali_aktual'] = date('Y-m-d');
        }

        $berhasil = $this->update($idSewa, $updateData);

        if (! $berhasil) {
            log_message('error', "[ubahStatusManual] UPDATE STATUS GAGAL untuk sewa id={$idSewa}. Validation errors: " . json_encode($this->errors()));
            return false;
        }

        log_message('debug', "[ubahStatusManual] update status BERHASIL untuk sewa id={$idSewa} -> {$statusBaru}");

        // Guard ganda: catatKeLaporan() HANYA untuk status 'dikembalikan'
        if ($statusBaru === 'dikembalikan') {
            $this->catatKeLaporan($idSewa);
        }

        return true;
    }

    /**
     * Catat 1 data sewa + pembayarannya ke tabel laporan_penyewaan.
     * Dibungkus try-catch supaya kalau ada error, TERCATAT DI LOG, bukan diam-diam gagal.
     */
    protected function catatKeLaporan(int $idSewa): void
    {
        try {
            $laporanModel = new \App\Models\LaporanModel();

            if ($laporanModel->sudahAdaLaporan($idSewa)) {
                log_message('debug', "[catatKeLaporan] sewa id={$idSewa} SUDAH ADA di laporan, dilewati.");
                return;
            }

            $sewa = $this->findWithPerlengkapan($idSewa);

            if (! $sewa) {
                log_message('error', "[catatKeLaporan] sewa id={$idSewa} TIDAK DITEMUKAN saat mau dicatat.");
                return;
            }

            $durasiHari = (int) ceil(
                (strtotime($sewa['tgl_kembali_rencana']) - strtotime($sewa['tgl_sewa'])) / 86400
            );
            $subtotalItem = $durasiHari * $sewa['jumlah'] * $sewa['harga_sewa']; // harga ASLI item ini, sebelum diskon

            $db = \Config\Database::connect();
            $user = $db->table('user')->where('id_user', $sewa['id_user'])->get()->getRowArray();

            $pembayaranModel = new \App\Models\PembayaranModel();

            // ── Ambil data pembayaran: coba lewat kode_transaksi dulu (multi-item), fallback ke id_sewa (single) ──
            $bayar = null;
            $subtotalTransaksi = $subtotalItem; // default: anggap transaksi ini cuma 1 item

            if (! empty($sewa['kode_transaksi'])) {
                $bayar = $pembayaranModel->byKodeTransaksi($sewa['kode_transaksi']);

                // Hitung subtotal GABUNGAN semua item dalam transaksi ini (perlu untuk proporsi diskon per item)
                $semuaItemTransaksi = $this->historyByKodeTransaksi($sewa['kode_transaksi']);
                $subtotalTransaksi = 0;
                foreach ($semuaItemTransaksi as $it) {
                    $d = (int) ceil((strtotime($it['tgl_kembali_rencana']) - strtotime($it['tgl_sewa'])) / 86400);
                    $subtotalTransaksi += $d * $it['jumlah'] * $it['harga_sewa'];
                }
            } else {
                $bayar = $pembayaranModel->byIdSewa($idSewa);
            }

            // ── Hitung proporsi diskon untuk item ini (kalau transaksi multi-item, dibagi rata sesuai kontribusi harga) ──
            $nilaiPotonganItem = 0;
            $jumlahBayarItem   = $subtotalItem; // default: sama dengan harga asli, HANYA dipakai kalau memang tidak ada diskon

            if ($bayar && isset($bayar['nilai_potongan']) && (float) $bayar['nilai_potongan'] > 0 && $subtotalTransaksi > 0) {
                $proporsi = $subtotalItem / $subtotalTransaksi;
                $nilaiPotonganItem = round($bayar['nilai_potongan'] * $proporsi);
                $jumlahBayarItem   = $subtotalItem - $nilaiPotonganItem;
            } elseif ($bayar && isset($bayar['jumlah_bayar']) && (float) $bayar['jumlah_bayar'] > 0 && $subtotalTransaksi > 0) {
                // Fallback: kalau nilai_potongan kosong tapi jumlah_bayar ada, hitung proporsi dari situ
                $proporsi = $subtotalItem / $subtotalTransaksi;
                $jumlahBayarItem = round($bayar['jumlah_bayar'] * $proporsi);
                $nilaiPotonganItem = $subtotalItem - $jumlahBayarItem;
            } else {
                // Data pembayaran tidak ditemukan / tidak valid -> JANGAN diam-diam pakai harga penuh.
                // Catat sebagai warning supaya kelihatan di log kalau ada transaksi yang datanya bermasalah.
                log_message(
                    'warning',
                    "[catatKeLaporan] sewa id={$idSewa}: data pembayaran tidak ditemukan/tidak valid " .
                        "(kode_transaksi=" . ($sewa['kode_transaksi'] ?? '-') . "), memakai harga ASLI tanpa diskon sebagai fallback terakhir."
                );
            }

            $tglKembaliAktual = (empty($sewa['tgl_kembali_aktual']) || $sewa['tgl_kembali_aktual'] === '0000-00-00')
                ? date('Y-m-d')
                : $sewa['tgl_kembali_aktual'];

            $dataLaporan = [
                'id_sewa'             => $sewa['id_sewa'],
                'id_user'             => $sewa['id_user'],
                'id_perlengkapan'     => $sewa['id_perlengkapan'],
                'username'            => $user['username'] ?? '-',
                'nama_perlengkapan'   => $sewa['nama_perlengkapan'],
                'jumlah'              => $sewa['jumlah'],
                'tgl_sewa'            => $sewa['tgl_sewa'],
                'tgl_kembali_rencana' => $sewa['tgl_kembali_rencana'],
                'tgl_kembali_aktual'  => $tglKembaliAktual,
                'durasi_hari'         => $durasiHari,
                'total_biaya'         => $jumlahBayarItem,        // ← SEKARANG sudah dipotong diskon (proporsional)
                'order_id'            => $bayar['order_id'] ?? null,
                'jumlah_bayar'        => $jumlahBayarItem,        // ← konsisten dengan total_biaya
                'metode_bayar'        => $bayar['metode_bayar'] ?? null,
                'status_bayar'        => $bayar['status_bayar'] ?? null,
                'nilai_potongan'      => $nilaiPotonganItem,      // ← BARU, simpan info diskon
                'kode_voucher'        => $bayar['kode_voucher'] ?? null,
            ];

            $idLaporan = $laporanModel->insert($dataLaporan, true);

            if (! $idLaporan) {
                log_message('error', "[catatKeLaporan] INSERT GAGAL untuk sewa id={$idSewa}. Errors: " . json_encode($laporanModel->errors()));
            } else {
                log_message('debug', "[catatKeLaporan] BERHASIL! sewa id={$idSewa} -> laporan id={$idLaporan}, total_biaya(setelah diskon)={$jumlahBayarItem}");
            }
        } catch (\Throwable $e) {
            log_message('error', "[catatKeLaporan] EXCEPTION untuk sewa id={$idSewa}: " . $e->getMessage());
        }
    }
    public function statistikMingguIni(): array
    {
        $awalMinggu = date('Y-m-d', strtotime('monday this week'));
        $hasil = array_fill(0, 7, 0); // index 0 = Senin, 6 = Minggu

        $rows = $this->select("DATE(created_at) as tanggal, COUNT(*) as total")
            ->where('created_at >=', $awalMinggu . ' 00:00:00')
            ->groupBy('DATE(created_at)')
            ->findAll();

        foreach ($rows as $r) {
            $index = (int) date('N', strtotime($r['tanggal'])) - 1;
            if ($index >= 0 && $index <= 6) {
                $hasil[$index] = (int) $r['total'];
            }
        }

        return $hasil;
    }

    /**
     * Jumlah penyewaan per HARI dalam bulan berjalan (dipakai chart filter "Bulan Ini").
     * Panjang array mengikuti jumlah hari di bulan berjalan (28-31).
     */
    public function statistikBulanIni(): array
    {
        $totalHari = (int) date('t'); // jumlah hari di bulan ini
        $hasil = array_fill(1, $totalHari, 0);

        $rows = $this->select("DAY(created_at) as hari, COUNT(*) as total")
            ->where('MONTH(created_at)', date('n'))
            ->where('YEAR(created_at)', date('Y'))
            ->groupBy('DAY(created_at)')
            ->findAll();

        foreach ($rows as $r) {
            $hasil[(int) $r['hari']] = (int) $r['total'];
        }

        return array_values($hasil);
    }

    /**
     * Jumlah penyewaan per BULAN dalam tahun berjalan (dipakai chart filter "Tahun Ini").
     * Selalu 12 elemen, Januari s/d Desember.
     */
    public function statistikTahunIni(): array
    {
        $hasil = array_fill(1, 12, 0);

        $rows = $this->select("MONTH(created_at) as bulan, COUNT(*) as total")
            ->where('YEAR(created_at)', date('Y'))
            ->groupBy('MONTH(created_at)')
            ->findAll();

        foreach ($rows as $r) {
            $hasil[(int) $r['bulan']] = (int) $r['total'];
        }

        return array_values($hasil);
    }

    /**
     * Total penyewaan yang diajukan pada bulan & tahun tertentu,
     * dipakai untuk hitung persentase perbandingan bulan ini vs bulan lalu.
     */
    public function totalByBulan(int $bulan, int $tahun): int
    {
        return $this->where('MONTH(created_at)', $bulan)
            ->where('YEAR(created_at)', $tahun)
            ->countAllResults();
    }
}
