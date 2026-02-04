<?php

/**
 * Review System Routes
 * URL: /admin-pusat/review
 */

$routes->get('review', 'Admin\\Review::index');
$routes->post('review/approve/(:num)', 'Admin\\Review::approve/$1');
$routes->post('review/reject/(:num)', 'Admin\\Review::reject/$1');
$routes->get('review/detail/(:num)', 'Admin\\Review::detail/$1');
