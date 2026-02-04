<?php

/**
 * Feature Toggle Routes
 * URL: /admin-pusat/feature-toggle
 */

$routes->get('feature-toggle', 'Admin\\FeatureToggle::index');
$routes->post('feature-toggle/toggle', 'Admin\\FeatureToggle::toggle');
$routes->get('feature-toggle/get-feature/(:segment)', 'Admin\\FeatureToggle::getFeature/$1');
$routes->post('feature-toggle/update-config', 'Admin\\FeatureToggle::updateConfig');
$routes->post('feature-toggle/bulk-toggle', 'Admin\\FeatureToggle::bulkToggle');
$routes->get('feature-toggle/logs', 'Admin\\FeatureToggle::logs');
