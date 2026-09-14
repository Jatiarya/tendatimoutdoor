<?php

namespace App\Libraries;

/**
 * TurnstileVerifier — verifikasi token dari widget Cloudflare Turnstile
 * ke endpoint resmi Cloudflare (siteverify).
 *
 * Cara pakai (sudah diterapkan di AuthController):
 *   $token = $this->request->getPost('cf-turnstile-response');
 *   $turnstile = new \App\Libraries\TurnstileVerifier();
 *   if (! $turnstile->verifikasi($token, $this->request->getIPAddress())) {
 *       // tolak request
 *   }
 *
 * Secret Key diambil dari .env: turnstile.secretKey = ISI_SECRET_KEY
 */
class TurnstileVerifier
{
    private const ENDPOINT = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    /**
     * Verifikasi token Turnstile.
     *
     * @param string|null $token Nilai dari field 'cf-turnstile-response' pada form
     * @param string|null $ip    IP address pengunjung (opsional, tapi disarankan Cloudflare)
     */
    public function verifikasi(?string $token, ?string $ip = null): bool
    {
        // Token kosong = widget belum diselesaikan user / JS Turnstile tidak jalan -> tolak
        if (empty($token)) {
            log_message('warning', '[TurnstileVerifier] Token kosong, request ditolak.');
            return false;
        }

        $secretKey = env('turnstile.secretKey');

        if (empty($secretKey)) {
            // Secret key belum diisi di .env -> JANGAN loloskan diam-diam,
            // supaya kesalahan config langsung ketahuan saat testing,
            // bukan malah bikin form auth "bocor" tanpa proteksi captcha.
            log_message('critical', '[TurnstileVerifier] turnstile.secretKey belum diisi di .env!');
            return false;
        }

        try {
            $client = \Config\Services::curlrequest();

            $payload = [
                'secret'   => $secretKey,
                'response' => $token,
            ];

            if (! empty($ip)) {
                $payload['remoteip'] = $ip;
            }

            $response = $client->post(self::ENDPOINT, [
                'form_params' => $payload,
                'http_errors' => false,
                'timeout'     => 10,
            ]);

            $body = json_decode($response->getBody(), true);

            if (! is_array($body)) {
                log_message('error', '[TurnstileVerifier] Response tidak valid dari Cloudflare: ' . $response->getBody());
                return false;
            }

            if (empty($body['success'])) {
                log_message('warning', '[TurnstileVerifier] Verifikasi gagal. Error codes: ' . json_encode($body['error-codes'] ?? []));
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            // Kalau Cloudflare down / network error -> TOLAK (fail closed),
            // bukan diloloskan -- captcha yang gagal-buka justru lebih
            // berbahaya daripada form yang sementara tidak bisa dipakai.
            log_message('error', '[TurnstileVerifier] Exception: ' . $e->getMessage());
            return false;
        }
    }
}