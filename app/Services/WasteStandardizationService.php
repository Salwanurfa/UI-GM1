<?php

namespace App\Services;

use CodeIgniter\Database\ConnectionInterface;

/**
 * Waste Standardization Service
 * 
 * Standardizes waste categories based on:
 * - UI GreenMetric Standards
 * - Indonesian Environmental Regulations (UU No. 18/2008)
 * - Ministry of Environment and Forestry Guidelines
 * - ISO 14001 Environmental Management Standards
 */
class WasteStandardizationService
{
    protected $db;
    
    /**
     * Standardized waste categories based on official regulations
     * Reference: UI GreenMetric & Indonesian Environmental Law
     */
    protected $standardCategories = [
        'limbah_organik' => [
            'name' => 'Limbah Organik',
            'description' => 'Limbah yang dapat terurai secara alami oleh mikroorganisme',
            'reference' => 'UI GreenMetric Standard & UU No. 18/2008 tentang Pengelolaan Sampah',
            'keywords' => [
                // Food waste
                'makanan', 'sisa makanan', 'food waste', 'organic food',
                'nasi', 'sayur', 'buah', 'daging', 'ikan', 'roti',
                // Garden waste
                'dedaunan', 'daun', 'ranting', 'rumput', 'bunga',
                'taman', 'garden', 'landscaping', 'pruning',
                // Organic materials
                'organik', 'organic', 'kompos', 'compost',
                'biodegradable', 'terurai', 'alami'
            ],
            'jenis_sampah' => [
                'Organik', 'Organik Basah', 'Organik Kering', 'Sisa Makanan',
                'Dedaunan', 'Limbah Taman', 'Sampah Dapur', 'Food Waste'
            ],
            'color' => 'success',
            'icon' => 'seedling'
        ],
        'limbah_anorganik' => [
            'name' => 'Limbah Anorganik',
            'description' => 'Limbah yang tidak dapat terurai secara alami dan dapat didaur ulang',
            'reference' => 'UI GreenMetric Standard & Peraturan Menteri LHK No. P.75/2019',
            'keywords' => [
                // Plastic
                'plastik', 'plastic', 'botol plastik', 'kemasan plastik',
                'styrofoam', 'polystyrene', 'PET', 'HDPE',
                // Paper
                'kertas', 'paper', 'kardus', 'cardboard', 'karton',
                'koran', 'majalah', 'buku', 'dokumen',
                // Metal
                'logam', 'metal', 'kaleng', 'aluminium', 'aluminum',
                'besi', 'tembaga', 'stainless', 'seng',
                // Glass
                'kaca', 'glass', 'botol kaca', 'pecahan kaca',
                // Rubber
                'karet', 'rubber', 'ban', 'sandal karet'
            ],
            'jenis_sampah' => [
                'Anorganik', 'Plastik', 'Kertas', 'Logam', 'Kaca', 'Karet',
                'Botol Plastik', 'Kemasan Plastik', 'Kardus', 'Kaleng',
                'Aluminium', 'Botol Kaca'
            ],
            'color' => 'info',
            'icon' => 'cube'
        ],
        'limbah_b3' => [
            'name' => 'Limbah B3 (Bahan Berbahaya & Beracun)',
            'description' => 'Limbah yang mengandung bahan berbahaya dan beracun',
            'reference' => 'PP No. 101/2014 tentang Pengelolaan Limbah B3 & UI GreenMetric',
            'keywords' => [
                // Hazardous materials
                'b3', 'berbahaya', 'beracun', 'hazardous', 'toxic',
                // Batteries
                'baterai', 'battery', 'aki', 'accu',
                // Electronic waste
                'elektronik', 'electronic', 'e-waste', 'komputer',
                'handphone', 'laptop', 'tv', 'radio',
                // Medical waste
                'medis', 'medical', 'jarum suntik', 'obat', 'medicine',
                'hospital', 'klinik', 'puskesmas',
                // Chemical waste
                'oli', 'oil', 'pelumas', 'cat', 'paint', 'thinner',
                'pestisida', 'insektisida', 'kimia', 'chemical',
                // Light bulbs
                'lampu', 'neon', 'fluorescent', 'mercury'
            ],
            'jenis_sampah' => [
                'B3', 'Limbah B3', 'Baterai', 'Aki Bekas', 'Oli Bekas',
                'Limbah Medis', 'Elektronik Bekas', 'Lampu Neon',
                'Limbah Kimia', 'Cat Bekas', 'Pestisida'
            ],
            'color' => 'danger',
            'icon' => 'biohazard'
        ],
        'limbah_cair' => [
            'name' => 'Limbah Cair',
            'description' => 'Air limbah dan cairan buangan yang memerlukan pengolahan khusus',
            'reference' => 'Peraturan Menteri LHK No. P.68/2016 & UI GreenMetric',
            'keywords' => [
                // Liquid waste
                'cair', 'liquid', 'air limbah', 'wastewater',
                // Domestic wastewater
                'domestik', 'domestic', 'rumah tangga', 'household',
                'kamar mandi', 'dapur', 'cuci', 'washing',
                // Laboratory wastewater
                'laboratorium', 'laboratory', 'lab', 'penelitian',
                'research', 'kimia cair', 'reagen',
                // Industrial wastewater
                'industri', 'industrial', 'pabrik', 'factory',
                // General liquid terms
                'cairan', 'fluid', 'air bekas', 'greywater'
            ],
            'jenis_sampah' => [
                'Limbah Cair', 'Air Limbah', 'Air Limbah Domestik',
                'Air Limbah Laboratorium', 'Limbah Cair Industri',
                'Air Bekas Cuci', 'Cairan Bekas'
            ],
            'color' => 'primary',
            'icon' => 'flask'
        ],
        'residu' => [
            'name' => 'Residu',
            'description' => 'Sampah akhir yang tidak dapat diolah kembali dan harus dibuang ke TPA',
            'reference' => 'UU No. 18/2008 & Peraturan Menteri LHK tentang Pengelolaan Sampah',
            'keywords' => [
                // Non-recyclable items
                'residu', 'residue', 'tidak dapat didaur ulang',
                'non-recyclable', 'final waste',
                // Contaminated items
                'tissue kotor', 'tisu bekas', 'dirty tissue',
                'pembalut', 'sanitary pad', 'diaper', 'popok',
                // Cigarette waste
                'puntung rokok', 'cigarette butt', 'rokok',
                'abu rokok', 'cigarette ash',
                // Mixed contaminated waste
                'sampah campur', 'mixed waste', 'kontaminasi',
                'kotor', 'dirty', 'terkontaminasi'
            ],
            'jenis_sampah' => [
                'Residu', 'Sampah Residu', 'Tissue Kotor', 'Puntung Rokok',
                'Pembalut Bekas', 'Popok Bekas', 'Sampah Campur'
            ],
            'color' => 'secondary',
            'icon' => 'trash'
        ]
    ];

    public function __construct(ConnectionInterface $db = null)
    {
        $this->db = $db ?? \Config\Database::connect();
    }

    /**
     * Standardize waste category based on official regulations
     */
    public function standardizeCategory(string $jenisSampah, string $namaSampah = ''): string
    {
        $jenisSampah = strtolower(trim($jenisSampah));
        $namaSampah = strtolower(trim($namaSampah));
        $combinedText = $jenisSampah . ' ' . $namaSampah;

        // Priority order based on hazard level (B3 first, then specific categories)
        
        // 1. B3 (Highest priority - safety concern)
        foreach ($this->standardCategories['limbah_b3']['keywords'] as $keyword) {
            if (strpos($combinedText, strtolower($keyword)) !== false) {
                return 'limbah_b3';
            }
        }

        // 2. Limbah Cair (Specific handling required)
        foreach ($this->standardCategories['limbah_cair']['keywords'] as $keyword) {
            if (strpos($combinedText, strtolower($keyword)) !== false) {
                return 'limbah_cair';
            }
        }

        // 3. Residu (Non-recyclable)
        foreach ($this->standardCategories['residu']['keywords'] as $keyword) {
            if (strpos($combinedText, strtolower($keyword)) !== false) {
                return 'residu';
            }
        }

        // 4. Organik (Biodegradable)
        foreach ($this->standardCategories['limbah_organik']['keywords'] as $keyword) {
            if (strpos($combinedText, strtolower($keyword)) !== false) {
                return 'limbah_organik';
            }
        }

        // 5. Anorganik (Recyclable - default for unknown recyclables)
        foreach ($this->standardCategories['limbah_anorganik']['keywords'] as $keyword) {
            if (strpos($combinedText, strtolower($keyword)) !== false) {
                return 'limbah_anorganik';
            }
        }

        // Default fallback to anorganik (most common recyclable category)
        return 'limbah_anorganik';
    }

    /**
     * Get standardized category information
     */
    public function getCategoryInfo(string $categoryKey): array
    {
        return $this->standardCategories[$categoryKey] ?? [
            'name' => 'Unknown Category',
            'description' => 'Kategori tidak dikenali',
            'reference' => 'Tidak ada rujukan',
            'color' => 'secondary',
            'icon' => 'question'
        ];
    }

    /**
     * Get all standard categories
     */
    public function getAllStandardCategories(): array
    {
        return $this->standardCategories;
    }

    /**
     * Get category mapping for legacy data
     */
    public function getLegacyCategoryMapping(): array
    {
        return [
            // Legacy names to standard mapping
            'organic' => 'limbah_organik',
            'organik basah' => 'limbah_organik',
            'organik kering' => 'limbah_organik',
            'inorganic' => 'limbah_anorganik',
            'anorganik' => 'limbah_anorganik',
            'plastic' => 'limbah_anorganik',
            'paper' => 'limbah_anorganik',
            'metal' => 'limbah_anorganik',
            'glass' => 'limbah_anorganik',
            'hazardous' => 'limbah_b3',
            'berbahaya' => 'limbah_b3',
            'electronic' => 'limbah_b3',
            'battery' => 'limbah_b3',
            'liquid' => 'limbah_cair',
            'wastewater' => 'limbah_cair',
            'residual' => 'residu',
            'mixed' => 'residu'
        ];
    }

    /**
     * Get standardized data with proper categorization and cross-form integration
     */
    public function getStandardizedWasteData(int $year): array
    {
        // Check what columns exist in the users table
        $columnsQuery = $this->db->query("SHOW COLUMNS FROM users");
        $columns = array_column($columnsQuery->getResultArray(), 'Field');
        
        $userNameSelect = 'usr.username';
        if (in_array('nama_lengkap', $columns)) {
            $userNameSelect = 'COALESCE(usr.nama_lengkap, usr.username, \'Unknown User\')';
        }

        // Get data from waste_management table
        $wasteQuery = $this->db->query("
            SELECT 
                wm.id,
                'waste_management' as source_table,
                COALESCE(u.nama_unit, 'Unknown Unit') as nama_unit,
                wm.jenis_sampah,
                COALESCE(wm.nama_sampah, wm.jenis_sampah) as nama_sampah_detail,
                wm.jumlah,
                wm.satuan,
                CASE 
                    WHEN wm.satuan = 'kg' THEN wm.jumlah 
                    WHEN wm.satuan = 'g' THEN wm.jumlah/1000 
                    WHEN wm.satuan = 'ton' THEN wm.jumlah*1000 
                    ELSE 0 
                END as volume_kg,
                CASE 
                    WHEN wm.satuan = 'L' THEN wm.jumlah 
                    WHEN wm.satuan = 'ml' THEN wm.jumlah/1000 
                    WHEN wm.satuan = 'm³' THEN wm.jumlah*1000
                    ELSE 0 
                END as volume_l,
                wm.tanggal,
                wm.bukti_foto,
                COALESCE(wm.gedung, 'Unknown') as gedung,
                {$userNameSelect} as nama_user,
                usr.role as user_role,
                wm.status
            FROM waste_management wm
            LEFT JOIN unit u ON wm.unit_id = u.id
            INNER JOIN users usr ON wm.user_id = usr.id
            WHERE YEAR(wm.tanggal) = ?
            AND wm.status IN ('disetujui', 'disetujui_tps', 'approved')
        ", [$year]);

        $wasteData = $wasteQuery->getResultArray();

        // Get data from limbah_b3 table if it exists
        $b3Data = [];
        try {
            $b3Query = $this->db->query("
                SELECT 
                    lb.id,
                    'limbah_b3' as source_table,
                    COALESCE(u.nama_unit, 'Unknown Unit') as nama_unit,
                    'B3' as jenis_sampah,
                    COALESCE(mlb.nama_limbah, 'Limbah B3') as nama_sampah_detail,
                    lb.timbulan as jumlah,
                    lb.satuan,
                    CASE 
                        WHEN lb.satuan = 'kg' THEN lb.timbulan 
                        WHEN lb.satuan = 'g' THEN lb.timbulan/1000 
                        WHEN lb.satuan = 'ton' THEN lb.timbulan*1000 
                        ELSE 0 
                    END as volume_kg,
                    CASE 
                        WHEN lb.satuan = 'L' THEN lb.timbulan 
                        WHEN lb.satuan = 'ml' THEN lb.timbulan/1000 
                        ELSE 0 
                    END as volume_l,
                    lb.tanggal_input as tanggal,
                    lb.bukti_foto,
                    COALESCE(lb.gedung, 'Unknown') as gedung,
                    {$userNameSelect} as nama_user,
                    usr.role as user_role,
                    lb.status
                FROM limbah_b3 lb
                LEFT JOIN master_limbah_b3 mlb ON lb.master_b3_id = mlb.id
                LEFT JOIN unit u ON lb.unit_id = u.id
                INNER JOIN users usr ON lb.user_id = usr.id
                WHERE YEAR(lb.tanggal_input) = ?
                AND lb.status IN ('disetujui_admin', 'disetujui_tps', 'approved')
            ", [$year]);
            
            $b3Data = $b3Query->getResultArray();
        } catch (\Exception $e) {
            log_message('info', 'Limbah B3 table not found or accessible: ' . $e->getMessage());
        }

        // Combine all data sources
        $allData = array_merge($wasteData, $b3Data);
        
        // Standardize each record with proper liquid/solid separation
        $standardizedData = [];
        foreach ($allData as $record) {
            $standardCategory = $this->standardizeCategory($record['jenis_sampah'], $record['nama_sampah_detail']);
            $categoryInfo = $this->getCategoryInfo($standardCategory);
            
            // Apply liquid waste logic - if it's liquid waste, prioritize volume_l
            $finalVolumeKg = $record['volume_kg'];
            $finalVolumeL = $record['volume_l'];
            
            if ($standardCategory === 'limbah_cair') {
                // For liquid waste, ensure volume is in liters
                if ($finalVolumeKg > 0 && $finalVolumeL == 0) {
                    // If weight was provided for liquid waste, convert or flag for review
                    $finalVolumeL = $finalVolumeKg; // Assume 1kg ≈ 1L for water-based liquids
                    $finalVolumeKg = 0; // Clear weight for liquid waste
                }
            } else {
                // For solid waste, ensure volume is in kg
                if ($finalVolumeL > 0 && $finalVolumeKg == 0 && $standardCategory !== 'limbah_cair') {
                    // If volume was provided for solid waste, it might be an error
                    // Keep both values but flag for review
                }
            }
            
            $standardizedData[] = array_merge($record, [
                'standard_category' => $standardCategory,
                'standard_category_name' => $categoryInfo['name'],
                'category_description' => $categoryInfo['description'],
                'category_reference' => $categoryInfo['reference'],
                'category_color' => $categoryInfo['color'],
                'category_icon' => $categoryInfo['icon'],
                'volume_kg' => $finalVolumeKg,
                'volume_l' => $finalVolumeL,
                'is_liquid_waste' => ($standardCategory === 'limbah_cair'),
                'is_recyclable' => in_array($standardCategory, ['limbah_organik', 'limbah_anorganik']),
                'processing_notes' => $this->getProcessingNotes($standardCategory, $record)
            ]);
        }
        
        return $standardizedData;
    }

    /**
     * Get processing notes for waste category
     */
    private function getProcessingNotes(string $category, array $record): string
    {
        switch ($category) {
            case 'limbah_cair':
                return 'Diproses sebagai limbah cair - volume dalam liter (L)';
            case 'limbah_b3':
                return 'Memerlukan penanganan khusus B3 - protokol keamanan tinggi';
            case 'limbah_organik':
                return 'Dapat dikompos atau diolah menjadi biogas';
            case 'limbah_anorganik':
                return 'Dapat didaur ulang - berkontribusi pada persentase daur ulang';
            case 'residu':
                return 'Limbah akhir - tidak dapat diolah kembali';
            default:
                return 'Diproses sesuai kategori standar';
        }
    }
}