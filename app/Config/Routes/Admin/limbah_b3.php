<?php

/**
 * Limbah B3 Routes (Admin)
 * URL: /admin-pusat/limbah-b3
 */

$routes->get('limbah-b3', 'Admin\\LimbahB3::index');
$routes->get('limbah-b3/create', 'Admin\\LimbahB3::create');
$routes->post('limbah-b3/store', 'Admin\\LimbahB3::store');
$routes->post('limbah-b3/approve/(:num)', 'Admin\\LimbahB3::approve/$1');

