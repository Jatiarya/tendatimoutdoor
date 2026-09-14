<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filter ini yang menjawab skenario:
 * "user boleh lihat-lihat produk tanpa login, tapi begitu mau sewa,
 *  harus login dulu."
 *
 * Cara pakai: pasang filter 'auth' di route yang butuh login,
 * misalnya route sewa produk / riwayat sewa. Lihat contoh di
 * routes_dan_filters.md.
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('isLoggedIn')) {
            // Simpan URL yang ingin dituju user, supaya setelah login
            // dia otomatis dibalikin kesitu lagi (bukan cuma ke homepage)
            session()->set('redirect_after_login', current_url());

            return redirect()->to(base_url('/login'))
                ->with('error', 'Kamu harus login terlebih dahulu untuk bisa menyewa produk.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // tidak dipakai
    }
}