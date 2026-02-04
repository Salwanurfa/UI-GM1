<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Enhanced Session Security Filter
 * 
 * Provides additional security layers for session management:
 * - Session timeout validation
 * - IP address validation
 * - User agent validation
 * - Session regeneration
 */
class SessionSecurityFilter implements FilterInterface
{
    private const SESSION_TIMEOUT = 3600; // 1 hour
    private const MAX_IDLE_TIME = 1800;   // 30 minutes
    
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // Skip if not logged in
        if (!$session->get('isLoggedIn')) {
            return null;
        }
        
        // 1. Check session timeout
        if ($this->isSessionExpired($session)) {
            $this->destroySession($session);
            return redirect()->to('/auth/login')
                ->with('error', 'Session telah berakhir. Silakan login kembali.');
        }
        
        // 2. Check idle timeout
        if ($this->isIdleTimeoutExceeded($session)) {
            $this->destroySession($session);
            return redirect()->to('/auth/login')
                ->with('error', 'Session timeout karena tidak aktif. Silakan login kembali.');
        }
        
        // 3. Validate IP address (optional - can be disabled for mobile users)
        if ($this->shouldValidateIP() && !$this->isValidIP($session, $request)) {
            $this->destroySession($session);
            log_message('warning', 'Session hijacking attempt detected - IP mismatch');
            return redirect()->to('/auth/login')
                ->with('error', 'Akses ditolak karena alasan keamanan.');
        }
        
        // 4. Validate User Agent (basic check)
        if (!$this->isValidUserAgent($session, $request)) {
            $this->destroySession($session);
            log_message('warning', 'Session hijacking attempt detected - User Agent mismatch');
            return redirect()->to('/auth/login')
                ->with('error', 'Akses ditolak karena alasan keamanan.');
        }
        
        // 5. Update last activity
        $session->set('last_activity', time());
        
        // 6. Regenerate session ID periodically (every 15 minutes)
        if ($this->shouldRegenerateSession($session)) {
            $session->regenerate();
            $session->set('last_regeneration', time());
        }
        
        return null;
    }
    
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
    
    /**
     * Check if session has expired
     */
    private function isSessionExpired($session): bool
    {
        $loginTime = $session->get('login_time');
        if (!$loginTime) {
            return true;
        }
        
        return (time() - $loginTime) > self::SESSION_TIMEOUT;
    }
    
    /**
     * Check if idle timeout exceeded
     */
    private function isIdleTimeoutExceeded($session): bool
    {
        $lastActivity = $session->get('last_activity');
        if (!$lastActivity) {
            return true;
        }
        
        return (time() - $lastActivity) > self::MAX_IDLE_TIME;
    }
    
    /**
     * Validate IP address
     */
    private function isValidIP($session, $request): bool
    {
        $sessionIP = $session->get('ip_address');
        $currentIP = $request->getIPAddress();
        
        return $sessionIP === $currentIP;
    }
    
    /**
     * Validate User Agent
     */
    private function isValidUserAgent($session, $request): bool
    {
        $sessionUA = $session->get('user_agent');
        $currentUA = $request->getUserAgent()->getAgentString();
        
        return $sessionUA === $currentUA;
    }
    
    /**
     * Check if should validate IP (can be configured)
     */
    private function shouldValidateIP(): bool
    {
        // Can be made configurable via environment or config
        return env('SESSION_VALIDATE_IP', false);
    }
    
    /**
     * Check if should regenerate session
     */
    private function shouldRegenerateSession($session): bool
    {
        $lastRegeneration = $session->get('last_regeneration');
        if (!$lastRegeneration) {
            return true;
        }
        
        return (time() - $lastRegeneration) > 900; // 15 minutes
    }
    
    /**
     * Safely destroy session
     */
    private function destroySession($session): void
    {
        $session->destroy();
    }
}