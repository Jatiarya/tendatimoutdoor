<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AuthModel;

class AuthController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new AuthModel();
    }


    public function login()
    {
        $title = ['title' => 'Login - Tendatim Outdoor'];

        if (session()->get('isLoggedIn')) {
            return $this->redirectByLevel(session()->get('level'));
        }

        return view('auth/login', $title);
    }

    public function authenticate()
    {
        $throttler = service('throttler');

        if ($throttler->check(md5($this->request->getIPAddress()), 5, MINUTE) === false) {
            return redirect()->back()->with('error', 'Terlalu banyak percobaan login. Coba lagi dalam 1 menit.');
        }

        // ── VERIFIKASI TURNSTILE ──
        $turnstileToken = $this->request->getPost('cf-turnstile-response');
        $turnstile = new \App\Libraries\TurnstileVerifier();

        if (! $turnstile->verifikasi($turnstileToken, $this->request->getIPAddress())) {
            return redirect()->back()->withInput()->with('error', 'Verifikasi keamanan gagal. Silakan coba lagi.');
        }
        $rules = [
            'email'    => 'required',
            'password' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Email/Username dan password wajib diisi.');
        }

        $identifier = trim($this->request->getPost('email'));
        $password   = $this->request->getPost('password');

        $user = $this->userModel->findByEmailOrUsername($identifier);

        if (! $user || ! password_verify($password, $user['password'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Email/Username atau password salah.');
        }

        session()->regenerate();

        session()->set([
            'id_user'    => $user['id_user'],
            'username'   => $user['username'],
            'email'      => $user['email'],
            'level'      => strtolower(trim($user['level'])),
            'isLoggedIn' => true,
        ]);

        $redirectUrl = session()->get('redirect_after_login');
        if ($redirectUrl) {
            session()->remove('redirect_after_login');
            return redirect()->to($redirectUrl)->with('success', 'Login berhasil, selamat datang kembali!');
        }

        return $this->redirectByLevel(session()->get('level'));
    }

    protected function redirectByLevel(string $level)
    {
        if ($level === 'admin') {
            return redirect()->to(base_url('/dashboard'))
                ->with('success', 'Login berhasil sebagai admin.');
        }

        return redirect()->to(base_url('/'))
            ->with('success', 'Login berhasil.');
    }

    public function register()
    {
        if (session()->get('isLoggedIn')) {
            return $this->redirectByLevel(session()->get('level'));
        }

        return view('auth/register');
    }

    public function simpan()
    {
        $turnstileToken = $this->request->getPost('cf-turnstile-response');
        $turnstile = new \App\Libraries\TurnstileVerifier();

        if (! $turnstile->verifikasi($turnstileToken, $this->request->getIPAddress())) {
            return redirect()->back()->withInput()->with('error', 'Verifikasi keamanan gagal. Silakan coba lagi.');
        }
        $rules = [
            'username'         => 'required|min_length[3]|max_length[50]|is_unique[user.username]',
            'email'            => 'required|valid_email|is_unique[user.email]',
            'password'         => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
            'nomor_wa'  => 'required|min_length[9]|max_length[15]|regex_match[/^[0-9+]+$/]',
        ];

        $messages = [
            'username' => ['is_unique' => 'Username sudah dipakai, silahkan pilih username lain.'],
            'email'    => ['is_unique' => 'Email sudah terdaftar, silahkan login.'],
            'password_confirm' => ['matches' => 'Konfirmasi password tidak cocok dengan password.'],
            'nomor_wa' => [
                'required'    => 'Nomor WhatsApp wajib diisi.',
                'regex_match' => 'Nomor WhatsApp hanya boleh berisi angka.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->userModel->insert([
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'nomor_wa' => $this->request->getPost('nomor_wa'),
            'level'    => 'user',
        ]);

        return redirect()->to(base_url('/login'))
            ->with('success', 'Pendaftaran berhasil, silahkan login dengan akun baru kamu.');
    }

    public function forgotPassword()
    {
        if (session()->get('isLoggedIn')) {
            return $this->redirectByLevel(session()->get('level'));
        }

        return view('auth/forgot', ['title' => 'Lupa Password - Tendatim Outdoor']);
    }

    public function processForgot()
    {
        $rules = ['email' => 'required|valid_email'];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Masukkan alamat email yang valid.');
        }

        $email = trim($this->request->getPost('email'));
        $user  = $this->userModel->findByEmail($email);


        $genericMessage = 'Kalau email tersebut terdaftar, link reset password sudah dikirim. Silakan cek inbox (dan folder spam) kamu.';

        if ($user) {
            $rawToken    = bin2hex(random_bytes(32));
            $hashedToken = hash('sha256', $rawToken);
            $expiresAt   = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $this->userModel->saveResetToken($user['id_user'], $hashedToken, $expiresAt);

            $resetLink = base_url('auth/reset/' . $rawToken);

            $emailService = \Config\Services::email();
            $emailService->setTo($user['email']);
            $emailService->setFrom(env('email.fromEmail'), env('email.fromName'));
            $emailService->setSubject('Reset Password - Tendatim Outdoor');
            $emailService->setMessage(
                "Halo {$user['username']},\n\n" .
                    "Kami menerima permintaan reset password untuk akun kamu.\n" .
                    "Klik link berikut untuk mengatur password baru (link berlaku 1 jam):\n\n" .
                    "{$resetLink}\n\n" .
                    "Kalau kamu tidak meminta ini, abaikan saja email ini — password kamu tetap aman."
            );

            if (! $emailService->send()) {
                log_message('error', 'Gagal kirim email reset password: ' . $emailService->printDebugger(['headers']));
            }
        }

        return redirect()->to(base_url('/login'))->with('success', $genericMessage);
    }


    public function resetPassword(string $token)
    {
        if (session()->get('isLoggedIn')) {
            return $this->redirectByLevel(session()->get('level'));
        }

        $hashedToken = hash('sha256', $token);
        $user = $this->userModel->findByValidResetToken($hashedToken);

        if (! $user) {
            return redirect()->to(base_url('/auth/forgot'))
                ->with('error', 'Link reset password tidak valid atau sudah kedaluwarsa. Silakan minta link baru.');
        }

        return view('auth/reset', [
            'title' => 'Reset Password - Tendatim Outdoor',
            'token' => $token,
        ]);
    }

    public function processReset(string $token)
    {
        $rules = [
            'password'         => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $hashedToken = hash('sha256', $token);
        $user = $this->userModel->findByValidResetToken($hashedToken);

        if (! $user) {
            return redirect()->to(base_url('/auth/forgot'))
                ->with('error', 'Link reset password tidak valid atau sudah kedaluwarsa. Silakan minta link baru.');
        }

        $this->userModel->resetPasswordAndClearToken($user['id_user'], $this->request->getPost('password'));

        return redirect()->to(base_url('/login'))
            ->with('success', 'Password berhasil diubah. Silakan login dengan password baru kamu.');
    }
        public function redirectGoogle()
    {
        $provider = $this->googleProvider();

        $authUrl = $provider->getAuthorizationUrl([
            'scope' => ['email', 'profile'],
        ]);

        session()->set('oauth2state', $provider->getState());

        return redirect()->to($authUrl);
    }

    public function callbackGoogle()
    {
        $provider = $this->googleProvider();

        $state = $this->request->getGet('state');

        if (empty($state) || $state !== session()->get('oauth2state')) {
            session()->remove('oauth2state');
            return redirect()->to('/login')->with('error', 'Verifikasi login Google gagal (state tidak cocok). Silakan coba lagi.');
        }

        try {
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $this->request->getGet('code'),
            ]);

            $googleUser = $provider->getResourceOwner($token);
            $data = $googleUser->toArray();

            $email = $data['email'] ?? null;
            $googleId = $data['sub'] ?? null;
            $nama = $data['name'] ?? explode('@', $email)[0];

            if (! $email || ! $googleId) {
                return redirect()->to('/login')->with('error', 'Gagal mengambil data akun Google.');
            }

            $authModel = new \App\Models\AuthModel();
            $user = $authModel->findOrCreateByGoogle($googleId, $email, $nama);

            // Set session sama persis seperti login manual biasa
            session()->set([
                'isLoggedIn' => true,
                'id_user'    => $user['id_user'],
                'username'   => $user['username'],
                'email'      => $user['email'],
                'level'      => $user['level'],
            ]);

            $redirectTujuan = $user['level'] === 'admin' ? '/dashboard' : '/';

            return redirect()->to($redirectTujuan)->with('success', 'Berhasil login dengan Google.');

        } catch (\Throwable $e) {
            log_message('error', '[AuthController::callbackGoogle] ' . $e->getMessage());
            return redirect()->to('/login')->with('error', 'Terjadi kesalahan saat login dengan Google.');
        }
    }

    private function googleProvider(): \League\OAuth2\Client\Provider\Google
    {
        return new \League\OAuth2\Client\Provider\Google([
            'clientId'     => env('google.clientId'),
            'clientSecret' => env('google.clientSecret'),
            'redirectUri'  => env('google.redirectUri'),
        ]);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('/login'))->with('success', 'Kamu berhasil logout.');
    }
}