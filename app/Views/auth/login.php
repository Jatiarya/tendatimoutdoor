<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>

    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/auth.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.css"
        integrity="sha512-kJlvECunwXftkPwyvHbclArO8wszgBGisiLeuDFwNM8ws+wKIw0sv1os3ClWZOcrEB2eRXULYUsm8OVRGJKwGA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>

<body>

    <!-- ── LEFT PANEL ── -->
    <div class="left-panel">
        <svg class="mountain-svg" viewBox="0 0 1000 300" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <path fill="#0d1f0d"
                d="M0,220 L120,100 L240,170 L380,50 L500,130 L620,20 L740,110 L860,60 L1000,140 L1000,300 L0,300 Z" />
            <path fill="#142414"
                d="M0,260 L180,160 L300,210 L440,120 L560,180 L680,100 L800,160 L920,130 L1000,170 L1000,300 L0,300 Z" />
        </svg>

        <div class="left-content">
            <div class="left-badge">⛺ Tendatim Outdoor</div>

            <h1>Selamat<br>Datang <em>Kembali</em></h1>

            <p>Platform pengelolaan penyewaan alat hiking terpercaya. Kelola semua transaksi dengan mudah dan efisien.
            </p>

            <div class="feature-list">
                <div class="feature-item">
                    <div class="feature-check">✓</div>
                    Pantau status penyewaan secara real-time
                </div>
                <div class="feature-item">
                    <div class="feature-check">✓</div>
                    Konfirmasi pembayaran dengan cepat
                </div>
                <div class="feature-item">
                    <div class="feature-check">✓</div>
                    Laporan pendapatan otomatis & terperinci
                </div>
                <div class="feature-item">
                    <div class="feature-check">✓</div>
                    Akses dari mana saja, kapan saja
                </div>
            </div>
        </div>
    </div>

    <!-- ── RIGHT PANEL ── -->
    <div class="right-panel">
        <div class="login-box">

            <!-- Brand -->
            <div class="login-brand">
                <div class="brand-icon"><img src="<?= base_url('img/logo.jpg') ?>" alt="logo"></div>
                <div>
                    <div class="brand-name">Tendatim Outdoor</div>
                    <div class="brand-sub">Sistem Manajemen Sewa</div>
                </div>
            </div>

            <!-- Heading -->
            <div class="login-heading">
                <h2>Cus Masuk Ke Akun</h2>
                <!-- <p>Ayok silahkan masukin data akun kalian biar bisa nyewa</p> -->
            </div>
            <!-- Flash message dari CodeIgniter -->
            <?php if (session()->getFlashdata('error')) : ?>
            <div class="php-alert error">
                ⚠ <?= session()->getFlashdata('error') ?>
            </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')) : ?>
            <div class="php-alert success">
                ✅ <?= session()->getFlashdata('success') ?>
            </div>
            <?php endif; ?>

            <!-- JS Alert Box -->
            <div class="alert-error" id="alertError">
                ⚠ <span id="alertMsg">Username atau password salah.</span>
            </div>

            <!-- Form -->
            <form action="<?= base_url('auth/login') ?>" method="post" id="loginForm" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="role" id="roleInput" value="admin">

                <!-- Email / Username -->
                <div class="form-field">
                    <label for="email" id="label-email">Email atau Username</label>
                    <div class="input-wrap">
                        <span class="input-icon"><i class="ri-mail-line"></i></span>
                        <input type="text" name="email" id="email" placeholder="admin@tendatim.com" autocomplete="email"
                            required>
                    </div>
                    <div class="field-error" id="err-email">⚠ Kolom ini wajib diisi.</div>
                </div>

                <!-- Password -->
                <div class="form-field">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <span class="input-icon"><i class="ri-lock-line"></i></span>
                        <input type="password" name="password" id="password" placeholder="Masukkin password"
                            autocomplete="current-password" required>
                        <button type="button" class="toggle-pw" id="togglePw" onclick="togglePassword()"
                            title="Tampilkan password">
                            <i class="ri-eye-line"></i>
                        </button>
                    </div>
                    <div class="field-error" id="err-password">⚠ Kolom ini wajib diisi.</div>
                </div>

                <!-- Extras -->
                <div class="form-extras">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" value="1">
                        Ingat saya
                    </label>
                    <a href="<?= base_url('auth/forgot') ?>" class="forgot-link">Lupa password?</a>
                </div>
                <div class="divider">atau</div>

                <a href="<?= base_url('auth/google') ?>" class="btn-login" style="background:#fff; color:#374151; border:1.5px solid #ddd8cc; text-decoration:none;">
                    <img src="https://www.google.com/favicon.ico" alt="Google" style="width:18px; height:18px;">
                    Masuk dengan Google
                </a>
                <div class="cf-turnstile" data-sitekey="<?= esc(env('turnstile.siteKey')) ?>" data-theme="light"></div>
                <!-- Submit -->
                <button type="submit" class="btn-login" id="btnLogin">
                    <span id="btn-text">Gas Masuk</span>
                    <div class="spinner" id="spinner"></div>
                </button>
                <a href="<?= base_url('/register') ?>" class="btn-login none">Daftar Akun</a>
                <a href="<?= base_url('/') ?>" class="back-home">← Balik ke beranda</a>

            </form>

            <div class="login-footer">
                &copy; <?= date('Y') ?> Tendatim Outdoor. Hak Cipta Dilindungi.<br>
                Masalah login? Hubungi <a href="mailto:admin@tendatim.com"
                    style="color:var(--pine);">admin@tendatim.com</a>
            </div>

        </div>
    </div>

    <script>
    function togglePassword() {
        const pwInput = document.getElementById('password');
        const toggleBtn = document.getElementById('togglePw');

        if (pwInput.type === 'password') {
            pwInput.type = 'text';
            toggleBtn.innerHTML = '<i class="ri-eye-close-line"></i>';
            toggleBtn.title = 'Sembunyikan password';
        } else {
            pwInput.type = 'password';
            toggleBtn.innerHTML = '<i class="ri-eye-line"></i>';
            toggleBtn.title = 'Tampilkan password';
        }
    }
    </script>
    
</body>

</html>