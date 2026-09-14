<?= $this->extend('admin/layout/template') ?>
<?= $this->section('content') ?>
<div class="page-header animate-up">
    <div>
        <h1 class="page-header-title">Tambah Foto Galeri</h1>
        <p class="page-header-sub">Tambahkan foto baru untuk section galeri di halaman Tentang Kami</p>
    </div>

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
    <form action="<?= base_url('galeritentang/simpan') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="nama_foto">Nama / Judul Foto</label>
            <input type="text" name="nama_foto" id="nama_foto" class="form-control" value="<?= old('nama_foto') ?>"
                placeholder="Contoh: Camping di Rinjani" required>
        </div>

        <div class="form-group">
            <label for="alt">Teks Alt (opsional, untuk aksesibilitas & SEO)</label>
            <input type="text" name="alt" id="alt" class="form-control" value="<?= old('alt') ?>"
                placeholder="Deskripsi singkat gambar">
        </div>

        <div class="form-group">
            <label for="foto">Foto</label>
            <input type="file" name="foto" id="foto" class="form-control" accept="image/png,image/jpeg,image/webp"
                required>
            <small style="color:#777;">Format: JPG, PNG, atau WebP. Maksimal 3MB.</small>
        </div>

        <button type="submit" class="btn-primary">
            <i class="ri-save-line"></i> Simpan Foto
        </button>
        <a href="<?= base_url('galeritentang') ?>" class="btn-outline">
            <i class="ri-arrow-left-line"></i> Kembali
        </a>
    </form>
</div>
<?= $this->endSection() ?>