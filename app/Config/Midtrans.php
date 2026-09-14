<?php
// app/Config/Midtrans.php
namespace Config;

use CodeIgniter\Config\BaseConfig;

class Midtrans extends BaseConfig
{
    public string $serverKey = ''; // diisi dari .env, jangan hardcode
    public string $clientKey = '';
    public bool $isProduction = false;

    public function __construct()
    {
        parent::__construct();
        $this->serverKey    = env('midtrans.serverKey', '');
        $this->clientKey    = env('midtrans.clientKey', '');
        $this->isProduction = env('midtrans.isProduction', false);
    }

    public function snapUrl(): string
    {
        return $this->isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }
}