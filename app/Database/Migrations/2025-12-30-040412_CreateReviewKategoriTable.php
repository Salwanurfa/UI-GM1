<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReviewKategoriTable extends Migration
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
            'pengiriman_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'indikator_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'status_review' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'disetujui', 'perlu_revisi'],
                'default'    => 'pending',
            ],
            'catatan_review' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'reviewer_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'tanggal_review' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'skor_review' => [
                'type'       => 'DECIMAL',
                'constraint' => '8,2',
                'null'       => true,
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
        $this->forge->addKey('pengiriman_id');
        $this->forge->addKey('indikator_id');
        $this->forge->addKey('reviewer_id');
        $this->forge->addForeignKey('pengiriman_id', 'pengiriman_unit', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('indikator_id', 'indikator', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('reviewer_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('review_kategori');
    }

    public function down()
    {
        $this->forge->dropTable('review_kategori');
    }
}
