<?php

namespace App\Services\User;

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
            $unitId = $user['unit_id'];

            // Validate unit exists
            $unit = $this->unitModel->find($unitId);
            if (!$unit) {
                throw new \Exception('Unit tidak ditemukan');
            }

            return [
                'user' => $user,
                'unit' => $unit,
                'stats' => $this->getWasteStats($unitId),
                'wasteOverallStats' => $this->getWasteOverallStats($unitId),
                'wasteStats' => $this->getWasteStatsByType($unitId),
                'wasteManagementSummary' => $this->getWasteManagementSummary($unitId),
                'recent_activities' => $this->getRecentActivities($user['id'], $unitId),
                'feature_data' => $this->getFeatureData()
            ];
        } catch (\Exception $e) {
            log_message('error', 'User Dashboard Service Error: ' . $e->getMessage());
            
            return [
                'user' => session()->get('user'),
                'unit' => null,
                'stats' => $this->getDefaultStats(),
                'wasteOverallStats' => [],
                'wasteStats' => [],
                'wasteManagementSummary' => [],
                'recent_activities' => [],
                'feature_data' => []
            ];
        }
    }

    public function getApiStats(): array
    {
        try {
            $user = session()->get('user');
            $unitId = $user['unit_id'];

            return [
                'waste_stats' => $this->getWasteStats($unitId),
                'overall_stats' => $this->getOverallStats($unitId)
            ];
        } catch (\Exception $e) {
            log_message('error', 'User API Stats Error: ' . $e->getMessage());
            return $this->getDefaultStats();
        }
    }

    private function getWasteStats(int $unitId): array
    {
        $today = date('Y-m-d');
        $thisMonth = date('Y-m');

        return [
            'total_today' => $this->wasteModel
                ->where('unit_id', $unitId)
                ->where('DATE(created_at)', $today)
                ->countAllResults(),
            
            'total_month' => $this->wasteModel
                ->where('unit_id', $unitId)
                ->where('DATE_FORMAT(created_at, "%Y-%m")', $thisMonth)
                ->countAllResults(),
            
            'approved_count' => $this->wasteModel
                ->where('unit_id', $unitId)
                ->where('status', 'disetujui')
                ->countAllResults(),
            
            'pending_count' => $this->wasteModel
                ->where('unit_id', $unitId)
                ->whereIn('status', ['dikirim', 'review'])
                ->countAllResults(),
            
            'weight_today' => $this->wasteModel
                ->selectSum('berat_kg')
                ->where('unit_id', $unitId)
                ->where('DATE(created_at)', $today)
                ->get()
                ->getRow()
                ->berat_kg ?? 0,
            
            'weight_month' => $this->wasteModel
                ->selectSum('berat_kg')
                ->where('unit_id', $unitId)
                ->where('DATE_FORMAT(created_at, "%Y-%m")', $thisMonth)
                ->get()
                ->getRow()
                ->berat_kg ?? 0
        ];
    }

    private function getOverallStats(int $unitId): array
    {
        return [
            'total_entries' => $this->wasteModel->where('unit_id', $unitId)->countAllResults(),
            'total_weight' => $this->wasteModel
                ->selectSum('berat_kg')
                ->where('unit_id', $unitId)
                ->get()
                ->getRow()
                ->berat_kg ?? 0
        ];
    }

    private function getRecentActivities(int $userId, int $unitId): array
    {
        if (!isFeatureEnabled('dashboard_recent_activity', 'user')) {
            return [];
        }

        try {
            $maxItems = 10; // Increase limit to show more activities
            
            $recentWaste = $this->wasteModel
                ->select('waste_management.*, users.nama_lengkap as reviewer_name')
                ->join('users', 'users.id = waste_management.reviewed_by', 'left')
                ->where('waste_management.unit_id', $unitId)
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
    
    private function getWasteOverallStats(int $unitId): array
    {
        try {
            $db = \Config\Database::connect();
            
            // Get total berat
            $totalBeratQuery = $db->table('waste_management')
                ->selectSum('berat_kg')
                ->where('unit_id', $unitId)
                ->get()
                ->getRow();
            
            return [
                'disetujui' => $db->table('waste_management')
                    ->where('unit_id', $unitId)
                    ->where('status', 'disetujui')
                    ->countAllResults(),
                'ditolak' => $db->table('waste_management')
                    ->where('unit_id', $unitId)
                    ->where('status', 'ditolak')
                    ->countAllResults(),
                'menunggu_review' => $db->table('waste_management')
                    ->where('unit_id', $unitId)
                    ->where('status', 'dikirim')
                    ->countAllResults(),
                'draft' => $db->table('waste_management')
                    ->where('unit_id', $unitId)
                    ->where('status', 'draft')
                    ->countAllResults(),
                'total_berat' => $totalBeratQuery->berat_kg ?? 0
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error getting waste overall stats: ' . $e->getMessage());
            return ['disetujui' => 0, 'ditolak' => 0, 'menunggu_review' => 0, 'draft' => 0, 'total_berat' => 0];
        }
    }
    
    private function getWasteStatsByType(int $unitId): array
    {
        try {
            $db = \Config\Database::connect();
            
            $query = $db->query("
                SELECT 
                    jenis_sampah,
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'disetujui' THEN 1 ELSE 0 END) as disetujui,
                    SUM(CASE WHEN status = 'perlu_revisi' THEN 1 ELSE 0 END) as perlu_revisi,
                    SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft,
                    SUM(CASE WHEN status = 'dikirim' THEN 1 ELSE 0 END) as dikirim
                FROM waste_management
                WHERE unit_id = ?
                GROUP BY jenis_sampah
                ORDER BY total DESC
            ", [$unitId]);
            
            $results = $query->getResultArray();
            $stats = [];
            
            foreach ($results as $row) {
                $stats[$row['jenis_sampah']] = [
                    'total' => $row['total'],
                    'disetujui' => $row['disetujui'],
                    'perlu_revisi' => $row['perlu_revisi'],
                    'draft' => $row['draft'],
                    'dikirim' => $row['dikirim']
                ];
            }
            
            return $stats;
        } catch (\Exception $e) {
            log_message('error', 'Error getting waste stats by type: ' . $e->getMessage());
            return [];
        }
    }
    
    private function getWasteManagementSummary(int $unitId): array
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
            ", [$unitId]);
            
            return $query->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error getting waste management summary: ' . $e->getMessage());
            return [];
        }
    }

    private function getFeatureData(): array
    {
        $data = [];
        
        // Real-time updates
        if (isFeatureEnabled('real_time_updates', 'user')) {
            $data['real_time_enabled'] = true;
            $data['refresh_interval'] = 30; // seconds
        }
        
        // Export functionality
        if (isFeatureEnabled('export_data', 'user')) {
            $data['export_enabled'] = true;
        }
        
        return $data;
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
        $kategori = $waste['kategori'] ?? 'Sampah';
        $berat = $waste['berat_kg'] ?? 0;
        
        switch ($waste['status']) {
            case 'draft':
                return "Data {$kategori} {$berat}kg disimpan sebagai draft";
            case 'dikirim':
            case 'review':
                return "Data {$kategori} {$berat}kg dikirim untuk review";
            case 'disetujui':
                return "Data {$kategori} {$berat}kg disetujui";
            case 'ditolak':
            case 'perlu_revisi':
                return "Data {$kategori} {$berat}kg ditolak";
            default:
                return "Data {$kategori} {$berat}kg diperbarui";
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
            'total_today' => 0,
            'total_month' => 0,
            'approved_count' => 0,
            'pending_count' => 0,
            'weight_today' => 0,
            'weight_month' => 0
        ];
    }
}