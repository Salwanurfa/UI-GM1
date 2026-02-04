<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SimpleSeeder extends Seeder
{
    public function run()
    {
        // Initialize UIGM Categories
        $this->initializeUIGMCategories();

        // Create assessment year
        $this->createAssessmentYear();

        // Create sample units
        $this->createSampleUnits();

        // Create admin users
        $this->createAdminUsers();
    }

    private function initializeUIGMCategories()
    {
        $categories = [
            [
                'kode_kategori' => 'SI',
                'nama_kategori' => 'Setting & Infrastructure',
                'deskripsi' => 'Pengaturan dan Infrastruktur kampus yang mendukung keberlanjutan',
                'bobot' => 15.00,
                'warna' => '#2E8B57',
                'urutan' => 1,
                'status_aktif' => true
            ],
            [
                'kode_kategori' => 'EC',
                'nama_kategori' => 'Energy & Climate Change',
                'deskripsi' => 'Penggunaan energi dan upaya mitigasi perubahan iklim',
                'bobot' => 21.00,
                'warna' => '#FFD700',
                'urutan' => 2,
                'status_aktif' => true
            ],
            [
                'kode_kategori' => 'WS',
                'nama_kategori' => 'Waste',
                'deskripsi' => 'Pengelolaan limbah dan daur ulang',
                'bobot' => 18.00,
                'warna' => '#8B4513',
                'urutan' => 3,
                'status_aktif' => true
            ],
            [
                'kode_kategori' => 'WR',
                'nama_kategori' => 'Water',
                'deskripsi' => 'Konservasi dan penggunaan air',
                'bobot' => 10.00,
                'warna' => '#1E90FF',
                'urutan' => 4,
                'status_aktif' => true
            ],
            [
                'kode_kategori' => 'TR',
                'nama_kategori' => 'Transportation',
                'deskripsi' => 'Kebijakan transportasi berkelanjutan',
                'bobot' => 18.00,
                'warna' => '#DC143C',
                'urutan' => 5,
                'status_aktif' => true
            ],
            [
                'kode_kategori' => 'ED',
                'nama_kategori' => 'Education & Research',
                'deskripsi' => 'Pendidikan dan penelitian keberlanjutan',
                'bobot' => 18.00,
                'warna' => '#5c8cbf',
                'urutan' => 6,
                'status_aktif' => true
            ]
        ];

        foreach ($categories as $category) {
            $existing = $this->db->table('indikator')
                ->where('kode_kategori', $category['kode_kategori'])
                ->get()
                ->getRow();

            if (!$existing) {
                $this->db->table('indikator')->insert($category);
            }
        }
    }

    private function createAssessmentYear()
    {
        $currentYear = date('Y');

        $existing = $this->db->table('tahun_penilaian')
            ->where('tahun', $currentYear)
            ->get()
            ->getRow();

        if (!$existing) {
            $this->db->table('tahun_penilaian')->insert([
                'tahun' => $currentYear,
                'nama_periode' => "Penilaian UIGM {$currentYear}",
                'tanggal_mulai' => "{$currentYear}-01-01 00:00:00",
                'tanggal_selesai' => "{$currentYear}-12-31 23:59:59",
                'status_aktif' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    private function createSampleUnits()
    {
        $units = [
            [
                'nama_unit' => 'Fakultas Teknik Elektro',
                'kode_unit' => 'FTE',
                'tipe_unit' => 'fakultas',
                'parent_id' => null,
                'admin_unit_id' => null,
                'status_aktif' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nama_unit' => 'Fakultas Teknik Mesin dan Dirgantara',
                'kode_unit' => 'FTMD',
                'tipe_unit' => 'fakultas',
                'parent_id' => null,
                'admin_unit_id' => null,
                'status_aktif' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nama_unit' => 'Fakultas Teknik Sipil dan Lingkungan',
                'kode_unit' => 'FTSL',
                'tipe_unit' => 'fakultas',
                'parent_id' => null,
                'admin_unit_id' => null,
                'status_aktif' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nama_unit' => 'Fakultas Rekayasa Industri',
                'kode_unit' => 'FRI',
                'tipe_unit' => 'fakultas',
                'parent_id' => null,
                'admin_unit_id' => null,
                'status_aktif' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nama_unit' => 'Fakultas Informatika',
                'kode_unit' => 'FIF',
                'tipe_unit' => 'fakultas',
                'parent_id' => null,
                'admin_unit_id' => null,
                'status_aktif' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        foreach ($units as $unit) {
            $existing = $this->db->table('unit')
                ->where('kode_unit', $unit['kode_unit'])
                ->get()
                ->getRow();

            if (!$existing) {
                $this->db->table('unit')->insert($unit);
            }
        }
    }

    private function createAdminUsers()
    {
        // Create Admin Pusat
        $existing = $this->db->table('users')
            ->where('username', 'admin_pusat')
            ->get()
            ->getRow();

        if (!$existing) {
            $this->db->table('users')->insert([
                'username' => 'admin_pusat',
                'email' => 'admin.pusat@polban.ac.id',
                'password' => 'adminpusat123',
                'nama_lengkap' => 'Administrator Pusat UIGM',
                'role' => 'admin_pusat',
                'unit_id' => null,
                'status_aktif' => true,
                'last_login' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        // Create Super Admin
        $existing = $this->db->table('users')
            ->where('username', 'super_admin')
            ->get()
            ->getRow();

        if (!$existing) {
            $this->db->table('users')->insert([
                'username' => 'super_admin',
                'email' => 'super.admin@polban.ac.id',
                'password' => 'superadmin123',
                'nama_lengkap' => 'Super Administrator',
                'role' => 'super_admin',
                'unit_id' => null,
                'status_aktif' => true,
                'last_login' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        // Create Admin Unit for each faculty
        $units = $this->db->table('unit')
            ->where('tipe_unit', 'fakultas')
            ->get()
            ->getResult();

        foreach ($units as $unit) {
            $username = 'admin_' . strtolower($unit->kode_unit);

            $existing = $this->db->table('users')
                ->where('username', $username)
                ->get()
                ->getRow();

            if (!$existing) {
                $adminUnitId = $this->db->table('users')->insert([
                    'username' => $username,
                    'email' => $username . '@polban.ac.id',
                    'password' => 'adminunit123',
                    'nama_lengkap' => 'Admin ' . $unit->nama_unit,
                    'role' => 'admin_unit',
                    'unit_id' => $unit->id,
                    'status_aktif' => true,
                    'last_login' => null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                // Update unit with admin_unit_id
                $this->db->table('unit')
                    ->where('id', $unit->id)
                    ->update(['admin_unit_id' => $this->db->insertID()]);
            }
        }
    }
}
