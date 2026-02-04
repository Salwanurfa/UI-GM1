<?php

// File: app/Database/Seeds/KriteriaSeeder.php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KriteriaSeeder extends Seeder
{
    public function run()
    {
        $kriteriaModel = new \App\Models\KriteriaModel();
        
        // Sample data kriteria untuk testing
        $sampleData = [
            // User ID 3 (user1)
            [
                'user_id' => 3,
                'tanggal' => '2026-01-01',
                'unit_prodi' => 'Teknik Informatika',
                'jenis_sampah' => 'Kertas',
                'satuan' => 'kg',
                'jumlah' => 5.5,
                'gedung' => 'Gedung A',
                'created_at' => '2026-01-01 08:00:00'
            ],
            [
                'user_id' => 3,
                'tanggal' => '2026-01-01',
                'unit_prodi' => 'Teknik Informatika',
                'jenis_sampah' => 'Plastik',
                'satuan' => 'kg',
                'jumlah' => 2.3,
                'gedung' => 'Gedung A',
                'created_at' => '2026-01-01 09:00:00'
            ],
            [
                'user_id' => 3,
                'tanggal' => '2026-01-02',
                'unit_prodi' => 'Teknik Informatika',
                'jenis_sampah' => 'Organik',
                'satuan' => 'kg',
                'jumlah' => 8.7,
                'gedung' => 'Gedung B',
                'created_at' => '2026-01-02 10:00:00'
            ],
            
            // User ID 4 (user2)
            [
                'user_id' => 4,
                'tanggal' => '2026-01-01',
                'unit_prodi' => 'Teknik Elektro',
                'jenis_sampah' => 'Anorganik',
                'satuan' => 'kg',
                'jumlah' => 3.2,
                'gedung' => 'Gedung C',
                'created_at' => '2026-01-01 11:00:00'
            ],
            [
                'user_id' => 4,
                'tanggal' => '2026-01-02',
                'unit_prodi' => 'Teknik Elektro',
                'jenis_sampah' => 'Limbah Cair',
                'satuan' => 'L',
                'jumlah' => 15.0,
                'gedung' => 'Gedung D',
                'created_at' => '2026-01-02 12:00:00'
            ],
            
            // User ID 5 (mahasiswa1)
            [
                'user_id' => 5,
                'tanggal' => '2026-01-01',
                'unit_prodi' => 'Teknik Mesin',
                'jenis_sampah' => 'B3',
                'satuan' => 'kg',
                'jumlah' => 1.5,
                'gedung' => 'Gedung Workshop',
                'created_at' => '2026-01-01 13:00:00'
            ],
            [
                'user_id' => 5,
                'tanggal' => '2026-01-02',
                'unit_prodi' => 'Teknik Mesin',
                'jenis_sampah' => 'Kertas',
                'satuan' => 'kg',
                'jumlah' => 4.2,
                'gedung' => 'Gedung E',
                'created_at' => '2026-01-02 14:00:00'
            ],
            
            // User ID 6 (dosen1)
            [
                'user_id' => 6,
                'tanggal' => '2026-01-01',
                'unit_prodi' => 'Teknik Kimia',
                'jenis_sampah' => 'Limbah Cair',
                'satuan' => 'L',
                'jumlah' => 25.5,
                'gedung' => 'Gedung Laboratorium',
                'created_at' => '2026-01-01 15:00:00'
            ],
            [
                'user_id' => 6,
                'tanggal' => '2026-01-02',
                'unit_prodi' => 'Teknik Kimia',
                'jenis_sampah' => 'B3',
                'satuan' => 'kg',
                'jumlah' => 0.8,
                'gedung' => 'Gedung Laboratorium',
                'created_at' => '2026-01-02 16:00:00'
            ]
        ];

        foreach ($sampleData as $data) {
            // Cek apakah data sudah ada (berdasarkan user_id, tanggal, dan jenis_sampah)
            $existing = $kriteriaModel->where([
                'user_id' => $data['user_id'],
                'tanggal' => $data['tanggal'],
                'jenis_sampah' => $data['jenis_sampah'],
                'gedung' => $data['gedung']
            ])->first();
            
            if (!$existing) {
                $kriteriaModel->insert($data);
                echo "Data kriteria untuk user_id {$data['user_id']} - {$data['jenis_sampah']} berhasil ditambahkan.\n";
            } else {
                echo "Data kriteria untuk user_id {$data['user_id']} - {$data['jenis_sampah']} sudah ada.\n";
            }
        }
    }
}