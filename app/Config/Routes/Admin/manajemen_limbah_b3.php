<?php

/**
 * Manajemen Master Limbah B3 Routes
 * File dipanggil dalam group 'admin-pusat', jadi URL menjadi: /admin-pusat/manajemen-limbah-b3
 * 
 * PENTING: Rute di file ini TIDAK perlu prefix 'admin-pusat' karena sudah di dalam group
 */

// List & View
$routes->get('manajemen-limbah-b3', 'Admin\ManajemenLimbahB3::index');
$routes->get('manajemen-limbah-b3/get/(:num)', 'Admin\ManajemenLimbahB3::get/$1');

// Create & Store
$routes->get('manajemen-limbah-b3/create', 'Admin\ManajemenLimbahB3::create');
$routes->post('manajemen-limbah-b3/store', 'Admin\ManajemenLimbahB3::store');

// Update
$routes->post('manajemen-limbah-b3/update/(:num)', 'Admin\ManajemenLimbahB3::update/$1');

// Delete
$routes->post('manajemen-limbah-b3/delete/(:num)', 'Admin\ManajemenLimbahB3::delete/$1');
$routes->delete('manajemen-limbah-b3/delete/(:num)', 'Admin\ManajemenLimbahB3::delete/$1');

// Export PDF
$routes->get('manajemen-limbah-b3/export-pdf', 'Admin\ManajemenLimbahB3::exportPdf');

// Logs & History
$routes->get('manajemen-limbah-b3/logs', 'Admin\ManajemenLimbahB3::logs');