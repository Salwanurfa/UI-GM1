<?php

namespace App\Services\Admin;

use App\Models\WasteModel;
use App\Models\UnitModel;
use App\Models\UserModel;

class ReviewService
{
    protected $wasteModel;
    protected $unitModel;
    protected $userModel;

    public function __construct()
    {
        $this->wasteModel = new WasteModel();
        $this->unitModel = new UnitModel();
        $this->userModel = new UserModel();
    }

    public function getReviewData(): array
    {
        try {
            return [
                'pending_reviews' => $this->getPendingReviews(),
                'recent_reviews' => $this->getRecentReviews(),
                'stats' => $this->getReviewStats(),
                'queue_summary' => $this->getQueueSummary()
            ];
        } catch (\Exception $e) {
            log_message('error', 'Admin Review Service Error: ' . $e->getMessage());
            
            return [
                'pending_reviews' => [],
                'recent_reviews' => [],
                'stats' => $this->getDefaultStats(),
                'queue_summary' => []
            ];
        }
    }

    public function approveWaste(int $id, array $data = []): array
    {
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            $waste = $this->wasteModel->find($id);
            if (!$waste) {
                $db->transRollback();
                return ['success' => false, 'message' => 'Data waste tidak ditemukan'];
            }

            if ($waste['status'] !== 'dikirim' && $waste['status'] !== 'pending') {
                $db->transRollback();
                return ['success' => false, 'message' => 'Data waste tidak dalam status dikirim/pending'];
            }

            // 1. Insert ke laporan_waste
            $laporanData = [
                'waste_id' => $waste['id'],
                'unit_id' => $waste['unit_id'],
                'kategori_id' => $waste['kategori_id'],
                'jenis_sampah' => $waste['jenis_sampah'],
                'berat_kg' => $waste['berat_kg'],
                'satuan' => $waste['satuan'] ?? 'kg',
                'jumlah' => $waste['jumlah'] ?? $waste['berat_kg'],
                'nilai_rupiah' => $waste['nilai_rupiah'] ?? 0,
                'tanggal_input' => $waste['tanggal'] ?? $waste['created_at'],
                'status' => 'approved',
                'reviewed_by' => session()->get('user')['id'],
                'reviewed_at' => date('Y-m-d H:i:s'),
                'review_notes' => $data['notes'] ?? 'Disetujui oleh admin',
                'created_by' => $waste['user_id'] ?? $waste['created_by'] ?? null,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $db->table('laporan_waste')->insert($laporanData);
            
            // 2. Update status and set action_timestamp (will be auto-deleted after 2 days)
            $this->wasteModel->update($id, [
                'status' => 'disetujui',
                'action_timestamp' => date('Y-m-d H:i:s'),
                'catatan_admin' => $data['notes'] ?? 'Disetujui oleh admin',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                return ['success' => false, 'message' => 'Gagal menyetujui data waste'];
            }
            
            // Send notification to user (if notification system exists)
            $this->sendApprovalNotification($waste);
            
            return ['success' => true, 'message' => 'Data waste berhasil disetujui'];

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Approve Waste Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()];
        }
    }

    public function rejectWaste(int $id, array $data): array
    {
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            $waste = $this->wasteModel->find($id);
            if (!$waste) {
                $db->transRollback();
                return ['success' => false, 'message' => 'Data waste tidak ditemukan'];
            }

            if ($waste['status'] !== 'dikirim' && $waste['status'] !== 'pending') {
                $db->transRollback();
                return ['success' => false, 'message' => 'Data waste tidak dalam status dikirim/pending'];
            }

            if (empty($data['notes'])) {
                $db->transRollback();
                return ['success' => false, 'message' => 'Alasan penolakan harus diisi'];
            }

            // 1. Insert ke laporan_waste dengan status rejected
            $laporanData = [
                'waste_id' => $waste['id'],
                'unit_id' => $waste['unit_id'],
                'kategori_id' => $waste['kategori_id'],
                'jenis_sampah' => $waste['jenis_sampah'],
                'berat_kg' => $waste['berat_kg'],
                'satuan' => $waste['satuan'] ?? 'kg',
                'jumlah' => $waste['jumlah'] ?? $waste['berat_kg'],
                'nilai_rupiah' => 0, // Rejected waste has no value
                'tanggal_input' => $waste['tanggal'] ?? $waste['created_at'],
                'status' => 'rejected',
                'reviewed_by' => session()->get('user')['id'],
                'reviewed_at' => date('Y-m-d H:i:s'),
                'review_notes' => $data['notes'],
                'created_by' => $waste['user_id'] ?? $waste['created_by'] ?? null,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $db->table('laporan_waste')->insert($laporanData);
            
            // 2. Hapus data dari waste_management (sama seperti approve)
            $this->wasteModel->delete($id);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                return ['success' => false, 'message' => 'Gagal menolak data waste'];
            }
            
            // Send notification to user (if notification system exists)
            $this->sendRejectionNotification($waste, $data['notes']);
            
            return ['success' => true, 'message' => 'Data waste berhasil ditolak'];

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Reject Waste Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()];
        }
    }

    public function getWasteDetail(int $id): array
    {
        try {
            $waste = $this->wasteModel->find($id);

            if (!$waste) {
                return ['success' => false, 'message' => 'Data waste tidak ditemukan'];
            }

            return ['success' => true, 'data' => $waste];

        } catch (\Exception $e) {
            log_message('error', 'Get Waste Detail Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem'];
        }
    }

    private function getPendingReviews(): array
    {
        $db = \Config\Database::connect();
        
        return $db->table('waste_management')
            ->select('waste_management.*, unit.nama_unit')
            ->join('unit', 'unit.id = waste_management.unit_id', 'left')
            ->whereIn('waste_management.status', ['dikirim', 'pending'])
            ->orderBy('waste_management.created_at', 'ASC')
            ->get()
            ->getResultArray();
    }

    private function getRecentReviews(): array
    {
        // Get from laporan_waste instead of waste_management
        $db = \Config\Database::connect();
        
        return $db->table('laporan_waste')
            ->select('laporan_waste.*, master_harga_sampah.jenis_sampah as kategori, unit.nama_unit, users.nama_lengkap as created_by_name, reviewer.nama_lengkap as reviewed_by_name')
            ->join('master_harga_sampah', 'master_harga_sampah.id = laporan_waste.kategori_id', 'left')
            ->join('unit', 'unit.id = laporan_waste.unit_id', 'left')
            ->join('users', 'users.id = laporan_waste.created_by', 'left')
            ->join('users as reviewer', 'reviewer.id = laporan_waste.reviewed_by', 'left')
            ->whereIn('laporan_waste.status', ['approved', 'rejected'])
            ->orderBy('laporan_waste.reviewed_at', 'DESC')
            ->limit(20)
            ->get()
            ->getResultArray();
    }

    private function getReviewStats(): array
    {
        $db = \Config\Database::connect();
        
        return [
            'pending_count' => $this->wasteModel->whereIn('status', ['dikirim', 'pending'])->countAllResults(),
            'approved_count' => $db->table('laporan_waste')->where('status', 'approved')->countAllResults(),
            'rejected_count' => $db->table('laporan_waste')->where('status', 'rejected')->countAllResults(),
            'total_count' => $this->wasteModel->countAllResults() + $db->table('laporan_waste')->countAllResults(),
            'today_reviewed' => $db->table('laporan_waste')
                ->whereIn('status', ['approved', 'rejected'])
                ->where('DATE(reviewed_at)', date('Y-m-d'))
                ->countAllResults()
        ];
    }

    private function getQueueSummary(): array
    {
        $today = date('Y-m-d');
        $urgentDate = date('Y-m-d', strtotime('-3 days'));

        return [
            'urgent_count' => $this->wasteModel
                ->whereIn('status', ['dikirim', 'pending'])
                ->where('DATE(created_at) <=', $urgentDate)
                ->countAllResults(),
            
            'today_count' => $this->wasteModel
                ->whereIn('status', ['dikirim', 'pending'])
                ->where('DATE(created_at)', $today)
                ->countAllResults(),
            
            'average_review_time' => $this->getAverageReviewTime()
        ];
    }

    private function getAverageReviewTime(): float
    {
        try {
            $db = \Config\Database::connect();
            
            $result = $db->table('laporan_waste')
                ->select('AVG(TIMESTAMPDIFF(HOUR, created_at, reviewed_at)) as avg_hours')
                ->whereIn('status', ['approved', 'rejected'])
                ->where('reviewed_at IS NOT NULL')
                ->where('DATE(reviewed_at) >=', date('Y-m-d', strtotime('-30 days')))
                ->get()
                ->getRow();

            return $result ? round($result->avg_hours, 1) : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function sendApprovalNotification(array $waste): void
    {
        // Implementation for sending approval notification
        // This would integrate with your notification system
        log_message('info', "Waste ID {$waste['id']} approved - notification should be sent to user {$waste['user_id']}");
    }

    private function sendRejectionNotification(array $waste, string $reason): void
    {
        // Implementation for sending rejection notification
        // This would integrate with your notification system
        log_message('info', "Waste ID {$waste['id']} rejected - notification should be sent to user {$waste['user_id']} with reason: {$reason}");
    }

    private function getDefaultStats(): array
    {
        return [
            'pending_count' => 0,
            'approved_count' => 0,
            'rejected_count' => 0,
            'total_count' => 0,
            'today_reviewed' => 0
        ];
    }
}