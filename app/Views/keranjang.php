<?= $this->extend('layout/template'); ?>
<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('css/public.css') ?>">

<div class="page-header">
    <div class="page-header-inner">
        <div class="breadcrumb">
            <a href="/">Beranda</a><span>/</span>
            <a href="<?= base_url('katalog') ?>">Katalog</a><span>/</span>
            Keranjang
        </div>
        <h1>Keranjang Sewa</h1>
        <p>Review pesananmu sebelum checkout</p>
    </div>
</div>

<?php if (session()->getFlashdata('error')) : ?>
<div class="php-alert error" style="max-width:900px;margin:16px auto 0;">
    ⚠ <?= esc(session()->getFlashdata('error')) ?>
</div>
<?php endif; ?>

<div class="sewa-wrapper">

    <div class="sewa-form-col">

        <?php if (empty($items)) : ?>
        <div class="form-card" style="text-align:center; padding:48px 24px;">
            <div style="font-size:3rem; margin-bottom:12px;"><i class="ri-shopping-cart-line"></i></div>
            <h3>Keranjang Kosong</h3>
            <p style="color:var(--stone); margin-bottom:20px;">Belum ada perlengkapan yang ditambahkan.</p>
            <a href="<?= base_url('katalog') ?>" class="btn-submit"
                style="display:inline-block; text-decoration:none; width:auto; padding:12px 28px;">
                Jelajahi Katalog
            </a>
        </div>
        <?php else : ?>

        <?php foreach ($items as $item) : $p = $item['perlengkapan']; ?>
        <div class="form-card" data-key="<?= esc($item['key']) ?>">
            <div style="display:flex; gap:16px; align-items:center;">
                <img src="/uploads/perlengkapan/<?= esc($p['foto_perlengkapan']) ?>"
                    alt="<?= esc($p['nama_perlengkapan']) ?>"
                    style="width:80px; height:80px; object-fit:cover; border-radius:10px; flex-shrink:0;"
                    onerror="this.style.background='#e8f0e8'; this.removeAttribute('src')">

                <div style="flex:1;">
                    <h3 style="margin:0 0 4px;"><?= esc($p['nama_perlengkapan']) ?></h3>
                    <div style="color:var(--stone); font-size:0.85rem;">
                        <?= esc($item['jumlah']) ?> unit · <?= $item['durasi_hari'] ?> hari ·
                        <?= esc(date('d M Y', strtotime($item['tgl_sewa']))) ?> s/d
                        <?= esc(date('d M Y', strtotime($item['tgl_kembali_rencana']))) ?>
                    </div>
                    <div style="font-weight:700; color:var(--primary); margin-top:6px;">
                        Rp <?= number_format($item['subtotal'], 0, ',', '.') ?>
                    </div>
                </div>

                <button type="button" class="btn-hapus-item" data-key="<?= esc($item['key']) ?>"
                    style="background:none; border:none; color:#dc2626; cursor:pointer; font-size:1.4rem; padding:8px;">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>

    </div>

    <?php if (! empty($items)) : ?>
    <aside class="sewa-summary">
        <div class="summary-card">
            <div class="summary-title">Ringkasan Pesanan</div>

            <div class="summary-rows">
                <?php foreach ($items as $item) : $p = $item['perlengkapan']; ?>
                <div class="summary-row">
                    <span><?= esc($p['nama_perlengkapan']) ?> (<?= $item['jumlah'] ?>x)</span>
                    <span>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></span>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="summary-divider"></div>

            <!-- ── TRIGGER MODAL VOUCHER ── -->
            <?php if (! empty($voucherTersedia)) : ?>
            <div class="summary-row summary-voucher-trigger" onclick="bukaModalVoucher()">
                <span><i class="ri-coupon-3-line"></i> Voucher</span>
                <span class="voucher-trigger-value" id="voucher_trigger_label">
                    Pilih Voucher <i class="ri-arrow-right-s-line"></i>
                </span>
            </div>
            <div class="summary-divider"></div>
            <?php endif; ?>

            <div class="summary-row" id="row_diskon" style="display:none;">
                <span>Diskon</span>
                <span id="sum_diskon" style="color:#16a34a;">-Rp 0</span>
            </div>

            <div class="summary-divider"></div>

            <div class="summary-row summary-total">
                <span>Total Bayar</span>
                <span id="sum_total">Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
            </div>

            <div class="summary-divider"></div>

            <!-- ── METODE PEMBAYARAN ── -->
            <div class="field-group">
                <label>Metode Pembayaran</label>
                <div class="voucher-list">
                    <label class="voucher-card">
                        <input type="radio" name="metode_pembayaran" value="online" checked>
                        <div class="voucher-card-body">
                            <div class="text-white">Bayar Online (Midtrans)</div>
                            <div class="voucher-card-desc">Kartu, E-Wallet, QRIS, VA</div>
                        </div>
                    </label>
                    <label class="voucher-card">
                        <input type="radio" name="metode_pembayaran" value="cod">
                        <div class="voucher-card-body">
                            <div class="text-white">COD (Bayar di Tempat)</div>
                            <div class="voucher-card-desc">Bayar tunai saat barang diambil/diantar</div>
                        </div>
                    </label>
                </div>
            </div>

            <?php if (empty($user['foto_ktp'])) : ?>
            <div class="stok-badge stok-habis" style="margin:16px 0; font-size:0.85rem;">
                ⚠️ Kamu belum unggah KTP.
                <a href="<?= base_url('profile') ?>" style="color:inherit; text-decoration:underline;">Unggah
                    sekarang</a>
            </div>
            <button type="button" class="btn-submit" disabled style="opacity:0.5; cursor:not-allowed;">
                <i class="ri-shopping-cart-line"></i> Checkout
            </button>
            <?php else : ?>
            <button type="button" class="btn-submit" id="btn_checkout" style="margin-top:16px;">
                <i class="ri-shopping-cart-line"></i> Checkout
            </button>
            <button type="button" class="btn-submit" id="btn_nambah" style="margin-top:16px;">
                <i class="ri-add-line"></i> Nambah barang lagi
            </button>
            <?php endif; ?>

            <div id="checkout_error" style="color:#dc2626; font-size:0.85rem; margin-top:10px; display:none;"></div>
        </div>
    </aside>
    <?php endif; ?>

    <!-- ══ MODAL PILIH VOUCHER ══ -->
    <?php if (! empty($voucherTersedia)) : ?>
    <div class="modal-overlay" id="modal_voucher_overlay" onclick="if(event.target===this) tutupModalVoucher()">
        <div class="modal-box">
            <div class="modal-box-header">
                <h3><i class="ri-coupon-3-line"></i> Pilih Voucher</h3>
                <button type="button" class="modal-close-btn" onclick="tutupModalVoucher()">
                    <i class="ri-close-line"></i>
                </button>
            </div>

            <div class="modal-box-body">
                <div class="voucher-list">
                    <label class="voucher-card voucher-card-none">
                        <input type="radio" name="voucher_pilihan" value="" checked
                            onchange="pilihVoucherKeranjang(this)">
                        <div class="voucher-card-body">
                            <div class="voucher-card-title">Tanpa Voucher</div>
                            <div class="voucher-card-desc">Bayar harga normal</div>
                        </div>
                    </label>

                    <?php foreach ($voucherTersedia as $v) :
                            $labelNilai = $v['tipe'] === 'persen' ? $v['nilai'] . '%' : 'Rp ' . number_format($v['nilai'], 0, ',', '.');
                        ?>
                    <label class="voucher-card">
                        <input type="radio" name="voucher_pilihan" value="<?= esc($v['kode_voucher']) ?>"
                            onchange="pilihVoucherKeranjang(this)">
                        <div class="voucher-card-body">
                            <div class="voucher-card-badge"><?= esc($labelNilai) ?> OFF</div>
                            <div class="voucher-card-title"><?= esc($v['nama']) ?></div>
                            <div class="voucher-card-desc">
                                Kode: <strong><?= esc($v['kode_voucher']) ?></strong>
                                <?php if (! empty($v['minimal_belanja'])) : ?>
                                <br>Min. belanja Rp <?= number_format($v['minimal_belanja'], 0, ',', '.') ?>
                                <?php endif; ?>
                            </div>
                            <div class="voucher-card-periode">Berlaku s/d
                                <?= esc(date('d M Y', strtotime($v['tgl_selesai']))) ?></div>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>

                <div id="voucher_message" style="margin-top:10px; font-size:0.85rem;"></div>
            </div>

            <div class="modal-box-footer">
                <button type="button" class="btn-submit" onclick="tutupModalVoucher()">Terapkan</button>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<script src="https://app.<?= $isProduction ?? false ? '' : 'sandbox.' ?>midtrans.com/snap/snap.js"
    data-client-key="<?= esc($clientKey ?? '') ?>"></script>

<script>
const subtotalAsli = <?= (int) $subtotal ?>;
let potonganAktif = 0;

// ── MODAL VOUCHER (global, dipanggil dari onclick di HTML) ──
function bukaModalVoucher() {
    document.getElementById('modal_voucher_overlay').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function tutupModalVoucher() {
    document.getElementById('modal_voucher_overlay').classList.remove('active');
    document.body.style.overflow = '';
}

function pilihVoucherKeranjang(radio) {
    const kode = radio.value;
    const messageBox = document.getElementById('voucher_message');
    const label = document.getElementById('voucher_trigger_label');

    if (!kode) {
        potonganAktif = 0;
        document.getElementById('row_diskon').style.display = 'none';
        document.getElementById('sum_total').textContent = 'Rp ' + subtotalAsli.toLocaleString('id-ID');
        messageBox.textContent = '';
        label.innerHTML = 'Pilih Voucher <i class="ri-arrow-right-s-line"></i>';
        return;
    }

    fetch('<?= base_url('sewa/cek-voucher') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                kode_voucher: kode,
                id_perlengkapan: 0,
                subtotal: subtotalAsli,
            })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.valid) {
                messageBox.textContent = data.message;
                messageBox.style.color = '#dc2626';
                potonganAktif = 0;
                document.getElementById('row_diskon').style.display = 'none';
            } else {
                messageBox.textContent = data.message;
                messageBox.style.color = '#16a34a';
                potonganAktif = data.potongan;
                document.getElementById('row_diskon').style.display = 'flex';
                document.getElementById('sum_diskon').textContent = '-Rp ' + potonganAktif
                    .toLocaleString('id-ID');

                const nama = radio.closest('.voucher-card').querySelector('.voucher-card-title')
                    ?.textContent.trim() || kode;
                label.innerHTML = '<strong>' + nama + '</strong> <i class="ri-edit-line"></i>';
            }
            document.getElementById('sum_total').textContent = 'Rp ' + (subtotalAsli - potonganAktif)
                .toLocaleString('id-ID');
        });
}

document.addEventListener('DOMContentLoaded', function() {

    // ── HAPUS ITEM ──
    document.querySelectorAll('.btn-hapus-item').forEach(btn => {
        btn.addEventListener('click', function() {
            const key = this.dataset.key;

            fetch('<?= base_url('keranjang/hapus') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        key
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) window.location.reload();
                });
        });
    });

    const btnNambah = document.getElementById('btn_nambah');
    if (btnNambah) {
        btnNambah.addEventListener('click', function() {
            window.location.href = '<?= base_url('/katalog'); ?>';

        })
    }
    // ── CHECKOUT ──
    const btnCheckout = document.getElementById('btn_checkout');
    if (btnCheckout) {
        btnCheckout.addEventListener('click', function() {
            const errorBox = document.getElementById('checkout_error');
            errorBox.style.display = 'none';
            this.disabled = true;
            this.textContent = 'Memproses...';

            fetch('<?= base_url('keranjang/checkout') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        kode_voucher: document.querySelector(
                            'input[name="voucher_pilihan"]:checked')?.value || '',
                        metode_pembayaran: document.querySelector(
                                'input[name="metode_pembayaran"]:checked')?.value ||
                            'online',
                    })
                })
                .then(res => res.json().then(data => ({
                    status: res.status,
                    body: data
                })))
                .then(({
                    status,
                    body
                }) => {
                    if (status !== 200 || !body.success) {
                        this.disabled = false;
                        this.textContent = 'Checkout';
                        errorBox.textContent = body.message || 'Gagal checkout.';
                        errorBox.style.display = 'block';

                        if (body.redirect_url) {
                            setTimeout(() => window.location.href = body.redirect_url, 1500);
                        }
                        return;
                    }

                    if (!body.has_snap) {
                        window.location.href = body.redirect_url;
                        return;
                    }

                    snap.pay(body.snap_token, {
                        onSuccess: () => window.location.href =
                            '<?= base_url('riwayatsewa') ?>?bayar=sukses',
                        onPending: () => window.location.href =
                            '<?= base_url('riwayatsewa') ?>?bayar=pending',
                        onError: () => {
                            this.disabled = false;
                            this.textContent = 'Checkout';
                            errorBox.textContent = 'Pembayaran gagal diproses.';
                            errorBox.style.display = 'block';
                        },
                        onClose: () => window.location.href = body.redirect_url,
                    });

                    this.disabled = false;
                    this.textContent = 'Checkout';
                })
                .catch(() => {
                    this.disabled = false;
                    this.textContent = 'Checkout';
                    errorBox.textContent = 'Gagal terhubung ke server.';
                    errorBox.style.display = 'block';
                });
        });
    }
});
</script>

<style>
.summary-voucher-trigger {
    cursor: pointer;
    transition: opacity .15s;
}

.summary-voucher-trigger:hover {
    opacity: 0.7;
}

.voucher-trigger-value {
    color: var(--primary, #16a34a);
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 2px;
}

.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.55);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    padding: 16px;
}

.modal-overlay.active {
    display: flex;
}

.modal-box {
    background: #fff;
    border-radius: 16px;
    max-width: 480px;
    width: 100%;
    max-height: 85vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    animation: modalPopIn .18s ease;
}

@keyframes modalPopIn {
    from {
        transform: scale(.96);
        opacity: 0;
    }

    to {
        transform: scale(1);
        opacity: 1;
    }
}

.modal-box-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 20px;
    border-bottom: 1px solid #eee;
    flex-shrink: 0;
}

.modal-box-header h3 {
    margin: 0;
    font-size: 1.05rem;
    display: flex;
    align-items: center;
    gap: 8px;
}

.modal-close-btn {
    background: none;
    border: none;
    font-size: 1.4rem;
    line-height: 1;
    cursor: pointer;
    color: #666;
    padding: 4px;
}

.modal-box-body {
    padding: 20px;
    overflow-y: auto;
}

.modal-box-footer {
    padding: 16px 20px;
    border-top: 1px solid #eee;
    flex-shrink: 0;
}
</style>

<?= $this->endSection() ?>