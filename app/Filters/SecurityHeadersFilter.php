<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Security Headers Filter
 * 
 * Adds security headers to all responses
 */
class SecurityHeadersFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Do nothing before request
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Apply essential security headers
        $securityHeaders = [
            'X-Frame-Options' => 'DENY',
            'X-Content-Type-Options' => 'nosniff',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'X-Permitted-Cross-Domain-Policies' => 'none'
        ];
        
        foreach ($securityHeaders as $header => $value) {
            $response->setHeader($header, $value);
        }
        
        return $response;
    }
}