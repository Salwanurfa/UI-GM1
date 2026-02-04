<?php

/**
 * Unit Management Routes
 * Manage units/faculties/departments
 */

$routes->get('unit-management', 'Admin\\UnitManagement::index');
$routes->get('unit-management/get/(:num)', 'Admin\\UnitManagement::getUnit/$1');
$routes->post('unit-management/create', 'Admin\\UnitManagement::create');
$routes->post('unit-management/update/(:num)', 'Admin\\UnitManagement::update/$1');
$routes->post('unit-management/toggle-status/(:num)', 'Admin\\UnitManagement::toggleStatus/$1');
$routes->post('unit-management/delete/(:num)', 'Admin\\UnitManagement::delete/$1');
