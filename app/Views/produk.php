<?= $this->extend('layout/template'); ?>

<?= $this->section('content') ?>

<!-- ── PAGE HEADER ── -->
<div class="page-header">
    <div class="page-header-inner">
        <div class="breadcrumb">
            <a href="/">Beranda</a><span>/</span> Perlengkapan
        </div>
        <h1>Daftar Perlengkapan</h1>
        <p>Pilih perlengkapan hiking terbaik untuk petualanganmu</p>
    </div>
</div>

<!-- ── FILTER BAR (disederhanakan: search + dropdown kategori dalam 1 wadah) ── -->
<div class="filter-bar">
    <div class="filter-bar-controls">
        <div class="search-box">
            <span style="color:var(--stone); font-size:1rem;"><i class="ri-search-line"></i></span>
            <input type="text" id="searchProduk" placeholder="Cari perlengkapan..." oninput="filterProduk()">
        </div>

        <div class="select-box">
            <i class="ri-filter-3-line"></i>
            <select id="filterKategori" onchange="filterProduk()">
                <option value="semua">Semua Kategori</option>
                <?php foreach ($kategori as $k): ?>
                <option value="<?= $k['id_kategori'] ?>"><?= esc($k['nama_kategori']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="filter-count" id="filterCount">
        <?= count($perlengkapan) ?> katalog
    </div>
</div>

<!-- ── MAIN ── -->
<div class="produk-container">

    <div class="info-bar">
        <div>
            <div class="info-bar-title">Katalog Lengkap</div>
            <div class="info-bar-subtitle">Semua peralatan siap pakai & terawat</div>
        </div>
        <button class="sort-btn" onclick="toggleSortHarga()">
            ↕ Urutkan: Harga
        </button>
    </div>

    <!-- Desktop Table -->
    <div class="table-wrap">
        <table class="produk-table" id="tabelProduk">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Foto</th>
                    <th>Nama Barang</th>
                    <th>Harga / Hari</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tabelProdukBody">
                <?php if (empty($perlengkapan)) : ?>
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <div class="empty-icon"><i class="ri-box-line"></i></div>
                            <h3>Belum Ada Produk</h3>
                            <p>Produk akan segera tersedia. Pantau terus!</p>
                        </div>
                    </td>
                </tr>
                <?php else : ?>
                <?php $i = 1;
                    foreach ($perlengkapan as $p) : ?>
                <tr data-kategori="<?= $p['id_kategori'] ?>" data-nama="<?= esc(strtolower($p['nama_perlengkapan'])) ?>"
                    data-harga="<?= $p['harga_sewa'] ?>">
                    <td class="td-num"><?= $i++ ?></td>
                    <td class="td-img">
                        <img src="/uploads/perlengkapan/<?= $p['foto_perlengkapan'] ?>"
                            alt="<?= $p['nama_perlengkapan'] ?>">
                    </td>
                    <td class="td-nama">
                        <?= $p['nama_perlengkapan'] ?>
                        <small>Stok: <?= $p['stok']; ?></small>
                    </td>
                    <td class="td-harga">
                        Rp <?= $p['harga_sewa']; ?>
                        <small>per hari</small>
                    </td>

                    <td>
                        <div style="display:flex; gap:6px;">
                            <a href="/sewa/<?= $p['id_perlengkapan']; ?>" class="btn-sewa">Sewa</a>
                            <button type="button" class="btn-sewa" style="background:#53cbf3;"
                                onclick="bukaModalKeranjang(<?= $p['id_perlengkapan'] ?>, '<?= esc($p['nama_perlengkapan'], 'js') ?>', <?= $p['harga_sewa'] ?>)">
                                <i class="ri-shopping-cart-line"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach;
                endif; ?>
            </tbody>
        </table>

        <div id="tabelEmptyResult" style="display:none; text-align:center; padding:40px 20px;">
            <div class="empty-icon" style="font-size:2.5rem; margin-bottom:8px;"><i class="ri-search-line"></i></div>
            <h3>Tidak Ditemukan</h3>
            <p style="color:var(--stone);">Coba kata kunci atau kategori lain.</p>
        </div>
    </div>

    <!-- Mobile Card Grid -->
    <div class="card-grid" id="cardGrid">
        <?php if (!empty($perlengkapan)) : ?>
        <?php foreach ($perlengkapan as $p) : ?>
        <div class="prod-card" data-kategori="<?= $p['id_kategori'] ?>"
            data-nama="<?= esc(strtolower($p['nama_perlengkapan'])) ?>" data-harga="<?= $p['harga_sewa'] ?>">
            <img src="/uploads/perlengkapan/<?= $p['foto_perlengkapan'] ?>" alt="<?= $p['nama_perlengkapan'] ?>">
            <div class="prod-card-body">
                <h3><?= esc($p['nama_perlengkapan']) ?></h3>

                <div class="prod-card-price">
                    <small><?= $p['stok'] ?> unit</small>
                </div>

                <div class="prod-card-price">
                    Rp <?= number_format($p['harga_sewa'], 0, ',', '.') ?>
                    /<small>per hari</small>
                </div>
                <div style="display:flex; gap:6px;">
                    <a href="/sewa/<?= $p['id_perlengkapan'] ?>" class="btn-sewa" style="flex:1;">Sewa</a>
                    <button type="button" class="btn-sewa" style="background:#53cbf3; flex:1;"
                        onclick="bukaModalKeranjang(<?= $p['id_perlengkapan'] ?>, '<?= esc($p['nama_perlengkapan'], 'js') ?>', <?= $p['harga_sewa'] ?>)">
                        <i class="ri-shopping-cart-line"></i>
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach;
        endif; ?>

        <div id="cardEmptyResult" style="display:none; text-align:center; padding:40px 20px; grid-column: 1 / -1;">
            <div class="empty-icon" style="font-size:2.5rem; margin-bottom:8px;"><i class="ri-search-line"></i></div>
            <h3>Tidak Ditemukan</h3>
            <p style="color:var(--stone);">Coba kata kunci atau kategori lain.</p>
        </div>
    </div>

</div>

<div id="modalKeranjang"
    style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:2000; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:16px; padding:24px; max-width:420px; width:90%;">
        <h3 id="modal_nama_produk" style="margin:0 0 16px; font-size:1.1rem;"></h3>

        <div class="field-group">
            <label for="modal_jumlah">Jumlah Unit</label>
            <input type="number" id="modal_jumlah" min="1" value="1" class="form-control">
        </div>
        <div class="field-group">
            <label for="modal_tgl_sewa">Tanggal Sewa</label>
            <input type="date" id="modal_tgl_sewa" class="form-control" min="<?= date('Y-m-d') ?>">
        </div>
        <div class="field-group">
            <label for="modal_tgl_kembali">Tanggal Kembali Rencana</label>
            <input type="date" id="modal_tgl_kembali" class="form-control" min="<?= date('Y-m-d') ?>">
        </div>

        <div id="modal_error" style="color:#dc2626; font-size:0.85rem; margin-top:8px; display:none;"></div>

        <div style="display:flex; gap:8px; margin-top:20px;">
            <button type="button" onclick="tutupModalKeranjang()" class="btn-secondary" style="flex:1;">Batal</button>
            <button type="button" onclick="submitTambahKeranjang()" class="btn-primary"
                style="flex:1;">Tambahkan</button>
        </div>
    </div>
</div>

<style>

</style>

<script>
/* ══════════════ FILTER KATEGORI + PENCARIAN ══════════════ */
let sortHargaAsc = true;

function filterProduk() {
    const keyword = document.getElementById('searchProduk').value.toLowerCase().trim();
    const kategoriAktif = document.getElementById('filterKategori').value;

    const tabelRows = document.querySelectorAll('#tabelProdukBody tr[data-kategori]');
    const cards = document.querySelectorAll('#cardGrid .prod-card[data-kategori]');

    let jumlahTampil = 0;

    function cocokFilter(el) {
        const cocokKategori = kategoriAktif === 'semua' || el.dataset.kategori === kategoriAktif;
        const cocokNama = el.dataset.nama.includes(keyword);
        return cocokKategori && cocokNama;
    }

    tabelRows.forEach(row => {
        const tampil = cocokFilter(row);
        row.style.display = tampil ? '' : 'none';
        if (tampil) {
            row.classList.add('row-fade-in');
            jumlahTampil++;
        }
    });

    cards.forEach(card => {
        const tampil = cocokFilter(card);
        card.style.display = tampil ? '' : 'none';
        if (tampil) card.classList.add('row-fade-in');
    });

    // Tampilkan pesan "tidak ditemukan" kalau hasilnya kosong
    const adaData = tabelRows.length > 0;
    document.getElementById('tabelEmptyResult').style.display = (adaData && jumlahTampil === 0) ? 'block' : 'none';
    document.getElementById('cardEmptyResult').style.display = (adaData && jumlahTampil === 0) ? 'block' : 'none';

    const counterEl = document.getElementById('filterCount');
    counterEl.textContent = jumlahTampil + ' produk';
    counterEl.classList.remove('pulse');
    void counterEl.offsetWidth; // trigger reflow biar animasi bisa diulang
    counterEl.classList.add('pulse');
}

/* ══════════════ SORT HARGA (bonus, tombol yang sudah ada di UI) ══════════════ */
function toggleSortHarga() {
    sortHargaAsc = !sortHargaAsc;

    const tbody = document.getElementById('tabelProdukBody');
    const rows = Array.from(tbody.querySelectorAll('tr[data-harga]'));
    rows.sort((a, b) => {
        const hargaA = parseFloat(a.dataset.harga);
        const hargaB = parseFloat(b.dataset.harga);
        return sortHargaAsc ? hargaA - hargaB : hargaB - hargaA;
    });
    rows.forEach((row, i) => {
        row.querySelector('.td-num').textContent = i + 1;
        tbody.appendChild(row);
    });

    const cardGrid = document.getElementById('cardGrid');
    const cards = Array.from(cardGrid.querySelectorAll('.prod-card[data-harga]'));
    cards.sort((a, b) => {
        const hargaA = parseFloat(a.dataset.harga);
        const hargaB = parseFloat(b.dataset.harga);
        return sortHargaAsc ? hargaA - hargaB : hargaB - hargaA;
    });
    cards.forEach(card => cardGrid.appendChild(card));
}

/* ══════════════ MODAL KERANJANG (tidak berubah) ══════════════ */
let idProdukAktif = null;

function bukaModalKeranjang(idPerlengkapan, nama, harga) {
    <?php if (! session()->get('isLoggedIn')) : ?>
    window.location.href = '<?= base_url('login') ?>';
    return;
    <?php endif; ?>

    idProdukAktif = idPerlengkapan;
    document.getElementById('modal_nama_produk').textContent = nama + ' — Rp ' + harga.toLocaleString('id-ID') +
        '/hari';
    document.getElementById('modal_jumlah').value = 1;
    document.getElementById('modal_tgl_sewa').value = '';
    document.getElementById('modal_tgl_kembali').value = '';
    document.getElementById('modal_error').style.display = 'none';
    document.getElementById('modalKeranjang').style.display = 'flex';
}

function tutupModalKeranjang() {
    document.getElementById('modalKeranjang').style.display = 'none';
}

function submitTambahKeranjang() {
    const jumlah = document.getElementById('modal_jumlah').value;
    const tglSewa = document.getElementById('modal_tgl_sewa').value;
    const tglKembali = document.getElementById('modal_tgl_kembali').value;
    const errorBox = document.getElementById('modal_error');

    if (!jumlah || jumlah < 1 || !tglSewa || !tglKembali) {
        errorBox.textContent = 'Lengkapi semua field terlebih dahulu.';
        errorBox.style.display = 'block';
        return;
    }

    fetch('<?= base_url('keranjang/tambah') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                id_perlengkapan: idProdukAktif,
                jumlah: jumlah,
                tgl_sewa: tglSewa,
                tgl_kembali_rencana: tglKembali,
            })
        })
        .then(res => res.json().then(data => ({
            status: res.status,
            body: data
        })))
        .then(({
            status,
            body
        }) => {
            if (status !== 200 || !body.success) {
                errorBox.textContent = body.message || 'Gagal menambahkan ke keranjang.';
                errorBox.style.display = 'block';
                return;
            }

            tutupModalKeranjang();

            const cartCount = document.getElementById('cart-count');
            if (cartCount) cartCount.textContent = body.jumlah_item;

            window.location.href = '<?= base_url('keranjang') ?>';
        })
        .catch(() => {
            errorBox.textContent = 'Gagal terhubung ke server.';
            errorBox.style.display = 'block';
        });
}
</script>
<?= $this->endSection() ?>