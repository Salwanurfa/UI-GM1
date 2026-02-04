<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;

class WasteApi extends BaseController
{
    public function getSummary()
    {
        try {
            // Validate session
            if (!session()->get('isLoggedIn')) {
                return $this->response->setStatusCode(401)
                    ->setJSON(['success' => false, 'message' => 'Unauthorized']);
            }

            $user = session()->get('user');
            $role = $user['role'];

            // Get waste summary based on role
            switch ($role) {
                case 'admin_pusat':
                case 'super_admin':
                    $wasteModel = new \App\Models\WasteModel();
                    $summary = [
                        'total_waste' => $wasteModel->countAllResults(),
                        'pending_review' => $wasteModel->where('status', 'pending')->countAllResults(),
                        'approved' => $wasteModel->where('status', 'approved')->countAllResults(),
                        'rejected' => $wasteModel->where('status', 'rejected')->countAllResults(),
                        'total_weight' => $wasteModel->selectSum('berat_kg')->get()->getRow()->berat_kg ?? 0
                    ];
                    break;
                    
                case 'user':
                    $wasteModel = new \App\Models\WasteModel();
                    $unitId = $user['unit_id'];
                    $summary = [
                        'total_waste' => $wasteModel->where('unit_id', $unitId)->countAllResults(),
                        'pending_review' => $wasteModel->where('unit_id', $unitId)->where('status', 'pending')->countAllResults(),
                        'approved' => $wasteModel->where('unit_id', $unitId)->where('status', 'approved')->countAllResults(),
                        'rejected' => $wasteModel->where('unit_id', $unitId)->where('status', 'rejected')->countAllResults(),
                        'total_weight' => $wasteModel->selectSum('berat_kg')->where('unit_id', $unitId)->get()->getRow()->berat_kg ?? 0
                    ];
                    break;
                    
                case 'pengelola_tps':
                    $wasteModel = new \App\Models\WasteModel();
                    $tpsId = $user['unit_id'];
                    $summary = [
                        'total_waste' => $wasteModel->where('tps_id', $tpsId)->countAllResults(),
                        'total_weight' => $wasteModel->selectSum('berat_kg')->where('tps_id', $tpsId)->get()->getRow()->berat_kg ?? 0,
                        'waste_today' => $wasteModel->where('tps_id', $tpsId)->where('DATE(created_at)', date('Y-m-d'))->countAllResults(),
                        'weight_today' => $wasteModel->selectSum('berat_kg')->where('tps_id', $tpsId)->where('DATE(created_at)', date('Y-m-d'))->get()->getRow()->berat_kg ?? 0
                    ];
                    break;
                    
                default:
                    return $this->response->setStatusCode(403)
                        ->setJSON(['success' => false, 'message' => 'Access denied']);
            }

            return $this->response->setJSON([
                'success' => true,
                'summary' => $summary,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Waste API Error: ' . $e->getMessage());
            
            return $this->response->setStatusCode(500)
                ->setJSON(['success' => false, 'message' => 'Internal server error']);
        }
    }
}