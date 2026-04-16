<?php

// Infrastructure Management Routes for Admin
$routes->group('infrastructure', function ($routes) {
    // Main infrastructure & population management page
    $routes->get('/', 'Admin\\Infrastructure::index');
    
    // Infrastructure data management
    $routes->get('infrastructure-form', 'Admin\\Infrastructure::infrastructureForm');
    $routes->get('infrastructure-form/(:num)', 'Admin\\Infrastructure::infrastructureForm/$1');
    $routes->post('save-infrastructure', 'Admin\\Infrastructure::saveInfrastructure');
    $routes->post('delete-infrastructure/(:num)', 'Admin\\Infrastructure::deleteInfrastructure/$1');
    
    // Population data management
    $routes->get('population-form', 'Admin\\Infrastructure::populationForm');
    $routes->get('population-form/(:num)', 'Admin\\Infrastructure::populationForm/$1');
    $routes->post('save-population', 'Admin\\Infrastructure::savePopulation');
    $routes->post('delete-population/(:num)', 'Admin\\Infrastructure::deletePopulation/$1');
    
    // Laporan page (can be added later if needed)
    $routes->get('laporan', 'Admin\\Infrastructure::laporan');
});