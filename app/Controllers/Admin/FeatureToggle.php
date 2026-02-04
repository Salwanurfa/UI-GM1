<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FeatureToggleModel;

class FeatureToggle extends BaseController
{
    protected $featureModel;

    public function __construct()
    {
        $this->featureModel = new FeatureToggleModel();
    }

    public function index()
    {
        if (!$this->validateAdminSession()) {
            return redirect()->to('/auth/login');
        }

        // Get all features grouped by category
        $featuresByCategory = $this->featureModel->getGroupedFeatures();
        
        // Calculate statistics
        $allFeatures = $this->featureModel->findAll();
        $totalFeatures = count($allFeatures);
        $enabledFeatures = count(array_filter($allFeatures, fn($f) => $f['is_enabled'] == 1));
        $disabledFeatures = $totalFeatures - $enabledFeatures;

        $data = [
            'title' => 'Feature Toggle Management',
            'features' => $allFeatures,
            'featuresByCategory' => $featuresByCategory,
            'statistics' => [
                'total_features' => $totalFeatures,
                'enabled_features' => $enabledFeatures,
                'disabled_features' => $disabledFeatures,
                'last_updated' => date('Y-m-d H:i:s')
            ],
            'categories' => [
                'dashboard' => 'Dashboard Features',
                'waste' => 'Waste Management',
                'management' => 'Management Features',
                'system' => 'System Features'
            ]
        ];

        return view('admin_pusat/feature_toggle/index', $data);
    }

    public function toggle()
    {
        if (!$this->validateAdminSession()) {
            log_message('error', 'Toggle failed: Unauthorized');
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $featureKey = $this->request->getPost('feature_key');
            $reason = $this->request->getPost('reason');
            
            log_message('info', "Toggle request received: feature_key={$featureKey}, reason={$reason}");
            
            if (empty($featureKey)) {
                log_message('error', 'Toggle failed: Feature key empty');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Feature key tidak boleh kosong'
                ]);
            }

            $session = session();
            $user = $session->get('user');
            $userId = $user['id'] ?? 0;
            
            log_message('info', "User ID: {$userId}");

            // Toggle the feature
            $success = $this->featureModel->toggleFeature($featureKey, $userId);
            
            log_message('info', "Toggle result: " . ($success ? 'SUCCESS' : 'FAILED'));

            if ($success) {
                // Get updated feature status
                $feature = $this->featureModel->getByKey($featureKey);
                $newStatus = $feature['is_enabled'] ? 'enabled' : 'disabled';
                
                log_message('info', "Feature '{$featureKey}' {$newStatus} by user {$userId}. Reason: {$reason}");
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => "Feature berhasil di{$newStatus}",
                    'new_status' => (bool)$feature['is_enabled']
                ]);
            }

            log_message('error', "Toggle failed for feature: {$featureKey}");
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengubah status feature'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Feature toggle exception: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function getFeature($featureKey)
    {
        if (!$this->validateAdminSession()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $feature = $this->featureModel->getByKey($featureKey);
            
            if (!$feature) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Feature tidak ditemukan'
                ]);
            }

            // Decode JSON fields
            $feature['target_roles'] = json_decode($feature['target_roles'], true) ?? [];
            $feature['config'] = json_decode($feature['config'], true) ?? [];

            return $this->response->setJSON([
                'success' => true,
                'data' => $feature
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function updateConfig()
    {
        if (!$this->validateAdminSession()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $featureKey = $this->request->getPost('feature_key');
            $config = $this->request->getPost('config');
            
            if (empty($featureKey)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Feature key tidak boleh kosong'
                ]);
            }

            $session = session();
            $user = $session->get('user');
            $userId = $user['id'] ?? 0;

            // Parse config if it's a JSON string
            if (is_string($config)) {
                $config = json_decode($config, true);
            }

            $success = $this->featureModel->updateConfig($featureKey, $config ?? [], $userId);

            if ($success) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Konfigurasi berhasil diupdate'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengupdate konfigurasi'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function bulkToggle()
    {
        if (!$this->validateAdminSession()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $action = $this->request->getPost('action'); // 'enable_all' or 'disable_all'
            $category = $this->request->getPost('category'); // optional
            
            $session = session();
            $user = $session->get('user');
            $userId = $user['id'] ?? 0;

            $newStatus = ($action === 'enable_all') ? 1 : 0;
            
            $builder = $this->featureModel->builder();
            
            if ($category) {
                $builder->like('feature_key', $category);
            }
            
            $success = $builder->set([
                'is_enabled' => $newStatus,
                'updated_by' => $userId
            ])->update();

            if ($success) {
                $actionText = $action === 'enable_all' ? 'diaktifkan' : 'dinonaktifkan';
                return $this->response->setJSON([
                    'success' => true,
                    'message' => "Semua feature berhasil {$actionText}"
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengubah status features'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    private function validateAdminSession(): bool
    {
        $session = session();
        $user = $session->get('user');
        
        return $session->get('isLoggedIn') && 
               isset($user['role']) &&
               in_array($user['role'], ['admin_pusat', 'super_admin']);
    }
}
