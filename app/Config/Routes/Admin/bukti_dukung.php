<?php

// Bukti Dukung Routes
$routes->get('bukti-dukung', 'Admin\\BuktiDukung::index');
$routes->post('bukti-dukung/upload', 'Admin\\BuktiDukung::upload');
$routes->get('bukti-dukung/preview/(:num)', 'Admin\\BuktiDukung::preview/$1');
$routes->get('bukti-dukung/download/(:num)', 'Admin\\BuktiDukung::download/$1');
$routes->get('bukti-dukung/delete/(:num)', 'Admin\\BuktiDukung::delete/$1');