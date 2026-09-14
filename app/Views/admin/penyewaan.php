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
        <form method="GET" action="<?= base_url('penyewaan') ?>" class="periode-bar-dates" id="filterForm">
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

    <!-- ── STAT CARDS ── -->
    <div class="grid gap-3" style="grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));">

        <div class="stat-card animate-up delay-1">
            <div class="stat-card-top">
                <span class="stat-label">
                    Total Penyewa
                    <small style="display:block; font-weight:400; opacity:0.7;">
                        <?= esc(date('d M Y', strtotime($tglMulai))) ?> –
                        <?= esc(date('d M Y', strtotime($tglAkhir))) ?>
                    </small>
                </span>
                <div class="stat-icon">
                    <i class="ri-group-line"></i>
                </div>
            </div>
            <div class="stat-value"><?= $ringkasan['total_penyewa'] ?></div>
            <div class="perlengkapan-breakdown text-gray-500">
                Jumlah pengguna yang mengajukan sewa
            </div>
        </div>

        <!-- <div class="stat-card animate-up delay-2">
            <div class="stat-card-top">
                <span class="stat-label">
                    Penyewa Aktif (Disewa)
                    <small style="display:block; font-weight:400; opacity:0.7;">
                        <?= esc(date('d M Y', strtotime($tglMulai))) ?> –
                        <?= esc(date('d M Y', strtotime($tglAkhir))) ?>
                    </small>
                </span>
                <div class="stat-icon">
                    <i class="ri-shopping-bag-3-line"></i>
                </div>
            </div>
            <div class="stat-value"><?= $ringkasan['total_aktif'] ?></div>
            <div class="perlengkapan-breakdown text-gray-500">
                Jumlah orang dengan status <span class="up">Disewa</span>
            </div>
        </div> -->

        <div class="stat-card animate-up delay-3">
            <div class="stat-card-top">
                <span class="stat-label">
                    Perlengkapan Sedang Disewa
                    <small style="display:block; font-weight:400; opacity:0.7;">
                        <?= esc(date('d M Y', strtotime($tglMulai))) ?> –
                        <?= esc(date('d M Y', strtotime($tglAkhir))) ?>
                    </small>
                </span>
                <div class="stat-icon">
                    <i class="ri-suitcase-3-line"></i>
                </div>
            </div>
            <div class="stat-value"><?= $perlengkapanAktif['total_unit'] ?> unit</div>
            <div class="stat-desc">
                Dari <span class="up"><?= $perlengkapanAktif['jenis_berbeda'] ?></span> jenis perlengkapan berbeda
            </div>

            <?php if (! empty($perlengkapanAktif['breakdown'])) : ?>
            <div class="perlengkapan-breakdown">
                <?php
                    $i = 0;
                    foreach ($perlengkapanAktif['breakdown'] as $nama => $jumlah):
                        if ($i >= 4) break;
                        $i++;
                    ?>
                <div class="breakdown-item">
                    <span class="breakdown-name"><?= esc($nama) ?></span>
                    <span class="breakdown-qty"><?= $jumlah ?> unit</span>
                </div>
                <?php endforeach; ?>

                <?php if (count($perlengkapanAktif['breakdown']) > 4) : ?>
                <div class="breakdown-more">
                    +<?= count($perlengkapanAktif['breakdown']) - 4 ?> jenis lainnya
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

    </div>

    <!-- ── TABEL PENYEWAAN (tanpa DataTables) ── -->
    <div class="table-card animate-up delay-3">
        <div class="table-card-header">
            <h3 class="table-card-title">Penyewaan</h3>
            <div class="flex items-center gap-3">
                <div class="search-pill">
                    <i class="ri-search-line"></i>
                    <input type="text" id="tableSearch" placeholder="Cari Penyewaan..." oninput="filterTabel()">
                </div>

            </div>
        </div>

        <div style="overflow-x:auto;">
            <table class="data-table" id="recentTable" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ID Pengguna</th>
                        <th>Nomor WA</th>
                        <th>Perlengkapan</th>
                        <th>Estimasi</th>
                        <th>Total</th>
                        <th>Foto KTP</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="recentTableBody">
                    <?php if (empty($data)): ?>
                    <tr id="emptyRow">
                        <td colspan="8" style="text-align:center; padding:32px; color:#999;">
                            Tidak ada data penyewaan pada rentang tanggal ini.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($data as $pesanan):
                            $totalManual = 0;
                            $namaProdukGabungan = [];
                            foreach ($pesanan['items'] as $it) {
                                $durasi = (int) ceil((strtotime($it['tgl_kembali_rencana']) - strtotime($it['tgl_sewa'])) / 86400);
                                $totalManual += $durasi * $it['jumlah'] * $it['harga_sewa'];
                                $namaProdukGabungan[] = $it['nama_perlengkapan'];
                            }

                            // Prioritaskan jumlah_bayar ASLI dari tabel pembayaran (sudah termasuk potongan diskon)
                            $totalBiayaPesanan = $pesanan['jumlah_bayar'] !== null
                                ? (float) $pesanan['jumlah_bayar']
                                : $totalManual;

                            $adaDiskonPesanan = ! empty($pesanan['nilai_potongan']) && (float) $pesanan['nilai_potongan'] > 0;

                            $idTampil = $pesanan['is_multi'] ? $pesanan['kode_transaksi'] : $pesanan['items'][0]['id_sewa'];
                            $searchText = strtolower($idTampil . ' ' . $pesanan['username'] . ' ' . implode(' ', $namaProdukGabungan) . ' ' . $pesanan['status']);
                        ?>
                    <tr data-search="<?= esc($searchText) ?>">
                        <td>
                            <strong><?= $pesanan['is_multi'] ? esc($pesanan['kode_transaksi']) : '#' . $pesanan['items'][0]['id_sewa'] ?></strong>
                            <?php if ($pesanan['is_multi']) : ?>
                            <br><span class="badge badge-secondary"
                                style="font-size:0.7rem;"><?= count($pesanan['items']) ?> produk</span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= esc($pesanan['username']) ?></strong></td>
                        <td><strong><?= esc($pesanan['nomor_wa']) ?></strong></td>
                        <td>
                            <?php foreach ($pesanan['items'] as $it) : ?>
                            <div style="margin-bottom:4px;">
                                <?= esc($it['nama_perlengkapan']) ?> <small style="color:#8a8070;">(<?= $it['jumlah'] ?>
                                    unit)</small>
                            </div>
                            <?php endforeach; ?>
                        </td>
                        <td>
                            <?php foreach ($pesanan['items'] as $it) : ?>
                            <div style="margin-bottom:4px; font-size:0.85rem;">
                                <?= esc($it['tgl_sewa']) ?> s/d <?= esc($it['tgl_kembali_rencana']) ?>
                            </div>
                            <?php endforeach; ?>
                        </td>
                        <td>
                            <strong>Rp <?= number_format($totalBiayaPesanan, 0, ',', '.') ?></strong>
                            <?php if ($adaDiskonPesanan) : ?>
                            <br><small style="color:#16a34a;">
                                <i class="ri-coupon-line"></i> -Rp
                                <?= number_format($pesanan['nilai_potongan'], 0, ',', '.') ?>
                                <?= $pesanan['kode_voucher'] ? '(' . esc($pesanan['kode_voucher']) . ')' : '' ?>
                            </small>
                            <br><small style="color:#8a8070; text-decoration:line-through;">
                                Rp <?= number_format($totalManual, 0, ',', '.') ?>
                            </small>
                            <?php endif; ?>
                        </td>
                        <td class="flex items-center gap-2">
                            <?php foreach ($pesanan['items'] as $it) : ?>
                            <a target="_blank" href="<?= base_url('penyewaan/ktp/' . $it['id_sewa']) ?>">
                                <img src="<?= base_url('penyewaan/ktp/' . $it['id_sewa']) ?>"
                                    style="width: 40px; height: 40px; object-fit: cover; margin-right:4px;">
                            </a>
                            <?php break; ?>
                            <?php endforeach; ?>
                        </td>
                        <td><strong><?= esc($pesanan['status']) ?></strong></td>
                        <td>
                            <div class="action-btns">
                                <a href="<?= base_url('penyewaan/ubah-transaksi/' . $pesanan['group_key']) ?>"
                                    class="act-btn act-btn-edit" title="Edit Status">
                                    <i class="ri-pencil-line"></i>
                                </a>

                                <form action="<?= base_url('penyewaan/hapus/' . $pesanan['group_key']) ?>" method="post"
                                    class="form-hapus-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="act-btn act-btn-del btn-delete" title="Hapus">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
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
/* ── BREAKDOWN PERLENGKAPAN DI STAT CARD ── */
.perlengkapan-breakdown {
    margin-top: 14px;
    padding-top: 12px;
    border-top: 1px dashed #e5e7eb;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.breakdown-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.8rem;
}

.breakdown-name {
    color: #4a5568;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 65%;
}

.breakdown-qty {
    color: #53cbf3;
    font-weight: 700;
    background: rgba(83, 203, 243, 0.12);
    padding: 2px 8px;
    border-radius: 999px;
    font-size: 0.75rem;
}

.breakdown-more {
    font-size: 0.75rem;
    color: #8a8070;
    font-style: italic;
    margin-top: 2px;
}

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

/* ── TABEL POLOS (pengganti style DataTables) ── */
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

    // ── Hapus (konfirmasi via SweetAlert, tetap submit form POST) ──
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');

            Swal.fire({
                title: 'Hapus Data?',
                html: `
                    <div style="margin-top:10px">
                        <div style="width:80px;height:80px;background:#ff00004c;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:auto;">
                            <i class="ri-delete-bin-6-line" style="font-size:42px;color:#ff0000;"></i>
                        </div>
                        <p style="margin-top:20px">Apakah Anda yakin ingin menghapus data ini?</p>
                        <small style="color:#999">Data yang telah dihapus tidak dapat dikembalikan.</small>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
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