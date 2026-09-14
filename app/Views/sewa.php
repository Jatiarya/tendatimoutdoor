<?= $this->extend('layout/template'); ?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('css/public.css') ?>">

<!-- ── PAGE HEADER ── -->
<div class="page-header">
    <div class="page-header-inner">
        <div class="breadcrumb">
            <a href="/">Beranda</a><span>/</span>
            <a href="/perlengkapan">Perlengkapan</a><span>/</span>
            Form Penyewaan
        </div>
        <h1>Form Penyewaan</h1>
        <p>Lengkapi data di bawah untuk memproses penyewaan alat hikingmu</p>
    </div>
</div>

<!-- ── FLASH MESSAGE ── -->
<?php if (session()->getFlashdata('error')) : ?>
<div class="php-alert error" style="max-width:900px;margin:16px auto 0;">
    ⚠ <?= esc(session()->getFlashdata('error')) ?>
</div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')) : ?>
<div class="php-alert error" style="max-width:900px;margin:16px auto 0;">
    <ul style="margin:0; padding-left:18px;">
        <?php foreach (session()->getFlashdata('errors') as $err) : ?>
        <li>⚠ <?= esc($err) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<!-- ── MAIN ── -->
<div class="sewa-wrapper">

    <div class="sewa-form-col">
        <div class="form-card">
            <div class="form-card-header">
                <div class="step-badge">01</div>
                <div>
                    <h2>Jumlah dan Tanggal Sewa</h2>
                    <p>Tentukan berapa unit dan berapa lama kamu menyewa</p>
                </div>
            </div>

            <div class="field-group">
                <label for="jumlah">Jumlah Unit <span class="req">*</span></label>
                <input type="number" id="jumlah" name="jumlah" min="1" max="<?= $perlengkapan['stok'] ?>" value="1"
                    required oninput="hitungDurasi()">
                <span class="field-hint">Maks. <?= $perlengkapan['stok'] ?> unit sesuai stok tersedia</span>
            </div>

            <div class="date-row">
                <div class="field-group">
                    <label for="tgl_sewa"><i class="ri-calendar-line"></i> Tanggal Sewa</label>
                    <input type="date" id="tgl_sewa" name="tgl_sewa" min="<?= date('Y-m-d') ?>" required
                        oninput="hitungDurasi()">
                    <span class="field-hint">Tanggal paling cepat: hari ini</span>
                </div>
                <div class="date-arrow">→</div>
                <div class="field-group">
                    <label for="tgl_kembali_rencana"><i class="ri-calendar-line"></i> Rencana Kembali</label>
                    <input type="date" id="tgl_kembali_rencana" name="tgl_kembali_rencana"
                        min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required oninput="hitungDurasi()">
                    <span class="field-hint">Minimal 1 hari sewa</span>
                </div>
            </div>

            <!-- Estimasi ringkasan -->
            <div class="durasi-strip" id="durasi_strip" style="display:none">
                <div class="durasi-item">
                    <span class="durasi-label">Durasi</span>
                    <span class="durasi-val" id="durasi_hari">—</span>
                </div>
                <div class="durasi-item">
                    <span class="durasi-label">Harga/Hari</span>
                    <span class="durasi-val">Rp <?= number_format($perlengkapan['harga_sewa'], 0, ',', '.') ?></span>
                </div>
                <div class="durasi-item durasi-total">
                    <span class="durasi-label">Estimasi Total</span>
                    <span class="durasi-val" id="estimasi_total">—</span>
                </div>
            </div>
        </div>
        <div class="form-card">
            <div class="form-card-header">
                <div class="step-badge">02</div>
                <div>
                    <h2>Produk yang Disewa</h2>
                    <p>Produk dipilih otomatis dari halaman katalog</p>
                </div>
            </div>

            <div class="perlengkapan-selected">
                <div class="perlengkapan-selected-img">
                    <img src="/uploads/perlengkapan/<?= esc($perlengkapan['foto_perlengkapan']) ?>"
                        alt="<?= esc($perlengkapan['nama_perlengkapan']) ?>"
                        onerror="this.style.background='#e8f0e8';this.removeAttribute('src')">
                </div>
                <div class="perlengkapan-selected-info">
                    <div class="perlengkapan-selected-name">
                        <strong><?= esc($perlengkapan['nama_perlengkapan']) ?></strong>
                    </div>
                    <div class="perlengkapan-selected-price"> <strong>Rp
                            <?= number_format($perlengkapan['harga_sewa'], 0, ',', '.') ?>
                            <span>/ hari</span></strong>

                    </div>
                    <div class="stok-badge <?= $perlengkapan['stok'] > 0 ? 'stok-ok' : 'stok-habis' ?>">
                        <?= $perlengkapan['stok'] > 0
                            ? '✓ Stok tersedia (' . $perlengkapan['stok'] . ' unit)'
                            : '✗ Stok habis' ?>
                    </div>
                </div>
                <a href="/katalog" class="ganti-perlengkapan-btn btn-primary mt-2">Ganti Produk</a>
            </div>

            <!-- Dikirim ke controller. id_user TIDAK dikirim dari form —
                 diambil langsung dari session di server untuk keamanan. -->
            <input type="hidden" name="id_perlengkapan" value="<?= $perlengkapan['id_perlengkapan'] ?>">
            <input type="hidden" name="harga_per_hari" id="harga_per_hari" value="<?= $perlengkapan['harga_sewa'] ?>">
            <input type="hidden" name="stok_tersedia" id="stok_tersedia" value="<?= $perlengkapan['stok'] ?>">
        </div>

        <!-- 2. JUMLAH & TANGGAL SEWA -->

    </div><!-- /sewa-form-col -->

    <!-- ══ RIGHT COLUMN: SUMMARY ══ -->
    <aside class="sewa-summary">
        <div class="summary-card">
            <div class="summary-title">Ringkasan Pesanan</div>

            <div class="summary-perlengkapan">
                <img src="/uploads/perlengkapan/<?= esc($perlengkapan['foto_perlengkapan']) ?>"
                    alt="<?= esc($perlengkapan['nama_perlengkapan']) ?>" onerror="this.style.display='none'">
                <div>
                    <div class="summary-perlengkapan-name"><?= esc($perlengkapan['nama_perlengkapan']) ?></div>
                    <div class="summary-perlengkapan-price">Rp
                        <?= number_format($perlengkapan['harga_sewa'], 0, ',', '.') ?> / hari</div>
                </div>
            </div>

            <div class="summary-rows">
                <div class="summary-row">
                    <span>Jumlah</span>
                    <span id="sum_jumlah">1 unit</span>
                </div>
                <div class="summary-row">
                    <span>Tanggal Sewa</span>
                    <span id="sum_mulai">—</span>
                </div>
                <div class="summary-row">
                    <span>Rencana Kembali</span>
                    <span id="sum_selesai">—</span>
                </div>
                <div class="summary-row">
                    <span>Durasi</span>
                    <span id="sum_durasi">—</span>
                </div>
                <div class="summary-divider"></div>

                <!-- ── TRIGGER MODAL VOUCHER ── -->
                <div class="summary-row summary-voucher-trigger" onclick="bukaModalVoucher()">
                    <span><i class="ri-coupon-3-line"></i> Voucher</span>
                    <span class="voucher-trigger-value" id="voucher_trigger_label">
                        Pilih Voucher <i class="ri-arrow-right-s-line"></i>
                    </span>
                </div>

                <div class="summary-divider"></div>
                <div class="summary-row summary-total">
                    <span>Estimasi Total</span>
                    <span id="sum_total">—</span>
                </div>
                <div class="summary-row" id="row_diskon" style="display:none;">
                    <span>Diskon</span>
                    <span id="sum_diskon" style="color:#16a34a;">-Rp 0</span>
                </div>
                <div class="summary-divider"></div>

                <!-- ── METODE PEMBAYARAN ── -->
                <div class="field-group">
                    <label>Metode Pembayaran</label>
                    <div class="voucher-list">
                        <label class="voucher-card">
                            <input type="radio" name="metode_pembayaran" value="online" checked
                                onchange="pilihMetodeBayar(this)">
                            <div class="voucher-card-body">
                                <div class="text-white"><i class="ri-wallet-line"></i> Bayar Online (Midtrans)</div>
                                <div class="voucher-card-desc">Kartu, E-Wallet, QRIS, VA</div>
                            </div>
                        </label>
                        <label class="voucher-card">
                            <input type="radio" name="metode_pembayaran" value="cod" onchange="pilihMetodeBayar(this)">
                            <div class="voucher-card-body">
                                <div class="text-white"><i class="ri-truck-line"></i> COD (Bayar di Tempat)</div>
                                <div class="voucher-card-desc">Bayar tunai saat barang diambil/diantar</div>
                            </div>
                        </label>
                    </div>
                </div>

            </div>

            <button type="submit" form="form-sewa" class="btn-submit" id="btn_submit">
                <i class="ri-wallet-line"></i> Ajukan Penyewaan
            </button>

            <div class="summary-disclaimer">
                Pesananmu akan berstatus <strong>menunggu konfirmasi</strong> sampai admin memverifikasi data & KTP
                kamu. Kamu bisa memantau statusnya di <a href="<?= base_url('riwayat-sewa') ?>">Riwayat Sewa</a>.
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

    <!-- ══ MODAL PILIH VOUCHER ══ -->
    <div class="modal-overlay" id="modal_voucher_overlay" onclick="if(event.target===this) tutupModalVoucher()">
        <div class="modal-box">
            <div class="modal-box-header">
                <h3><i class="ri-coupon-3-line"></i> Pilih Voucher</h3>
                <button type="button" class="modal-close-btn" onclick="tutupModalVoucher()">
                    <i class="ri-close-line"></i>
                </button>
            </div>

            <div class="modal-box-body">
                <?php if ($diskonOtomatis): ?>
                <div class="stok-badge stok-ok" style="font-size:0.85rem; padding:12px 16px; margin-bottom:16px;">
                    🎉 Event aktif: <strong><?= esc($diskonOtomatis['nama']) ?></strong> —
                    <?= $diskonOtomatis['tipe'] === 'persen' ? esc($diskonOtomatis['nilai']) . '%' : 'Rp ' . number_format($diskonOtomatis['nilai'], 0, ',', '.') ?>
                    otomatis diterapkan!
                </div>
                <?php endif; ?>

                <?php if (empty($voucherTersedia)) : ?>
                <p style="color:var(--stone); font-size:0.85rem;">Belum ada voucher tersedia untuk produk ini saat
                    ini.</p>
                <?php else : ?>
                <div class="voucher-list">
                    <label class="voucher-card voucher-card-none">
                        <input type="radio" name="voucher_pilihan" value="" checked onchange="pilihVoucher(this)">
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
                            onchange="pilihVoucher(this)">
                        <div class="voucher-card-body">
                            <div class="voucher-card-badge"><?= esc($labelNilai) ?> OFF</div>
                            <div class="voucher-card-title"><?= esc($v['nama']) ?></div>
                            <div class="voucher-card-desc">
                                Kode: <strong><?= esc($v['kode_voucher']) ?></strong>
                                <?php if (! empty($v['minimal_belanja'])) : ?>
                                <br>Min. belanja Rp <?= number_format($v['minimal_belanja'], 0, ',', '.') ?>
                                <?php endif; ?>
                                <?php if (! empty($v['maks_potongan']) && $v['tipe'] === 'persen') : ?>
                                <br>Maks. potongan Rp <?= number_format($v['maks_potongan'], 0, ',', '.') ?>
                                <?php endif; ?>
                            </div>
                            <div class="voucher-card-periode">Berlaku s/d
                                <?= esc(date('d M Y', strtotime($v['tgl_selesai']))) ?></div>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Ini yang benar-benar dikirim ke server saat submit form -->
                <input type="hidden" id="kode_voucher" name="kode_voucher" value="">

                <div id="voucher_message" style="margin-top:10px; font-size:0.85rem;"></div>
            </div>

            <div class="modal-box-footer">
                <button type="button" class="btn-submit" onclick="tutupModalVoucher()">Terapkan</button>
            </div>
        </div>
    </div>

</div><!-- /sewa-wrapper -->

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
<script src="https://app.<?= $isProduction ? '' : 'sandbox.' ?>midtrans.com/snap/snap.js"
    data-client-key="<?= esc($clientKey) ?>"></script>

<!-- Bungkus form (aksi POST ke controller) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const wrapper = document.querySelector('.sewa-wrapper');
    const form = document.createElement('form');
    form.id = 'form-sewa';
    form.method = 'POST';
    form.action = '<?= base_url('sewa/store') ?>';
    form.enctype = 'multipart/form-data';
    wrapper.parentNode.insertBefore(form, wrapper);
    form.appendChild(wrapper);

    // ── SUBMIT VIA AJAX, LANGSUNG BUKA POPUP SNAP ──
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const btn = document.getElementById('btn_submit');
        const originalText = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Memproses...';

        clearErrorAlert();

        const formData = new FormData(form);

        fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
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
                    btn.disabled = false;
                    btn.textContent = originalText;

                    if (body.errors) {
                        showErrorAlert(Object.values(body.errors));
                    } else {
                        showErrorAlert([body.message || 'Terjadi kesalahan, silakan coba lagi.']);
                    }
                    return;
                }

                if (!body.has_snap) {
                    // Gagal bikin transaksi Midtrans, arahkan ke halaman pembayaran manual
                    window.location.href = body.redirect_url;
                    return;
                }

                // Langsung buka popup Snap, tanpa pindah halaman
                snap.pay(body.snap_token, {
                    onSuccess: function() {
                        window.location.href =
                            '<?= base_url('riwayatsewa') ?>?bayar=sukses';
                    },
                    onPending: function() {
                        window.location.href =
                            '<?= base_url('riwayatsewa') ?>?bayar=pending';
                    },
                    onError: function() {
                        btn.disabled = false;
                        btn.textContent = originalText;
                        showErrorAlert([
                            'Pembayaran gagal diproses. Silakan coba lagi.'
                        ]);
                    },
                    onClose: function() {
                        // User nutup popup tanpa selesai bayar.
                        // Sewa & transaksi tetap tersimpan (status pending),
                        // arahkan ke halaman pembayaran supaya bisa lanjut bayar nanti.
                        window.location.href = body.redirect_url;
                    }
                });

                btn.disabled = false;
                btn.textContent = originalText;
            })
            .catch(() => {
                btn.disabled = false;
                btn.textContent = originalText;
                showErrorAlert(['Gagal terhubung ke server. Periksa koneksi internet kamu.']);
            });
    });
});

function showErrorAlert(messages) {
    let box = document.getElementById('js-error-alert');
    if (!box) {
        box = document.createElement('div');
        box.id = 'js-error-alert';
        box.className = 'php-alert error';
        box.style.maxWidth = '900px';
        box.style.margin = '16px auto 0';
        document.querySelector('.page-header').insertAdjacentElement('afterend', box);
    }
    box.innerHTML = '<ul style="margin:0; padding-left:18px;">' +
        messages.map(m => `<li>⚠ ${m}</li>`).join('') +
        '</ul>';
    box.scrollIntoView({
        behavior: 'smooth',
        block: 'center'
    });
}

function clearErrorAlert() {
    const box = document.getElementById('js-error-alert');
    if (box) box.remove();
}

let potonganAktif = 0;
const diskonOtomatisNilai = <?= $diskonOtomatis ? json_encode($diskonOtomatis) : 'null' ?>;

function hitungPotonganOtomatis(subtotal) {
    if (!diskonOtomatisNilai) return 0;
    let potongan = diskonOtomatisNilai.tipe === 'persen' ?
        subtotal * (parseFloat(diskonOtomatisNilai.nilai) / 100) :
        parseFloat(diskonOtomatisNilai.nilai);
    if (diskonOtomatisNilai.maks_potongan) {
        potongan = Math.min(potongan, parseFloat(diskonOtomatisNilai.maks_potongan));
    }
    return Math.min(potongan, subtotal);
}

function hitungDurasi() {
    const jumlah = parseInt(document.getElementById('jumlah').value) || 0;
    const mulai = document.getElementById('tgl_sewa').value;
    const selesai = document.getElementById('tgl_kembali_rencana').value;
    const hargaHari = parseInt(document.getElementById('harga_per_hari').value) || 0;

    document.getElementById('sum_jumlah').textContent = jumlah + ' unit';
    document.getElementById('sum_mulai').textContent = mulai || '—';
    document.getElementById('sum_selesai').textContent = selesai || '—';

    if (!mulai || !selesai) return;

    const durasiHari = Math.round((new Date(selesai) - new Date(mulai)) / 86400000);
    if (durasiHari < 1) return;

    const subtotal = durasiHari * hargaHari * jumlah;
    const kodeVoucherAktif = document.getElementById('kode_voucher').value;

    if (kodeVoucherAktif) {
        // Ada voucher dipilih -> minta server validasi & hitung ulang potongan
        fetch('<?= base_url('sewa/cek-voucher') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    kode_voucher: kodeVoucherAktif,
                    id_perlengkapan: '<?= $perlengkapan['id_perlengkapan'] ?>',
                    subtotal: subtotal,
                })
            })
            .then(res => res.json())
            .then(data => {
                const messageBox = document.getElementById('voucher_message');
                if (!data.valid) {
                    potonganAktif = 0;
                    messageBox.textContent = data.message;
                    messageBox.style.color = '#dc2626';
                } else {
                    potonganAktif = data.potongan;
                    messageBox.textContent = data.message;
                    messageBox.style.color = '#16a34a';
                }
                perbaruiRingkasan(subtotal, durasiHari);
            });
    } else {
        potonganAktif = diskonOtomatisNilai ? hitungPotonganOtomatis(subtotal) : 0;
        perbaruiRingkasan(subtotal, durasiHari);
    }
}

function perbaruiRingkasan(subtotal, durasiHari) {
    const total = subtotal - potonganAktif;

    document.getElementById('durasi_strip').style.display = 'flex';
    document.getElementById('durasi_hari').textContent = durasiHari + ' hari';
    document.getElementById('estimasi_total').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('sum_durasi').textContent = durasiHari + ' hari';

    if (potonganAktif > 0) {
        document.getElementById('row_diskon').style.display = 'flex';
        document.getElementById('sum_diskon').textContent = '-Rp ' + potonganAktif.toLocaleString('id-ID');
    } else {
        document.getElementById('row_diskon').style.display = 'none';
    }

    document.getElementById('sum_total').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

function pilihMetodeBayar(radio) {
    const isCod = radio.value === 'cod';

    const btn = document.getElementById('btn_submit');
    btn.innerHTML = isCod ?
        '<i class="ri-shopping-cart-line"></i> Ajukan Penyewaan (COD)' :
        '<i class="ri-wallet-line"></i> Ajukan Penyewaan';
}

function bukaModalVoucher() {
    document.getElementById('modal_voucher_overlay').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function tutupModalVoucher() {
    document.getElementById('modal_voucher_overlay').classList.remove('active');
    document.body.style.overflow = '';
}

function pilihVoucher(radio) {
    document.getElementById('kode_voucher').value = radio.value;

    const label = document.getElementById('voucher_trigger_label');
    if (!radio.value) {
        document.getElementById('voucher_message').textContent = '';
        label.innerHTML = 'Pilih Voucher <i class="ri-arrow-right-s-line"></i>';
    } else {
        const nama = radio.closest('.voucher-card').querySelector('.voucher-card-title')?.textContent.trim() ||
            radio.value;
        label.innerHTML = '<strong>' + nama + '</strong> <i class="ri-edit-line"></i>';
    }

    hitungDurasi();
}

function previewKTP(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];

    document.getElementById('upload_idle').style.display = 'none';
    document.getElementById('upload_preview').style.display = 'flex';
    document.getElementById('preview_filename').textContent = file.name;
    document.getElementById('preview_filesize').textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';

    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('ktp_preview_img').src = e.target.result;
        reader.readAsDataURL(file);
    }
}

function resetUpload() {
    document.getElementById('foto_ktp').value = '';
    document.getElementById('upload_idle').style.display = 'block';
    document.getElementById('upload_preview').style.display = 'none';
}

function handleDrop(event) {
    event.preventDefault();
    document.getElementById('upload_zone').classList.remove('drag-over');
    const input = document.getElementById('foto_ktp');
    input.files = event.dataTransfer.files;
    previewKTP(input);
}
</script>

<?= $this->endSection() ?>