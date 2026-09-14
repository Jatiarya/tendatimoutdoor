<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanModel extends Model
{
    protected $table            = 'laporan';
    protected $primaryKey       = 'id_laporan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'id_sewa',
        'id_user',
        'id_perlengkapan',
        'username',
        'nama_perlengkapan',
        'jumlah',
        'tgl_sewa',
        'tgl_kembali_rencana',
        'tgl_kembali_aktual',
        'durasi_hari',
        'total_biaya',
        'order_id',
        'jumlah_bayar',
        'nilai_potongan',
        'kode_voucher',
        'metode_bayar',
        'status_bayar',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // WAJIB string kosong, bukan false

    public function sudahAdaLaporan(int $idSewa): bool
    {
        return $this->where('id_sewa', $idSewa)->first() !== null;
    }

    public function rekapByRentang(string $tglMulai, string $tglAkhir)
    {
        return $this->where('tgl_kembali_aktual >=', $tglMulai)
            ->where('tgl_kembali_aktual <=', $tglAkhir)
            ->orderBy('tgl_kembali_aktual', 'DESC')
            ->findAll();
    }

    public function akumulasiByRentang(string $tglMulai, string $tglAkhir): array
    {
        $row = $this->builder()
            ->select('COALESCE(SUM(jumlah_bayar), 0) as total_pendapatan, COUNT(*) as total_transaksi, COALESCE(SUM(jumlah), 0) as total_unit')
            ->where('tgl_kembali_aktual >=', $tglMulai)
            ->where('tgl_kembali_aktual <=', $tglAkhir)
            ->get()
            ->getRowArray();

        return [
            'total_pendapatan' => (float) $row['total_pendapatan'],
            'total_transaksi'  => (int) $row['total_transaksi'],
            'total_unit'       => (int) $row['total_unit'],
        ];
    }
    /**
     * Total pendapatan KESELURUHAN (sepanjang waktu) dari transaksi yang sudah
     * selesai (masuk laporan = status sewa 'dikembalikan') DAN pembayarannya 'lunas'.
     * Dipakai di dashboard, supaya angkanya sinkron dengan definisi "pendapatan" di halaman Laporan.
     */
    public function totalPendapatan(): float
    {
        $row = $this->builder()
            ->selectSum('jumlah_bayar', 'total')
            ->where('status_bayar', 'lunas')
            ->get()
            ->getRowArray();

        return (float) ($row['total'] ?? 0);
    }

    /**
     * Total pendapatan (status_bayar lunas) pada bulan & tahun tertentu.
     * Menggunakan tgl_kembali_aktual sebagai acuan bulan, karena itu tanggal
     * transaksi benar-benar "selesai" dan tercatat ke laporan.
     */
    public function pendapatanByBulan(int $bulan, int $tahun): float
    {
        $row = $this->builder()
            ->selectSum('jumlah_bayar', 'total')
            ->where('status_bayar', 'lunas')
            ->where('MONTH(tgl_kembali_aktual)', $bulan)
            ->where('YEAR(tgl_kembali_aktual)', $tahun)
            ->get()
            ->getRowArray();

        return (float) ($row['total'] ?? 0);
    }
}