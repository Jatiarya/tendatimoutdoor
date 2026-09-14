<?= $this->extend('admin/layout/template') ?>
<?= $this->section('content') ?>
<title>Dashboard - Tendatim Outdoor</title>
<?php

$chartRevenueData = json_encode($chartRevenue);
$chartRentalData  = json_encode($chartRental);
$chartLabels      = json_encode(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min']);

?>

<!-- ════════════ PAGE HEADER ════════════ -->
<div class="page-header animate-up">
    <div>
        <h1 class="page-header-title">Ringkasan Penyewaan</h1>
        <p class="page-header-sub">Selamat datang kembali admin, <?= esc(session()->get('username') ?? 'Admin') ?> 👋
        </p>
    </div>
    <div class="page-header-date">
        <i class="ri-calendar-line" style="color:var(--primary);"></i>
        <?= date('l, d F Y') ?>
    </div>
</div>

<!-- ════════════ STAT CARDS ════════════ -->
<div class="stat-grid">

    <!-- Total Penyewaan -->
    <div class="stat-card animate-up delay-1">
        <div class="stat-card-top">
            <span class="stat-label">Total Penyewaan Perlengkapan</span>
            <div class="stat-icon">
                <i class="ri-shopping-bag-3-line"></i>
            </div>
        </div>
        <div class="stat-value"><?= count($penyewaan) ?></div>
        <div class="stat-desc">
            Bulan ini:
            <span class="<?= $persenPenyewaan['naik'] ? 'up' : 'down' ?>">
                <?= $persenPenyewaan['naik'] ? '↑' : '↓' ?> <?= abs($persenPenyewaan['nilai']) ?>%
            </span>
        </div>
    </div>

    <!-- Pengguna -->
    <div class="stat-card animate-up delay-2">
        <div class="stat-card-top">
            <span class="stat-label">Pengguna</span>
            <div class="stat-icon">
                <i class="ri-user-3-line"></i>
            </div>
        </div>
        <div class="stat-value"><?= count($user) ?></div>
        <div class="stat-desc">
            Bulan ini:
            <span class="<?= $persenUser['naik'] ? 'up' : 'down' ?>">
                <?= $persenUser['naik'] ? '↑' : '↓' ?> <?= abs($persenUser['nilai']) ?>%
            </span>
        </div>
    </div>

    <!-- Produk Tersedia -->
    <div class="stat-card animate-up delay-3">
        <div class="stat-card-top">
            <span class="stat-label">Perlengkapan Tersedia</span>
            <div class="stat-icon">
                <i class="ri-archive-2-line"></i>
            </div>
        </div>
        <div class="stat-value"><?= count($perlengkapan) ?></div>
        <div class="stat-desc">Stok tersedia: <?= $perlengkapanAktif ?> jenis</div>
    </div>

    <!-- Total Pendapatan -->
    <div class="stat-card animate-up delay-4">
        <div class="stat-card-top">
            <span class="stat-label">Total Pendapatan</span>
            <div class="stat-icon">
                <i class="ri-cash-line"></i>
            </div>
        </div>
        <div class="stat-value revenue">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></div>
        <div class="stat-desc">
            Bulan ini:
            <span class="<?= $persenPendapatan['naik'] ? 'up' : 'down' ?>">
                <?= $persenPendapatan['naik'] ? '↑' : '↓' ?> <?= abs($persenPendapatan['nilai']) ?>%
            </span>
        </div>
    </div>

</div>

<!-- ════════════ NOTIFIKASI PESANAN TERBARU ════════════ -->
<div class="chart-card dashboard-notif-card animate-up delay-1">
    <div class="chart-card-header">
        <h3 class="chart-title">Pesanan Menunggu Konfirmasi</h3>
        <?php if (! empty($notifikasiTerbaru)) : ?>
            <button type="button" class="chart-filter" onclick="tandaiSemuaDibacaDashboard()">
                Tandai semua dibaca
            </button>
        <?php endif; ?>
    </div>

    <?php if (empty($notifikasiTerbaru)) : ?>
        <div style="padding:32px 16px; text-align:center; color:#9ca3af; font-size:0.85rem;">
            Tidak ada pesanan baru yang menunggu konfirmasi. 🎉
        </div>
    <?php else : ?>
        <div id="dashboardNotifList">
            <?php foreach ($notifikasiTerbaru as $n) : ?>
                <div class="dashboard-notif-item" id="notif-row-<?= $n['id_notifikasi'] ?>">
                    <div class="dashboard-notif-icon"><i class="ri-shopping-bag-3-line"></i></div>
                    <div class="dashboard-notif-body">
                        <div class="dashboard-notif-title"><?= esc($n['judul']) ?></div>
                        <div class="dashboard-notif-desc"><?= esc($n['pesan']) ?></div>
                        <div class="dashboard-notif-time"><?= esc(date('d M Y, H:i', strtotime($n['created_at']))) ?></div>
                    </div>
                    <a href="<?= esc($n['url']) ?>" class="dashboard-notif-link"
                        onclick="tandaiDibacaDashboard(<?= $n['id_notifikasi'] ?>)">
                        Lihat <i class="ri-arrow-right-s-line"></i>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- ════════════ CHARTS ════════════ -->
<div class="charts-row">

    <!-- Revenue Analytics -->
    <div class="chart-card animate-up delay-1">
        <div class="chart-card-header">
            <h3 class="chart-title">Analisis Pendapatan</h3>
            <div class="chart-filter-wrap">
                <select class="chart-filter-select" id="filterRevenue"
                    onchange="gantiPeriodeChart('revenue', this.value)">
                    <option value="week">Minggu Ini</option>
                    <option value="month">Bulan Ini</option>
                    <option value="year">Tahun Ini</option>
                </select>
                <i class="ri-arrow-down-s-line chart-filter-icon"></i>
            </div>
        </div>
        <div class="chart-canvas-wrap">
            <canvas id="revenueChart" role="img" aria-label="Grafik pendapatan mingguan">Grafik pendapatan.</canvas>
        </div>
    </div>

    <!-- Rental Analytics -->
    <div class="chart-card animate-up delay-2">
        <div class="chart-card-header">
            <h3 class="chart-title">Analisis Penyewaan</h3>
            <div class="chart-filter-wrap">
                <select class="chart-filter-select" id="filterRental"
                    onchange="gantiPeriodeChart('rental', this.value)">
                    <option value="week">Minggu Ini</option>
                    <option value="month">Bulan Ini</option>
                    <option value="year">Tahun Ini</option>
                </select>
                <i class="ri-arrow-down-s-line chart-filter-icon"></i>
            </div>
        </div>
        <div class="chart-canvas-wrap">
            <canvas id="rentalChart" role="img" aria-label="Grafik jumlah penyewaan">Grafik penyewaan.</canvas>
        </div>
    </div>

</div>

<style>
    .stat-desc .up {
        color: #16a34a;
        font-weight: 600;
    }

    .stat-desc .down {
        color: #dc2626;
        font-weight: 600;
    }

    /* ── Dropdown filter periode chart, dibuat tampil senada dengan
       tombol pill .chart-filter yang lama (bukan tampilan <select> bawaan) ── */
    .chart-filter-wrap {
        position: relative;
        display: inline-flex;
        align-items: center;
    }

    .chart-filter-select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background: #f4f2ec;
        border: none;
        border-radius: 999px;
        padding: 6px 28px 6px 14px;
        font-size: 0.78rem;
        font-weight: 600;
        color: #374151;
        font-family: inherit;
        cursor: pointer;
    }

    .chart-filter-select:hover {
        background: #ece9df;
    }

    .chart-filter-select:focus {
        outline: 2px solid rgba(83, 203, 243, 0.4);
        outline-offset: 1px;
    }

    .chart-filter-icon {
        position: absolute;
        right: 9px;
        pointer-events: none;
        font-size: 0.95rem;
        color: #6b7280;
    }

    .chart-canvas-wrap.is-loading {
        opacity: 0.4;
        transition: opacity .15s;
    }

    .dashboard-notif-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 20px;
        border-bottom: 1px solid #f3f4f6;
        transition: background .15s;
    }

    .dashboard-notif-item:last-child {
        border-bottom: none;
    }

    .dashboard-notif-item:hover {
        background: #fafafa;
    }

    .dashboard-notif-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: rgba(83, 203, 243, 0.15);
        color: #53cbf3;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.1rem;
    }

    .dashboard-notif-body {
        flex: 1;
        min-width: 0;
    }

    .dashboard-notif-title {
        font-size: 0.88rem;
        font-weight: 600;
        color: #1f2937;
    }

    .dashboard-notif-desc {
        font-size: 0.8rem;
        color: #6b7280;
        margin-top: 2px;
    }

    .dashboard-notif-time {
        font-size: 0.72rem;
        color: #9ca3af;
        margin-top: 4px;
    }

    .dashboard-notif-link {
        display: inline-flex;
        align-items: center;
        font-size: 0.8rem;
        font-weight: 600;
        color: #53cbf3;
        text-decoration: none;
        flex-shrink: 0;
        white-space: nowrap;
    }

    .dashboard-notif-link:hover {
        text-decoration: underline;
    }
</style>

<?= $this->endSection() ?>

<!-- ════════════ SCRIPTS ════════════ -->
<?= $this->section('scripts') ?>
<script>
    const labels = <?= $chartLabels ?>;
    const revenueData = <?= $chartRevenueData ?>;
    const rentalData = <?= $chartRentalData ?>;

    const chartDefaults = {
        borderRadius: 50,
        borderSkipped: false,
        barPercentage: 0.55,
        categoryPercentage: 0.7,
    };

    function formatRupiahSingkat(angka) {
        if (angka >= 1000000000) {
            return (angka / 1000000000).toFixed(angka % 1000000000 === 0 ? 0 : 1) + ' M';
        }
        if (angka >= 1000000) {
            return (angka / 1000000).toFixed(angka % 1000000 === 0 ? 0 : 1) + ' jt';
        }
        if (angka >= 1000) {
            return (angka / 1000).toFixed(angka % 1000 === 0 ? 0 : 1) + ' rb';
        }
        return angka.toString();
    }

    function formatRupiahLengkap(angka) {
        return 'Rp ' + angka.toLocaleString('id-ID');
    }

    const revenueChart = new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Pendapatan',
                data: revenueData,
                backgroundColor: (ctx) => {
                    const max = Math.max(...revenueData);
                    return ctx.raw === max ? '#53cbf3' : 'rgba(83,203,243,0.25)';
                },
                hoverBackgroundColor: '#53cbf3',
                ...chartDefaults,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#1f2937',
                    padding: 10,
                    cornerRadius: 10,
                    callbacks: {
                        label: (ctx) => ' ' + formatRupiahLengkap(ctx.raw),
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            family: 'Poppins',
                            size: 11
                        },
                        color: '#9ca3af'
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.04)',
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            family: 'Poppins',
                            size: 10
                        },
                        color: '#9ca3af',
                        callback: (value) => formatRupiahSingkat(value)
                    },
                    border: {
                        display: false
                    },
                }
            }
        }
    });

    const rentalChart = new Chart(document.getElementById('rentalChart'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Jumlah Sewa',
                data: rentalData,
                backgroundColor: (ctx) => {
                    const max = Math.max(...rentalData);
                    return ctx.raw === max ? '#53cbf3' : 'rgba(83,203,243,0.25)';
                },
                hoverBackgroundColor: '#53cbf3',
                ...chartDefaults,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#1f2937',
                    padding: 10,
                    cornerRadius: 10,
                    callbacks: {
                        label: (ctx) => ' ' + ctx.raw + ' penyewaan'
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            family: 'Poppins',
                            size: 11
                        },
                        color: '#9ca3af'
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: {
                            family: 'Poppins',
                            size: 10
                        },
                        color: '#9ca3af'
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.04)',
                        drawBorder: false
                    },
                    border: {
                        display: false
                    },
                }
            }
        }
    });

    // ── FILTER PERIODE CHART (AJAX, tanpa reload halaman) ──
    const chartInstances = {
        revenue: revenueChart,
        rental: rentalChart,
    };

    function gantiPeriodeChart(metric, period) {
        const chart = chartInstances[metric];
        const wrap = document.getElementById(metric === 'revenue' ? 'revenueChart' : 'rentalChart')
            .closest('.chart-canvas-wrap');

        wrap.classList.add('is-loading');

        fetch(`<?= base_url('dashboard/chart-data') ?>?period=${period}&metric=${metric}`)
            .then(res => res.json())
            .then(data => {
                chart.data.labels = data.labels;
                chart.data.datasets[0].data = data.data;
                chart.update();
            })
            .catch(err => console.error('Gagal memuat data chart:', err))
            .finally(() => wrap.classList.remove('is-loading'));
    }
</script>

<script>
    // ── Widget notifikasi di body dashboard, pakai endpoint yang sama dengan navbar ──
    // (variabel csrfName/csrfHash dideklarasikan global di navbar.php, yang selalu
    // ke-include LEBIH DULU lewat template.php -- jadi tinggal dipakai ulang di sini)
    function tandaiDibacaDashboard(id) {
        // PENTING: keepalive:true supaya fetch ini TETAP jalan di background
        // walau <a href> di bawahnya langsung memindahkan halaman. Tanpa ini,
        // browser sering membatalkan request sebelum sempat sampai ke server.
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
            .catch(err => console.error('Gagal tandai dibaca:', err));
        // Tidak perlu preventDefault: biarkan link tetap membawa admin ke halaman pembayaran.
    }

    function tandaiSemuaDibacaDashboard() {
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
                window.location.reload();
            })
            .catch(err => console.error('Gagal tandai semua dibaca:', err));
    }
</script>
<?= $this->endSection() ?>