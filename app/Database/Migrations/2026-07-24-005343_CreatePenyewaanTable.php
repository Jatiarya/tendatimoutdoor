<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePenyewaanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_sewa' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_user' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'id_produk' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'jumlah' => [
                'type'    => 'INT',
                'default' => 1,
            ],
            'tgl_sewa' => [
                'type' => 'DATE',
            ],
            'tgl_kembali_rencana' => [
                'type' => 'DATE',
            ],
            'tgl_kembali_aktual' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['disewa', 'dikembalikan', 'terlambat'],
                'default'    => 'disewa',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_sewa', true);

        // Pastikan tabel user & produk sudah ada duluan sebelum migration ini dijalankan,
        // karena foreign key butuh tabel tujuannya sudah tersedia.
        $this->forge->addForeignKey('id_user', 'user', 'id_user', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_produk', 'produk', 'id_produk', 'CASCADE', 'CASCADE');

        $this->forge->createTable('rental');
    }

    public function down()
    {
        $this->forge->dropTable('rental');
    }
}           