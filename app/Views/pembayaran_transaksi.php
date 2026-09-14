<?= $this->extend('layout/template'); ?>
<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('css/public.css') ?>">

<div class="page-header">
    <div class="page-header-inner">
        <div class="breadcrumb">
            <a href="/">Beranda</a><span>/</span>
            <a href="<?= base_url('riwayatsewa') ?>">Riwayat Sewa</a><span>/</span>
            Pembayaran
        </div>
        <h1>Selesaikan Pembayaran</h1>
        <p>Pesananmu (<?= count($items) ?> item) sudah tersimpan</p>
    </div>
</div>

<div class="sewa-wrapper">

    <div class="sewa-form-col">

        <div class="form-card">
            <div class="form-card-header">
                <div class="step-badge">01</div>
                <div>
                    <h2>Status Pembayaran</h2>
                    <p>Order ID: <?= esc($bayar['order_id']) ?></p>
                </div>
            </div>

            <?php if ($bayar['status_bayar'] === 'lunas') : ?>
                <div class="stok-badge stok-ok" style="font-size:1rem;padding:14px 18px;">
                    ✓ Pembayaran sudah diterima. Menunggu konfirmasi admin.
                </div>
            <?php elseif ($bayar['status_bayar'] === 'expired') : ?>
                <div class="stok-badge stok-habis" style="font-size:1rem;padding:14px 18px;">
                    ✗ Batas waktu pembayaran sudah habis. Silakan hubungi admin.
                </div>
            <?php elseif ($bayar['status_bayar'] === 'gagal') : ?>
                <div class="stok-badge stok-habis" style="font-size:1rem;padding:14px 18px;">
                    ✗ Pembayaran gagal/dibatalkan. Silakan hubungi admin.
                </div>
            <?php elseif (($bayar['metode_bayar'] ?? null) === 'cod') : ?>
                <div class="payment-info-box">
                    <div class="payment-info-icon"><i class="ri-truck-line"></i></div>
                    <div class="payment-info-text">
                        <div class="payment-info-title">Cash on Delivery (COD)</div>
                        <div class="payment-info-desc">
                            Kamu memilih bayar di tempat. Admin akan menghubungimu untuk konfirmasi pesanan, dan
                            pembayaran dilakukan tunai saat barang diambil/diantar.
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <div class="payment-info-box">
                    <div class="payment-info-icon"><i class="ri-question-line"></i></div>
                    <div class="payment-info-text">
                        <div class="payment-info-title">Menunggu Pembayaran</div>
                        <div class="payment-info-desc">Klik "Bayar Sekarang" untuk membuka pilihan metode pembayaran.</div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="form-card">
            <div class="form-card-header">
                <div class="step-badge">02</div>
                <div>
                    <h2>Detail Pesanan (<?= count($items) ?> item)</h2>
                    <p>Rincian produk yang kamu sewa</p>
                </div>
            </div>

            <?php foreach ($items as $item) : ?>
                <div class="perlengkapan-selected" style="margin-bottom:12px;">
                    <div class="perlengkapan-selected-img">
                        <img src="/uploads/perlengkapan/<?= esc($item['foto_perlengkapan']) ?>"
                            alt="<?= esc($item['nama_perlengkapan']) ?>"
                            onerror="this.style.background='#e8f0e8';this.removeAttribute('src')">
                    </div>
                    <div class="perlengkapan-selected-info">
                        <div class="perlengkapan-selected-name"><?= esc($item['nama_perlengkapan']) ?></div>
                        <div class="perlengkapan-selected-price">
                            <?= esc($item['jumlah']) ?> unit ·
                            <?= esc(date('d M Y', strtotime($item['tgl_sewa']))) ?> s/d
                            <?= esc(date('d M Y', strtotime($item['tgl_kembali_rencana']))) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>

    <aside class="sewa-summary">
        <div class="summary-card">
            <div class="summary-title">Ringkasan Pembayaran</div>

            <div class="summary-rows">
                <div class="summary-row">
                    <span>Order ID</span>
                    <span><?= esc($bayar['order_id']) ?></span>
                </div>
                <div class="summary-row">
                    <span>Jumlah Item</span>
                    <span><?= count($items) ?> produk</span>
                </div>
                <div class="summary-row">
                    <span>Metode Bayar</span>
                    <span><?= (($bayar['metode_bayar'] ?? null) === 'cod') ? 'COD (Bayar di Tempat)' : 'Online (Midtrans)' ?></span>
                </div>
                <?php if (! empty($bayar['nilai_potongan']) && $bayar['nilai_potongan'] > 0) : ?>
                    <div class="summary-row">
                        <span>Diskon <?= $bayar['kode_voucher'] ? '(' . esc($bayar['kode_voucher']) . ')' : '' ?></span>
                        <span style="color:#16a34a;">-Rp <?= number_format($bayar['nilai_potongan'], 0, ',', '.') ?></span>
                    </div>
                <?php endif; ?>
                <div class="summary-divider"></div>
                <div class="summary-row summary-total">
                    <span>Total Bayar</span>
                    <span>Rp <?= number_format($bayar['jumlah_bayar'], 0, ',', '.') ?></span>
                </div>
            </div>

            <?php if ($bayar['status_bayar'] === 'pending' && ($bayar['metode_bayar'] ?? null) !== 'cod') : ?>
                <button type="button" class="btn-submit" id="btn_bayar_sekarang"><i class="ri-wallet-line"></i>
                    Bayar Sekarang</button>
            <?php else : ?>
                <a href="<?= base_url('riwayatsewa') ?>" class="btn-submit"
                    style="text-align:center;display:block;text-decoration:none;">
                    Lihat Riwayat Sewa
                </a>
            <?php endif; ?>
        </div>
    </aside>

</div>

<?php if ($bayar['status_bayar'] === 'pending' && ($bayar['metode_bayar'] ?? null) !== 'cod') : ?>
    <script src="https://app.<?= $isProduction ? '' : 'sandbox.' ?>midtrans.com/snap/snap.js"
        data-client-key="<?= esc($clientKey) ?>"></script>
    <script>
        document.getElementById('btn_bayar_sekarang').addEventListener('click', function() {
            snap.pay('<?= esc($bayar['snap_token'], 'js') ?>', {
                onSuccess: () => window.location.href = '<?= base_url('riwayatsewa') ?>?bayar=sukses',
                onPending: () => window.location.href = '<?= base_url('riwayatsewa') ?>?bayar=pending',
                onError: () => alert('Pembayaran gagal. Silakan coba lagi.'),
                onClose: () => {},
            });
        });
    </script>
<?php endif; ?>

<?php if ($bayar['status_bayar'] === 'pending') : ?>
    <script>
        // ── POLLING STATUS: tetap jalan untuk metode online MAUPUN COD,
        //    supaya halaman auto-reload begitu admin konfirmasi lunas ──
        const pollStatus = setInterval(function() {
            fetch('<?= base_url('pembayaran/status-transaksi/' . $kodeTransaksi) ?>')
                .then(res => res.json())
                .then(data => {
                    if (data.status_bayar && data.status_bayar !== 'pending') {
                        clearInterval(pollStatus);
                        window.location.reload();
                    }
                });
        }, 5000);

        window.addEventListener('beforeunload', () => clearInterval(pollStatus));
    </script>
<?php endif; ?>

<?= $this->endSection() ?>