<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLogPerubahanHargaTable extends Migration
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
            'master_harga_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'jenis_sampah' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'harga_lama' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'harga_baru' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
            ],
            'perubahan_harga' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
                'default'    => 0.00,
            ],
            'persentase_perubahan' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => false,
                'default'    => 0.00,
            ],
            'alasan_perubahan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status_perubahan' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'null'       => false,
                'default'    => 'pending',
            ],
            'tanggal_berlaku' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'approved_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
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

        $this->forge->addKey('id', true);
        $this->forge->addKey('master_harga_id');
        $this->forge->addKey(['jenis_sampah', 'tanggal_berlaku']);
        $this->forge->addKey(['status_perubahan', 'created_at']);
        $this->forge->createTable('log_perubahan_harga');
    }

    public function down()
    {
        $this->forge->dropTable('log_perubahan_harga');
    }
}