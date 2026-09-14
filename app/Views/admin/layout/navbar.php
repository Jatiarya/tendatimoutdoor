<link rel="stylesheet" href="layout.css">
<nav class="top-navbar">

    <div class="navbar-left">
        <button type="button" class="navbar-toggle" id="sidebarToggleBtn" aria-label="Buka menu"
            aria-controls="adminSidebar" aria-expanded="false">
            <i class="ri-menu-line"></i>
        </button>

        <a href="<?= base_url('/dashboard') ?>" class="navbar-logo">
            <div class="navbar-logo-icon">
                <img src="./img/logo.jpg" alt="logo" class="navbar-logo-img">
            </div>
            <span class="navbar-logo-text">TendaTim <span class="navbar-logo-sub">Outdoor</span></span>
        </a>
    </div>

    <div class="navbar-right">

        <div class="search-pill navbar-search">
            <i class="ri-search-line"></i>
            <input type="text" placeholder="Cari sesuatu...">
        </div>

        <!-- Notifikasi -->
      
        <div class="navbar-profile" onclick="toggleProfileDropdown()" style="position:relative;">
            <div class="navbar-avatar"><?= substr(session()->get('username'), 0, 2) ?></div>
            <div style="text-align:left;">
                <div class="navbar-profile-name"><?= session()->get('username') ?></div>
                <div class="navbar-profile-role"><?= session()->get('level') === 'admin' ? 'Admin' : 'User' ?></div>
            </div>

            <div id="profileDropdown" style="
                display:none; position:absolute; top:calc(100% + 10px); right:0;
                background:white; border-radius:16px; box-shadow:0 8px 32px rgba(0,0,0,0.12);
                padding:8px; min-width:180px; z-index:999;
            ">
                <a href="<?= base_url('admin/profil') ?>" style="
                    display:flex; align-items:center; gap:10px; padding:10px 14px;
                    border-radius:10px; color:#374151; font-size:0.85rem;
                    text-decoration:none; transition:background .2s;
                " onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='none'">
                    <i class="ri-user-line" style="font-size:1rem;color:#9ca3af;"></i> Profil Saya
                </a>
                <div style="height:1px;background:#f3f4f6;margin:6px 0;"></div>
                <a href="<?= base_url('logout') ?>" style="
                    display:flex; align-items:center; gap:10px; padding:10px 14px;
                    border-radius:10px; color:#ef4444; font-size:0.85rem;
                    text-decoration:none; transition:background .2s;
                " onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='none'">
                    <i class="ri-logout-box-r-line" style="font-size:1rem;"></i> Keluar
                </a>
            </div>
        </div>

    </div>
</nav>
<script>
    // navbar js

    function toggleProfileDropdown() {
        const el = document.getElementById('profileDropdown');
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
    }

    function waktuRelatif(datetimeStr) {
        if (!datetimeStr) return '-';
        const detik = Math.floor((Date.now() - new Date(datetimeStr.replace(' ', 'T'))) / 1000);
        if (detik < 60) return 'baru saja';
        if (detik < 3600) return Math.floor(detik / 60) + ' menit lalu';
        if (detik < 86400) return Math.floor(detik / 3600) + ' jam lalu';
        return Math.floor(detik / 86400) + ' hari lalu';
    }

    // ── NOTIFIKASI ──
    // Nama field CSRF TIDAK berubah antar-request, aman disimpan sekali.
    // Hash-nya BISA berubah (CI4 regenerate token per request POST yang berhasil),
    // makanya disimpan di variabel let supaya bisa di-refresh dari response server.
    const csrfName = '<?= csrf_token() ?>';
    let csrfHash = '<?= csrf_hash() ?>';

    function toggleNotifDropdown() {
        document.getElementById('notifDropdown').classList.toggle('active');
    }

    function muatNotifikasi() {
        fetch('<?= base_url('notifikasi/unread') ?>')
            .then(res => res.json())
            .then(data => {
                const dot = document.getElementById('notifDot');
                const countLabel = document.getElementById('notifCountLabel');
                const list = document.getElementById('notifList');

                dot.style.display = data.count > 0 ? 'block' : 'none';
                countLabel.textContent = data.count;

                if (!data.items || data.items.length === 0) {
                    list.innerHTML = '<div class="navbar-notif-empty">Tidak ada notifikasi baru.</div>';
                    return;
                }

                list.innerHTML = data.items.map(item => `
                <div class="navbar-notif-item" onclick="bukaNotifikasi(${item.id}, '${item.url}')">
                    <div class="navbar-notif-item-icon"><i class="ri-shopping-bag-3-line"></i></div>
                    <div class="navbar-notif-item-body">
                        <div class="navbar-notif-item-title"><strong>${item.judul}</strong></div>
                        <div class="navbar-notif-item-desc">${item.pesan ?? ''}</div>
                        <div class="navbar-notif-item-time">${waktuRelatif(item.waktu)}</div>
                    </div>
                </div>
            `).join('');
            })
            .catch(err => console.error('Gagal muat notifikasi:', err));
    }

    function bukaNotifikasi(id, url) {
        fetch('<?= base_url('notifikasi/read') ?>/' + id, {
                method: 'POST',
                keepalive: true,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    [csrfName]: csrfHash
                }),
            })
            .then(res => res.json())
            .then(data => {
                if (data.csrf_hash) csrfHash = data.csrf_hash;
            })
            .catch(err => console.error('Gagal tandai notifikasi dibaca:', err))
            .finally(() => {
                window.location.href = url;
            });
    }

    function tandaiSemuaDibaca() {
        fetch('<?= base_url('notifikasi/read-all') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    [csrfName]: csrfHash
                }),
            })
            .then(res => res.json())
            .then(data => {
                if (data.csrf_hash) csrfHash = data.csrf_hash;
                muatNotifikasi();
            })
            .catch(err => console.error('Gagal tandai semua dibaca:', err));
    }

    muatNotifikasi();
    setInterval(muatNotifikasi, 20000); // refresh otomatis tiap 20 detik

    document.addEventListener('click', function(e) {
        const dd = document.getElementById('notifDropdown');
        if (dd && !e.target.closest('.navbar-notif-wrap')) dd.classList.remove('active');
    });

    document.addEventListener('click', function(e) {
        const dd = document.getElementById('profileDropdown');
        if (dd && !e.target.closest('.navbar-profile')) dd.style.display = 'none';
    });
</script>

<style>
    .navbar-notif-dropdown {
        display: none;
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        width: 340px;
        max-width: 90vw;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
        overflow: hidden;
        z-index: 999;
    }

    .navbar-notif-dropdown.active {
        display: block;
    }

    .navbar-notif-dropdown-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px;
        border-bottom: 1px solid #f3f4f6;
        font-size: 0.85rem;
        font-weight: 700;
        color: #374151;
    }

    .navbar-notif-count-label {
        background: #fef2f2;
        color: #dc2626;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 999px;
        margin-left: 4px;
    }

    .navbar-notif-readall {
        background: none;
        border: none;
        color: #53cbf3;
        font-size: 0.72rem;
        font-weight: 600;
        cursor: pointer;
        padding: 0;
    }

    .navbar-notif-readall:hover {
        text-decoration: underline;
    }

    .navbar-notif-list {
        max-height: 320px;
        overflow-y: auto;
    }

    .navbar-notif-empty {
        padding: 24px 16px;
        text-align: center;
        color: #9ca3af;
        font-size: 0.82rem;
    }

    .navbar-notif-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px 16px;
        cursor: pointer;
        border-bottom: 1px solid #f9fafb;
        transition: background .15s;
    }

    .navbar-notif-item:hover {
        background: #f9fafb;
    }

    .navbar-notif-item-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: rgba(83, 203, 243, 0.15);
        color: #53cbf3;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1rem;
    }

    .navbar-notif-item-title {
        font-size: 0.82rem;
        color: #374151;
        line-height: 1.4;
    }

    .navbar-notif-item-desc {
        font-size: 0.76rem;
        color: #6b7280;
        margin-top: 2px;
    }

    .navbar-notif-item-time {
        font-size: 0.72rem;
        color: #9ca3af;
        margin-top: 4px;
    }

    .navbar-notif-footer {
        display: block;
        text-align: center;
        padding: 12px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #53cbf3;
        text-decoration: none;
        border-top: 1px solid #f3f4f6;
    }

    .navbar-notif-footer:hover {
        background: #f9fafb;
    }
</style>