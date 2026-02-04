<?php

namespace App\Helpers;

/**
 * Admin Menu Helper
 * Helper untuk menghubungkan menu admin ke dashboard
 */
class AdminMenuHelper
{
    /**
     * Get Feature Toggle menu configuration
     */
    public static function getFeatureToggleMenu(): array
    {
        return [
            'title' => 'Feature Toggle',
            'subtitle' => 'Control UI Features',
            'icon' => 'fas fa-toggle-on',
            'url' => base_url('/admin-pusat/feature-toggle'),
            'description' => 'Kontrol fitur dan komponen dashboard User dan TPS',
            'status' => 'active',
            'permissions' => ['admin_pusat', 'super_admin'],
            'category' => 'system'
        ];
    }
    
    /**
     * Get Manajemen Harga menu configuration
     */
    public static function getManajemenHargaMenu(): array
    {
        return [
            'title' => 'Manajemen Harga',
            'subtitle' => 'Kelola Harga Sampah',
            'icon' => 'fas fa-money-bill-wave',
            'url' => base_url('/admin-pusat/manajemen-harga'),
            'description' => 'Kelola harga jual sampah untuk sistem waste management',
            'status' => 'active',
            'permissions' => ['admin_pusat', 'super_admin'],
            'category' => 'data_management'
        ];
    }
    
    /**
     * Get all admin menus
     */
    public static function getAllAdminMenus(): array
    {
        return [
            'feature_toggle' => self::getFeatureToggleMenu(),
            'manajemen_harga' => self::getManajemenHargaMenu(),
        ];
    }
    
    /**
     * Get menu by category
     */
    public static function getMenusByCategory(string $category): array
    {
        $allMenus = self::getAllAdminMenus();
        $filtered = [];
        
        foreach ($allMenus as $key => $menu) {
            if ($menu['category'] === $category) {
                $filtered[$key] = $menu;
            }
        }
        
        return $filtered;
    }
    
    /**
     * Check if user has permission to access menu
     */
    public static function hasPermission(array $menu, string $userRole): bool
    {
        return in_array($userRole, $menu['permissions']);
    }
    
    /**
     * Get menu statistics for dashboard
     */
    public static function getMenuStatistics(): array
    {
        try {
            $stats = [];
            
            // Feature Toggle stats
            $featureModel = new \App\Models\FeatureToggleModel();
            $stats['feature_toggle'] = [
                'total_features' => $featureModel->countAllResults(),
                'enabled_features' => $featureModel->where('is_enabled', 1)->countAllResults(),
                'last_updated' => $featureModel->selectMax('updated_at')->first()['updated_at'] ?? null
            ];
            
            // Manajemen Harga stats
            $hargaModel = new \App\Models\MasterHargaSampahModel();
            $stats['manajemen_harga'] = [
                'total_items' => $hargaModel->countAllResults(),
                'active_items' => $hargaModel->where('status_aktif', 1)->countAllResults(),
                'last_updated' => $hargaModel->selectMax('updated_at')->first()['updated_at'] ?? null
            ];
            
            return $stats;
            
        } catch (\Exception $e) {
            log_message('error', 'AdminMenuHelper::getMenuStatistics - Error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Generate menu HTML for dashboard
     */
    public static function generateMenuHtml(string $userRole): string
    {
        $menus = self::getAllAdminMenus();
        $html = '';
        
        foreach ($menus as $key => $menu) {
            if (self::hasPermission($menu, $userRole)) {
                $html .= '<a href="' . $menu['url'] . '" class="quick-action-btn">';
                $html .= '<i class="' . $menu['icon'] . '"></i>';
                $html .= '<span>' . $menu['title'] . '</span>';
                $html .= '</a>';
            }
        }
        
        return $html;
    }
}