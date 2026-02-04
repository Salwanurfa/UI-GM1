<?php

namespace App\Services\TPS;

use App\Models\WasteModel;
use App\Models\UnitModel;

class DashboardService
{
    protected $wasteModel;
    protected $unitModel;

    public function __construct()
    {
        $this->wasteModel = new WasteModel();
        $this->unitModel = new UnitModel();
    }

    public function getDashboardData(): array
    {
        try {
            $user = session()->get('user');
            
            // Validate user session
            if (!$user || !isset($user['unit_id'])) {
                throw new \Exception('Invalid user session');
            }
            
            $tpsId = $user['unit_id'];

            // Get TPS info
            $tpsInfo = $this->unitModel->find($tpsId);
            if (!$tpsInfo) {
                throw new \Exception('TPS tidak ditemukan');
            }

            return [
                'user' => $user,
                'tps_info' => $tpsInfo,
                'stats' => $this->getStats($tpsId),
                'wasteOverallStats' => $this->getWasteOverallStats($tpsId),
                'wasteManagementSummary' => $this->getWasteManagementSummary($tpsId),
                'recent_waste' => $this->getRecentWaste($tpsId),
                'recent_activities' => $this->getRecentActivities($user['id'], $tpsId),
                'monthly_summary' => $this->getMonthlySummary($tpsId),
                'approved_data' => $this->getApprovedData($tpsId),
                'rejected_data' => $this->getRejectedData($tpsId)
            ];
        } catch (\Exception $e) {
            log_message('error', 'TPS Dashboard Service Error: ' . $e->getMessage());
            
            $user = session()->get('user');
            
            return [
                'user' => $user,
                'tps_info' => [
                    'nama_unit' => $user['nama_unit'] ?? 'TPS',
                    'id' => $user['unit_id'] ?? 0
                ],
                'stats' => $this->getDefaultStats(),
                'wasteOverallStats' => [],
                'wasteManagementSummary' => [],
                'recent_waste' => [],
                'monthly_summary' => [],
                'approved_data' => [],
                'rejected_data' => []
            ];
        }
    }

    private function getApprovedData(int $tpsId): array
    {
        try {
            $db = \Config\Database::connect();
            
            // Get data yang sudah disetujui oleh TPS
            return $db->table('waste_management')
                ->select('waste_management.*, 
                         unit.nama_unit as unit_nama, 
                         users.nama_lengkap as user_nama')
                ->join('unit', 'unit.id = waste_management.unit_id', 'left')
                ->join('users', 'users.id = waste_management.user_id', 'left')
                ->where('waste_management.status', 'disetujui_tps')
                ->where('waste_management.tps_reviewed_by', session()->get('user')['id'])
                ->orderBy('waste_management.tps_reviewed_at', 'DESC')
                ->limit(20)
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error getting approved data: ' . $e->getMessage());
            return [];
        }
    }

    private function getRejectedData(int $tpsId): array
    {
        try {
            $db = \Config\Database::connect();
            
            // Get data yang ditolak oleh TPS
            return $db->table('waste_management')
                ->select('waste_management.*, 
                         unit.nama_unit as unit_nama, 
                         users.nama_lengkap as user_nama')
                ->join('unit', 'unit.id = waste_management.unit_id', 'left')
                ->join('users', 'users.id = waste_management.user_id', 'left')
                ->where('waste_management.status', 'ditolak_tps')
                ->where('waste_management.tps_reviewed_by', session()->get('user')['id'])
                ->orderBy('waste_management.tps_reviewed_at', 'DESC')
                ->limit(20)
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error getting rejected data: ' . $e->getMessage());
            return [];
        }
    }

    private function getStats(int $tpsId): array
    {
        $today = date('Y-m-d');
        $thisMonth = date('Y-m');

        try {
            return [
                'total_waste_today' => $this->wasteModel
                    ->where('unit_id', $tpsId)
                    ->where('DATE(created_at)', $today)
                    ->countAllResults(),
                
                'total_waste_month' => $this->wasteModel
                    ->where('unit_id', $tpsId)
                    ->where('DATE_FORMAT(created_at, "%Y-%m")', $thisMonth)
                    ->countAllResults(),
                
                'total_weight_today' => $this->wasteModel
                    ->selectSum('berat_kg')
                    ->where('unit_id', $tpsId)
                    ->where('DATE(created_at)', $today)
                    ->get()
                    ->getRow()
                    ->berat_kg ?? 0,
                
                'total_weight_month' => $this->wasteModel
                    ->selectSum('berat_kg')
                    ->where('unit_id', $tpsId)
                    ->where('DATE_FORMAT(created_at, "%Y-%m")', $thisMonth)
                    ->get()
                    ->getRow()
                    ->berat_kg ?? 0
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error getting stats: ' . $e->getMessage());
            return $this->getDefaultStats();
        }
    }

    private function getRecentWaste(int $tpsId): array
    {
        try {
            return $this->wasteModel
                ->where('waste_management.unit_id', $tpsId)
                ->orderBy('waste_management.created_at', 'DESC')
                ->limit(10)
                ->findAll();
        } catch (\Exception $e) {
            log_message('error', 'Error getting recent waste: ' . $e->getMessage());
            return [];
        }
    }

    private function getMonthlySummary(int $tpsId): array
    {
        $currentYear = date('Y');
        
        try {
            return $this->wasteModel
                ->select('MONTH(created_at) as month, COUNT(*) as count, SUM(berat_kg) as total_weight')
                ->where('unit_id', $tpsId)
                ->where('YEAR(created_at)', $currentYear)
                ->groupBy('MONTH(created_at)')
                ->orderBy('MONTH(created_at)', 'ASC')
                ->findAll();
        } catch (\Exception $e) {
            log_message('error', 'Error getting monthly summary: ' . $e->getMessage());
            return [];
        }
    }

    private function getRecentActivities(int $userId, int $tpsId): array
    {
        if (!isFeatureEnabled('dashboard_recent_activity', 'pengelola_tps')) {
            return [];
        }

        try {
            $maxItems = 10;
            
            $recentWaste = $this->wasteModel
                ->select('waste_management.*, users.nama_lengkap as reviewer_name')
                ->join('users', 'users.id = waste_management.reviewed_by', 'left')
                ->where('waste_management.unit_id', $tpsId)
                ->orderBy('waste_management.updated_at', 'DESC')
                ->limit($maxItems)
                ->findAll();
            
            $activities = [];
            foreach ($recentWaste as $waste) {
                $activities[] = [
                    'id' => $waste['id'],
                    'icon' => $this->getStatusIcon($waste['status']),
                    'message' => $this->getActivityMessage($waste),
                    'time' => $this->timeAgo($waste['updated_at']),
                    'status' => $waste['status'],
                    'jenis_sampah' => $waste['jenis_sampah'] ?? 'N/A',
                    'berat_kg' => $waste['berat_kg'] ?? 0,
                    'nilai_rupiah' => $waste['nilai_rupiah'] ?? 0,
                    'catatan_review' => $waste['catatan_review'] ?? '',
                    'reviewer_name' => $waste['reviewer_name'] ?? 'Admin',
                    'tanggal_review' => $waste['reviewed_at'] ?? $waste['updated_at'],
                    'has_detail' => in_array($waste['status'], ['disetujui', 'ditolak'])
                ];
            }
            
            return $activities;
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting recent activities: ' . $e->getMessage());
            return [];
        }
    }

    private function getStatusIcon(string $status): string
    {
        switch ($status) {
            case 'draft':
                return 'edit';
            case 'dikirim':
            case 'review':
                return 'clock';
            case 'disetujui':
                return 'check-circle';
            case 'ditolak':
            case 'perlu_revisi':
                return 'x-circle';
            default:
                return 'circle';
        }
    }

    private function getActivityMessage(array $waste): string
    {
        $kategori = $waste['jenis_sampah'] ?? 'Sampah';
        $berat = $waste['berat_kg'] ?? 0;
        
        switch ($waste['status']) {
            case 'draft':
                return "Data Sampah {$berat}kg disimpan sebagai draft";
            case 'dikirim':
            case 'review':
                return "Data Sampah {$berat}kg dikirim untuk review";
            case 'disetujui':
                return "Data Sampah {$berat}kg disetujui";
            case 'ditolak':
            case 'perlu_revisi':
                return "Data Sampah {$berat}kg ditolak";
            default:
                return "Data Sampah {$berat}kg diperbarui";
        }
    }

    private function timeAgo(string $datetime): string
    {
        $time = time() - strtotime($datetime);
        
        if ($time < 60) return 'Baru saja';
        if ($time < 3600) return floor($time/60) . ' menit yang lalu';
        if ($time < 86400) return floor($time/3600) . ' jam yang lalu';
        if ($time < 2592000) return floor($time/86400) . ' hari yang lalu';
        
        return date('d/m/Y', strtotime($datetime));
    }

    private function getDefaultStats(): array
    {
        return [
            'total_waste_today' => 0,
            'total_waste_month' => 0,
            'total_weight_today' => 0,
            'total_weight_month' => 0
        ];
    }
    
    private function getWasteOverallStats(int $tpsId): array
    {
        try {
            $db = \Config\Database::connect();
            
            // Get total berat
            $totalBeratQuery = $db->table('waste_management')
                ->selectSum('berat_kg')
                ->where('unit_id', $tpsId)
                ->get()
                ->getRow();
            
            return [
                'disetujui' => $db->table('waste_management')
                    ->where('unit_id', $tpsId)
                    ->where('status', 'disetujui')
                    ->countAllResults(),
                'ditolak' => $db->table('laporan_waste')
                    ->where('unit_id', $tpsId)
                    ->where('status', 'rejected')
                    ->countAllResults(),
                'menunggu_review' => $db->table('waste_management')
                    ->where('unit_id', $tpsId)
                    ->where('status', 'dikirim')
                    ->countAllResults(),
                'draft' => $db->table('waste_management')
                    ->where('unit_id', $tpsId)
                    ->where('status', 'draft')
                    ->countAllResults(),
                'total_berat' => $totalBeratQuery->berat_kg ?? 0
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error getting waste overall stats: ' . $e->getMessage());
            return ['disetujui' => 0, 'ditolak' => 0, 'menunggu_review' => 0, 'draft' => 0, 'total_berat' => 0];
        }
    }
    
    private function getWasteManagementSummary(int $tpsId): array
    {
        try {
            $db = \Config\Database::connect();
            
            // Get recent waste data (read-only, no CRUD)
            $query = $db->query("
                SELECT 
                    wm.*,
                    u.nama_unit
                FROM waste_management wm
                LEFT JOIN unit u ON u.id = wm.unit_id
                WHERE wm.unit_id = ?
                ORDER BY wm.created_at DESC
                LIMIT 10
            ", [$tpsId]);
            
            return $query->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error getting waste management summary: ' . $e->getMessage());
            return [];
        }
    }
}