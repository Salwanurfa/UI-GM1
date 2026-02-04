<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateWasteFlowToTPS extends Migration
{
    public function up()
    {
        // Add new columns for TPS review
        $fields = [
            'tps_reviewed_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'created_by'
            ],
            'tps_reviewed_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'tps_reviewed_by'
            ],
            'tps_catatan' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'tps_reviewed_at'
            ],
            'admin_reviewed_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'tps_catatan'
            ],
            'admin_reviewed_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'admin_reviewed_by'
            ],
            'admin_catatan' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'admin_reviewed_at'
            ],
        ];
        
        $this->forge->addColumn('waste_management', $fields);
        
        // Modify status column to include new statuses
        $this->db->query("
            ALTER TABLE waste_management 
            MODIFY COLUMN status ENUM(
                'draft', 
                'dikirim_ke_tps', 
                'disetujui_tps', 
                'ditolak_tps', 
                'dikirim_ke_admin', 
                'disetujui', 
                'ditolak'
            ) NOT NULL DEFAULT 'draft'
        ");
        
        // Update existing data: 'dikirim' -> 'dikirim_ke_admin' (skip TPS for old data)
        $this->db->query("
            UPDATE waste_management 
            SET status = 'dikirim_ke_admin' 
            WHERE status = 'dikirim'
        ");
        
        log_message('info', 'Migration UpdateWasteFlowToTPS: Added TPS review columns and updated status enum');
    }

    public function down()
    {
        // Remove added columns
        $this->forge->dropColumn('waste_management', [
            'tps_reviewed_by',
            'tps_reviewed_at',
            'tps_catatan',
            'admin_reviewed_by',
            'admin_reviewed_at',
            'admin_catatan'
        ]);
        
        // Revert status column
        $this->db->query("
            ALTER TABLE waste_management 
            MODIFY COLUMN status ENUM('draft', 'dikirim', 'disetujui', 'ditolak') 
            NOT NULL DEFAULT 'draft'
        ");
        
        // Revert data: 'dikirim_ke_admin' -> 'dikirim'
        $this->db->query("
            UPDATE waste_management 
            SET status = 'dikirim' 
            WHERE status = 'dikirim_ke_admin'
        ");
        
        log_message('info', 'Migration UpdateWasteFlowToTPS: Rolled back changes');
    }
}
