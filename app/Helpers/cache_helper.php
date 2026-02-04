<?php

/**
 * Cache Helper
 * 
 * Override untuk mengatasi masalah permission cache di Windows
 */

if (!function_exists('is_really_writable')) {
    /**
     * Override is_really_writable untuk Windows
     * 
     * @param string $file
     * @return bool
     */
    function is_really_writable($file)
    {
        // Jika file tidak ada, cek parent directory
        if (!file_exists($file)) {
            $file = dirname($file);
        }

        // Untuk Windows, gunakan is_writable langsung
        if (DIRECTORY_SEPARATOR === '\\') {
            // Coba buat file test
            $testFile = rtrim($file, '/\\') . DIRECTORY_SEPARATOR . uniqid('test_', true) . '.tmp';
            
            if (@file_put_contents($testFile, 'test') !== false) {
                @unlink($testFile);
                return true;
            }
            
            // Fallback ke is_writable
            return is_writable($file);
        }

        // Untuk Unix/Linux, gunakan logika original
        return is_writable($file);
    }
}
