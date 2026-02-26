<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLimbahB3Tables extends Migration
{
    public function up()
    {
        /**
         * Tabel transaksi Limbah B3
         * Mengikuti rancangan: relasi ke users dan master_limbah_b3,
         * tanpa kolom harga atau nilai jual.
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_user' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => false,
            ],
            'master_b3_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => false,
            ],
            'lokasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'timbulan' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,3',
                'null'       => false,
            ],
            'satuan' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
            ],
            'bentuk_fisik' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'kemasan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => [
                    'draf',
                    'dikirim_ke_tps',
                    'disetujui_tps',
                    'ditolak_tps',
                    'disetujui_admin',
                ],
                'default' => 'draf',
                'null'    => false,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'tanggal_input' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('id_user');
        $this->forge->addKey('master_b3_id');
        $this->forge->addKey('status');
        $this->forge->createTable('limbah_b3', true);

        /**
         * Tabel master jenis Limbah B3
         * Digunakan untuk auto-lookup kode, kategori bahaya, dan karakteristik.
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_limbah' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'kode_limbah' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'kategori_bahaya' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'karakteristik' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'status_aktif' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 1,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('nama_limbah');
        $this->forge->createTable('master_limbah_b3', true);

        // Seed awal beberapa jenis Limbah B3 agar auto-lookup langsung bisa digunakan
        $this->db->table('master_limbah_b3')->insertBatch([
            [
                'nama_limbah'     => 'Oli Bekas',
                'kode_limbah'     => 'B105d',
                'kategori_bahaya' => 'Kat 2',
                'karakteristik'   => 'Beracun',
                'status_aktif'    => 1,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'nama_limbah'     => 'Gemuk',
                'kode_limbah'     => 'B105e',
                'kategori_bahaya' => 'Kat 2',
                'karakteristik'   => 'Beracun',
                'status_aktif'    => 1,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'nama_limbah'     => 'Kain Bekas',
                'kode_limbah'     => 'B109d',
                'kategori_bahaya' => 'Kat 2',
                'karakteristik'   => 'Beracun',
                'status_aktif'    => 1,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'nama_limbah'     => 'Karbon Aktif',
                'kode_limbah'     => 'B110d',
                'kategori_bahaya' => 'Kat 2',
                'karakteristik'   => 'Beracun',
                'status_aktif'    => 1,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'nama_limbah'     => 'Limbah Asam/Basa/Organik',
                'kode_limbah'     => 'B225d',
                'kategori_bahaya' => 'Kat 1',
                'karakteristik'   => 'Korosif/Beracun',
                'status_aktif'    => 1,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('limbah_b3', true);
        $this->forge->dropTable('master_limbah_b3', true);
    }
}

