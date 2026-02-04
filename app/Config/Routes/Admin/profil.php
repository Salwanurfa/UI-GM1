<?php

/**
 * Profil Routes
 * URL: /admin-pusat/profil
 */

$routes->get('profil', 'Admin\\Profil::index');
$routes->post('profil/update', 'Admin\\Profil::update');
$routes->post('profil/change-password', 'Admin\\Profil::changePassword');
