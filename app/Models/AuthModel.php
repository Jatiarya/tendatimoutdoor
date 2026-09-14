<?php

namespace App\Models;

use CodeIgniter\Model;

class AuthModel extends Model
{
    protected $table            = 'user';
    protected $primaryKey       = 'id_user';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields = ['email', 'username','nomor_wa', 'google_id', 'password', 'level', 'foto_ktp', 'status_ktp', 'catatan_ktp', 'reset_token', 'reset_token_expires'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[50]|is_unique[user.username,id_user,{id_user}]',
        'email'    => 'required|valid_email|is_unique[user.email,id_user,{id_user}]',
        'password' => 'required|min_length[6]',
        'nomor_wa' => 'required|min_length[9]|max_length[15]|regex_match[/^[0-9+]+$/]',
    ];

    protected $validationMessages = [
        'username' => [
            'is_unique' => 'Username sudah dipakai, silahkan pilih username lain.',
        ],
        'email' => [
            'is_unique' => 'Email sudah terdaftar, silahkan login atau gunakan email lain.',
        ],
        'password' => [
            'min_length' => 'Password minimal 6 karakter.',
        ],
        'nomor_wa' => [
            'required' => 'Nomor WhatsApp wajib diisi.',
            'regex_match' => 'Nomor WhatsApp hanya boleh berisi angka(boleh diawali +).',
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['hashPassword'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function updateFotoKtp(int $idUser, string $namaFile): bool
    {
        return $this->update($idUser, ['foto_ktp' => $namaFile]);
    }

    public function findById(int $id_user)
    {
        return $this->find($id_user);
    }

  
    public function updateProfile(int $id_user, array $data)
    {
        return $this->skipValidation(true)->update($id_user, $data);
    }

    public function verifyPassword(int $id_user, string $plainPassword): bool
    {
        $user = $this->find($id_user);

        if (! $user) {
            return false;
        }

        return password_verify($plainPassword, $user['password']);
    }

    public function updatePassword(int $id_user, string $newPassword)
    {
        return $this->skipValidation(true)->update($id_user, ['password' => $newPassword]);
    }
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }

        return $data;
    }

    public function findByEmailOrUsername(string $identifier)
    {
        return $this->where('email', $identifier)
            ->orWhere('username', $identifier)
            ->first();
    }
    public function findByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }

    public function saveResetToken(int $id_user, string $hashedToken, string $expiresAt)
    {
        return $this->db->table($this->table)
            ->where('id_user', $id_user)
            ->update([
                'reset_token'          => $hashedToken,
                'reset_token_expires'  => $expiresAt,
            ]);
    }
    public function findByValidResetToken(string $hashedToken)
    {
        return $this->where('reset_token', $hashedToken)
            ->where('reset_token_expires >=', date('Y-m-d H:i:s'))
            ->first();
    }

    public function resetPasswordAndClearToken(int $id_user, string $plainPassword)
    {
        return $this->skipValidation(true)->update($id_user, [
            'password'             => $plainPassword,
            'reset_token'          => null,
            'reset_token_expires'  => null,
        ]);
    }
        public function findByGoogleId(string $googleId)
    {
        return $this->where('google_id', $googleId)->first();
    }

    /**
     * Login/daftar via Google: kalau email sudah terdaftar (dari daftar manual sebelumnya),
     * tautkan google_id ke akun itu. Kalau belum ada sama sekali, buat akun baru.
     */
    public function findOrCreateByGoogle(string $googleId, string $email, string $nama): array
    {
        $user = $this->findByGoogleId($googleId);
        if ($user) {
            return $user;
        }

        $user = $this->findByEmail($email);
        if ($user) {
            // Akun manual sudah ada dengan email sama -> tautkan google_id-nya
            $this->skipValidation(true)->update($user['id_user'], ['google_id' => $googleId]);
            return $this->find($user['id_user']);
        }

        // ── Belum ada sama sekali -> buat akun baru ──
        // PENTING: kolom `password` di tabel `user` WAJIB nullable di database.
        // Akun yang daftar lewat Google tidak punya password manual (login
        // selalu lewat Google), jadi field ini sengaja dikirim null. Kalau
        // kolom `password` masih NOT NULL di skema tabel, insert() di bawah
        // ini akan gagal dengan SQL error -- cek `php spark db:table user`
        // atau migration awal kalau ini terjadi.
        $usernameBase = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $nama)) ?: 'user';
        $username = $usernameBase . rand(100, 999);

        while ($this->where('username', $username)->first()) {
            $username = $usernameBase . rand(1000, 9999);
        }

        $idUser = $this->skipValidation(true)->insert([
            'username'  => $username,
            'email'     => $email,
            'password'  => null, // tidak ada password manual untuk akun Google
            'google_id' => $googleId,
            'level'     => 'user',
        ], true);

        return $this->find($idUser);
    }
}