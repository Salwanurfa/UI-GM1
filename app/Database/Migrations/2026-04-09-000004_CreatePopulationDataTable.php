<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePopulationDataTable extends Migration
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
            'jumlah_dosen' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'Jumlah dosen aktif'
            ],
            'jumlah_mahasiswa' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'Jumlah mahasiswa aktif'
            ],
            'jumlah_tenaga_kependidikan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'Jumlah tenaga kependidikan/staff'
            ],
            'total_populasi' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'Total populasi kampus (auto calculated)'
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
        
        $this->forge->createTable('population_data');
    }

    public function down()
    {
        $this->forge->dropTable('population_data');
    }
}