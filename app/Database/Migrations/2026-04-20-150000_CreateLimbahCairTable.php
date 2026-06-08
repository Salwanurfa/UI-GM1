<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLimbahCairTable extends Migration
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
            'id_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'ID user yang menginput data',
            ],
            'tanggal_input' => [
                'type'    => 'DATETIME',
                'null'    => false,
                'comment' => 'Tanggal input data',
            ],
            'lokasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Lokasi/sumber limbah cair',
            ],
            'jenis_limbah' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
                'comment'    => 'Jenis limbah cair (Domestik, Laboratorium, Industri, dll)',
            ],
            'volume' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,3',
                'null'       => false,
                'comment'    => 'Volume/jumlah limbah cair',
            ],
            'satuan' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
                'default'    => 'liter',
                'comment'    => 'Satuan volume (liter, m3, dll)',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Keterangan tambahan',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'dikirim_ke_tps', 'disetujui_tps', 'ditolak_tps', 'disetujui_admin'],
                'default'    => 'draft',
                'comment'    => 'Status data limbah cair',
            ],
            'rejection_reason' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Alasan penolakan jika ditolak TPS',
            ],
            'reviewed_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'ID user TPS yang mereview',
            ],
            'reviewed_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Tanggal direview oleh TPS',
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
        $this->forge->addKey('id_user');
        $this->forge->addKey('status');
        $this->forge->addKey('tanggal_input');
        
        // Foreign key
        $this->forge->addForeignKey('id_user', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('reviewed_by', 'users', 'id', 'SET NULL', 'CASCADE');
        
        $this->forge->createTable('limbah_cair');
    }

    public function down()
    {
        $this->forge->dropTable('limbah_cair');
    }
}
