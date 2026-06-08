<?php

/**
 * Manajemen Limbah Cair Routes (Admin Pusat)
 * File dipanggil dalam group 'admin-pusat', jadi URL menjadi: /admin-pusat/manajemen-limbah-cair
 * 
 * PENTING: Rute di file ini TIDAK perlu prefix 'admin-pusat' karena sudah di dalam group
 */

// List & View
$routes->get('manajemen-limbah-cair', 'Admin\ManajemenLimbahCair::index');
$routes->get('manajemen-limbah-cair/get/(:num)', 'Admin\ManajemenLimbahCair::get/$1');

// Approve & Reject
$routes->post('manajemen-limbah-cair/approve/(:num)', 'Admin\ManajemenLimbahCair::approve/$1');
$routes->post('manajemen-limbah-cair/reject/(:num)', 'Admin\ManajemenLimbahCair::reject/$1');

// Export
$routes->get('manajemen-limbah-cair/export-pdf', 'Admin\ManajemenLimbahCair::exportPdf');
$routes->get('manajemen-limbah-cair/export-excel', 'Admin\ManajemenLimbahCair::exportExcel');
