<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKategoriSederhanaToTransportStats extends Migration
{
    public function up()
    {
        // Add kategori_sederhana column
        $fields = [
            'kategori_sederhana' => [
                'type' => 'ENUM',
                'constraint' => ['Roda Dua', 'Roda Empat', 'Fasilitas Kampus'],
                'null' => true,
                'after' => 'kategori_kendaraan'
            ]
        ];
        
        $this->forge->addColumn('transport_stats', $fields);
        
        // Update existing data based on rules
        $db = \Config\Database::connect();
        
        // Rule 1: Listrik atau Sepeda → Fasilitas Kampus
        $db->query("
            UPDATE transport_stats 
            SET kategori_sederhana = 'Fasilitas Kampus' 
            WHERE jenis_bahan_bakar = 'Listrik' 
               OR jenis_bahan_bakar = 'Non-BBM'
               OR kategori_kendaraan LIKE '%Sepeda%'
        ");
        
        // Rule 2: Sepeda Motor, Roda Dua → Roda Dua
        $db->query("
            UPDATE transport_stats 
            SET kategori_sederhana = 'Roda Dua' 
            WHERE kategori_sederhana IS NULL
              AND (kategori_kendaraan LIKE '%Motor%' 
                   OR kategori_kendaraan LIKE '%Roda Dua%'
                   OR kategori_kendaraan LIKE '%Roda 2%')
        ");
        
        // Rule 3: Mobil, Bus, Truck → Roda Empat
        $db->query("
            UPDATE transport_stats 
            SET kategori_sederhana = 'Roda Empat' 
            WHERE kategori_sederhana IS NULL
              AND (kategori_kendaraan LIKE '%Mobil%' 
                   OR kategori_kendaraan LIKE '%Bus%'
                   OR kategori_kendaraan LIKE '%Truck%'
                   OR kategori_kendaraan LIKE '%Roda Empat%'
                   OR kategori_kendaraan LIKE '%Roda 4%')
        ");
        
        // Default: Jika masih NULL, set berdasarkan bahan bakar
        $db->query("
            UPDATE transport_stats 
            SET kategori_sederhana = 'Roda Dua' 
            WHERE kategori_sederhana IS NULL
              AND jenis_bahan_bakar = 'Bensin'
        ");
        
        $db->query("
            UPDATE transport_stats 
            SET kategori_sederhana = 'Roda Empat' 
            WHERE kategori_sederhana IS NULL
        ");
    }

    public function down()
    {
        $this->forge->dropColumn('transport_stats', 'kategori_sederhana');
    }
}
