<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKategoriToBuktiDukung extends Migration
{
    public function up()
    {
        $fields = [
            'kategori' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'periode'
            ]
        ];
        
        $this->forge->addColumn('bukti_dukung', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('bukti_dukung', 'kategori');
    }
}
