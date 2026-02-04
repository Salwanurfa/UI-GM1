<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DataSeeder extends Seeder
{
    public function run()
    {
        // Create sample submissions
        $this->createSampleSubmissions();

        // Create sample reviews
        $this->createSampleReviews();

        // Create sample notifications
        $this->createSampleNotifications();
    }

    private function createSampleSubmissions()
    {
        // Get active year
        $tahun = $this->db->table('tahun_penilaian')
            ->where('status_aktif', true)
            ->get()
            ->getRow();

        if (!$tahun) return;

        // Get units
        $units = $this->db->table('unit')
            ->where('status_aktif', true)
            ->get()
            ->getResult();

        $statuses = ['draft', 'dikirim', 'review', 'perlu_revisi', 'disetujui'];

        foreach ($units as $index => $unit) {
            $existing = $this->db->table('pengiriman_unit')
                ->where('unit_id', $unit->id)
                ->where('tahun_penilaian_id', $tahun->id)
                ->get()
                ->getRow();

            if (!$existing) {
                $status = $statuses[$index % count($statuses)];
                $progress = match ($status) {
                    'draft' => rand(20, 50),
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

                $this->db->table('pengiriman_unit')->insert([
                    'unit_id' => $unit->id,
                    'tahun_penilaian_id' => $tahun->id,
                    'status_pengiriman' => $status,
                    'progress_persen' => $progress,
                    'tanggal_kirim' => $status !== 'draft' ? $tanggalKirim : null,
                    'tanggal_review' => $tanggalReview,
                    'tanggal_disetujui' => $tanggalDisetujui,
                    'reviewer_id' => null,
                    'catatan_admin_pusat' => $status === 'perlu_revisi' ? 'Beberapa data perlu dilengkapi dan diperbaiki.' : null,
                    'catatan_admin_unit' => 'Data telah diisi sesuai kondisi terkini unit.',
                    'versi' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
    }

    private function createSampleReviews()
    {
        // Get submissions that need reviews
        $submissions = $this->db->table('pengiriman_unit')
            ->whereIn('status_pengiriman', ['dikirim', 'review', 'perlu_revisi', 'disetujui'])
            ->get()
            ->getResult();

        // Get categories
        $categories = $this->db->table('indikator')
            ->where('status_aktif', true)
            ->orderBy('urutan')
            ->get()
            ->getResult();

        // Get admin pusat
        $adminPusat = $this->db->table('users')
            ->where('role', 'admin_pusat')
            ->get()
            ->getRow();

        if (!$adminPusat) return;

        foreach ($submissions as $submission) {
            foreach ($categories as $category) {
                $existing = $this->db->table('review_kategori')
                    ->where('pengiriman_id', $submission->id)
                    ->where('indikator_id', $category->id)
                    ->get()
                    ->getRow();

                if (!$existing) {
                    $statusReview = match ($submission->status_pengiriman) {
                        'dikirim' => 'pending',
                        'review' => 'pending',
                        'perlu_revisi' => rand(0, 1) ? 'perlu_revisi' : 'disetujui',
                        'disetujui' => 'disetujui',
                        default => 'pending'
                    };

                    $catatan = $statusReview === 'perlu_revisi'
                        ? 'Data pada kategori ini perlu dilengkapi dengan dokumentasi yang lebih detail.'
                        : ($statusReview === 'disetujui' ? 'Data sudah sesuai dan lengkap.' : null);

                    $this->db->table('review_kategori')->insert([
                        'pengiriman_id' => $submission->id,
                        'indikator_id' => $category->id,
                        'status_review' => $statusReview,
                        'catatan_review' => $catatan,
                        'reviewer_id' => $statusReview !== 'pending' ? $adminPusat->id : null,
                        'tanggal_review' => $statusReview !== 'pending' ? $submission->tanggal_review : null,
                        'skor_review' => $statusReview === 'disetujui' ? rand(70, 100) : null,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }
    }

    private function createSampleNotifications()
    {
        // Get admin pusat
        $adminPusat = $this->db->table('users')
            ->where('role', 'admin_pusat')
            ->get()
            ->getRow();

        // Get admin units
        $adminUnits = $this->db->table('users')
            ->where('role', 'admin_unit')
            ->get()
            ->getResult();

        if (!$adminPusat) return;

        // Notifications for Admin Pusat
        $units = $this->db->table('unit')
            ->where('status_aktif', true)
            ->limit(3)
            ->get()
            ->getResult();

        foreach ($units as $unit) {
            $this->db->table('notifikasi')->insert([
                'user_id' => $adminPusat->id,
                'tipe_notifikasi' => 'data_masuk',
                'judul' => 'Data Baru Masuk',
                'pesan' => "Data UIGM dari {$unit->nama_unit} telah dikirim dan menunggu review.",
                'data_terkait' => json_encode(['unit_id' => $unit->id, 'action' => 'review_data']),
                'is_read' => false,
                'tanggal_dibaca' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        // Notifications for Admin Units
        foreach ($adminUnits as $index => $adminUnit) {
            if ($index % 2 === 0) {
                $this->db->table('notifikasi')->insert([
                    'user_id' => $adminUnit->id,
                    'tipe_notifikasi' => 'approval',
                    'judul' => 'Data Disetujui',
                    'pesan' => "Data UIGM unit Anda telah disetujui oleh Admin Pusat.",
                    'data_terkait' => json_encode(['action' => 'view_result']),
                    'is_read' => false,
                    'tanggal_dibaca' => null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                $this->db->table('notifikasi')->insert([
                    'user_id' => $adminUnit->id,
                    'tipe_notifikasi' => 'rejection',
                    'judul' => 'Perlu Revisi',
                    'pesan' => "Data UIGM unit Anda perlu direvisi pada beberapa kategori.",
                    'data_terkait' => json_encode(['action' => 'revise_data']),
                    'is_read' => false,
                    'tanggal_dibaca' => null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
    }
}
