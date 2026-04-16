<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceWasteLoggingSystem extends Migration
{
    public function up()
    {
        // Enhance waste_management table with detailed logging fields
        $wasteFields = [
            'tanggal_input' => [
                'type' => 'DATE',
                'null' => false,
                'after' => 'created_at'
            ],
            'volume_input' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => true,
                'comment' => 'Volume sampah mentah (untuk organik)',
                'after' => 'berat_kg'
            ],
            'volume_output' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => true,
                'comment' => 'Volume hasil olahan (kompos/pupuk)',
                'after' => 'volume_input'
            ],
            'satuan_volume' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'default' => 'm3',
                'comment' => 'Satuan volume (m3, liter, dll)',
                'after' => 'volume_output'
            ],
            'kategori_spesifik' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Kategori detail sampah (organik basah, organik kering, dll)',
                'after' => 'jenis_sampah'
            ],
            'sumber_sampah' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Sumber spesifik sampah (gedung, unit, area)',
                'after' => 'kategori_spesifik'
            ],
            'metode_pengolahan' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Metode pengolahan (kompos, biogas, daur ulang, dll)',
                'after' => 'sumber_sampah'
            ],
            'keterangan_detail' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Keterangan detail proses dan hasil',
                'after' => 'metode_pengolahan'
            ],
            'koordinat_lokasi' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Koordinat GPS lokasi pengolahan',
                'after' => 'keterangan_detail'
            ],
            'petugas_input' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Nama petugas yang melakukan input',
                'after' => 'koordinat_lokasi'
            ]
        ];

        // Check if table exists and add fields
        if ($this->db->tableExists('waste_management')) {
            $this->forge->addColumn('waste_management', $wasteFields);
        }

        // Enhance limbah_b3 table with additional logging fields
        $limbahFields = [
            'volume_limbah' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => true,
                'comment' => 'Volume limbah dalam m3 atau liter',
                'after' => 'timbulan'
            ],
            'satuan_volume' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'default' => 'liter',
                'comment' => 'Satuan volume limbah',
                'after' => 'volume_limbah'
            ],
            'sumber_limbah' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Sumber spesifik limbah (lab, workshop, dll)',
                'after' => 'lokasi'
            ],
            'metode_penanganan' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Metode penanganan limbah B3',
                'after' => 'kemasan'
            ],
            'kondisi_penyimpanan' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Kondisi penyimpanan limbah',
                'after' => 'metode_penanganan'
            ],
            'tanggal_produksi' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'Tanggal produksi/timbulnya limbah',
                'after' => 'tanggal_input'
            ],
            'tanggal_kadaluarsa' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'Tanggal kadaluarsa atau batas aman',
                'after' => 'tanggal_produksi'
            ],
            'petugas_input' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Nama petugas yang melakukan input',
                'after' => 'keterangan'
            ],
            'nomor_manifest' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Nomor manifest limbah B3',
                'after' => 'petugas_input'
            ]
        ];

        if ($this->db->tableExists('limbah_b3')) {
            $this->forge->addColumn('limbah_b3', $limbahFields);
        }

        // Create waste_categories table for better categorization
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'kategori_utama' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
                'comment' => 'Kategori utama (organik, anorganik, B3)'
            ],
            'sub_kategori' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
                'comment' => 'Sub kategori detail'
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Deskripsi kategori'
            ],
            'metode_pengolahan_default' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Metode pengolahan default'
            ],
            'target_pengurangan' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'comment' => 'Target pengurangan dalam persen'
            ],
            'status_aktif' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'null' => false
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
        $this->forge->addKey('kategori_utama');
        $this->forge->createTable('waste_categories', true);

        // Create waste_processing_log table for detailed processing records
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'waste_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Reference to waste_management'
            ],
            'limbah_b3_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Reference to limbah_b3'
            ],
            'tanggal_proses' => [
                'type' => 'DATE',
                'null' => false,
                'comment' => 'Tanggal proses pengolahan'
            ],
            'jenis_proses' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
                'comment' => 'Jenis proses (kompos, biogas, daur ulang, dll)'
            ],
            'input_volume' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => false,
                'comment' => 'Volume input ke proses'
            ],
            'output_volume' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => true,
                'comment' => 'Volume output dari proses'
            ],
            'efisiensi_proses' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'comment' => 'Efisiensi proses dalam persen'
            ],
            'kualitas_output' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Kualitas hasil (baik, sedang, kurang)'
            ],
            'petugas_proses' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
                'comment' => 'Petugas yang melakukan proses'
            ],
            'catatan_proses' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Catatan detail proses'
            ],
            'foto_sebelum' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Path foto sebelum proses'
            ],
            'foto_sesudah' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Path foto sesudah proses'
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
        $this->forge->addKey('waste_id');
        $this->forge->addKey('limbah_b3_id');
        $this->forge->addKey('tanggal_proses');
        $this->forge->createTable('waste_processing_log', true);

        // Seed waste categories
        $this->db->table('waste_categories')->insertBatch([
            [
                'kategori_utama' => 'organik',
                'sub_kategori' => 'Organik Basah',
                'deskripsi' => 'Sampah organik dengan kadar air tinggi (sisa makanan, sayuran)',
                'metode_pengolahan_default' => 'kompos',
                'target_pengurangan' => 80.00,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'kategori_utama' => 'organik',
                'sub_kategori' => 'Organik Kering',
                'deskripsi' => 'Sampah organik dengan kadar air rendah (daun kering, ranting)',
                'metode_pengolahan_default' => 'kompos',
                'target_pengurangan' => 85.00,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'kategori_utama' => 'anorganik',
                'sub_kategori' => 'Plastik Daur Ulang',
                'deskripsi' => 'Plastik yang dapat didaur ulang (PET, HDPE, dll)',
                'metode_pengolahan_default' => 'daur ulang',
                'target_pengurangan' => 70.00,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'kategori_utama' => 'anorganik',
                'sub_kategori' => 'Kertas dan Karton',
                'deskripsi' => 'Kertas, karton, dan produk kertas lainnya',
                'metode_pengolahan_default' => 'daur ulang',
                'target_pengurangan' => 75.00,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'kategori_utama' => 'anorganik',
                'sub_kategori' => 'Logam',
                'deskripsi' => 'Kaleng, aluminium, dan logam lainnya',
                'metode_pengolahan_default' => 'daur ulang',
                'target_pengurangan' => 90.00,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'kategori_utama' => 'B3',
                'sub_kategori' => 'Limbah Laboratorium',
                'deskripsi' => 'Limbah dari kegiatan laboratorium',
                'metode_pengolahan_default' => 'pengolahan khusus',
                'target_pengurangan' => 95.00,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ]);

        // Add indexes for performance
        if ($this->db->tableExists('waste_management')) {
            $this->db->query('ALTER TABLE waste_management ADD INDEX idx_tanggal_input (tanggal_input)');
            $this->db->query('ALTER TABLE waste_management ADD INDEX idx_kategori_spesifik (kategori_spesifik)');
            $this->db->query('ALTER TABLE waste_management ADD INDEX idx_sumber_sampah (sumber_sampah)');
        }

        if ($this->db->tableExists('limbah_b3')) {
            $this->db->query('ALTER TABLE limbah_b3 ADD INDEX idx_tanggal_produksi (tanggal_produksi)');
            $this->db->query('ALTER TABLE limbah_b3 ADD INDEX idx_sumber_limbah (sumber_limbah)');
            $this->db->query('ALTER TABLE limbah_b3 ADD INDEX idx_nomor_manifest (nomor_manifest)');
        }
    }

    public function down()
    {
        // Drop new tables
        $this->forge->dropTable('waste_processing_log', true);
        $this->forge->dropTable('waste_categories', true);

        // Remove added columns from waste_management
        if ($this->db->tableExists('waste_management')) {
            $this->db->query('ALTER TABLE waste_management DROP INDEX IF EXISTS idx_tanggal_input');
            $this->db->query('ALTER TABLE waste_management DROP INDEX IF EXISTS idx_kategori_spesifik');
            $this->db->query('ALTER TABLE waste_management DROP INDEX IF EXISTS idx_sumber_sampah');
            
            $this->forge->dropColumn('waste_management', [
                'tanggal_input', 'volume_input', 'volume_output', 'satuan_volume',
                'kategori_spesifik', 'sumber_sampah', 'metode_pengolahan',
                'keterangan_detail', 'koordinat_lokasi', 'petugas_input'
            ]);
        }

        // Remove added columns from limbah_b3
        if ($this->db->tableExists('limbah_b3')) {
            $this->db->query('ALTER TABLE limbah_b3 DROP INDEX IF EXISTS idx_tanggal_produksi');
            $this->db->query('ALTER TABLE limbah_b3 DROP INDEX IF EXISTS idx_sumber_limbah');
            $this->db->query('ALTER TABLE limbah_b3 DROP INDEX IF EXISTS idx_nomor_manifest');
            
            $this->forge->dropColumn('limbah_b3', [
                'volume_limbah', 'satuan_volume', 'sumber_limbah', 'metode_penanganan',
                'kondisi_penyimpanan', 'tanggal_produksi', 'tanggal_kadaluarsa',
                'petugas_input', 'nomor_manifest'
            ]);
        }
    }
}