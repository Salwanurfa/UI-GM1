<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInfrastructureDataTable extends Migration
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
            'tahun_akademik' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
                'comment'    => 'Contoh: 2025/2026'
            ],
            'luas_total_kampus' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => false,
                'comment'    => 'Luas total kampus dalam m²'
            ],
            'luas_area_parkir_total' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => false,
                'comment'    => 'Total luas area parkir dalam m²'
            ],
            'luas_parkir_terbuka' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
                'default'    => 0,
                'comment'    => 'Luas area parkir terbuka dalam m²'
            ],
            'luas_parkir_berkanopi' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
                'default'    => 0,
                'comment'    => 'Luas area parkir berkanopi/gedung dalam m²'
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Keterangan tambahan'
            ],
            'status_aktif' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 1,
                'comment'    => '1=aktif, 0=nonaktif'
            ],
            'input_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'comment'    => 'ID Admin yang menginput'
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
        $this->forge->addKey('tahun_akademik');
        $this->forge->addKey('status_aktif');
        
        // Add foreign key constraint to users table
        $this->forge->addForeignKey('input_by', 'users', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('infrastructure_data');
    }

    public function down()
    {
        $this->forge->dropTable('infrastructure_data');
    }
}