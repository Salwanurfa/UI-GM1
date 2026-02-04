<?php

if (!function_exists('hasRole')) {
    /**
     * Check if current user has specific role
     */
    function hasRole(string $role): bool
    {
        $user = session()->get('user');
        return $user && isset($user['role']) && $user['role'] === $role;
    }
}

if (!function_exists('hasAnyRole')) {
    /**
     * Check if current user has any of the specified roles
     */
    function hasAnyRole(array $roles): bool
    {
        $user = session()->get('user');
        return $user && isset($user['role']) && in_array($user['role'], $roles);
    }
}

if (!function_exists('isAdmin')) {
    /**
     * Check if current user is admin (admin_pusat or super_admin)
     */
    function isAdmin(): bool
    {
        return hasAnyRole(['admin_pusat', 'super_admin']);
    }
}

if (!function_exists('isUser')) {
    /**
     * Check if current user is regular user
     */
    function isUser(): bool
    {
        return hasRole('user');
    }
}

if (!function_exists('isTPS')) {
    /**
     * Check if current user is TPS manager
     */
    function isTPS(): bool
    {
        return hasRole('pengelola_tps');
    }
}

if (!function_exists('getCurrentRole')) {
    /**
     * Get current user role
     */
    function getCurrentRole(): ?string
    {
        $user = session()->get('user');
        return $user['role'] ?? null;
    }
}

if (!function_exists('getCurrentUserId')) {
    /**
     * Get current user ID
     */
    function getCurrentUserId(): ?int
    {
        $user = session()->get('user');
        return isset($user['id']) ? (int)$user['id'] : null;
    }
}

if (!function_exists('getCurrentUnitId')) {
    /**
     * Get current user unit ID
     */
    function getCurrentUnitId(): ?int
    {
        $user = session()->get('user');
        return isset($user['unit_id']) ? (int)$user['unit_id'] : null;
    }
}

if (!function_exists('canAccessRoute')) {
    /**
     * Check if current user can access specific route based on role
     */
    function canAccessRoute(string $route): bool
    {
        $role = getCurrentRole();
        
        if (!$role) {
            return false;
        }
        
        // Define route permissions
        $permissions = [
            'admin_pusat' => [
                '/admin-pusat/*'
            ],
            'super_admin' => [
                '/admin-pusat/*'
            ],
            'user' => [
                '/user/*'
            ],
            'pengelola_tps' => [
                '/pengelola-tps/*'
            ]
        ];
        
        if (!isset($permissions[$role])) {
            return false;
        }
        
        foreach ($permissions[$role] as $pattern) {
            if (fnmatch($pattern, $route)) {
                return true;
            }
        }
        
        return false;
    }
}

if (!function_exists('getDashboardUrl')) {
    /**
     * Get dashboard URL based on current user role
     */
    function getDashboardUrl(): string
    {
        $role = getCurrentRole();
        
        switch ($role) {
            case 'admin_pusat':
            case 'super_admin':
                return '/admin-pusat/dashboard';
            case 'user':
                return '/user/dashboard';
            case 'pengelola_tps':
                return '/pengelola-tps/dashboard';
            default:
                return '/auth/login';
        }
    }
}

if (!function_exists('getRoleName')) {
    /**
     * Get human readable role name
     */
    function getRoleName(string $role = null): string
    {
        if ($role === null) {
            $role = getCurrentRole();
        }
        
        $roleNames = [
            'admin_pusat' => 'Admin Pusat',
            'super_admin' => 'Super Admin',
            'user' => 'User (Petugas Gedung)',
            'pengelola_tps' => 'Pengelola TPS'
        ];
        
        return $roleNames[$role] ?? 'Unknown';
    }
}