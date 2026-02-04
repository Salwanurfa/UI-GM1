<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;

class DashboardApi extends BaseController
{
    public function getStats()
    {
        try {
            // Validate session
            if (!session()->get('isLoggedIn')) {
                return $this->response->setStatusCode(401)
                    ->setJSON(['success' => false, 'message' => 'Unauthorized']);
            }

            $user = session()->get('user');
            $role = $user['role'];

            // Get stats based on role
            switch ($role) {
                case 'admin_pusat':
                case 'super_admin':
                    $service = new \App\Services\Admin\DashboardService();
                    $data = $service->getDashboardData();
                    break;
                    
                case 'user':
                    $service = new \App\Services\User\DashboardService();
                    $data = $service->getApiStats();
                    break;
                    
                case 'pengelola_tps':
                    $service = new \App\Services\TPS\DashboardService();
                    $data = $service->getDashboardData();
                    break;
                    
                default:
                    return $this->response->setStatusCode(403)
                        ->setJSON(['success' => false, 'message' => 'Access denied']);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Dashboard API Error: ' . $e->getMessage());
            
            return $this->response->setStatusCode(500)
                ->setJSON(['success' => false, 'message' => 'Internal server error']);
        }
    }
}