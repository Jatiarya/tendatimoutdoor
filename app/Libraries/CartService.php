<?php

namespace App\Libraries;

class CartService
{
    protected const SESSION_KEY = 'keranjang_sewa';

    public function semua(): array
    {
        return session()->get(self::SESSION_KEY) ?? [];
    }

    public function tambah(int $idPerlengkapan, int $jumlah, string $tglSewa, string $tglKembaliRencana): void
    {
        $keranjang = $this->semua();

        // Kalau produk yang sama dengan tanggal sama sudah ada di keranjang, gabungkan jumlahnya
        foreach ($keranjang as &$item) {
            if ($item['id_perlengkapan'] == $idPerlengkapan
                && $item['tgl_sewa'] == $tglSewa
                && $item['tgl_kembali_rencana'] == $tglKembaliRencana) {
                $item['jumlah'] += $jumlah;
                session()->set(self::SESSION_KEY, $keranjang);
                return;
            }
        }
        unset($item);

        $keranjang[] = [
            'key'                 => uniqid('item_'), // identifier unik per baris keranjang, buat hapus/edit
            'id_perlengkapan'     => $idPerlengkapan,
            'jumlah'              => $jumlah,
            'tgl_sewa'            => $tglSewa,
            'tgl_kembali_rencana' => $tglKembaliRencana,
        ];

        session()->set(self::SESSION_KEY, $keranjang);
    }

    public function hapus(string $key): void
    {
        $keranjang = array_values(array_filter($this->semua(), fn ($item) => $item['key'] !== $key));
        session()->set(self::SESSION_KEY, $keranjang);
    }

    public function updateJumlah(string $key, int $jumlahBaru): void
    {
        $keranjang = $this->semua();
        foreach ($keranjang as &$item) {
            if ($item['key'] === $key) {
                $item['jumlah'] = max(1, $jumlahBaru);
                break;
            }
        }
        unset($item);
        session()->set(self::SESSION_KEY, $keranjang);
    }

    public function kosongkan(): void
    {
        session()->remove(self::SESSION_KEY);
    }

    public function jumlahItem(): int
    {
        return count($this->semua());
    }
}