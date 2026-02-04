<?php

/**
 * Laporan Routes
 * URL: /admin-pusat/laporan
 */

$routes->get('laporan', 'Admin\\Laporan::index');
$routes->get('laporan/export', 'Admin\\Laporan::export');
$routes->get('laporan-waste', 'Admin\\LaporanWaste::index');
$routes->get('laporan-waste/export-csv', 'Admin\\LaporanWaste::exportCsv');
$routes->get('laporan-waste/export-pdf', 'Admin\\LaporanWaste::exportPdf');

// Rekap Sampah Routes
$routes->get('laporan/rekap-sampah', 'Admin\\Laporan::rekapSampah');
$routes->get('laporan/rekap-sampah/data', 'Admin\\Laporan::rekapSampahData');
$routes->post('laporan/confirm/(:num)', 'Admin\\Laporan::confirmReport/$1');

// Rekap Unit Routes
$routes->get('laporan/rekap-unit', 'Admin\\Laporan::rekapUnit');
$routes->get('laporan/rekap-unit/data', 'Admin\\Laporan::rekapUnitData');
