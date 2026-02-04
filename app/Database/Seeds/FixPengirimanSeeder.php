<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FixPengirimanSeeder extends Seeder
{
    public function run()
    {
        echo "Fixing pengiriman status for admin_unit...\n";

        // Get admin_unit user
        $adminUnit = $this->db->table('users')->where('role', 'admin_unit')->get()->getRowArray();

        if (!$adminUnit) {
            echo "No admin_unit user found!\n";
            return;
        }

        echo "Found admin_unit: {$adminUnit['username']} (Unit ID: {$adminUnit['unit_id']})\n";

        // Get active year
        $tahunAktif = $this->db->table('tahun_penilaian')->where('status_aktif', true)->get()->getRowArray();

        if (!$tahunAktif) {
            echo "No active year found!\n";
            return;
        }

        echo "Active year: {$tahunAktif['tahun']}\n";

        // Check existing pengiriman
        $pengiriman = $this->db->table('pengiriman_unit')
            ->where('unit_id', $adminUnit['unit_id'])
            ->where('tahun_penilaian_id', $tahunAktif['id'])
            ->get()->getRowArray();

        if ($pengiriman) {
            echo "Found existing pengiriman ID: {$pengiriman['id']}, Status: {$pengiriman['status_pengiriman']}\n";

            // If status is not draft or perlu_revisi, reset it to draft
            if (!in_array($pengiriman['status_pengiriman'], ['draft', 'perlu_revisi'])) {
                echo "Resetting status to 'draft' to allow editing...\n";
                $this->db->table('pengiriman_unit')
                    ->where('id', $pengiriman['id'])
                    ->update([
                        'status_pengiriman' => 'draft',
                        'progress_persen' => 0.0,
                        'tanggal_kirim' => null,
                        'tanggal_disetujui' => null,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                echo "Status reset to 'draft'\n";
            } else {
                echo "Status is already editable: {$pengiriman['status_pengiriman']}\n";
            }
        } else {
            echo "No pengiriman found, creating new draft...\n";
            $this->db->table('pengiriman_unit')->insert([
                'unit_id' => $adminUnit['unit_id'],
                'tahun_penilaian_id' => $tahunAktif['id'],
                'status_pengiriman' => 'draft',
                'progress_persen' => 0.0,
                'versi' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            echo "New draft pengiriman created\n";
        }

        // Also reset any review_kategori records to pending
        $pengirimanId = $pengiriman['id'] ?? $this->db->insertID();

        $this->db->table('review_kategori')
            ->where('pengiriman_id', $pengirimanId)
            ->update([
                'status_review' => 'pending',
                'catatan_review' => null,
                'skor_review' => null,
                'reviewer_id' => null,
                'tanggal_review' => null
            ]);

        echo "Reset review_kategori records to pending status\n";
        echo "Fix completed! Admin Unit should now be able to edit data.\n";
    }
}
