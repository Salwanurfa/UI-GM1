<?php

namespace App\Services;

use App\Models\DashboardSettingModel;
use App\Models\WasteDataModel;
use App\Models\HargaSampahModel;

class DashboardService
{
    protected $dashboardSettingModel;
    protected $wasteDataModel;
    protected $hargaSampahModel;

    public function __construct()
    {
        try {
            $this->dashboardSettingModel = new DashboardSettingModel();
        } catch (\Exception $e) {
            log_message('error', 'DashboardService: Failed to load DashboardSettingModel - ' . $e->getMessage());
            $this->dashboardSettingModel = null;
        }
        
        try {
            $this->wasteDataModel = new WasteDataModel();
        } catch (\Exception $e) {
            log_message('error', 'DashboardService: Failed to load WasteDataModel - ' . $e->getMessage());
            $this->wasteDataModel = null;
        }
        
        try {
            $this->hargaSampahModel = new HargaSampahModel();
        } catch (\Exception $e) {
            log_message('error', 'DashboardService: Failed to load HargaSampahModel - ' . $e->getMessage());
            $this->hargaSampahModel = null;
        }
    }

    /**
     * Get Admin Dashboard Data
     */
    public function getAdminDashboardData(): array
    {
        try {
            $user = session()->get('user');

            return [
                'user' => $user,
                'stats' => $this->getAdminStats(),
                'charts' => $this->getAdminChartData(),
                'recent_activities' => $this->getAdminRecentActivities(),
                'notifications' => $this->getAdminNotifications()
            ];
        } catch (\Exception $e) {
            log_message('error', 'Admin Dashboard Service Error: ' . $e->getMessage());
            
            return [
                'user' => session()->get('user'),
                'stats' => $this->getDefaultAdminStats(),
                'charts' => [],
                'recent_activities' => [],
                'notifications' => []
            ];
        }
    }

    /**
     * Get User Dashboard Data
     */
    public function getUserDashboardData(): array
    {
        try {
            $user = session()->get('user');
            $unitId = $user['unit_id'];

            return [
                'user' => $user,
                'unit' => $this->getUnitInfo($unitId),
                'stats' => $this->getUserStats($unitId),
                'recent_activities' => $this->getUserRecentActivities($user['id']),
                'feature_data' => $this->getUserFeatureData()
            ];
        } catch (\Exception $e) {
            log_message('error', 'User Dashboard Service Error: ' . $e->getMessage());
            
            return [
                'user' => session()->get('user'),
                'unit' => null,
                'stats' => $this->getDefaultUserStats(),
                'recent_activities' => [],
                'feature_data' => []
            ];
        }
    }

    /**
     * Get TPS Dashboard Data
     */
    public function getTpsDashboardData(): array
    {
        try {
            $user = session()->get('user');
            $tpsId = $user['unit_id'];

            return [
                'user' => $user,
                'tps_info' => $this->getUnitInfo($tpsId),
                'stats' => $this->getTpsStats($tpsId),
                'recent_waste' => $this->getTpsRecentWaste($tpsId),
                'monthly_summary' => $this->getTpsMonthlySummary($tpsId)
            ];
        } catch (\Exception $e) {
            log_message('error', 'TPS Dashboard Service Error: ' . $e->getMessage());
            
            return [
                'user' => session()->get('user'),
                'tps_info' => null,
                'stats' => $this->getDefaultTpsStats(),
                'recent_waste' => [],
                'monthly_summary' => []
            ];
        }
    }

    /**
     * Get User API Stats
     */
    public function getUserApiStats(): array
    {
        try {
            $user = session()->get('user');
            $unitId = $user['unit_id'];

            return [
                'waste_stats' => $this->getUserStats($unitId),
                'overall_stats' => $this->getUserOverallStats($unitId)
            ];
        } catch (\Exception $e) {
            log_message('error', 'User API Stats Error: ' . $e->getMessage());
            return $this->getDefaultUserStats();
        }
    }

    // Helper methods for new dashboard functions
    private function getUnitInfo(int $unitId): ?array
    {
        // Mock implementation - replace with actual unit model
        return [
            'id' => $unitId,
            'nama_unit' => 'Unit ' . $unitId,
            'kode_unit' => 'U' . str_pad($unitId, 3, '0', STR_PAD_LEFT)
        ];
    }

    private function getAdminStats(): array
    {
        return [
            'total_waste' => 150,
            'total_users' => 25,
            'total_tps' => 5,
            'active_prices' => 8
        ];
    }

    private function getAdminChartData(): array
    {
        return [
            'monthly_waste' => [
                ['month' => 1, 'count' => 45],
                ['month' => 2, 'count' => 52],
                ['month' => 3, 'count' => 38]
            ]
        ];
    }

    private function getAdminRecentActivities(): array
    {
        return [
            [
                'user_name' => 'John Doe',
                'jenis_sampah' => 'Plastik',
                'jumlah_berat' => 5.5,
                'status' => 'dikirim',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))
            ]
        ];
    }

    private function getAdminNotifications(): array
    {
        return [];
    }

    private function getUserStats(int $unitId): array
    {
        return [
            'total_today' => 3,
            'total_month' => 25,
            'approved_count' => 18,
            'pending_count' => 7,
            'weight_today' => 12.5,
            'weight_month' => 145.8
        ];
    }

    private function getUserOverallStats(int $unitId): array
    {
        return [
            'total_entries' => 89,
            'total_weight' => 456.7
        ];
    }

    private function getUserRecentActivities(int $userId): array
    {
        if (!isFeatureEnabled('dashboard_recent_activity', 'user')) {
            return [];
        }

        return [
            [
                'icon' => 'check-circle',
                'message' => 'Data Plastik 5.5kg disetujui',
                'time' => '2 jam yang lalu'
            ],
            [
                'icon' => 'clock',
                'message' => 'Data Kertas 3.2kg dikirim untuk review',
                'time' => '4 jam yang lalu'
            ]
        ];
    }

    private function getUserFeatureData(): array
    {
        $data = [];
        
        if (isFeatureEnabled('real_time_updates', 'user')) {
            $data['real_time_enabled'] = true;
            $data['refresh_interval'] = 30;
        }
        
        if (isFeatureEnabled('export_data', 'user')) {
            $data['export_enabled'] = true;
        }
        
        return $data;
    }

    private function getTpsStats(int $tpsId): array
    {
        return [
            'total_waste_today' => 8,
            'total_waste_month' => 156,
            'total_weight_today' => 45.2,
            'total_weight_month' => 1250.8
        ];
    }

    private function getTpsRecentWaste(int $tpsId): array
    {
        return [
            [
                'kategori' => 'Plastik',
                'berat_kg' => 15.5,
                'harga_per_kg' => 2500,
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 hour'))
            ]
        ];
    }

    private function getTpsMonthlySummary(int $tpsId): array
    {
        return [
            ['month' => 1, 'count' => 45, 'total_weight' => 234.5],
            ['month' => 2, 'count' => 52, 'total_weight' => 287.3],
            ['month' => 3, 'count' => 38, 'total_weight' => 198.7]
        ];
    }

    private function getDefaultAdminStats(): array
    {
        return [
            'total_waste' => 0,
            'total_users' => 0,
            'total_tps' => 0,
            'active_prices' => 0
        ];
    }

    private function getDefaultUserStats(): array
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

    private function getDefaultTpsStats(): array
    {
        return [
            'total_waste_today' => 0,
            'total_waste_month' => 0,
            'total_weight_today' => 0,
            'total_weight_month' => 0
        ];
    }

    /**
     * Get data for specific widget
     */
    private function getWidgetData(string $widgetKey, string $role, int $userId = null, array $config = []): array
    {
        switch ($widgetKey) {
            case 'stat_cards':
                return $this->getStatCardsData($role, $userId, $config);
            
            case 'waste_summary':
                return $this->getWasteSummaryData($role, $userId, $config);
            
            case 'recent_activity':
                return $this->getRecentActivityData($role, $userId, $config);
            
            case 'quick_actions':
                return $this->getQuickActionsData($role, $config);
            
            case 'price_info':
                return $this->getPriceInfoData($config);
            
            case 'tps_operations':
                return $this->getTpsOperationsData($userId, $config);
            
            default:
                return [];
        }
    }

    /**
     * Get statistics cards data
     */
    private function getStatCardsData(string $role, int $userId = null, array $config = []): array
    {
        if (!$this->wasteDataModel) {
            return ['error' => 'WasteDataModel not available'];
        }
        
        try {
            $builder = $this->wasteDataModel->builder();
            
            if ($userId && $role !== 'admin_pusat') {
                $builder->where('user_id', $userId);
            }

            $stats = [
                'disetujui' => $builder->where('status', 'disetujui')->countAllResults(false),
                'dikirim' => $builder->where('status', 'dikirim')->countAllResults(false),
                'perlu_revisi' => $builder->where('status', 'perlu_revisi')->countAllResults(false),
                'draft' => $builder->where('status', 'draft')->countAllResults(false),
            ];

            // Filter based on configuration
            $filteredStats = [];
            if ($config['show_approved'] ?? true) {
                $filteredStats['disetujui'] = $stats['disetujui'];
            }
            if ($config['show_pending'] ?? true) {
                $filteredStats['dikirim'] = $stats['dikirim'];
            }
            if ($config['show_revision'] ?? true) {
                $filteredStats['perlu_revisi'] = $stats['perlu_revisi'];
            }
            if ($config['show_draft'] ?? true) {
                $filteredStats['draft'] = $stats['draft'];
            }

            return $filteredStats;
        } catch (\Exception $e) {
            log_message('error', 'DashboardService: getStatCardsData error - ' . $e->getMessage());
            return ['error' => 'Failed to load statistics'];
        }
    }

    /**
     * Get waste summary data
     */
    private function getWasteSummaryData(string $role, int $userId = null, array $config = []): array
    {
        if (!$this->wasteDataModel) {
            return ['error' => 'WasteDataModel not available'];
        }
        
        try {
            $builder = $this->wasteDataModel->builder();
            
            if ($userId && $role !== 'admin_pusat') {
                $builder->where('user_id', $userId);
            }

            // Fixed: Use correct column names from waste_tps table
            $wasteData = $builder->select('jenis_sampah, status, SUM(jumlah_berat) as total_berat, COUNT(*) as total_data')
                                ->groupBy(['jenis_sampah', 'status'])
                                ->get()
                                ->getResultArray();

            // Process data by waste type
            $wasteStats = [];
            foreach ($wasteData as $data) {
                $jenis = $data['jenis_sampah'];
                $status = $data['status'];
                
                if (!isset($wasteStats[$jenis])) {
                    $wasteStats[$jenis] = [
                        'total' => 0,
                        'disetujui' => 0,
                        'dikirim' => 0,
                        'perlu_revisi' => 0,
                        'draft' => 0,
                        'total_berat' => 0,
                        'total_nilai' => 0
                    ];
                }
                
                $wasteStats[$jenis]['total'] += $data['total_data'];
                $wasteStats[$jenis][$status] = $data['total_data'];
                $wasteStats[$jenis]['total_berat'] += $data['total_berat'];
            }

            // Calculate values if enabled
            if (($config['show_value_calculation'] ?? true) && $this->hargaSampahModel) {
                $hargaList = $this->hargaSampahModel->getActiveHarga();
                $hargaMap = [];
                foreach ($hargaList as $harga) {
                    $hargaMap[$harga['jenis_sampah']] = $harga['harga_per_kg'];
                }

                foreach ($wasteStats as $jenis => &$stats) {
                    if (isset($hargaMap[$jenis])) {
                        $stats['total_nilai'] = $stats['total_berat'] * $hargaMap[$jenis];
                        $stats['harga_per_kg'] = $hargaMap[$jenis];
                    }
                }
            }

            return [
                'waste_stats' => $wasteStats,
                'show_details' => $config['show_details'] ?? true,
                'show_value_calculation' => $config['show_value_calculation'] ?? true
            ];
        } catch (\Exception $e) {
            log_message('error', 'DashboardService: getWasteSummaryData error - ' . $e->getMessage());
            return ['error' => 'Failed to load waste summary'];
        }
    }

    /**
     * Get recent activity data
     */
    private function getRecentActivityData(string $role, int $userId = null, array $config = []): array
    {
        if (!$this->wasteDataModel) {
            return ['error' => 'WasteDataModel not available'];
        }
        
        try {
            $maxItems = $config['max_items'] ?? 5;
            
            $builder = $this->wasteDataModel->builder();
            
            if ($userId && $role !== 'admin_pusat') {
                $builder->where('user_id', $userId);
            }

            // Fixed: Use correct column names
            $recentData = $builder->select('jenis_sampah, jumlah_berat, status, created_at, updated_at')
                                 ->orderBy('updated_at', 'DESC')
                                 ->limit($maxItems)
                                 ->get()
                                 ->getResultArray();

            $activities = [];
            foreach ($recentData as $data) {
                $activities[] = [
                    'icon' => $this->getStatusIcon($data['status']),
                    'message' => $this->generateActivityMessage($data),
                    'time' => $this->timeAgo($data['updated_at']),
                    'status' => $data['status']
                ];
            }

            return $activities;
        } catch (\Exception $e) {
            log_message('error', 'DashboardService: getRecentActivityData error - ' . $e->getMessage());
            return ['error' => 'Failed to load recent activity'];
        }
    }

    /**
     * Get quick actions data
     */
    private function getQuickActionsData(string $role, array $config = []): array
    {
        $actions = [];

        if ($config['show_input_form'] ?? true) {
            $actions[] = [
                'title' => 'Input Data Sampah',
                'description' => 'Tambah data sampah baru',
                'icon' => 'fas fa-plus',
                'url' => base_url("/{$role}/waste/input"),
                'color' => 'primary'
            ];
        }

        if ($config['show_export'] ?? true) {
            $actions[] = [
                'title' => 'Export Data',
                'description' => 'Download laporan data',
                'icon' => 'fas fa-download',
                'url' => base_url("/{$role}/waste/export"),
                'color' => 'success'
            ];
        }

        if ($config['show_reports'] ?? true) {
            $actions[] = [
                'title' => 'Laporan',
                'description' => 'Lihat laporan lengkap',
                'icon' => 'fas fa-chart-line',
                'url' => base_url("/{$role}/reports"),
                'color' => 'info'
            ];
        }

        return $actions;
    }

    /**
     * Get price information data
     */
    private function getPriceInfoData(array $config = []): array
    {
        $data = [];

        if ($config['show_current_prices'] ?? true) {
            $data['current_prices'] = $this->hargaSampahModel->getActiveHarga();
        }

        if ($config['show_price_trends'] ?? false) {
            // Implement price trends logic here
            $data['price_trends'] = [];
        }

        return $data;
    }

    /**
     * Get TPS operations data
     */
    private function getTpsOperationsData(int $userId = null, array $config = []): array
    {
        $data = [];

        if ($config['show_capacity'] ?? true) {
            // Mock data - implement actual TPS capacity logic
            $data['capacity'] = [
                'current' => 75,
                'max' => 100,
                'unit' => 'ton'
            ];
        }

        if ($config['show_schedule'] ?? true) {
            // Mock data - implement actual schedule logic
            $data['schedule'] = [
                'next_pickup' => '2024-01-08 08:00:00',
                'frequency' => 'Harian'
            ];
        }

        return $data;
    }

    /**
     * Helper methods
     */
    private function getStatusIcon(string $status): string
    {
        $icons = [
            'disetujui' => 'check',
            'dikirim' => 'paper-plane',
            'perlu_revisi' => 'edit',
            'draft' => 'save'
        ];

        return $icons[$status] ?? 'circle';
    }

    private function generateActivityMessage(array $data): string
    {
        $statusText = [
            'disetujui' => 'disetujui',
            'dikirim' => 'dikirim untuk review',
            'perlu_revisi' => 'perlu revisi',
            'draft' => 'disimpan sebagai draft'
        ];

        // Fixed: Use jumlah_berat instead of berat
        $berat = $data['jumlah_berat'] ?? $data['berat'] ?? 0;
        return "Data {$data['jenis_sampah']} {$berat}kg {$statusText[$data['status']]}";
    }

    private function timeAgo(string $datetime): string
    {
        $time = time() - strtotime($datetime);

        if ($time < 60) return 'Baru saja';
        if ($time < 3600) return floor($time/60) . ' menit yang lalu';
        if ($time < 86400) return floor($time/3600) . ' jam yang lalu';
        if ($time < 2592000) return floor($time/86400) . ' hari yang lalu';
        if ($time < 31536000) return floor($time/2592000) . ' bulan yang lalu';
        
        return floor($time/31536000) . ' tahun yang lalu';
    }

    /**
     * Admin methods
     */
    public function getAllDashboardSettings(): array
    {
        return $this->dashboardSettingModel->getAllSettingsGroupedByRole();
    }

    public function updateWidgetSettings(int $id, array $data): bool
    {
        return $this->dashboardSettingModel->updateWidgetConfig($id, $data);
    }

    public function toggleWidget(int $id): bool
    {
        return $this->dashboardSettingModel->toggleWidget($id);
    }

    public function updateWidgetOrder(string $role, array $orders): bool
    {
        return $this->dashboardSettingModel->updateWidgetOrder($role, $orders);
    }

    public function getAvailableWidgets(): array
    {
        return $this->dashboardSettingModel->getAvailableWidgetTypes();
    }

    public function resetDashboardSettings(string $role): bool
    {
        return $this->dashboardSettingModel->resetToDefault($role);
    }
}