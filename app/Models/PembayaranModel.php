<?php

namespace App\Models;

use CodeIgniter\Model;

class PembayaranModel extends Model
{
    protected $table            = 'pembayaran';
    protected $primaryKey       = 'id_pembayaran';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'id_sewa',
        'kode_transaksi',
        'order_id',
        'jumlah_bayar',
        'nilai_potongan',
        'kode_voucher',
        'metode_bayar',
        'status_bayar',
        'snap_token',
        'payload_callback',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // string kosong, BUKAN false

    public function findByOrderId(string $orderId)
    {
        return $this->where('order_id', $orderId)->first();
    }

    public function byIdSewa(int $idSewa)
    {
        return $this->where('id_sewa', $idSewa)->orderBy('created_at', 'DESC')->first();
    }

    /**
     * Data pembayaran untuk halaman admin, difilter rentang tanggal.
     * Status 'dikembalikan' dikecualikan karena datanya sudah dipindah ke laporan.
     *
     * PENTING: join ke 'penyewaan' WAJIB ada di method ini, karena kita
     * memfilter berdasarkan kolom penyewaan.status di WHERE clause di bawah.
     */
    public function allForAdminByRentang(string $tglMulai, string $tglAkhir)
    {
        $sql = "
            SELECT 
                pembayaran.*,
                GROUP_CONCAT(perlengkapan.nama_perlengkapan SEPARATOR ', ') as nama_perlengkapan,
                SUM(penyewaan.jumlah) as jumlah,
                MAX(penyewaan.status) as status_sewa,
                COUNT(penyewaan.id_sewa) as jumlah_item,
                MAX(user.username) as username
            FROM pembayaran
            LEFT JOIN penyewaan ON 
                (pembayaran.kode_transaksi IS NOT NULL AND penyewaan.kode_transaksi = pembayaran.kode_transaksi)
                OR
                (pembayaran.kode_transaksi IS NULL AND penyewaan.id_sewa = pembayaran.id_sewa)
            LEFT JOIN perlengkapan ON perlengkapan.id_perlengkapan = penyewaan.id_perlengkapan
            LEFT JOIN user ON user.id_user = penyewaan.id_user
            WHERE pembayaran.created_at >= ?
                AND pembayaran.created_at <= ?
                AND (penyewaan.status IS NULL OR penyewaan.status != 'dikembalikan')
            GROUP BY pembayaran.id_pembayaran
            ORDER BY pembayaran.created_at DESC
        ";

        $query = $this->db->query($sql, [$tglMulai . ' 00:00:00', $tglAkhir . ' 23:59:59']);

        return $query->getResultArray();
    }
    public function akumulasiByRentang(string $tglMulai, string $tglAkhir): array
    {
        $sql = "
            SELECT 
                COALESCE(SUM(pembayaran.jumlah_bayar), 0) as total_nominal,
                COUNT(*) as total_transaksi
            FROM pembayaran
            LEFT JOIN penyewaan ON 
                (pembayaran.kode_transaksi IS NOT NULL AND penyewaan.kode_transaksi = pembayaran.kode_transaksi)
                OR
                (pembayaran.kode_transaksi IS NULL AND penyewaan.id_sewa = pembayaran.id_sewa)
            WHERE pembayaran.status_bayar = 'lunas'
                AND pembayaran.created_at >= ?
                AND pembayaran.created_at <= ?
                AND (penyewaan.status IS NULL OR penyewaan.status != 'dikembalikan')
        ";

        $query = $this->db->query($sql, [$tglMulai . ' 00:00:00', $tglAkhir . ' 23:59:59']);
        $row = $query->getRowArray();

        return [
            'total_nominal'   => (float) ($row['total_nominal'] ?? 0),
            'total_transaksi' => (int) ($row['total_transaksi'] ?? 0),
        ];
    }
    /**
     * Total pendapatan (status lunas) per hari dalam minggu ini,
     * dipakai untuk chart "Revenue Analytics". Nilai dalam ribuan (untuk skala chart).
     */
    public function pendapatanMingguIni(): array
    {
        $awalMinggu = date('Y-m-d', strtotime('monday this week'));
        $hasil = array_fill(0, 7, 0);

        $rows = $this->select("DATE(created_at) as tanggal, SUM(jumlah_bayar) as total")
            ->where('status_bayar', 'lunas')
            ->where('created_at >=', $awalMinggu . ' 00:00:00')
            ->groupBy('DATE(created_at)')
            ->findAll();

        foreach ($rows as $r) {
            $index = (int) date('N', strtotime($r['tanggal'])) - 1;
            if ($index >= 0 && $index <= 6) {
                $hasil[$index] = (float) $r['total'];
            }
        }

        return $hasil;
    }

    /**
     * Total pendapatan (status lunas) per HARI dalam bulan berjalan
     * (dipakai chart filter "Bulan Ini").
     */
    public function pendapatanBulanIniHarian(): array
    {
        $totalHari = (int) date('t');
        $hasil = array_fill(1, $totalHari, 0);

        $rows = $this->select("DAY(created_at) as hari, SUM(jumlah_bayar) as total")
            ->where('status_bayar', 'lunas')
            ->where('MONTH(created_at)', date('n'))
            ->where('YEAR(created_at)', date('Y'))
            ->groupBy('DAY(created_at)')
            ->findAll();

        foreach ($rows as $r) {
            $hasil[(int) $r['hari']] = (float) $r['total'];
        }

        return array_values($hasil);
    }

    /**
     * Total pendapatan (status lunas) per BULAN dalam tahun berjalan
     * (dipakai chart filter "Tahun Ini"). Selalu 12 elemen, Jan s/d Des.
     */
    public function pendapatanTahunIniBulanan(): array
    {
        $hasil = array_fill(1, 12, 0);

        $rows = $this->select("MONTH(created_at) as bulan, SUM(jumlah_bayar) as total")
            ->where('status_bayar', 'lunas')
            ->where('YEAR(created_at)', date('Y'))
            ->groupBy('MONTH(created_at)')
            ->findAll();

        foreach ($rows as $r) {
            $hasil[(int) $r['bulan']] = (float) $r['total'];
        }

        return array_values($hasil);
    }

    /**
     * Total pendapatan (status lunas) sepanjang waktu.
     */
    public function totalPendapatan(): float
    {
        $row = $this->selectSum('jumlah_bayar', 'total')
            ->where('status_bayar', 'lunas')
            ->get()
            ->getRowArray();

        return (float) ($row['total'] ?? 0);
    }

    /**
     * Total pendapatan (status lunas) pada bulan & tahun tertentu.
     */
    public function pendapatanByBulan(int $bulan, int $tahun): float
    {
        $row = $this->selectSum('jumlah_bayar', 'total')
            ->where('status_bayar', 'lunas')
            ->where('MONTH(created_at)', $bulan)
            ->where('YEAR(created_at)', $tahun)
            ->get()
            ->getRowArray();

        return (float) ($row['total'] ?? 0);
    }
    public function byKodeTransaksi(string $kodeTransaksi)
    {
        return $this->where('kode_transaksi', $kodeTransaksi)->orderBy('created_at', 'DESC')->first();
    }
}
