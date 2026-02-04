<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MasterHargaSampahSeeder extends Seeder
{
    public function run()
    {
        // Check if data already exists
        $existing = $this->db->table('master_harga_sampah')->countAllResults();
        
        if ($existing > 0) {
            echo "Data master_harga_sampah sudah ada ({$existing} records). Skip seeding.\n";
            return;
        }

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
        
        echo "Berhasil menambahkan " . count($data) . " data master harga sampah.\n";
    }
}
