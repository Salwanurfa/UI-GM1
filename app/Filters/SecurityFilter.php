<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class SecurityFilter implements FilterInterface
{
    /**
     * 🔒 SECURITY: Enhanced security filter with multiple protections
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. Rate Limiting Protection
        $this->checkRateLimit($request);
        
        // 2. CSRF Protection (already handled by CodeIgniter, but we can add custom logic)
        $this->validateCSRF($request);
        
        // 3. Input Sanitization
        $this->sanitizeInputs($request);
        
        // 4. SQL Injection Protection (basic patterns)
        $this->checkSQLInjection($request);
        
        // 5. XSS Protection
        $this->checkXSS($request);
        
        // 6. Session Security
        $this->validateSession($request);
        
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Add security headers
        $response->setHeader('X-Content-Type-Options', 'nosniff');
        $response->setHeader('X-Frame-Options', 'DENY');
        $response->setHeader('X-XSS-Protection', '1; mode=block');
        $response->setHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        return $response;
    }

    /**
     * 🔒 SECURITY: Rate limiting protection
     */
    private function checkRateLimit(RequestInterface $request)
    {
        $session = session();
        $clientIP = $request->getIPAddress();
        $currentTime = time();
        
        // Get rate limit data from session
        $rateLimitKey = 'rate_limit_' . md5($clientIP);
        $rateLimitData = $session->get($rateLimitKey) ?? ['count' => 0, 'first_request' => $currentTime];
        
        // Reset counter if time window passed (60 seconds)
        if ($currentTime - $rateLimitData['first_request'] > 60) {
            $rateLimitData = ['count' => 1, 'first_request' => $currentTime];
        } else {
            $rateLimitData['count']++;
        }
        
        // Check if rate limit exceeded (max 100 requests per minute)
        if ($rateLimitData['count'] > 100) {
            log_message('warning', "Rate limit exceeded for IP: {$clientIP}");
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Rate limit exceeded');
        }
        
        $session->set($rateLimitKey, $rateLimitData);
    }

    /**
     * 🔒 SECURITY: CSRF validation
     */
    private function validateCSRF(RequestInterface $request)
    {
        // Skip CSRF for GET requests and API endpoints
        if ($request->getMethod() === 'GET' || strpos($request->getUri()->getPath(), '/api/') === 0) {
            return;
        }
        
        // Additional CSRF validation can be added here
        // CodeIgniter handles basic CSRF automatically if enabled
    }

    /**
     * 🔒 SECURITY: Input sanitization
     */
    private function sanitizeInputs(RequestInterface $request)
    {
        $dangerousPatterns = [
            '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi',
            '/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/mi',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload\s*=/i',
            '/onerror\s*=/i',
            '/onclick\s*=/i'
        ];
        
        $postData = $request->getPost();
        if (!empty($postData)) {
            foreach ($postData as $key => $value) {
                if (is_string($value)) {
                    foreach ($dangerousPatterns as $pattern) {
                        if (preg_match($pattern, $value)) {
                            log_message('warning', "Dangerous input detected in field '{$key}': {$value}");
                            throw new \CodeIgniter\Exceptions\PageNotFoundException('Invalid input detected');
                        }
                    }
                }
            }
        }
    }

    /**
     * 🔒 SECURITY: SQL injection protection
     */
    private function checkSQLInjection(RequestInterface $request)
    {
        $sqlPatterns = [
            '/(\bUNION\b.*\bSELECT\b)/i',
            '/(\bSELECT\b.*\bFROM\b.*\bWHERE\b.*[\'"].*[\'"])/i',
            '/(\bINSERT\b.*\bINTO\b)/i',
            '/(\bUPDATE\b.*\bSET\b)/i',
            '/(\bDELETE\b.*\bFROM\b)/i',
            '/(\bDROP\b.*\bTABLE\b)/i',
            '/(\bALTER\b.*\bTABLE\b)/i',
            '/(\bEXEC\b|\bEXECUTE\b)/i',
            '/(\'|\")(\s*)(OR|AND)(\s*)(\'|\")/i',
            '/(\-\-|\#|\/\*)/i'
        ];
        
        $allInputs = array_merge(
            $request->getGet() ?? [],
            $request->getPost() ?? []
        );
        
        foreach ($allInputs as $key => $value) {
            if (is_string($value)) {
                foreach ($sqlPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        log_message('error', "SQL injection attempt detected in field '{$key}': {$value}");
                        throw new \CodeIgniter\Exceptions\PageNotFoundException('Invalid request detected');
                    }
                }
            }
        }
    }

    /**
     * 🔒 SECURITY: XSS protection
     */
    private function checkXSS(RequestInterface $request)
    {
        $xssPatterns = [
            '/<script[^>]*>.*?<\/script>/is',
            '/<iframe[^>]*>.*?<\/iframe>/is',
            '/javascript\s*:/i',
            '/vbscript\s*:/i',
            '/on\w+\s*=/i',
            '/<object[^>]*>.*?<\/object>/is',
            '/<embed[^>]*>/i',
            '/<applet[^>]*>.*?<\/applet>/is'
        ];
        
        $allInputs = array_merge(
            $request->getGet() ?? [],
            $request->getPost() ?? []
        );
        
        foreach ($allInputs as $key => $value) {
            if (is_string($value)) {
                foreach ($xssPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        log_message('warning', "XSS attempt detected in field '{$key}': {$value}");
                        throw new \CodeIgniter\Exceptions\PageNotFoundException('Invalid content detected');
                    }
                }
            }
        }
    }

    /**
     * 🔒 SECURITY: Session validation
     */
    private function validateSession(RequestInterface $request)
    {
        $session = session();
        
        // Check for session hijacking
        $userAgent = $request->getUserAgent()->getAgentString();
        $clientIP = $request->getIPAddress();
        
        $storedUserAgent = $session->get('user_agent');
        $storedIP = $session->get('client_ip');
        
        if ($session->get('isLoggedIn')) {
            // First time storing session info
            if (!$storedUserAgent || !$storedIP) {
                $session->set('user_agent', $userAgent);
                $session->set('client_ip', $clientIP);
            } else {
                // Validate session consistency
                if ($storedUserAgent !== $userAgent) {
                    log_message('warning', "Session hijacking attempt detected - User Agent mismatch for IP: {$clientIP}");
                    $session->destroy();
                    throw new \CodeIgniter\Exceptions\PageNotFoundException('Session security violation');
                }
                
                // Optional: IP validation (can be disabled if users have dynamic IPs)
                // if ($storedIP !== $clientIP) {
                //     log_message('warning', "Session hijacking attempt detected - IP mismatch: {$storedIP} vs {$clientIP}");
                //     $session->destroy();
                //     throw new \CodeIgniter\Exceptions\PageNotFoundException('Session security violation');
                // }
            }
        }
    }
}