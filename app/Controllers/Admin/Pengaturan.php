<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\Admin\PengaturanService;

class Pengaturan extends BaseController
{
    protected $pengaturanService;

    public function __construct()
    {
        $this->pengaturanService = new PengaturanService();
    }

    public function index()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            // Provide default data for the view
            $viewData = [
                'title' => 'Pengaturan Sistem',
                'allTahun' => [], // Empty array as default
                'system_settings' => [
                    'app_name' => 'Waste Management System',
                    'app_version' => '1.0.0',
                    'maintenance_mode' => false
                ],
                'feature_settings' => [],
                'user_settings' => []
            ];

            return view('admin_pusat/pengaturan', $viewData);

        } catch (\Exception $e) {
            log_message('error', 'Admin Pengaturan Error: ' . $e->getMessage());
            
            return view('admin_pusat/pengaturan', [
                'title' => 'Pengaturan Sistem',
                'allTahun' => [],
                'system_settings' => [],
                'feature_settings' => [],
                'user_settings' => [],
                'error' => 'Terjadi kesalahan saat memuat pengaturan'
            ]);
        }
    }

    public function update()
    {
        try {
            if (!$this->validateSession()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Session invalid']);
            }

            $result = $this->pengaturanService->updatePengaturan($this->request->getPost());
            
            return $this->response->setJSON($result);

        } catch (\Exception $e) {
            log_message('error', 'Admin Pengaturan Update Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate pengaturan'
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