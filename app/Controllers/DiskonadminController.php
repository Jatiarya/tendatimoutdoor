<?php

namespace App\Controllers;

use App\Models\DiskonModel;
use App\Models\PerlengkapanModel;

class DiskonadminController extends BaseController
{
    protected $diskonModel;
    protected $perlengkapanModel;

    public function __construct()
    {
        $this->diskonModel      = new DiskonModel();
        $this->perlengkapanModel = new PerlengkapanModel();
    }

    public function index()
    {
        $data = $this->diskonModel->orderBy('created_at', 'DESC')->findAll();
        return view('admin/diskon', ['data' => $data]);
    }

    public function tambah()
    {
        $perlengkapan = $this->perlengkapanModel->findAll();
        return view('admin/diskontambah', ['perlengkapan' => $perlengkapan]);
    }

    public function simpan()
    {
        $rules = [
            'nama'        => 'required|min_length[3]',
            'tipe'        => 'required|in_list[persen,nominal]',
            'nilai'       => 'required|numeric|greater_than[0]',
            'tgl_mulai'   => 'required|valid_date',
            'tgl_selesai' => 'required|valid_date',
            'kode_voucher' => 'permit_empty|is_unique[diskon.kode_voucher]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if (strtotime($this->request->getPost('tgl_selesai')) < strtotime($this->request->getPost('tgl_mulai'))) {
            return redirect()->back()->withInput()->with('error', 'Tanggal selesai harus setelah tanggal mulai.');
        }

        $idPerlengkapan = $this->request->getPost('id_perlengkapan');

        $this->diskonModel->insert([
            'nama'            => $this->request->getPost('nama'),
            'kode_voucher'    => $this->request->getPost('kode_voucher') ?: null,
            'tipe'            => $this->request->getPost('tipe'),
            'nilai'           => $this->request->getPost('nilai'),
            'minimal_belanja'  => $this->request->getPost('minimal_belanja') ?: null,
            'maks_potongan'   => $this->request->getPost('maks_potongan') ?: null,
            'id_perlengkapan' => $idPerlengkapan ?: null,
            'tgl_mulai'       => $this->request->getPost('tgl_mulai'),
            'tgl_selesai'     => $this->request->getPost('tgl_selesai'),
            'status'          => $this->request->getPost('status') ?: 'aktif',
            'keterangan'      => $this->request->getPost('keterangan'),
        ]);

        return redirect()->to('/diskon')->with('success', 'Diskon berhasil dibuat.');
    }

    public function ubah($id_diskon)
    {
        $diskon = $this->diskonModel->find($id_diskon);
        $perlengkapan = $this->perlengkapanModel->findAll();

        if (! $diskon) {
            return redirect()->to('/diskon')->with('error', 'Diskon tidak ditemukan.');
        }

        return view('admin/diskonubah', ['diskon' => $diskon, 'perlengkapan' => $perlengkapan]);
    }

    public function update($id_diskon)
    {
        $rules = [
            'nama'         => 'required|min_length[3]',
            'tipe'         => 'required|in_list[persen,nominal]',
            'nilai'        => 'required|numeric|greater_than[0]',
            'tgl_mulai'    => 'required|valid_date',
            'tgl_selesai'  => 'required|valid_date',
            'kode_voucher' => "permit_empty|is_unique[diskon.kode_voucher,id_diskon,{$id_diskon}]",
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $idPerlengkapan = $this->request->getPost('id_perlengkapan');

        $this->diskonModel->update($id_diskon, [
            'nama'            => $this->request->getPost('nama'),
            'kode_voucher'    => $this->request->getPost('kode_voucher') ?: null,
            'tipe'            => $this->request->getPost('tipe'),
            'nilai'           => $this->request->getPost('nilai'),
            'minimal_belanja'  => $this->request->getPost('minimal_belanja') ?: null,
            'maks_potongan'   => $this->request->getPost('maks_potongan') ?: null,
            'id_perlengkapan' => $idPerlengkapan ?: null,
            'tgl_mulai'       => $this->request->getPost('tgl_mulai'),
            'tgl_selesai'     => $this->request->getPost('tgl_selesai'),
            'status'          => $this->request->getPost('status'),
            'keterangan'      => $this->request->getPost('keterangan'),
        ]);

        return redirect()->to('/diskon')->with('success', 'Diskon berhasil diperbarui.');
    }

    public function hapus($id_diskon)
    {
        $this->diskonModel->delete($id_diskon);
        return redirect()->to('/diskon')->with('success', 'Diskon berhasil dihapus.');
    }
}
