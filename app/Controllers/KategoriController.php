<?php

namespace App\Controllers;
use App\Controllers\BaseController;
use App\Models\KategoriModel;


class KategoriController extends BaseController
{
    protected $kategoriModel;

    public function __construct()
    {
        $this->kategoriModel = new KategoriModel();
    }

    public function index()
    {
    $data['kategori'] = $this->kategoriModel->findAll();
    
    return view('admin/kategori', $data);
    }
    public function tambah () {
        return view('admin/kategoritambah');
    }
    public function simpan()
{
    $this->kategoriModel->insert([
        'kd_kategori'   => $this->request->getPost('kd_kategori'),
        'nama_kategori' => $this->request->getPost('nama_kategori')
    ]);

    return redirect()->to('/kategori');
}
    public function ubah($id_kategori) {
    $data['kategori'] = $this->kategoriModel->getKategoriById($id_kategori);
    return view ('admin/kategoriubah', $data);
    }
    public function update($id_kategori)
    {
        $this->kategoriModel->save([
            'id_kategori' => $id_kategori,
            'kd_kategori' => $this->request->getPost('kd_kategori'),
            'nama_kategori' => $this->request->getPost('nama_kategori')
        ]);
        return redirect()->to('/kategori');
    }
    public function hapus($id_kategori) {
        $this->kategoriModel->delete($id_kategori);
        return redirect()->to('/kategori');
    }
}