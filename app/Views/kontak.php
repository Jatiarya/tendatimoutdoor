<?= $this->extend('layout/template'); ?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('css/public.css') ?>">

<!-- ── PAGE HEADER ── -->
<div class="page-header">
    <div class="page-header-inner">
        <div class="breadcrumb">
            <a href="/">Beranda</a><span>/</span> Kontak
        </div>
        <h1>Hubungi Kami</h1>
        <p>Ada pertanyaan? Kami siap membantu</p>
    </div>
</div>

<!-- ── FLASH MESSAGE ── -->
<?php if (session()->getFlashdata('success')) : ?>
<div class="php-alert success" style="max-width:900px;margin:16px auto 0;">
    ✓ <?= esc(session()->getFlashdata('success')) ?>
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

<div class="sewa-wrapper">

    <!-- ══ LEFT: FORM KONTAK ══ -->
    <div class="sewa-form-col">
        <div class="form-card">
            <div class="form-card-header">
                <div class="step-badge"><i class="ri-mail-line"></i></div>
                <div>
                    <h2>Kirim Pesan</h2>
                    <p>Isi form di bawah, kami akan segera membalas</p>
                </div>
            </div>

            <form action="<?= base_url('kontak/kirim') ?>" method="post" novalidate>
                <?= csrf_field() ?>

                <div class="field-group">
                    <label for="nama">Nama Lengkap <span class="req">*</span></label>
                    <input type="text" id="nama" name="nama" value="<?= old('nama') ?>" required minlength="3">
                </div>

                <div class="field-group">
                    <label for="email">Email <span class="req">*</span></label>
                    <input type="email" id="email" name="email" value="<?= old('email') ?>" required>
                </div>

                <div class="field-group">
                    <label for="telepon">Nomor Telepon (opsional)</label>
                    <input type="tel" id="telepon" name="telepon" value="<?= old('telepon') ?>"
                        placeholder="08xxxxxxxxxx">
                </div>

                <div class="field-group">
                    <label for="pesan">Pesan <span class="req">*</span></label>
                    <textarea id="pesan" name="pesan" rows="5" required minlength="10"
                        placeholder="Tulis pertanyaan atau pesanmu di sini..."><?= old('pesan') ?></textarea>
                </div>

                <button type="submit" class="btn-submit" style="width:auto; padding:12px 28px; margin-top: 10px;">
                    <i class="ri-send-plane-line"></i> Kirim Pesan
                </button>
            </form>
        </div>
    </div>

    <!-- ══ RIGHT: INFO KONTAK ══ -->
    <aside class="sewa-summary">
        <div class="summary-card">
            <div class="summary-title">Info Kontak</div>

            <div class="summary-rows">
                <div class="summary-row" style="flex-direction:column; align-items:flex-start; gap:4px;">
                    <span style="color:var(--stone); font-size:0.8rem; text-transform:uppercase;">Alamat</span>
                    <span>Jl. Dane Rahil No.72, Lenek Daya, Kec. Aikmel, Kabupaten Lombok Timur, Nusa Tengga</span>
                </div>
                <div class="summary-divider"></div>
                <div class="summary-row" style="flex-direction:column; align-items:flex-start; gap:4px;">
                    <span style="color:var(--stone); font-size:0.8rem; text-transform:uppercase;">Telepon</span>
                    <span>+62 812-3456-7890</span>
                </div>
                <div class="summary-divider"></div>
                <div class="summary-row" style="flex-direction:column; align-items:flex-start; gap:4px;">
                    <span style="color:var(--stone); font-size:0.8rem; text-transform:uppercase;">Media Sosial</span>
                    <span><i class="ri-mail-line"></i> hello@tendatimoutdoor.com</span>
                    <span><a href="https://www.instagram.com/tendatim_outdoor/" target="_blank"><i
                                class="ri-instagram-line"></i>@tendatim_outdoor</a></span>
                </div>
                <div class="summary-divider"></div>
                <div class="summary-row" style="flex-direction:column; align-items:flex-start; gap:4px;">
                    <span style="color:var(--stone); font-size:0.8rem; text-transform:uppercase;">Jam Operasional</span>
                    <span>Setiap Hari, 08.00 – 21.00 WIB</span>
                </div>
            </div>
        </div>

        <!-- Kontak Cepat -->
        <div class="quick-contact">
            <div class="qc-title">Butuh Respons Cepat?</div>
            <a href="https://wa.me/6281234567890" target="_blank" class="qc-wa">
                <span>💬</span> Chat WhatsApp Admin
            </a>
            <div class="qc-hours">Kami biasanya membalas dalam beberapa menit</div>
        </div>
    </aside>

</div>

<?= $this->endSection() ?>