<?php

/**
 * User Management Routes
 * URL: /admin-pusat/user-management
 */

$routes->get('user-management', 'Admin\\UserManagement::index');
$routes->get('user-management/get/(:num)', 'Admin\\UserManagement::getUser/$1');
$routes->post('user-management/create', 'Admin\\UserManagement::create');
$routes->post('user-management/update/(:num)', 'Admin\\UserManagement::update/$1');
$routes->post('user-management/toggle-status/(:num)', 'Admin\\UserManagement::toggleStatus/$1');
$routes->delete('user-management/delete/(:num)', 'Admin\\UserManagement::delete/$1');
$routes->post('user-management/import', 'Admin\\UserManagement::import');
$routes->get('user-management/download-template', 'Admin\\UserManagement::downloadTemplate');
