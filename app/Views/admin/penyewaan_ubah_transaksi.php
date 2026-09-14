<?= $this->extend('admin/layout/template') ?>
<?= $this->section('content') ?>

<div class="table-card animate-up delay-3">
    <div class="page-header">
        <div>
            <h1 class="page-header-title">Ubah Status Pesanan</h1>
            <p class="page-header-sub">
                <?= count($items) > 1 ? count($items) . ' produk dalam 1 pesanan' : '1 produk' ?>
            </p>
        </div>
        <a href="<?= base_url('penyewaan') ?>" class="btn-secondary">
            <i class="ri-arrow-left-line"></i> Kembali
        </a>
    </div>

    <div class="form-card">

        <!-- Daftar produk dalam pesanan ini, read-only -->
        <div style="margin-bottom:20px;">
            <?php foreach ($items as $it) : ?>
            <div style="display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid #eee;">
                <span><?= esc($it['nama_perlengkapan']) ?> (<?= $it['jumlah'] ?> unit)</span>
                <span style="color:#8a8070;"><?= esc($it['tgl_sewa']) ?> s/d
                    <?= esc($it['tgl_kembali_rencana']) ?></span>
            </div>
            <?php endforeach; ?>
        </div>

        <form action="<?= base_url('penyewaan/update-transaksi/' . $identifier) ?>" method="post">
            <?= csrf_field() ?>

            <div class="form-grid">
                <div class="form-group">
                    <label>Status Saat Ini</label>
                    <input type="text" value="<?= esc(ucwords(str_replace('_', ' ', $statusSaatIni))) ?>"
                        class="form-control" disabled>
                </div>

                <div class="form-group">
                    <label>Ubah Status Menjadi</label>
                    <select name="status" class="form-control" required>
                        <?php
                        $statusList = [
                            'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
                            'disewa'              => 'Disewa',
                            'ditolak'             => 'Ditolak',
                            'dibatalkan'          => 'Dibatalkan',
                            'dikembalikan'        => 'Dikembalikan',
                            'terlambat'           => 'Terlambat',
                        ];
                        ?>
                        <?php foreach ($statusList as $value => $label) : ?>
                        <option value="<?= $value ?>" <?= $statusSaatIni === $value ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <?php if (count($items) > 1) : ?>
            <div class="ktp-note" style="margin-top:16px;">
                <span><i class="ri-information-line"></i></span>
                <span>Perubahan status ini akan berlaku untuk <strong>semua <?= count($items) ?> produk</strong> dalam
                    pesanan ini sekaligus.</span>
            </div>
            <?php endif; ?>

            <div class="form-action">
                <button type="submit" class="btn-primary">
                    <i class="ri-save-line"></i> Simpan Perubahan
                </button>
                <a href="<?= base_url('penyewaan') ?>" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>