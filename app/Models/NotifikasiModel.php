<?php

namespace App\Models;

use CodeIgniter\Model;

class NotifikasiModel extends Model
{
    protected $table            = 'notifikasi';
    protected $primaryKey       = 'id_notifikasi';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'tipe',
        'judul',
        'pesan',
        'id_referensi',
        'url',
        'is_read',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // WAJIB string kosong, bukan false

    /**
     * Notifikasi yang belum dibaca, terbaru duluan.
     */
    public function belumDibaca(int $limit = 8): array
    {
        return $this->where('is_read', 0)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function hitungBelumDibaca(): int
    {
        return $this->where('is_read', 0)->countAllResults();
    }

    public function tandaiDibaca(int $id): bool
    {
        return $this->update($id, ['is_read' => 1]);
    }

    public function tandaiSemuaDibaca(): bool
    {
        return $this->where('is_read', 0)->set(['is_read' => 1])->update();
    }

    /**
     * Cegah notifikasi duplikat untuk transaksi yang sama (dipakai oleh
     * RentalModel::buatNotifikasiPesananBaru saat checkout multi-item,
     * supaya 1 transaksi keranjang isi 3 produk tidak jadi 3 notifikasi).
     */
    public function sudahAdaUntukReferensi(string $idReferensi): bool
    {
        return $this->where('id_referensi', $idReferensi)->first() !== null;
    }
}
