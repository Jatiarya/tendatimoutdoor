<?= $this->extend('layout/template'); ?>

<?= $this->section('content') ?>
<?php
$statusKtp = $user['status_ktp'] ?? 'belum_upload';
$statusMap = [
    'belum_upload'         => ['Belum Upload', 'status-pending', 'ri-upload-cloud-2-line'],
    'menunggu_verifikasi'  => ['Menunggu Verifikasi Admin', 'status-pending', 'ri-time-line'],
    'terverifikasi'        => ['Terverifikasi', 'status-active', 'ri-shield-check-line'],
    'ditolak'              => ['Verifikasi Gagal', 'status-rejected', 'ri-close-circle-line'],
];
[$labelKtp, $classKtp, $iconKtp] = $statusMap[$statusKtp] ?? $statusMap['belum_upload'];
?>
<link rel="stylesheet" href="<?= base_url('css/public.css') ?>">

<!-- ── PAGE HEADER ── -->
<div class="page-header">
    <div class="page-header-inner">
        <div class="breadcrumb">
            <a href="/">Beranda</a><span>/</span>
            Pengaturan Akun
        </div>
        <h1>Pengaturan Akun</h1>
        <p>Informasi akun dan dokumen jaminan kamu</p>
    </div>
</div>

<!-- ── FLASH MESSAGE ── -->
<?php if (session()->getFlashdata('success')) : ?>
    <div class="php-alert success text-center bg-cyan-500 text-white"
        style="max-width:650px; height:50px;margin:16px auto 0; border-radius: 10px; display:flex; align-items:center; justify-content:center;">
        ✅ <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="php-alert error text-center bg-red-500 text-white"
        style="max-width:650px;margin:16px auto 0; border-radius: 10px; display:flex; align-items:center; justify-content:center;">
        ⚠ <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')) : ?>
    <div class="php-alert error"
        style="max-width:650px;margin:16px auto 0; border-radius: 10px; display:flex; align-items:center; justify-content:center;  ">
        <ul style="margin:0; padding-left:18px;">
            <?php foreach (session()->getFlashdata('errors') as $err) : ?>
                <li>⚠ <?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="sewa-wrapper" style="grid-template-columns: 1fr; max-width:900px; margin:0 auto;">
    <div class="sewa-form-col">

        <a href="<?= base_url('/') ?>" class="btn-primary"><i class="ri-arrow-left-line"></i>Kembali</a>

        <!-- 1. INFORMASI AKUN (READ-ONLY) -->
        <div class="form-card">
            <div class="form-card-header">
                <div class="step-badge"><i class="ri-user-line"></i></div>
                <div>
                    <h2>Informasi Akun</h2>
                    <p>Detail akun kamu saat ini</p>
                </div>
                <a href="<?= base_url('profileupdate') ?>" class="btn-secondary" style="margin-left:auto;">
                    <i class="ri-edit-line"></i> Edit
                </a>
            </div>

            <div class="profile-detail-list">
                <div class="profile-detail-item">
                    <span class="profile-detail-label"><i class="ri-user-3-line"></i> Username</span>
                    <span class="profile-detail-value"><?= esc($user['username']) ?></span>
                </div>
                <div class="profile-detail-item">
                    <span class="profile-detail-label"><i class="ri-mail-line"></i> Email</span>
                    <span class="profile-detail-value"><?= esc($user['email']) ?></span>
                </div>
                <div class="profile-detail-item">
                    <span class="profile-detail-label"><i class="ri-phone-line"></i> Nomor WA</span>
                    <span class="profile-detail-value"><?= esc($user['nomor_wa']) ?></span>
                </div>
                <div class="profile-detail-item">
                    <span class="profile-detail-label"><i class="ri-shield-user-line"></i> Role</span>
                    <span class="profile-detail-value">
                        <?= ($user['level'] ?? 'user') === 'admin' ? 'Admin' : 'User (Penyewa)' ?>
                    </span>
                </div>
                <div class="profile-detail-item">
                    <span class="profile-detail-label"><i class="ri-lock-line"></i> Password</span>
                    <span class="profile-detail-value">••••••••</span>
                </div>
            </div>
        </div>

        <!-- 2. FOTO KTP (JAMINAN SEWA) -->
        <div class="form-card">
            <div class="form-card-header">
                <div class="step-badge"><i class="ri-id-card-fill"></i></div>
                <div>
                    <h2>Foto KTP</h2>
                    <p>Dipakai otomatis sebagai jaminan setiap kali kamu menyewa</p>
                </div>
            </div>

            <div class="ktp-status-box" style="margin-bottom:16px;">
                <span class="status-badge <?= $classKtp ?>">
                    <i class="<?= $iconKtp ?>"></i> <?= esc($labelKtp) ?>
                </span>

                <?php if ($statusKtp === 'ditolak') : ?>
                    <div class="php-alert error" style="margin-top:10px;">
                        <strong>KTP yang kamu unggah ditolak admin.</strong><br>
                        <?php if (! empty($user['catatan_ktp'])) : ?>
                            Alasan: <?= esc($user['catatan_ktp']) ?><br>
                        <?php endif; ?>
                        Silakan unggah ulang foto KTP asli kamu di bawah ini.
                    </div>
                <?php elseif ($statusKtp === 'menunggu_verifikasi') : ?>
                    <p style="color:var(--stone); font-size:0.85rem; margin-top:8px;">
                        KTP kamu sedang menunggu diperiksa oleh admin. Proses ini biasanya tidak lama.
                    </p>
                <?php elseif ($statusKtp === 'terverifikasi') : ?>
                    <p style="color:#16a34a; font-size:0.85rem; margin-top:8px;">
                        <i class="ri-checkbox-circle-fill"></i> KTP kamu sudah diverifikasi. Tidak perlu upload ulang.
                    </p>
                <?php endif; ?>
            </div>



            <form action="<?= base_url('profile/upload-ktp') ?>" method="post" enctype="multipart/form-data" novalidate>
                <?= csrf_field() ?>

                <div class="field-group">
                    <label for="foto_ktp"><?= empty($user['foto_ktp']) ? 'Pilih File' : 'Ganti File' ?> (JPG/PNG/PDF,
                        maks 5MB)</label>
                    <input type="file" id="foto_ktp" name="foto_ktp" accept=".jpg,.jpeg,.png,.pdf" required>
                </div>

                <button type="submit" class="btn-submit" style="width:auto; padding:12px 28px; margin-top:8px;">
                    <i class="ri-upload-line"></i> Simpan Foto KTP
                </button>
            </form>
        </div>

    </div>
</div>

<style>
    .form-card-header {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .profile-detail-list {
        display: flex;
        flex-direction: column;
    }

    .profile-detail-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .profile-detail-item:last-child {
        border-bottom: none;
    }

    .profile-detail-label {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--stone, #6b7280);
        font-size: 0.9rem;
    }

    .profile-detail-value {
        font-weight: 600;
        color: #111827;
    }
</style>

<?= $this->endSection() ?>