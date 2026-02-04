<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class JenisSampahSeeder extends Seeder
{
    public function run()
    {
        // Check if table exists and has data
        if ($this->db->tableExists('jenis_sampah')) {
            $count = $this->db->table('jenis_sampah')->countAll();
            if ($count > 0) {
                echo "Tabel jenis_sampah sudah memiliki data ({$count} records)\n";
                return;
            }
        }

        echo "Seeding jenis_sampah table...\n";

        // Data struktur sampah organik
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

        try {
            $this->db->table('jenis_sampah')->insertBatch($data);
            echo "✅ Berhasil insert " . count($data) . " records ke tabel jenis_sampah\n";
        } catch (\Exception $e) {
            echo "❌ Error saat insert data: " . $e->getMessage() . "\n";
        }
    }
}
