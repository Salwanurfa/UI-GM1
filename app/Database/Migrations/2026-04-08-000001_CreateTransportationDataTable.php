<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransportationDataTable extends Migration
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
            'tanggal' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'waktu' => [
                'type' => 'TIME',
                'null' => false,
            ],
            'jenis_kendaraan' => [
                'type'       => 'ENUM',
                'constraint' => ['motor', 'mobil', 'bus', 'truk', 'sepeda'],
                'null'       => false,
            ],
            'plat_nomor' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
            ],
            'tujuan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['masuk', 'keluar'],
                'null'       => false,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'petugas_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
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
        $this->forge->addKey('tanggal');
        $this->forge->addKey('petugas_id');
        $this->forge->addForeignKey('petugas_id', 'users', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('transportation_data');
    }

    public function down()
    {
        $this->forge->dropTable('transportation_data');
    }
}