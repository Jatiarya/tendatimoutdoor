<?php

namespace App\Controllers;
use App\Models\PerlengkapanModel;
use App\Models\KategoriModel;

class PerlengkapanController extends BaseController
{
    protected $perlengkapanModel;
    protected $kategoriModel;
    public function __construct()
    {
        
        $this->perlengkapanModel = new PerlengkapanModel();
        $this->kategoriModel = new KategoriModel();
    }

    public function index()
    {
        $perlengkapan = $this->perlengkapanModel->getPerlengkapanWithKategori();
        $data = ['perlengkapan' => $perlengkapan];
        return view('admin/perlengkapan', $data);
    }
    public function tambah() {

        $data['kategori'] = $this->kategoriModel->getPerlengkapanWithKategori();
        return view('admin/perlengkapantambah', $data);
    }
   public function simpan()
{
    $fileFoto = $this->request->getFile('foto_perlengkapan');

    $namaFoto = 'default.png';

    if ($fileFoto->isValid() && !$fileFoto->hasMoved()) {
        $namaFoto = $fileFoto->getRandomName();
        $fileFoto->move('uploads/perlengkapan', $namaFoto);
    }

    $this->perlengkapanModel->insert([
        'kd_perlengkapan'   => $this->request->getPost('kd_perlengkapan'),
        'nama_perlengkapan' => $this->request->getPost('nama_perlengkapan'),
        'id_kategori'       => $this->request->getPost('kategori'),
        'stok'              => $this->request->getPost('stok'),
        'harga_sewa'        => $this->request->getPost('harga_sewa'),
        'foto_perlengkapan' => $namaFoto
    ]);

    return redirect()->to('/perlengkapan')
                     ->with('success', 'Data perlengkapan berhasil ditambahkan.');
}    
    public function ubah($id_perlengkapan) {
        $data['perlengkapan'] = $this->perlengkapanModel->getPerlengkapanById($id_perlengkapan);
        $data['kategori'] = $this->kategoriModel->findAll();
        return view('admin/perlengkapanubah', $data);
    }
   public function update($id_perlengkapan)
{
    $perlengkapan = $this->perlengkapanModel->getPerlengkapanById($id_perlengkapan);

    if (!$perlengkapan) {
        return redirect()->back()->with('error', 'Data tidak ditemukan.');
    }

    $file = $this->request->getFile('foto_perlengkapan');

    $namaFoto = $perlengkapan['foto_perlengkapan'] ?? 'default.png';

    if ($file->isValid() && !$file->hasMoved()) {

        if (
            $namaFoto != 'default.png' &&
            file_exists(FCPATH . 'uploads/perlengkapan/' . $namaFoto)
        ) {
            unlink(FCPATH . 'uploads/perlengkapan/' . $namaFoto);
        }

        $namaFoto = $file->getRandomName();
        $file->move(FCPATH . 'uploads/perlengkapan', $namaFoto);
    }

    $this->perlengkapanModel->update($id_perlengkapan, [
        'kd_perlengkapan'   => $this->request->getPost('kd_perlengkapan'),
        'nama_perlengkapan' => $this->request->getPost('nama_perlengkapan'),
        'id_kategori'       => $this->request->getPost('kategori'),
        'stok'              => $this->request->getPost('stok'),
        'harga_sewa'        => $this->request->getPost('harga_sewa'),
        'foto_perlengkapan' => $namaFoto
    ]);

    return redirect()->to('/perlengkapan')->with('success', 'Data berhasil diperbarui.');
}
   public function hapus($id)
{
    $this->perlengkapanModel->delete($id);

    return redirect()->to('/perlengkapan')
                     ->with('success', 'Data berhasil dihapus.');
}
}