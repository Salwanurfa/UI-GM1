<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

// Set default timezone to Asia/Jakarta (WIB) for the entire application
date_default_timezone_set('Asia/Jakarta');

/**
 * Helper function to format datetime with WIB timezone
 */
if (!function_exists('format_datetime_wib')) {
    function format_datetime_wib($datetime, $format = 'd/m/Y H:i') {
        if (empty($datetime)) {
            return '-';
        }
        
        try {
            $date = new DateTime($datetime);
            $date->setTimezone(new DateTimeZone('Asia/Jakarta'));
            return $date->format($format) . ' WIB';
        } catch (Exception $e) {
            return '-';
        }
    }
}

/**
 * Helper function to get current datetime in WIB
 */
if (!function_exists('now_wib')) {
    function now_wib($format = 'Y-m-d H:i:s') {
        $date = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
        return $date->format($format);
    }
}
