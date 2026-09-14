<?= $this->extend('layout/template'); ?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('css/public.css') ?>">

<!-- ── PAGE HEADER ── -->
<div class="page-header">
    <div class="page-header-inner">
        <div class="breadcrumb">
            <a href="/">Beranda</a><span>/</span>
            <a href="<?= base_url('riwayatsewa') ?>">Riwayat Sewa</a><span>/</span>
            Pembayaran
        </div>
        <h1>Selesaikan Pembayaran</h1>
        <p>Pesananmu sudah tersimpan, tinggal satu langkah lagi</p>
    </div>
</div>

<!-- ── FLASH MESSAGE ── -->
<?php if (session()->getFlashdata('error')) : ?>
    <div class="php-alert error" style="max-width:900px;margin:16px auto 0;">
        ⚠ <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<!-- ── MAIN ── -->
<div class="sewa-wrapper">

    <!-- ══ LEFT COLUMN ══ -->
    <div class="sewa-form-col">

        <!-- 1. STATUS PEMBAYARAN -->
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
                    ✗ Pembayaran gagal / dibatalkan. Silakan hubungi admin untuk mengulang transaksi.
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
                        <div class="payment-info-desc">
                            Klik tombol "Bayar Sekarang" di samping untuk membuka pilihan metode pembayaran
                            (Kartu Kredit, E-Wallet, QRIS, atau Virtual Account).
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- 2. DETAIL PRODUK -->
        <div class="form-card">
            <div class="form-card-header">
                <div class="step-badge">02</div>
                <div>
                    <h2>Detail Penyewaan</h2>
                    <p>Rincian produk dan tanggal sewa</p>
                </div>
            </div>

            <div class="perlengkapan-selected">
                <div class="perlengkapan-selected-img">
                    <img src="/uploads/perlengkapan/<?= esc($sewa['foto_perlengkapan']) ?>"
                        alt="<?= esc($sewa['nama_perlengkapan']) ?>"
                        onerror="this.style.background='#e8f0e8';this.removeAttribute('src')">
                </div>
                <div class="perlengkapan-selected-info">
                    <div class="perlengkapan-selected-name"><?= esc($sewa['nama_perlengkapan']) ?></div>
                    <div class="perlengkapan-selected-price">
                        <?= esc($sewa['jumlah']) ?> unit ·
                        <?= esc(date('d M Y', strtotime($sewa['tgl_sewa']))) ?> s/d
                        <?= esc(date('d M Y', strtotime($sewa['tgl_kembali_rencana']))) ?>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- /sewa-form-col -->

    <!-- ══ RIGHT COLUMN: SUMMARY ══ -->
    <aside class="sewa-summary">
        <div class="summary-card">
            <div class="summary-title">Ringkasan Pembayaran</div>

            <div class="summary-perlengkapan">
                <img src="/uploads/perlengkapan/<?= esc($sewa['foto_perlengkapan']) ?>"
                    alt="<?= esc($sewa['nama_perlengkapan']) ?>" onerror="this.style.display='none'">
                <div>
                    <div class="summary-perlengkapan-name"><?= esc($sewa['nama_perlengkapan']) ?></div>
                    <div class="summary-perlengkapan-price"><?= esc($sewa['jumlah']) ?> unit</div>
                </div>
            </div>

            <div class="summary-rows">
                <div class="summary-row">
                    <span>Order ID</span>
                    <span><?= esc($bayar['order_id']) ?></span>
                </div>
                <div class="summary-row">
                    <span>Metode Bayar</span>
                    <span><?= (($bayar['metode_bayar'] ?? null) === 'cod') ? 'COD (Bayar di Tempat)' : 'Online (Midtrans)' ?></span>
                </div>
                <div class="summary-row">
                    <span>Status</span>
                    <span><?= esc(ucfirst($bayar['status_bayar'])) ?></span>
                </div>
                <div class="summary-divider"></div>
                <div class="summary-row summary-total">
                    <span>Total Bayar</span>
                    <span>Rp <?= number_format($bayar['jumlah_bayar'], 0, ',', '.') ?></span>
                </div>
            </div>

            <?php if ($bayar['status_bayar'] === 'pending' && ($bayar['metode_bayar'] ?? null) !== 'cod') : ?>
                <button type="button" class="btn-submit" id="btn_bayar_sekarang">
                    <i class="ri-wallet-line"></i> Bayar Sekarang
                </button>
            <?php else : ?>
                <a href="<?= base_url('riwayatsewa') ?>" class="btn-submit"
                    style="text-align:center;display:block;text-decoration:none;">
                    Lihat Riwayat Sewa
                </a>
            <?php endif; ?>

            <div class="summary-disclaimer">
                Pesananmu akan berstatus <strong>menunggu konfirmasi</strong> setelah pembayaran diterima,
                sampai admin memverifikasi data & KTP kamu. Pantau statusnya di
                <a href="<?= base_url('riwayatsewa') ?>">Riwayat Sewa</a>.
            </div>
        </div>

        <!-- Kontak Cepat -->
        <div class="quick-contact">
            <div class="qc-title">Butuh Bantuan?</div>
            <a href="https://wa.me/6281234567890" target="_blank" class="qc-wa">
                <span>💬</span> Chat WhatsApp Admin
            </a>
            <div class="qc-hours">Jam operasional: 08.00 – 21.00 WIB</div>
        </div>
    </aside>

</div><!-- /sewa-wrapper -->

<?php if ($bayar['status_bayar'] === 'pending' && ($bayar['metode_bayar'] ?? null) !== 'cod') : ?>
    <script src="https://app.<?= $isProduction ? '' : 'sandbox.' ?>midtrans.com/snap/snap.js"
        data-client-key="<?= esc($clientKey) ?>"></script>
    <script>
        document.getElementById('btn_bayar_sekarang').addEventListener('click', function() {
            snap.pay('<?= esc($bayar['snap_token'], 'js') ?>', {
                onSuccess: function(result) {
                    window.location.href = '<?= base_url('riwayatsewa') ?>?bayar=sukses';
                },
                onPending: function(result) {
                    window.location.href = '<?= base_url('riwayatsewa') ?>?bayar=pending';
                },
                onError: function(result) {
                    alert('Pembayaran gagal. Silakan coba lagi.');
                },
                onClose: function() {
                    // user nutup popup tanpa selesai bayar, biarkan tetap di halaman ini
                }
            });
        });
    </script>
<?php endif; ?>

<?php if ($bayar['status_bayar'] === 'pending') : ?>
    <script>
        // ── POLLING STATUS: tetap jalan untuk metode online MAUPUN COD,
        //    supaya halaman auto-reload begitu admin konfirmasi lunas ──
        const pollStatusPembayaran = setInterval(function() {
            fetch('<?= base_url('pembayaran/status/' . $sewa['id_sewa']) ?>')
                .then(res => res.json())
                .then(data => {
                    if (data.status_bayar && data.status_bayar !== 'pending') {
                        clearInterval(pollStatusPembayaran);
                        window.location.reload();
                    }
                })
                .catch(err => console.error('Gagal cek status pembayaran:', err));
        }, 5000);

        // Hentikan polling kalau user tinggalkan halaman, biar nggak nge-hang di background
        window.addEventListener('beforeunload', function() {
            clearInterval(pollStatusPembayaran);
        });
    </script>
<?php endif; ?>

<?= $this->endSection() ?>