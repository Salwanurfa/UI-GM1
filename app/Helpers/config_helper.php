<?php

if (!function_exists('safeConfig')) {
    /**
     * Safely get configuration value with default
     */
    function safeConfig(string $key, $default = null)
    {
        try {
            return config($key) ?? $default;
        } catch (\Exception $e) {
            log_message('error', 'Config error for key ' . $key . ': ' . $e->getMessage());
            return $default;
        }
    }
}

if (!function_exists('getAppConfig')) {
    /**
     * Get application configuration safely
     */
    function getAppConfig(string $key, $default = null)
    {
        try {
            $config = config('App');
            return $config->{$key} ?? $default;
        } catch (\Exception $e) {
            log_message('error', 'App config error for key ' . $key . ': ' . $e->getMessage());
            return $default;
        }
    }
}

if (!function_exists('getDatabaseConfig')) {
    /**
     * Get database configuration safely
     */
    function getDatabaseConfig(string $key, $default = null)
    {
        try {
            $config = config('Database');
            return $config->default[$key] ?? $default;
        } catch (\Exception $e) {
            log_message('error', 'Database config error for key ' . $key . ': ' . $e->getMessage());
            return $default;
        }
    }
}

if (!function_exists('isDebugMode')) {
    /**
     * Check if application is in debug mode
     */
    function isDebugMode(): bool
    {
        return ENVIRONMENT === 'development' || getAppConfig('debug', false);
    }
}

if (!function_exists('getBaseURL')) {
    /**
     * Get base URL safely
     */
    function getBaseURL(): string
    {
        return getAppConfig('baseURL', '/');
    }
}

if (!function_exists('getAppName')) {
    /**
     * Get application name
     */
    function getAppName(): string
    {
        return getAppConfig('appName', 'CodeIgniter App');
    }
}

if (!function_exists('getAppVersion')) {
    /**
     * Get application version
     */
    function getAppVersion(): string
    {
        return getAppConfig('appVersion', '1.0.0');
    }
}

if (!function_exists('getTimezone')) {
    /**
     * Get application timezone
     */
    function getTimezone(): string
    {
        return getAppConfig('appTimezone', 'UTC');
    }
}

if (!function_exists('getMaxFileSize')) {
    /**
     * Get maximum file upload size
     */
    function getMaxFileSize(): int
    {
        return getAppConfig('maxFileSize', 2048); // 2MB default
    }
}

if (!function_exists('getAllowedFileTypes')) {
    /**
     * Get allowed file types for upload
     */
    function getAllowedFileTypes(): array
    {
        return getAppConfig('allowedFileTypes', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx']);
    }
}

if (!function_exists('getSessionTimeout')) {
    /**
     * Get session timeout in seconds
     */
    function getSessionTimeout(): int
    {
        return getAppConfig('sessionTimeout', 1800); // 30 minutes default
    }
}

if (!function_exists('isMaintenanceMode')) {
    /**
     * Check if application is in maintenance mode
     */
    function isMaintenanceMode(): bool
    {
        return getAppConfig('maintenanceMode', false);
    }
}

if (!function_exists('getMaintenanceMessage')) {
    /**
     * Get maintenance mode message
     */
    function getMaintenanceMessage(): string
    {
        return getAppConfig('maintenanceMessage', 'Aplikasi sedang dalam pemeliharaan. Silakan coba lagi nanti.');
    }
}