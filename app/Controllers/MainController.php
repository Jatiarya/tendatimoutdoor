<?php

namespace App\Controllers;

use App\Models\PerlengkapanModel;
use App\Models\KategoriModel;
use App\Models\GaleriModel;

class MainController extends BaseController
{
    protected $perlengkapanModel;
    protected $kategoriModel;
    protected $galeriModel;
    public function __construct()
    {

        $this->perlengkapanModel = new PerlengkapanModel();
        $this->kategoriModel = new KategoriModel();
        $this->galeriModel = new GaleriModel();
    }
    public function index()
    {
        $data = [
            'title' => 'Home - Tendatim Outdoor'
        ];
        return view('home', $data);
    }
    public function produk()
    {
        $perlengkapan =  $this->perlengkapanModel->findAll();
        $kategori = $this->kategoriModel->findAll();

        $data = [
            'title' => 'Produk - Tendatim Outdoor',
            'perlengkapan' => $perlengkapan,
            'kategori' => $kategori,

        ];
        return view('/produk', $data);
    }
    public function about()
    {
        $perlengkapan = $this->perlengkapanModel->findAll();
        $galeri = $this->galeriModel->findAll();
        $data = [
            'title' => 'Tentang Kami - Tendatim Outdoor',
            'galeri' => $galeri,
            'perlengkapan' => $perlengkapan
        ];
        return view('tentang', $data);
        return view('tentang');
    }

    public function kontak()
    {
        $data = ['title' => 'Kontak Kami - Tendatim Outddoor'];
        return view('kontak', $data);
    }

    public function kirimPesanKontak()
    {
        $rules = [
            'nama'    => 'required|min_length[3]',
            'email'   => 'required|valid_email',
            'pesan'   => 'required|min_length[10]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = \Config\Services::email();
        $email->setTo(env('ADMIN_EMAIL', 'jazlyyy04@gmail.com'));
        $email->setFrom($this->request->getPost('email'), $this->request->getPost('nama'));
        $email->setSubject('Pesan Baru dari Formulir Kontak - Tendatim Outdoor');
        $email->setMessage(
            'Nama: ' . esc($this->request->getPost('nama')) . "\n" .
                'Email: ' . esc($this->request->getPost('email')) . "\n" .
                'Telepon: ' . esc($this->request->getPost('telepon')) . "\n\n" .
                'Pesan:' . "\n" . esc($this->request->getPost('pesan'))
        );

        $email->send();

        return redirect()->to(base_url('/kontak'))->with('success', 'Pesan kamu berhasil dikirim! Kami akan segera menghubungimu.');
    }
}
