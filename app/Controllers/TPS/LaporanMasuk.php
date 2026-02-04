<?php

namespace App\Controllers\TPS;

use App\Controllers\BaseController;
use App\Services\TPS\LaporanMasukService;

class LaporanMasuk extends BaseController
{
    protected $laporanService;

    public function __construct()
    {
        $this->laporanService = new LaporanMasukService();
    }

    public function index()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            $data = $this->laporanService->getLaporanMasuk();
            
            $viewData = [
                'title' => 'Laporan Masuk dari User',
                'laporan_pending' => $data['laporan_pending'],
                'laporan_reviewed' => $data['laporan_reviewed'],
                'stats' => $data['stats']
            ];

            return view('pengelola_tps/laporan_masuk', $viewData);

        } catch (\Exception $e) {
            log_message('error', 'TPS Laporan Masuk Error: ' . $e->getMessage());
            
            return view('pengelola_tps/laporan_masuk', [
                'title' => 'Laporan Masuk dari User',
                'laporan_pending' => [],
                'laporan_reviewed' => [],
                'stats' => [],
                'error' => 'Terjadi kesalahan saat memuat data laporan'
            ]);
        }
    }

    public function detail($id)
    {
        try {
            if (!$this->validateSession()) {
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Unauthorized']);
            }

            $result = $this->laporanService->getDetailLaporan($id);
            
            return $this->response
                ->setContentType('application/json')
                ->setJSON($result);

        } catch (\Exception $e) {
            log_message('error', 'TPS Get Detail Laporan Error: ' . $e->getMessage());
            
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
        }
    }

    public function approve($id)
    {
        try {
            if (!$this->validateSession()) {
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Session invalid']);
            }

            $catatan = $this->request->getPost('catatan') ?? '';
            
            $result = $this->laporanService->approveLaporan($id, $catatan);
            
            return $this->response
                ->setContentType('application/json')
                ->setJSON($result);

        } catch (\Exception $e) {
            log_message('error', 'TPS Approve Laporan Error: ' . $e->getMessage());
            
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
        }
    }

    public function reject($id)
    {
        try {
            if (!$this->validateSession()) {
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Session invalid']);
            }

            $catatan = $this->request->getPost('catatan');
            
            if (empty($catatan)) {
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Alasan penolakan harus diisi']);
            }
            
            $result = $this->laporanService->rejectLaporan($id, $catatan);
            
            return $this->response
                ->setContentType('application/json')
                ->setJSON($result);

        } catch (\Exception $e) {
            log_message('error', 'TPS Reject Laporan Error: ' . $e->getMessage());
            
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
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
