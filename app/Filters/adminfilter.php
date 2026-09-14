<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Melindungi halaman/route admin (contoh: admin/dashboard, data penyewaan admin).
 * Kalau level user bukan 'admin', dia dilempar balik ke halaman utama.
 *
 * Inilah yang membuat skenario "akun user diubah levelnya jadi admin lewat DB
 * -> saat login lagi diarahkan ke admin/dashboard" bekerja: begitu level di DB
 * berubah, session baru yang dibuat saat login berikutnya otomatis membawa
 * level 'admin', dan filter ini yang mengizinkan dia masuk ke area admin.
 */
class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(base_url('/login'));
        }

        if (session()->get('level') !== 'admin') {
            return redirect()->to(base_url('/'))
                ->with('error', 'Kamu tidak punya akses ke halaman admin.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // tidak dipakai
    }
}