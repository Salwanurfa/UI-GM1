<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUigmIndicatorsTable extends Migration
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
                'constraint' => 4,
                'null'       => false,
            ],
            'kode_indikator' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => false,
                'comment'    => 'WS.1, WS.2, WS.3, etc.'
            ],
            'nama_indikator' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'target_capaian' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => false,
                'comment'    => 'Target dalam persentase (0.00 - 100.00)'
            ],
            'bukti_dukung' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Path file bukti dukung (PDF/JPG)'
            ],
            'status_bukti' => [
                'type'       => 'ENUM',
                'constraint' => ['belum_upload', 'sudah_upload'],
                'default'    => 'belum_upload',
                'null'       => false,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
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
        $this->forge->addKey(['tahun', 'kode_indikator'], false, true); // Unique combination
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('uigm_indicators');
    }

    public function down()
    {
        $this->forge->dropTable('uigm_indicators');
    }
}