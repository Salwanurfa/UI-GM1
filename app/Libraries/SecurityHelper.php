<?php

namespace App\Libraries;

/**
 * Security Helper Library
 * 
 * Provides security utilities for input validation, sanitization, and protection
 */
class SecurityHelper
{
    /**
     * Sanitize input data to prevent XSS
     */
    public static function sanitizeInput($data, $allowHtml = false): string
    {
        if (is_array($data)) {
            return array_map([self::class, 'sanitizeInput'], $data);
        }
        
        // Remove null bytes
        $data = str_replace(chr(0), '', $data);
        
        if (!$allowHtml) {
            // Strip all HTML tags
            $data = strip_tags($data);
        } else {
            // Allow only safe HTML tags
            $allowedTags = '<p><br><strong><em><u><ol><ul><li><h1><h2><h3><h4><h5><h6>';
            $data = strip_tags($data, $allowedTags);
        }
        
        // Convert special characters to HTML entities
        $data = htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        return trim($data);
    }
    
    /**
     * Validate and sanitize numeric input
     */
    public static function sanitizeNumeric($value, $type = 'int', $min = null, $max = null)
    {
        if ($type === 'float' || $type === 'decimal') {
            $value = filter_var($value, FILTER_VALIDATE_FLOAT);
        } else {
            $value = filter_var($value, FILTER_VALIDATE_INT);
        }
        
        if ($value === false) {
            return null;
        }
        
        if ($min !== null && $value < $min) {
            return null;
        }
        
        if ($max !== null && $value > $max) {
            return null;
        }
        
        return $value;
    }
    
    /**
     * Validate email address
     */
    public static function validateEmail(string $email): ?string
    {
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        return $email !== false ? $email : null;
    }
    
    /**
     * Generate secure random token
     */
    public static function generateSecureToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }
    
    /**
     * Validate CSRF token
     */
    public static function validateCSRF(string $token): bool
    {
        $sessionToken = session()->get('csrf_token');
        return $sessionToken && hash_equals($sessionToken, $token);
    }
    
    /**
     * Generate CSRF token
     */
    public static function generateCSRF(): string
    {
        $token = self::generateSecureToken();
        session()->set('csrf_token', $token);
        return $token;
    }
    
    /**
     * Validate file upload security
     */
    public static function validateFileUpload($file, array $allowedTypes = [], int $maxSize = 2097152): array
    {
        $result = [
            'valid' => false,
            'error' => null,
            'sanitized_name' => null
        ];
        
        if (!$file || !$file->isValid()) {
            $result['error'] = 'File tidak valid atau tidak ada file yang diupload';
            return $result;
        }
        
        // Check file size
        if ($file->getSize() > $maxSize) {
            $result['error'] = 'Ukuran file terlalu besar. Maksimal ' . number_format($maxSize / 1024 / 1024, 1) . 'MB';
            return $result;
        }
        
        // Check file type
        if (!empty($allowedTypes)) {
            $fileType = $file->getMimeType();
            if (!in_array($fileType, $allowedTypes)) {
                $result['error'] = 'Tipe file tidak diizinkan. Hanya: ' . implode(', ', $allowedTypes);
                return $result;
            }
        }
        
        // Sanitize filename
        $originalName = $file->getName();
        $sanitizedName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
        $sanitizedName = self::generateSecureToken(8) . '_' . $sanitizedName;
        
        $result['valid'] = true;
        $result['sanitized_name'] = $sanitizedName;
        
        return $result;
    }
    
    /**
     * Check for SQL injection patterns
     */
    public static function detectSQLInjection(string $input): bool
    {
        $patterns = [
            '/(\b(SELECT|INSERT|UPDATE|DELETE|DROP|CREATE|ALTER|EXEC|UNION|SCRIPT)\b)/i',
            '/(\b(OR|AND)\s+\d+\s*=\s*\d+)/i',
            '/(\b(OR|AND)\s+[\'"]?\w+[\'"]?\s*=\s*[\'"]?\w+[\'"]?)/i',
            '/(--|#|\/\*|\*\/)/i',
            '/(\bxp_cmdshell\b)/i'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Rate limiting check with safe cache key
     */
    public static function checkRateLimit(string $key, int $maxAttempts = 5, int $timeWindow = 300): bool
    {
        // Sanitize cache key to remove reserved characters
        $safeKey = self::sanitizeCacheKey($key);
        
        try {
            $cache = \Config\Services::cache();
            $attempts = $cache->get($safeKey) ?? 0;
            
            if ($attempts >= $maxAttempts) {
                return false;
            }
            
            $cache->save($safeKey, $attempts + 1, $timeWindow);
            return true;
        } catch (\Exception $e) {
            // Fallback: If cache service not available, allow the request
            // This prevents breaking the application if cache is misconfigured
            log_message('warning', 'Cache service unavailable for rate limiting: ' . $e->getMessage());
            return true;
        }
    }
    
    /**
     * Sanitize cache key to remove reserved characters
     */
    private static function sanitizeCacheKey(string $key): string
    {
        // Remove or replace reserved characters: {}()/\@:
        $sanitized = preg_replace('/[{}()\\/\\\\@:]/', '_', $key);
        
        // Ensure key is not too long (max 250 chars for most cache drivers)
        if (strlen($sanitized) > 200) {
            $sanitized = substr($sanitized, 0, 150) . '_' . md5($key);
        }
        
        return $sanitized;
    }
    
    /**
     * Log security event
     */
    public static function logSecurityEvent(string $event, array $data = []): void
    {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'user_id' => session()->get('user')['id'] ?? null,
            'data' => $data
        ];
        
        log_message('warning', 'SECURITY_EVENT: ' . json_encode($logData));
    }
}