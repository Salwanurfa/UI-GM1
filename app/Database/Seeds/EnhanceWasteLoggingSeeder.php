<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EnhanceWasteLoggingSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $forge = \Config\Database::forge();

        echo "Starting waste logging system enhancement...\n";

        // Enhance waste_management table with detailed logging fields
        $wasteFields = [
            'tanggal_input' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'Tanggal input data sampah'
            ],
            'volume_input' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => true,
                'comment' => 'Volume sampah mentah (untuk organik)'
            ],
            'volume_output' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => true,
                'comment' => 'Volume hasil olahan (kompos/pupuk)'
            ],
            'satuan_volume' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'default' => 'm3',
                'comment' => 'Satuan volume (m3, liter, dll)'
            ],
            'kategori_spesifik' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Kategori detail sampah (organik basah, organik kering, dll)'
            ],
            'sumber_sampah' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Sumber spesifik sampah (gedung, unit, area)'
            ],
            'metode_pengolahan' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Metode pengolahan (kompos, biogas, daur ulang, dll)'
            ],
            'keterangan_detail' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Keterangan detail proses dan hasil'
            ],
            'koordinat_lokasi' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Koordinat GPS lokasi pengolahan'
            ],
            'petugas_input' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Nama petugas yang melakukan input'
            ]
        ];

        // Check if waste_management table exists and add fields
        if ($db->tableExists('waste_management')) {
            echo "Enhancing waste_management table...\n";
            
            // Check which columns already exist
            $existingColumns = $db->getFieldNames('waste_management');
            
            foreach ($wasteFields as $fieldName => $fieldConfig) {
                if (!in_array($fieldName, $existingColumns)) {
                    try {
                        $forge->addColumn('waste_management', [$fieldName => $fieldConfig]);
                        echo "Added column: {$fieldName}\n";
                    } catch (\Exception $e) {
                        echo "Error adding column {$fieldName}: " . $e->getMessage() . "\n";
                    }
                }
            }
        }

        // Enhance limbah_b3 table with additional logging fields
        $limbahFields = [
            'volume_limbah' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => true,
                'comment' => 'Volume limbah dalam m3 atau liter'
            ],
            'satuan_volume' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'default' => 'liter',
                'comment' => 'Satuan volume limbah'
            ],
            'sumber_limbah' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Sumber spesifik limbah (lab, workshop, dll)'
            ],
            'metode_penanganan' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Metode penanganan limbah B3'
            ],
            'kondisi_penyimpanan' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Kondisi penyimpanan limbah'
            ],
            'tanggal_produksi' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'Tanggal produksi/timbulnya limbah'
            ],
            'tanggal_kadaluarsa' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'Tanggal kadaluarsa atau batas aman'
            ],
            'petugas_input' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Nama petugas yang melakukan input'
            ],
            'nomor_manifest' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Nomor manifest limbah B3'
            ]
        ];

        if ($db->tableExists('limbah_b3')) {
            echo "Enhancing limbah_b3 table...\n";
            
            $existingColumns = $db->getFieldNames('limbah_b3');
            
            foreach ($limbahFields as $fieldName => $fieldConfig) {
                if (!in_array($fieldName, $existingColumns)) {
                    try {
                        $forge->addColumn('limbah_b3', [$fieldName => $fieldConfig]);
                        echo "Added column: {$fieldName}\n";
                    } catch (\Exception $e) {
                        echo "Error adding column {$fieldName}: " . $e->getMessage() . "\n";
                    }
                }
            }
        }

        // Create waste_categories table for better categorization
        if (!$db->tableExists('waste_categories')) {
            echo "Creating waste_categories table...\n";
            
            $forge->addField([
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

            $forge->addKey('id', true);
            $forge->addKey('kategori_utama');
            $forge->createTable('waste_categories', true);
            
            // Seed waste categories
            $db->table('waste_categories')->insertBatch([
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
            
            echo "Waste categories seeded successfully\n";
        }

        // Create waste_processing_log table for detailed processing records
        if (!$db->tableExists('waste_processing_log')) {
            echo "Creating waste_processing_log table...\n";
            
            $forge->addField([
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

            $forge->addKey('id', true);
            $forge->addKey('waste_id');
            $forge->addKey('limbah_b3_id');
            $forge->addKey('tanggal_proses');
            $forge->createTable('waste_processing_log', true);
            
            echo "Waste processing log table created successfully\n";
        }

        // Add indexes for performance
        try {
            if ($db->tableExists('waste_management')) {
                $db->query('ALTER TABLE waste_management ADD INDEX IF NOT EXISTS idx_tanggal_input (tanggal_input)');
                $db->query('ALTER TABLE waste_management ADD INDEX IF NOT EXISTS idx_kategori_spesifik (kategori_spesifik)');
                $db->query('ALTER TABLE waste_management ADD INDEX IF NOT EXISTS idx_sumber_sampah (sumber_sampah)');
                echo "Added indexes to waste_management table\n";
            }

            if ($db->tableExists('limbah_b3')) {
                $db->query('ALTER TABLE limbah_b3 ADD INDEX IF NOT EXISTS idx_tanggal_produksi (tanggal_produksi)');
                $db->query('ALTER TABLE limbah_b3 ADD INDEX IF NOT EXISTS idx_sumber_limbah (sumber_limbah)');
                $db->query('ALTER TABLE limbah_b3 ADD INDEX IF NOT EXISTS idx_nomor_manifest (nomor_manifest)');
                echo "Added indexes to limbah_b3 table\n";
            }
        } catch (\Exception $e) {
            echo "Note: Some indexes may already exist - " . $e->getMessage() . "\n";
        }

        echo "Waste logging system enhancement completed successfully!\n";
    }
}