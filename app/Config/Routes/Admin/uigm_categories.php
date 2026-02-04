<?php

// UIGM Categories Routes

// Infrastructure
$routes->get('infrastructure', 'Admin\Infrastructure::index');
$routes->get('infrastructure/laporan', 'Admin\Infrastructure::laporan');

// Energy & Climate
$routes->get('energy', 'Admin\Energy::index');
$routes->get('energy/laporan', 'Admin\Energy::laporan');

// Water Management
$routes->get('water', 'Admin\Water::index');
$routes->get('water/laporan', 'Admin\Water::laporan');

// Transportation
$routes->get('transportation', 'Admin\Transportation::index');
$routes->get('transportation/laporan', 'Admin\Transportation::laporan');

// Education & Research
$routes->get('education', 'Admin\Education::index');
$routes->get('education/laporan', 'Admin\Education::laporan');
