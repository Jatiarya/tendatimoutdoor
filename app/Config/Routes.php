<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ── PUBLIK ──
$routes->get('/', 'MainController::index');
$routes->get('/katalog', 'MainController::produk');
$routes->get('/tentang', 'MainController::about');
$routes->get('/kontak', 'MainController::kontak');
$routes->post('/kontak/kirim', 'MainController::kirimPesanKontak');

$routes->get('login', 'AuthController::login');
$routes->post('auth/login', 'AuthController::authenticate');
$routes->get('register', 'AuthController::register');
$routes->post('register/simpan', 'AuthController::simpan');
$routes->get('logout', 'AuthController::logout');
$routes->get('auth/forgot', 'AuthController::forgotPassword');
$routes->post('auth/forgot', 'AuthController::processForgot');
$routes->get('auth/reset/(:any)', 'AuthController::resetPassword/$1');
$routes->post('auth/reset/(:any)', 'AuthController::processReset/$1');
$routes->get('auth/google', 'AuthController::redirectGoogle');
$routes->get('auth/google/callback', 'AuthController::callbackGoogle');

// Webhook Midtrans -> publik, diverifikasi via signature, dikecualikan dari CSRF di Filters.php
$routes->post('midtrans/callback', 'RentalController::callback');

// ── WAJIB LOGIN USER BIASA (filter 'auth') ──
$routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->get('sewa/(:num)', 'RentalController::create/$1');
    $routes->post('sewa/store', 'RentalController::proses');
    $routes->post('sewa/cek-voucher', 'RentalController::cekVoucher');
    $routes->get('riwayatsewa', 'RentalController::history');
    $routes->get('pembayaran/(:num)', 'RentalController::pembayaran/$1');
    $routes->get('pembayaran/status/(:num)', 'RentalController::statusCheck/$1');
    $routes->get('pembayaran/status-transaksi/(:any)', 'RentalController::statusCheckTransaksi/$1');
    $routes->post('keranjang/tambah', 'CartController::tambah');
    $routes->get('keranjang', 'CartController::index');
    $routes->post('keranjang/hapus', 'CartController::hapus');
    $routes->post('keranjang/checkout', 'CartController::checkout');
    $routes->get('pembayaran-transaksi/(:any)', 'RentalController::pembayaranTransaksi/$1');
    // profile
    $routes->get('/profile', 'ProfileController::index');
    $routes->get('/profileupdate', 'ProfileController::edit');
    $routes->post('profile/update', 'ProfileController::updateAccount');
    $routes->post('profile/update-password', 'ProfileController::updatePasswordAccount');
    $routes->post('profile/upload-ktp', 'ProfileController::uploadKtp');
});

// ── WAJIB LOGIN SEBAGAI ADMIN (filter 'admin') ──
// PENTING: SEMUA route halaman admin WAJIB berada di dalam grup ini.
$routes->group('', ['filter' => 'admin'], function ($routes) {

    $routes->get('/dashboard', 'DashboardController::dashboard');
    $routes->get('dashboard/chart-data', 'DashboardController::chartData');
    $routes->get('admin/profil', 'ProfileController::indexAdmin');
    $routes->get('galeritentang', 'GaleriController::index');
    $routes->get('galeritentang/tambah', 'GaleriController::tambah');
    $routes->post('galeritentang/simpan', 'GaleriController::simpan');
    $routes->get('galeritentang/ubah/(:num)', 'GaleriController::ubah/$1');
    $routes->post('galeritentang/update/(:num)', 'GaleriController::update/$1');
    $routes->post('galeritentang/hapus/(:num)', 'GaleriController::hapus/$1'); // GET ->
    $routes->get('notifikasi/unread', 'NotifikasiController::unread');
    $routes->post('notifikasi/read/(:num)', 'NotifikasiController::read/$1');
    $routes->post('notifikasi/read-all', 'NotifikasiController::readAll');
    // Perlengkapan
    $routes->get('/perlengkapan', 'PerlengkapanController::index');
    $routes->get('/perlengkapan/tambah', 'PerlengkapanController::tambah');
    $routes->post('/perlengkapan/simpan', 'PerlengkapanController::simpan');
    $routes->get('/perlengkapan/ubah/(:num)', 'PerlengkapanController::ubah/$1');
    $routes->post('/perlengkapan/update/(:num)', 'PerlengkapanController::update/$1');
    $routes->post('/perlengkapan/hapus/(:num)', 'PerlengkapanController::hapus/$1'); // GET -> POST

    // Kategori
    $routes->get('/kategori', 'KategoriController::index');
    $routes->get('/kategori/tambah', 'KategoriController::tambah');
    $routes->post('/kategori/simpan', 'KategoriController::simpan');
    $routes->get('/kategori/ubah/(:num)', 'KategoriController::ubah/$1');
    $routes->post('/kategori/update/(:num)', 'KategoriController::update/$1');
    $routes->post('/kategori/hapus/(:num)', 'KategoriController::hapus/$1'); // GET -> POST

    // Pengguna
    $routes->get('/pengguna', 'UseradminController::index');
    $routes->get('/pengguna/ubah/(:num)', 'UseradminController::ubah/$1');
    $routes->post('/pengguna/update/(:num)', 'UseradminController::update/$1'); // GET -> POST
    $routes->post('/pengguna/hapus/(:num)', 'UseradminController::hapus/$1');   // GET -> POST
    $routes->get('/pengguna/ktp/(:any)', 'UseradminController::lihatKtp/$1');
    $routes->post('/pengguna/verifikasi-ktp/(:num)', 'UseradminController::verifikasiKtp/$1');
    $routes->post('/pengguna/tolak-ktp/(:num)', 'UseradminController::tolakKtp/$1');

    // Penyewaan
    $routes->get('/penyewaan', 'RentaladminController::index');
    $routes->get('/penyewaan/ubah/(:num)', 'RentaladminController::ubah/$1');
    $routes->post('/penyewaan/update/(:num)', 'RentaladminController::update/$1');
    $routes->post('/penyewaan/hapus/(:any)', 'RentaladminController::hapus/$1');           // GET -> POST
    $routes->post('/penyewaan/tolak/(:any)', 'RentaladminController::tolak/$1');           // GET -> POST
    $routes->post('/penyewaan/dikembalikan/(:any)', 'RentaladminController::dikembalikan/$1'); // GET -> POST
    $routes->get('/penyewaan/ktp/(:num)', 'RentaladminController::lihatKtp/$1');
    $routes->get('/penyewaan/ubah-transaksi/(:any)', 'RentaladminController::ubahTransaksi/$1');
    $routes->post('/penyewaan/update-transaksi/(:any)', 'RentaladminController::updateTransaksi/$1');

    // diskon
    $routes->get('/diskon', 'DiskonadminController::index');
    $routes->get('/diskon/tambah', 'DiskonadminController::tambah');
    $routes->post('/diskon/simpan', 'DiskonadminController::simpan');
    $routes->get('/diskon/ubah/(:num)', 'DiskonadminController::ubah/$1');
    $routes->post('/diskon/update/(:num)', 'DiskonadminController::update/$1');
    $routes->post('/diskon/hapus/(:num)', 'DiskonadminController::hapus/$1');

    // Pembayaran
    $routes->get('/pembayaran', 'PembayaranadminController::index');
    $routes->post('/pembayaran/konfirmasi/(:num)', 'PembayaranadminController::konfirmasi/$1'); // GET -> POST
    $routes->post('/pembayaran/tolak/(:num)', 'PembayaranadminController::tolak/$1');            // GET -> POST

    // Laporan
    $routes->get('/laporan', 'LaporanadminController::index');
    $routes->get('/laporan/export', 'LaporanadminController::export');
});
