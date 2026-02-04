<?php

namespace App\Models;

use CodeIgniter\Model;

class FeatureToggleModel extends Model
{
    protected $table = 'feature_toggles';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'feature_key',
        'feature_name',
        'description',
        'is_enabled',
        'target_roles',
        'config',
        'updated_by'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'feature_key' => 'required|max_length[100]|is_unique[feature_toggles.feature_key,id,{id}]',
        'feature_name' => 'required|max_length[255]',
        'is_enabled' => 'required|in_list[0,1]'
    ];

    protected $validationMessages = [
        'feature_key' => [
            'required' => 'Feature key harus diisi',
            'is_unique' => 'Feature key sudah ada'
        ],
        'feature_name' => [
            'required' => 'Nama feature harus diisi'
        ]
    ];

    /**
     * Get feature by key
     */
    public function getByKey(string $key): ?array
    {
        return $this->where('feature_key', $key)->first();
    }

    /**
     * Check if feature is enabled
     */
    public function isEnabled(string $key, ?string $role = null): bool
    {
        $feature = $this->getByKey($key);
        
        if (!$feature) {
            return false;
        }

        // Check if feature is globally enabled
        if (!$feature['is_enabled']) {
            return false;
        }

        // Check role-specific access
        if ($role && !empty($feature['target_roles'])) {
            $targetRoles = json_decode($feature['target_roles'], true);
            if (!in_array($role, $targetRoles)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Toggle feature status
     */
    public function toggleFeature(string $key, int $userId): bool
    {
        $feature = $this->getByKey($key);
        
        if (!$feature) {
            log_message('error', "Toggle failed: Feature '{$key}' not found");
            return false;
        }

        $newStatus = $feature['is_enabled'] ? 0 : 1; // Explicit 0 or 1
        
        log_message('info', "Toggling feature '{$key}' from {$feature['is_enabled']} to {$newStatus}");
        
        $result = $this->where('feature_key', $key)->set([
            'is_enabled' => $newStatus,
            'updated_by' => $userId
        ])->update();
        
        if ($result) {
            log_message('info', "Toggle success: Feature '{$key}' updated to {$newStatus}");
        } else {
            log_message('error', "Toggle failed: Database update failed for '{$key}'");
        }
        
        return $result;
    }

    /**
     * Get all features grouped by category
     */
    public function getGroupedFeatures(): array
    {
        $features = $this->findAll();
        
        $grouped = [
            'dashboard' => [],
            'waste' => [],
            'management' => [],
            'system' => []
        ];

        foreach ($features as $feature) {
            $key = $feature['feature_key'];
            
            if (strpos($key, 'dashboard') !== false) {
                $grouped['dashboard'][] = $feature;
            } elseif (strpos($key, 'waste') !== false) {
                $grouped['waste'][] = $feature;
            } elseif (strpos($key, 'management') !== false || strpos($key, 'review') !== false) {
                $grouped['management'][] = $feature;
            } else {
                $grouped['system'][] = $feature;
            }
        }

        return $grouped;
    }

    /**
     * Get features by role
     */
    public function getByRole(string $role): array
    {
        $allFeatures = $this->findAll();
        
        return array_filter($allFeatures, function($feature) use ($role) {
            if (empty($feature['target_roles'])) {
                return true;
            }
            
            $targetRoles = json_decode($feature['target_roles'], true);
            return in_array($role, $targetRoles);
        });
    }

    /**
     * Update feature configuration
     */
    public function updateConfig(string $key, array $config, int $userId): bool
    {
        return $this->where('feature_key', $key)->set([
            'config' => json_encode($config),
            'updated_by' => $userId
        ])->update();
    }
}
