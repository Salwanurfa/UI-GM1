<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRiwayatVersiTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'pengiriman_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'versi' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'aksi' => [
                'type'       => 'ENUM',
                'constraint' => ['create', 'update', 'submit', 'review', 'approve', 'reject', 'finalize'],
            ],
            'deskripsi_perubahan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'data_sebelum' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'data_sesudah' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
                'null'       => true,
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('pengiriman_id');
        $this->forge->addKey('user_id');
        $this->forge->addForeignKey('pengiriman_id', 'pengiriman_unit', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('riwayat_versi');
    }

    public function down()
    {
        $this->forge->dropTable('riwayat_versi');
    }
}
