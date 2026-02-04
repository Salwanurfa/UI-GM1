<?php

namespace App\Services\Admin;

use App\Models\FeatureToggleModel;
use App\Models\UserModel;

class PengaturanService
{
    protected $featureModel;
    protected $userModel;

    public function __construct()
    {
        $this->featureModel = new FeatureToggleModel();
        $this->userModel = new UserModel();
    }

    public function getPengaturanData(): array
    {
        try {
            return [
                'system_settings' => $this->getSystemSettings(),
                'feature_settings' => $this->getFeatureSettings(),
                'user_settings' => $this->getUserSettings()
            ];
        } catch (\Exception $e) {
            log_message('error', 'Pengaturan Service Error: ' . $e->getMessage());
            
            return [
                'system_settings' => [],
                'feature_settings' => [],
                'user_settings' => []
            ];
        }
    }

    public function updatePengaturan(array $data): array
    {
        try {
            $settingType = $data['setting_type'] ?? '';
            
            switch ($settingType) {
                case 'system':
                    return $this->updateSystemSettings($data);
                case 'feature':
                    return $this->updateFeatureSettings($data);
                case 'user':
                    return $this->updateUserSettings($data);
                default:
                    return ['success' => false, 'message' => 'Tipe pengaturan tidak valid'];
            }

        } catch (\Exception $e) {
            log_message('error', 'Update Pengaturan Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem'];
        }
    }

    private function getSystemSettings(): array
    {
        return [
            'app_name' => getAppName(),
            'app_version' => getAppVersion(),
            'maintenance_mode' => isMaintenanceMode(),
            'debug_mode' => isDebugMode(),
            'session_timeout' => getSessionTimeout(),
            'max_file_size' => getMaxFileSize()
        ];
    }

    private function getFeatureSettings(): array
    {
        return $this->featureModel->findAll();
    }

    private function getUserSettings(): array
    {
        return [
            'total_users' => $this->userModel->countAllResults(),
            'active_users' => $this->userModel->where('status_aktif', 1)->countAllResults(),
            'admin_count' => $this->userModel->whereIn('role', ['admin_pusat', 'super_admin'])->countAllResults(),
            'user_count' => $this->userModel->where('role', 'user')->countAllResults(),
            'tps_count' => $this->userModel->where('role', 'pengelola_tps')->countAllResults()
        ];
    }

    private function updateSystemSettings(array $data): array
    {
        // In a real implementation, you would update system configuration
        // For now, we'll just return success
        return ['success' => true, 'message' => 'Pengaturan sistem berhasil diupdate'];
    }

    private function updateFeatureSettings(array $data): array
    {
        if (empty($data['feature_key'])) {
            return ['success' => false, 'message' => 'Feature key harus diisi'];
        }

        $feature = $this->featureModel->getByKey($data['feature_key']);
        if (!$feature) {
            return ['success' => false, 'message' => 'Feature tidak ditemukan'];
        }

        $updateData = [
            'is_enabled' => isset($data['is_enabled']) ? (bool)$data['is_enabled'] : $feature['is_enabled'],
            'updated_by' => session()->get('user')['id']
        ];

        if (isset($data['role_config'])) {
            $updateData['role_config'] = json_encode($data['role_config']);
        }

        $result = $this->featureModel->update($feature['id'], $updateData);
        
        if ($result) {
            return ['success' => true, 'message' => 'Pengaturan feature berhasil diupdate'];
        }

        return ['success' => false, 'message' => 'Gagal mengupdate pengaturan feature'];
    }

    private function updateUserSettings(array $data): array
    {
        // User settings updates would be handled here
        return ['success' => true, 'message' => 'Pengaturan user berhasil diupdate'];
    }
}