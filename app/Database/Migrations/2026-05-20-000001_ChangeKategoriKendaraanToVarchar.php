<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeKategoriKendaraanToVarchar extends Migration
{
    public function up()
    {
        // Change kategori_kendaraan from ENUM to VARCHAR to accept any vehicle type
        $fields = [
            'kategori_kendaraan' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ]
        ];
        
        $this->forge->modifyColumn('transport_stats', $fields);
        
        // Update existing data: copy kategori_sederhana to kategori_kendaraan if empty
        $db = \Config\Database::connect();
        $db->query("
            UPDATE transport_stats 
            SET kategori_kendaraan = kategori_sederhana 
            WHERE kategori_kendaraan IN ('', 'Roda Dua', 'Roda Empat', 'Sepeda', 'Kendaraan Umum')
               OR kategori_kendaraan IS NULL
        ");
    }

    public function down()
    {
        // Revert back to ENUM (not recommended, but for rollback purposes)
        $fields = [
            'kategori_kendaraan' => [
                'type' => 'ENUM',
                'constraint' => ['Roda Dua', 'Roda Empat', 'Sepeda', 'Kendaraan Umum'],
                'null' => false,
            ]
        ];
        
        $this->forge->modifyColumn('transport_stats', $fields);
    }
}
