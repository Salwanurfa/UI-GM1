<?php

// Transportation Management Routes for Admin
$routes->group('transportation', function ($routes) {
    // Main transportation statistics page
    $routes->get('/', 'Admin\\Transportation::index');
    
    // Export functionality
    $routes->get('export-pdf', 'Admin\\Transportation::exportPdf');
    $routes->get('export-excel', 'Admin\\Transportation::exportExcel');
    
    // Laporan page (can be added later if needed)
    $routes->get('laporan', 'Admin\\Transportation::laporan');
});