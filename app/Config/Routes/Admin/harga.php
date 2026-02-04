<?php

/**
 * Manajemen Harga Routes
 * URL: /admin-pusat/manajemen-harga
 */

$routes->get('manajemen-harga', 'Admin\\Harga::index');
$routes->get('manajemen-harga/test-simple', 'Admin\\Harga::testSimple'); // Test view untuk debug
$routes->get('manajemen-harga/get/(:num)', 'Admin\\Harga::get/$1');
$routes->post('manajemen-harga/store', 'Admin\\Harga::store');
$routes->post('manajemen-harga/update/(:num)', 'Admin\\Harga::update/$1');
$routes->post('manajemen-harga/toggle-status/(:num)', 'Admin\\Harga::toggleStatus/$1');
$routes->post('manajemen-harga/delete/(:num)', 'Admin\\Harga::delete/$1');
$routes->delete('manajemen-harga/delete/(:num)', 'Admin\\Harga::delete/$1');
$routes->get('manajemen-harga/logs', 'Admin\\Harga::logs');
$routes->get('manajemen-harga/recent-changes', 'Admin\\Harga::recentChanges'); // JSON endpoint for polling
