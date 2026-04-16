<?php

// Reference Management Routes
$routes->get('reference', 'Admin\\Reference::index');
$routes->post('reference/add-category', 'Admin\\Reference::addCategory');
$routes->post('reference/add-fuel', 'Admin\\Reference::addFuel');
$routes->get('reference/delete-category/(:num)', 'Admin\\Reference::deleteCategory/$1');
$routes->get('reference/delete-fuel/(:num)', 'Admin\\Reference::deleteFuel/$1');