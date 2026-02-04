<?php

/**
 * Pengaturan Routes
 * URL: /admin-pusat/pengaturan
 */

$routes->get('pengaturan', 'Admin\\Pengaturan::index');
$routes->post('pengaturan/update', 'Admin\\Pengaturan::update');
