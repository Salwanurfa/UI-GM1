<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransportStatsTable extends Migration
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
            'periode' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
                'comment'    => 'Contoh: Minggu 1 - April 2026, Bulan Mei 2026'
            ],
            'kategori_kendaraan' => [
                'type'       => 'ENUM',
                'constraint' => ['Roda Dua', 'Roda Empat', 'Sepeda', 'Kendaraan Umum'],
                'null'       => false,
            ],
            'jenis_bahan_bakar' => [
                'type'       => 'ENUM',
                'constraint' => ['Bensin', 'Diesel', 'Listrik', 'Non-BBM'],
                'null'       => false,
            ],
            'jumlah_total' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'Jumlah kendaraan yang terhitung'
            ],
            'input_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'comment'    => 'ID Security yang menginput'
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
        $this->forge->addKey('periode');
        $this->forge->addKey('input_by');
        $this->forge->addKey('created_at');
        
        // Add foreign key constraint to users table
        $this->forge->addForeignKey('input_by', 'users', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('transport_stats');
    }

    public function down()
    {
        $this->forge->dropTable('transport_stats');
    }
}