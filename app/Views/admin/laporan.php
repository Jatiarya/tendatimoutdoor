<?= $this->extend('admin/layout/template') ?>
<?= $this->section('content') ?>

<div class="table-container w-full grid gap-3">

    <!-- ── FILTER TANGGAL (1 baris ringkas) ── -->
    <div class="periode-bar animate-up delay-1">
        <div class="periode-bar-label">
            <i class="ri-calendar-line"></i><span>Filter Tanggal</span>
        </div>
        <div class="periode-bar-pills">
            <button type="button" class="periode-pill" data-quick="hari_ini">Hari Ini</button>
            <button type="button" class="periode-pill" data-quick="minggu_ini">Minggu Ini</button>
            <button type="button" class="periode-pill" data-quick="bulan_ini">Bulan Ini</button>
            <button type="button" class="periode-pill" data-quick="tahun_ini">Tahun Ini</button>
        </div>
        <form method="GET" action="<?= base_url('laporan') ?>" class="periode-bar-dates" id="filterForm">
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
                <span class="stat-label">Total Pendapatan Selesai</span>
                <div class="stat-icon"><i class="ri-cash-line"></i></div>
            </div>
            <div class="stat-value">Rp <?= number_format($akumulasi['total_pendapatan'], 0, ',', '.') ?></div>
            <div class="stat-desc">Dari transaksi yang sudah <span class="up">Dikembalikan</span></div>
        </div>

        <div class="stat-card animate-up delay-2">
            <div class="stat-card-top">
                <span class="stat-label">Total Transaksi Selesai</span>
                <div class="stat-icon"><i class="ri-file-list-3-line"></i></div>
            </div>
            <div class="stat-value"><?= $akumulasi['total_transaksi'] ?></div>
            <div class="stat-desc">Jumlah sewa yang tuntas dikembalikan</div>
        </div>

        <div class="stat-card animate-up delay-3">
            <div class="stat-card-top">
                <span class="stat-label">Total Unit Dikembalikan</span>
                <div class="stat-icon"><i class="ri-suitcase-3-line"></i></div>
            </div>
            <div class="stat-value"><?= $akumulasi['total_unit'] ?> unit</div>
            <div class="stat-desc">Total perlengkapan yang sudah kembali</div>
        </div>
    </div>

    <!-- ── TABEL LAPORAN ── -->
    <div class="table-card animate-up delay-3">
        <div class="table-card-header">
            <h3 class="table-card-title">Rekapitulasi Penyewaan Selesai</h3>
            <div class="flex items-center gap-3">
                <div class="search-pill">
                    <i class="ri-search-line"></i>
                    <input type="text" id="tableSearch" placeholder="Cari Laporan...">
                </div>
                <a href="<?= base_url('laporan/export?tanggal_mulai=' . $tglMulai . '&tanggal_akhir=' . $tglAkhir) ?>"
                    class="btn-export-excel">
                    <i class="ri-file-excel-2-line"></i> Export Excel
                </a>
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table class="data-table" id="recentTable" style="width:100%">
                <thead>
                    <tr>
                        <th>ID Sewa</th>
                        <th>Penyewa</th>
                        <th>Perlengkapan</th>
                        <th>Jumlah</th>
                        <th>Periode Sewa</th>
                        <th>Tgl Kembali</th>
                        <th>Durasi</th>
                        <th>Total Biaya</th>
                        <th>Order ID</th>
                        <th>Status Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data)): ?>
                        <tr>
                            <td colspan="10" style="text-align:center; padding:32px; color:#999;">
                                Belum ada data laporan pada rentang tanggal ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data as $l): ?>
                            <tr>
                                <td><strong>#<?= $l['id_sewa'] ?></strong></td>
                                <td><strong><?= esc($l['username']) ?></strong></td>
                                <td><strong><?= esc($l['nama_perlengkapan']) ?></strong></td>
                                <td><?= $l['jumlah'] ?> unit</td>
                                <td><?= esc($l['tgl_sewa']) ?> s/d <?= esc($l['tgl_kembali_rencana']) ?></td>
                                <td><?= esc($l['tgl_kembali_aktual']) ?></td>
                                <td><?= $l['durasi_hari'] ?> hari</td>
                                <td><strong>Rp <?= number_format($l['jumlah_bayar'], 0, ',', '.') ?></strong></td>
                                <td><?= esc($l['order_id'] ?? '-') ?></td>
                                <td>
                                    <?php
                                    $badgeClass = match ($l['status_bayar']) {
                                        'lunas'   => 'badge-success',
                                        'pending' => 'badge-warning',
                                        'gagal', 'expired' => 'badge-danger',
                                        default   => 'badge-secondary',
                                    };
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= esc($l['status_bayar'] ?? '-') ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<style>
    /* ── periode-bar & periode-pill kini didefinisikan bersama di style.css ── */
    #recentTable_filter,
    #recentTable_info {
        display: none;
    }

    .dataTables_wrapper .dataTables_paginate {
        margin-top: 12px;
        text-align: right;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 6px 12px;
        margin-left: 4px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        cursor: pointer;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #53cbf3 !important;
        color: #fff !important;
        border: none;
    }

    .dataTables_wrapper .dataTables_length {
        margin-bottom: 10px;
    }

    .btn-export-excel {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 18px;
        border-radius: 9px;
        background: linear-gradient(135deg, #1d9d5b, #16a34a);
        color: #fff;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 600;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 4px 14px rgba(29, 157, 91, 0.3);
        white-space: nowrap;
    }

    .btn-export-excel:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(29, 157, 91, 0.4);
        color: #fff;
    }

    .btn-export-excel i {
        font-size: 1rem;
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

        const table = $('#recentTable').DataTable({
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            order: [],
            language: {
                search: "",
                lengthMenu: "Tampilkan _MENU_ data",
                zeroRecords: "Data tidak ditemukan",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                paginate: {
                    previous: "‹",
                    next: "›"
                }
            }
        });

        document.getElementById('tableSearch').addEventListener('keyup', function() {
            table.search(this.value).draw();
        });
    });
</script>

<?= $this->endSection() ?>