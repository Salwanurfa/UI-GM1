<?php

/**
 * Routes for Standardized Waste Management
 */

// Standardized Waste Dashboard
$routes->get('waste-standardized', 'Admin\\WasteStandardized::index');
$routes->get('waste-standardized/dashboard', 'Admin\\WasteStandardized::index');

// Waste Data Management
$routes->get('waste/get/(:num)', 'Admin\\WasteStandardized::getWasteData/$1');
$routes->post('waste/update-mapping', 'Admin\\WasteStandardized::updateMapping');
$routes->post('waste/bulk-update-categories', 'Admin\\WasteStandardized::bulkUpdateCategories');

// Export Functions
$routes->get('waste-standardized/export', 'Admin\\WasteStandardized::export');
$routes->get('waste-standardized/export/excel', 'Admin\\WasteStandardized::export');
$routes->get('waste-standardized/export/pdf', 'Admin\\WasteStandardized::export');

// Category Management
$routes->get('waste-categories/standard', 'Admin\\WasteCategoryStandard::index');
$routes->post('waste-categories/standard/save', 'Admin\\WasteCategoryStandard::save');
$routes->post('waste-categories/standard/edit/(:num)', 'Admin\\WasteCategoryStandard::edit/$1');
$routes->post('waste-categories/standard/delete/(:num)', 'Admin\\WasteCategoryStandard::delete/$1');

// UIGM Integration
$routes->get('waste-standardized/uigm-compliance', 'Admin\\WasteStandardized::uigmCompliance');
$routes->get('waste-standardized/uigm-data/(:segment)', 'Admin\\WasteStandardized::getUigmData/$1');