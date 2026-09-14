<?php
// app/Database/Migrations/2025-07-28-000001_CreatePembayaranTable.php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePembayaranTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_pembayaran' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'id_sewa'       => ['type' => 'INT', 'unsigned' => true],
            'order_id'      => ['type' => 'VARCHAR', 'constraint' => 100], // dikirim ke Midtrans
            'jumlah_bayar'  => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'metode_bayar'  => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true], // qris, bank_transfer, dll (diisi setelah callback)
            'status_bayar'  => ['type' => 'ENUM', 'constraint' => ['pending', 'lunas', 'gagal', 'expired'], 'default' => 'pending'],
            'snap_token'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'payload_callback' => ['type' => 'TEXT', 'null' => true], // simpan raw notif midtrans buat audit
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id_pembayaran');
        $this->forge->addUniqueKey('order_id');
        $this->forge->addForeignKey('id_sewa', 'penyewaan', 'id_sewa', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pembayaran');
    }

    public function down()
    {
        $this->forge->dropTable('pembayaran');
    }
}