<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMasterLimbahCairTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_limbah' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'kode_limbah' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'deskripsi_sumber' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'parameter_wajib' => [
                'type'       => 'VARCHAR',
                'constraint' => '500',
                'null'       => true,
                'comment'    => 'Comma-separated list of required parameters'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('master_limbah_cair');
    }

    public function down()
    {
        $this->forge->dropTable('master_limbah_cair');
    }
}
