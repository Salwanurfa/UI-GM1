<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\Admin\LaporanWasteService;

class LaporanWaste extends BaseController
{
    protected $laporanService;

    public function __construct()
    {
        $this->laporanService = new LaporanWasteService();
    }

    public function index()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            // Get filter parameters
            $filters = [
                'start_date' => $this->request->getGet('start_date'),
                'end_date' => $this->request->getGet('end_date'),
                'status' => $this->request->getGet('status'),
                'unit_id' => $this->request->getGet('unit_id'),
                'filter_bulan' => $this->request->getGet('filter_bulan'),
                'filter_tahun' => $this->request->getGet('filter_tahun'),
                'filter_minggu' => $this->request->getGet('filter_minggu'),
                'filter_gedung' => $this->request->getGet('filter_gedung'),
                'filter_pelapor' => $this->request->getGet('filter_pelapor')
            ];
            
            // Get page for each section
            $section = $this->request->getGet('section') ?? 'disetujui';
            $page = (int)($this->request->getGet('page') ?? 1);
            
            // Set page untuk section yang aktif, section lain page 1
            $pages = [
                'disetujui' => $section === 'disetujui' ? $page : 1,
                'ditolak' => $section === 'ditolak' ? $page : 1,
                'rekap_jenis' => $section === 'rekap_jenis' ? $page : 1,
                'rekap_unit' => $section === 'rekap_unit' ? $page : 1,
                'detail_rekap' => $section === 'detail_rekap' ? $page : 1
            ];

            $data = $this->laporanService->getLaporanData($filters, $pages, 10);
            
            $viewData = [
                'title' => 'Laporan Waste',
                'rekap_jenis' => $data['rekap_jenis'],
                'rekap_unit' => $data['rekap_unit'],
                'detail_rekap' => $data['detail_rekap'],
                'data_disetujui' => $data['data_disetujui'],
                'data_ditolak' => $data['data_ditolak'],
                'units' => $data['units'],
                'filters' => $filters,
                'summary' => $data['summary'],
                'pagination' => $data['pagination'],
                'active_section' => $section,
                'current_page' => $page
            ];

            return view('admin_pusat/laporan_waste/index', $viewData);

        } catch (\Exception $e) {
            log_message('error', 'Laporan Waste Error: ' . $e->getMessage());
            
            return view('admin_pusat/laporan_waste/index', [
                'title' => 'Laporan Waste',
                'rekap_jenis' => [],
                'rekap_unit' => [],
                'detail_rekap' => [],
                'data_disetujui' => [],
                'data_ditolak' => [],
                'units' => [],
                'filters' => [],
                'summary' => [],
                'error' => 'Terjadi kesalahan saat memuat laporan'
            ]);
        }
    }

    public function export()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            $filters = [
                'start_date' => $this->request->getGet('start_date'),
                'end_date' => $this->request->getGet('end_date'),
                'status' => $this->request->getGet('status'),
                'unit_id' => $this->request->getGet('unit_id')
            ];

            $result = $this->laporanService->exportLaporan($filters);
            
            if ($result['success']) {
                return $this->response->download($result['file_path'], null)->setFileName($result['filename']);
            }

            return redirect()->back()->with('error', $result['message']);

        } catch (\Exception $e) {
            log_message('error', 'Export Laporan Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat export laporan');
        }
    }

    public function exportPdf()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            $filters = [
                'start_date' => $this->request->getGet('start_date'),
                'end_date' => $this->request->getGet('end_date'),
                'status' => $this->request->getGet('status'),
                'unit_id' => $this->request->getGet('unit_id')
            ];

            $result = $this->laporanService->exportPdf($filters);
            
            if ($result['success']) {
                return $this->response->download($result['file_path'], null)->setFileName($result['filename']);
            }

            return redirect()->back()->with('error', $result['message']);

        } catch (\Exception $e) {
            log_message('error', 'Export PDF Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat export PDF');
        }
    }

    public function exportCsv()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            $filters = [
                'start_date' => $this->request->getGet('start_date'),
                'end_date' => $this->request->getGet('end_date'),
                'status' => $this->request->getGet('status'),
                'unit_id' => $this->request->getGet('unit_id')
            ];

            $result = $this->laporanService->exportCsv($filters);
            
            if ($result['success']) {
                return $this->response->download($result['file_path'], null)->setFileName($result['filename']);
            }

            return redirect()->back()->with('error', $result['message']);

        } catch (\Exception $e) {
            log_message('error', 'Export CSV Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat export CSV');
        }
    }

    public function exportExcel()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            $filters = [
                'start_date' => $this->request->getGet('start_date'),
                'end_date' => $this->request->getGet('end_date'),
                'status' => $this->request->getGet('status'),
                'unit_id' => $this->request->getGet('unit_id')
            ];

            // Load helper
            helper('excel');

            // This will output Excel and exit
            $this->laporanService->exportExcel($filters);
            
            // Code below will not be executed because exportExcel() calls exit()
            exit;

        } catch (\Exception $e) {
            log_message('error', 'Export Excel Error: ' . $e->getMessage());
            
            // Show error page instead of redirect
            echo '<html><body>';
            echo '<h1>Error Export Excel</h1>';
            echo '<p>Terjadi kesalahan: ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<p><a href="' . base_url('/admin-pusat/laporan-waste') . '">Kembali ke Laporan</a></p>';
            echo '</body></html>';
            exit;
        }
    }

    /**
     * Get detail rekap jenis sampah (AJAX endpoint)
     * Returns rincian per gedung dan pelapor
     */
    public function getDetailRekapJenis()
    {
        try {
            // Debug: Check if method is called
            log_message('info', 'getDetailRekapJenis called');
            
            if (!$this->validateSession()) {
                log_message('error', 'Session validation failed');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Session tidak valid'
                ])->setStatusCode(401);
            }

            $jenisSampah = $this->request->getGet('jenis_sampah');
            log_message('info', 'Jenis sampah: ' . $jenisSampah);
            
            if (empty($jenisSampah)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Jenis sampah harus diisi'
                ])->setStatusCode(400);
            }

            $filters = [
                'start_date' => $this->request->getGet('start_date'),
                'end_date' => $this->request->getGet('end_date'),
                'unit_id' => $this->request->getGet('unit_id')
            ];
            
            log_message('info', 'Filters: ' . json_encode($filters));

            $result = $this->laporanService->getDetailRekapJenis($jenisSampah, $filters);
            
            log_message('info', 'Result success: ' . ($result['success'] ? 'true' : 'false'));

            return $this->response->setJSON($result);

        } catch (\Exception $e) {
            log_message('error', 'Get Detail Rekap Jenis Controller Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat detail',
                'error' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    private function validateSession(): bool
    {
        $session = session();
        $user = $session->get('user');
        
        return $session->get('isLoggedIn') && 
               isset($user['id'], $user['role']) &&
               $user['role'] === 'admin_pusat';
    }
}
