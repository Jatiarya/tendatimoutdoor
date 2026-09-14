<?php
// app/Database/Migrations/2025-07-30-000001_CreateLaporanPenyewaanTable.php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLaporanPenyewaanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_laporan'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'id_sewa'             => ['type' => 'INT', 'unsigned' => true],
            'id_user'             => ['type' => 'INT', 'unsigned' => true],
            'id_perlengkapan'     => ['type' => 'INT', 'unsigned' => true],
            'username'            => ['type' => 'VARCHAR', 'constraint' => 100],
            'nama_perlengkapan'   => ['type' => 'VARCHAR', 'constraint' => 150],
            'jumlah'              => ['type' => 'INT'],
            'tgl_sewa'            => ['type' => 'DATE'],
            'tgl_kembali_rencana' => ['type' => 'DATE'],
            'tgl_kembali_aktual'  => ['type' => 'DATE'],
            'durasi_hari'         => ['type' => 'INT'],
            'total_biaya'         => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'created_at'          => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id_laporan');
        $this->forge->addUniqueKey('id_sewa'); // 1 sewa cuma boleh masuk laporan 1x
        $this->forge->addForeignKey('id_sewa', 'penyewaan', 'id_sewa', 'CASCADE', 'CASCADE');
        $this->forge->createTable('laporan_penyewaan');
    }

    public function down()
    {
        $this->forge->dropTable('laporan_penyewaan');
    }
}