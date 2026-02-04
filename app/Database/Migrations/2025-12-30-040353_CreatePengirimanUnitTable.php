<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePengirimanUnitTable extends Migration
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
            'unit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tahun_penilaian_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'status_pengiriman' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'dikirim', 'review', 'perlu_revisi', 'disetujui', 'final'],
                'default'    => 'draft',
            ],
            'progress_persen' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
            ],
            'tanggal_kirim' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'tanggal_review' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'tanggal_disetujui' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'reviewer_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'catatan_admin_pusat' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'catatan_admin_unit' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'versi' => [
                'type'    => 'INT',
                'constraint' => 11,
                'default' => 1,
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
        $this->forge->addKey('unit_id');
        $this->forge->addKey('tahun_penilaian_id');
        $this->forge->addKey('reviewer_id');
        $this->forge->addForeignKey('unit_id', 'unit', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tahun_penilaian_id', 'tahun_penilaian', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('reviewer_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('pengiriman_unit');
    }

    public function down()
    {
        $this->forge->dropTable('pengiriman_unit');
    }
}
