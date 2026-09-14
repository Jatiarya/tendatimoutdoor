<title><?= $title ?></title>
<link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500&display=swap"
    rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('css/auth.css') ?>">

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
            <h1>Lupa <em>Password?</em></h1>
            <p>Tenang, masukin email yang terdaftar dan kami kirimkan link untuk atur ulang password kamu.</p>
        </div>
    </div>

    <div class="right-panel">
        <div class="login-box">
            <div class="login-brand">
                <div class="brand-icon" style="border-radius: 50%;">
                    <img src="/img/logo.jpg" alt="logo" style="width: 40px; height:40px; border-radius: 50%;">
                </div>
                <div>
                    <div class="brand-name">Tendatim Outdoor</div>
                    <div class="brand-sub">Sistem Manajemen Sewa</div>
                </div>
            </div>

            <div class="login-heading">
                <h2>Reset Password</h2>
                <p>Masukin email akun kamu, nanti kami kirimin link reset-nya</p>
            </div>

            <?php if (session()->getFlashdata('error')) : ?>
            <div class="php-alert error">⚠ <?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')) : ?>
            <div class="php-alert success">✅ <?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>

            <form action="<?= base_url('auth/forgot') ?>" method="post" novalidate>
                <?= csrf_field() ?>

                <div class="form-field">
                    <label for="email">Email Terdaftar</label>
                    <div class="input-wrap">
                        <span class="input-icon">✉</span>
                        <input type="email" name="email" id="email" placeholder="email@kamu.com"
                            value="<?= old('email') ?>" autocomplete="email" required>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <span>Kirim Link Reset</span>
                </button>

                <a href="<?= base_url('/login') ?>" class="back-home">← Balik ke halaman login</a>
            </form>

            <div class="login-footer">
                &copy; <?= date('Y') ?> Tendatim Outdoor. Hak Cipta Dilindungi.
            </div>
        </div>
    </div>
</body>