<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\Admin\DashboardService;

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
            // Validasi session
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            // Get page number untuk waste by type pagination
            $page = $this->request->getGet('page') ?? 1;
            $perPage = 4; // 4 items per page

            // Get dashboard data melalui service
            $data = $this->dashboardService->getDashboardData($page, $perPage);
            
            $viewData = [
                'title' => 'Dashboard Admin Pusat',
                'stats' => $data['stats'],
                'recentSubmissions' => $data['recentSubmissions'],
                'recentPriceChanges' => $data['recentPriceChanges'],
                'wasteByType' => $data['wasteByType'],
                'pager' => $data['pager'],
                'currentPage' => $page,
                'monthlySummary' => $data['monthlySummary']
            ];

            return view('admin_pusat/dashboard', $viewData);

        } catch (\Exception $e) {
            log_message('error', 'Admin Dashboard Error: ' . $e->getMessage());
            
            return view('admin_pusat/dashboard', [
                'title' => 'Dashboard Admin Pusat',
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
                'currentPage' => 1,
                'monthlySummary' => [],
                'error' => 'Terjadi kesalahan saat memuat dashboard'
            ]);
        }
    }

    /**
     * Validasi session user
     */
    private function validateSession(): bool
    {
        $session = session();
        $user = $session->get('user');
        
        return $session->get('isLoggedIn') && 
               isset($user['id'], $user['role'], $user['unit_id']) &&
               in_array($user['role'], ['admin_pusat', 'super_admin']);
    }
}