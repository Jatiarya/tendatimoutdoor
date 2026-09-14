<?= $this->extend('/admin/layout/template') ?>
<?= $this->section('content') ?>
<div class="table-card animate-up delay-3">
    <div class="page-header">
        <div>
            <h1 class="page-header-title">Ubah Data Kategori</h1>
            <p class="page-header-sub">
                Ubah data kategori baru ke dalam sistem.
            </p>
        </div>

        <a href="<?= base_url('kategori') ?>" class="btn-secondary">
            <i class="ri-arrow-left-line"></i>
            Kembali
        </a>
    </div>

    <div class="form-card">

        <form action="<?= base_url('kategori/update/'. $kategori['id_kategori']) ?>" method="post" enctype="multipart/form-data">

            <?= csrf_field(); ?>

            <div class="form-grid">

                <div class="form-group">
                    <label>Kode Kategori</label>
                    <input type="text" name="kd_kategori" value="<?= $kategori['kd_kategori'] ?>" class="form-control"
                        placeholder="Contoh : KT001" required>
                </div>
                <div class="form-group">
                    <label>Nama Kategori</label>
                    <input type="text" name="nama_kategori" value="<?= $kategori['nama_kategori'] ?>"
                        class="form-control" placeholder="Contoh: Tas, Tenda...." required>
                </div>
            </div>

            <div class="form-action">

                <button type="submit" class="btn-primary">
                    <i class="ri-save-line"></i>
                    Simpan
                </button>

                <a href="<?= base_url('kategori') ?>" class="btn-secondary">
                    Batal
                </a>

            </div>

        </form>

    </div>
</div>
<?= $this->endSection() ?>