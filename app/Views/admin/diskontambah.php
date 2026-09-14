<?= $this->extend('admin/layout/template') ?>
<?= $this->section('content') ?>

<div class="table-card animate-up delay-3">
    <div class="page-header">
        <div>
            <h1 class="page-header-title">Tambah Diskon</h1>
            <p class="page-header-sub">Buat diskon event otomatis atau kode voucher.</p>
        </div>
        <a href="<?= base_url('diskon') ?>" class="btn-secondary">
            <i class="ri-arrow-left-line"></i> Kembali
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
        <form action="<?= base_url('diskon/simpan') ?>" method="post">
            <?= csrf_field() ?>

            <div class="form-grid">
                <div class="form-group">
                    <label>Nama Diskon</label>
                    <input type="text" name="nama" class="form-control" placeholder="Diskon Kemerdekaan 17 Agustus"
                        required>
                </div>

                <div class="form-group">
                    <label>Kode Voucher <small>(kosongkan untuk diskon otomatis tanpa kode)</small></label>
                    <input type="text" name="kode_voucher" class="form-control" placeholder="MERDEKA20"
                        style="text-transform:uppercase;">
                </div>

                <div class="form-group">
                    <label>Tipe Potongan</label>
                    <select name="tipe" class="form-control" required>
                        <option value="persen">Persentase (%)</option>
                        <option value="nominal">Nominal (Rp)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Nilai</label>
                    <input type="number" name="nilai" class="form-control" step="0.01" placeholder="20" required>
                </div>

                <div class="form-group">
                    <label>Minimal Belanja (opsional) <small>(kosongkan jika tidak ada syarat minimal)</small></label>
                    <input type="number" name="minimal_belanja" class="form-control" placeholder="200000">
                </div>

                <div class="form-group">
                    <label>Maks. Potongan (opsional, khusus tipe persen)</label>
                    <input type="number" name="maks_potongan" class="form-control" placeholder="50000">
                </div>


                <div class="form-group">
                    <label>Berlaku Untuk Produk</label>
                    <select name="id_perlengkapan" class="form-control">
                        <option value="">Semua Produk</option>
                        <?php foreach ($perlengkapan as $p): ?>
                        <option value="<?= $p['id_perlengkapan'] ?>"><?= esc($p['nama_perlengkapan']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tanggal Mulai</label>
                    <input type="date" name="tgl_mulai" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Tanggal Selesai</label>
                    <input type="date" name="tgl_selesai" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Keterangan (opsional)</label>
                    <textarea name="keterangan" class="form-control" rows="3"></textarea>
                </div>
            </div>

            <div class="form-action">
                <button type="submit" class="btn-primary">
                    <i class="ri-save-line"></i> Simpan Diskon
                </button>
                <a href="<?= base_url('diskon') ?>" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>