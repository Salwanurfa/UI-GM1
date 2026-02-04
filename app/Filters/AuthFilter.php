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
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login')
                ->with('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini');
        }

        // Check role-based access if arguments provided
        if (!empty($arguments)) {
            $userRole = session()->get('user')['role'] ?? null;

            if (!in_array($userRole, $arguments)) {
                return redirect()->to('/auth/login')
                    ->with('error', 'Anda tidak memiliki akses ke halaman ini');
            }
        }

        // Update last activity
        session()->set('last_activity', time());

        // Check session timeout (30 minutes)
        $lastActivity = session()->get('last_activity') ?? time();
        if (time() - $lastActivity > 1800) { // 30 minutes
            session()->destroy();
            return redirect()->to('/auth/login')
                ->with('error', 'Sesi Anda telah berakhir. Silakan login kembali');
        }
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
