<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDefaultSatuanKemasanToMasterLimbahB3 extends Migration
{
    public function up()
    {
        // Add new columns to master_limbah_b3 table
        $fields = [
            'default_satuan' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'comment' => 'Default satuan untuk auto-fill form'
            ],
            'default_kemasan' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Default kemasan untuk auto-fill form'
            ]
        ];

        $this->forge->addColumn('master_limbah_b3', $fields);
    }

    public function down()
    {
        // Remove the columns
        $this->forge->dropColumn('master_limbah_b3', ['default_satuan', 'default_kemasan']);
    }
}