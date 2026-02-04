<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        // Get admin_pusat user
        $adminPusat = $this->db->table('users')->where('role', 'admin_pusat')->get()->getRowArray();

        if (!$adminPusat) {
            echo "No admin_pusat user found. Please create one first.\n";
            return;
        }

        $notifications = [
            [
                'user_id' => $adminPusat['id'],
                'tipe_notifikasi' => 'data_masuk',
                'judul' => 'Data Baru dari Unit Teknik Informatika',
                'pesan' => 'Unit Teknik Informatika telah mengirim data UIGM dan menunggu review dari Admin Pusat.',
                'data_terkait' => json_encode(['unit_id' => 1, 'action' => 'review_data']),
                'is_read' => false,
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))
            ],
            [
                'user_id' => $adminPusat['id'],
                'tipe_notifikasi' => 'data_masuk',
                'judul' => 'Data Baru dari Unit Teknik Sipil',
                'pesan' => 'Unit Teknik Sipil telah mengirim data UIGM dan menunggu review dari Admin Pusat.',
                'data_terkait' => json_encode(['unit_id' => 2, 'action' => 'review_data']),
                'is_read' => false,
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 hour')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-1 hour'))
            ],
            [
                'user_id' => $adminPusat['id'],
                'tipe_notifikasi' => 'revisi_selesai',
                'judul' => 'Revisi Selesai dari Unit Teknik Mesin',
                'pesan' => 'Unit Teknik Mesin telah menyelesaikan revisi data UIGM sesuai catatan review.',
                'data_terkait' => json_encode(['unit_id' => 3, 'action' => 'review_revision']),
                'is_read' => true,
                'tanggal_dibaca' => date('Y-m-d H:i:s', strtotime('-30 minutes')),
                'created_at' => date('Y-m-d H:i:s', strtotime('-45 minutes')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-30 minutes'))
            ],
            [
                'user_id' => $adminPusat['id'],
                'tipe_notifikasi' => 'deadline',
                'judul' => 'Pengingat Deadline Review',
                'pesan' => 'Terdapat 3 pengiriman data yang belum direview. Deadline review: 31 Desember 2024.',
                'data_terkait' => json_encode(['deadline' => '2024-12-31', 'action' => 'review_pending']),
                'is_read' => false,
                'created_at' => date('Y-m-d H:i:s', strtotime('-15 minutes')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-15 minutes'))
            ],
            [
                'user_id' => $adminPusat['id'],
                'tipe_notifikasi' => 'approval',
                'judul' => 'Data Disetujui - Unit Teknik Elektro',
                'pesan' => 'Data UIGM Unit Teknik Elektro telah berhasil disetujui dan difinalisasi.',
                'data_terkait' => json_encode(['unit_id' => 4, 'action' => 'view_approved']),
                'is_read' => true,
                'tanggal_dibaca' => date('Y-m-d H:i:s', strtotime('-10 minutes')),
                'created_at' => date('Y-m-d H:i:s', strtotime('-20 minutes')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-10 minutes'))
            ]
        ];

        // Insert notifications
        foreach ($notifications as $notification) {
            $this->db->table('notifikasi')->insert($notification);
        }

        echo "Sample notifications created successfully!\n";
    }
}
