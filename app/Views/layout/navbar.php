<style>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500&display=swap');
@import url("https://fonts.googleapis.com/css2?family=Carter+One&family=Poppins:wght@300;400;500;600;700&display=swap");

:root {
    --forest: #1a2e1a;
    --moss: #2d4a2d;
    --pine: #3d6b3d;
    --sage: #7aaa6a;
    --amber: #e8a020;
    --cream: #f5f0e8;
    --stone: #8a8070;
    --primary: #53cbf3;

}

.navbar {
    position: sticky;
    top: 0;
    z-index: 999;
    background: rgba(83, 203, 243, 0.92);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border-bottom: 1px solid rgba(184, 242, 235, 0.15);
    padding: 0 8vw;
    display: flex;
    justify-content: space-between;
    align-items: center;
    height: 68px;
    font-family: 'DM Sans', sans-serif;
}

/* ── LOGO ── */
.navbar-brand {
    font-family: "Carter One", system-ui;
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    color: white;
}

.navbar-brand .brand-icon img {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, var(--pine), var(--sage));
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.navbar-brand .brand-text {
    display: flex;
    flex-direction: column;
    line-height: 1;
}

.navbar-brand .brand-name {
    font-family: 'Playfair Display', serif;
    font-weight: 700;
    font-size: 1.1rem;
    letter-spacing: -0.01em;
    color: black;
}

.navbar-brand .brand-tagline {
    font-size: 0.65rem;
    color: var(--forest);
    letter-spacing: 0.1em;
    text-transform: uppercase;
    margin-top: 2px;
}

/* ── NAV LINKS ── */
.navbar-links {
    display: flex;
    align-items: center;
    gap: 4px;
    list-style: none;
}

.navbar-links a {
    display: inline-flex;
    align-items: center;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 0.88rem;
    font-weight: 400;
    color: white;
    text-decoration: none;
    letter-spacing: 0.01em;
    transition: color 0.2s, background 0.2s;
    position: relative;
}

.navbar-links a:hover {
    color: var(--cream);
    background: rgba(122, 170, 106, 0.12);
}

.navbar-links a.active {
    color: black;
    font-weight: 500;
}

.navbar-links a.active::after {
    content: '';
    position: absolute;
    bottom: 4px;
    left: 50%;
    transform: translateX(-50%);
    width: 4px;
    height: 4px;
    background: rgb(255, 230, 0);
    border-radius: 50%;
}

/* ── CTA BUTTON ── */
.navbar-cta {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: rgb(255, 230, 0);
    color: black !important;
    font-weight: 500 !important;
    padding: 9px 20px !important;
    border-radius: 8px;
    font-size: 0.85rem !important;
    transition: transform 0.2s, box-shadow 0.2s !important;
    margin-left: 8px;
}

.navbar-cta:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 6px 20px rgba(237, 221, 83, 0.4);
    background: var(--primary) !important;
}

/* ── USER INFO ── */
.navbar-user {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    font-size: 0.85rem;
    color: black;
}

/* ── CART BUTTON (ikon keranjang) ── */
.cart-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.35);
    color: var(--forest);
    text-decoration: none;
    font-size: 1.15rem;
    transition: background 0.2s, transform 0.2s;
    position: relative;
    flex-shrink: 0;
}

.cart-btn:hover {
    background: rgba(255, 255, 255, 0.55);
    transform: translateY(-1px);
    color: var(--forest);
}

/* ── WRAPPER: hamburger + cart (mobile), selalu terlihat di navbar utama ── */
.navbar-mobile-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Cart versi mobile (di luar hamburger) disembunyikan default di desktop */
.cart-btn-mobile {
    display: none;
}

/* ── HAMBURGER (Mobile) ── */
.navbar-toggle {
    display: none;
    flex-direction: column;
    gap: 5px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 4px;
}

.navbar-toggle span {
    display: block;
    width: 24px;
    height: 2px;
    background: var(--cream);
    border-radius: 2px;
    transition: transform 0.3s, opacity 0.3s;
}

.navbar-toggle.open span:nth-child(1) {
    transform: translateY(7px) rotate(45deg);
}

.navbar-toggle.open span:nth-child(2) {
    opacity: 0;
}

.navbar-toggle.open span:nth-child(3) {
    transform: translateY(-7px) rotate(-45deg);
}

/* ── USER DROPDOWN ── */
.navbar-user-wrap {
    position: relative;
}

.navbar-user-trigger {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 9px 20px;
    border-radius: 10px;
    font-size: 0.88rem;
    color: black;
    cursor: pointer;
    background: rgb(255, 230, 0);
    border: none;
    font-family: 'DM Sans', sans-serif;
    transition: background 0.2s;
}

.navbar-user-trigger:hover {
    background: rgba(122, 170, 106, 0.12);
}

.navbar-user-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--pine), var(--sage));
    color: var(--cream);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: 600;
    flex-shrink: 0;
    text-transform: uppercase;
}

.navbar-user-chevron {
    font-size: 0.65rem;
    transition: transform 0.2s;
}

.navbar-user-wrap.open .navbar-user-chevron {
    transform: rotate(180deg);
}

.navbar-user-dropdown {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    min-width: 200px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 12px 32px rgba(26, 46, 26, 0.18);
    padding: 8px;
    display: none;
    flex-direction: column;
    gap: 2px;
    z-index: 1000;
}

.navbar-user-wrap.open .navbar-user-dropdown {
    display: flex;
}

.navbar-user-dropdown a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    border-radius: 8px;
    font-size: 0.88rem;
    color: var(--forest);
    text-decoration: none;
    transition: background 0.2s;
}

.navbar-user-dropdown a:hover {
    background: rgba(83, 203, 243, 0.12);
}

.navbar-user-dropdown a.logout-link {
    color: #e74c3c;
}

.navbar-user-dropdown a.logout-link:hover {
    background: rgba(231, 76, 60, 0.1);
}

.navbar-user-dropdown-divider {
    height: 1px;
    background: rgba(138, 128, 112, 0.15);
    margin: 4px 0;
}

/* ── MOBILE ── */
@media (max-width: 768px) {
    .navbar {
        padding: 0 6vw;
    }

    .navbar-toggle {
        display: flex;
    }

    /* Tampilkan cart mandiri di navbar utama, sembunyikan yang dobel di dalam menu */
    .cart-btn-mobile {
        display: inline-flex;
    }

    .navbar-links .cart-btn-desktop-only {
        display: none;
    }

    .navbar-links {
        display: none;
        position: absolute;
        top: 68px;
        left: 0;
        right: 0;
        background: #53cbf3;
        backdrop-filter: blur(16px);
        flex-direction: column;
        align-items: stretch;
        gap: 0;
        padding: 12px 6vw 20px;
        border-bottom: 1px solid rgba(122, 170, 106, 0.15);
    }

    .navbar-links.open {
        display: flex;
    }

    .navbar-links a {
        padding: 13px 12px;
        border-radius: 10px;
        font-size: 0.95rem;
    }

    .navbar-cta {
        margin-left: 0 !important;
        margin-top: 8px;
        justify-content: center;
    }

    .brand-tagline {
        display: none;
    }

    .navbar-user-wrap {
        width: 100%;
    }

    .navbar-user-trigger {
        width: 100%;
        justify-content: space-between;
        padding: 10px 12px;
        margin-top: 5px;
    }

    .navbar-user-dropdown {
        position: static;
        box-shadow: none;
        background: rgba(255, 255, 255, 0.5);
        margin-top: 4px;
    }
}
</style>

<!-- ── NAVBAR ── -->
<nav class="navbar">
    <a href="/" class="navbar-brand">
        <div class="brand-icon"><img src="/img/logo.jpg" alt=""></div>
        <div class="brand-text">
            <span class="navbar-brand-text">TendaTim <span class="navbar-logo-sub">Outdoor</span></span>
        </div>
    </a>

    <!-- ── Wrapper aksi mobile: Cart + Hamburger, SELALU terlihat di navbar utama ── -->
    <div class="navbar-mobile-actions">
        <?php if (session()->get('isLoggedIn')) : ?>
        <a href="<?= base_url('/keranjang') ?>" class="cart-btn cart-btn-mobile" aria-label="Keranjang">
            <i class="ri-shopping-cart-line"></i>
        </a>
        <?php endif; ?>

        <button class="navbar-toggle" id="navToggle" aria-label="Toggle menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>

    <!-- Links -->
    <ul class="navbar-links" id="navLinks">
        <li><a href="/">Beranda</a></li>
        <li><a href="/katalog">Katalog</a></li>
        <li><a href="/tentang">Tentang</a></li>
        <li><a href="/kontak">Kontak</a></li>
        <li><a href="/katalog" class="navbar-cta">Sewa Sekarang</a></li>
        <?php if (! session()->get('isLoggedIn')) : ?>
        <!-- Belum login → tampilkan tombol Login -->
        <li><a href="<?= base_url('/login') ?>" class="navbar-cta">Login</a></li>
        <?php else :
            $username = session()->get('username');
            $inisial  = strtoupper(substr($username, 0, 1));
        ?>
        <!-- Sudah login → dropdown user -->
        <li class="navbar-user-wrap" id="userDropdownWrap">
            <button type="button" class="navbar-user-trigger" id="userDropdownTrigger">
                <span><i class="ri-user-line"></i></span>
                <span class="navbar-user-chevron">▾</span>
            </button>
            <div class="navbar-user-dropdown">
                <a href="<?= base_url('/profile') ?>">
                    <i class="ri-user-line"></i> Profile
                </a>
                <a href="<?= base_url('/riwayatsewa') ?>">
                    <i class="ri-history-line"></i> Riwayat Sewa
                </a>
                <div class="navbar-user-dropdown-divider"></div>
                <a href="<?= base_url('/logout') ?>" class="logout-link">
                    <i class="ri-logout-box-r-line"></i> Logout
                </a>
            </div>
        </li>
        <!-- Cart versi desktop (di dalam menu, disembunyikan otomatis saat mobile lewat CSS) -->
        <li class="cart-btn-desktop-only">
            <a href="<?= base_url('/keranjang') ?>" class="cart-btn" aria-label="Keranjang">
                <i class="ri-shopping-cart-line"></i>
            </a>
        </li>
        <?php endif; ?>
    </ul>
</nav>

<script>
// Toggle mobile menu
const toggle = document.getElementById('navToggle');
const links = document.getElementById('navLinks');
toggle.addEventListener('click', () => {
    toggle.classList.toggle('open');
    links.classList.toggle('open');
});

// Auto-highlight active link
const currentPath = window.location.pathname.split('/').pop() || 'Home';
document.querySelectorAll('.navbar-links a:not(.navbar-cta)').forEach(a => {
    if (a.getAttribute('href') === currentPath) a.classList.add('active');
});

// Toggle dropdown user
const userWrap = document.getElementById('userDropdownWrap');
const userTrigger = document.getElementById('userDropdownTrigger');

if (userTrigger) {
    userTrigger.addEventListener('click', (e) => {
        e.stopPropagation();
        userWrap.classList.toggle('open');
    });

    document.addEventListener('click', (e) => {
        if (!userWrap.contains(e.target)) {
            userWrap.classList.remove('open');
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') userWrap.classList.remove('open');
    });
}
</script>