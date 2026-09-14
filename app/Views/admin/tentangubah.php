<?= $this->extend('admin/layout/template') ?>
<?= $this->section('content') ?>
<div class="page-header animate-up">
    <div>
        <h1 class="page-header-title">Edit Foto Galeri</h1>
        <p class="page-header-sub">Perbarui informasi atau ganti foto</p>
    </div>

</div>
<div style="margin: 16px 20px;">
    <p style="margin-bottom:8px; font-weight:600;">Foto Saat Ini</p>
    <img src="<?= base_url('uploads/galery/' . esc($foto['foto'])) ?>" alt="<?= esc($foto['alt']) ?>"
        style="max-width:100%; max-height:260px; border-radius:8px; object-fit:cover;">
</div>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <ul style="margin:0; padding-left:18px;">
            <?php foreach (session()->getFlashdata('errors') as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="form-container page-header animate-up" style="margin: 0 20px;">



    <form action="<?= base_url('galeritentang/update/' . $foto['id']) ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="nama_foto">Nama / Judul Foto</label>
            <input type="text" name="nama_foto" id="nama_foto" class="form-control"
                value="<?= old('nama_foto', $foto['nama_foto']) ?>" required>
        </div>

        <div class="form-group">
            <label for="alt">Teks Alt (opsional)</label>
            <input type="text" name="alt" id="alt" class="form-control" value="<?= old('alt', $foto['alt']) ?>">
        </div>

        <div class="form-group">
            <label for="foto">Ganti Foto (opsional)</label>
            <input type="file" name="foto" id="foto" class="form-control" accept="image/png,image/jpeg,image/webp">
            <small style="color:#777;">Kosongkan jika tidak ingin mengganti foto. Maksimal 3MB.</small>
        </div>

        <button type="submit" class="btn-primary">
            <i class="ri-save-line"></i> Simpan Perubahan
        </button>
        <a href="<?= base_url('galeritentang') ?>" class="btn-outline">
            <i class="ri-arrow-left-line"></i> Kembali
        </a>
    </form>
</div>
<?= $this->endSection() ?>