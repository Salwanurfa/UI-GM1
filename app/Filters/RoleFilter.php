<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login')
                ->with('error', 'Anda harus login terlebih dahulu');
        }

        $user = $session->get('user');
        
        // Validasi session user harus memiliki id dan role
        // unit_id tidak wajib untuk role security
        if (!isset($user['id'], $user['role'])) {
            log_message('error', 'Invalid session data: ' . json_encode($user));
            $session->destroy();
            return redirect()->to('/auth/login')
                ->with('error', 'Session tidak valid. Silakan login kembali');
        }

        // Validasi unit_id untuk role yang memerlukan unit
        $rolesRequiringUnit = ['admin_pusat', 'super_admin', 'user', 'pengelola_tps'];
        if (in_array($user['role'], $rolesRequiringUnit) && !isset($user['unit_id'])) {
            log_message('error', 'Missing unit_id for role requiring unit: ' . json_encode($user));
            $session->destroy();
            return redirect()->to('/auth/login')
                ->with('error', 'Session tidak valid. Silakan login kembali');
        }

        $userRole = $user['role'];

        // If no role requirement specified, just check if logged in
        if (empty($arguments)) {
            return null;
        }

        // Handle multiple roles - improved parsing
        $allowedRoles = [];
        if (is_array($arguments)) {
            // If arguments is already an array
            $allowedRoles = $arguments;
        } else {
            // Handle string format like "role:admin_pusat,super_admin"
            $roleString = is_array($arguments) ? $arguments[0] : $arguments;
            
            // Remove "role:" prefix if present
            if (strpos($roleString, 'role:') === 0) {
                $roleString = substr($roleString, 5);
            }
            
            // Split by comma and clean up
            $allowedRoles = array_map('trim', explode(',', $roleString));
        }

        // Check if user role is in allowed roles
        if (!in_array($userRole, $allowedRoles)) {
            // Redirect based on user role
            $redirectUrl = $this->getRedirectUrlByRole($userRole);
            
            return redirect()->to($redirectUrl)
                ->with('error', 'Akses ditolak. Anda tidak memiliki akses ke halaman ini.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }

    /**
     * Get appropriate redirect URL based on user role (KONSISTEN DENGAN ROUTES)
     */
    private function getRedirectUrlByRole(?string $role): string
    {
        switch ($role) {
            case 'admin_pusat':
            case 'super_admin':
                return '/admin-pusat/dashboard';
            case 'user':
                return '/user/dashboard';
            case 'pengelola_tps':
                return '/pengelola-tps/dashboard';
            case 'security':
                return '/security/dashboard';
            default:
                return '/auth/login';
        }
    }
}