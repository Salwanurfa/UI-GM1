<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminPusatSeeder extends Seeder
{
    public function run()
    {
        // Initialize UIGM Categories first
        $this->initializeUIGMCategories();

        // Create assessment year
        $this->createAssessmentYear();

        // Create sample units
        $this->createSampleUnits();

        // Create admin users
        $this->createAdminUsers();

        // Create sample submissions
        $this->createSampleSubmissions();

        // Create sample reviews
        $this->createSampleReviews();

        // Create sample notifications
        $this->createSampleNotifications();
    }

    private function initializeUIGMCategories()
    {
        $indikatorModel = new \App\Models\IndikatorModel();

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
            $existing = $indikatorModel->where('kode_kategori', $category['kode_kategori'])->first();
            if (!$existing) {
                $indikatorModel->insert($category);
            }
        }
    }

    private function createAssessmentYear()
    {
        $tahunModel = new \App\Models\TahunPenilaianModel();

        $currentYear = date('Y');
        $existing = $tahunModel->where('tahun', $currentYear)->first();

        if (!$existing) {
            $tahunModel->insert([
                'tahun' => $currentYear,
                'nama_periode' => "Penilaian UIGM {$currentYear}",
                'tanggal_mulai' => date('Y-m-d H:i:s', strtotime("{$currentYear}-01-01")),
                'tanggal_selesai' => date('Y-m-d H:i:s', strtotime("{$currentYear}-12-31")),
                'status_aktif' => true
            ]);
        }
    }

    private function createSampleUnits()
    {
        $unitModel = new \App\Models\UnitModel();

        $units = [
            [
                'nama_unit' => 'Fakultas Teknik Elektro',
                'kode_unit' => 'FTE',
                'tipe_unit' => 'fakultas',
                'parent_id' => null,
                'admin_unit_id' => null,
                'status_aktif' => true
            ],
            [
                'nama_unit' => 'Fakultas Teknik Mesin dan Dirgantara',
                'kode_unit' => 'FTMD',
                'tipe_unit' => 'fakultas',
                'parent_id' => null,
                'admin_unit_id' => null,
                'status_aktif' => true
            ],
            [
                'nama_unit' => 'Fakultas Teknik Sipil dan Lingkungan',
                'kode_unit' => 'FTSL',
                'tipe_unit' => 'fakultas',
                'parent_id' => null,
                'admin_unit_id' => null,
                'status_aktif' => true
            ],
            [
                'nama_unit' => 'Fakultas Rekayasa Industri',
                'kode_unit' => 'FRI',
                'tipe_unit' => 'fakultas',
                'parent_id' => null,
                'admin_unit_id' => null,
                'status_aktif' => true
            ],
            [
                'nama_unit' => 'Fakultas Informatika',
                'kode_unit' => 'FIF',
                'tipe_unit' => 'fakultas',
                'parent_id' => null,
                'admin_unit_id' => null,
                'status_aktif' => true
            ],
            [
                'nama_unit' => 'Fakultas Ekonomi dan Bisnis',
                'kode_unit' => 'FEB',
                'tipe_unit' => 'fakultas',
                'parent_id' => null,
                'admin_unit_id' => null,
                'status_aktif' => true
            ],
            [
                'nama_unit' => 'Fakultas Seni Rupa dan Desain',
                'kode_unit' => 'FSRD',
                'tipe_unit' => 'fakultas',
                'parent_id' => null,
                'admin_unit_id' => null,
                'status_aktif' => true
            ],
            [
                'nama_unit' => 'Direktorat Sumber Daya',
                'kode_unit' => 'DSD',
                'tipe_unit' => 'unit_kerja',
                'parent_id' => null,
                'admin_unit_id' => null,
                'status_aktif' => true
            ]
        ];

        foreach ($units as $unit) {
            $existing = $unitModel->where('kode_unit', $unit['kode_unit'])->first();
            if (!$existing) {
                $unitModel->insert($unit);
            }
        }
    }

    private function createAdminUsers()
    {
        $userModel = new \App\Models\UserModel();
        $unitModel = new \App\Models\UnitModel();

        // Create Admin Pusat
        $adminPusat = $userModel->where('username', 'admin_pusat')->first();
        if (!$adminPusat) {
            $userModel->insert([
                'username' => 'admin_pusat',
                'email' => 'admin.pusat@polban.ac.id',
                'password' => 'adminpusat123',
                'nama_lengkap' => 'Administrator Pusat UIGM',
                'role' => 'admin_pusat',
                'unit_id' => null,
                'status_aktif' => true
            ]);
        }

        // Create Admin Unit for each faculty
        $units = $unitModel->where('tipe_unit', 'fakultas')->findAll();

        foreach ($units as $unit) {
            $username = 'admin_' . strtolower($unit['kode_unit']);
            $existing = $userModel->where('username', $username)->first();

            if (!$existing) {
                $adminUnitId = $userModel->insert([
                    'username' => $username,
                    'email' => $username . '@polban.ac.id',
                    'password' => 'adminunit123',
                    'nama_lengkap' => 'Admin ' . $unit['nama_unit'],
                    'role' => 'admin_unit',
                    'unit_id' => $unit['id'],
                    'status_aktif' => true
                ]);

                // Update unit with admin_unit_id
                $unitModel->update($unit['id'], ['admin_unit_id' => $adminUnitId]);
            }
        }
    }

    private function createSampleSubmissions()
    {
        $pengirimanModel = new \App\Models\PengirimanUnitModel();
        $unitModel = new \App\Models\UnitModel();
        $tahunModel = new \App\Models\TahunPenilaianModel();

        $activeYear = $tahunModel->getActiveYear();
        if (!$activeYear) return;

        $units = $unitModel->where('status_aktif', true)->findAll();
        $statuses = ['dikirim', 'review', 'perlu_revisi', 'disetujui'];

        foreach ($units as $index => $unit) {
            $existing = $pengirimanModel->where('unit_id', $unit['id'])
                ->where('tahun_penilaian_id', $activeYear['id'])
                ->first();

            if (!$existing) {
                $status = $statuses[$index % count($statuses)];
                $progress = match ($status) {
                    'dikirim' => rand(80, 95),
                    'review' => rand(85, 100),
                    'perlu_revisi' => rand(60, 80),
                    'disetujui' => 100,
                    default => rand(50, 90)
                };

                $tanggalKirim = date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days'));
                $tanggalReview = in_array($status, ['review', 'perlu_revisi', 'disetujui'])
                    ? date('Y-m-d H:i:s', strtotime($tanggalKirim . ' +' . rand(1, 7) . ' days'))
                    : null;
                $tanggalDisetujui = $status === 'disetujui'
                    ? date('Y-m-d H:i:s', strtotime($tanggalReview . ' +' . rand(1, 3) . ' days'))
                    : null;

                $pengirimanModel->insert([
                    'unit_id' => $unit['id'],
                    'tahun_penilaian_id' => $activeYear['id'],
                    'status_pengiriman' => $status,
                    'progress_persen' => $progress,
                    'tanggal_kirim' => $tanggalKirim,
                    'tanggal_review' => $tanggalReview,
                    'tanggal_disetujui' => $tanggalDisetujui,
                    'reviewer_id' => null,
                    'catatan_admin_pusat' => $status === 'perlu_revisi' ? 'Beberapa data perlu dilengkapi dan diperbaiki.' : null,
                    'catatan_admin_unit' => 'Data telah diisi sesuai kondisi terkini unit.',
                    'versi' => 1
                ]);
            }
        }
    }

    private function createSampleReviews()
    {
        $reviewModel = new \App\Models\ReviewKategoriModel();
        $pengirimanModel = new \App\Models\PengirimanUnitModel();
        $indikatorModel = new \App\Models\IndikatorModel();
        $userModel = new \App\Models\UserModel();

        $submissions = $pengirimanModel->whereIn('status_pengiriman', ['review', 'perlu_revisi', 'disetujui'])->findAll();
        $categories = $indikatorModel->getUIGMCategories();
        $adminPusat = $userModel->where('role', 'admin_pusat')->first();

        if (!$adminPusat) return;

        foreach ($submissions as $submission) {
            foreach ($categories as $category) {
                $existing = $reviewModel->where('pengiriman_id', $submission['id'])
                    ->where('indikator_id', $category['id'])
                    ->first();

                if (!$existing) {
                    $statusReview = match ($submission['status_pengiriman']) {
                        'review' => 'pending',
                        'perlu_revisi' => rand(0, 1) ? 'perlu_revisi' : 'disetujui',
                        'disetujui' => 'disetujui',
                        default => 'pending'
                    };

                    $catatan = $statusReview === 'perlu_revisi'
                        ? 'Data pada kategori ini perlu dilengkapi dengan dokumentasi yang lebih detail.'
                        : ($statusReview === 'disetujui' ? 'Data sudah sesuai dan lengkap.' : null);

                    $reviewModel->insert([
                        'pengiriman_id' => $submission['id'],
                        'indikator_id' => $category['id'],
                        'status_review' => $statusReview,
                        'catatan_review' => $catatan,
                        'reviewer_id' => $statusReview !== 'pending' ? $adminPusat['id'] : null,
                        'tanggal_review' => $statusReview !== 'pending' ? $submission['tanggal_review'] : null,
                        'skor_review' => $statusReview === 'disetujui' ? rand(70, 100) : null
                    ]);
                }
            }
        }
    }

    private function createSampleNotifications()
    {
        $notifikasiModel = new \App\Models\NotifikasiModel();
        $userModel = new \App\Models\UserModel();
        $unitModel = new \App\Models\UnitModel();

        $adminPusat = $userModel->where('role', 'admin_pusat')->first();
        $adminUnits = $userModel->where('role', 'admin_unit')->findAll();

        if (!$adminPusat) return;

        // Notifications for Admin Pusat
        $units = $unitModel->where('status_aktif', true)->limit(3)->findAll();
        foreach ($units as $unit) {
            $notifikasiModel->createNotification(
                $adminPusat['id'],
                'data_masuk',
                'Data Baru Masuk',
                "Data UIGM dari {$unit['nama_unit']} telah dikirim dan menunggu review.",
                ['unit_id' => $unit['id'], 'action' => 'review_data']
            );
        }

        // Notifications for Admin Units
        foreach ($adminUnits as $index => $adminUnit) {
            if ($index % 2 === 0) {
                $notifikasiModel->createNotification(
                    $adminUnit['id'],
                    'approval',
                    'Data Disetujui',
                    "Data UIGM unit Anda telah disetujui oleh Admin Pusat.",
                    ['action' => 'view_result']
                );
            } else {
                $notifikasiModel->createNotification(
                    $adminUnit['id'],
                    'rejection',
                    'Perlu Revisi',
                    "Data UIGM unit Anda perlu direvisi pada beberapa kategori.",
                    ['action' => 'revise_data']
                );
            }
        }

        // Deadline reminders
        foreach ($adminUnits as $adminUnit) {
            $notifikasiModel->createNotification(
                $adminUnit['id'],
                'deadline',
                'Pengingat Deadline',
                "Deadline pengisian data UIGM: 31 Desember " . date('Y') . ". Pastikan data Anda sudah lengkap.",
                ['deadline' => date('Y') . '-12-31', 'action' => 'complete_data']
            );
        }
    }
}
