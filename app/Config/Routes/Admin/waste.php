<?php

/**
 * Waste Management Routes
 * URL: /admin-pusat/waste
 */

$routes->get('waste', 'Admin\\Waste::index');
$routes->get('waste/export', 'Admin\\Waste::export');
$routes->post('waste/store', 'Admin\\Waste::store');
$routes->post('waste/update/(:num)', 'Admin\\Waste::update/$1');
$routes->post('waste/approve/(:num)', 'Admin\\Waste::approve/$1');
$routes->post('waste/reject/(:num)', 'Admin\\Waste::reject/$1');
$routes->post('waste/delete/(:num)', 'Admin\\Waste::delete/$1');
$routes->delete('waste/delete/(:num)', 'Admin\\Waste::delete/$1');
