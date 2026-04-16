<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class StandardizeWasteCategories extends Migration
{
    public function up()
    {
        // 1. Update waste_management table untuk standardisasi
        $fields = [
            'waste_category_standard' => [
                'type' => 'ENUM',
                'constraint' => ['organik', 'anorganik', 'b3', 'cair', 'residu'],
                'null' => true,
                'comment' => 'Kategori limbah standar UI GreenMetric',
                'after' => 'kategori_spesifik'
            ],
            'waste_subcategory' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Sub-kategori detail (sisa makanan, plastik, dll)',
                'after' => 'waste_category_standard'
            ],
            'volume_unit' => [
                'type' => 'ENUM',
                'constraint' => ['kg', 'm3', 'liter', 'unit'],
                'null' => false,
                'default' => 'kg',
                'comment' => 'Satuan volume yang seragam',
                'after' => 'berat_kg'
            ],
            'volume_standardized' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => true,
                'comment' => 'Volume dalam satuan standar',
                'after' => 'volume_unit'
            ],
            'processing_method_standard' => [
                'type' => 'ENUM',
                'constraint' => ['daur_ulang', 'kompos', 'biogas', 'reuse', 'reduce', 'landfill', 'incineration', 'treatment'],
                'null' => true,
                'comment' => 'Metode pengolahan standar',
                'after' => 'metode_pengolahan'
            ],
            'source_type' => [
                'type' => 'ENUM',
                'constraint' => ['user_umum', 'tps_unit', 'laboratorium', 'kantin', 'asrama'],
                'null' => true,
                'comment' => 'Tipe sumber data',
                'after' => 'sumber_sampah'
            ]
        ];
        
        $this->forge->addColumn('waste_management', $fields);

        // 2. Create standardized waste categories lookup table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'category_code' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
                'comment' => 'Kode kategori (ORG, ANORG, B3, CAIR, RES)'
            ],
            'category_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'subcategory_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'uigm_mapping' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
                'comment' => 'Mapping ke kategori UIGM (WS.1, WS.2, dll)'
            ],
            'default_unit' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => false,
                'default' => 'kg'
            ],
            'is_recyclable' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '1 jika bisa didaur ulang'
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
        $this->forge->addKey('category_code');
        $this->forge->addKey('uigm_mapping');
        $this->forge->createTable('waste_categories_standard');

        // 3. Insert standardized categories
        $standardCategories = [
            // ORGANIK
            ['ORG', 'Limbah Organik', 'Sisa Makanan', 'WS.3', 'kg', 1, 1],
            ['ORG', 'Limbah Organik', 'Dedaunan', 'WS.3', 'kg', 1, 1],
            ['ORG', 'Limbah Organik', 'Limbah Taman', 'WS.3', 'kg', 1, 1],
            ['ORG', 'Limbah Organik', 'Sampah Dapur', 'WS.3', 'kg', 1, 1],
            
            // ANORGANIK
            ['ANORG', 'Limbah Anorganik', 'Plastik', 'WS.4', 'kg', 1, 1],
            ['ANORG', 'Limbah Anorganik', 'Kertas', 'WS.4', 'kg', 1, 1],
            ['ANORG', 'Limbah Anorganik', 'Logam', 'WS.4', 'kg', 1, 1],
            ['ANORG', 'Limbah Anorganik', 'Kaca', 'WS.4', 'kg', 1, 1],
            ['ANORG', 'Limbah Anorganik', 'Kardus', 'WS.4', 'kg', 1, 1],
            
            // B3 (Bahan Berbahaya & Beracun)
            ['B3', 'Limbah B3', 'Baterai', 'WS.5', 'unit', 0, 1],
            ['B3', 'Limbah B3', 'Lampu Neon', 'WS.5', 'unit', 0, 1],
            ['B3', 'Limbah B3', 'Limbah Medis', 'WS.5', 'kg', 0, 1],
            ['B3', 'Limbah B3', 'Elektronik', 'WS.5', 'unit', 0, 1],
            ['B3', 'Limbah B3', 'Oli Bekas', 'WS.5', 'liter', 0, 1],
            ['B3', 'Limbah B3', 'Limbah Laboratorium', 'WS.5', 'liter', 0, 1],
            
            // CAIR
            ['CAIR', 'Limbah Cair', 'Air Limbah Domestik', 'WS.6', 'm3', 0, 1],
            ['CAIR', 'Limbah Cair', 'Air Limbah Laboratorium', 'WS.6', 'm3', 0, 1],
            ['CAIR', 'Limbah Cair', 'Air Limbah Kantin', 'WS.6', 'm3', 0, 1],
            
            // RESIDU
            ['RES', 'Limbah Residu', 'Sampah Campur', 'WS.2', 'kg', 0, 1],
            ['RES', 'Limbah Residu', 'Sampah Non-Recyclable', 'WS.2', 'kg', 0, 1],
        ];

        foreach ($standardCategories as $category) {
            $this->db->table('waste_categories_standard')->insert([
                'category_code' => $category[0],
                'category_name' => $category[1],
                'subcategory_name' => $category[2],
                'uigm_mapping' => $category[3],
                'default_unit' => $category[4],
                'is_recyclable' => $category[5],
                'status_aktif' => $category[6],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // 4. Update existing data mapping (sample)
        $this->db->query("
            UPDATE waste_management 
            SET waste_category_standard = 'organik',
                waste_subcategory = 'Sisa Makanan',
                volume_unit = 'kg',
                volume_standardized = berat_kg,
                source_type = 'user_umum'
            WHERE kategori_spesifik LIKE '%organik%' OR jenis_sampah LIKE '%makanan%'
        ");

        $this->db->query("
            UPDATE waste_management 
            SET waste_category_standard = 'anorganik',
                waste_subcategory = 'Plastik',
                volume_unit = 'kg',
                volume_standardized = berat_kg,
                source_type = 'user_umum'
            WHERE kategori_spesifik LIKE '%plastik%' OR jenis_sampah LIKE '%plastik%'
        ");
    }

    public function down()
    {
        $this->forge->dropTable('waste_categories_standard', true);
        $this->forge->dropColumn('waste_management', [
            'waste_category_standard',
            'waste_subcategory', 
            'volume_unit',
            'volume_standardized',
            'processing_method_standard',
            'source_type'
        ]);
    }
}