<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml" sizes="any">
    <link rel="icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#0f172a" media="(prefers-color-scheme: dark)">
    <title></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Carter+One&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">

    <!-- Tailwind CSS (browser) -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.8/css/dataTables.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.8/js/dataTables.js"></script>
    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
    <!--  -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <?php if (session()->getFlashdata('success')) : ?>
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: '<?= session()->getFlashdata('success') ?>',
        timer: 1800,
        showConfirmButton: false
    });
    </script>
    <?php endif; ?>

    <!-- ══ NAVBAR ══ -->
    <?= $this->include('admin/layout/navbar') ?>
    <div class="body-wrapper">

        <?= $this->include('admin/layout/sidebar') ?>

        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <main class="main-content">

            <?php if (session()->getFlashdata('success')): ?>
            <div
                class="flex items-center gap-3 p-4 mb-5 bg-green-50 border border-green-200 rounded-2xl text-green-700 text-sm">
                <i class="ri-checkbox-circle-line text-lg"></i>
                <?= session()->getFlashdata('success') ?>
                <button onclick="this.parentElement.remove()" class="ml-auto text-green-400 hover:text-green-700"><i
                        class="ri-close-line"></i></button>
            </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
            <div
                class="flex items-center gap-3 p-4 mb-5 bg-red-50 border border-red-200 rounded-2xl text-red-600 text-sm">
                <i class="ri-error-warning-line text-lg"></i>
                <?= session()->getFlashdata('error') ?>
                <button onclick="this.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-600"><i
                        class="ri-close-line"></i></button>
            </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>

        </main>
    </div>
    <?= $this->include('admin/layout/footer') ?>

    <?= $this->renderSection('scripts') ?>

    <script>
    (function() {
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('sidebarToggleBtn');
        const closeBtn = document.getElementById('sidebarCloseBtn');
        if (!sidebar || !overlay) return;

        function openSidebar() {
            sidebar.classList.add('open');
            overlay.classList.add('show');
            document.body.classList.add('no-scroll');
            toggleBtn?.setAttribute('aria-expanded', 'true');
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
            document.body.classList.remove('no-scroll');
            toggleBtn?.setAttribute('aria-expanded', 'false');
        }

        toggleBtn?.addEventListener('click', () => {
            sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
        });
        closeBtn?.addEventListener('click', closeSidebar);
        overlay.addEventListener('click', closeSidebar);
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeSidebar();
        });

        /* Tutup sidebar otomatis saat sebuah menu di-tap (khusus mobile) */
        sidebar.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) closeSidebar();
            });
        });

        /* Reset state kalau layar di-resize balik ke desktop */
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) closeSidebar();
        });
    })();
    </script>

</body>

</html>