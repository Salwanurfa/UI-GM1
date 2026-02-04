<?php

/**
 * Admin Dashboard Routes
 * URL: /admin-pusat/dashboard
 */

$routes->get('dashboard', 'Admin\\Dashboard::index');
$routes->get('/', 'Admin\\Dashboard::index');
