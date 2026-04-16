<?php

/**
 * Admin Routes - Indikator UIGM
 * Routes untuk mengelola indikator UI GreenMetric
 */

// Indikator UIGM Routes
$routes->get('indikator-uigm', 'Admin\\IndikatorUigm::index');
$routes->get('indikator-uigm/category/(:segment)', 'Admin\\IndikatorUigm::category/$1');
$routes->get('indikator-uigm/detail/(:segment)', 'Admin\\IndikatorUigm::detail/$1');
$routes->get('indikator-uigm/category-detail/(:segment)', 'Admin\\IndikatorUigm::categoryDetail/$1'); // Legacy route
$routes->post('indikator-uigm/upload-evidence', 'Admin\\IndikatorUigm::uploadEvidence');
$routes->post('indikator-uigm/remove-evidence/(:num)', 'Admin\\IndikatorUigm::removeEvidence/$1');
$routes->post('indikator-uigm/update-target', 'Admin\\IndikatorUigm::updateTarget');
$routes->get('indikator-uigm/export', 'Admin\\IndikatorUigm::export');

// New UIGM indicator mapping API
$routes->get('indikator-uigm/get-uigm-indicator-data', 'Admin\\IndikatorUigm::getUIGMIndicatorData');
$routes->get('indikator-uigm/get-detailed-recap-data', 'Admin\\IndikatorUigm::getDetailedRecapData');
$routes->get('indikator-uigm/get-categorized-data', 'Admin\\IndikatorUigm::getCategorizedData');
$routes->get('indikator-uigm/get-standardized-categorized-data', 'Admin\\IndikatorUigm::getStandardizedCategorizedData');
$routes->get('indikator-uigm/get-category-summary', 'Admin\\IndikatorUigm::getCategorySummary');
$routes->get('indikator-uigm/debug-data', 'Admin\\IndikatorUigm::debugData');
$routes->get('indikator-uigm/check-orphaned-data', 'Admin\\IndikatorUigm::checkOrphanedData');
$routes->post('indikator-uigm/clean-orphaned-data', 'Admin\\IndikatorUigm::cleanOrphanedData');
$routes->get('indikator-uigm/test-connection', 'Admin\\IndikatorUigm::testConnection');
$routes->get('indikator-uigm/get-standardized-data', 'Admin\\IndikatorUigm::getStandardizedData');
$routes->get('indikator-uigm/get-waste-data-for-linking', 'Admin\\IndikatorUigm::getWasteDataForLinking');
$routes->post('indikator-uigm/link-waste-data', 'Admin\\IndikatorUigm::linkWasteData');

// Legacy routes for backward compatibility
$routes->post('indikator-uigm/save', 'Admin\\IndikatorUigm::save');
$routes->get('indikator-uigm/get/(:num)', 'Admin\\IndikatorUigm::get/$1');
$routes->post('indikator-uigm/edit/(:num)', 'Admin\\IndikatorUigm::edit/$1');
$routes->post('indikator-uigm/delete/(:num)', 'Admin\\IndikatorUigm::delete/$1');