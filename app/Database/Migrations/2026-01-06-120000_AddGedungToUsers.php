<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGedungToUsers extends Migration
{
    public function up()
    {
        // Add gedung field to users table
        $fields = [
            'gedung' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Gedung default untuk user ini',
                'after' => 'unit_prodi'
            ]
        ];
        
        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        // Remove gedung field
        $this->forge->dropColumn('users', ['gedung']);
    }
}