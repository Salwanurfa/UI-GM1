<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEvidenceColumns extends Migration
{
    public function up()
    {
        // Add keterangan_bukti column to waste_management table if it doesn't exist
        if (!$this->db->fieldExists('keterangan_bukti', 'waste_management')) {
            $this->forge->addColumn('waste_management', [
                'keterangan_bukti' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'comment' => 'Keterangan untuk bukti dukung yang diunggah'
                ]
            ]);
        }

        // Add keterangan_bukti column to limbah_b3 table if it exists and doesn't have the column
        if ($this->db->tableExists('limbah_b3') && !$this->db->fieldExists('keterangan_bukti', 'limbah_b3')) {
            $this->forge->addColumn('limbah_b3', [
                'keterangan_bukti' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'comment' => 'Keterangan untuk bukti dukung yang diunggah'
                ]
            ]);
        }

        // Create evidence upload log table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'record_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'source_table' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'file_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'uploaded_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'upload_date' => [
                'type' => 'DATETIME',
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addKey(['record_id', 'source_table']);
        $this->forge->createTable('evidence_upload_log', true);
    }

    public function down()
    {
        // Remove columns
        if ($this->db->fieldExists('keterangan_bukti', 'waste_management')) {
            $this->forge->dropColumn('waste_management', 'keterangan_bukti');
        }

        if ($this->db->tableExists('limbah_b3') && $this->db->fieldExists('keterangan_bukti', 'limbah_b3')) {
            $this->forge->dropColumn('limbah_b3', 'keterangan_bukti');
        }

        // Drop log table
        $this->forge->dropTable('evidence_upload_log', true);
    }
}