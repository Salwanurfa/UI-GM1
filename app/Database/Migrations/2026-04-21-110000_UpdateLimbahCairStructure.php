<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateLimbahCairStructure extends Migration
{
    public function up()
    {
        // Drop old columns if they exist
        if ($this->db->fieldExists('jenis_limbah', 'limbah_cair')) {
            $this->forge->dropColumn('limbah_cair', 'jenis_limbah');
        }
        if ($this->db->fieldExists('volume', 'limbah_cair')) {
            $this->forge->dropColumn('limbah_cair', 'volume');
        }

        // Add new columns
        $fields = [
            'nama_limbah' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
                'after' => 'lokasi',
            ],
            'kode_limbah' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'nama_limbah',
            ],
            'tingkat_bahaya' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'kode_limbah',
            ],
            'karakteristik' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'tingkat_bahaya',
            ],
            'pengolahan' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'karakteristik',
            ],
            'timbulan' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'default' => 0,
                'after' => 'pengolahan',
            ],
            'bentuk_fisik' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'default' => 'Cair',
                'after' => 'satuan',
            ],
            'kemasan' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'bentuk_fisik',
            ],
            'ph' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'after' => 'kemasan',
            ],
            'bod' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'after' => 'ph',
            ],
            'cod' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'after' => 'bod',
            ],
            'tss' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'after' => 'cod',
            ],
        ];

        $this->forge->addColumn('limbah_cair', $fields);
    }

    public function down()
    {
        // Remove new columns
        $this->forge->dropColumn('limbah_cair', [
            'nama_limbah',
            'kode_limbah',
            'tingkat_bahaya',
            'karakteristik',
            'pengolahan',
            'timbulan',
            'bentuk_fisik',
            'kemasan',
            'ph',
            'bod',
            'cod',
            'tss',
        ]);

        // Restore old columns
        $fields = [
            'jenis_limbah' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
                'after' => 'lokasi',
            ],
            'volume' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'default' => 0,
                'after' => 'jenis_limbah',
            ],
        ];

        $this->forge->addColumn('limbah_cair', $fields);
    }
}

