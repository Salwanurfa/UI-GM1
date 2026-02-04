<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTpsFieldsToWasteManagement extends Migration
{
    public function up()
    {
        // Add TPS specific fields to waste_management table
        $fields = [
            'pengirim_gedung' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Gedung pengirim sampah untuk TPS',
                'after' => 'gedung'
            ],
            'kategori_sampah' => [
                'type' => 'ENUM',
                'constraint' => ['bisa_dijual', 'tidak_bisa_dijual'],
                'null' => true,
                'comment' => 'Kategori sampah untuk TPS',
                'after' => 'pengirim_gedung'
            ],
            'nilai_rupiah' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
                'comment' => 'Nilai rupiah untuk sampah yang bisa dijual',
                'after' => 'kategori_sampah'
            ]
        ];
        
        $this->forge->addColumn('waste_management', $fields);
    }

    public function down()
    {
        // Remove TPS specific fields
        $this->forge->dropColumn('waste_management', ['pengirim_gedung', 'kategori_sampah', 'nilai_rupiah']);
    }
}