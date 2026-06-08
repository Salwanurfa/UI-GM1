<?php

// Transportation Management Routes for Admin
$routes->group('transportation', function ($routes) {
    // Main management page (CRUD)
    $routes->get('/', 'Admin\\Transportation::index');
    
    // Manajemen Master Data - Kategori & Bahan Bakar
    $routes->get('manajemen', 'Admin\\Transportation::manajemen');
    $routes->post('tambah-kategori', 'Admin\\Transportation::tambahKategori');
    $routes->get('hapus-kategori/(:num)', 'Admin\\Transportation::hapusKategori/$1');
    $routes->post('tambah-bahan-bakar', 'Admin\\Transportation::tambahBahanBakar');
    $routes->get('hapus-bahan-bakar/(:num)', 'Admin\\Transportation::hapusBahanBakar/$1');
    
    // Indikator Efisiensi Transportasi
    $routes->get('indikator', 'Admin\\Transportation::indikator');
    $routes->post('simpan-indikator', 'Admin\\Transportation::simpanIndikator');
    $routes->get('hapus-indikator/(:num)', 'Admin\\Transportation::hapusIndikator/$1');
    
    // Analisis & Skor UI GreenMetric (Unified Page with Tabs)
    $routes->get('analisis-skor', 'Admin\\Transportation::analisisSkor');
    $routes->post('simpan-populasi', 'Admin\\Transportation::simpanPopulasi');
    $routes->post('simpan-parkir', 'Admin\\Transportation::simpanParkir');
    $routes->post('simpan-pedestrian', 'Admin\\Transportation::simpanPedestrian');
    $routes->get('hapus-pedestrian/(:num)', 'Admin\\Transportation::hapusPedestrian/$1');
    $routes->post('upload-dokumen', 'Admin\\Transportation::uploadDokumen');
    $routes->get('download-dokumen/(:num)', 'Admin\\Transportation::downloadDokumen/$1');
    $routes->get('hapus-dokumen/(:num)', 'Admin\\Transportation::hapusDokumen/$1');
    
    // CRUD Operations
    $routes->get('edit/(:num)', 'Admin\\Transportation::edit/$1');
    $routes->post('update/(:num)', 'Admin\\Transportation::update/$1');
    $routes->get('delete/(:num)', 'Admin\\Transportation::delete/$1');
    $routes->post('delete/(:num)', 'Admin\\Transportation::delete/$1');
    
    // Export functionality
    $routes->get('export-pdf', 'Admin\\Transportation::exportPdf');
    $routes->get('export-excel', 'Admin\\Transportation::exportExcel');
    
    // Statistics/Laporan page
    $routes->get('statistics', 'Admin\\Transportation::statistics');
    $routes->get('laporan', 'Admin\\Transportation::statistics'); // Alias
    
    // Monthly Summary Report (NEW - Informatif)
    $routes->get('summary', 'Admin\\Transportation::summary');
    $routes->get('ringkasan-bulanan', 'Admin\\Transportation::summary'); // Alias
    
    // Comprehensive Report with Filters (NEWEST - Meniru Laporan Sampah)
    $routes->get('laporan-lengkap', 'Admin\\Transportation::laporan');
    $routes->post('get-detail-laporan', 'Admin\\Transportation::getDetailLaporan');
    $routes->get('export-laporan-excel', 'Admin\\Transportation::exportLaporanExcel');
    $routes->get('export-laporan-pdf', 'Admin\\Transportation::exportLaporanPdf');
    
    // Log Harian Kendaraan (MOVED TO SECURITY)
    // Routes removed - now handled by Security role
    
    // History Page (Mirip Laporan Waste dengan Rekap Mingguan)
    $routes->get('history', 'Admin\\Transportation::history');
    $routes->get('export-history-excel', 'Admin\\Transportation::exportLaporanExcel');
    $routes->get('export-history-pdf', 'Admin\\Transportation::exportLaporanPdf');
});