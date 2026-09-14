<?= $this->extend('admin/layout/template') ?>
<?= $this->section('content') ?>

<div class="table-container w-full grid gap-3">

    <div class="table-card animate-up delay-1">
        <div class="table-card-header">
            <h3 class="table-card-title">Diskon & Voucher</h3>
            <div class="flex items-center gap-3">
                <div class="search-pill">
                    <i class="ri-search-line"></i>
                    <input type="text" id="tableSearch" placeholder="Cari Diskon...">
                </div>
                <a href="<?= base_url('diskon/tambah') ?>" class="btn-primary" style="padding:9px 18px;">
                    Tambah<i class="ri-add-line"></i>
                </a>
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table class="data-table" id="recentTable" style="width:100%">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Kode</th>
                        <th>Tipe</th>
                        <th>Nilai</th>
                        <th>Minimal Belanja</th>
                        <th>Periode</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $d): ?>
                        <tr>
                            <td><strong><?= esc($d['nama']) ?></strong></td>
                            <td><?= $d['kode_voucher'] ? '<span class="badge badge-secondary">' . esc($d['kode_voucher']) . '</span>' : '<em>Otomatis (event)</em>' ?>
                            </td>
                            <td><?= $d['tipe'] === 'persen' ? 'Persentase' : 'Nominal' ?></td>
                            <td>
                                <?= $d['tipe'] === 'persen' ? $d['nilai'] . '%' : 'Rp ' . number_format($d['nilai'], 0, ',', '.') ?>
                                <?php if ($d['maks_potongan']): ?>
                                    <br><small>maks Rp <?= number_format($d['maks_potongan'], 0, ',', '.') ?></small>
                                <?php endif; ?>
                                <?php if (! empty($d['minimal_belanja'])): ?>
                                    <br><small style="color:#e8a020;">min. belanja Rp
                                        <?= number_format($d['minimal_belanja'], 0, ',', '.') ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $d['tipe'] === 'persen' ? $d['nilai'] . '%' : 'Rp ' . number_format($d['nilai'], 0, ',', '.') ?>
                                <?php if ($d['maks_potongan']): ?>
                                    <br><small>maks Rp <?= number_format($d['maks_potongan'], 0, ',', '.') ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($d['tgl_mulai']) ?> s/d <?= esc($d['tgl_selesai']) ?></td>
                            <td>
                                <span class="badge <?= $d['status'] === 'aktif' ? 'badge-success' : 'badge-secondary' ?>">
                                    <?= ucfirst($d['status']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="<?= base_url('diskon/ubah/' . $d['id_diskon']) ?>" class="act-btn act-btn-edit"
                                        title="Edit">
                                        <i class="ri-pencil-line"></i>
                                    </a>
                                    <form action="<?= base_url('diskon/hapus/' . $d['id_diskon']) ?>" method="post"
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
                targets: [6] // kolom Aksi tidak perlu sortable
            }]
        });

        // hubungkan search-pill custom ke DataTables
        document.getElementById('tableSearch').addEventListener('keyup', function() {
            table.search(this.value).draw();
        });

        // delegasi event karena baris di-render ulang oleh DataTables saat paging/search
        $('#recentTable tbody').on('click', '.btn-delete', function(e) {
            e.preventDefault();
            const form = this.closest('form');

            Swal.fire({
                title: 'Hapus Diskon?',
                html: `
                <div style="margin-top:10px">
                    <div style="width:80px;height:80px;background:#ff00004c;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:auto;">
                        <i class="ri-delete-bin-6-line" style="font-size:42px;color:#ff0000;"></i>
                    </div>
                    <p style="margin-top:20px">Apakah Anda yakin ingin menghapus diskon ini?</p>
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
</script>

<?= $this->endSection() ?>