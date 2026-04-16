<?php

namespace App\Services;

use CodeIgniter\Database\ConnectionInterface;

/**
 * UIGM Indicator Mapping Service
 * 
 * Service untuk mapping data limbah user ke 7 Indikator UIGM:
 * 1. Program 3R (Reduce, Reuse, Recycle)
 * 2. Pengurangan Kertas & Plastik
 * 3. Limbah Organik
 * 4. Limbah Anorganik
 * 5. Limbah B3
 * 6. Limbah Cair
 * 7. Persentase Daur Ulang (Calculated)
 */
class UIGMIndicatorMappingService
{
    protected $db;
    
    // Mapping kategori limbah ke indikator UIGM
    protected $indicatorMapping = [
        'indikator_1' => [
            'name' => 'Program 3R',
            'description' => 'Kegiatan Reduce, Reuse, Recycle',
            'keywords' => [
                // Reduce keywords
                'reduce', 'pengurangan', 'minimasi', 'efisiensi',
                // Reuse keywords  
                'reuse', 'pakai ulang', 'daur pakai', 'refill',
                // Recycle keywords
                'recycle', 'daur ulang', 'recycling', 'olah ulang'
            ],
            'jenis_sampah' => [
                'Kertas Daur Ulang', 'Plastik Daur Ulang', 'Botol Bekas Pakai Ulang',
                'Kardus Bekas', 'Kemasan Refill'
            ]
        ],
        'indikator_2' => [
            'name' => 'Pengurangan Kertas & Plastik',
            'description' => 'Upaya mengurangi penggunaan kertas dan plastik',
            'keywords' => [
                'kertas', 'paper', 'plastik', 'plastic', 'keyboard', 'kemasan'
            ],
            'jenis_sampah' => [
                'Kertas', 'Plastik', 'Keyboard Bekas', 'Kemasan Plastik',
                'Kertas Bekas', 'Plastik Bekas', 'Kemasan Kertas'
            ]
        ],
        'indikator_3' => [
            'name' => 'Limbah Organik',
            'description' => 'Limbah yang dapat terurai secara alami',
            'keywords' => [
                'organik', 'makanan', 'sisa', 'dedaunan', 'taman', 'kompos'
            ],
            'jenis_sampah' => [
                'Organik', 'Organik Basah', 'Organik Kering', 'Sisa Makanan',
                'Dedaunan', 'Limbah Taman', 'Sampah Dapur'
            ]
        ],
        'indikator_4' => [
            'name' => 'Limbah Anorganik',
            'description' => 'Limbah non-organik umum',
            'keywords' => [
                'anorganik', 'logam', 'kaca', 'metal', 'kaleng', 'aluminium'
            ],
            'jenis_sampah' => [
                'Anorganik', 'Logam', 'Kaca', 'Kaleng', 'Aluminium',
                'Besi', 'Tembaga', 'Stainless'
            ]
        ],
        'indikator_5' => [
            'name' => 'Limbah B3',
            'description' => 'Bahan Berbahaya dan Beracun',
            'keywords' => [
                'b3', 'oli', 'baterai', 'medis', 'kimia', 'beracun', 'berbahaya'
            ],
            'jenis_sampah' => [
                'B3', 'Oli Bekas', 'Baterai', 'Limbah Medis', 'Limbah Kimia',
                'Lampu Neon', 'Elektronik Bekas', 'Aki Bekas'
            ]
        ],
        'indikator_6' => [
            'name' => 'Limbah Cair',
            'description' => 'Air limbah dan cairan buangan',
            'keywords' => [
                'cair', 'air', 'limbah cair', 'cairan', 'liquid'
            ],
            'jenis_sampah' => [
                'Limbah Cair', 'Air Limbah', 'Limbah Laboratorium Cair',
                'Air Bekas Cuci', 'Cairan Bekas'
            ]
        ]
    ];

    public function __construct(ConnectionInterface $db = null)
    {
        $this->db = $db ?? \Config\Database::connect();
    }

    /**
     * Map jenis sampah ke indikator UIGM
     */
    public function mapToIndicator(string $jenisSampah, string $namaSampah = ''): string
    {
        $jenisSampah = strtolower(trim($jenisSampah));
        $namaSampah = strtolower(trim($namaSampah));
        $combinedText = $jenisSampah . ' ' . $namaSampah;

        // Check exact match first
        foreach ($this->indicatorMapping as $indicatorKey => $config) {
            foreach ($config['jenis_sampah'] as $jenisPattern) {
                if (strtolower($jenisPattern) === $jenisSampah) {
                    return $indicatorKey;
                }
            }
        }

        // Check keyword match
        foreach ($this->indicatorMapping as $indicatorKey => $config) {
            foreach ($config['keywords'] as $keyword) {
                if (strpos($combinedText, strtolower($keyword)) !== false) {
                    return $indicatorKey;
                }
            }
        }

        // Default fallback based on common categories
        if (strpos($combinedText, 'organik') !== false || strpos($combinedText, 'makanan') !== false) {
            return 'indikator_3';
        }
        if (strpos($combinedText, 'plastik') !== false || strpos($combinedText, 'kertas') !== false) {
            return 'indikator_2';
        }
        if (strpos($combinedText, 'b3') !== false || strpos($combinedText, 'oli') !== false) {
            return 'indikator_5';
        }
        if (strpos($combinedText, 'cair') !== false) {
            return 'indikator_6';
        }

        // Default to anorganik
        return 'indikator_4';
    }

    /**
     * Get data for all UIGM indicators
     */
    public function getUIGMIndicatorData(int $year): array
    {
        $result = [];
        
        foreach ($this->indicatorMapping as $indicatorKey => $config) {
            $result[$indicatorKey] = $this->getIndicatorData($indicatorKey, $year);
            $result[$indicatorKey]['config'] = $config;
        }

        // Calculate Indikator 7 (Persentase Daur Ulang)
        $result['indikator_7'] = $this->calculateRecyclingPercentage($result, $year);

        return $result;
    }

    /**
     * Get data for specific indicator with cross-form integration
     */
    public function getIndicatorData(string $indicatorKey, int $year): array
    {
        if (!isset($this->indicatorMapping[$indicatorKey])) {
            return $this->getEmptyIndicatorData();
        }

        // Use standardization service for comprehensive data
        $standardizationService = new \App\Services\WasteStandardizationService();
        $allWasteData = $standardizationService->getStandardizedWasteData($year);
        
        $config = $this->indicatorMapping[$indicatorKey];
        
        // Filter data based on indicator mapping
        $relevantData = [];
        foreach ($allWasteData as $record) {
            $mappedIndicator = $this->mapToIndicator($record['jenis_sampah'], $record['nama_sampah_detail']);
            if ($mappedIndicator === $indicatorKey) {
                $relevantData[] = $record;
            }
        }
        
        // Calculate totals with proper liquid/solid separation
        $totalKg = 0;
        $totalL = 0;
        $totalRecords = count($relevantData);
        $evidenceCount = 0;
        $sources = [];
        
        foreach ($relevantData as $record) {
            // For liquid waste (Indikator 6), prioritize volume_l
            if ($indicatorKey === 'indikator_6' || $record['standard_category'] === 'limbah_cair') {
                $totalL += $record['volume_l'];
                // Don't add liquid waste to kg totals
            } else {
                // For solid waste, use volume_kg
                $totalKg += $record['volume_kg'];
            }
            
            if (!empty($record['bukti_foto'])) {
                $evidenceCount++;
            }
            
            if (!in_array($record['nama_unit'], $sources)) {
                $sources[] = $record['nama_unit'];
            }
        }

        return [
            'total_kg' => round($totalKg, 2),
            'total_l' => round($totalL, 2),
            'total_records' => $totalRecords,
            'evidence_count' => $evidenceCount,
            'sources' => count($sources),
            'has_evidence' => $evidenceCount > 0,
            'data' => $relevantData,
            'source_breakdown' => $this->getSourceBreakdown($relevantData),
            'processing_notes' => $this->getIndicatorProcessingNotes($indicatorKey, $totalKg, $totalL)
        ];
    }

    /**
     * Get source breakdown for indicator data
     */
    private function getSourceBreakdown(array $data): array
    {
        $breakdown = [
            'waste_management' => 0,
            'limbah_b3' => 0,
            'user_input' => 0,
            'tps_input' => 0
        ];
        
        foreach ($data as $record) {
            $breakdown[$record['source_table']]++;
            
            if (in_array($record['user_role'], ['user', 'admin_unit'])) {
                $breakdown['user_input']++;
            } elseif ($record['user_role'] === 'pengelola_tps') {
                $breakdown['tps_input']++;
            }
        }
        
        return $breakdown;
    }

    /**
     * Get processing notes for specific indicator
     */
    private function getIndicatorProcessingNotes(string $indicatorKey, float $totalKg, float $totalL): string
    {
        switch ($indicatorKey) {
            case 'indikator_6':
                return "Limbah cair diproses dalam liter (L). Total: {$totalL} L";
            case 'indikator_5':
                return "Limbah B3 memerlukan penanganan khusus. Total: {$totalKg} kg, {$totalL} L";
            case 'indikator_7':
                return "Perhitungan otomatis berdasarkan total sampah terolah vs dihasilkan";
            default:
                return "Limbah padat diproses dalam kilogram (kg). Total: {$totalKg} kg";
        }
    }

    /**
     * Calculate recycling percentage (Indikator 7) with proper logic
     */
    private function calculateRecyclingPercentage(array $indicators, int $year): array
    {
        // Get comprehensive waste data from all sources
        $standardizationService = new \App\Services\WasteStandardizationService();
        $allWasteData = $standardizationService->getStandardizedWasteData($year);
        
        // Calculate totals by category
        $organicTotal = 0;
        $inorganicTotal = 0;
        $totalSolidWaste = 0;
        $totalRecyclableWaste = 0;
        
        foreach ($allWasteData as $record) {
            $category = $record['standard_category'];
            $volumeKg = $record['volume_kg'];
            
            // Only count solid waste (exclude liquid waste)
            if ($category !== 'limbah_cair' && $volumeKg > 0) {
                $totalSolidWaste += $volumeKg;
                
                // Count recyclable waste (organic + inorganic)
                if (in_array($category, ['limbah_organik', 'limbah_anorganik'])) {
                    $totalRecyclableWaste += $volumeKg;
                    
                    if ($category === 'limbah_organik') {
                        $organicTotal += $volumeKg;
                    } elseif ($category === 'limbah_anorganik') {
                        $inorganicTotal += $volumeKg;
                    }
                }
            }
        }
        
        // Calculate recycling percentage: (Recyclable Waste / Total Solid Waste) * 100
        $recyclingPercentage = $totalSolidWaste > 0 ? 
            round(($totalRecyclableWaste / $totalSolidWaste) * 100, 2) : 0;

        // Count evidence and sources
        $evidenceCount = 0;
        $sources = [];
        $totalRecords = 0;
        
        foreach ($allWasteData as $record) {
            if (in_array($record['standard_category'], ['limbah_organik', 'limbah_anorganik'])) {
                $totalRecords++;
                if (!empty($record['bukti_foto'])) {
                    $evidenceCount++;
                }
                if (!in_array($record['nama_unit'], $sources)) {
                    $sources[] = $record['nama_unit'];
                }
            }
        }

        return [
            'name' => 'Persentase Daur Ulang',
            'description' => 'Persentase limbah yang dapat didaur ulang dari total limbah padat',
            'total_kg' => $totalRecyclableWaste,
            'total_l' => 0, // Recycling percentage doesn't include liquid waste
            'total_records' => $totalRecords,
            'evidence_count' => $evidenceCount,
            'sources' => count($sources),
            'has_evidence' => $evidenceCount > 0,
            'recycling_percentage' => $recyclingPercentage,
            'recyclable_kg' => $totalRecyclableWaste,
            'total_solid_kg' => $totalSolidWaste,
            'organic_kg' => $organicTotal,
            'inorganic_kg' => $inorganicTotal,
            'calculation_formula' => "({$totalRecyclableWaste} kg recyclable / {$totalSolidWaste} kg total solid) × 100",
            'config' => [
                'name' => 'Persentase Daur Ulang',
                'description' => 'Perhitungan otomatis berdasarkan total sampah terolah vs total sampah dihasilkan'
            ]
        ];
    }

    /**
     * Get detailed recap data for admin table with cross-form integration
     */
    public function getDetailedRecapData(int $year): array
    {
        // Use standardization service for comprehensive cross-form data
        $standardizationService = new \App\Services\WasteStandardizationService();
        $allData = $standardizationService->getStandardizedWasteData($year);

        // Add indicator mapping to each record
        foreach ($allData as &$record) {
            $indicatorKey = $this->mapToIndicator($record['jenis_sampah'], $record['nama_sampah_detail']);
            $record['indikator_key'] = $indicatorKey;
            $record['indikator_name'] = $this->indicatorMapping[$indicatorKey]['name'] ?? 'Unknown';
            $record['indikator_description'] = $this->indicatorMapping[$indicatorKey]['description'] ?? '';
            
            // Add display formatting
            $record['volume_display'] = $this->formatVolumeDisplay($record);
            $record['source_display'] = $this->formatSourceDisplay($record);
            $record['category_display'] = $this->formatCategoryDisplay($record);
        }

        // Sort by date (newest first) and then by unit name
        usort($allData, function($a, $b) {
            $dateCompare = strtotime($b['tanggal']) - strtotime($a['tanggal']);
            if ($dateCompare === 0) {
                return strcmp($a['nama_unit'], $b['nama_unit']);
            }
            return $dateCompare;
        });

        return $allData;
    }

    /**
     * Format volume display based on waste type
     */
    private function formatVolumeDisplay(array $record): string
    {
        if ($record['is_liquid_waste']) {
            return number_format($record['volume_l'], 2) . ' L';
        } else {
            return number_format($record['volume_kg'], 2) . ' kg';
        }
    }

    /**
     * Format source display
     */
    private function formatSourceDisplay(array $record): string
    {
        $sourceMap = [
            'waste_management' => 'Form Umum',
            'limbah_b3' => 'Form B3'
        ];
        
        $roleMap = [
            'user' => 'USER',
            'pengelola_tps' => 'TPS',
            'admin_unit' => 'USER',
            'admin_pusat' => 'ADMIN PUSAT',
            'super_admin' => 'ADMIN PUSAT'
        ];
        
        $source = $sourceMap[$record['source_table']] ?? 'Unknown';
        $role = $roleMap[$record['user_role']] ?? 'USER';
        
        return "{$source} ({$role})";
    }

    /**
     * Format category display
     */
    private function formatCategoryDisplay(array $record): string
    {
        $category = $record['standard_category_name'];
        $notes = $record['processing_notes'] ?? '';
        
        if ($record['is_liquid_waste']) {
            $category .= ' (Cair)';
        }
        
        return $category;
    }

    /**
     * Get empty indicator data structure
     */
    private function getEmptyIndicatorData(): array
    {
        return [
            'total_kg' => 0,
            'total_l' => 0,
            'total_records' => 0,
            'evidence_count' => 0,
            'sources' => 0,
            'has_evidence' => false,
            'data' => []
        ];
    }

    /**
     * Get indicator mapping configuration
     */
    public function getIndicatorMapping(): array
    {
        return $this->indicatorMapping;
    }
}