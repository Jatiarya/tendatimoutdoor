<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AuthModel;

class UseradminController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new AuthModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Data Pengguna - Tendatim Outdoor',
            'user'  => $this->userModel->orderBy('id_user', 'ASC')->findAll(),
        ];

        return view('admin/pengguna', $data);
    }

    public function ubah($id)
    {
        $user = $this->userModel->find($id);

        if (! $user) {
            return redirect()->to(base_url('/pengguna'))
                ->with('error', 'Data pengguna tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Pengguna - Tendatim Outdoor',
            'user'  => $user,
        ];

        return view('admin/penggunaubah', $data);
    }

    public function update($id)
    {
        $user = $this->userModel->find($id);

        if (! $user) {
            return redirect()->to(base_url('/pengguna'))
                ->with('error', 'Data pengguna tidak ditemukan.');
        }

        $rules = [
            'username' => "required|min_length[3]|max_length[50]|is_unique[user.username,id_user,{$id}]",
            'email'    => "required|valid_email|is_unique[user.email,id_user,{$id}]",
            'level'    => 'required|in_list[user,admin]',
        ];

        $messages = [
            'username' => ['is_unique' => 'Username sudah dipakai user lain.'],
            'email'    => ['is_unique' => 'Email sudah dipakai user lain.'],
            'level'    => ['in_list' => 'Level tidak valid.'],
        ];

        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[6]';
        }

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'level'    => strtolower(trim($this->request->getPost('level'))),
        ];

        $password = $this->request->getPost('password');
        if (! empty($password)) {
            $updateData['password'] = $password;
        }

        if ((int) $id === (int) session()->get('id_user')) {
            session()->set([
                'username' => $updateData['username'],
                'email'    => $updateData['email'],
                'level'    => $updateData['level'],
            ]);
        }

        $berhasil = $this->userModel->skipValidation(true)->update($id, $updateData);

        if (! $berhasil) {
            log_message('error', '[UseradminController::update] Gagal update user id=' . $id . '. Errors: ' . json_encode($this->userModel->errors()));
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data pengguna. Silakan coba lagi.');
        }

        return redirect()->to(base_url('/pengguna'))
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function hapus($id)
    {
        if ((int) $id === (int) session()->get('id_user')) {
            return redirect()->to(base_url('/pengguna'))
                ->with('error', 'Kamu tidak bisa menghapus akunmu sendiri.');
        }

        $user = $this->userModel->find($id);

        if (! $user) {
            return redirect()->to(base_url('/pengguna'))
                ->with('error', 'Data pengguna tidak ditemukan.');
        }

        $this->userModel->delete($id);

        return redirect()->to(base_url('/pengguna'))
            ->with('success', 'Data pengguna berhasil dihapus.');
    }

    public function lihatKtp($namaFile)
    {
        $namaFileAman = basename((string) $namaFile);

        if ($namaFileAman === '') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Nama file tidak valid.');
        }

        $path = WRITEPATH . 'uploads/ktp/' . $namaFileAman;

        if (! is_file($path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File KTP tidak ditemukan.');
        }

        $mime = mime_content_type($path) ?: 'application/octet-stream';

        return $this->response
            ->setContentType($mime)
            ->setBody(file_get_contents($path));
    }
    public function verifikasiKtp($id)
    {
        $user = $this->userModel->find($id);

        if (! $user) {
            return redirect()->to(base_url('/pengguna'))->with('error', 'Data pengguna tidak ditemukan.');
        }

        if (empty($user['foto_ktp'])) {
            return redirect()->back()->with('error', 'Pengguna ini belum mengunggah foto KTP.');
        }

        $this->userModel->update($id, [
            'status_ktp'  => 'terverifikasi',
            'catatan_ktp' => null,
        ]);

        return redirect()->to(base_url('pengguna'))
            ->with('success', 'Foto KTP berhasil diverifikasi.');
    }

    public function tolakKtp($id)
    {
        $user = $this->userModel->find($id);

        if (! $user) {
            return redirect()->to(base_url('/pengguna'))->with('error', 'Data pengguna tidak ditemukan.');
        }

        $rules = [
            'alasan_tolak' => 'required|min_length[5]|max_length[255]',
        ];

        $messages = [
            'alasan_tolak' => [
                'required'   => 'Alasan penolakan wajib diisi.',
                'min_length' => 'Alasan terlalu singkat, jelaskan sedikit lebih detail.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->to(base_url('pengguna/ubah/' . $id))
                ->with('errors', $this->validator->getErrors());
        }

        $this->userModel->update($id, [
            'status_ktp'  => 'ditolak',
            'catatan_ktp' => $this->request->getPost('alasan_tolak'),
        ]);

        return redirect()->to(base_url('pengguna'))
            ->with('success', 'Verifikasi KTP ditolak. Pengguna akan diminta mengunggah ulang.');
    }
}
