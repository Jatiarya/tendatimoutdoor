<?= $this->extend('admin/layout/template') ?>
<?= $this->section('content') ?>
<div class="table-container w-full grid gap-3">

    <div class="stat-card animate-up delay-1">
        <div class="stat-card-top">
            <span class="stat-label">Total Perlengkapan</span>
            <div class="stat-icon">
                <i class="ri-shopping-bag-3-line"></i>
            </div>
        </div>
        <div class="stat-value"><?= count($perlengkapan); ?></div>
        <div class="stat-desc">
            Last month: <span class="up">-</span>
        </div>
    </div>
    <div class="table-card animate-up delay-3">
        <div class="table-card-header">
            <h3 class="table-card-title">Perlengkapan</h3>
            <div class="flex items-center gap-3">
                <div class="search-pill">
                    <i class="ri-search-line"></i>
                    <input type="text" id="tableSearch" placeholder="Cari Perlengkapan...">
                </div>
                <a href="<?= base_url('perlengkapan/tambah') ?>" class="btn-primary" style="padding:9px 18px;">
                    Tambah<i class="ri-add-line"></i>
                </a>
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table class="data-table" id="recentTable" style="width:100%; ">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kode</th>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Harga Sewa</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="recentTableBody">
                    <?php foreach ($perlengkapan as $p): ?>

                    <tr>
                        <td><strong><?=$p['id_perlengkapan'] ?></strong></td>
                        <td><?= esc($p['kd_perlengkapan']) ?></td>
                        <td class="flex items-center gap-2">
                            <img src="<?= base_url('uploads/perlengkapan/' . $p['foto_perlengkapan']) ?>"
                                style="width: 50px; height: 50px; object-fit: cover;">
                            <?= esc($p['nama_perlengkapan']) ?>
                        </td>
                        <td><?= $p['nama_kategori'] ?></td>
                        <td><?= esc($p['stok']) ?></strong></td>
                        <td>Rp <?= number_format($p['harga_sewa'], 0, ',', '.') ?>/ hari</td>
                        <td>
                            <div class="action-btns">

                                <a href="<?= base_url('perlengkapan/ubah/' . $p['id_perlengkapan']) ?>"
                                    class="act-btn act-btn-edit" title="Edit">
                                    <i class="ri-pencil-line"></i>
                                </a>
                                <a href="<?= base_url('perlengkapan/hapus/' . $p['id_perlengkapan']) ?>"
                                    class="act-btn act-btn-del btn-delete" title="Hapus">
                                    <i class="ri-delete-bin-line"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- DataTables assets: pindahkan ke template.php kalau mau dipakai global di semua halaman admin -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<style>
/* sembunyikan default search & info bawaan DataTables, kita pakai search-pill custom */
#recentTable_filter,
#recentTable_info {
    display: none;
}

/* rapikan pagination biar nyatu sama style admin */
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {

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
        },
        columnDefs: [{
                orderable: false,
                targets: [2, 6]
            } // kolom foto & aksi tidak perlu sortable
        ]
    });

    // hubungkan search-pill custom ke DataTables
    document.getElementById('tableSearch').addEventListener('keyup', function() {
        table.search(this.value).draw();
    });

    // delegasi event delete karena baris di-render ulang oleh DataTables saat paging/search
    $('#recentTable tbody').on('click', '.btn-delete', function(e) {

        e.preventDefault();

        const url = this.href;

        Swal.fire({

            title: 'Hapus Data?',

            html: `
                <div style="margin-top:10px">
                    <div style="
                        width:80px;
                        height:80px;
                        background:#ff00004c;
                        border-radius:50%;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        margin:auto;">
                        <i class="ri-delete-bin-6-line"
                           style="font-size:42px;color:#ff0000;"></i>
                    </div>

                    <p style="margin-top:20px">
                        Apakah Anda yakin ingin menghapus data ini?
                    </p>

                    <small style="color:#999">
                        Data yang telah dihapus tidak dapat dikembalikan.
                    </small>
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

            if (result.isConfirmed) {

                window.location.href = url;

            }

        });

    });

});
</script>

<?= $this->endSection() ?>