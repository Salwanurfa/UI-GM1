<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\Admin\WasteService;

class Waste extends BaseController
{
    protected $wasteService;

    public function __construct()
    {
        $this->wasteService = new WasteService();
    }

    public function index()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            log_message('info', 'Admin Waste Controller - Starting index...');
            
            $page = $this->request->getGet('page') ?? 1;
            $data = $this->wasteService->getWasteData($page, 10);
            
            log_message('info', 'Admin Waste Controller - Service returned: ' . count($data['waste_list']) . ' records');
            log_message('info', 'Admin Waste Controller - Data: ' . json_encode($data));
            
            // Debug: Cek langsung dari database
            $db = \Config\Database::connect();
            $directCount = $db->table('waste_management')->countAllResults();
            log_message('info', 'Admin Waste Controller - Direct count from DB: ' . $directCount);
            
            $viewData = [
                'title' => 'Waste Management',
                'waste_list' => $data['waste_list'],
                'summary' => $data['summary'],
                'filters' => $data['filters'] ?? [],
                'statistics' => $data['statistics'] ?? [],
                'pager' => $data['pager'] ?? null
            ];
            
            // Debug: Log view data
            log_message('info', 'Admin Waste Controller - Sending to view: ' . count($viewData['waste_list']) . ' records');

            return view('admin_pusat/waste_management', $viewData);

        } catch (\Exception $e) {
            log_message('error', 'Admin Waste Controller Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return view('admin_pusat/waste_management', [
                'title' => 'Waste Management',
                'waste_list' => [],
                'summary' => [],
                'error' => 'Terjadi kesalahan saat memuat data sampah: ' . $e->getMessage()
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

            $result = $this->wasteService->exportWaste();
            
            if ($result['success']) {
                return $this->response->download($result['file_path'], null)->setFileName($result['filename']);
            }

            return redirect()->back()->with('error', $result['message']);

        } catch (\Exception $e) {
            log_message('error', 'Admin Waste Export Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat export data');
        }
    }

    public function approve($id)
    {
        try {
            if (!$this->validateSession()) {
                return $this->response
                    ->setStatusCode(401)
                    ->setJSON(['success' => false, 'message' => 'Session invalid']);
            }

            log_message('info', 'Admin - Approving waste ID: ' . $id);

            $result = $this->wasteService->approveWaste($id, $this->request->getPost());
            
            log_message('info', 'Admin - Approve result: ' . json_encode($result));
            
            return $this->response
                ->setContentType('application/json')
                ->setJSON($result);

        } catch (\Exception $e) {
            log_message('error', 'Admin Waste Approve Error: ' . $e->getMessage());
            
            return $this->response
                ->setStatusCode(500)
                ->setContentType('application/json')
                ->setJSON([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyetujui data: ' . $e->getMessage()
                ]);
        }
    }

    public function reject($id)
    {
        try {
            if (!$this->validateSession()) {
                return $this->response
                    ->setStatusCode(401)
                    ->setJSON(['success' => false, 'message' => 'Session invalid']);
            }

            log_message('info', 'Admin - Rejecting waste ID: ' . $id);

            $result = $this->wasteService->rejectWaste($id, $this->request->getPost());
            
            log_message('info', 'Admin - Reject result: ' . json_encode($result));
            
            return $this->response
                ->setContentType('application/json')
                ->setJSON($result);

        } catch (\Exception $e) {
            log_message('error', 'Admin Waste Reject Error: ' . $e->getMessage());
            
            return $this->response
                ->setStatusCode(500)
                ->setContentType('application/json')
                ->setJSON([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menolak data: ' . $e->getMessage()
                ]);
        }
    }

    public function delete($id)
    {
        try {
            if (!$this->validateSession()) {
                return $this->response
                    ->setStatusCode(401)
                    ->setJSON(['success' => false, 'message' => 'Session invalid']);
            }

            log_message('info', 'Admin - Deleting waste ID: ' . $id);

            $result = $this->wasteService->deleteWaste($id);
            
            log_message('info', 'Admin - Delete result: ' . json_encode($result));
            
            return $this->response
                ->setContentType('application/json')
                ->setJSON($result);

        } catch (\Exception $e) {
            log_message('error', 'Admin Waste Delete Error: ' . $e->getMessage());
            
            return $this->response
                ->setStatusCode(500)
                ->setContentType('application/json')
                ->setJSON([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
                ]);
        }
    }

    private function validateSession(): bool
    {
        $session = session();
        $user = $session->get('user');
        
        return $session->get('isLoggedIn') && 
               isset($user['id'], $user['role']) &&
               in_array($user['role'], ['admin_pusat', 'super_admin']);
    }
}