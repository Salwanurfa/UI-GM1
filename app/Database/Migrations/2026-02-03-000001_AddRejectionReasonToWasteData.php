<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRejectionReasonToWasteData extends Migration
{
    public function up()
    {
        // Add rejection_reason column to waste_data table
        $fields = [
            'rejection_reason' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'status'
            ],
            'reviewed_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'rejection_reason'
            ],
            'reviewed_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'reviewed_by'
            ]
        ];
        
        $this->forge->addColumn('waste_data', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('waste_data', ['rejection_reason', 'reviewed_by', 'reviewed_at']);
    }
}
