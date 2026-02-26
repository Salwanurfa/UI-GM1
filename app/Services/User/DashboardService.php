<?php

namespace App\Services\User;

use App\Models\WasteModel;
use App\Models\UnitModel;
use App\Models\LimbahB3Model;

class DashboardService
{
    protected $wasteModel;
    protected $unitModel;
    protected $limbahB3Model;

    public function __construct()
    {
        $this->wasteModel = new WasteModel();
        $this->unitModel = new UnitModel();
        $this->limbahB3Model = new LimbahB3Model();
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
                'limbah_b3_list' => $this->getLimbahB3List($user['id']),
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
                'limbah_b3_list' => [],
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
            $maxItems = 10;
            $db = \Config\Database::connect();
            
            // UNION query: gabungkan waste_management dan limbah_b3
            $sql = "
                SELECT 
                    'waste' AS type,
                    wm.id,
                    wm.created_at AS tanggal,
                    wm.jenis_sampah AS nama_item,
                    wm.berat_kg AS timbulan,
                    'kg' AS satuan,
                    wm.status,
                    wm.updated_at,
                    wm.nilai_rupiah,
                    wm.catatan_review,
                    u.nama_lengkap AS reviewer_name,
                    wm.reviewed_at AS tanggal_review
                FROM waste_management wm
                LEFT JOIN users u ON u.id = wm.reviewed_by
                WHERE wm.unit_id = ?
                
                UNION ALL
                
                SELECT 
                    'limbah_b3' AS type,
                    lb.id,
                    lb.tanggal_input AS tanggal,
                    mlb.nama_limbah AS nama_item,
                    lb.timbulan,
                    lb.satuan,
                    lb.status,
                    lb.tanggal_input AS updated_at,
                    NULL AS nilai_rupiah,
                    lb.keterangan AS catatan_review,
                    NULL AS reviewer_name,
                    lb.tanggal_input AS tanggal_review
                FROM limbah_b3 lb
                LEFT JOIN master_limbah_b3 mlb ON mlb.id = lb.master_b3_id
                WHERE lb.id_user = ?
                
                ORDER BY tanggal DESC
                LIMIT ?
            ";
            
            $query = $db->query($sql, [$unitId, $userId, $maxItems]);
            $results = $query->getResultArray();
            
            $activities = [];
            foreach ($results as $item) {
                $activities[] = [
                    'id' => $item['id'],
                    'type' => $item['type'],
                    'icon' => $this->getStatusIcon($item['status']),
                    'message' => $this->getActivityMessageMerged($item),
                    'time' => $this->timeAgo($item['tanggal']),
                    'status' => $item['status'],
                    'jenis_sampah' => $item['nama_item'] ?? 'N/A',
                    'berat_kg' => $item['timbulan'] ?? 0,
                    'nilai_rupiah' => $item['nilai_rupiah'] ?? 0,
                    'catatan_review' => $item['catatan_review'] ?? '',
                    'reviewer_name' => $item['reviewer_name'] ?? 'Admin',
                    'tanggal_review' => $item['tanggal_review'],
                    'has_detail' => in_array($item['status'], ['disetujui', 'ditolak', 'disetujui_tps', 'ditolak_tps'])
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

    /**
     * Message untuk activity gabungan (waste + limbah_b3)
     */
    private function getActivityMessageMerged(array $item): string
    {
        $nama = $item['nama_item'] ?? 'Data';
        $timbulan = $item['timbulan'] ?? 0;
        $satuan = $item['satuan'] ?? 'kg';
        
        switch ($item['status']) {
            case 'draft':
                return "Data {$nama} {$timbulan}{$satuan} disimpan sebagai draft";
            case 'dikirim':
            case 'dikirim_ke_tps':
                return "Data {$nama} {$timbulan}{$satuan} dikirim";
            case 'review':
                return "Data {$nama} {$timbulan}{$satuan} sedang dalam review";
            case 'disetujui':
            case 'disetujui_tps':
            case 'disetujui_admin':
                return "Data {$nama} {$timbulan}{$satuan} disetujui";
            case 'ditolak':
            case 'ditolak_tps':
                return "Data {$nama} {$timbulan}{$satuan} ditolak";
            case 'perlu_revisi':
                return "Data {$nama} {$timbulan}{$satuan} perlu revisi";
            default:
                return "Data {$nama} {$timbulan}{$satuan} diperbarui";
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

    /**
     * Ambil daftar Limbah B3 milik user untuk dashboard
     * Limit ke 10 records terbaru
     */
    private function getLimbahB3List(int $userId): array
    {
        try {
            return $this->limbahB3Model
                ->select('limbah_b3.*, master_limbah_b3.nama_limbah')
                ->join('master_limbah_b3', 'master_limbah_b3.id = limbah_b3.master_b3_id', 'left')
                ->where('limbah_b3.id_user', $userId)
                ->orderBy('limbah_b3.tanggal_input', 'DESC')
                ->limit(10)
                ->findAll();
        } catch (\Exception $e) {
            log_message('error', 'Error getting limbah B3 list: ' . $e->getMessage());
            return [];
        }
    }
}