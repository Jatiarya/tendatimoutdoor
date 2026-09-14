<?php
$uri     = service('uri');
$segment = $uri->getSegment(1) ?? '/dashboard';
$sub     = $uri->getSegment(2) ?? '/dashboard';

// Cek apakah halaman aktif ada di dalam grup perlengkapan
$perlengkapanPages = ['perlengkapan', 'kategori', 'diskon'];
$isPerlengkapanActive = in_array($segment, $perlengkapanPages);
?>
<link rel="stylesheet" href="layout.css">
<aside class="sidebar" id="adminSidebar">

    <!-- ── Menu Utama ── -->
    <div>
        <p class="sidebar-section-label">Menu Utama</p>

        <nav style="display:flex; flex-direction:column; gap:2px;">

            <!-- Dashboard -->
            <a href="<?= base_url('/dashboard') ?>"
                class="sidebar-link <?= $segment === 'dashboard' ? 'active' : '' ?>">
                <span class="sidebar-link-icon"><i class="ri-dashboard-3-line"></i></span>
                Dashboard
            </a>

            <!-- ── Perlengkapan (dengan dropdown) ── -->
            <div class="dropdown-wrap <?= $isPerlengkapanActive ? 'open' : '' ?>">

                <!-- Parent trigger -->
                <button type="button"
                    class="sidebar-link has-dropdown <?= $isPerlengkapanActive ? 'parent-active' : '' ?>"
                    onclick="toggleDropdown(this)" aria-expanded="<?= $isPerlengkapanActive ? 'true' : 'false' ?>">
                    <span class="sidebar-link-icon"><i class="ri-tent-line"></i></span>
                    Perlengkapan
                    <i class="ri-arrow-down-s-line sidebar-chevron <?= $isPerlengkapanActive ? 'open' : '' ?>"></i>
                </button>

                <!-- Submenu -->
                <div class="sidebar-dropdown <?= $isPerlengkapanActive ? 'open' : '' ?>" id="dropdownPerlengkapan">

                    <a href="<?= base_url('perlengkapan') ?>"
                        class="sidebar-dropdown-item <?= $segment === 'perlengkapan' ? 'active' : '' ?>">
                        Data Perlengkapan
                    </a>

                    <a href="<?= base_url('kategori') ?>"
                        class="sidebar-dropdown-item <?= $segment === 'kategori' ? 'active' : '' ?>">
                        Data Kategori
                    </a>
                    <a href="<?= base_url('diskon') ?>"
                        class="sidebar-dropdown-item <?= $segment === 'diskon' ? 'active' : '' ?>">
                        Data Diskon
                    </a>

                </div>
            </div>
            <!-- ── End Perlengkapan ── -->

            <!-- Penyewaan -->
            <a href="<?= base_url('pengguna') ?>" class="sidebar-link <?= $segment === 'pengguna' ? 'active' : '' ?>">
                <span class="sidebar-link-icon"><i class="ri-group-line"></i></span>
                Data Pengguna
            </a>
            <a href="<?= base_url('penyewaan') ?>" class="sidebar-link <?= $segment === 'penyewaan' ? 'active' : '' ?>">
                <span class="sidebar-link-icon"><i class="ri-todo-line"></i></span>
                Data Penyewaan
            </a>

            <!-- Pembayaran -->
            <a href="<?= base_url('pembayaran') ?>"
                class="sidebar-link <?= $segment === 'pembayaran' ? 'active' : '' ?>">
                <span class="sidebar-link-icon"><i class="ri-wallet-2-line"></i></span>
                Data Pembayaran
            </a>

            <!-- Laporan -->
            <a href="<?= base_url('laporan') ?>" class="sidebar-link <?= $segment === 'laporan' ? 'active' : '' ?>">
                <span class="sidebar-link-icon"><i class="ri-file-chart-line"></i></span>
                Laporan
            </a>

        </nav>
    </div>
    <div>
        <p class="sidebar-section-label">Manajemen Halaman Depan</p>
        <a href="<?= base_url('galeritentang') ?>"
            class="sidebar-link <?= $segment === 'galeritentang' ? 'active' : '' ?>">
            <span class="sidebar-link-icon"><i class="ri-folder-image-line"></i></span>
            Galeri - Tentang
        </a>
    </div>
    <!-- ── General ── -->
    <div>
        <p class="sidebar-section-label">Umum</p>

        <a href="<?= base_url('logout') ?>" class="sidebar-logout" title="Log out">
            <span class="sidebar-link-icon">
                <i class="ri-logout-box-r-line"></i>
            </span>
            Keluar
        </a>
    </div>

</aside>

<script>
function toggleDropdown(btn) {
    const wrap = btn.closest('.dropdown-wrap');
    const dropdown = btn.nextElementSibling;
    const chevron = btn.querySelector('.sidebar-chevron');
    const isOpen = dropdown.classList.contains('open');

    /* Tutup semua dropdown lain dulu */
    document.querySelectorAll('.sidebar-dropdown.open').forEach(el => {
        if (el !== dropdown) {
            el.classList.remove('open');
            el.closest('.dropdown-wrap')?.classList.remove('open');
            el.previousElementSibling?.querySelector('.sidebar-chevron')?.classList.remove('open');
            el.previousElementSibling?.classList.remove('parent-active');
            el.previousElementSibling?.setAttribute('aria-expanded', 'false');
        }
    });

    /* Toggle dropdown yang diklik */
    dropdown.classList.toggle('open', !isOpen);
    wrap.classList.toggle('open', !isOpen);
    chevron.classList.toggle('open', !isOpen);
    btn.classList.toggle('parent-active', !isOpen);
    btn.setAttribute('aria-expanded', String(!isOpen));
}
</script>