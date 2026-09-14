<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Fonnte extends BaseConfig
{
    /**
     * Token device dari dashboard Fonnte (fonnte.com > Device > Token).
     * Diisi lewat .env: fonnte.token = xxxxxxxxxxxxx
     */
    public string $token = '';

    /**
     * Nomor WA admin tujuan notifikasi, format: 08xx ATAU 62xx (dua-duanya
     * diterima Fonnte, tidak seperti Meta yang strict harus 62xx).
     * Diisi lewat .env: fonnte.adminNumber = 081234567890
     */
    public string $adminNumber = '';
}