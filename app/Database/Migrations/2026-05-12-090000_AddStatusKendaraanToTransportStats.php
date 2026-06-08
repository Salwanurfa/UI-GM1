<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusKendaraanToTransportStats extends Migration
{
    public function up()
    {
        // Add status_kendaraan column
        $fields = [
            'status_kendaraan' => [
                'type' => 'ENUM',
                'constraint' => ['Milik Pribadi', 'Milik Universitas', 'Kendaraan Sewa', 'Kendaraan Umum'],
                'null' => true,
                'after' => 'kategori_sederhana'
            ]
        ];
        
        $this->forge->addColumn('transport_stats', $fields);
        
        // Set default value for existing data
        // Assume: Data dari Security = Milik Pribadi (default)
        $db = \Config\Database::connect();
        $db->query("
            UPDATE transport_stats 
            SET status_kendaraan = 'Milik Pribadi' 
            WHERE status_kendaraan IS NULL
        ");
    }

    public function down()
    {
        $this->forge->dropColumn('transport_stats', 'status_kendaraan');
    }
}
