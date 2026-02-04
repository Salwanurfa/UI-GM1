<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Services\User\DashboardService;

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
            
            $viewData = [
                'title' => 'Dashboard User - ' . ($data['unit']['nama_unit'] ?? 'User'),
                'user' => $data['user'],
                'unit' => $data['unit'],
                'stats' => $data['stats'],
                'wasteOverallStats' => $data['wasteOverallStats'] ?? [],
                'wasteStats' => $data['wasteStats'] ?? [],
                'wasteManagementSummary' => $data['wasteManagementSummary'] ?? [],
                'recentActivities' => $data['recent_activities'] ?? [],
                'featureData' => $data['feature_data'] ?? []
            ];

            return view('user/dashboard', $viewData);

        } catch (\Exception $e) {
            log_message('error', 'User Dashboard Error: ' . $e->getMessage());
            
            return view('user/dashboard', [
                'title' => 'Dashboard User',
                'user' => session()->get('user'),
                'unit' => null,
                'stats' => [],
                'wasteOverallStats' => [],
                'wasteStats' => [],
                'wasteManagementSummary' => [],
                'recentActivities' => [],
                'featureData' => [],
                'error' => 'Terjadi kesalahan saat memuat dashboard'
            ]);
        }
    }

    /**
     * API endpoint for dashboard stats (for real-time updates)
     */
    public function apiStats()
    {
        try {
            if (!$this->validateSession()) {
                return $this->response->setStatusCode(401)
                    ->setJSON(['success' => false, 'message' => 'Unauthorized']);
            }

            $data = $this->dashboardService->getApiStats();
            
            return $this->response->setJSON([
                'success' => true,
                'stats' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            log_message('error', 'User API Stats Error: ' . $e->getMessage());
            
            return $this->response->setStatusCode(500)
                ->setJSON(['success' => false, 'message' => 'Internal server error']);
        }
    }

    private function validateSession(): bool
    {
        $session = session();
        $user = $session->get('user');
        
        return $session->get('isLoggedIn') && 
               isset($user['id'], $user['role'], $user['unit_id']) &&
               $user['role'] === 'user';
    }
}