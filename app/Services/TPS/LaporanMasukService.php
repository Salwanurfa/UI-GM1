<?php

namespace App\Services\TPS;

use App\Models\WasteModel;
use App\Models\UserModel;
use App\Models\UnitModel;
use App\Models\LimbahB3Model;
use App\Models\MasterLimbahB3Model;

class LaporanMasukService
{
    protected $wasteModel;
    protected $userModel;
    protected $unitModel;
    protected $limbahB3Model;
    protected $masterLimbahB3Model;

    public function __construct()
    {
        $this->wasteModel = new WasteModel();
        $this->userModel = new UserModel();
        $this->unitModel = new UnitModel();
        $this->limbahB3Model = new LimbahB3Model();
        $this->masterLimbahB3Model = new MasterLimbahB3Model();
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
            
            // First check if it's a waste report
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

            if ($laporan) {
                $laporan['type'] = 'waste';
                if (!in_array($laporan['status'], ['dikirim_ke_tps', 'disetujui_tps', 'ditolak_tps'])) {
                    return ['success' => false, 'message' => 'Laporan tidak valid untuk TPS ini'];
                }
                return ['success' => true, 'data' => $laporan];
            }
            
            // If not waste, check if it's limbah_b3
            $laporan = $db->table('limbah_b3')
                ->select('limbah_b3.*,
                         master_limbah_b3.nama_limbah,
                         users.nama_lengkap as user_nama,
                         users.email as user_email')
                ->join('master_limbah_b3', 'master_limbah_b3.id = limbah_b3.master_b3_id', 'left')
                ->join('users', 'users.id = limbah_b3.id_user', 'left')
                ->where('limbah_b3.id', $id)
                ->get()
                ->getRowArray();
            
            if ($laporan) {
                $laporan['type'] = 'limbah_b3';
                if (!in_array($laporan['status'], ['dikirim_ke_tps', 'disetujui_tps', 'ditolak_tps'])) {
                    return ['success' => false, 'message' => 'Laporan tidak valid untuk TPS ini'];
                }
                return ['success' => true, 'data' => $laporan];
            }
            
            return ['success' => false, 'message' => 'Laporan tidak ditemukan'];

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

            log_message('info', '=== TPS APPROVE START ===');
            log_message('info', 'Laporan ID: ' . $id);
            log_message('info', 'TPS User ID: ' . $user['id']);
            log_message('info', 'Catatan: ' . $catatan);

            // Check if waste report
            $laporan = $this->wasteModel->find($id);
            
            if ($laporan) {
                log_message('info', 'TPS Approve - Type: WASTE');
                return $this->approveWasteLaporan($id, $user['id'], $catatan, $db);
            }
            
            // Check if limbah_b3 report
            $limbah = $this->limbahB3Model->find($id);
            
            if ($limbah) {
                log_message('info', 'TPS Approve - Type: LIMBAH_B3');
                return $this->approveLimbahB3Laporan($id, $user['id'], $catatan, $db);
            }
            
            $db->transRollback();
            log_message('error', 'TPS Approve - Laporan not found: ' . $id);
            return ['success' => false, 'message' => 'Laporan tidak ditemukan'];

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', '=== TPS APPROVE EXCEPTION ===');
            log_message('error', 'Approve Laporan Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()];
        }
    }

    private function approveWasteLaporan(int $id, int $userId, string $catatan, object $db): array
    {
        $laporan = $this->wasteModel->find($id);
        
        if (!$laporan) {
            $db->transRollback();
            return ['success' => false, 'message' => 'Laporan tidak ditemukan'];
        }

        log_message('info', 'TPS Approve Waste - Current status: ' . $laporan['status']);

        if ($laporan['status'] !== 'dikirim_ke_tps') {
            $db->transRollback();
            log_message('error', 'TPS Approve Waste - Invalid status: ' . $laporan['status']);
            return ['success' => false, 'message' => 'Laporan tidak dalam status menunggu review TPS'];
        }

        $updateData = [
            'status' => 'disetujui_tps',
            'tps_reviewed_by' => $userId,
            'tps_reviewed_at' => date('Y-m-d H:i:s'),
            'tps_catatan' => $catatan ?: 'Disetujui oleh TPS',
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        log_message('info', 'TPS Approve Waste - Setting status to: disetujui_tps for ID: ' . $id);
        
        $result = $this->wasteModel->update($id, $updateData);
        
        log_message('info', 'TPS Approve Waste - Update result: ' . ($result ? 'SUCCESS' : 'FAILED'));
        
        $db->transComplete();
        
        if ($db->transStatus() === false) {
            log_message('error', 'TPS Approve Waste - Transaction failed');
            return ['success' => false, 'message' => 'Gagal menyetujui laporan'];
        }

        log_message('info', '=== TPS APPROVE WASTE SUCCESS ===');
        return ['success' => true, 'message' => 'Laporan sampah berhasil disetujui. Data telah masuk ke sistem TPS.'];
    }

    private function approveLimbahB3Laporan(int $id, int $userId, string $catatan, object $db): array
    {
        $limbah = $this->limbahB3Model->find($id);
        
        if (!$limbah) {
            $db->transRollback();
            return ['success' => false, 'message' => 'Laporan limbah B3 tidak ditemukan'];
        }

        log_message('info', 'TPS Approve Limbah B3 - Current status: ' . $limbah['status']);

        if ($limbah['status'] !== 'dikirim_ke_tps') {
            $db->transRollback();
            log_message('error', 'TPS Approve Limbah B3 - Invalid status: ' . $limbah['status']);
            return ['success' => false, 'message' => 'Laporan limbah B3 tidak dalam status menunggu review TPS'];
        }

        $updateData = [
            'status' => 'disetujui_tps',
            'keterangan' => $catatan ?: 'Disetujui oleh TPS'
        ];
        
        log_message('info', 'TPS Approve Limbah B3 - Setting status to: disetujui_tps for ID: ' . $id);
        
        $result = $this->limbahB3Model->update($id, $updateData);
        
        log_message('info', 'TPS Approve Limbah B3 - Update result: ' . ($result ? 'SUCCESS' : 'FAILED'));
        
        $db->transComplete();
        
        if ($db->transStatus() === false) {
            log_message('error', 'TPS Approve Limbah B3 - Transaction failed');
            return ['success' => false, 'message' => 'Gagal menyetujui laporan limbah B3'];
        }

        log_message('info', '=== TPS APPROVE LIMBAH B3 SUCCESS ===');
        return ['success' => true, 'message' => 'Laporan limbah B3 berhasil disetujui. Data telah masuk ke sistem TPS.'];
    }

    public function rejectLaporan(int $id, string $catatan): array
    {
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            $user = session()->get('user');

            log_message('info', '=== TPS REJECT START ===');
            log_message('info', 'Laporan ID: ' . $id);
            log_message('info', 'TPS User ID: ' . $user['id']);
            log_message('info', 'Catatan: ' . $catatan);

            // Check if waste report
            $laporan = $this->wasteModel->find($id);
            
            if ($laporan) {
                log_message('info', 'TPS Reject - Type: WASTE');
                return $this->rejectWasteLaporan($id, $user['id'], $catatan, $db);
            }
            
            // Check if limbah_b3 report
            $limbah = $this->limbahB3Model->find($id);
            
            if ($limbah) {
                log_message('info', 'TPS Reject - Type: LIMBAH_B3');
                return $this->rejectLimbahB3Laporan($id, $user['id'], $catatan, $db);
            }
            
            $db->transRollback();
            log_message('error', 'TPS Reject - Laporan not found: ' . $id);
            return ['success' => false, 'message' => 'Laporan tidak ditemukan'];

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', '=== TPS REJECT EXCEPTION ===');
            log_message('error', 'Reject Laporan Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()];
        }
    }

    private function rejectWasteLaporan(int $id, int $userId, string $catatan, object $db): array
    {
        $laporan = $this->wasteModel->find($id);
        
        if (!$laporan) {
            $db->transRollback();
            return ['success' => false, 'message' => 'Laporan tidak ditemukan'];
        }

        log_message('info', 'TPS Reject Waste - Current status: ' . $laporan['status']);

        if ($laporan['status'] !== 'dikirim_ke_tps') {
            $db->transRollback();
            log_message('error', 'TPS Reject Waste - Invalid status: ' . $laporan['status']);
            return ['success' => false, 'message' => 'Laporan tidak dalam status menunggu review TPS'];
        }

        if (empty($catatan)) {
            $db->transRollback();
            log_message('error', 'TPS Reject Waste - Empty catatan');
            return ['success' => false, 'message' => 'Alasan penolakan harus diisi'];
        }

        $updateData = [
            'status' => 'ditolak_tps',
            'tps_reviewed_by' => $userId,
            'tps_reviewed_at' => date('Y-m-d H:i:s'),
            'tps_catatan' => $catatan,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        log_message('info', 'TPS Reject Waste - Setting status to: ditolak_tps for ID: ' . $id);
        
        $result = $this->wasteModel->update($id, $updateData);
        
        log_message('info', 'TPS Reject Waste - Update result: ' . ($result ? 'SUCCESS' : 'FAILED'));
        
        $db->transComplete();
        
        if ($db->transStatus() === false) {
            log_message('error', 'TPS Reject Waste - Transaction failed');
            return ['success' => false, 'message' => 'Gagal menolak laporan'];
        }

        log_message('info', '=== TPS REJECT WASTE SUCCESS ===');
        return ['success' => true, 'message' => 'Laporan sampah berhasil ditolak'];
    }

    private function rejectLimbahB3Laporan(int $id, int $userId, string $catatan, object $db): array
    {
        $limbah = $this->limbahB3Model->find($id);
        
        if (!$limbah) {
            $db->transRollback();
            return ['success' => false, 'message' => 'Laporan limbah B3 tidak ditemukan'];
        }

        log_message('info', 'TPS Reject Limbah B3 - Current status: ' . $limbah['status']);

        if ($limbah['status'] !== 'dikirim_ke_tps') {
            $db->transRollback();
            log_message('error', 'TPS Reject Limbah B3 - Invalid status: ' . $limbah['status']);
            return ['success' => false, 'message' => 'Laporan limbah B3 tidak dalam status menunggu review TPS'];
        }

        if (empty($catatan)) {
            $db->transRollback();
            log_message('error', 'TPS Reject Limbah B3 - Empty catatan');
            return ['success' => false, 'message' => 'Alasan penolakan harus diisi'];
        }

        $updateData = [
            'status' => 'ditolak_tps',
            'keterangan' => $catatan
        ];
        
        log_message('info', 'TPS Reject Limbah B3 - Setting status to: ditolak_tps for ID: ' . $id);
        
        $result = $this->limbahB3Model->update($id, $updateData);
        
        log_message('info', 'TPS Reject Limbah B3 - Update result: ' . ($result ? 'SUCCESS' : 'FAILED'));
        
        $db->transComplete();
        
        if ($db->transStatus() === false) {
            log_message('error', 'TPS Reject Limbah B3 - Transaction failed');
            return ['success' => false, 'message' => 'Gagal menolak laporan limbah B3'];
        }

        log_message('info', '=== TPS REJECT LIMBAH B3 SUCCESS ===');
        return ['success' => true, 'message' => 'Laporan limbah B3 berhasil ditolak'];
    }

    private function getLaporanPending(int $tpsId): array
    {
        try {
            $db = \Config\Database::connect();
            
            // Get all waste reports with status 'dikirim_ke_tps'
            $wasteReports = $db->table('waste_management')
                ->select('waste_management.*, 
                         unit.nama_unit as unit_nama, 
                         users.nama_lengkap as user_nama,
                         users.email as user_email,
                         "waste" as type')
                ->join('unit', 'unit.id = waste_management.unit_id', 'left')
                ->join('users', 'users.id = waste_management.user_id', 'left')
                ->where('waste_management.status', 'dikirim_ke_tps')
                ->get()
                ->getResultArray();
            
            // Get all limbah_b3 reports with status 'dikirim_ke_tps'
            $limbahB3Reports = $db->table('limbah_b3')
                ->select('limbah_b3.id,
                         limbah_b3.id_user as user_id,
                         limbah_b3.master_b3_id,
                         limbah_b3.lokasi,
                         limbah_b3.timbulan,
                         limbah_b3.satuan,
                         limbah_b3.bentuk_fisik,
                         limbah_b3.kemasan,
                         limbah_b3.keterangan,
                         limbah_b3.status,
                         limbah_b3.tanggal_input as created_at,
                         master_limbah_b3.nama_limbah,
                         users.nama_lengkap as user_nama,
                         users.email as user_email,
                         "limbah_b3" as type')
                ->join('master_limbah_b3', 'master_limbah_b3.id = limbah_b3.master_b3_id', 'left')
                ->join('users', 'users.id = limbah_b3.id_user', 'left')
                ->where('limbah_b3.status', 'dikirim_ke_tps')
                ->get()
                ->getResultArray();
            
            // Combine and sort by date
            $allReports = array_merge($wasteReports, $limbahB3Reports);
            
            // Sort by created_at ascending
            usort($allReports, function($a, $b) {
                return strtotime($a['created_at']) - strtotime($b['created_at']);
            });
            
            return $allReports;
        } catch (\Exception $e) {
            log_message('error', 'Error getting laporan pending: ' . $e->getMessage());
            return [];
        }
    }

    private function getLaporanReviewed(int $tpsId): array
    {
        try {
            $db = \Config\Database::connect();
            
            // Get waste reports that have been reviewed by this TPS user
            $wasteReviewed = $db->table('waste_management')
                ->select('waste_management.*, 
                         unit.nama_unit as unit_nama, 
                         users.nama_lengkap as user_nama,
                         reviewer.nama_lengkap as reviewed_by_name,
                         "waste" as type')
                ->join('unit', 'unit.id = waste_management.unit_id', 'left')
                ->join('users', 'users.id = waste_management.user_id', 'left')
                ->join('users as reviewer', 'reviewer.id = waste_management.tps_reviewed_by', 'left')
                ->where('waste_management.tps_reviewed_by', session()->get('user')['id'])
                ->whereIn('waste_management.status', ['disetujui_tps', 'ditolak_tps', 'dikirim_ke_admin', 'disetujui', 'ditolak'])
                ->get()
                ->getResultArray();
            
            // Get limbah_b3 reports that have been reviewed
            // NOTE: limbah_b3 doesn't have tps_reviewed_by field yet, so we'll need to add approval mechanism
            // For now, we'll check for approved/rejected status
            $limbahB3Reviewed = $db->table('limbah_b3')
                ->select('limbah_b3.id,
                         limbah_b3.id_user as user_id,
                         limbah_b3.master_b3_id,
                         limbah_b3.lokasi,
                         limbah_b3.timbulan,
                         limbah_b3.satuan,
                         limbah_b3.bentuk_fisik,
                         limbah_b3.kemasan,
                         limbah_b3.keterangan,
                         limbah_b3.status,
                         limbah_b3.tanggal_input as tps_reviewed_at,
                         master_limbah_b3.nama_limbah,
                         users.nama_lengkap as user_nama,
                         "limbah_b3" as type')
                ->join('master_limbah_b3', 'master_limbah_b3.id = limbah_b3.master_b3_id', 'left')
                ->join('users', 'users.id = limbah_b3.id_user', 'left')
                ->whereIn('limbah_b3.status', ['disetujui_tps', 'ditolak_tps', 'disetujui_admin'])
                ->get()
                ->getResultArray();
            
            // Combine and sort
            $allReviewed = array_merge($wasteReviewed, $limbahB3Reviewed);
            
            // Sort by tps_reviewed_at descending, limit 20
            usort($allReviewed, function($a, $b) {
                $dateA = strtotime($a['tps_reviewed_at'] ?? $a['created_at'] ?? 0);
                $dateB = strtotime($b['tps_reviewed_at'] ?? $b['created_at'] ?? 0);
                return $dateB - $dateA;
            });
            
            return array_slice($allReviewed, 0, 20);
        } catch (\Exception $e) {
            log_message('error', 'Error getting laporan reviewed: ' . $e->getMessage());
            return [];
        }
    }

    private function getStats(int $tpsId): array
    {
        try {
            $today = date('Y-m-d');
            
            // Count pending waste and limbah_b3
            $pendingWaste = $this->wasteModel
                ->where('status', 'dikirim_ke_tps')
                ->countAllResults();
            
            $pendingLimbahB3 = $this->limbahB3Model
                ->where('status', 'dikirim_ke_tps')
                ->countAllResults();
            
            // Count approved waste and limbah_b3 today
            $approvedWasteToday = $this->wasteModel
                ->where('status', 'disetujui_tps')
                ->where('DATE(tps_reviewed_at)', $today)
                ->countAllResults();
            
            $approvedLimbahB3Today = $this->limbahB3Model
                ->where('status', 'disetujui_tps')
                ->where('DATE(tanggal_input)', $today)
                ->countAllResults();
            
            // Count rejected waste and limbah_b3 today
            $rejectedWasteToday = $this->wasteModel
                ->where('status', 'ditolak_tps')
                ->where('DATE(tps_reviewed_at)', $today)
                ->countAllResults();
            
            $rejectedLimbahB3Today = $this->limbahB3Model
                ->where('status', 'ditolak_tps')
                ->where('DATE(tanggal_input)', $today)
                ->countAllResults();
            
            // Count total reviewed for this user (waste only, limbah_b3 tracking to be added)
            $totalReviewed = $this->wasteModel
                ->where('tps_reviewed_by', session()->get('user')['id'])
                ->countAllResults();
            
            return [
                'pending_count' => $pendingWaste + $pendingLimbahB3,
                'approved_today' => $approvedWasteToday + $approvedLimbahB3Today,
                'rejected_today' => $rejectedWasteToday + $rejectedLimbahB3Today,
                'total_reviewed' => $totalReviewed
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
