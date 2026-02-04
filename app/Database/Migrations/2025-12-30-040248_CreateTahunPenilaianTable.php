<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTahunPenilaianTable extends Migration
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
            'tahun' => [
                'type'       => 'YEAR',
                'unique'     => true,
            ],
            'nama_periode' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'tanggal_mulai' => [
                'type' => 'DATE',
            ],
            'tanggal_selesai' => [
                'type' => 'DATE',
            ],
            'status_aktif' => [
                'type'    => 'BOOLEAN',
                'default' => false,
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
        $this->forge->createTable('tahun_penilaian');
    }

    public function down()
    {
        $this->forge->dropTable('tahun_penilaian');
    }
}
