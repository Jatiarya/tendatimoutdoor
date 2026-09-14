<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AuthModel;

class ProfileController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new AuthModel();
    }

    public function index()
    {
        $id_user = session()->get('id_user');
        $user = $this->userModel->findById($id_user);

        if (! $user) {
            session()->destroy();
            return redirect()->to(base_url('/login'))
                ->with('error', 'Akun kamu tidak ditemukan. Silakan login ulang.');
        }

        return view('profile', [
            'title' => 'Pengaturan Akun - Tendatim Outdoor',
            'user'  => $user,
        ]);
    }

    public function edit()
    {
        $id_user = session()->get('id_user');
        $user = $this->userModel->findById($id_user);

        if (! $user) {
            session()->destroy();
            return redirect()->to(base_url('/login'))
                ->with('error', 'Akun kamu tidak ditemukan. Silakan login ulang.');
        }

        return view('profileupdate', [
            'title' => 'Edit Akun - Tendatim Outdoor',
            'user'  => $user,
        ]);
    }

    public function indexAdmin()
    {
        $id_user = session()->get('id_user');
        $user = $this->userModel->findById($id_user);

        if (! $user) {
            session()->destroy();
            return redirect()->to(base_url('/login'))
                ->with('error', 'Akun kamu tidak ditemukan. Silakan login ulang.');
        }

        return view('admin/profil', [
            'title' => 'Pengaturan Akun - Admin Tendatim Outdoor',
            'user'  => $user,
        ]);
    }


    public function updateAccount()
    {
        $id_user = session()->get('id_user');
        $user    = $this->userModel->findById($id_user);

        if (! $user) {
            return redirect()->to(base_url('/login'))->with('error', 'Sesi berakhir, silakan login kembali.');
        }

        $rules = [
            'username' => "required|min_length[3]|max_length[50]|is_unique[user.username,id_user,{$id_user}]",
            'email'    => "required|valid_email|is_unique[user.email,id_user,{$id_user}]",
        ];

        $messages = [
            'username' => ['is_unique' => 'Username sudah dipakai, silahkan pilih username lain.'],
            'email'    => ['is_unique' => 'Email sudah dipakai oleh akun lain.'],
        ];

        $currentPassword   = $this->request->getPost('current_password');
        $passwordBaru      = $this->request->getPost('password');
        $passwordConfirm   = $this->request->getPost('password_confirm');
        $inginGantiPassword = ! empty($currentPassword) || ! empty($passwordBaru) || ! empty($passwordConfirm);

        if ($inginGantiPassword) {
            $rules['current_password'] = 'required';
            $rules['password']         = 'required|min_length[6]';
            $rules['password_confirm'] = 'required|matches[password]';
            $messages['password_confirm'] = ['matches' => 'Konfirmasi password baru tidak cocok.'];
        }

        if (! $this->validate($rules, $messages)) {
            return redirect()->to(base_url('profileupdate'))->withInput()->with('errors', $this->validator->getErrors());
        }

        if ($inginGantiPassword && ! $this->userModel->verifyPassword($id_user, $currentPassword)) {
            return redirect()->to(base_url('profileupdate'))->withInput()->with('error', 'Password saat ini yang kamu masukkan salah.');
        }

        $berhasilUpdateProfil = $this->userModel->updateProfile($id_user, [
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
        ]);

        if (! $berhasilUpdateProfil) {
            log_message('error', '[ProfileController::updateAccount] Gagal update profil id_user=' . $id_user . '. Errors: ' . json_encode($this->userModel->errors()));
            return redirect()->to(base_url('profileupdate'))->withInput()->with('error', 'Gagal menyimpan perubahan. Silakan coba lagi.');
        }

        if ($inginGantiPassword) {
            $berhasilUpdatePassword = $this->userModel->updatePassword($id_user, $passwordBaru);

            if (! $berhasilUpdatePassword) {
                log_message('error', '[ProfileController::updateAccount] Gagal update password id_user=' . $id_user . '. Errors: ' . json_encode($this->userModel->errors()));
                return redirect()->to(base_url('profileupdate'))->withInput()->with('error', 'Username/email tersimpan, tapi gagal mengubah password. Silakan coba lagi.');
            }
        }

        session()->set([
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
        ]);

        $pesan = $inginGantiPassword
            ? 'Informasi akun dan password berhasil diperbarui.'
            : 'Informasi akun berhasil diperbarui.';

        return redirect()->to(base_url('/profile'))->with('success', $pesan);
    }


    public function uploadKtp()
    {
        $rules = [
            'foto_ktp' => 'uploaded[foto_ktp]|max_size[foto_ktp,5120]|mime_in[foto_ktp,image/jpg,image/jpeg,image/png,application/pdf]',
        ];

        $messages = [
            'foto_ktp' => [
                'uploaded' => 'Pilih file foto KTP terlebih dahulu.',
                'max_size' => 'Ukuran file maksimal 5 MB.',
                'mime_in'  => 'Format file harus JPG, PNG, atau PDF.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $id_user = session()->get('id_user');
        $userLama = $this->userModel->findById($id_user);

        $file = $this->request->getFile('foto_ktp');
        $namaBaru = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads/ktp', $namaBaru);

        $this->userModel->updateFotoKtp($id_user, $namaBaru);

        if (! empty($userLama['foto_ktp'])) {
            $pathLama = WRITEPATH . 'uploads/ktp/' . $userLama['foto_ktp'];
            if (is_file($pathLama)) {
                @unlink($pathLama);
            }
        }

        return redirect()->to(base_url('/profile'))->with('success', 'Foto KTP berhasil diperbarui.');
    }
}
