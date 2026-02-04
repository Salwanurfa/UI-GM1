<?php

namespace App\Controllers\TPS;

use App\Controllers\BaseController;
use App\Services\TPS\DashboardService;

class Dashboard extends BaseController
{
    protected $dashboardService;

    public function __construct()
    {
        $this->dashboardService = new DashboardService();
    }

    public function index()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            $data = $this->dashboardService->getDashboardData();
            
            // Ensure all required data is present
            if (!$data || !isset($data['stats'])) {
                throw new \Exception('Dashboard data incomplete');
            }
            
            $viewData = [
                'title' => 'Dashboard Pengelola TPS - ' . ($data['tps_info']['nama_unit'] ?? 'TPS'),
                'user' => $data['user'],
                'tps_info' => $data['tps_info'],
                'stats' => $data['stats'],
                'wasteOverallStats' => $data['wasteOverallStats'] ?? [],
                'wasteManagementSummary' => $data['wasteManagementSummary'] ?? [],
                'recent_waste' => $data['recent_waste'] ?? [],
                'monthly_summary' => $data['monthly_summary'] ?? [],
                'approved_data' => $data['approved_data'] ?? [],
                'rejected_data' => $data['rejected_data'] ?? [],
                'widgets' => []
            ];

            return view('pengelola_tps/dashboard', $viewData);

        } catch (\Exception $e) {
            log_message('error', 'TPS Dashboard Error: ' . $e->getMessage());
            
            return view('pengelola_tps/dashboard', [
                'title' => 'Dashboard Pengelola TPS',
                'user' => session()->get('user') ?? ['nama_lengkap' => 'User'],
                'tps_info' => ['nama_unit' => 'TPS'],
                'stats' => [
                    'total_waste_today' => 0,
                    'total_waste_month' => 0,
                    'total_weight_today' => 0,
                    'total_weight_month' => 0
                ],
                'wasteOverallStats' => [],
                'wasteManagementSummary' => [],
                'recent_waste' => [],
                'monthly_summary' => [],
                'widgets' => [],
                'approved_data' => [],
                'rejected_data' => [],
                'error' => 'Terjadi kesalahan saat memuat dashboard: ' . $e->getMessage()
            ]);
        }
    }

    private function validateSession(): bool
    {
        $session = session();
        $user = $session->get('user');
        
        return $session->get('isLoggedIn') && 
               isset($user['id'], $user['role'], $user['unit_id']) &&
               $user['role'] === 'pengelola_tps';
    }
}