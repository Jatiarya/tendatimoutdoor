<?php

namespace App\Controllers;

use App\Models\NotifikasiModel;

class NotifikasiController extends BaseController
{
    protected $notifikasiModel;

    public function __construct()
    {
        $this->notifikasiModel = new NotifikasiModel();
    }

    public function unread()
    {
        $items = array_map(function ($n) {
            return [
                'id'    => (int) $n['id_notifikasi'],
                'judul' => $n['judul'],
                'pesan' => $n['pesan'],
                'url'   => $n['url'],
                'waktu' => $n['created_at'],
            ];
        }, $this->notifikasiModel->belumDibaca(8));

        return $this->response->setJSON([
            'count' => $this->notifikasiModel->hitungBelumDibaca(),
            'items' => $items,
        ]);
    }
    public function read($id)
    {
        $berhasil = $this->notifikasiModel->tandaiDibaca((int) $id);

        return $this->response->setJSON(['success' => $berhasil]);
    }
    public function readAll()
    {
        $berhasil = $this->notifikasiModel->tandaiSemuaDibaca();

        return $this->response->setJSON(['success' => $berhasil]);
    }
}
