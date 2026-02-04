<?php

/**
 * JSON Helper Functions
 * Helper untuk menangani JSON data dengan aman
 */

if (!function_exists('safe_json_decode')) {
    /**
     * Safely decode JSON data with proper type handling
     * 
     * @param mixed $data The data to decode
     * @param bool $assoc Return associative array instead of object
     * @return array|object|null
     */
    function safe_json_decode($data, $assoc = true)
    {
        // Handle null or empty data
        if (empty($data)) {
            return $assoc ? [] : new stdClass();
        }

        // If it's already an array and we want array, return as is
        if (is_array($data) && $assoc) {
            return $data;
        }

        // If it's already an object and we want object, return as is
        if (is_object($data) && !$assoc) {
            return $data;
        }

        // If it's an object but we want array, convert it
        if (is_object($data) && $assoc) {
            return json_decode(json_encode($data), true);
        }

        // If it's an array but we want object, convert it
        if (is_array($data) && !$assoc) {
            return json_decode(json_encode($data), false);
        }

        // If it's a string, try to decode it
        if (is_string($data)) {
            $decoded = json_decode($data, $assoc);

            // Check for JSON errors
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }

            // If JSON decode fails, return empty structure
            return $assoc ? [] : new stdClass();
        }

        // For any other type, return empty structure
        return $assoc ? [] : new stdClass();
    }
}

if (!function_exists('safe_json_encode')) {
    /**
     * Safely encode data to JSON
     * 
     * @param mixed $data The data to encode
     * @return string
     */
    function safe_json_encode($data)
    {
        if (empty($data)) {
            return '{}';
        }

        $encoded = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $encoded;
        }

        // If encoding fails, return empty JSON object
        return '{}';
    }
}
