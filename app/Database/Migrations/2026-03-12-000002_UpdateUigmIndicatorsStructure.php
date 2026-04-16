<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateUigmIndicatorsStructure extends Migration
{
    public function up()
    {
        // Drop the old table if exists
        $this->forge->dropTable('uigm_indicators', true);
        
        // Create new uigm_categories table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode_kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => false,
                'comment'    => 'WS.1, WS.2, WS.3, etc.'
            ],
            'nama_kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'icon_class' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'default'    => 'fa-leaf'
            ],
            'color_class' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
                'default'    => 'primary'
            ],
            'target_capaian' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => false,
                'default'    => 0.00,
                'comment'    => 'Target dalam persentase (0.00 - 100.00)'
            ],
            'tahun' => [
                'type'       => 'YEAR',
                'constraint' => 4,
                'null'       => false,
            ],
            'status_aktif' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
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
        $this->forge->addKey(['tahun', 'kode_kategori'], false, true); // Unique combination
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('uigm_categories');

        // Create uigm_evidence table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kategori_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'nama_bukti' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
                'comment'    => 'Nama dokumen bukti (SK, SOP, Foto, dll)'
            ],
            'deskripsi_bukti' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Deskripsi detail bukti yang dibutuhkan'
            ],
            'file_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Path file bukti dukung'
            ],
            'file_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Nama file asli'
            ],
            'file_size' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'comment'    => 'Ukuran file dalam bytes'
            ],
            'file_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'MIME type file'
            ],
            'status_upload' => [
                'type'       => 'ENUM',
                'constraint' => ['belum_upload', 'sudah_upload', 'perlu_revisi'],
                'default'    => 'belum_upload',
                'null'       => false,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Catatan atau keterangan tambahan'
            ],
            'urutan' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 1,
                'null'       => false,
                'comment'    => 'Urutan tampil bukti'
            ],
            'uploaded_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'uploaded_at' => [
                'type' => 'DATETIME',
                'null' => true,
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
        $this->forge->addKey('kategori_id');
        $this->forge->addKey('urutan');
        $this->forge->addForeignKey('kategori_id', 'uigm_categories', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('uploaded_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('uigm_evidence');
    }

    public function down()
    {
        $this->forge->dropTable('uigm_evidence', true);
        $this->forge->dropTable('uigm_categories', true);
    }
}