<?= $this->extend('layout/template'); ?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('css/public.css') ?>">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css">



<!-- ═══════════════ HERO ═══════════════ -->
<!-- ═══════════════ PAGE HEADER (konsisten dengan halaman lain) ═══════════════ -->
<div class="page-header">
    <div class="page-header-inner">
        <div class="breadcrumb">
            <a href="/">Beranda</a><span>/</span> Tentang Kami
        </div>
        <h1>Membawa Setiap Orang <span style="">Lebih Dekat</span> dengan Alam</h1>

        <div class="tt-hero-btns">
            <a href="<?= base_url('produk') ?>" class="btn-sewa"><i class="ri-shopping-bag-3-line"></i> Lihat
                Katalog</a>
            <a href="<?= base_url('kontak') ?>" class="tt-btn-ghost-light"><i class="ri-chat-3-line"></i> Hubungi
                Kami</a>
        </div>
    </div>
</div>

<!-- ═══════════════ STATS MENGAMBANG ═══════════════ -->
<div class="tt-stats">
    <div class="tt-stat">
        <div class="tt-stat-icon"><i class="ri-user-smile-line"></i></div>
        <div class="tt-stat-num">500+</div>
        <div class="tt-stat-label">Penyewa Puas</div>
    </div>
    <div class="tt-stat">
        <div class="tt-stat-icon"><i class="ri-archive-2-line"></i></div>
        <div class="tt-stat-num"><?= count($perlengkapan) ?></div>
        <div class="tt-stat-label">Jenis Perlengkapan</div>
    </div>
    <div class="tt-stat">
        <div class="tt-stat-icon"><i class="ri-mountain-line"></i></div>
        <div class="tt-stat-num">10+</div>
        <div class="tt-stat-label">Gunung Terjangkau</div>
    </div>
    <div class="tt-stat">
        <div class="tt-stat-icon"><i class="ri-star-smile-line"></i></div>
        <div class="tt-stat-num">4.9★</div>
        <div class="tt-stat-label">Rating Pelanggan</div>
    </div>
</div>

<div class="tt-section">

    <!-- ═══════════════ STORY 1: AWAL MULA ═══════════════ -->
    <div class="tt-story">
        <div class="tt-story-img">
            <img src="img/logo.jpg" alt="Awal mula Tendatim Outdoor">
            <div class="tt-badge-float">
                <div class="icon-circle"><i class="ri-calendar-check-line"></i></div>
                <div>
                    <strong>Sejak 29 April 2025</strong>
                    <small>Melayani pendaki Lombok</small>
                </div>
            </div>
        </div>
        <div class="tt-story-text">
            <span class="tt-tag">Awal Mula</span>
            <h2>Bermula dari Komunitas Kecil Pecinta Alam</h2>
            <p>Tendatim Outdoor lahir dari kecintaan terhadap alam dan keinginan membuat aktivitas hiking lebih
                terjangkau bagi siapa saja. Kami percaya, setiap orang berhak merasakan sensasi mendaki gunung dan
                berkemah tanpa harus membeli peralatan mahal yang jarang dipakai.</p>
            <p>Berawal dari beberapa set perlengkapan pribadi yang dipinjamkan ke teman-teman sesama pendaki, kini
                koleksi kami berkembang menjadi puluhan jenis perlengkapan siap sewa.</p>
            <ul class="tt-story-list">
                <li><i class="ri-checkbox-circle-fill"></i> Perlengkapan dicuci & dicek rutin</li>
                <li><i class="ri-checkbox-circle-fill"></i> Harga transparan, tanpa biaya tersembunyi</li>
                <li><i class="ri-checkbox-circle-fill"></i> Konfirmasi cepat, maksimal 1x24 jam</li>
            </ul>
        </div>
    </div>

    <!-- ═══════════════ STORY 2: MISI KAMI ═══════════════ -->
    <div class="tt-story reverse">
        <div class="tt-story-img">
            <img src="img/logo.jpg" alt="Misi Tendatim Outdoor">
            <div class="tt-badge-float">
                <div class="icon-circle"><i class="ri-heart-3-line"></i></div>
                <div>
                    <strong>100% Fokus Pelanggan</strong>
                    <small>Kepuasan penyewa prioritas kami</small>
                </div>
            </div>
        </div>
        <div class="tt-story-text">
            <span class="tt-tag">Misi Kami</span>
            <h2>Membuat Alam Bebas Lebih Mudah Diakses</h2>
            <p>Kami ingin menghilangkan hambatan biaya yang sering membuat orang mengurungkan niat mendaki. Dengan
                sistem sewa yang mudah dan online, siapa pun bisa merencanakan petualangan tanpa perlu investasi besar
                di peralatan.</p>
            <p>Setiap transaksi kami rancang seaman dan sejelas mungkin — mulai dari pengajuan online, pembayaran
                digital, hingga status penyewaan yang bisa dipantau real-time.</p>
        </div>
    </div>

    <!-- ═══════════════ TIMELINE ═══════════════ -->
    <div class="tt-timeline-wrap">
        <div class="tt-head">
            <div class="tt-eyebrow-line">Perjalanan Kami</div>
            <h2>Dari Ide Kecil Jadi Kepercayaan Banyak Orang</h2>
        </div>

        <div class="tt-timeline">
            <div class="tt-timeline-item">
                <div class="tt-timeline-dot">1</div>
                <div class="tt-timeline-year">2025</div>
                <h4>Berdiri dari Komunitas</h4>
                <p>Dimulai sebagai kegiatan pinjam-meminjam alat antar sesama pendaki di Mataram.</p>
            </div>
            <div class="tt-timeline-item">
                <div class="tt-timeline-dot">2</div>
                <div class="tt-timeline-year">2025</div>
                <h4>Ekspansi Koleksi Perlengkapan</h4>
                <p>Menambah puluhan unit tenda, sleeping bag, dan kompor portable untuk memenuhi permintaan.</p>
            </div>
            <div class="tt-timeline-item">
                <div class="tt-timeline-dot">3</div>
                <div class="tt-timeline-year">2026</div>
                <h4>Sistem Sewa Online</h4>
                <p>Meluncurkan platform online supaya proses pengajuan dan pembayaran lebih cepat dan transparan.</p>
            </div>
            <div class="tt-timeline-item">
                <div class="tt-timeline-dot"><i class="ri-flashlight-fill"></i></div>
                <div class="tt-timeline-year">Sekarang</div>
                <h4>Terus Berkembang</h4>
                <p>Melayani ratusan pendaki setiap bulan dengan koleksi yang terus bertambah dan terawat.</p>
            </div>
        </div>
    </div>

    <!-- ═══════════════ NILAI-NILAI ═══════════════ -->
    <div class="tt-head" style="padding-top: 30px;">
        <div class="tt-eyebrow-line">Kenapa Memilih Kami</div>
        <h2>Nilai yang Selalu Kami Pegang</h2>
        <p>Empat prinsip yang jadi pondasi setiap layanan yang kami berikan</p>
    </div>

    <div class="tt-values">
        <div class="tt-value-card" data-num="01">
            <div class="tt-value-icon"><i class="ri-shield-check-line"></i></div>
            <h3>Peralatan Terawat</h3>
            <p>Setiap perlengkapan dicek dan dibersihkan sebelum disewakan kembali ke penyewa berikutnya.</p>
        </div>
        <div class="tt-value-card" data-num="02">
            <div class="tt-value-icon"><i class="ri-wallet-3-line"></i></div>
            <h3>Harga Terjangkau</h3>
            <p>Sewa jauh lebih hemat dibanding membeli peralatan baru untuk sesekali dipakai.</p>
        </div>
        <div class="tt-value-card" data-num="03">
            <div class="tt-value-icon"><i class="ri-flashlight-line"></i></div>
            <h3>Proses Cepat</h3>
            <p>Ajukan sewa online, konfirmasi dalam 1x24 jam, langsung siap dipakai.</p>
        </div>
        <div class="tt-value-card" data-num="04">
            <div class="tt-value-icon"><i class="ri-hand-heart-line"></i></div>
            <h3>Terpercaya</h3>
            <p>Transparan dalam proses, dengan jaminan KTP untuk keamanan kedua belah pihak.</p>
        </div>
    </div>

    <!-- ═══════════════ CARA KERJA ═══════════════ -->
    <div class="tt-steps-wrap">
        <div class="tt-head">
            <div class="tt-eyebrow-line">Cara Kerja</div>
            <h2>Sewa dalam 4 Langkah Mudah</h2>
        </div>

        <div class="tt-steps">
            <div class="tt-step">
                <div class="tt-step-num">1</div>
                <h4>Pilih Perlengkapan</h4>
                <p>Jelajahi katalog dan pilih perlengkapan sesuai kebutuhanmu.</p>
            </div>
            <div class="tt-step">
                <div class="tt-step-num">2</div>
                <h4>Isi Tanggal Sewa</h4>
                <p>Tentukan tanggal mulai dan rencana pengembalian.</p>
            </div>
            <div class="tt-step">
                <div class="tt-step-num">3</div>
                <h4>Bayar Online</h4>
                <p>Selesaikan pembayaran lewat Midtrans dengan aman.</p>
            </div>
            <div class="tt-step">
                <div class="tt-step-num">4</div>
                <h4>Siap Berpetualang</h4>
                <p>Admin konfirmasi, perlengkapan siap kamu ambil.</p>
            </div>
        </div>
    </div>

    <!-- ═══════════════ GALERI ═══════════════ -->
    <div class="tt-head">
        <div class="tt-eyebrow-line">Galeri</div>
        <h2>Momen di Alam Bebas</h2>
        <p>Beberapa jepretan dari petualangan tim kami</p>
    </div>

    <div class="tt-gallery">
        <?php foreach ($galeri as $foto): ?>
            <div class="tt-gallery-item" onclick="bukaLightbox(this.querySelector('img').src)">
                <img src="<?= base_url('uploads/galery/' . esc($foto['foto'])) ?>" alt="<?= esc($foto['alt']) ?>">
                <div class="tt-gallery-cap"><?= $foto['nama_foto'] ?></div>
            </div>
        <?php endforeach; ?>

    </div>

</div>



<div class="tt-section">

    <!-- ═══════════════ FAQ ═══════════════ -->
    <div class="tt-faq-wrap">
        <div class="tt-head">
            <div class="tt-eyebrow-line">FAQ</div>
            <h2>Pertanyaan yang Sering Ditanyakan</h2>
        </div>

        <div class="tt-faq-item">
            <div class="tt-faq-q" onclick="toggleFaq(this)">
                Apa syarat untuk bisa menyewa perlengkapan?
                <i class="ri-arrow-down-s-line"></i>
            </div>
            <div class="tt-faq-a">
                <div class="tt-faq-a-inner">Kamu cukup mendaftar akun, mengunggah foto KTP sebagai jaminan, lalu memilih
                    perlengkapan yang ingin disewa.</div>
            </div>
        </div>
        <div class="tt-faq-item">
            <div class="tt-faq-q" onclick="toggleFaq(this)">
                Berapa lama proses konfirmasi pesanan?
                <i class="ri-arrow-down-s-line"></i>
            </div>
            <div class="tt-faq-a">
                <div class="tt-faq-a-inner">Setelah pembayaran berhasil, admin akan mengonfirmasi pesanan maksimal dalam
                    1x24 jam.</div>
            </div>
        </div>
        <div class="tt-faq-item">
            <div class="tt-faq-q" onclick="toggleFaq(this)">
                Bagaimana jika perlengkapan rusak saat disewa?
                <i class="ri-arrow-down-s-line"></i>
            </div>
            <div class="tt-faq-a">
                <div class="tt-faq-a-inner">Silakan hubungi admin melalui halaman kontak untuk penanganan lebih lanjut
                    sesuai kondisi kerusakan.</div>
            </div>
        </div>
        <div class="tt-faq-item">
            <div class="tt-faq-q" onclick="toggleFaq(this)">
                Apakah bisa menyewa lebih dari 1 jenis perlengkapan sekaligus?
                <i class="ri-arrow-down-s-line"></i>
            </div>
            <div class="tt-faq-a">
                <div class="tt-faq-a-inner">Bisa! Kamu bisa memasukkan beberapa perlengkapan ke keranjang dan
                    membayarnya sekaligus dalam satu transaksi.</div>
            </div>
        </div>
    </div>

    <!-- ═══════════════ CTA FINAL ═══════════════ -->
    <div class="tt-cta">
        <h2>Siap Memulai Petualanganmu?</h2>
        <p>Jelajahi katalog perlengkapan kami dan mulai rencanakan perjalanan berikutnya hari ini.</p>
        <div class="tt-cta-btns">
            <a href="<?= base_url('produk') ?>" class="tt-btn-primary"><i class="ri-shopping-bag-3-line"></i> Lihat
                Katalog</a>
            <a href="<?= base_url('kontak') ?>" class="tt-btn-ghost"><i class="ri-chat-3-line"></i> Hubungi Kami</a>
        </div>
    </div>

</div>

<!-- ═══════════════ LIGHTBOX ═══════════════ -->
<div class="tt-lightbox" id="ttLightbox" onclick="tutupLightbox(event)">
    <button class="tt-lightbox-close" onclick="tutupLightbox(event)">&times;</button>
    <img src="" id="ttLightboxImg" alt="Preview foto">
</div>

<script>
    function bukaLightbox(src) {
        document.getElementById('ttLightboxImg').src = src;
        document.getElementById('ttLightbox').classList.add('active');
    }

    function tutupLightbox(e) {
        if (e.target.id === 'ttLightbox' || e.target.classList.contains('tt-lightbox-close')) {
            document.getElementById('ttLightbox').classList.remove('active');
        }
    }

    function toggleFaq(el) {
        const item = el.parentElement;
        const sudahTerbuka = item.classList.contains('open');

        document.querySelectorAll('.tt-faq-item').forEach(i => i.classList.remove('open'));

        if (!sudahTerbuka) item.classList.add('open');
    }
</script>

<?= $this->endSection() ?>