<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\Admin\DashboardService;
use App\Models\TransportStatsModel;

class Dashboard extends BaseController
{
    protected $dashboardService;
    protected $transportStatsModel;

    public function __construct()
    {
        $this->dashboardService = new DashboardService();
        $this->transportStatsModel = new TransportStatsModel();
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
            
            // Get transportation data
            $transportationController = new Transportation();
            $transportData = $transportationController->getDashboardWidget();
            
            // Get infrastructure and population data
            $infrastructureController = new Infrastructure();
            $infrastructureData = $infrastructureController->getDashboardWidget();
            
            $viewData = [
                'title' => 'Dashboard Admin Pusat',
                'stats' => $data['stats'],
                'recentSubmissions' => $data['recentSubmissions'],
                'recentPriceChanges' => $data['recentPriceChanges'],
                'wasteByType' => $data['wasteByType'],
                'pager' => $data['pager'],
                'currentPage' => $page,
                'monthlySummary' => $data['monthlySummary'],
                'transportStats' => $transportData['summary_stats'],
                'recentTransportEntries' => $transportData['recent_entries'],
                'infrastructureStats' => $infrastructureData['infrastructure_stats'],
                'populationStats' => $infrastructureData['population_stats'],
                'uigmRatios' => $infrastructureData['uigm_ratios']
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
                'transportStats' => [
                    'total_vehicles' => 0,
                    'total_entries' => 0,
                    'total_officers' => 0
                ],
                'recentTransportEntries' => [],
                'infrastructureStats' => [
                    'luas_total_kampus' => 0,
                    'luas_area_parkir_total' => 0,
                    'parking_ratio' => 0,
                    'tahun_akademik' => 'N/A'
                ],
                'populationStats' => [
                    'total_populasi' => 0,
                    'jumlah_dosen' => 0,
                    'jumlah_mahasiswa' => 0,
                    'jumlah_tenaga_kependidikan' => 0,
                    'tahun_akademik' => 'N/A'
                ],
                'uigmRatios' => [
                    'parking_ratio' => 0,
                    'vehicle_population_ratio' => 0,
                    'zev_ratio' => 0,
                    'shuttle_ratio' => 0
                ],
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