<?php

// File: app/Database/Migrations/2026-01-02-100000_CreateKriteriaUigmTable.php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKriteriaUigmTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'unit_prodi' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'jenis_sampah' => [
                'type' => 'ENUM',
                'constraint' => ['Kertas', 'Plastik', 'Organik', 'Anorganik', 'Limbah Cair', 'B3'],
                'null' => false,
            ],
            'satuan' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => false,
            ],
            'jumlah' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
            ],
            'gedung' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey(['user_id', 'tanggal']);
        $this->forge->addKey('jenis_sampah');
        
        // Add foreign key constraint to users table
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('kriteria_uigm');
    }

    public function down()
    {
        $this->forge->dropTable('kriteria_uigm');
    }
}