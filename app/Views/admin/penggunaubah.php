<?= $this->extend('/admin/layout/template') ?>
<?= $this->section('content') ?>
<div class="table-card animate-up delay-3">
    <div class="page-header">
        <div>
            <h1 class="page-header-title">Ubah Data Pengguna</h1>
            <p class="page-header-sub">
                Ubah data pengguna ke dalam sistem.
            </p>
        </div>

        <a href="<?= base_url('pengguna') ?>" class="btn-secondary">
            <i class="ri-arrow-left-line"></i>
            Kembali
        </a>
    </div>

    <?php if (session()->getFlashdata('errors')) : ?>
    <div class="php-alert error" style="margin-bottom:16px;">
        <ul style="margin:0; padding-left:18px;">
            <?php foreach (session()->getFlashdata('errors') as $err) : ?>
            <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <div class="form-card">

        <form action="<?= base_url('pengguna/update/' . $user['id_user']) ?>" method="post">

            <?= csrf_field(); ?>

            <div class="form-grid">

                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" value="<?= old('username', $user['username']) ?>"
                        class="form-control" disabled minlength="3" maxlength="50">
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= old('email', $user['email']) ?>" class="form-control"
                        disabled>
                </div>
                <div class="form-group">
                    <label>Nomor WA</label>
                    <input type="text" name="nomor_wa" value="<?= old('nomor_wa', $user['nomor_wa']) ?>"
                        class="form-control" disabled>
                </div>


                <div class="form-group">
                    <label>Role / Level Akun</label>
                    <select name="level" class="form-control" required>
                        <option value="user" <?= old('level', $user['level']) === 'user' ? 'selected' : '' ?>>
                            User (Penyewa)
                        </option>
                        <option value="admin" <?= old('level', $user['level']) === 'admin' ? 'selected' : '' ?>>
                            Admin
                        </option>
                    </select>
                    <?php if ((int) $user['id_user'] === (int) session()->get('id_user')) : ?>
                    <span style="display:block; margin-top:6px; font-size:0.8rem; color:#e8a020;">
                        ⚠️ Ini akun kamu sendiri. Mengubah role bisa membuat kamu kehilangan akses admin.
                    </span>
                    <?php endif; ?>
                </div>



            </div>

            <div class="form-action">

                <button type="submit" class="btn-primary">
                    <i class="ri-save-line"></i>
                    Simpan
                </button>

                <a href="<?= base_url('pengguna') ?>" class="btn-secondary">
                    Batal
                </a>

            </div>

        </form>
        <div class="form-group">
            <label>Foto KTP</label>
            <?php if (! empty($user['foto_ktp'])) : ?>
            <a href="<?= base_url('pengguna/ktp/' . $user['foto_ktp']) ?>" target="_blank">
                <img src="<?= base_url('pengguna/ktp/' . $user['foto_ktp']) ?>" alt="Foto KTP"
                    style="width:180px; height:120px; object-fit:cover; border-radius:8px; border:1px solid #e5e7eb; display:block;">
            </a>
            <small style="display:block; margin-top:6px; color:#999;">Klik gambar untuk memperbesar</small>
            <?php else : ?>
            <span style="color:#999; font-size:0.85rem;">Pengguna ini belum mengunggah foto KTP.</span>
            <?php endif; ?>
        </div>
        <?php if (! empty($user['foto_ktp'])) : ?>
        <!-- ── STATUS VERIFIKASI KTP (terpisah dari form update data, form sendiri) ── -->
        <div class="ktp-verifikasi-box">
            <div class="ktp-verifikasi-header">
                <span>Status Verifikasi KTP</span>
                <?php
                    $statusKtp = $user['status_ktp'] ?? 'menunggu_verifikasi';
                    $statusMap = [
                        'menunggu_verifikasi' => ['Menunggu Verifikasi', 'badge-secondary'],
                        'terverifikasi'       => ['Terverifikasi', 'badge-success'],
                        'ditolak'             => ['Verifikasi Gagal', 'badge-danger'],
                    ];
                    [$labelStatus, $classStatus] = $statusMap[$statusKtp] ?? ['Belum Upload', 'badge-secondary'];
                    ?>
                <span class="badge <?= $classStatus ?>"><?= esc($labelStatus) ?></span>
            </div>

            <?php if ($statusKtp === 'ditolak' && ! empty($user['catatan_ktp'])) : ?>
            <div class="php-alert error" style="margin-top:10px;">
                <strong>Alasan penolakan sebelumnya:</strong> <?= esc($user['catatan_ktp']) ?>
            </div>
            <?php endif; ?>

            <?php if ($statusKtp !== 'terverifikasi') : ?>
            <form action="<?= base_url('pengguna/verifikasi-ktp/' . $user['id_user']) ?>" method="post"
                style="display:inline-block; margin-top:12px;">
                <?= csrf_field() ?>
                <button type="submit" class="btn-primary" style="background:#16a34a;">
                    <i class="ri-check-line"></i> Verifikasi KTP
                </button>
            </form>
            <?php endif; ?>

            <button type="button" class="btn-secondary" style="margin-top:12px; color:#dc2626; border-color:#dc2626;"
                onclick="document.getElementById('formTolakKtp').classList.toggle('hidden')">
                <i class="ri-close-line"></i> Tolak KTP
            </button>

            <form id="formTolakKtp" action="<?= base_url('pengguna/tolak-ktp/' . $user['id_user']) ?>" method="post"
                class="hidden" style="margin-top:12px;">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Alasan Penolakan</label>
                    <textarea name="alasan_tolak" class="form-control" rows="3" required minlength="5"
                        placeholder="Contoh: Foto yang diunggah bukan foto KTP, mohon unggah ulang foto KTP asli."></textarea>
                </div>
                <button type="submit" class="btn-primary" style="background:#dc2626;">
                    <i class="ri-send-plane-line"></i> Kirim Penolakan
                </button>
            </form>
        </div>
        <?php endif; ?>

    </div>
</div>

<style>
.ktp-verifikasi-box {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px dashed #e5e7eb;
}

.ktp-verifikasi-header {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
    font-size: 0.92rem;
}

.badge-danger {
    background: rgba(220, 38, 38, 0.1);
    color: #dc2626;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
}

#formTolakKtp.hidden {
    display: none;
}
</style>

<?= $this->endSection() ?>