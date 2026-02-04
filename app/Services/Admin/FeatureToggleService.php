<?php

namespace App\Services\Admin;

use App\Models\FeatureToggleModel;

class FeatureToggleService
{
    protected $featureModel;

    public function __construct()
    {
        $this->featureModel = new FeatureToggleModel();
    }

    public function getFeatureData(): array
    {
        try {
            return [
                'features' => $this->featureModel->findAll(),
                'roles' => $this->getAvailableRoles()
            ];
        } catch (\Exception $e) {
            log_message('error', 'Feature Toggle Service Error: ' . $e->getMessage());
            
            return [
                'features' => [],
                'roles' => []
            ];
        }
    }

    public function toggleFeature(array $data): array
    {
        try {
            if (empty($data['feature_key'])) {
                return ['success' => false, 'message' => 'Feature key harus diisi'];
            }

            $feature = $this->featureModel->getByKey($data['feature_key']);
            if (!$feature) {
                return ['success' => false, 'message' => 'Feature tidak ditemukan'];
            }

            $newStatus = $feature['is_enabled'] ? 0 : 1;
            
            $result = $this->featureModel->update($feature['id'], [
                'is_enabled' => $newStatus,
                'updated_by' => session()->get('user')['id']
            ]);

            if ($result) {
                $status = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
                return ['success' => true, 'message' => "Feature berhasil {$status}"];
            }

            return ['success' => false, 'message' => 'Gagal mengubah status feature'];

        } catch (\Exception $e) {
            log_message('error', 'Toggle Feature Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem'];
        }
    }

    public function bulkToggleFeatures(array $data): array
    {
        try {
            if (empty($data['features']) || !is_array($data['features'])) {
                return ['success' => false, 'message' => 'Data features tidak valid'];
            }

            $action = $data['action'] ?? 'enable';
            $status = $action === 'enable' ? 1 : 0;
            $successCount = 0;

            foreach ($data['features'] as $featureKey) {
                $feature = $this->featureModel->getByKey($featureKey);
                if ($feature) {
                    $result = $this->featureModel->update($feature['id'], [
                        'is_enabled' => $status,
                        'updated_by' => session()->get('user')['id']
                    ]);
                    
                    if ($result) {
                        $successCount++;
                    }
                }
            }

            $actionText = $action === 'enable' ? 'diaktifkan' : 'dinonaktifkan';
            return [
                'success' => true, 
                'message' => "{$successCount} feature berhasil {$actionText}"
            ];

        } catch (\Exception $e) {
            log_message('error', 'Bulk Toggle Features Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem'];
        }
    }

    public function updateRoleConfig(string $featureKey, array $roleConfig): array
    {
        try {
            $feature = $this->featureModel->getByKey($featureKey);
            if (!$feature) {
                return ['success' => false, 'message' => 'Feature tidak ditemukan'];
            }

            $result = $this->featureModel->update($feature['id'], [
                'role_config' => json_encode($roleConfig),
                'updated_by' => session()->get('user')['id']
            ]);

            if ($result) {
                return ['success' => true, 'message' => 'Konfigurasi role berhasil diupdate'];
            }

            return ['success' => false, 'message' => 'Gagal mengupdate konfigurasi role'];

        } catch (\Exception $e) {
            log_message('error', 'Update Role Config Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem'];
        }
    }

    public function getFeatureLogs(): array
    {
        try {
            // Implementasi log feature toggle jika diperlukan
            return [
                'logs' => []
            ];
        } catch (\Exception $e) {
            log_message('error', 'Feature Logs Error: ' . $e->getMessage());
            return ['logs' => []];
        }
    }

    private function getAvailableRoles(): array
    {
        return [
            'admin_pusat' => 'Admin Pusat',
            'super_admin' => 'Super Admin',
            'user' => 'User (Petugas Gedung)',
            'pengelola_tps' => 'Pengelola TPS'
        ];
    }
}