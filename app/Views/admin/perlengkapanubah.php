<?= $this->extend('admin/layout/template') ?>
<?= $this->section('content') ?>
<div class="table-card animate-up delay-3">
    <div class="page-header">
        <div>
            <h1 class="page-header-title">Ubah Perlengkapan</h1>
            <p class="page-header-sub">
                Perbarui data perlengkapan yang sudah ada.
            </p>
        </div>

        <a href="<?= base_url('perlengkapan') ?>" class="btn-secondary">
            <i class="ri-arrow-left-line"></i>
            Kembali
        </a>
    </div>

    <div class="form-card">

        <form action="<?= base_url('perlengkapan/update/' . $perlengkapan['id_perlengkapan']) ?>" method="post"
            enctype="multipart/form-data">

            <?= csrf_field(); ?>

            <div class="form-grid">

                <div class="form-group">
                    <label>Kode Perlengkapan</label>
                    <input type="text" name="kd_perlengkapan" class="form-control"
                        value="<?= $perlengkapan['kd_perlengkapan'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Nama Perlengkapan</label>
                    <input type="text" name="nama_perlengkapan" class="form-control"
                        value="<?= $perlengkapan['nama_perlengkapan'] ?>" required>
                </div>

                <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori" class="form-control" required>

                        <option value="">-- Pilih Kategori --</option>

                        <?php foreach ($kategori as $k) : ?>
                        <option value="<?= $k['id_kategori'] ?>"
                            <?= ($k['id_kategori'] == $perlengkapan['id_kategori']) ? 'selected' : '' ?>>
                            <?= $k['id_kategori'] ?> -
                            <?= $k['nama_kategori'] ?></option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="form-group">
                    <label>Harga Sewa / Hari</label>
                    <input type="number" name="harga_sewa" class="form-control"
                        value="<?= $perlengkapan['harga_sewa'] ?>" required>
                </div>

                <div class="form-group">
                    <label>Stok</label>
                    <input type="number" name="stok" class="form-control" value="<?= $perlengkapan['stok'] ?>" required>
                </div>
                <div class="form-group full">
                    <label>Foto Produk</label>
                    <img id="preview"
                        src="<?= base_url('uploads/perlengkapan/' . $perlengkapan['foto_perlengkapan']) ?>"
                        style="width:120px;height:120px;object-fit:cover;border-radius:10px;">

                    <input type="file" name="foto_perlengkapan" id="foto_perlengkapan" class="form-control">
                </div>

            </div>

            <div class="form-action">

                <button type="submit" class="btn-primary">
                    <i class="ri-save-line"></i>
                    Simpan
                </button>

                <a href="<?= base_url('perlengkapan') ?>" class="btn-secondary">
                    Batal
                </a>

            </div>

        </form>

    </div>
</div>
<script>
document.getElementById('foto_perlengkapan').addEventListener('change', function(e) {
    const file = e.target.files[0];

    if (file) {
        document.getElementById('preview').src = URL.createObjectURL(file);
    }
});
</script>
<?= $this->endSection() ?>