<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $uri = $request->getUri()->getPath();
        
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            log_message('info', 'AuthFilter - User not logged in, redirecting to login. URI: ' . $uri);
            return redirect()->to('/auth/login')
                ->with('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini');
        }

        $user = session()->get('user');
        $userRole = $user['role'] ?? null;
        
        log_message('info', 'AuthFilter - User logged in. Role: ' . $userRole . ', URI: ' . $uri . ', Arguments: ' . json_encode($arguments));

        // Check role-based access if arguments provided
        if (!empty($arguments)) {
            // Normalize roles: support both underscore and dash variants
            $normalizedUserRole = str_replace('-', '_', $userRole);
            $normalizedArguments = array_map(function($role) {
                return str_replace('-', '_', $role);
            }, $arguments);
            
            log_message('info', 'AuthFilter - Checking role access. Normalized user role: ' . $normalizedUserRole . ', Allowed: ' . json_encode($normalizedArguments));

            if (!in_array($normalizedUserRole, $normalizedArguments)) {
                log_message('warning', 'AuthFilter - Access denied for role: ' . $userRole . ' to URI: ' . $uri);
                return redirect()->to('/auth/login')
                    ->with('error', 'Anda tidak memiliki akses ke halaman ini');
            }
        }

        // Update last activity
        session()->set('last_activity', time());

        // Check session timeout (30 minutes)
        $lastActivity = session()->get('last_activity') ?? time();
        if (time() - $lastActivity > 1800) { // 30 minutes
            log_message('info', 'AuthFilter - Session timeout for user role: ' . $userRole);
            session()->destroy();
            return redirect()->to('/auth/login')
                ->with('error', 'Sesi Anda telah berakhir. Silakan login kembali');
        }
        
        log_message('info', 'AuthFilter - Access granted for role: ' . $userRole . ' to URI: ' . $uri);
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing to do here
    }
}
