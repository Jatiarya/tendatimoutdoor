<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\GaleriModel;

class GaleriController extends BaseController
{
    protected $galeriModel;

    protected string $uploadPath = FCPATH . 'uploads/galery/';

    public function __construct()
    {
        $this->galeriModel = new GaleriModel();
    }

    public function index()
    {
        $data['galeri'] = $this->galeriModel->orderBy('id', 'DESC')->findAll();
        return view('admin/tentang', $data);
    }

    public function tambah()
    {
        return view('admin/tentangtambah');
    }

    public function simpan()
    {
        $validationRule = [
            'nama_foto' => 'required|min_length[3]|max_length[150]',
            'alt'       => 'permit_empty|max_length[150]',
            'foto'      => [
                'label' => 'Foto',
                'rules' => 'uploaded[foto]'
                    . '|is_image[foto]'
                    . '|mime_in[foto,image/jpg,image/jpeg,image/png,image/webp]'
                    . '|max_size[foto,3072]', // max 3MB
            ],
        ];

        if (!$this->validate($validationRule)) {
            return redirect()->to('/galeritentang/tambah')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('foto');

        if (!$file->isValid() || $file->hasMoved()) {
            return redirect()->to('/galeritentang/tambah')
                ->withInput()
                ->with('error', 'File tidak valid, silakan coba lagi.');
        }

        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }

        $newName = $file->getRandomName();
        $file->move($this->uploadPath, $newName);

        $this->galeriModel->insert([
            'nama_foto'  => $this->request->getPost('nama_foto'),
            'alt'        => $this->request->getPost('alt') ?: $this->request->getPost('nama_foto'),
            'foto'       => $newName,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/galeritentang')->with('success', 'Foto berhasil ditambahkan.');
    }

    public function ubah($id)
    {
        $foto = $this->galeriModel->find($id);

        if (!$foto) {
            return redirect()->to('/galeritentang')->with('error', 'Foto tidak ditemukan.');
        }

        return view('admin/tentangubah', ['foto' => $foto]);
    }

    public function update($id)
    {
        $existing = $this->galeriModel->find($id);

        if (!$existing) {
            return redirect()->to('/galeritentang')->with('error', 'Foto tidak ditemukan.');
        }

        $validationRule = [
            'nama_foto' => 'required|min_length[3]|max_length[150]',
            'alt'       => 'permit_empty|max_length[150]',
            'foto'      => [
                'label' => 'Foto',
                'rules' => 'permit_empty'
                    . '|is_image[foto]'
                    . '|mime_in[foto,image/jpg,image/jpeg,image/png,image/webp]'
                    . '|max_size[foto,3072]',
            ],
        ];

        if (!$this->validate($validationRule)) {
            return redirect()->to('/galeritentang/ubah/' . $id)
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'nama_foto'  => $this->request->getPost('nama_foto'),
            'alt'        => $this->request->getPost('alt') ?: $this->request->getPost('nama_foto'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $file = $this->request->getFile('foto');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            if (!is_dir($this->uploadPath)) {
                mkdir($this->uploadPath, 0755, true);
            }

            $newName = $file->getRandomName();
            $file->move($this->uploadPath, $newName);
            $updateData['foto'] = $newName;

            if (!empty($existing['foto']) && file_exists($this->uploadPath . $existing['foto'])) {
                unlink($this->uploadPath . $existing['foto']);
            }
        }

        $this->galeriModel->update($id, $updateData);

        return redirect()->to('/galeritentang')->with('success', 'Foto berhasil diperbarui.');
    }

    public function hapus($id)
    {
        $foto = $this->galeriModel->find($id);

        if (!$foto) {
            return redirect()->to('/galeritentang')->with('error', 'Foto tidak ditemukan.');
        }

        if (!empty($foto['foto']) && file_exists($this->uploadPath . $foto['foto'])) {
            unlink($this->uploadPath . $foto['foto']);
        }

        $this->galeriModel->delete($id);

        return redirect()->to('/galeritentang')->with('success', 'Foto berhasil dihapus.');
    }
}
