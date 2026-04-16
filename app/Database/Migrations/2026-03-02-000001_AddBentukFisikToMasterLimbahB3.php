<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBentukFisikToMasterLimbahB3 extends Migration
{
    public function up()
    {
        // Check if column already exists
        if (!$this->db->fieldExists('bentuk_fisik', 'master_limbah_b3')) {
            $this->forge->addColumn('master_limbah_b3', [
                'bentuk_fisik' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'karakteristik'
                ]
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('bentuk_fisik', 'master_limbah_b3')) {
            $this->forge->dropColumn('master_limbah_b3', 'bentuk_fisik');
        }
    }
}