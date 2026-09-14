<?= $this->extend('layout/template'); ?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('css/public.css') ?>">

<!-- ── PAGE HEADER ── -->
<div class="page-header">
    <div class="page-header-inner">
        <div class="breadcrumb">
            <a href="/">Beranda</a><span>/</span>
            <a href="<?= base_url('profile') ?>">Pengaturan Akun</a><span>/</span>
            Edit
        </div>
        <h1>Edit Informasi Akun</h1>
        <p>Perbarui username, email, dan password kamu</p>
    </div>
</div>

<!-- ── FLASH MESSAGE ── -->
<?php if (session()->getFlashdata('error')) : ?>
<div class="php-alert error text-center bg-red-500 text-white"
    style="max-width:650px;margin:16px auto 0; border-radius: 10px; display:flex; align-items:center; justify-content:center;">
    ⚠ <?= esc(session()->getFlashdata('error')) ?>
</div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')) : ?>
<div class="php-alert error"
    style="max-width:650px;margin:16px auto 0; border-radius: 10px; display:flex; align-items:center; justify-content:center;">
    <ul style="margin:0; padding-left:18px;">
        <?php foreach (session()->getFlashdata('errors') as $err) : ?>
        <li>⚠ <?= esc($err) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<div class="sewa-wrapper" style="grid-template-columns: 1fr; max-width:900px; margin:0 auto;">
    <div class="sewa-form-col">

        <a href="<?= base_url('profile') ?>" class="btn-primary">
            <i class="ri-arrow-left-line"></i> Kembali ke Profil
        </a>

        <div class="form-card">
            <div class="form-card-header">
                <div class="step-badge"><i class="ri-user-line"></i></div>
                <div>
                    <h2>Informasi Akun</h2>
                    <p>Perbarui username, email, dan password kamu</p>
                </div>
            </div>

            <form action="<?= base_url('profile/update') ?>" method="post" class="profile-form" id="formAkun"
                novalidate>
                <?= csrf_field() ?>

                <div class="field-group">
                    <label for="username">Username <span class="req">*</span></label>
                    <input type="text" id="username" name="username" value="<?= old('username', $user['username']) ?>"
                        required minlength="3" maxlength="50">
                </div>

                <div class="field-group">
                    <label for="email">Email <span class="req">*</span></label>
                    <input type="email" id="email" name="email" value="<?= old('email', $user['email']) ?>" required>
                </div>
                <div class="field-group">
                    <label for="nomor_wa">Nomor WA <span class="req">*</span></label>
                    <input type="text" id="nomor_wa" name="nomor_wa" value="<?= old('nomor_wa', $user['nomor_wa']) ?>"
                        required>
                </div>

                <hr class="field-divider">

                <p class="field-section-title">
                    Ganti Password
                    <small>(opsional, kosongkan semua kalau tidak ingin mengubah password)</small>
                </p>

                <div class="field-group">
                    <label for="current_password">Password Saat Ini</label>
                    <input type="password" id="current_password" name="current_password"
                        autocomplete="current-password">
                </div>

                <div class="field-group">
                    <label for="password">Password Baru</label>
                    <input type="password" id="password" name="password" autocomplete="new-password" minlength="6">
                </div>

                <div class="field-group">
                    <label for="password_confirm">Konfirmasi Password Baru</label>
                    <input type="password" id="password_confirm" name="password_confirm" autocomplete="new-password"
                        minlength="6">
                </div>

                <div id="passwordClientError" class="php-alert error" style="display:none; margin-bottom:12px;"></div>

                <div style="display:flex; gap:10px;">
                    <button type="submit" class="btn-submit" style="width:auto; padding:12px 28px;">
                        <i class="ri-save-line"></i> Simpan Perubahan
                    </button>
                    <a href="<?= base_url('profile') ?>" class="btn-secondary"
                        style="padding:12px 28px; display:inline-flex; align-items:center;">
                        Batal
                    </a>
                </div>
            </form>
        </div>

    </div>
</div>

<style>
.field-divider {
    margin: 20px 0 16px;
    border: none;
    border-top: 1px solid #e5e7eb;
}

.field-section-title {
    font-weight: 600;
    margin-bottom: 12px;
    font-size: 0.95rem;
}

.field-section-title small {
    font-weight: 400;
    color: var(--stone, #999);
    font-size: 0.8rem;
    display: block;
    margin-top: 2px;
}
</style>

<script>
/* ── Validasi ringan di client: kalau salah satu field password diisi,
       ketiganya wajib diisi & password baru harus cocok dengan konfirmasi.
       Validasi SEBENARNYA tetap harus ada di server (client-side gampang dilewati). ── */
document.getElementById('formAkun').addEventListener('submit', function(e) {
    const currentPassword = document.getElementById('current_password').value.trim();
    const password = document.getElementById('password').value.trim();
    const passwordConfirm = document.getElementById('password_confirm').value.trim();
    const errorBox = document.getElementById('passwordClientError');

    const adaSalahSatuDiisi = currentPassword || password || passwordConfirm;

    if (!adaSalahSatuDiisi) {
        errorBox.style.display = 'none';
        return; // user tidak ganti password, lanjut submit normal
    }

    if (!currentPassword || !password || !passwordConfirm) {
        e.preventDefault();
        errorBox.textContent =
            '⚠ Untuk ganti password, isi Password Saat Ini, Password Baru, dan Konfirmasinya.';
        errorBox.style.display = 'block';
        return;
    }

    if (password.length < 6) {
        e.preventDefault();
        errorBox.textContent = '⚠ Password baru minimal 6 karakter.';
        errorBox.style.display = 'block';
        return;
    }

    if (password !== passwordConfirm) {
        e.preventDefault();
        errorBox.textContent = '⚠ Konfirmasi password baru tidak cocok.';
        errorBox.style.display = 'block';
        return;
    }

    errorBox.style.display = 'none';
});
</script>

<?= $this->endSection() ?>