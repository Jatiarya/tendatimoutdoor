<?= $this->extend('layout/template'); ?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('css/public.css') ?>">

<!-- ── PAGE HEADER ── -->
<div class="page-header">
    <div class="page-header-inner">
        <div class="breadcrumb">
            <a href="/">Beranda</a><span>/</span> Riwayat Sewa
        </div>
        <h1>Riwayat Sewa Saya</h1>
        <p>Pantau status penyewaan dan pembayaranmu di sini</p>
    </div>
</div>

<!-- ── FLASH MESSAGE ── -->
<?php if (session()->getFlashdata('success')) : ?>
    <div class="php-alert success" style="max-width:900px;margin:16px auto 0;">
        ✓ <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')) : ?>
    <div class="php-alert error" style="max-width:900px;margin:16px auto 0;">
        ⚠ <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<!-- ── FILTER BAR (disederhanakan: search + dropdown status dalam 1 wadah) ── -->
<div class="filter-bar">
    <div class="filter-bar-controls">
        <div class="search-box">
            <span style="color:var(--stone); font-size:1rem;"><i class="ri-search-line"></i></span>
            <input type="text" id="riwayatSearch" placeholder="Cari nama perlengkapan..." oninput="filterRiwayat()">
        </div>

        <div class="select-box">
            <i class="ri-filter-3-line"></i>
            <select id="filterStatus" onchange="filterRiwayat()">
                <option value="semua">Semua Status</option>
                <option value="menunggu_konfirmasi">Menunggu Konfirmasi</option>
                <option value="disewa">Disewa</option>
                <option value="terlambat">Terlambat</option>
                <option value="dikembalikan">Dikembalikan</option>
                <option value="ditolak">Ditolak</option>
                <option value="dibatalkan">Dibatalkan</option>
            </select>
        </div>
    </div>

    <div class="filter-count" id="filterCount">
        <?= count($riwayat) ?> riwayat
    </div>
</div>

<!-- ── MAIN ── -->
<div class="produk-container">

    <!-- Desktop Table -->
    <div class="table-wrap">
        <table class="produk-table" id="riwayatTable">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Perlengkapan</th>
                    <th>Tanggal Sewa</th>
                    <th>Jumlah</th>
                    <th>Total</th>
                    <th>Pembayaran</th>
                    <th>Status Sewa</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="riwayatTableBody">
                <?php if (empty($riwayat)) : ?>
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="ri-file-list-line"></i></div>
                                <h3>Belum Ada Riwayat</h3>
                                <p>Kamu belum pernah menyewa perlengkapan. Yuk mulai jelajahi katalog!</p>
                            </div>
                        </td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($riwayat as $pesanan) :
                        $totalHargaManual = 0;
                        foreach ($pesanan['items'] as $it) {
                            $durasi = (int) ceil((strtotime($it['tgl_kembali_rencana']) - strtotime($it['tgl_sewa'])) / 86400);
                            $totalHargaManual += $durasi * $it['jumlah'] * $it['harga_sewa'];
                        }

                        $totalHarga = $pesanan['jumlah_bayar'] !== null
                            ? (float) $pesanan['jumlah_bayar']
                            : $totalHargaManual;

                        $adaDiskon = ! empty($pesanan['nilai_potongan']) && (float) $pesanan['nilai_potongan'] > 0;

                        $statusSewaMap = [
                            'menunggu_konfirmasi' => ['Menunggu Konfirmasi', 'status-pending'],
                            'disewa'              => ['Sedang Disewa', 'status-active'],
                            'terlambat'           => ['Terlambat', 'status-late'],
                            'dikembalikan'        => ['Dikembalikan', 'status-done'],
                            'ditolak'             => ['Ditolak', 'status-rejected'],
                            'dibatalkan'          => ['Dibatalkan', 'status-rejected'],
                        ];
                        [$labelSewa, $classSewa] = $statusSewaMap[$pesanan['status']] ?? [$pesanan['status'], 'status-pending'];

                        $statusBayarMap = [
                            'lunas'   => ['Lunas', 'status-active'],
                            'pending' => ['Menunggu Konfirmasi', 'status-pending'],
                            'gagal'   => ['Gagal', 'status-rejected'],
                            'expired' => ['Kedaluwarsa', 'status-rejected'],
                        ];
                        $bayarKey = $pesanan['status_bayar'] ?? 'pending';
                        [$labelBayar, $classBayar] = $statusBayarMap[$bayarKey] ?? [$bayarKey, 'status-pending'];

                        $namaGabungan = strtolower(implode(' ', array_column($pesanan['items'], 'nama_perlengkapan')));
                        $itemPertama = $pesanan['items'][0];

                        // ── Susun data detail untuk modal (dipakai kalau status = dikembalikan) ──
                        $detailItems = [];
                        foreach ($pesanan['items'] as $it) {
                            $durasiRencana = (int) ceil((strtotime($it['tgl_kembali_rencana']) - strtotime($it['tgl_sewa'])) / 86400);
                            $tglKembaliAktual = ! empty($it['tgl_kembali_aktual']) && $it['tgl_kembali_aktual'] !== '0000-00-00'
                                ? $it['tgl_kembali_aktual']
                                : null;
                            $durasiAktual = $tglKembaliAktual
                                ? (int) ceil((strtotime($tglKembaliAktual) - strtotime($it['tgl_sewa'])) / 86400)
                                : null;

                            $detailItems[] = [
                                'nama'              => $it['nama_perlengkapan'],
                                'jumlah'            => $it['jumlah'],
                                'tgl_sewa'          => date('d M Y', strtotime($it['tgl_sewa'])),
                                'tgl_rencana'       => date('d M Y', strtotime($it['tgl_kembali_rencana'])),
                                'tgl_aktual'        => $tglKembaliAktual ? date('d M Y', strtotime($tglKembaliAktual)) : '-',
                                'durasi_rencana'    => $durasiRencana,
                                'durasi_aktual'     => $durasiAktual ?? $durasiRencana,
                                'subtotal'          => $durasiRencana * $it['jumlah'] * $it['harga_sewa'],
                            ];
                        }

                        $dataDetail = [
                            'kode'          => $pesanan['is_multi'] ? $pesanan['kode_transaksi'] : ('#' . $itemPertama['id_sewa']),
                            'items'         => $detailItems,
                            'total'         => $totalHarga,
                            'diskon'        => $adaDiskon ? (float) $pesanan['nilai_potongan'] : 0,
                            'kode_voucher'  => $pesanan['kode_voucher'] ?? null,
                            'status_bayar'  => $labelBayar,
                        ];
                    ?>
                        <tr data-status="<?= esc($pesanan['status']) ?>" data-nama="<?= esc($namaGabungan) ?>">
                            <td class="td-img">
                                <img src="/uploads/perlengkapan/<?= esc($itemPertama['foto_perlengkapan']) ?>"
                                    alt="<?= esc($itemPertama['nama_perlengkapan']) ?>"
                                    onerror="this.style.background='#e8f0e8';this.removeAttribute('src')">
                            </td>
                            <td class="td-nama">
                                <?php if ($pesanan['is_multi']) : ?>
                                    <span class="status-badge status-active"
                                        style="font-size:0.7rem; margin-bottom:4px; display:inline-block;">
                                        <?= count($pesanan['items']) ?> produk
                                    </span>
                                <?php endif; ?>
                                <?php foreach ($pesanan['items'] as $it) : ?>
                                    <div style="margin-bottom:2px;">
                                        <?= esc($it['nama_perlengkapan']) ?> <small
                                            style="color:var(--stone);">(<?= $it['jumlah'] ?> unit)</small>
                                    </div>
                                <?php endforeach; ?>
                            </td>
                            <td>
                                <?php foreach ($pesanan['items'] as $it) : ?>
                                    <div style="margin-bottom:4px; font-size:0.85rem;">
                                        <?= esc(date('d M Y', strtotime($it['tgl_sewa']))) ?><br>
                                        <small style="color:var(--stone);">s/d
                                            <?= esc(date('d M Y', strtotime($it['tgl_kembali_rencana']))) ?></small>
                                    </div>
                                <?php endforeach; ?>
                            </td>
                            <td>
                                <?php foreach ($pesanan['items'] as $it) : ?>
                                    <div style="margin-bottom:4px;"><?= esc($it['jumlah']) ?> unit</div>
                                <?php endforeach; ?>
                            </td>
                            <td class="td-harga">
                                Rp <?= number_format($totalHarga, 0, ',', '.') ?>
                                <?php if ($adaDiskon) : ?>
                                    <br><small style="color:#16a34a;">
                                        🎁 Hemat Rp <?= number_format($pesanan['nilai_potongan'], 0, ',', '.') ?>
                                        <?= $pesanan['kode_voucher'] ? '(' . esc($pesanan['kode_voucher']) . ')' : '' ?>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td><span class="status-badge <?= $classBayar ?>"><?= esc($labelBayar) ?></span></td>
                            <td><span class="status-badge <?= $classSewa ?>"><?= esc($labelSewa) ?></span></td>
                            <td>
                                <?php if ($bayarKey === 'pending') : ?>
                                    <?php if ($pesanan['is_multi']) : ?>
                                        <a href="<?= base_url('pembayaran-transaksi/' . $pesanan['kode_transaksi']) ?>"
                                            class="btn-sewa">Bayar</a>
                                    <?php else : ?>
                                        <a href="<?= base_url('pembayaran/' . $itemPertama['id_sewa']) ?>" class="btn-sewa">Bayar</a>
                                    <?php endif; ?>
                                <?php elseif ($pesanan['status'] === 'dikembalikan') : ?>
                                    <button type="button" class="btn-sewa btn-lihat-detail cursor-pointer"
                                        data-detail="<?= esc(json_encode($dataDetail), 'attr') ?>">
                                        Detail
                                    </button>
                                <?php else : ?>
                                    <span style="color:var(--stone); font-size:0.85rem;">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                <?php endforeach;
                endif; ?>
            </tbody>
        </table>

        <div id="riwayatEmptyResult" style="display:none; text-align:center; padding:40px 20px;">
            <div class="empty-icon" style="font-size:2.5rem; margin-bottom:8px;"><i class="ri-search-line"></i></div>
            <h3>Tidak Ditemukan</h3>
            <p style="color:var(--stone);">Coba kata kunci atau status lain.</p>
        </div>
    </div>

    <!-- Mobile Card Grid -->
    <div class="card-grid" id="riwayatCardGrid">
        <?php if (!empty($riwayat)) : ?>
            <?php foreach ($riwayat as $pesanan) :
                $totalHargaManual = 0;
                foreach ($pesanan['items'] as $it) {
                    $durasi = (int) ceil((strtotime($it['tgl_kembali_rencana']) - strtotime($it['tgl_sewa'])) / 86400);
                    $totalHargaManual += $durasi * $it['jumlah'] * $it['harga_sewa'];
                }

                $totalHarga = $pesanan['jumlah_bayar'] !== null
                    ? (float) $pesanan['jumlah_bayar']
                    : $totalHargaManual;

                $adaDiskon = ! empty($pesanan['nilai_potongan']) && (float) $pesanan['nilai_potongan'] > 0;

                [$labelSewa, $classSewa] = $statusSewaMap[$pesanan['status']] ?? [$pesanan['status'], 'status-pending'];
                $bayarKey = $pesanan['status_bayar'] ?? 'pending';
                [$labelBayar, $classBayar] = $statusBayarMap[$bayarKey] ?? [$bayarKey, 'status-pending'];

                $namaGabungan = strtolower(implode(' ', array_column($pesanan['items'], 'nama_perlengkapan')));
                $itemPertama = $pesanan['items'][0];

                $detailItems = [];
                foreach ($pesanan['items'] as $it) {
                    $durasiRencana = (int) ceil((strtotime($it['tgl_kembali_rencana']) - strtotime($it['tgl_sewa'])) / 86400);
                    $tglKembaliAktual = ! empty($it['tgl_kembali_aktual']) && $it['tgl_kembali_aktual'] !== '0000-00-00'
                        ? $it['tgl_kembali_aktual']
                        : null;
                    $durasiAktual = $tglKembaliAktual
                        ? (int) ceil((strtotime($tglKembaliAktual) - strtotime($it['tgl_sewa'])) / 86400)
                        : null;

                    $detailItems[] = [
                        'nama'           => $it['nama_perlengkapan'],
                        'jumlah'         => $it['jumlah'],
                        'tgl_sewa'       => date('d M Y', strtotime($it['tgl_sewa'])),
                        'tgl_rencana'    => date('d M Y', strtotime($it['tgl_kembali_rencana'])),
                        'tgl_aktual'     => $tglKembaliAktual ? date('d M Y', strtotime($tglKembaliAktual)) : '-',
                        'durasi_rencana' => $durasiRencana,
                        'durasi_aktual'  => $durasiAktual ?? $durasiRencana,
                        'subtotal'       => $durasiRencana * $it['jumlah'] * $it['harga_sewa'],
                    ];
                }

                $dataDetail = [
                    'kode'         => $pesanan['is_multi'] ? $pesanan['kode_transaksi'] : ('#' . $itemPertama['id_sewa']),
                    'items'        => $detailItems,
                    'total'        => $totalHarga,
                    'diskon'       => $adaDiskon ? (float) $pesanan['nilai_potongan'] : 0,
                    'kode_voucher' => $pesanan['kode_voucher'] ?? null,
                    'status_bayar' => $labelBayar,
                ];
            ?>
                <div class="prod-card" data-status="<?= esc($pesanan['status']) ?>" data-nama="<?= esc($namaGabungan) ?>">
                    <img src="/uploads/perlengkapan/<?= esc($itemPertama['foto_perlengkapan']) ?>"
                        alt="<?= esc($itemPertama['nama_perlengkapan']) ?>">
                    <div class="prod-card-body">
                        <?php if ($pesanan['is_multi']) : ?>
                            <span class="status-badge status-active" style="font-size:0.7rem;"><?= count($pesanan['items']) ?>
                                produk</span>
                        <?php endif; ?>

                        <?php foreach ($pesanan['items'] as $it) : ?>
                            <h3 style="font-size:0.95rem; margin:4px 0 0;"><?= esc($it['nama_perlengkapan']) ?>
                                <small>(<?= $it['jumlah'] ?> unit)</small>
                            </h3>
                        <?php endforeach; ?>

                        <div style="display:flex; gap:6px; margin:6px 0;">
                            <span class="status-badge <?= $classBayar ?>"><?= esc($labelBayar) ?></span>
                            <span class="status-badge <?= $classSewa ?>"><?= esc($labelSewa) ?></span>
                        </div>
                        <div class="prod-card-price">Rp <?= number_format($totalHarga, 0, ',', '.') ?></div>
                        <?php if ($adaDiskon) : ?>
                            <small style="color:#16a34a; display:block; margin-top:2px;">
                                <i class="ri-gift-line"></i> Hemat Rp <?= number_format($pesanan['nilai_potongan'], 0, ',', '.') ?>
                                <?= $pesanan['kode_voucher'] ? '(' . esc($pesanan['kode_voucher']) . ')' : '' ?>
                            </small>
                        <?php endif; ?>

                        <?php if ($bayarKey === 'pending') : ?>
                            <?php if ($pesanan['is_multi']) : ?>
                                <a href="<?= base_url('pembayaran-transaksi/' . $pesanan['kode_transaksi']) ?>" class="btn-sewa">Bayar
                                    Sekarang</a>
                            <?php else : ?>
                                <a href="<?= base_url('pembayaran/' . $itemPertama['id_sewa']) ?>" class="btn-sewa">Bayar Sekarang</a>
                            <?php endif; ?>
                        <?php elseif ($pesanan['status'] === 'dikembalikan') : ?>
                            <button type="button" class="btn-sewa btn-lihat-detail" style="width:100%; margin-top:8px;"
                                data-detail="<?= esc(json_encode($dataDetail), 'attr') ?>">
                                Lihat Detail
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
        <?php endforeach;
        endif; ?>
    </div>

</div>
<div class="quick-contact">
    <div class="qc-title">Butuh Respons Cepat?</div>
    <a href="https://wa.me/6281234567890" target="_blank" class="qc-wa">
        <span>💬</span> Chat WhatsApp Admin
    </a>
    <div class="qc-hours">Kami biasanya membalas dalam beberapa menit</div>
</div>
<!-- ── MODAL DETAIL RIWAYAT ── -->
<div id="modalDetail"
    style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:2000; align-items:center; justify-content:center; padding:16px;">
    <div
        style="background:#fff; border-radius:16px; padding:24px; max-width:480px; width:100%; max-height:85vh; overflow-y:auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h3 style="margin:0; font-size:1.05rem;">Detail Pesanan <span id="detailKode"
                    style="color:var(--primary);"></span></h3>
            <button type="button" onclick="tutupModalDetail()"
                style="background:none; border:none; font-size:1.3rem; cursor:pointer; color:var(--stone);">&times;</button>
        </div>

        <div id="detailItemsWrap"></div>

        <div style="border-top:1px dashed #e5e7eb; margin-top:14px; padding-top:14px;">
            <div id="detailDiskonRow"
                style="display:none; display:flex; justify-content:space-between; font-size:0.88rem; color:#16a34a; margin-bottom:6px;">
                <span>Diskon</span>
                <span id="detailDiskonNilai"></span>
            </div>
            <div style="display:flex; justify-content:space-between; font-weight:700; font-size:1rem;">
                <span>Total Dibayar</span>
                <span id="detailTotal"></span>
            </div>
            <div
                style="display:flex; justify-content:space-between; font-size:0.82rem; color:var(--stone); margin-top:6px;">
                <span>Status Pembayaran</span>
                <span id="detailStatusBayar"></span>
            </div>
        </div>

        <button type="button" onclick="tutupModalDetail()"
            class="btn-close bg-red-500 p-4 rounded-lg text-white cursor-pointer"
            style="width:100%; margin-top:18px;">Tutup</button>
    </div>
</div>

<style>

</style>

<script>
    /* ══════════════ FILTER (search + dropdown status) ══════════════ */
    function filterRiwayat() {
        const keyword = document.getElementById('riwayatSearch').value.toLowerCase().trim();
        const activeStatus = document.getElementById('filterStatus').value;

        const rows = document.querySelectorAll('#riwayatTableBody tr[data-nama]');
        const cards = document.querySelectorAll('#riwayatCardGrid .prod-card[data-nama]');

        let jumlahTampil = 0;

        function cocok(el) {
            const cocokNama = el.dataset.nama.includes(keyword);
            const cocokStatus = activeStatus === 'semua' || el.dataset.status === activeStatus;
            return cocokNama && cocokStatus;
        }

        rows.forEach(row => {
            const tampil = cocok(row);
            row.style.display = tampil ? '' : 'none';
            if (tampil) {
                row.classList.add('row-fade-in');
                jumlahTampil++;
            }
        });

        cards.forEach(card => {
            const tampil = cocok(card);
            card.style.display = tampil ? '' : 'none';
            if (tampil) card.classList.add('row-fade-in');
        });

        const adaData = rows.length > 0;
        document.getElementById('riwayatEmptyResult').style.display = (adaData && jumlahTampil === 0) ? 'block' : 'none';

        const counterEl = document.getElementById('filterCount');
        counterEl.textContent = jumlahTampil + ' riwayat';
        counterEl.classList.remove('pulse');
        void counterEl.offsetWidth;
        counterEl.classList.add('pulse');
    }

    /* ══════════════ MODAL DETAIL (untuk status "Dikembalikan") ══════════════ */
    function bukaModalDetail(dataStr) {
        const data = JSON.parse(dataStr);

        document.getElementById('detailKode').textContent = data.kode;

        let html = '';
        data.items.forEach(it => {
            html += `
            <div style="padding:12px 0; border-bottom:1px solid #f4f2ec;">
                <div style="font-weight:600; margin-bottom:6px;">${it.nama} <small style="color:var(--stone); font-weight:400;">(${it.jumlah} unit)</small></div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:4px 12px; font-size:0.82rem; color:var(--stone);">
                    <div>Tgl Sewa: <strong style="color:#333;">${it.tgl_sewa}</strong></div>
                    <div>Rencana Kembali: <strong style="color:#333;">${it.tgl_rencana}</strong></div>
                    <div>Kembali Aktual: <strong style="color:#333;">${it.tgl_aktual}</strong></div>
                    <div>Durasi: <strong style="color:#333;">${it.durasi_rencana} hari</strong></div>
                </div>
            </div>
        `;
        });
        document.getElementById('detailItemsWrap').innerHTML = html;

        const diskonRow = document.getElementById('detailDiskonRow');
        if (data.diskon > 0) {
            diskonRow.style.display = 'flex';
            document.getElementById('detailDiskonNilai').textContent = '-Rp ' + data.diskon.toLocaleString('id-ID') +
                (data.kode_voucher ? ' (' + data.kode_voucher + ')' : '');
        } else {
            diskonRow.style.display = 'none';
        }

        document.getElementById('detailTotal').textContent = 'Rp ' + data.total.toLocaleString('id-ID');
        document.getElementById('detailStatusBayar').textContent = data.status_bayar;

        document.getElementById('modalDetail').style.display = 'flex';
    }

    function tutupModalDetail() {
        document.getElementById('modalDetail').style.display = 'none';
    }

    document.querySelectorAll('.btn-lihat-detail').forEach(btn => {
        btn.addEventListener('click', function() {
            bukaModalDetail(this.dataset.detail);
        });
    });
</script>

<?= $this->endSection() ?>