<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Default route
$routes->get('/', 'Home::index');

// Auth Routes (Public)
$routes->group('auth', function ($routes) {
    $routes->get('login', 'Auth::login');
    $routes->get('test-login', 'Auth::testLogin');
    $routes->post('process-login', 'Auth::processLogin');
    $routes->get('logout', 'Auth::logout');
});

// ================================================
// ADMIN ROUTES (Role: admin_pusat, super_admin)
// ================================================
$routes->group('admin-pusat', ['filter' => 'role:admin_pusat,super_admin'], function ($routes) {
    // Load all admin routes from separate files
    require APPPATH . 'Config/Routes/Admin/dashboard.php';
    require APPPATH . 'Config/Routes/Admin/harga.php';
    require APPPATH . 'Config/Routes/Admin/feature_toggle.php';
    require APPPATH . 'Config/Routes/Admin/user_management.php';
    require APPPATH . 'Config/Routes/Admin/unit_management.php';
    require APPPATH . 'Config/Routes/Admin/waste.php';
    require APPPATH . 'Config/Routes/Admin/waste_standardized.php';
    require APPPATH . 'Config/Routes/Admin/review.php';
    require APPPATH . 'Config/Routes/Admin/laporan.php';
    require APPPATH . 'Config/Routes/Admin/laporan_waste.php';
    require APPPATH . 'Config/Routes/Admin/manajemen_limbah_b3.php';
    require APPPATH . 'Config/Routes/Admin/indikator_uigm.php';
    require APPPATH . 'Config/Routes/Admin/profil.php';
    require APPPATH . 'Config/Routes/Admin/pengaturan.php';
    require APPPATH . 'Config/Routes/Admin/uigm_categories.php';
    require APPPATH . 'Config/Routes/Admin/transportation.php';
    require APPPATH . 'Config/Routes/Admin/reference.php';
    require APPPATH . 'Config/Routes/Admin/bukti_dukung.php';
    require APPPATH . 'Config/Routes/Admin/infrastructure.php';
    
    // LOGBOOK ROUTES - BERSIH DAN BARU
    $routes->get('logbook', 'Admin\\LogBook::index');
    $routes->post('logbook/save', 'Admin\\LogBook::save');
});

// ================================================
// USER ROUTES (Role: user)
// ================================================
$routes->group('user', ['filter' => 'role:user'], function ($routes) {
    // Dashboard
    $routes->get('dashboard', 'User\\Dashboard::index');
    $routes->get('/', 'User\\Dashboard::index');
    
    // Waste Management
    $routes->get('waste', 'User\\Waste::index');
    $routes->get('waste/get/(:num)', 'User\\Waste::get/$1');
    $routes->post('waste/save', 'User\\Waste::save');
    $routes->post('waste/edit/(:num)', 'User\\Waste::edit/$1');
    $routes->post('waste/delete/(:num)', 'User\\Waste::delete/$1'); // Changed from DELETE to POST
    $routes->delete('waste/delete/(:num)', 'User\\Waste::delete/$1'); // Keep DELETE for backward compatibility
    $routes->get('waste/export', 'User\\Waste::export');
    $routes->get('waste/export-pdf', 'User\\Waste::exportPdf');

    // Limbah B3
    $routes->get('limbah-b3', 'User\\LimbahB3::index');
    $routes->get('limbah-b3/get/(:num)', 'User\\LimbahB3::get/$1');
    $routes->post('limbah-b3/save', 'User\\LimbahB3::save');
    $routes->post('limbah-b3/edit/(:num)', 'User\\LimbahB3::edit/$1');
    $routes->post('limbah-b3/delete/(:num)', 'User\\LimbahB3::delete/$1');
    $routes->get('limbah-b3/master/(:num)', 'User\\LimbahB3::master/$1');
        $routes->get('limbah-b3/export-excel', 'User\\LimbahB3::exportExcel');
        $routes->get('limbah-b3/export-pdf', 'User\\LimbahB3::exportPdf');
    
    // Profile
    $routes->get('profile', 'User\\Profile::index');
    $routes->post('profile/update', 'User\\Profile::update');
    $routes->post('profile/change-password', 'User\\Profile::changePassword');
    
    // Dashboard API
    $routes->get('dashboard/api-stats', 'User\\Dashboard::apiStats');
});

// ================================================
// TPS ROUTES (Role: pengelola_tps)
// ================================================
$routes->group('pengelola-tps', ['filter' => 'role:pengelola_tps'], function ($routes) {
    // Dashboard
    $routes->get('dashboard', 'TPS\\Dashboard::index');
    $routes->get('/', 'TPS\\Dashboard::index');
    
    // Laporan Masuk dari User
    $routes->get('laporan-masuk', 'TPS\\LaporanMasuk::index');
    $routes->get('laporan-masuk/detail/(:num)', 'TPS\\LaporanMasuk::detail/$1');
    $routes->post('laporan-masuk/approve/(:num)', 'TPS\\LaporanMasuk::approve/$1');
    $routes->post('laporan-masuk/reject/(:num)', 'TPS\\LaporanMasuk::reject/$1');
    
    // Waste Management
    $routes->get('waste', 'TPS\\Waste::index');
    $routes->get('waste/form', 'TPS\\Waste::form'); // Form input data sampah
    $routes->get('waste/get/(:num)', 'TPS\\Waste::get/$1');
    $routes->post('waste/save', 'TPS\\Waste::save');
    $routes->post('waste/edit/(:num)', 'TPS\\Waste::edit/$1');
    $routes->post('waste/delete/(:num)', 'TPS\\Waste::delete/$1'); // Changed from DELETE to POST
    $routes->delete('waste/delete/(:num)', 'TPS\\Waste::delete/$1'); // Keep DELETE for backward compatibility
    $routes->get('waste/export', 'TPS\\Waste::export');
    $routes->get('waste/export-pdf', 'TPS\\Waste::exportPdf');

    // Limbah B3 TPS
    $routes->get('limbah-b3', 'TPS\\LimbahB3::index');
    $routes->post('limbah-b3/approve/(:num)', 'TPS\\LimbahB3::approve/$1');
    $routes->post('limbah-b3/reject/(:num)', 'TPS\\LimbahB3::reject/$1');
    
    // Profile
    $routes->get('profile', 'TPS\\Profile::index');
    $routes->post('profile/update', 'TPS\\Profile::update');
    $routes->post('profile/change-password', 'TPS\\Profile::changePassword');
});

// ================================================
// SECURITY ROUTES (Role: security)
// ================================================
$routes->group('security', ['filter' => 'role:security'], function ($routes) {
    // Dashboard
    $routes->get('dashboard', 'Security\\Dashboard::index');
    $routes->get('/', 'Security\\Dashboard::index');
    $routes->post('dashboard/delete/(:num)', 'Security\\Dashboard::deleteEntry/$1');
    $routes->get('dashboard/export-pdf', 'Security\\Dashboard::exportPdf');
    $routes->get('dashboard/export-excel', 'Security\\Dashboard::exportExcel');
    
    // Transportation Management
    $routes->get('transportation', 'Security\\Transportation::index');
    $routes->post('transportation/save', 'Security\\Transportation::save');
    $routes->post('transportation/delete/(:num)', 'Security\\Transportation::delete/$1');
    
    // Profile
    $routes->get('profile', 'Security\\Profile::index');
    $routes->post('profile/update', 'Security\\Profile::update');
});

// ================================================
// API ROUTES (Protected)
// ================================================
$routes->group('api', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard/stats', 'Api\\DashboardApi::getStats');
    $routes->get('waste/summary', 'Api\\WasteApi::getSummary');
    $routes->post('notifications/mark-read/(:num)', 'Api\\NotificationController::markAsRead/$1');
});

// ================================================
// FILE SERVING ROUTES (Public access to uploaded files)
// ================================================
$routes->get('uploads/uigm_evidence/(:any)', function($filename) {
    $filePath = WRITEPATH . 'uploads/uigm_evidence/' . $filename;
    
    if (!file_exists($filePath)) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('File not found');
    }
    
    // Get file info
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $filePath);
    finfo_close($finfo);
    
    // Set headers
    header('Content-Type: ' . $mimeType);
    header('Content-Length: ' . filesize($filePath));
    header('Cache-Control: public, max-age=3600');
    
    // Output file
    readfile($filePath);
    exit;
});

// ================================================
// FALLBACK & ERROR HANDLING
// ================================================
$routes->set404Override(function() {
    $user = session()->get('user');
    
    if (!$user || !session()->get('isLoggedIn')) {
        // Return view instead of redirect for 404
        echo view('errors/html/error_404', [
            'message' => 'Halaman tidak ditemukan. Silakan login terlebih dahulu.',
            'login_url' => base_url('/auth/login')
        ]);
        return;
    }
    
    // For logged in users, show 404 page instead of redirecting
    echo view('errors/html/error_404', [
        'message' => 'Halaman tidak ditemukan.',
        'login_url' => null
    ]);
});