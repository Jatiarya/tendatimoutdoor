<?php

namespace App\Models;

use CodeIgniter\Model;

class PerlengkapanModel extends Model
{
    protected $table            = 'perlengkapan';
    protected $primaryKey       = 'id_perlengkapan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['kd_perlengkapan','nama_perlengkapan','id_kategori','stok','harga_sewa','foto_perlengkapan'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
    public function kurangiStok(int $idProduk, int $jumlah)
    {
        $this->set('stok', "stok - {$jumlah}", false)
             ->where('id_perlengkapan', $idProduk)
             ->update();
    }
 
    public function tambahStok(int $idProduk, int $jumlah)
    {
        $this->set('stok', "stok + {$jumlah}", false)
             ->where('id_perlengkapan', $idProduk)
             ->update();
    }
    public function getPerlengkapanWithKategori()
    {
        return $this->select('perlengkapan.*, kategori.nama_kategori')
            ->join('kategori', 'kategori.id_kategori = perlengkapan.id_kategori')
            ->findAll();
    }
    public function getPerlengkapanById($id_perlengkapan)
    {
        return $this->select('perlengkapan.*, kategori.nama_kategori')
            ->join('kategori', 'kategori.id_kategori = perlengkapan.id_kategori')
            ->where('perlengkapan.id_perlengkapan', $id_perlengkapan)
            ->first();
    }
    public function countActive(): int
    {
        return $this->where('stok >', 0)->countAllResults();
    }
}