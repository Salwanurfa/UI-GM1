<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\Admin\ReviewService;

class Review extends BaseController
{
    protected $reviewService;

    public function __construct()
    {
        $this->reviewService = new ReviewService();
    }

    public function index()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            // Provide default data for the view
            $viewData = [
                'title' => 'Review Data Sampah',
                'pengiriman' => [
                    'nama_unit' => 'N/A',
                    'kode_unit' => 'N/A',
                    'tahun' => date('Y')
                ],
                'pending_reviews' => [],
                'recent_reviews' => [],
                'stats' => [
                    'pending' => 0,
                    'approved' => 0,
                    'rejected' => 0
                ],
                'queue_summary' => []
            ];

            return view('admin_pusat/review', $viewData);

        } catch (\Exception $e) {
            log_message('error', 'Admin Review Error: ' . $e->getMessage());
            
            return view('admin_pusat/review', [
                'title' => 'Review Data Sampah',
                'pengiriman' => ['nama_unit' => 'N/A', 'kode_unit' => 'N/A', 'tahun' => date('Y')],
                'pending_reviews' => [],
                'recent_reviews' => [],
                'stats' => [],
                'queue_summary' => [],
                'error' => 'Terjadi kesalahan saat memuat data review'
            ]);
        }
    }

    public function approve($id)
    {
        try {
            if (!$this->validateSession()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Session invalid']);
            }

            $result = $this->reviewService->approveWaste($id, $this->request->getPost());
            
            return $this->response->setJSON($result);

        } catch (\Exception $e) {
            log_message('error', 'Admin Review Approve Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyetujui data'
            ]);
        }
    }

    public function reject($id)
    {
        try {
            if (!$this->validateSession()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Session invalid']);
            }

            $result = $this->reviewService->rejectWaste($id, $this->request->getPost());
            
            return $this->response->setJSON($result);

        } catch (\Exception $e) {
            log_message('error', 'Admin Review Reject Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menolak data'
            ]);
        }
    }

    public function detail($id)
    {
        try {
            if (!$this->validateSession()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Session invalid']);
            }

            $result = $this->reviewService->getWasteDetail($id);
            
            return $this->response->setJSON($result);

        } catch (\Exception $e) {
            log_message('error', 'Admin Review Detail Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail data'
            ]);
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