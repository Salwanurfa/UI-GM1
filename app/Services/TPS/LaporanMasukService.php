<?php

namespace App\Services\TPS;

use App\Models\WasteModel;
use App\Models\UserModel;
use App\Models\UnitModel;

class LaporanMasukService
{
    protected $wasteModel;
    protected $userModel;
    protected $unitModel;

    public function __construct()
    {
        $this->wasteModel = new WasteModel();
        $this->userModel = new UserModel();
        $this->unitModel = new UnitModel();
    }

    public function getLaporanMasuk(): array
    {
        try {
            $user = session()->get('user');
            $tpsId = $user['unit_id'];

            return [
                'laporan_pending' => $this->getLaporanPending($tpsId),
                'laporan_reviewed' => $this->getLaporanReviewed($tpsId),
                'stats' => $this->getStats($tpsId)
            ];
        } catch (\Exception $e) {
            log_message('error', 'TPS Laporan Masuk Service Error: ' . $e->getMessage());
            
            return [
                'laporan_pending' => [],
                'laporan_reviewed' => [],
                'stats' => $this->getDefaultStats()
            ];
        }
    }

    public function getDetailLaporan(int $id): array
    {
        try {
            $user = session()->get('user');
            $tpsId = $user['unit_id'];

            $db = \Config\Database::connect();
            
            $laporan = $db->table('waste_management')
                ->select('waste_management.*, 
                         unit.nama_unit as unit_nama, 
                         users.nama_lengkap as user_nama,
                         users.email as user_email')
                ->join('unit', 'unit.id = waste_management.unit_id', 'left')
                ->join('users', 'users.id = waste_management.user_id', 'left')
                ->where('waste_management.id', $id)
                ->get()
                ->getRowArray();

            if (!$laporan) {
                return ['success' => false, 'message' => 'Laporan tidak ditemukan'];
            }

            // Verify this laporan is for this TPS (check if it's sent to this TPS)
            // For now, we'll check if status is 'dikirim_ke_tps'
            if (!in_array($laporan['status'], ['dikirim_ke_tps', 'disetujui_tps', 'ditolak_tps'])) {
                return ['success' => false, 'message' => 'Laporan tidak valid untuk TPS ini'];
            }

            return ['success' => true, 'data' => $laporan];

        } catch (\Exception $e) {
            log_message('error', 'Get Detail Laporan Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem'];
        }
    }

    public function approveLaporan(int $id, string $catatan = ''): array
    {
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            $user = session()->get('user');
            $tpsId = $user['unit_id'];

            log_message('info', '=== TPS APPROVE START ===');
            log_message('info', 'Laporan ID: ' . $id);
            log_message('info', 'TPS User ID: ' . $user['id']);
            log_message('info', 'Catatan: ' . $catatan);

            $laporan = $this->wasteModel->find($id);
            
            if (!$laporan) {
                $db->transRollback();
                log_message('error', 'TPS Approve - Laporan not found: ' . $id);
                return ['success' => false, 'message' => 'Laporan tidak ditemukan'];
            }

            log_message('info', 'TPS Approve - Current status: ' . $laporan['status']);

            if ($laporan['status'] !== 'dikirim_ke_tps') {
                $db->transRollback();
                log_message('error', 'TPS Approve - Invalid status: ' . $laporan['status']);
                return ['success' => false, 'message' => 'Laporan tidak dalam status menunggu review TPS'];
            }

            // Update status to approved by TPS - data langsung masuk ke TPS (tidak kembali ke user)
            $updateData = [
                'status' => 'disetujui_tps',  // PENTING: status harus 'disetujui_tps' bukan 'ditolak_tps'
                'tps_reviewed_by' => $user['id'],
                'tps_reviewed_at' => date('Y-m-d H:i:s'),
                'tps_catatan' => $catatan ?: 'Disetujui oleh TPS',
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            log_message('info', 'TPS Approve - Setting status to: disetujui_tps for ID: ' . $id);
            log_message('info', 'TPS Approve - Update data: ' . json_encode($updateData));
            
            $result = $this->wasteModel->update($id, $updateData);
            
            log_message('info', 'TPS Approve - Update result: ' . ($result ? 'SUCCESS' : 'FAILED'));
            
            // Verify the update
            $verifyData = $this->wasteModel->find($id);
            log_message('info', 'TPS Approve - Verified status after update: ' . ($verifyData['status'] ?? 'NULL'));
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                log_message('error', 'TPS Approve - Transaction failed');
                return ['success' => false, 'message' => 'Gagal menyetujui laporan'];
            }

            log_message('info', '=== TPS APPROVE SUCCESS ===');
            return ['success' => true, 'message' => 'Laporan berhasil disetujui. Data telah masuk ke sistem TPS.'];

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', '=== TPS APPROVE EXCEPTION ===');
            log_message('error', 'Approve Laporan Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()];
        }
    }

    public function rejectLaporan(int $id, string $catatan): array
    {
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            $user = session()->get('user');
            $tpsId = $user['unit_id'];

            log_message('info', '=== TPS REJECT START ===');
            log_message('info', 'Laporan ID: ' . $id);
            log_message('info', 'TPS User ID: ' . $user['id']);
            log_message('info', 'Catatan: ' . $catatan);

            $laporan = $this->wasteModel->find($id);
            
            if (!$laporan) {
                $db->transRollback();
                log_message('error', 'TPS Reject - Laporan not found: ' . $id);
                return ['success' => false, 'message' => 'Laporan tidak ditemukan'];
            }

            log_message('info', 'TPS Reject - Current status: ' . $laporan['status']);

            if ($laporan['status'] !== 'dikirim_ke_tps') {
                $db->transRollback();
                log_message('error', 'TPS Reject - Invalid status: ' . $laporan['status']);
                return ['success' => false, 'message' => 'Laporan tidak dalam status menunggu review TPS'];
            }

            if (empty($catatan)) {
                $db->transRollback();
                log_message('error', 'TPS Reject - Empty catatan');
                return ['success' => false, 'message' => 'Alasan penolakan harus diisi'];
            }

            // Update status to rejected by TPS
            $updateData = [
                'status' => 'ditolak_tps',  // PENTING: status harus 'ditolak_tps' bukan 'disetujui_tps'
                'tps_reviewed_by' => $user['id'],
                'tps_reviewed_at' => date('Y-m-d H:i:s'),
                'tps_catatan' => $catatan,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            log_message('info', 'TPS Reject - Setting status to: ditolak_tps for ID: ' . $id);
            log_message('info', 'TPS Reject - Update data: ' . json_encode($updateData));
            
            $result = $this->wasteModel->update($id, $updateData);
            
            log_message('info', 'TPS Reject - Update result: ' . ($result ? 'SUCCESS' : 'FAILED'));
            
            // Verify the update
            $verifyData = $this->wasteModel->find($id);
            log_message('info', 'TPS Reject - Verified status after update: ' . ($verifyData['status'] ?? 'NULL'));
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                log_message('error', 'TPS Reject - Transaction failed');
                return ['success' => false, 'message' => 'Gagal menolak laporan'];
            }

            log_message('info', '=== TPS REJECT SUCCESS ===');
            return ['success' => true, 'message' => 'Laporan berhasil ditolak'];

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', '=== TPS REJECT EXCEPTION ===');
            log_message('error', 'Reject Laporan Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()];
        }
    }

    private function getLaporanPending(int $tpsId): array
    {
        try {
            $db = \Config\Database::connect();
            
            // Get all laporan with status 'dikirim_ke_tps' (sent to TPS for review)
            return $db->table('waste_management')
                ->select('waste_management.*, 
                         unit.nama_unit as unit_nama, 
                         users.nama_lengkap as user_nama,
                         users.email as user_email')
                ->join('unit', 'unit.id = waste_management.unit_id', 'left')
                ->join('users', 'users.id = waste_management.user_id', 'left')
                ->where('waste_management.status', 'dikirim_ke_tps')
                ->orderBy('waste_management.created_at', 'ASC')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error getting laporan pending: ' . $e->getMessage());
            return [];
        }
    }

    private function getLaporanReviewed(int $tpsId): array
    {
        try {
            $db = \Config\Database::connect();
            
            // Get laporan that have been reviewed by this TPS
            return $db->table('waste_management')
                ->select('waste_management.*, 
                         unit.nama_unit as unit_nama, 
                         users.nama_lengkap as user_nama,
                         reviewer.nama_lengkap as reviewed_by_name')
                ->join('unit', 'unit.id = waste_management.unit_id', 'left')
                ->join('users', 'users.id = waste_management.user_id', 'left')
                ->join('users as reviewer', 'reviewer.id = waste_management.tps_reviewed_by', 'left')
                ->where('waste_management.tps_reviewed_by', session()->get('user')['id'])
                ->whereIn('waste_management.status', ['disetujui_tps', 'ditolak_tps', 'dikirim_ke_admin', 'disetujui', 'ditolak'])
                ->orderBy('waste_management.tps_reviewed_at', 'DESC')
                ->limit(20)
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error getting laporan reviewed: ' . $e->getMessage());
            return [];
        }
    }

    private function getStats(int $tpsId): array
    {
        try {
            $today = date('Y-m-d');
            
            return [
                'pending_count' => $this->wasteModel
                    ->where('status', 'dikirim_ke_tps')
                    ->countAllResults(),
                
                'approved_today' => $this->wasteModel
                    ->where('status', 'disetujui_tps')
                    ->where('DATE(tps_reviewed_at)', $today)
                    ->countAllResults(),
                
                'rejected_today' => $this->wasteModel
                    ->where('status', 'ditolak_tps')
                    ->where('DATE(tps_reviewed_at)', $today)
                    ->countAllResults(),
                
                'total_reviewed' => $this->wasteModel
                    ->where('tps_reviewed_by', session()->get('user')['id'])
                    ->countAllResults()
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error getting stats: ' . $e->getMessage());
            return $this->getDefaultStats();
        }
    }

    private function getDefaultStats(): array
    {
        return [
            'pending_count' => 0,
            'approved_today' => 0,
            'rejected_today' => 0,
            'total_reviewed' => 0
        ];
    }
}
