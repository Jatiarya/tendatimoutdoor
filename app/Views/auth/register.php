<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Tendatim Outdoor</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../css/auth.css">

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.css"
        integrity="sha512-kJlvECunwXftkPwyvHbclArO8wszgBGisiLeuDFwNM8ws+wKIw0sv1os3ClWZOcrEB2eRXULYUsm8OVRGJKwGA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>

<body>

    <div class="left-panel">
        <svg class="mountain-svg" viewBox="0 0 1000 300" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <path fill="#0d1f0d"
                d="M0,220 L120,100 L240,170 L380,50 L500,130 L620,20 L740,110 L860,60 L1000,140 L1000,300 L0,300 Z" />
            <path fill="#142414"
                d="M0,260 L180,160 L300,210 L440,120 L560,180 L680,100 L800,160 L920,130 L1000,170 L1000,300 L0,300 Z" />
        </svg>

        <div class="left-content">
            <div class="left-badge">⛺ Tendatim Outdoor</div>
            <h1>Gabung<br>Bareng <em>Kami</em></h1>
            <p>Daftarin akun kalian, biar makin akrab kita</p>
        </div>
    </div>

    <div class="right-panel">
        <div class="login-box">

            <div class="login-brand">
                <div class="brand-icon"><img src="img/logo.jpg" alt="logo"></div>
                <div>
                    <div class="brand-name">Tendatim Outdoor</div>
                    <div class="brand-sub">Sistem Manajemen Sewa</div>
                </div>
            </div>

            <div class="login-heading">
                <h2>Buat Akun Baru</h2>
                <!-- <p>Jangan lupa isi datanya yang bener, inget dan simpan data kalian baik2 ya biar tetep bisa login</p> -->
            </div>

            <?php if (session()->getFlashdata('errors')) : ?>
                <div class="php-alert error">
                    <ul style="margin:0; padding-left:18px;">
                        <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                            <li>⚠ <?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')) : ?>
                <div class="php-alert error">⚠ <?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <form action="<?= base_url('register/simpan') ?>" method="post" novalidate>
                <?= csrf_field() ?>

                <div class="form-field">
                    <label for="username">Username</label>
                    <div class="input-wrap">
                        <span class="input-icon"><i class="ri-user-line"></i></span>
                        <input type="text" name="username" id="username" placeholder="Masukkan username"
                            value="<?= old('username') ?>" required>
                    </div>
                </div>

                <div class="form-field">
                    <label for="email">Email</label>
                    <div class="input-wrap">
                        <span class="input-icon"><i class="ri-mail-line"></i></span>
                        <input type="email" name="email" id="email" placeholder="nama@email.com"
                            value="<?= old('email') ?>" required>
                    </div>
                </div>
                <div class="form-field">
                    <label for="nomor_wa">Nomor WhatsApp</label>
                    <div class="input-wrap">
                        <span class="input-icon"><i class="ri-whatsapp-line"></i></span>
                        <input type="text" name="nomor_wa" id="nomor_wa" placeholder="08xxxxxxxxxx" autocomplete="tel"
                            required>
                    </div>
                    <div class="field-error" id="err-nomor_wa">⚠ Nomor WhatsApp wajib diisi.</div>
                </div>
                <div class="form-field">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <span class="input-icon"><i class="ri-lock-line"></i></span>
                        <input type="password" name="password" id="password" placeholder="Masukkan password" required>
                        <button type="button" class="toggle-pw" id="togglePassword"
                            onclick="togglePasswordField('password', 'togglePassword')" title="Tampilkan password">
                            <i class="ri-eye-line"></i>
                        </button>
                    </div>
                </div>

                <div class="form-field">
                    <label for="password_confirm">Konfirmasi Password</label>
                    <div class="input-wrap">
                        <span class="input-icon"><i class="ri-lock-line"></i></span>
                        <input type="password" name="password_confirm" id="password_confirm"
                            placeholder="Ulangi password" required>
                        <button type="button" class="toggle-pw" id="togglePasswordConfirm"
                            onclick="togglePasswordField('password_confirm', 'togglePasswordConfirm')"
                            title="Tampilkan password">
                            <i class="ri-eye-line"></i>
                        </button>
                    </div>
                </div>
                <div class="divider">atau</div>

                <a href="<?= base_url('auth/google') ?>" class="btn-login" style="background:#fff; color:#374151; border:1.5px solid #ddd8cc; text-decoration:none;">
                    <img src="https://www.google.com/favicon.ico" alt="Google" style="width:18px; height:18px;">
                    Masuk dengan Google
                </a>
                <div class="cf-turnstile mx-auto" data-sitekey="<?= esc(env('turnstile.siteKey')) ?>" data-theme="light"></div>
                <button type="submit" class="btn-login">Gas Daftar</button>

                <a href="<?= base_url('/login') ?>" class="btn-login">Masuk
                </a>
            </form>

            <div class="login-footer">
                &copy; <?= date('Y') ?> Tendatim Outdoor. Hak Cipta Dilindungi.
            </div>

        </div>
    </div>

    <script>
        function togglePasswordField(inputId, btnId) {
            const input = document.getElementById(inputId);
            const btn = document.getElementById(btnId);

            if (input.type === 'password') {
                input.type = 'text';
                btn.innerHTML = '<i class="ri-eye-close-line"></i>';
                btn.title = 'Sembunyikan password';
            } else {
                input.type = 'password';
                btn.innerHTML = '<i class="ri-eye-line"></i>';
                btn.title = 'Tampilkan password';
            }
        }
    </script>

</body>

</html>