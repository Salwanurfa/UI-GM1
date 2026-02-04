<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ForceDraftStatusSeeder extends Seeder
{
    public function run()
    {
        echo "Forcing all pengiriman to draft status...\n";

        // Get admin_unit user
        $adminUnit = $this->db->table('users')->where('username', 'admin_unit')->get()->getRowArray();

        if (!$adminUnit) {
            echo "admin_unit user not found!\n";
            return;
        }

        echo "Found admin_unit user ID: {$adminUnit['id']}, Unit ID: {$adminUnit['unit_id']}\n";

        // Force update ALL pengiriman records for this unit to draft status
        $updated = $this->db->table('pengiriman_unit')
            ->where('unit_id', $adminUnit['unit_id'])
            ->update([
                'status_pengiriman' => 'draft',
                'progress_persen' => 0.0,
                'tanggal_kirim' => null,
                'tanggal_disetujui' => null,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        echo "Updated {$updated} pengiriman records to draft status\n";

        // Also reset all review_kategori for this unit's pengiriman
        $pengirimanIds = $this->db->table('pengiriman_unit')
            ->select('id')
            ->where('unit_id', $adminUnit['unit_id'])
            ->get()
            ->getResultArray();

        foreach ($pengirimanIds as $p) {
            $reviewUpdated = $this->db->table('review_kategori')
                ->where('pengiriman_id', $p['id'])
                ->update([
                    'status_review' => 'pending',
                    'catatan_review' => null,
                    'skor_review' => null,
                    'reviewer_id' => null,
                    'tanggal_review' => null
                ]);

            if ($reviewUpdated > 0) {
                echo "Reset {$reviewUpdated} review records for pengiriman {$p['id']}\n";
            }
        }

        // Verify final status
        $finalStatus = $this->db->table('pengiriman_unit')
            ->where('unit_id', $adminUnit['unit_id'])
            ->get()
            ->getResultArray();

        echo "\nFinal pengiriman status for unit {$adminUnit['unit_id']}:\n";
        foreach ($finalStatus as $status) {
            echo "ID: {$status['id']}, Status: {$status['status_pengiriman']}, Progress: {$status['progress_persen']}%\n";
        }

        echo "\nAll pengiriman records forced to draft status!\n";
        echo "Admin Unit should now be able to edit data.\n";
    }
}
