<?= $this->extend('admin/layout/template') ?>
<?= $this->section('content') ?>
<div class="table-card animate-up delay-3">
    <div class="page-header">
        <div>
            <h1 class="page-header-title">Ubah Status Penyewaan</h1>
            <p class="page-header-sub">
                Perbarui status penyewaan #<?= $sewa['id_sewa'] ?>.
            </p>
        </div>

        <a href="<?= base_url('penyewaan') ?>" class="btn-secondary">
            <i class="ri-arrow-left-line"></i>
            Kembali
        </a>
    </div>

    <?php if (session()->getFlashdata('error')) : ?>
    <div class="php-alert error" style="margin-bottom:16px;">
        ⚠ <?= esc(session()->getFlashdata('error')) ?>
    </div>
    <?php endif; ?>

    <div class="form-card">

        <!-- Info ringkas, read-only -->
        <div class="form-grid" style="margin-bottom:20px;">
            <div class="form-group">
                <label>Penyewa</label>
                <input type="text" value="<?= esc($sewa['username'] ?? '-') ?>" class="form-control" disabled>
            </div>
            <div class="form-group">
                <label>Perlengkapan</label>
                <input type="text" value="<?= esc($sewa['nama_perlengkapan']) ?>" class="form-control" disabled>
            </div>
            <div class="form-group">
                <label>Jumlah</label>
                <input type="text" value="<?= esc($sewa['jumlah']) ?> unit" class="form-control" disabled>
            </div>
            <div class="form-group">
                <label>Tanggal Sewa</label>
                <input type="text" value="<?= esc($sewa['tgl_sewa']) ?> s/d <?= esc($sewa['tgl_kembali_rencana']) ?>"
                    class="form-control" disabled>
            </div>
        </div>

        <form action="<?= base_url('penyewaan/update/' . $sewa['id_sewa']) ?>" method="post">

            <?= csrf_field(); ?>

            <div class="form-grid">

                <div class="form-group">
                    <label>Status Saat Ini</label>
                    <input type="text" value="<?= esc(ucwords(str_replace('_', ' ', $sewa['status']))) ?>"
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
                        <option value="<?= $value ?>" <?= $sewa['status'] === $value ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

            <div class="ktp-note" style="margin-top:16px;">
                <span>ℹ️</span>
                <span>
                    Mengubah status ke <strong>Disewa</strong> akan otomatis mengurangi stok perlengkapan.
                    Mengubah status keluar dari <strong>Disewa</strong>/<strong>Terlambat</strong>
                    (misal ke Dibatalkan/Ditolak/Dikembalikan) akan otomatis mengembalikan stok.
                </span>
            </div>

            <div class="form-action">

                <button type="submit" class="btn-primary">
                    <i class="ri-save-line"></i>
                    Simpan Perubahan
                </button>

                <a href="<?= base_url('penyewaan') ?>" class="btn-secondary">
                    Batal
                </a>

            </div>

        </form>

    </div>
</div>
<?= $this->endSection() ?>