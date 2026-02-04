<?php

/**
 * Feature Toggle Helper Functions
 * Provides feature toggle functionality for the application
 */

if (!function_exists('isFeatureEnabled')) {
    /**
     * Check if a feature is enabled for a specific role
     * 
     * @param string $feature Feature name
     * @param string|null $role User role (optional)
     * @return bool
     */
    function isFeatureEnabled(string $feature, ?string $role = null): bool
    {
        // Cache key untuk mengurangi query database
        static $featureCache = [];
        $cacheKey = $feature . '_' . ($role ?? 'all');
        
        // Return dari cache jika ada
        if (isset($featureCache[$cacheKey])) {
            return $featureCache[$cacheKey];
        }
        
        try {
            // Load model
            $model = new \App\Models\FeatureToggleModel();
            
            // Check from database
            $result = $model->isEnabled($feature, $role);
            
            // Simpan ke cache
            $featureCache[$cacheKey] = $result;
            
            return $result;
            
        } catch (\Exception $e) {
            log_message('error', 'Feature toggle error for ' . $feature . ': ' . $e->getMessage());
            
            // Fallback: return true untuk fitur-fitur penting
            // Ini memastikan aplikasi tetap berjalan meskipun ada error
            $criticalFeatures = [
                'dashboard_statistics_cards',
                'dashboard_waste_summary',
                'dashboard_recent_activity',
                'waste_input_form',
                'waste_edit_function',
                'waste_delete_function',
                'waste_value_calculation',
                'export_data',
                'export_pdf',
                'admin_review_waste',
                'admin_user_management',
                'admin_harga_management'
            ];
            
            $fallbackResult = in_array($feature, $criticalFeatures);
            $featureCache[$cacheKey] = $fallbackResult;
            
            return $fallbackResult;
        }
    }
}

if (!function_exists('getFeatureConfig')) {
    /**
     * Get configuration for a specific feature
     * 
     * @param string $feature Feature name
     * @return array
     */
    function getFeatureConfig(string $feature): array
    {
        try {
            $model = new \App\Models\FeatureToggleModel();
            $featureData = $model->getByKey($feature);
            
            if ($featureData && !empty($featureData['config'])) {
                return json_decode($featureData['config'], true) ?? [];
            }
            
            return [];
            
        } catch (\Exception $e) {
            log_message('error', 'Get feature config error: ' . $e->getMessage());
            return [];
        }
    }
}

if (!function_exists('getAllFeatures')) {
    /**
     * Get all available features with their status
     * 
     * @param string|null $role Filter by role
     * @return array
     */
    function getAllFeatures(?string $role = null): array
    {
        try {
            $model = new \App\Models\FeatureToggleModel();
            
            if ($role) {
                $features = $model->getByRole($role);
            } else {
                $features = $model->findAll();
            }
            
            // Convert to associative array with feature_key as key
            $result = [];
            foreach ($features as $feature) {
                $key = $feature['feature_key'];
                $result[$key] = [
                    'name' => $feature['feature_name'],
                    'description' => $feature['description'],
                    'enabled' => (bool)$feature['is_enabled'],
                    'roles' => json_decode($feature['target_roles'], true) ?? [],
                    'config' => json_decode($feature['config'], true) ?? []
                ];
            }
            
            return $result;
            
        } catch (\Exception $e) {
            log_message('error', 'Get all features error: ' . $e->getMessage());
            return [];
        }
    }
}

if (!function_exists('clearFeatureCache')) {
    /**
     * Clear feature toggle cache
     * Berguna setelah update feature toggle
     * 
     * @return void
     */
    function clearFeatureCache(): void
    {
        // Karena menggunakan static cache, tidak perlu clear
        // Cache akan otomatis clear saat request baru
        log_message('info', 'Feature cache cleared (will reset on next request)');
    }
}

if (!function_exists('featureEnabled')) {
    /**
     * Alias untuk isFeatureEnabled (shorthand)
     * 
     * @param string $feature Feature name
     * @param string|null $role User role (optional)
     * @return bool
     */
    function featureEnabled(string $feature, ?string $role = null): bool
    {
        return isFeatureEnabled($feature, $role);
    }
}

if (!function_exists('getDisabledFeatures')) {
    /**
     * Get list of disabled features for a specific role
     * 
     * @param string|null $role User role (optional)
     * @return array Array of disabled feature names
     */
    function getDisabledFeatures(?string $role = null): array
    {
        try {
            $model = new \App\Models\FeatureToggleModel();
            
            // Get all features
            $allFeatures = $role ? $model->getByRole($role) : $model->findAll();
            
            // Filter only disabled features
            $disabledFeatures = [];
            foreach ($allFeatures as $feature) {
                // Check if feature is disabled
                if (!$feature['is_enabled']) {
                    // Check if role has access to this feature
                    if ($role) {
                        $targetRoles = json_decode($feature['target_roles'], true) ?? [];
                        if (in_array($role, $targetRoles)) {
                            $disabledFeatures[] = $feature['feature_name'];
                        }
                    } else {
                        $disabledFeatures[] = $feature['feature_name'];
                    }
                }
            }
            
            return $disabledFeatures;
            
        } catch (\Exception $e) {
            log_message('error', 'Get disabled features error: ' . $e->getMessage());
            return [];
        }
    }
}

if (!function_exists('hasDisabledFeatures')) {
    /**
     * Check if there are any disabled features for a role
     * 
     * @param string|null $role User role (optional)
     * @return bool
     */
    function hasDisabledFeatures(?string $role = null): bool
    {
        return count(getDisabledFeatures($role)) > 0;
    }
}

if (!function_exists('renderDisabledFeaturesAlert')) {
    /**
     * Render alert for disabled features
     * 
     * @param string|null $role User role (optional)
     * @return string HTML alert
     */
    function renderDisabledFeaturesAlert(?string $role = null): string
    {
        $disabledFeatures = getDisabledFeatures($role);
        
        if (empty($disabledFeatures)) {
            return '';
        }
        
        $html = '<div class="alert alert-info alert-dismissible fade show disabled-features-alert" role="alert">';
        $html .= '<i class="fas fa-info-circle"></i> ';
        
        if (count($disabledFeatures) === 1) {
            $html .= '<strong>' . htmlspecialchars($disabledFeatures[0]) . '</strong> sedang dimatikan oleh admin.';
        } else {
            $html .= 'Beberapa fitur sedang dimatikan oleh admin: ';
            $html .= '<strong>' . htmlspecialchars(implode(', ', $disabledFeatures)) . '</strong>';
        }
        
        $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        $html .= '</div>';
        
        return $html;
    }
}
