<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKemasanToMasterLimbahB3 extends Migration
{
    public function up()
    {
        // Add kemasan column to master_limbah_b3 table if it doesn't exist
        if (!$this->db->fieldExists('kemasan', 'master_limbah_b3')) {
            $this->forge->addColumn('master_limbah_b3', [
                'kemasan' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'comment'    => 'Default kemasan untuk auto-fill form',
                    'after'      => 'bentuk_fisik'
                ]
            ]);
        }

        // Update existing data with sample kemasan values
        $this->db->query("
            UPDATE master_limbah_b3 SET 
                bentuk_fisik = CASE 
                    WHEN nama_limbah LIKE '%Oli%' THEN 'Cair'
                    WHEN nama_limbah LIKE '%Gemuk%' THEN 'Pasta'
                    WHEN nama_limbah LIKE '%Kain%' THEN 'Padat'
                    WHEN nama_limbah LIKE '%Karbon%' THEN 'Bubuk'
                    WHEN nama_limbah LIKE '%Asam%' OR nama_limbah LIKE '%Basa%' THEN 'Cair'
                    ELSE 'Padat'
                END,
                kemasan = CASE 
                    WHEN nama_limbah LIKE '%Oli%' THEN 'Drum 200L'
                    WHEN nama_limbah LIKE '%Gemuk%' THEN 'Drum 100L'
                    WHEN nama_limbah LIKE '%Kain%' THEN 'Karung'
                    WHEN nama_limbah LIKE '%Karbon%' THEN 'Karung'
                    WHEN nama_limbah LIKE '%Asam%' OR nama_limbah LIKE '%Basa%' THEN 'Jerrycan 20L'
                    ELSE 'Karung'
                END
            WHERE bentuk_fisik IS NULL OR kemasan IS NULL
        ");
    }

    public function down()
    {
        if ($this->db->fieldExists('kemasan', 'master_limbah_b3')) {
            $this->forge->dropColumn('master_limbah_b3', 'kemasan');
        }
    }
}