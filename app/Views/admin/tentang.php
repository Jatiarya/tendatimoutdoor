<?= $this->extend('admin/layout/template') ?>
<?= $this->section('content') ?>

<div class="table-container w-full grid gap-3">

    <div class="page-header animate-up">
        <div>
            <h1 class="page-header-title">Tentang Kami</h1>
            <p class="page-header-sub">Kelola konten halaman tentang kami pada halaman pengguna</p>
        </div>
        <a href="<?= base_url('galeritentang/tambah') ?>" class="btn-primary">
            <i class="ri-add-line"></i> Tambah Foto
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <!-- ── TABEL GALERI ── -->
    <div class="table-card animate-up delay-1">
        <div class="table-card-header">
            <h3 class="table-card-title">Galeri Foto</h3>
            <div class="flex items-center gap-3">
                <div class="search-pill">
                    <i class="ri-search-line"></i>
                    <input type="text" id="tableSearch" placeholder="Cari Foto..." oninput="filterTabel()">
                </div>
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table class="data-table" id="recentTable" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>Nama Foto</th>
                        <th>Alt</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="recentTableBody">
                    <?php if (empty($galeri)): ?>
                        <tr id="emptyRow">
                            <td colspan="5" style="text-align:center; padding:32px; color:#999;">
                                Belum ada foto galeri. Klik "Tambah Foto" untuk menambahkan.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($galeri as $i => $foto):
                            $searchText = strtolower($foto['nama_foto'] . ' ' . $foto['alt']);
                        ?>
                            <tr data-search="<?= esc($searchText) ?>">
                                <td><?= $i + 1 ?></td>
                                <td>
                                    <a target="_blank" href="<?= base_url('uploads/galery/' . esc($foto['foto'])) ?>">
                                        <img src="<?= base_url('uploads/galery/' . esc($foto['foto'])) ?>"
                                            alt="<?= esc($foto['alt']) ?>"
                                            style="width: 70px; height: 48px; object-fit: cover; border-radius:6px;">
                                    </a>
                                </td>
                                <td><strong><?= esc($foto['nama_foto']) ?></strong></td>
                                <td><?= esc($foto['alt']) ?></td>
                                <td>
                                    <div class="action-btns">
                                        <a href="<?= base_url('galeritentang/ubah/' . $foto['id']) ?>"
                                            class="act-btn act-btn-edit" title="Edit Foto">
                                            <i class="ri-pencil-line"></i>
                                        </a>

                                        <form action="<?= base_url('galeritentang/hapus/' . $foto['id']) ?>" method="post"
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
                Tidak ada foto yang cocok dengan pencarian.
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

    /* ── TABEL POLOS (konsisten dengan tabel penyewaan) ── */
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
        // ── Hapus (konfirmasi via SweetAlert, tetap submit form POST) ──
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');

                Swal.fire({
                    title: 'Hapus Foto?',
                    html: `
                    <div style="margin-top:10px">
                        <div style="width:80px;height:80px;background:#ff00004c;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:auto;">
                            <i class="ri-delete-bin-6-line" style="font-size:42px;color:#ff0000;"></i>
                        </div>
                        <p style="margin-top:20px">Apakah Anda yakin ingin menghapus foto ini?</p>
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

    // ── PENCARIAN MANUAL ──
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