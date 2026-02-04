<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMasterHargaSampahTable extends Migration
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
            'jenis_sampah' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'nama_jenis' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'harga_per_satuan' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
                'default'    => 0.00,
            ],
            'harga_per_kg' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
                'default'    => 0.00,
            ],
            'satuan' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
                'default'    => 'kg',
            ],
            'dapat_dijual' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 1,
                'comment'    => '1=dapat dijual, 0=tidak dapat dijual',
            ],
            'status_aktif' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 1,
                'comment'    => '1=aktif, 0=nonaktif',
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'tanggal_berlaku' => [
                'type' => 'DATE',
                'null' => false,
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
        $this->forge->addUniqueKey('jenis_sampah');
        $this->forge->addKey(['status_aktif', 'dapat_dijual']);
        $this->forge->createTable('master_harga_sampah');

        // Insert sample data
        $data = [
            [
                'jenis_sampah' => 'Plastik',
                'nama_jenis' => 'Plastik (Botol, Kemasan)',
                'harga_per_satuan' => 2000.00,
                'harga_per_kg' => 2000.00,
                'satuan' => 'kg',
                'dapat_dijual' => 1,
                'status_aktif' => 1,
                'deskripsi' => 'Plastik yang dapat didaur ulang',
                'tanggal_berlaku' => date('Y-m-d'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'jenis_sampah' => 'Kertas',
                'nama_jenis' => 'Kertas (HVS, Koran, Kardus)',
                'harga_per_satuan' => 1500.00,
                'harga_per_kg' => 1500.00,
                'satuan' => 'kg',
                'dapat_dijual' => 1,
                'status_aktif' => 1,
                'deskripsi' => 'Kertas yang dapat didaur ulang',
                'tanggal_berlaku' => date('Y-m-d'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'jenis_sampah' => 'Logam',
                'nama_jenis' => 'Logam (Kaleng, Aluminium)',
                'harga_per_satuan' => 5000.00,
                'harga_per_kg' => 5000.00,
                'satuan' => 'kg',
                'dapat_dijual' => 1,
                'status_aktif' => 1,
                'deskripsi' => 'Logam yang dapat didaur ulang',
                'tanggal_berlaku' => date('Y-m-d'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'jenis_sampah' => 'Organik',
                'nama_jenis' => 'Sampah Organik',
                'harga_per_satuan' => 0.00,
                'harga_per_kg' => 0.00,
                'satuan' => 'kg',
                'dapat_dijual' => 0,
                'status_aktif' => 1,
                'deskripsi' => 'Sampah organik untuk kompos',
                'tanggal_berlaku' => date('Y-m-d'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'jenis_sampah' => 'Residu',
                'nama_jenis' => 'Sampah Residu',
                'harga_per_satuan' => 0.00,
                'harga_per_kg' => 0.00,
                'satuan' => 'kg',
                'dapat_dijual' => 0,
                'status_aktif' => 1,
                'deskripsi' => 'Sampah yang tidak dapat didaur ulang',
                'tanggal_berlaku' => date('Y-m-d'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('master_harga_sampah')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('master_harga_sampah');
    }
}