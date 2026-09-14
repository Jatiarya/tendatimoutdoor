<?php
// app/Libraries/Midtrans.php
namespace App\Libraries;

use Config\Midtrans as MidtransConfig;

class Midtrans
{
    protected MidtransConfig $config;

    public function __construct()
    {
        $this->config = new MidtransConfig();
    }

    /**
     * Buat transaksi Snap, return array ['token' => ..., 'redirect_url' => ...]
     * atau null kalau gagal.
     */
    public function createTransaction(string $orderId, int $grossAmount, array $customer = []): ?array
    {
        $payload = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => $customer,
            'credit_card' => [
                'secure' => true,
            ],
        ];

        $auth = base64_encode($this->config->serverKey . ':');

        $ch = curl_init($this->config->snapUrl());
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Basic ' . $auth,
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($error || $httpCode >= 300) {
            log_message('error', 'Midtrans createTransaction gagal: ' . $error . ' | HTTP ' . $httpCode . ' | ' . $response);
            return null;
        }

        $data = json_decode($response, true);

        if (empty($data['token'])) {
            log_message('error', 'Midtrans response tidak ada token: ' . $response);
            return null;
        }

        return [
            'token'        => $data['token'],
            'redirect_url' => $data['redirect_url'] ?? null,
        ];
    }

    /**
     * Verifikasi signature notifikasi callback dari Midtrans.
     * Cegah orang lain kirim callback palsu.
     */
    public function verifySignature(array $notif): bool
    {
        $orderId    = $notif['order_id'] ?? '';
        $statusCode = $notif['status_code'] ?? '';
        $grossAmount = $notif['gross_amount'] ?? '';
        $signatureKey = $notif['signature_key'] ?? '';

        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $this->config->serverKey);

        return hash_equals($expected, $signatureKey);
    }
}