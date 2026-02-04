<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJenisSampahTable extends Migration
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
            'parent_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'unique'     => true,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'level' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'comment'    => '1=kategori utama, 2=area, 3=detail',
            ],
            'urutan' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 0,
            ],
            'status_aktif' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
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
        $this->forge->addKey('parent_id');
        $this->forge->addKey(['kode'], false, true); // unique key
        $this->forge->createTable('jenis_sampah', true); // true = IF NOT EXISTS

        // Insert data struktur sampah organik hanya jika tabel kosong
        $count = $this->db->table('jenis_sampah')->countAll();
        if ($count == 0) {
            $data = [
                // Level 1 - Kategori Utama
                [
                    'id' => 1,
                    'parent_id' => null,
                    'kode' => 'organik',
                    'nama' => 'Sampah Organik',
                    'level' => 1,
                    'urutan' => 1,
                    'status_aktif' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],

                // Level 2 - Area Sampah
                [
                    'id' => 2,
                    'parent_id' => 1,
                    'kode' => 'kantin',
                    'nama' => 'Sampah dari Kantin',
                    'level' => 2,
                    'urutan' => 1,
                    'status_aktif' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 3,
                    'parent_id' => 1,
                    'kode' => 'lingkungan_kampus',
                    'nama' => 'Sampah dari Lingkungan Kampus',
                    'level' => 2,
                    'urutan' => 2,
                    'status_aktif' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],

                // Level 3 - Detail Sampah Kantin
                [
                    'id' => 4,
                    'parent_id' => 2,
                    'kode' => 'sisa_makanan_sayuran',
                    'nama' => 'Sisa Makanan atau Sayuran',
                    'level' => 3,
                    'urutan' => 1,
                    'status_aktif' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 5,
                    'parent_id' => 2,
                    'kode' => 'sisa_buah_buahan',
                    'nama' => 'Sisa Buah-buahan',
                    'level' => 3,
                    'urutan' => 2,
                    'status_aktif' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 6,
                    'parent_id' => 2,
                    'kode' => 'produk_sisa_dapur',
                    'nama' => 'Produk Sisa Dapur (Ampas Kopi/Teh, Kulit Telur)',
                    'level' => 3,
                    'urutan' => 3,
                    'status_aktif' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],

                // Level 3 - Detail Sampah Lingkungan Kampus
                [
                    'id' => 7,
                    'parent_id' => 3,
                    'kode' => 'daun_kering_gugur',
                    'nama' => 'Daun-daun Kering yang Gugur',
                    'level' => 3,
                    'urutan' => 1,
                    'status_aktif' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 8,
                    'parent_id' => 3,
                    'kode' => 'rumput_dipotong',
                    'nama' => 'Rumput yang Dipotong',
                    'level' => 3,
                    'urutan' => 2,
                    'status_aktif' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'id' => 9,
                    'parent_id' => 3,
                    'kode' => 'ranting_pohon_kecil',
                    'nama' => 'Ranting-ranting Pohon Kecil',
                    'level' => 3,
                    'urutan' => 3,
                    'status_aktif' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
            ];

            $this->db->table('jenis_sampah')->insertBatch($data);
        }
    }

    public function down()
    {
        $this->forge->dropTable('jenis_sampah');
    }
}
