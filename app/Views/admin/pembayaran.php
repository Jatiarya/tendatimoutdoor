<?= $this->extend('admin/layout/template') ?>
<?= $this->section('content') ?>

<div class="table-container w-full grid gap-3">

    <!-- ── FILTER TANGGAL (1 baris ringkas) ── -->
    <div class="periode-bar animate-up delay-1">
        <div class="periode-bar-label">
            <i class="ri-calendar-2-line"></i><span>Filter Tanggal</span>
        </div>
        <div class="periode-bar-pills">
            <button type="button" class="periode-pill" data-quick="hari_ini">Hari Ini</button>
            <button type="button" class="periode-pill" data-quick="minggu_ini">Minggu Ini</button>
            <button type="button" class="periode-pill" data-quick="bulan_ini">Bulan Ini</button>
            <button type="button" class="periode-pill" data-quick="tahun_ini">Tahun Ini</button>
        </div>
        <form method="GET" action="<?= base_url('pembayaran') ?>" class="periode-bar-dates" id="filterForm">
            <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="<?= esc($tglMulai) ?>"
                max="<?= date('Y-m-d') ?>">
            <span class="periode-bar-sep">–</span>
            <input type="date" id="tanggal_akhir" name="tanggal_akhir" value="<?= esc($tglAkhir) ?>"
                max="<?= date('Y-m-d') ?>">
            <button type="submit" class="periode-bar-apply" title="Terapkan Filter">
                <i class="ri-search-line"></i>
            </button>
        </form>
    </div>

    <!-- ── STAT CARDS AKUMULASI ── -->
    <div class="grid gap-3" style="grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));">
        <div class="stat-card animate-up delay-1">
            <div class="stat-card-top">
                <span class="stat-label">
                    Total Pemasukan
                    <small style="display:block; font-weight:400; opacity:0.7;">
                        <?= esc(date('d M Y', strtotime($tglMulai))) ?> –
                        <?= esc(date('d M Y', strtotime($tglAkhir))) ?>
                    </small>
                </span>
                <div class="stat-icon">
                    <i class="ri-cash-line"></i>
                </div>
            </div>
            <div class="stat-value">Rp <?= number_format($akumulasi['total_nominal'], 0, ',', '.') ?></div>
            <div class="stat-desc">
                Dari pembayaran berstatus <span class="up">Lunas</span>
            </div>
        </div>

        <div class="stat-card animate-up delay-2">
            <div class="stat-card-top">
                <span class="stat-label">
                    Total Transaksi Lunas
                    <small style="display:block; font-weight:400; opacity:0.7;">
                        <?= esc(date('d M Y', strtotime($tglMulai))) ?> –
                        <?= esc(date('d M Y', strtotime($tglAkhir))) ?>
                    </small>
                </span>
                <div class="stat-icon">
                    <i class="ri-bank-card-line"></i>
                </div>
            </div>
            <div class="stat-value"><?= $akumulasi['total_transaksi'] ?></div>
            <div class="stat-desc">
                Dari total <span class="up"><?= count($data) ?></span> data ditampilkan
            </div>
        </div>
    </div>

    <!-- ── TABEL DATA PEMBAYARAN (tanpa DataTables) ── -->
    <div class="table-card animate-up delay-3">
        <div class="table-card-header">
            <h3 class="table-card-title">Data Pembayaran</h3>
            <div class="flex items-center gap-3">
                <div class="search-pill">
                    <i class="ri-search-line"></i>
                    <input type="text" id="tableSearch" placeholder="Cari Pembayaran..." oninput="filterTabel()">
                </div>
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table class="data-table" id="recentTable" style="width:100%">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Penyewa</th>
                        <th>Perlengkapan</th>
                        <th>Jumlah Bayar</th>
                        <th>Metode</th>
                        <th>Status Bayar</th>
                        <th>Status Sewa</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="recentTableBody">
                    <?php if (empty($data)): ?>
                        <tr id="emptyRow">
                            <td colspan="8" style="text-align:center; padding:32px; color:#999;">
                                Tidak ada data pembayaran pada rentang tanggal ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data as $p): ?>
                            <?php
                            $rowIdentifier = ! empty($p['kode_transaksi'])
                                ? $p['kode_transaksi']
                                : ('SINGLE-' . $p['id_sewa']);
                            ?>
                            <tr data-identifier="<?= esc($rowIdentifier) ?>"
                                data-search="<?= esc(strtolower($p['order_id'] . ' ' . $p['username'] . ' ' . $p['nama_perlengkapan'] . ' ' . $p['status_bayar'] . ' ' . ($p['metode_bayar'] ?? ''))) ?>">
                                <td><strong><?= esc($p['order_id']) ?></strong></td>
                                <td><strong><?= esc($p['username']) ?></strong></td>
                                <td><strong><?= esc($p['nama_perlengkapan']) ?></strong></td>
                                <td><strong>Rp <?= number_format($p['jumlah_bayar'], 0, ',', '.') ?></strong></td>
                                <td>
                                    <?php if (($p['metode_bayar'] ?? null) === 'cod') : ?>
                                        <span class="badge badge-cod"><i class="ri-truck-line"></i> COD</span>
                                    <?php else : ?>
                                        <strong><?= esc($p['metode_bayar'] ?? '-') ?></strong>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $badgeClass = match ($p['status_bayar']) {
                                        'lunas'   => 'badge-success',
                                        'pending' => 'badge-warning',
                                        'gagal', 'expired' => 'badge-danger',
                                        default   => 'badge-secondary',
                                    };
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= esc($p['status_bayar']) ?></span>
                                </td>
                                <td><strong><?= esc($p['status_sewa']) ?></strong></td>
                                <td>
                                    <div class="action-btns">
                                        <?php if ($p['status_bayar'] === 'pending'): ?>
                                            <form action="<?= base_url('pembayaran/konfirmasi/' . $p['id_pembayaran']) ?>"
                                                method="post" class="form-hapus-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="act-btn act-btn-edit btn-confirm-bayar"
                                                    title="Tandai Lunas">
                                                    <i class="ri-check-line"></i>
                                                </button>
                                            </form>
                                            <form action="<?= base_url('pembayaran/tolak/' . $p['id_pembayaran']) ?>" method="post"
                                                class="form-hapus-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="act-btn act-btn-del btn-tolak-bayar" title="Tolak">
                                                    <i class="ri-close-line"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="act-btn act-btn-disabled" title="Sudah diproses">
                                                <i class="ri-checkbox-circle-line"></i>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <div id="noResultRow" style="display:none; text-align:center; padding:32px; color:#999;">
                Tidak ada data yang cocok dengan pencarian.
            </div>
        </div>

    </div>
</div>

<style>
    .form-hapus-inline {
        display: inline-block;
        margin: 0;
    }

    .form-hapus-inline button {
        border: none;
        background: none;
        cursor: pointer;
        font: inherit;
    }

    /* ── TABEL POLOS ── */
    .data-table {
        border-collapse: collapse;
    }

    .data-table thead th {
        text-align: left;
        padding: 12px 14px;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: gray;
        border-bottom: 2px solid #f0ede5;
    }

    .data-table tbody td {
        padding: 12px 14px;
        border-bottom: 1px solid #f4f2ec;
        font-size: 0.88rem;
        vertical-align: middle;
    }

    .data-table tbody tr:hover {
        background: #fafaf8;
    }

    /* ── Badge khusus metode COD ── */
    .badge-cod {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 999px;
        background: #fff4e5;
        color: #c2740c;
        font-size: 0.78rem;
        font-weight: 700;
        border: 1px solid #ffdca8;
    }

    .badge-cod i {
        font-size: 0.9rem;
    }

    /* ── Highlight baris yang dituju dari notifikasi ── */
    @keyframes rowHighlightFade {
        0% {
            background: rgba(83, 203, 243, 0.35);
        }

        100% {
            background: transparent;
        }
    }

    .row-highlight {
        animation: rowHighlightFade 2.5s ease-out 3;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const inputMulai = document.getElementById('tanggal_mulai');
        const inputAkhir = document.getElementById('tanggal_akhir');
        const filterForm = document.getElementById('filterForm');

        function formatDate(date) {
            return date.toISOString().split('T')[0];
        }

        document.querySelectorAll('.periode-pill[data-quick]').forEach(btn => {
            btn.addEventListener('click', function() {
                const today = new Date();
                let mulai, akhir;

                switch (this.dataset.quick) {
                    case 'hari_ini':
                        mulai = akhir = today;
                        break;
                    case 'minggu_ini': {
                        const hari = today.getDay() === 0 ? 7 : today.getDay();
                        mulai = new Date(today);
                        mulai.setDate(today.getDate() - (hari - 1));
                        akhir = new Date(mulai);
                        akhir.setDate(mulai.getDate() + 6);
                        break;
                    }
                    case 'bulan_ini':
                        mulai = new Date(today.getFullYear(), today.getMonth(), 1);
                        akhir = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                        break;
                    case 'tahun_ini':
                        mulai = new Date(today.getFullYear(), 0, 1);
                        akhir = new Date(today.getFullYear(), 11, 31);
                        break;
                }

                inputMulai.value = formatDate(mulai);
                inputAkhir.value = formatDate(akhir > today ? today : akhir);
                filterForm.submit();
            });
        });

        (function highlightActiveQuick() {
            const mulai = inputMulai.value;
            const akhir = inputAkhir.value;
            const today = formatDate(new Date());

            document.querySelectorAll('.periode-pill[data-quick]').forEach(btn => {
                if (btn.dataset.quick === 'hari_ini' && mulai === today && akhir === today) {
                    btn.classList.add('active');
                }
            });
        })();

        // ── Datang dari klik notifikasi? Auto-scroll & highlight baris terkait ──
        const highlightIdentifier = <?= json_encode($highlightIdentifier ?? null) ?>;
        if (highlightIdentifier) {
            const row = document.querySelector(
                `tr[data-identifier="${CSS.escape(highlightIdentifier)}"]`);
            if (row) {
                row.style.display = ''; // pastikan tidak ketutup filter search sebelumnya
                row.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                row.classList.add('row-highlight');
            }
        }

        // ── Tandai Lunas ──
        document.querySelectorAll('.btn-confirm-bayar').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');

                Swal.fire({
                    title: 'Tandai Lunas?',
                    html: `
                    <div style="margin-top:10px">
                        <div style="width:80px;height:80px;background:#53cbf34c;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:auto;">
                            <i class="ri-check-double-line" style="font-size:42px;color:#53cbf3;"></i>
                        </div>
                        <p style="margin-top:20px">Konfirmasi bahwa pembayaran ini sudah diterima?</p>
                        <small style="color:#999">Status sewa otomatis berubah menjadi "Disewa" dan stok akan dikurangi.</small>
                    </div>
                `,
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Konfirmasi',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#53cbf3',
                    cancelButtonColor: '#666668',
                    reverseButtons: true,
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });

        // ── Tolak Pembayaran ──
        document.querySelectorAll('.btn-tolak-bayar').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');

                Swal.fire({
                    title: 'Tolak Pembayaran?',
                    html: `
                    <div style="margin-top:10px">
                        <div style="width:80px;height:80px;background:#ff00004c;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:auto;">
                            <i class="ri-close-circle-line" style="font-size:42px;color:#ff0000;"></i>
                        </div>
                        <p style="margin-top:20px">Tandai pembayaran ini gagal/ditolak?</p>
                        <small style="color:#999">Status sewa otomatis berubah menjadi "Dibatalkan".</small>
                    </div>
                `,
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Tolak',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#ff0000',
                    cancelButtonColor: '#666668',
                    reverseButtons: true,
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });

    });

    // ── PENCARIAN MANUAL (pengganti DataTables) ──
    function filterTabel() {
        const keyword = document.getElementById('tableSearch').value.toLowerCase().trim();
        const rows = document.querySelectorAll('#recentTableBody tr[data-search]');
        let adaHasil = false;

        rows.forEach(row => {
            const cocok = row.dataset.search.includes(keyword);
            row.style.display = cocok ? '' : 'none';
            if (cocok) adaHasil = true;
        });

        document.getElementById('noResultRow').style.display = (adaHasil || rows.length === 0) ? 'none' : 'block';
    }
</script>

<?= $this->endSection() ?>