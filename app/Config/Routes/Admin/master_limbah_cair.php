<?php

/**
 * Master Limbah Cair Routes (Admin Pusat)
 * File dipanggil dalam group 'admin-pusat', jadi URL menjadi: /admin-pusat/master-limbah-cair
 * 
 * PENTING: Rute di file ini TIDAK perlu prefix 'admin-pusat' karena sudah di dalam group
 */

// List & View
$routes->get('master-limbah-cair', 'Admin\MasterLimbahCair::index');
$routes->get('master-limbah-cair/get/(:num)', 'Admin\MasterLimbahCair::get/$1');

// Create & Store
$routes->post('master-limbah-cair/store', 'Admin\MasterLimbahCair::store');

// Update
$routes->post('master-limbah-cair/update/(:num)', 'Admin\MasterLimbahCair::update/$1');

// Delete
$routes->post('master-limbah-cair/delete/(:num)', 'Admin\MasterLimbahCair::delete/$1');
$routes->delete('master-limbah-cair/delete/(:num)', 'Admin\MasterLimbahCair::delete/$1');
