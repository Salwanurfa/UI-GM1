<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\Admin\LaporanService;
use App\Services\ReportService;

class Laporan extends BaseController
{
    protected $laporanService;
    protected $reportService;

    public function __construct()
    {
        $this->laporanService = new LaporanService();
        $this->reportService = new ReportService();
    }

    public function index()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            $data = $this->laporanService->getLaporanData();
            
            // Get all units for filter dropdown
            $unitModel = new \App\Models\UnitModel();
            $allUnits = $unitModel->where('status_aktif', 1)->findAll();
            
            // Get filter parameters
            $filters = [
                'start_date' => $this->request->getGet('start_date') ?? date('Y-m-01'),
                'end_date' => $this->request->getGet('end_date') ?? date('Y-m-d'),
                'tahun' => $this->request->getGet('tahun') ?? date('Y'),
                'bulan' => $this->request->getGet('bulan') ?? date('m'),
                'unit_id' => $this->request->getGet('unit_id') ?? '',
                'unit' => $this->request->getGet('unit') ?? '',
                'kategori' => $this->request->getGet('kategori') ?? '',
                'status' => $this->request->getGet('status') ?? ''
            ];
            
            $viewData = [
                'title' => 'Laporan & Monitoring',
                'monthly_report' => $data['monthly_report'] ?? [],
                'yearly_report' => $data['yearly_report'] ?? [],
                'tps_report' => $data['tps_report'] ?? [],
                'summary_stats' => $data['summary_stats'] ?? [],
                'progressUnit' => $data['progress_unit'] ?? [],
                'wasteByType' => $data['waste_by_type'] ?? [],
                'rekapWaste' => $data['rekap_waste'] ?? [],
                'summary' => [
                    'total_data' => $data['summary_stats']['total_data'] ?? 0,
                    'disetujui' => $data['summary_stats']['disetujui'] ?? 0,
                    'pending' => $data['summary_stats']['pending'] ?? 0,
                    'menunggu_review' => $data['summary_stats']['menunggu_review'] ?? 0,
                    'ditolak' => $data['summary_stats']['ditolak'] ?? 0,
                    'perlu_revisi' => $data['summary_stats']['perlu_revisi'] ?? 0
                ],
                'allUnits' => $allUnits,
                'filters' => $filters
            ];

            return view('admin_pusat/laporan', $viewData);

        } catch (\Exception $e) {
            log_message('error', 'Admin Laporan Error: ' . $e->getMessage());
            
            // Get units even in error case
            try {
                $unitModel = new \App\Models\UnitModel();
                $allUnits = $unitModel->where('status_aktif', 1)->findAll();
            } catch (\Exception $unitError) {
                $allUnits = [];
            }
            
            return view('admin_pusat/laporan', [
                'title' => 'Laporan & Monitoring',
                'monthly_report' => [],
                'yearly_report' => [],
                'tps_report' => [],
                'summary_stats' => [],
                'progressUnit' => [],
                'wasteByType' => [],
                'rekapWaste' => [],
                'summary' => [
                    'total_data' => 0,
                    'disetujui' => 0,
                    'pending' => 0,
                    'menunggu_review' => 0,
                    'ditolak' => 0,
                    'perlu_revisi' => 0
                ],
                'allUnits' => $allUnits,
                'filters' => [
                    'start_date' => date('Y-m-01'),
                    'end_date' => date('Y-m-d'),
                    'tahun' => date('Y'),
                    'bulan' => date('m'),
                    'unit_id' => '',
                    'unit' => '',
                    'kategori' => '',
                    'status' => ''
                ],
                'error' => 'Terjadi kesalahan saat memuat laporan: ' . $e->getMessage()
            ]);
        }
    }

    public function export()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            if (!isFeatureEnabled('export_data', 'admin_pusat')) {
                return redirect()->back()->with('error', 'Fitur export tidak tersedia');
            }

            $result = $this->laporanService->exportLaporan();
            
            if ($result['success']) {
                return $this->response->download($result['file_path'], null)->setFileName($result['filename']);
            }

            return redirect()->back()->with('error', $result['message']);

        } catch (\Exception $e) {
            log_message('error', 'Admin Laporan Export Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat export laporan');
        }
    }

    /**
     * Rekap Sampah by Nama Sampah
     */
    public function rekapSampah()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            // Get nama sampah list for dropdown
            $namaSampahList = $this->reportService->getAllNamaSampah();

            // Default filters
            $filters = [
                'from' => $this->request->getGet('from') ?? date('Y-m-01'),
                'to' => $this->request->getGet('to') ?? date('Y-m-d'),
                'nama_sampah' => $this->request->getGet('nama_sampah') ?? '',
                'search' => $this->request->getGet('search') ?? ''
            ];

            $viewData = [
                'title' => 'Rekap Sampah',
                'namaSampahList' => $namaSampahList,
                'filters' => $filters
            ];

            return view('admin_pusat/laporan/rekap_sampah', $viewData);

        } catch (\Exception $e) {
            log_message('error', 'Admin Laporan rekapSampah Error: ' . $e->getMessage());
            
            return view('admin_pusat/laporan/rekap_sampah', [
                'title' => 'Rekap Sampah',
                'namaSampahList' => [],
                'filters' => [
                    'from' => date('Y-m-01'),
                    'to' => date('Y-m-d'),
                    'nama_sampah' => '',
                    'search' => ''
                ],
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Rekap Sampah Data (AJAX endpoint)
     */
    public function rekapSampahData()
    {
        try {
            if (!$this->validateSession()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Session tidak valid'
                ])->setStatusCode(401);
            }

            // Get filters from query params
            $filters = [
                'from' => $this->request->getGet('from'),
                'to' => $this->request->getGet('to'),
                'nama_sampah' => $this->request->getGet('nama_sampah'),
                'search' => $this->request->getGet('search')
            ];

            // Get page
            $page = (int)($this->request->getGet('page') ?? 1);
            if ($page < 1) $page = 1;

            // Get data from service (perPage fixed at 5)
            $result = $this->reportService->getRekapSampah($filters, $page, 5);

            return $this->response->setJSON([
                'success' => true,
                'rows' => $result['rows'],
                'total' => $result['total'],
                'page' => $result['page'],
                'perPage' => $result['perPage'],
                'totalPages' => $result['totalPages'],
                'aggregates' => $result['aggregates']
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Admin Laporan rekapSampahData Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat data',
                'error' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Rekap per Unit
     */
    public function rekapUnit()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            // Get all units for dropdown
            $unitModel = new \App\Models\UnitModel();
            $allUnits = $unitModel->where('status_aktif', 1)->findAll();

            // Default filters
            $filters = [
                'from' => $this->request->getGet('from') ?? date('Y-m-01'),
                'to' => $this->request->getGet('to') ?? date('Y-m-d'),
                'unit_id' => $this->request->getGet('unit_id') ?? '',
                'search' => $this->request->getGet('search') ?? ''
            ];

            $viewData = [
                'title' => 'Rekap per Unit',
                'allUnits' => $allUnits,
                'filters' => $filters
            ];

            return view('admin_pusat/laporan/rekap_unit', $viewData);

        } catch (\Exception $e) {
            log_message('error', 'Admin Laporan rekapUnit Error: ' . $e->getMessage());
            
            return view('admin_pusat/laporan/rekap_unit', [
                'title' => 'Rekap per Unit',
                'allUnits' => [],
                'filters' => [
                    'from' => date('Y-m-01'),
                    'to' => date('Y-m-d'),
                    'unit_id' => '',
                    'search' => ''
                ],
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Rekap Unit Data (AJAX endpoint)
     */
    public function rekapUnitData()
    {
        try {
            if (!$this->validateSession()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Session tidak valid'
                ])->setStatusCode(401);
            }

            // Get filters from query params
            $filters = [
                'from' => $this->request->getGet('from'),
                'to' => $this->request->getGet('to'),
                'unit_id' => $this->request->getGet('unit_id'),
                'search' => $this->request->getGet('search')
            ];

            // Get page
            $page = (int)($this->request->getGet('page') ?? 1);
            if ($page < 1) $page = 1;

            // Get data from service (perPage fixed at 5)
            $result = $this->reportService->getRekapUnit($filters, $page, 5);

            return $this->response->setJSON([
                'success' => true,
                'rows' => $result['rows'],
                'total' => $result['total'],
                'page' => $result['page'],
                'perPage' => $result['perPage'],
                'totalPages' => $result['totalPages'],
                'aggregates' => $result['aggregates']
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Admin Laporan rekapUnitData Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat data',
                'error' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Confirm Report (POST)
     */
    public function confirmReport($id)
    {
        try {
            if (!$this->validateSession()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Session tidak valid'
                ])->setStatusCode(401);
            }

            // Validate CSRF
            if (!$this->validate(['csrf_test_name' => 'required'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'CSRF token tidak valid'
                ])->setStatusCode(403);
            }

            // Get admin info from session
            $session = session();
            $user = $session->get('user');
            $adminId = $user['id'];
            $adminName = $user['nama_lengkap'] ?? $user['username'];

            // Confirm report via service
            $result = $this->reportService->confirmReport((int)$id, $adminId, $adminName);

            if ($result['success']) {
                return $this->response->setJSON($result);
            } else {
                return $this->response->setJSON($result)->setStatusCode(400);
            }

        } catch (\Exception $e) {
            log_message('error', 'Admin Laporan confirmReport Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat konfirmasi laporan',
                'error' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    private function validateSession(): bool
    {
        $session = session();
        $user = $session->get('user');
        
        return $session->get('isLoggedIn') && 
               isset($user['id'], $user['role'], $user['unit_id']) &&
               in_array($user['role'], ['admin_pusat', 'super_admin']);
    }
}