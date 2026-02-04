<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRekapSampahColumns extends Migration
{
    public function up()
    {
        // Add columns to waste_management table for rekap sampah functionality
        $fields = [
            'nama_sampah' => [
                'type' => 'VARCHAR',
                'constraint' => 191,
                'null' => true,
                'after' => 'jenis_sampah'
            ],
            'nama_sampah_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'nama_sampah'
            ],
            'gedung_name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
                'after' => 'gedung'
            ],
            'user_name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
                'after' => 'user_id'
            ],
            'confirmed_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'admin_reviewed_at'
            ]
        ];

        $this->forge->addColumn('waste_management', $fields);

        // Add indexes for performance
        $this->db->query('ALTER TABLE waste_management ADD INDEX idx_created_at (created_at)');
        $this->db->query('ALTER TABLE waste_management ADD INDEX idx_nama_sampah (nama_sampah)');
        $this->db->query('ALTER TABLE waste_management ADD INDEX idx_gedung_id (unit_id)');
        $this->db->query('ALTER TABLE waste_management ADD INDEX idx_status (status)');
        $this->db->query('ALTER TABLE waste_management ADD INDEX idx_confirmed_at (confirmed_at)');

        log_message('info', 'Migration AddRekapSampahColumns: Columns and indexes added successfully');
    }

    public function down()
    {
        // Drop indexes first
        $this->db->query('ALTER TABLE waste_management DROP INDEX IF EXISTS idx_created_at');
        $this->db->query('ALTER TABLE waste_management DROP INDEX IF EXISTS idx_nama_sampah');
        $this->db->query('ALTER TABLE waste_management DROP INDEX IF EXISTS idx_gedung_id');
        $this->db->query('ALTER TABLE waste_management DROP INDEX IF EXISTS idx_status');
        $this->db->query('ALTER TABLE waste_management DROP INDEX IF EXISTS idx_confirmed_at');

        // Drop columns
        $this->forge->dropColumn('waste_management', ['nama_sampah', 'nama_sampah_id', 'gedung_name', 'user_name', 'confirmed_at']);

        log_message('info', 'Migration AddRekapSampahColumns: Rolled back successfully');
    }
}
