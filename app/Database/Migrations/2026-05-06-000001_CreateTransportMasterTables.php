<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransportMasterTables extends Migration
{
    public function up()
    {
        // Table for Transport Categories (Kategori Kendaraan)
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nama_kategori' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'kode_kategori' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_zev' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '1 = Zero Emission Vehicle, 0 = Non-ZEV',
            ],
            'status_aktif' => [
                'type' => 'TINYINT',
                'constraint' => 1,
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
        $this->forge->createTable('transport_categories');

        // Table for Transport Fuels (Jenis Bahan Bakar)
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nama_bahan_bakar' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'kode_bahan_bakar' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'is_zev' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '1 = Zero Emission (Listrik/Non-BBM), 0 = Fossil Fuel',
            ],
            'status_aktif' => [
                'type' => 'TINYINT',
                'constraint' => 1,
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
        $this->forge->createTable('transport_fuels');

        // Insert default categories
        $categories = [
            ['nama_kategori' => 'Sepeda Motor (Kategori L)', 'kode_kategori' => 'L', 'is_zev' => 0],
            ['nama_kategori' => 'Mobil Penumpang (Kategori M1)', 'kode_kategori' => 'M1', 'is_zev' => 0],
            ['nama_kategori' => 'Mobil Bus (Kategori M2/M3)', 'kode_kategori' => 'M2/M3', 'is_zev' => 0],
            ['nama_kategori' => 'Kendaraan Bermotor Listrik (KBL)', 'kode_kategori' => 'KBL', 'is_zev' => 1],
            ['nama_kategori' => 'Sepeda (Tidak Bermotor)', 'kode_kategori' => 'SEPEDA', 'is_zev' => 1],
        ];

        foreach ($categories as $category) {
            $category['created_at'] = date('Y-m-d H:i:s');
            $category['updated_at'] = date('Y-m-d H:i:s');
            $this->db->table('transport_categories')->insert($category);
        }

        // Insert default fuels
        $fuels = [
            ['nama_bahan_bakar' => 'Bensin', 'kode_bahan_bakar' => 'BENSIN', 'is_zev' => 0],
            ['nama_bahan_bakar' => 'Diesel', 'kode_bahan_bakar' => 'DIESEL', 'is_zev' => 0],
            ['nama_bahan_bakar' => 'Listrik', 'kode_bahan_bakar' => 'LISTRIK', 'is_zev' => 1],
            ['nama_bahan_bakar' => 'Non-BBM', 'kode_bahan_bakar' => 'NON_BBM', 'is_zev' => 1],
        ];

        foreach ($fuels as $fuel) {
            $fuel['created_at'] = date('Y-m-d H:i:s');
            $fuel['updated_at'] = date('Y-m-d H:i:s');
            $this->db->table('transport_fuels')->insert($fuel);
        }
    }

    public function down()
    {
        $this->forge->dropTable('transport_categories', true);
        $this->forge->dropTable('transport_fuels', true);
    }
}
