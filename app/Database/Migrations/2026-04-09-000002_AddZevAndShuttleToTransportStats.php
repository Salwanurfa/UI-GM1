<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddZevAndShuttleToTransportStats extends Migration
{
    public function up()
    {
        $fields = [
            'is_zev' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 0,
                'comment'    => '1=Zero Emission Vehicle (ZEV), 0=Conventional Vehicle'
            ],
            'is_shuttle' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 0,
                'comment'    => '1=Shuttle/Campus Transportation Service, 0=Regular Vehicle'
            ]
        ];

        $this->forge->addColumn('transport_stats', $fields);
        
        // Add indexes for better performance
        $this->forge->addKey(['is_zev'], false, false, 'idx_transport_stats_zev');
        $this->forge->addKey(['is_shuttle'], false, false, 'idx_transport_stats_shuttle');
        $this->forge->processIndexes('transport_stats');
    }

    public function down()
    {
        // Drop indexes first
        $this->forge->dropKey('transport_stats', 'idx_transport_stats_zev');
        $this->forge->dropKey('transport_stats', 'idx_transport_stats_shuttle');
        
        // Drop columns
        $this->forge->dropColumn('transport_stats', ['is_zev', 'is_shuttle']);
    }
}