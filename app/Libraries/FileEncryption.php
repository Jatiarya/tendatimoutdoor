<?php

namespace App\Libraries;

class FileEncryption
{
    private string $key;

    public function __construct()
    {
        // WAJIB disimpan di .env, JANGAN hardcode. Generate sekali pakai:
        // php -r "echo bin2hex(random_bytes(32));"
        $keyHex = env('encryption.fileKey');

        if (empty($keyHex)) {
            throw new \RuntimeException('encryption.fileKey belum diset di .env');
        }

        $this->key = hex2bin($keyHex);
    }

    public function encryptAndSave(string $sourceTmpPath, string $destPath): bool
    {
        $data = file_get_contents($sourceTmpPath);
        if ($data === false) {
            return false;
        }

        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $this->key, OPENSSL_RAW_DATA, $iv);

        if ($encrypted === false) {
            return false;
        }

        // Simpan IV di depan file terenkripsi supaya bisa dipakai lagi saat dekripsi
        return file_put_contents($destPath, $iv . $encrypted) !== false;
    }

    public function decrypt(string $encryptedPath): ?string
    {
        $raw = file_get_contents($encryptedPath);
        if ($raw === false || strlen($raw) < 16) {
            return null;
        }

        $iv = substr($raw, 0, 16);
        $encrypted = substr($raw, 16);

        $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', $this->key, OPENSSL_RAW_DATA, $iv);

        return $decrypted === false ? null : $decrypted;
    }
}
