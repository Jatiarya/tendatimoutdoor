<?= $this->extend('admin/layout/template') ?>
<?= $this->section('content') ?>
<div class="table-container w-full grid gap-3">

    <div class="stat-card animate-up delay-1">
        <div class="stat-card-top">
            <span class="stat-label">Data Kategori</span>
            <div class="stat-icon">
                <i class="ri-shopping-bag-3-line"></i>
            </div>
        </div>
        <div class="stat-value"><?= count($kategori); ?></div>
        <div class="stat-desc">
            Last month: <span class="up">-</span>
        </div>
    </div>
    <div class="table-card animate-up delay-3">
        <div class="table-card-header">
            <h3 class="table-card-title">Kategori</h3>
            <div class="flex items-center gap-3">
                <div class="search-pill">
                    <i class="ri-search-line"></i>
                    <input type="text" id="tableSearch" placeholder="Cari Kategori...">
                </div>
                <a href="<?= base_url('kategori/tambah') ?>" class="btn-primary" style="padding:9px 18px;">
                    Tambah<i class="ri-add-line"></i>
                </a>
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table class="data-table" id="recentTable" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kode Kategori</th>
                        <th>Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="recentTableBody">
                    <?php foreach ($kategori as $k): ?>

                    <tr>
                        <td><strong><?= $k['id_kategori'] ?></strong></td>
                        <td><?= $k['kd_kategori'] ?></td>
                        <td><?= esc($k['nama_kategori']) ?></td>
                        <td>
                            <div class="action-btns">

                                <a href="<?= base_url('kategori/ubah/' . $k['id_kategori']) ?>"
                                    class="act-btn act-btn-edit" title="Edit">
                                    <i class="ri-pencil-line"></i>
                                </a>
                                <a href="kategori/hapus/<?= $k['id_kategori'] ?>" class="act-btn act-btn-del"
                                    title="Hapus" onclick="return confirm('Hapus data ini?')">
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

<!-- Kalau jQuery + DataTables sudah dipindah ke template.php, hapus 3 baris di bawah ini -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<style>
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
                targets: [3]
            } // kolom Aksi tidak perlu sortable
        ]
    });

    // hubungkan search-pill custom ke DataTables
    document.getElementById('tableSearch').addEventListener('keyup', function() {
        table.search(this.value).draw();
    });

    // tombol hapus pakai onclick="confirm(...)" bawaan HTML, jadi tetap jalan
    // meski baris di-render ulang saat paging/search — tidak perlu delegasi.
});
</script>

<?= $this->endSection() ?>