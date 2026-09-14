<?php

namespace App\Models;

use CodeIgniter\Model;

class DiskonModel extends Model
{
    protected $table            = 'diskon';
    protected $primaryKey       = 'id_diskon';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields = [
        'nama',
        'kode_voucher',
        'tipe',
        'nilai',
        'minimal_belanja', // ← baru
        'maks_potongan',
        'id_perlengkapan',
        'tgl_mulai',
        'tgl_selesai',
        'status',
        'keterangan',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'nama'        => 'required|min_length[3]',
        'tipe'        => 'required|in_list[persen,nominal]',
        'nilai'       => 'required|numeric|greater_than[0]',
        'tgl_mulai'   => 'required|valid_date',
        'tgl_selesai' => 'required|valid_date',
    ];

    /**
     * Cari 1 diskon OTOMATIS (tanpa kode) yang sedang aktif untuk produk tertentu,
     * DAN memenuhi syarat minimal belanja (kalau syarat itu diisi).
     */
    public function getOtomatisAktif(int $idPerlengkapan, float $subtotal = 0): ?array
    {
        $hariIni = date('Y-m-d');

        $hasil = $this->where('kode_voucher IS NULL')
            ->where('status', 'aktif')
            ->where('tgl_mulai <=', $hariIni)
            ->where('tgl_selesai >=', $hariIni)
            ->groupStart()
            ->where('minimal_belanja IS NULL')
            ->orWhere('minimal_belanja <=', $subtotal)
            ->groupEnd()
            ->groupStart()
            ->where('id_perlengkapan', $idPerlengkapan)
            ->orGroupStart()
            ->where('id_perlengkapan', null)
            ->groupEnd()
            ->groupEnd()
            ->findAll();

        if (empty($hasil)) {
            return null;
        }

        // Prioritas: diskon dengan minimal_belanja (lebih spesifik) di atas yang umum,
        // lalu yang berlaku spesifik untuk produk ini di atas yang berlaku umum
        usort($hasil, function ($a, $b) {
            $skorA = ($a['minimal_belanja'] !== null ? 2 : 0) + ($a['id_perlengkapan'] !== null ? 1 : 0);
            $skorB = ($b['minimal_belanja'] !== null ? 2 : 0) + ($b['id_perlengkapan'] !== null ? 1 : 0);
            return $skorB <=> $skorA;
        });

        return $hasil[0];
    }

    /**
     * Cari diskon berdasarkan kode voucher, validasi lengkap (aktif, tanggal, produk match, minimal belanja).
     * Return null kalau kode tidak valid/kadaluwarsa/tidak berlaku untuk produk ini/belum capai minimal belanja.
     */
    public function cariByKode(string $kode, int $idPerlengkapan, float $subtotal = 0): ?array
    {
        $hariIni = date('Y-m-d');

        $diskon = $this->where('kode_voucher', $kode)
            ->where('status', 'aktif')
            ->where('tgl_mulai <=', $hariIni)
            ->where('tgl_selesai >=', $hariIni)
            ->first();

        if (! $diskon) {
            return null;
        }

        // Kalau diskon dibatasi ke produk tertentu, pastikan produk yang disewa cocok
        if ($diskon['id_perlengkapan'] !== null && (int) $diskon['id_perlengkapan'] !== $idPerlengkapan) {
            return null;
        }

        // Cek syarat minimal belanja
        if (! empty($diskon['minimal_belanja']) && $subtotal < (float) $diskon['minimal_belanja']) {
            return null;
        }

        return $diskon;
    }

    /**
     * Ambil pesan error yang lebih informatif untuk kode voucher yang gagal
     * (dipakai supaya user tahu KENAPA gagal, bukan cuma "tidak valid").
     */
    public function pesanGagalVoucher(string $kode, int $idPerlengkapan, float $subtotal): string
    {
        $hariIni = date('Y-m-d');

        $diskon = $this->where('kode_voucher', $kode)->first();

        if (! $diskon) {
            return 'Kode voucher tidak ditemukan.';
        }

        if ($diskon['status'] !== 'aktif') {
            return 'Voucher ini sedang tidak aktif.';
        }

        if ($diskon['tgl_mulai'] > $hariIni || $diskon['tgl_selesai'] < $hariIni) {
            return 'Voucher ini sudah tidak berlaku (di luar periode aktif).';
        }

        if ($diskon['id_perlengkapan'] !== null && (int) $diskon['id_perlengkapan'] !== $idPerlengkapan) {
            return 'Voucher ini tidak berlaku untuk produk yang dipilih.';
        }

        if (! empty($diskon['minimal_belanja']) && $subtotal < (float) $diskon['minimal_belanja']) {
            $kurang = (float) $diskon['minimal_belanja'] - $subtotal;
            return 'Belanja belum mencapai minimal Rp ' . number_format($diskon['minimal_belanja'], 0, ',', '.') .
                '. Kurang Rp ' . number_format($kurang, 0, ',', '.') . ' lagi.';
        }

        return 'Kode voucher tidak valid atau sudah kedaluwarsa.';
    }

    /**
     * Ambil semua voucher (yang PAKAI kode, bukan diskon event otomatis) yang sedang aktif
     * untuk 1 produk tertentu — dipakai di halaman sewa langsung.
     */
    public function getVoucherTersediaUntukProduk(int $idPerlengkapan): array
    {
        $hariIni = date('Y-m-d');

        return $this->where('kode_voucher IS NOT NULL')
            ->where('status', 'aktif')
            ->where('tgl_mulai <=', $hariIni)
            ->where('tgl_selesai >=', $hariIni)
            ->groupStart()
                ->where('id_perlengkapan', $idPerlengkapan)
                ->orGroupStart()
                    ->where('id_perlengkapan', null)
                ->groupEnd()
            ->groupEnd()
            ->orderBy('nilai', 'DESC')
            ->findAll();
    }

    /**
     * Sama seperti di atas, tapi untuk konteks keranjang (berlaku umum, tidak terikat 1 produk).
     */
    public function getVoucherTersediaUmum(): array
    {
        $hariIni = date('Y-m-d');

        return $this->where('kode_voucher IS NOT NULL')
            ->where('status', 'aktif')
            ->where('tgl_mulai <=', $hariIni)
            ->where('tgl_selesai >=', $hariIni)
            ->orderBy('nilai', 'DESC')
            ->findAll();
    }
    /**
     * Hitung nominal potongan aktual dari 1 diskon terhadap subtotal.
     * Selalu dihitung ulang di server, tidak pernah dipercaya dari input client.
     */
    public function hitungPotongan(array $diskon, float $subtotal): float
    {
        if ($diskon['tipe'] === 'persen') {
            $potongan = $subtotal * ((float) $diskon['nilai'] / 100);

            if (! empty($diskon['maks_potongan']) && $potongan > (float) $diskon['maks_potongan']) {
                $potongan = (float) $diskon['maks_potongan'];
            }
        } else {
            $potongan = (float) $diskon['nilai'];
        }

        return min($potongan, $subtotal);
    }
}