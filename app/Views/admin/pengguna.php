<?= $this->extend('admin/layout/template') ?>
<?= $this->section('content') ?>
<div class="table-container w-full grid gap-3">

    <div class="stat-card animate-up delay-1">
        <div class="stat-card-top">
            <span class="stat-label">Total Pengguna Sistem</span>
            <div class="stat-icon">
                <i class="ri-group-line"></i>
            </div>
        </div>
        <div class="stat-value"><?= count($user); ?></div>
        <div class="stat-desc">
            Total akun terdaftar dalam sistem
        </div>
    </div>

    <div class="table-card animate-up delay-3">
        <div class="table-card-header">
            <h3 class="table-card-title">Pengguna</h3>
            <div class="flex items-center gap-3">
                <div class="search-pill">
                    <i class="ri-search-line"></i>
                    <input type="text" id="tableSearch" placeholder="Cari Pengguna...">
                </div>
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table class="data-table" id="recentTable" style="width:100%">
                <thead>
                    <tr>
                        <th>ID User</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Nomor WhatsApp</th>
                        <th>Foto KTP</th>
                        <th>Status KTP</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="recentTableBody">
                    <?php foreach ($user as $u): ?>

                    <tr>
                        <td><strong><?= $u['id_user'] ?></strong></td>
                        <td><?= esc($u['username']) ?></td>
                        <td><?= esc($u['email']) ?></td>
                        <td><?= esc($u['nomor_wa']) ?></td>
                        <td>
                            <?php if (! empty($u['foto_ktp'])) : ?>
                            <a href="<?= base_url('pengguna/ktp/' . $u['foto_ktp']) ?>" target="_blank">
                                <img src="<?= base_url('pengguna/ktp/' . $u['foto_ktp']) ?>" alt="ktp"
                                    style="width: 40px; height: 40px; object-fit: cover; margin-right:4px; border-radius:6px;">
                            </a>
                            <?php else : ?>
                            <span style="color:#999; font-size:0.8rem;">Belum upload</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                                $statusKtpUser = $u['status_ktp'] ?? 'belum_upload';
                                $statusKtpMap = [
                                    'belum_upload'        => ['Belum Upload', 'badge-secondary'],
                                    'menunggu_verifikasi' => ['Menunggu Verifikasi', 'badge-warning'],
                                    'terverifikasi'       => ['Terverifikasi', 'badge-success'],
                                    'ditolak'             => ['Ditolak', 'badge-danger'],
                                ];
                                [$labelKtpUser, $classKtpUser] = $statusKtpMap[$statusKtpUser] ?? $statusKtpMap['belum_upload'];
                                ?>
                            <span class="badge <?= $classKtpUser ?>"
                                <?= $statusKtpUser === 'menunggu_verifikasi' ? 'style="background:rgba(234,179,8,0.12); color:#a16207;"' : '' ?>>
                                <?= esc($labelKtpUser) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge <?= $u['level'] === 'admin' ? 'badge-success' : 'badge-secondary' ?>">
                                <?= $u['level'] === 'admin' ? 'Admin' : 'User' ?>
                            </span>
                            <?php if ((int) $u['id_user'] === (int) session()->get('id_user')) : ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-btns">
                                <a href="<?= base_url('pengguna/ubah/' . $u['id_user']) ?>" class="act-btn act-btn-edit"
                                    title="Edit">
                                    <i class="ri-pencil-line"></i>
                                </a>

                                <?php if ((int) $u['id_user'] !== (int) session()->get('id_user')) : ?>
                                <form action="<?= base_url('pengguna/hapus/' . $u['id_user']) ?>" method="post"
                                    class="form-hapus-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="act-btn act-btn-del btn-delete" title="Hapus">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                                <?php else : ?>
                                <span class="act-btn act-btn-disabled" title="Tidak bisa menghapus akun sendiri">
                                    <i class="ri-delete-bin-line"></i>
                                </span>
                                <?php endif; ?>
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
            targets: [
                5
            ] // kolom Aksi tidak perlu sortable (indeks bergeser karena ada kolom Status KTP baru)
        }]
    });

    document.getElementById('tableSearch').addEventListener('keyup', function() {
        table.search(this.value).draw();
    });

    $('#recentTable tbody').on('click', '.btn-delete', function(e) {
        e.preventDefault();
        const form = this.closest('form');

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
                form.submit();
            }
        });
    });

});
</script>

<?= $this->endSection() ?>