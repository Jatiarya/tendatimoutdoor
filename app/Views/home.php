<?= $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>
<title><?= $title; ?></title>
<!-- ── HERO ── -->
<section class="hero">
    <div class="hero-inner">
        <div class="hero-badge"><i class="ri-tent-line"></i> Sewa Alat Hiking Terpercaya</div>

        <h1>Jelajahi Alam<br><em>Tanpa Batas</em></h1>

        <!-- <p>Perlengkapan hiking berkualitas premium dengan harga terjangkau. Siap antar, siap pakai, siap mendukung
            petualanganmu.</p> -->

        <div class="hero-cta">
            <a href="/katalog" class="btn-primary">
                Sewa Sekarang
            </a>
            <a href="/katalog" class="btn-outline">
                Lihat Katalog →
            </a>
        </div>
    </div>

    <div class="hero-stats">
        <div class="stat">
            <div class="stat-num">200+</div>
            <div class="stat-label">Unit Tersedia</div>
        </div>
        <div class="stat">
            <div class="stat-num">5K+</div>
            <div class="stat-label">Pelanggan Puas</div>
        </div>
        <div class="stat">
            <div class="stat-num">4.9★</div>
            <div class="stat-label">Rating</div>
        </div>
    </div>
</section>

<!-- ── LAYANAN ── -->
<section class="layanan">
    <div class="section-label">— Apa yang Kami Tawarkan</div>
    <h2 class="section-title">Layanan Unggulan Kami</h2>

    <div class="layanan-grid">
        <div class="layanan-card">
            <div class="card-num">01</div>
            <div class="card-icon"><i class="ri-tent-line"></i></div>
            <h3>Tenda</h3>
            <p>Tersedia berbagai ukuran tenda untuk kebutuhan camping solo hingga kelompok besar. Waterproof dan tahan
                angin.</p>
        </div>

        <div class="layanan-card">
            <div class="card-num">02</div>
            <div class="card-icon"><i class="ri-bag-line"></i></div>
            <h3>Carrier</h3>
            <p>Carrier nyaman dengan rangka ergonomis dan kapasitas 30–80L untuk perjalanan pendek maupun ekspedisi
                panjang.</p>
        </div>

        <div class="layanan-card">
            <div class="card-num">03</div>
            <div class="card-icon"><i class="ri-restaurant-line"></i></div>
            <h3>Peralatan Grill</h3>
            <p>Set kompor portable, nesting pot, dan perlengkapan makan outdoor yang ringan dan efisien untuk di alam
                terbuka.</p>
        </div>
    </div>
</section>
<!-- ── LOKASI ── -->
<section class="lokasi">
    <div class="lokasi-inner">
        <div class="lokasi-info">
            <div class="section-label">— Temukan Kami</div>
            <h2 class="section-title" style="text-align:left;">Kunjungi Toko Kami</h2>
            <p class="lokasi-desc">
                Datang langsung untuk konsultasi perlengkapan atau ambil pesanan sewamu. Kami juga melayani reservasi
                online sebelum kamu datang.
            </p>

            <div class="lokasi-detail">
                <div class="lokasi-item">
                    <div class="lokasi-icon"><i class="ri-map-pin-2-line"></i></div>
                    <div>
                        <strong>Alamat</strong>
                        <span>JJl. Dane Rahil No.72, Lenek Daya, Kec. Aikmel, Kabupaten Lombok Timur, Nusa Tenggara
                            Barat</span>
                    </div>
                </div>
                <div class="lokasi-item">
                    <div class="lokasi-icon"><i class="ri-time-line"></i></div>
                    <div>
                        <strong>Jam Operasional</strong>
                        <span>Setiap Hari, 08.00 – 21.00 WITA</span>
                    </div>
                </div>
                <div class="lokasi-item">
                    <div class="lokasi-icon"><i class="ri-phone-line"></i></div>
                    <div>
                        <strong>Telepon</strong>
                        <span>+62 812-3456-7890</span>
                    </div>
                </div>
            </div>

            <a href="https://www.google.com/maps?cid=15531197313822296282&g_mp=CiVnb29nbGUubWFwcy5wbGFjZXMudjEuUGxhY2VzLkdldFBsYWNlEAMYASAF&hl=en&gl=ID&source=embed"
                target="_blank" class="btn-primary" style="margin-top:8px; display:inline-flex;">
                <i class="ri-navigation-line"></i> Dapatkan Petunjuk Arah
            </a>
        </div>

        <div class="lokasi-map">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d234.37145493326094!2d116.5118881669288!3d-8.581976551421457!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dcc4b0016ffae01%3A0xd789e5ab4ae108da!2sTTM%20outdoor%20camp!5e1!3m2!1sen!2sid!4v1786084377724!5m2!1sen!2sid"
                width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</section>

<!-- ── WHY US STRIP ── -->
<div class="why-strip">
    <div class="why-item">
        <div class="why-icon"><i class="ri-check-line"></i></div>
        <strong>Peralatan Terawat</strong>
        <span>Dicek & dibersihkan sebelum setiap sewa</span>
    </div>
    <div class="why-item">
        <div class="why-icon"><i class="ri-truck-line"></i></div>
        <strong>Antar–Jemput</strong>
        <span>Gratis ongkir area Bandung & sekitarnya</span>
    </div>
    <div class="why-item">
        <div class="why-icon"><i class="ri-message-2-line"></i></div>
        <strong>Support 24 Jam</strong>
        <span>Tim siap bantu kapan saja via WhatsApp</span>
    </div>
    <div class="why-item">
        <div class="why-icon"><i class="ri-money-dollar-circle-line"></i></div>
        <strong>Harga Terjangkau</strong>
        <span>Mulai dari Rp 15.000 / hari</span>
    </div>
</div>

<!-- ── CTA BANNER ── -->
<section class="cta-banner">
    <h2>Siap untuk <span>Petualangan</span> Berikutmu?</h2>
    <a href="/katalog" class="btn-primary" style="white-space:nowrap; font-size:1rem; padding:18px 40px;">
        Cek Semua Katalog
    </a>
</section>

<?php $this->endSection(); ?>