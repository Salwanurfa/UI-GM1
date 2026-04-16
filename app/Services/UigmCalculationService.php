<?php

namespace App\Services;

use App\Models\WasteModel;
use App\Models\LimbahB3Model;
use App\Models\UigmCategoryModel;
use App\Models\UigmEvidenceModel;

/**
 * UIGM Calculation Service
 * Handles automatic calculations for UI GreenMetric indicators based on waste logging data
 */
class UigmCalculationService
{
    protected $wasteModel;
    protected $limbahB3Model;
    protected $categoryModel;
    protected $evidenceModel;

    public function __construct()
    {
        $this->wasteModel = new WasteModel();
        $this->limbahB3Model = new LimbahB3Model();
        $this->categoryModel = new UigmCategoryModel();
        $this->evidenceModel = new UigmEvidenceModel();
    }

    /**
     * Get waste data summary for specific year
     */
    public function getWasteDataSummary(int $year = null): array
    {
        if (!$year) {
            $year = date('Y');
        }

        try {
            $db = \Config\Database::connect();
            
            // Get organic waste data (WS.3)
            $organicWasteQuery = $db->query("
                SELECT 
                    COUNT(*) as total_entries,
                    SUM(CASE WHEN volume_input IS NOT NULL THEN volume_input ELSE 0 END) as total_volume_input,
                    SUM(CASE WHEN volume_output IS NOT NULL THEN volume_output ELSE 0 END) as total_volume_output,
                    SUM(berat_kg) as total_weight_kg
                FROM waste_management 
                WHERE YEAR(tanggal_input) = ? 
                AND kategori_spesifik IN ('Organik Basah', 'Organik Kering')
                AND status IN ('disetujui_tps', 'disetujui')
            ", [$year]);
            
            $organicData = $organicWasteQuery->getRowArray();

            // Get total waste data for recycling percentage (WS.7)
            $totalWasteQuery = $db->query("
                SELECT 
                    SUM(berat_kg) as total_waste_generated,
                    SUM(CASE WHEN metode_pengolahan IN ('daur ulang', 'kompos', 'biogas', 'reuse', 'reduce') 
                        THEN berat_kg ELSE 0 END) as total_waste_processed
                FROM waste_management 
                WHERE YEAR(tanggal_input) = ?
                AND status IN ('disetujui_tps', 'disetujui')
            ", [$year]);
            
            $totalWasteData = $totalWasteQuery->getRowArray();

            // Get waste by processing method
            $processingMethodQuery = $db->query("
                SELECT 
                    metode_pengolahan,
                    COUNT(*) as count,
                    SUM(berat_kg) as total_weight
                FROM waste_management 
                WHERE YEAR(tanggal_input) = ?
                AND status IN ('disetujui_tps', 'disetujui')
                AND metode_pengolahan IS NOT NULL
                GROUP BY metode_pengolahan
                ORDER BY total_weight DESC
            ", [$year]);
            
            $processingMethods = $processingMethodQuery->getResultArray();

            // Get waste by category
            $categoryQuery = $db->query("
                SELECT 
                    kategori_spesifik,
                    COUNT(*) as count,
                    SUM(berat_kg) as total_weight
                FROM waste_management 
                WHERE YEAR(tanggal_input) = ?
                AND status IN ('disetujui_tps', 'disetujui')
                AND kategori_spesifik IS NOT NULL
                GROUP BY kategori_spesifik
                ORDER BY total_weight DESC
            ", [$year]);
            
            $categories = $categoryQuery->getResultArray();

            // Calculate recycling percentage
            $recyclingPercentage = 0;
            if ($totalWasteData['total_waste_generated'] > 0) {
                $recyclingPercentage = ($totalWasteData['total_waste_processed'] / $totalWasteData['total_waste_generated']) * 100;
            }

            // Calculate organic waste processing efficiency
            $organicEfficiency = 0;
            if ($organicData['total_volume_input'] > 0) {
                $organicEfficiency = ($organicData['total_volume_output'] / $organicData['total_volume_input']) * 100;
            }

            return [
                'year' => $year,
                'organic_waste' => [
                    'total_entries' => (int)$organicData['total_entries'],
                    'total_volume_input' => (float)$organicData['total_volume_input'],
                    'total_volume_output' => (float)$organicData['total_volume_output'],
                    'total_weight_kg' => (float)$organicData['total_weight_kg'],
                    'processing_efficiency' => round($organicEfficiency, 2)
                ],
                'total_waste' => [
                    'total_generated' => (float)$totalWasteData['total_waste_generated'],
                    'total_processed' => (float)$totalWasteData['total_waste_processed'],
                    'recycling_percentage' => round($recyclingPercentage, 2)
                ],
                'processing_methods' => $processingMethods,
                'categories' => $categories
            ];

        } catch (\Exception $e) {
            log_message('error', 'UIGM Calculation Service Error: ' . $e->getMessage());
            return [
                'year' => $year,
                'organic_waste' => [
                    'total_entries' => 0,
                    'total_volume_input' => 0,
                    'total_volume_output' => 0,
                    'total_weight_kg' => 0,
                    'processing_efficiency' => 0
                ],
                'total_waste' => [
                    'total_generated' => 0,
                    'total_processed' => 0,
                    'recycling_percentage' => 0
                ],
                'processing_methods' => [],
                'categories' => []
            ];
        }
    }

    /**
     * Get standardized waste data summary for UIGM compliance
     */
    public function getStandardizedWasteDataSummary(int $year = null): array
    {
        if (!$year) {
            $year = date('Y');
        }

        try {
            $db = \Config\Database::connect();
            
            // Get data by standardized categories
            $categoryQuery = $db->query("
                SELECT 
                    wm.waste_category_standard,
                    wcs.category_name,
                    wcs.uigm_mapping,
                    COUNT(wm.id) as total_entries,
                    SUM(wm.volume_standardized) as total_volume,
                    wcs.default_unit,
                    SUM(CASE WHEN wm.processing_method_standard IN ('daur_ulang', 'kompos', 'biogas', 'reuse', 'reduce') 
                        THEN wm.volume_standardized ELSE 0 END) as processed_volume,
                    COUNT(DISTINCT wm.user_id) as unique_contributors,
                    wm.source_type
                FROM waste_management wm
                JOIN waste_categories_standard wcs ON wm.waste_category_standard = wcs.category_code
                WHERE YEAR(wm.tanggal_input) = ?
                AND wm.status IN ('disetujui_tps', 'disetujui')
                AND wcs.status_aktif = 1
                GROUP BY wm.waste_category_standard, wcs.category_name, wcs.uigm_mapping, 
                         wcs.default_unit, wm.source_type
                ORDER BY wcs.uigm_mapping, wm.waste_category_standard
            ", [$year]);
            
            $categoryData = $categoryQuery->getResultArray();

            // Calculate UIGM indicators
            $uigmIndicators = [];
            
            // WS.3 - Organic Waste Processing
            $organicData = array_filter($categoryData, function($item) {
                return $item['waste_category_standard'] === 'organik';
            });
            
            $totalOrganicVolume = array_sum(array_column($organicData, 'total_volume'));
            $processedOrganicVolume = array_sum(array_column($organicData, 'processed_volume'));
            $organicProcessingRate = $totalOrganicVolume > 0 ? ($processedOrganicVolume / $totalOrganicVolume) * 100 : 0;
            
            $uigmIndicators['WS.3'] = [
                'name' => 'Pengolahan Limbah Organik',
                'total_volume' => $totalOrganicVolume,
                'processed_volume' => $processedOrganicVolume,
                'processing_rate' => round($organicProcessingRate, 2),
                'unit' => 'kg',
                'target' => 80, // Target 80% organic waste processed
                'compliance' => $organicProcessingRate >= 80 ? 'compliant' : 'non_compliant'
            ];

            // WS.5 - B3 Waste Management
            $b3Data = array_filter($categoryData, function($item) {
                return $item['waste_category_standard'] === 'b3';
            });
            
            $totalB3Volume = array_sum(array_column($b3Data, 'total_volume'));
            $processedB3Volume = array_sum(array_column($b3Data, 'processed_volume'));
            $b3ProcessingRate = $totalB3Volume > 0 ? ($processedB3Volume / $totalB3Volume) * 100 : 0;
            
            $uigmIndicators['WS.5'] = [
                'name' => 'Penanganan Limbah B3',
                'total_volume' => $totalB3Volume,
                'processed_volume' => $processedB3Volume,
                'processing_rate' => round($b3ProcessingRate, 2),
                'unit' => 'mixed',
                'target' => 100, // Target 100% B3 waste properly handled
                'compliance' => $b3ProcessingRate >= 100 ? 'compliant' : 'non_compliant'
            ];

            // WS.6 - Wastewater Management
            $wastewater = array_filter($categoryData, function($item) {
                return $item['waste_category_standard'] === 'cair';
            });
            
            $totalWastewaterVolume = array_sum(array_column($wastewater, 'total_volume'));
            $processedWastewaterVolume = array_sum(array_column($wastewater, 'processed_volume'));
            $wastewaterProcessingRate = $totalWastewaterVolume > 0 ? ($processedWastewaterVolume / $totalWastewaterVolume) * 100 : 0;
            
            $uigmIndicators['WS.6'] = [
                'name' => 'Pengolahan Air Limbah',
                'total_volume' => $totalWastewaterVolume,
                'processed_volume' => $processedWastewaterVolume,
                'processing_rate' => round($wastewaterProcessingRate, 2),
                'unit' => 'm3',
                'target' => 90, // Target 90% wastewater treated
                'compliance' => $wastewaterProcessingRate >= 90 ? 'compliant' : 'non_compliant'
            ];

            // WS.7 - Overall Recycling Percentage (excluding liquid waste)
            $solidWasteData = array_filter($categoryData, function($item) {
                return in_array($item['waste_category_standard'], ['organik', 'anorganik', 'residu']);
            });
            
            $totalSolidWaste = array_sum(array_column($solidWasteData, 'total_volume'));
            $totalProcessedSolid = array_sum(array_column($solidWasteData, 'processed_volume'));
            $overallRecyclingRate = $totalSolidWaste > 0 ? ($totalProcessedSolid / $totalSolidWaste) * 100 : 0;
            
            $uigmIndicators['WS.7'] = [
                'name' => 'Persentase Daur Ulang Total',
                'total_volume' => $totalSolidWaste,
                'processed_volume' => $totalProcessedSolid,
                'processing_rate' => round($overallRecyclingRate, 2),
                'unit' => 'kg',
                'target' => 75, // Target 75% overall recycling rate
                'compliance' => $overallRecyclingRate >= 75 ? 'compliant' : 'non_compliant'
            ];

            // Data source breakdown
            $sourceBreakdown = [];
            foreach ($categoryData as $item) {
                $source = $item['source_type'] ?? 'unknown';
                if (!isset($sourceBreakdown[$source])) {
                    $sourceBreakdown[$source] = [
                        'total_entries' => 0,
                        'total_volume' => 0,
                        'contributors' => 0
                    ];
                }
                $sourceBreakdown[$source]['total_entries'] += $item['total_entries'];
                $sourceBreakdown[$source]['total_volume'] += $item['total_volume'];
                $sourceBreakdown[$source]['contributors'] += $item['unique_contributors'];
            }

            return [
                'year' => $year,
                'category_data' => $categoryData,
                'uigm_indicators' => $uigmIndicators,
                'source_breakdown' => $sourceBreakdown,
                'summary' => [
                    'total_waste_generated' => array_sum(array_column($categoryData, 'total_volume')),
                    'total_waste_processed' => array_sum(array_column($categoryData, 'processed_volume')),
                    'overall_processing_rate' => $totalSolidWaste > 0 ? round(($totalProcessedSolid / $totalSolidWaste) * 100, 2) : 0,
                    'compliance_score' => $this->calculateComplianceScore($uigmIndicators)
                ]
            ];

        } catch (\Exception $e) {
            log_message('error', 'Standardized UIGM Calculation Error: ' . $e->getMessage());
            return [
                'year' => $year,
                'category_data' => [],
                'uigm_indicators' => [],
                'source_breakdown' => [],
                'summary' => [
                    'total_waste_generated' => 0,
                    'total_waste_processed' => 0,
                    'overall_processing_rate' => 0,
                    'compliance_score' => 0
                ]
            ];
        }
    }

    /**
     * Calculate compliance score based on UIGM indicators
     */
    private function calculateComplianceScore(array $indicators): float
    {
        if (empty($indicators)) {
            return 0;
        }

        $totalScore = 0;
        $totalWeight = 0;

        $weights = [
            'WS.3' => 25, // Organic waste processing - 25%
            'WS.5' => 20, // B3 waste management - 20%
            'WS.6' => 20, // Wastewater treatment - 20%
            'WS.7' => 35  // Overall recycling - 35%
        ];

        foreach ($indicators as $code => $data) {
            if (isset($weights[$code])) {
                $achievement = min(100, ($data['processing_rate'] / $data['target']) * 100);
                $totalScore += $achievement * $weights[$code];
                $totalWeight += $weights[$code];
            }
        }

        return $totalWeight > 0 ? round($totalScore / $totalWeight, 2) : 0;
    }
    public function getB3WasteDataSummary(int $year = null): array
    {
        if (!$year) {
            $year = date('Y');
        }

        try {
            $db = \Config\Database::connect();
            
            $query = $db->query("
                SELECT 
                    COUNT(*) as total_entries,
                    SUM(timbulan) as total_timbulan,
                    SUM(CASE WHEN volume_limbah IS NOT NULL THEN volume_limbah ELSE 0 END) as total_volume,
                    COUNT(CASE WHEN metode_penanganan IS NOT NULL THEN 1 END) as entries_with_handling_method
                FROM limbah_b3 
                WHERE YEAR(tanggal_input) = ?
                AND status IN ('disetujui_tps', 'disetujui_admin')
            ", [$year]);
            
            $data = $query->getRowArray();

            // Get B3 waste by handling method
            $handlingMethodQuery = $db->query("
                SELECT 
                    metode_penanganan,
                    COUNT(*) as count,
                    SUM(timbulan) as total_timbulan
                FROM limbah_b3 
                WHERE YEAR(tanggal_input) = ?
                AND status IN ('disetujui_tps', 'disetujui_admin')
                AND metode_penanganan IS NOT NULL
                GROUP BY metode_penanganan
                ORDER BY total_timbulan DESC
            ", [$year]);
            
            $handlingMethods = $handlingMethodQuery->getResultArray();

            // Get B3 waste by source
            $sourceQuery = $db->query("
                SELECT 
                    sumber_limbah,
                    COUNT(*) as count,
                    SUM(timbulan) as total_timbulan
                FROM limbah_b3 
                WHERE YEAR(tanggal_input) = ?
                AND status IN ('disetujui_tps', 'disetujui_admin')
                AND sumber_limbah IS NOT NULL
                GROUP BY sumber_limbah
                ORDER BY total_timbulan DESC
            ", [$year]);
            
            $sources = $sourceQuery->getResultArray();

            return [
                'year' => $year,
                'total_entries' => (int)$data['total_entries'],
                'total_timbulan' => (float)$data['total_timbulan'],
                'total_volume' => (float)$data['total_volume'],
                'entries_with_handling_method' => (int)$data['entries_with_handling_method'],
                'handling_methods' => $handlingMethods,
                'sources' => $sources
            ];

        } catch (\Exception $e) {
            log_message('error', 'UIGM B3 Calculation Service Error: ' . $e->getMessage());
            return [
                'year' => $year,
                'total_entries' => 0,
                'total_timbulan' => 0,
                'total_volume' => 0,
                'entries_with_handling_method' => 0,
                'handling_methods' => [],
                'sources' => []
            ];
        }
    }

    /**
     * Get audit readiness status for each category
     */
    public function getAuditReadinessStatus(int $year = null): array
    {
        if (!$year) {
            $year = date('Y');
        }

        $wasteData = $this->getWasteDataSummary($year);
        $b3Data = $this->getB3WasteDataSummary($year);

        $status = [
            'WS.1' => [
                'name' => 'Program 3R',
                'status' => 'Data Belum Lengkap',
                'data_count' => 0,
                'requirements' => 'Membutuhkan data program reduce, reuse, recycle'
            ],
            'WS.2' => [
                'name' => 'Pengelolaan Sampah Universitas',
                'status' => 'Data Belum Lengkap',
                'data_count' => 0,
                'requirements' => 'Membutuhkan data pengelolaan sampah kampus'
            ],
            'WS.3' => [
                'name' => 'Limbah Organik',
                'status' => 'Data Belum Lengkap',
                'data_count' => $wasteData['organic_waste']['total_entries'],
                'requirements' => 'Membutuhkan data volume input dan output limbah organik'
            ],
            'WS.4' => [
                'name' => 'Limbah Anorganik',
                'status' => 'Data Belum Lengkap',
                'data_count' => 0,
                'requirements' => 'Membutuhkan data pengelolaan limbah anorganik'
            ],
            'WS.5' => [
                'name' => 'Limbah Beracun',
                'status' => 'Data Belum Lengkap',
                'data_count' => $b3Data['total_entries'],
                'requirements' => 'Membutuhkan data pengelolaan limbah B3'
            ],
            'WS.6' => [
                'name' => 'Pengelolaan Air Limbah',
                'status' => 'Data Belum Lengkap',
                'data_count' => 0,
                'requirements' => 'Membutuhkan data pengelolaan air limbah'
            ],
            'WS.7' => [
                'name' => 'Persentase Sampah Didaur Ulang',
                'status' => 'Data Belum Lengkap',
                'data_count' => 0,
                'requirements' => 'Membutuhkan data persentase daur ulang'
            ]
        ];

        // Update status based on available data
        if ($wasteData['organic_waste']['total_entries'] > 0 && 
            $wasteData['organic_waste']['total_volume_input'] > 0) {
            $status['WS.3']['status'] = 'Data Siap';
        }

        if ($b3Data['total_entries'] > 0 && $b3Data['entries_with_handling_method'] > 0) {
            $status['WS.5']['status'] = 'Data Siap';
        }

        if ($wasteData['total_waste']['total_generated'] > 0) {
            $status['WS.7']['status'] = 'Data Siap';
            $status['WS.7']['data_count'] = count($wasteData['processing_methods']);
        }

        // Check for general waste management data
        $totalWasteEntries = $wasteData['total_waste']['total_generated'] > 0 ? 1 : 0;
        if ($totalWasteEntries > 0) {
            $status['WS.1']['status'] = 'Data Siap';
            $status['WS.1']['data_count'] = count($wasteData['processing_methods']);
            
            $status['WS.2']['status'] = 'Data Siap';
            $status['WS.2']['data_count'] = count($wasteData['categories']);
        }

        return $status;
    }

    /**
     * Link waste log entries to UIGM evidence
     */
    public function linkWasteDataToEvidence(int $evidenceId, array $wasteLogIds): bool
    {
        try {
            $db = \Config\Database::connect();
            
            // First, remove existing links for this evidence
            $db->query("DELETE FROM uigm_waste_links WHERE evidence_id = ?", [$evidenceId]);
            
            // Add new links
            foreach ($wasteLogIds as $wasteLogId) {
                $db->query("
                    INSERT INTO uigm_waste_links (evidence_id, waste_log_id, created_at) 
                    VALUES (?, ?, NOW())
                ", [$evidenceId, $wasteLogId]);
            }
            
            return true;
            
        } catch (\Exception $e) {
            log_message('error', 'Error linking waste data to evidence: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get linked waste data for specific evidence
     */
    public function getLinkedWasteData(int $evidenceId): array
    {
        try {
            $db = \Config\Database::connect();
            
            $query = $db->query("
                SELECT 
                    wm.*,
                    uwl.created_at as linked_at
                FROM uigm_waste_links uwl
                JOIN waste_management wm ON uwl.waste_log_id = wm.id
                WHERE uwl.evidence_id = ?
                ORDER BY wm.tanggal_input DESC
            ", [$evidenceId]);
            
            return $query->getResultArray();
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting linked waste data: ' . $e->getMessage());
            return [];
        }
    }
}