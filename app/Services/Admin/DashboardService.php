<?php

namespace App\Services\Admin;

use App\Models\HargaSampahModel;
use App\Models\ChangeLogModel;
use App\Models\WasteModel;
use App\Models\UserModel;

class DashboardService
{
    protected $hargaModel;
    protected $changeLogModel;
    protected $wasteModel;
    protected $userModel;
    protected $hargaLogModel;

    public function __construct()
    {
        $this->hargaModel = new HargaSampahModel();
        $this->changeLogModel = new ChangeLogModel();
        $this->wasteModel = new WasteModel();
        $this->userModel = new UserModel();
        
        // Load HargaLogModel if exists
        if (class_exists('\App\Models\HargaLogModel')) {
            $this->hargaLogModel = new \App\Models\HargaLogModel();
        }
    }

    /**
     * Get Dashboard Data (Main method for Admin Dashboard Controller)
     * Returns all data needed for admin dashboard in one call
     * 
     * @param int $page Current page for waste by type pagination
     * @param int $perPage Items per page
     * @return array
     */
    public function getDashboardData(int $page = 1, int $perPage = 4): array
    {
        try {
            // 1. Get main statistics
            $stats = $this->getMainStatistics();
            
            // 2. Get recent submissions (pending review)
            $recentSubmissions = $this->getRecentSubmissions(5);
            
            // 3. Get recent price changes
            $recentPriceChanges = $this->getRecentPriceChanges(5);
            
            // 4. Get waste by type with pagination
            $wasteByTypeData = $this->getWasteByTypePaginated($page, $perPage);

            // 5. Get monthly summary
            $monthlySummary = $this->getMonthlySummary();

            return [
                'stats' => $stats,
                'recentSubmissions' => $recentSubmissions,
                'recentPriceChanges' => $recentPriceChanges,
                'wasteByType' => $wasteByTypeData['data'],
                'pager' => $wasteByTypeData['pager'],
                'totalPages' => $wasteByTypeData['totalPages'],
                'totalItems' => $wasteByTypeData['totalItems'],
                'monthlySummary' => $monthlySummary
            ];

        } catch (\Exception $e) {
            log_message('error', 'DashboardService getDashboardData error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            // Return safe fallback data
            return [
                'stats' => [
                    'total_users' => 0,
                    'menunggu_review' => 0,
                    'disetujui' => 0,
                    'perlu_revisi' => 0,
                    'total_berat' => 0,
                    'total_nilai' => 0
                ],
                'recentSubmissions' => [],
                'recentPriceChanges' => [],
                'wasteByType' => [],
                'pager' => null,
                'totalPages' => 0,
                'totalItems' => 0,
                'monthlySummary' => []
            ];
        }
    }

    /**
     * Get main statistics for dashboard
     * 
     * @return array
     */
    private function getMainStatistics(): array
    {
        try {
            $db = \Config\Database::connect();
            
            // Count users
            $totalUsers = $this->userModel->countAllResults(false);
            
            // Count approved data from waste_management
            $disetujui = $this->wasteModel->where('status', 'disetujui')->countAllResults(false);
            
            // Count rejected data from laporan_waste table
            $ditolak = $db->table('laporan_waste')
                ->where('status', 'rejected')
                ->countAllResults();
            
            // Calculate total value from approved waste (kategori bisa_dijual only)
            $totalNilaiQuery = $db->table('waste_management')
                ->selectSum('nilai_rupiah')
                ->where('status', 'disetujui')
                ->where('kategori_sampah', 'bisa_dijual')
                ->get()
                ->getRow();
            
            $totalNilai = $totalNilaiQuery->nilai_rupiah ?? 0;

            return [
                'total_users' => $totalUsers,
                'disetujui' => $disetujui,
                'ditolak' => $ditolak,
                'total_nilai' => (float)$totalNilai
            ];

        } catch (\Exception $e) {
            log_message('error', 'DashboardService getMainStatistics error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return [
                'total_users' => 0,
                'disetujui' => 0,
                'ditolak' => 0,
                'total_nilai' => 0
            ];
        }
    }

    /**
     * Get recent submissions (pending review)
     * Only show data with status 'dikirim' OR data that was approved/rejected less than 2 days ago
     * 
     * @param int $limit Number of items to retrieve
     * @return array
     */
    private function getRecentSubmissions(int $limit = 5): array
    {
        try {
            $submissions = $this->wasteModel
                ->select('waste_management.*, users.nama_lengkap as user_name, users.username, users.role, units.nama_unit')
                ->join('users', 'users.id = waste_management.user_id', 'left')
                ->join('units', 'units.id = waste_management.unit_id', 'left')
                ->groupStart()
                    ->where('waste_management.status', 'dikirim')
                    ->orGroupStart()
                        ->whereIn('waste_management.status', ['disetujui', 'ditolak'])
                        ->where('waste_management.action_timestamp IS NOT NULL')
                        ->where('waste_management.action_timestamp >=', date('Y-m-d H:i:s', strtotime('-2 days')))
                    ->groupEnd()
                ->groupEnd()
                ->orderBy('waste_management.created_at', 'DESC')
                ->limit($limit)
                ->findAll();

            return $submissions;

        } catch (\Exception $e) {
            log_message('error', 'DashboardService getRecentSubmissions error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get recent price changes
     * 
     * @param int $limit Number of items to retrieve
     * @return array
     */
    private function getRecentPriceChanges(int $limit = 5): array
    {
        try {
            // Try to get from ChangeLogModel first (new system)
            if ($this->changeLogModel) {
                $changes = $this->changeLogModel->getByEntity('harga_sampah', null, $limit);
                if (!empty($changes)) {
                    return $changes;
                }
            }
            
            // Fallback to HargaLogModel (old system)
            if ($this->hargaLogModel && method_exists($this->hargaLogModel, 'getRecentChanges')) {
                $changes = $this->hargaLogModel->getRecentChanges($limit);
                return $changes;
            }

            return [];

        } catch (\Exception $e) {
            log_message('error', 'DashboardService getRecentPriceChanges error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get waste by type with pagination
     * 
     * @param int $page Current page
     * @param int $perPage Items per page
     * @return array
     */
    private function getWasteByTypePaginated(int $page = 1, int $perPage = 4): array
    {
        try {
            // Get waste type summary with aggregation
            $query = $this->wasteModel
                ->select('jenis_sampah, COUNT(*) as jumlah_data, SUM(berat_kg) as total_berat, SUM(nilai_rupiah) as total_nilai')
                ->where('status', 'disetujui')
                ->groupBy('jenis_sampah')
                ->orderBy('total_berat', 'DESC');
            
            // Get total count for pagination
            $totalItems = count($query->findAll());
            $totalPages = ceil($totalItems / $perPage);
            
            // Apply pagination
            $offset = ($page - 1) * $perPage;
            $data = $query->limit($perPage, $offset)->findAll();
            
            // Create simple pager object
            $pager = null;
            if ($totalPages > 1) {
                $pager = [
                    'currentPage' => $page,
                    'totalPages' => $totalPages,
                    'perPage' => $perPage,
                    'totalItems' => $totalItems,
                    'hasMore' => $page < $totalPages,
                    'hasPrevious' => $page > 1
                ];
            }

            return [
                'data' => $data,
                'pager' => $pager,
                'totalPages' => $totalPages,
                'totalItems' => $totalItems
            ];

        } catch (\Exception $e) {
            log_message('error', 'DashboardService getWasteByTypePaginated error: ' . $e->getMessage());
            
            return [
                'data' => [],
                'pager' => null,
                'totalPages' => 0,
                'totalItems' => 0
            ];
        }
    }

    /**
     * Get Manajemen Sampah Statistics (single call)
     * Returns all stats needed for the dashboard in one array
     * 
     * @return array
     */
    public function getManajemenSampahStats(): array
    {
        try {
            // Get all statistics in one go
            $totalJenis = $this->hargaModel->countAllTypes();
            $hargaAktif = $this->hargaModel->countActivePrices();
            $bisaDijual = $this->hargaModel->countSellable();
            
            // Use HargaLogModel instead of ChangeLogModel (change_logs table doesn't exist)
            $recentChanges = [];
            $changeCount = 0;
            $todayCount = 0;
            
            if ($this->hargaLogModel && method_exists($this->hargaLogModel, 'getRecentChanges')) {
                $recentChanges = $this->hargaLogModel->getRecentChanges(5);
                $changeCount = count($recentChanges);
                
                // Count today's changes
                $today = date('Y-m-d');
                $todayCount = 0;
                foreach ($recentChanges as $change) {
                    if (date('Y-m-d', strtotime($change['created_at'])) === $today) {
                        $todayCount++;
                    }
                }
            }

            return [
                'total_jenis_sampah' => $totalJenis,
                'harga_aktif' => $hargaAktif,
                'bisa_dijual' => $bisaDijual,
                'perubahan_count' => $todayCount,
                'perubahan_total' => $changeCount,
                'recent_changes' => $recentChanges,
                'recent_changes_count' => $changeCount
            ];

        } catch (\Exception $e) {
            log_message('error', 'DashboardService getManajemenSampahStats error: ' . $e->getMessage());
            
            return [
                'total_jenis_sampah' => 0,
                'harga_aktif' => 0,
                'bisa_dijual' => 0,
                'perubahan_count' => 0,
                'perubahan_total' => 0,
                'recent_changes' => [],
                'recent_changes_count' => 0
            ];
        }
    }

    /**
     * Get main dashboard statistics
     * 
     * @return array
     */
    public function getDashboardStats(): array
    {
        try {
            // Get waste submissions statistics
            $totalSubmissions = $this->wasteModel->countAllResults(false);
            $pendingSubmissions = $this->wasteModel->where('status', 'dikirim')->countAllResults(false);
            $approvedSubmissions = $this->wasteModel->where('status', 'disetujui')->countAllResults(false);
            
            // Get user statistics
            $totalUsers = $this->userModel->countAllResults(false);
            $activeUsers = $this->userModel->where('status', 'active')->countAllResults(false);
            
            // Get recent submissions
            $recentSubmissions = $this->wasteModel
                ->select('waste_management.*, users.nama_lengkap as user_name, users.username')
                ->join('users', 'users.id = waste_management.user_id', 'left')
                ->where('waste_management.status', 'dikirim')
                ->orderBy('waste_management.created_at', 'DESC')
                ->limit(5)
                ->findAll();

            return [
                'total_submissions' => $totalSubmissions,
                'pending_submissions' => $pendingSubmissions,
                'approved_submissions' => $approvedSubmissions,
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'recent_submissions' => $recentSubmissions
            ];

        } catch (\Exception $e) {
            log_message('error', 'DashboardService getDashboardStats error: ' . $e->getMessage());
            
            return [
                'total_submissions' => 0,
                'pending_submissions' => 0,
                'approved_submissions' => 0,
                'total_users' => 0,
                'active_users' => 0,
                'recent_submissions' => []
            ];
        }
    }

    /**
     * Get waste type distribution
     * 
     * @return array
     */
    public function getWasteTypeDistribution(): array
    {
        try {
            $results = $this->wasteModel
                ->select('jenis_sampah, COUNT(*) as count, SUM(berat) as total_berat')
                ->where('status', 'disetujui')
                ->groupBy('jenis_sampah')
                ->orderBy('count', 'DESC')
                ->findAll();

            return $results;

        } catch (\Exception $e) {
            log_message('error', 'DashboardService getWasteTypeDistribution error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get monthly waste statistics
     * 
     * @param int $months Number of months to retrieve
     * @return array
     */
    public function getMonthlyWasteStats(int $months = 6): array
    {
        try {
            $results = $this->wasteModel
                ->select('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count, SUM(berat) as total_berat')
                ->where('status', 'disetujui')
                ->where('created_at >=', date('Y-m-d', strtotime("-{$months} months")))
                ->groupBy('month')
                ->orderBy('month', 'ASC')
                ->findAll();

            return $results;

        } catch (\Exception $e) {
            log_message('error', 'DashboardService getMonthlyWasteStats error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get monthly summary for current year (12 months grid)
     * Returns data count and total weight per month
     * 
     * @return array
     */
    public function getMonthlySummary(): array
    {
        try {
            $db = \Config\Database::connect();
            $currentYear = date('Y');
            
            // Get monthly data from laporan_waste (approved data)
            $query = $db->table('laporan_waste')
                ->select('MONTH(tanggal_input) as month, COUNT(*) as data_count, SUM(berat_kg) as total_weight')
                ->where('YEAR(tanggal_input)', $currentYear)
                ->where('status', 'approved')
                ->groupBy('MONTH(tanggal_input)')
                ->get();
            
            $results = $query->getResultArray();
            
            // Initialize 12 months with zero values
            $monthlySummary = [];
            for ($i = 1; $i <= 12; $i++) {
                $monthlySummary[$i] = [
                    'month' => $i,
                    'month_name' => date('M', mktime(0, 0, 0, $i, 1)),
                    'data_count' => 0,
                    'total_weight' => 0
                ];
            }
            
            // Fill in actual data
            foreach ($results as $row) {
                $month = (int)$row['month'];
                $monthlySummary[$month] = [
                    'month' => $month,
                    'month_name' => date('M', mktime(0, 0, 0, $month, 1)),
                    'data_count' => (int)$row['data_count'],
                    'total_weight' => (float)$row['total_weight']
                ];
            }
            
            return array_values($monthlySummary);

        } catch (\Exception $e) {
            log_message('error', 'DashboardService getMonthlySummary error: ' . $e->getMessage());
            
            // Return empty 12 months
            $monthlySummary = [];
            for ($i = 1; $i <= 12; $i++) {
                $monthlySummary[] = [
                    'month' => $i,
                    'month_name' => date('M', mktime(0, 0, 0, $i, 1)),
                    'data_count' => 0,
                    'total_weight' => 0
                ];
            }
            return $monthlySummary;
        }
    }
}

