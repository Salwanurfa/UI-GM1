<?php

namespace App\Models;

use CodeIgniter\Model;

class WasteCategoryStandardModel extends Model
{
    protected $table = 'waste_categories_standard';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'category_code',
        'category_name',
        'subcategory_name',
        'uigm_mapping',
        'default_unit',
        'is_recyclable',
        'status_aktif'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get categories grouped by main category
     */
    public function getCategoriesGrouped()
    {
        return $this->where('status_aktif', 1)
                   ->orderBy('category_code')
                   ->orderBy('subcategory_name')
                   ->findAll();
    }

    /**
     * Get categories for specific UIGM mapping
     */
    public function getCategoriesByUigm($uigmCode)
    {
        return $this->where('uigm_mapping', $uigmCode)
                   ->where('status_aktif', 1)
                   ->findAll();
    }

    /**
     * Get recyclable categories only
     */
    public function getRecyclableCategories()
    {
        return $this->where('is_recyclable', 1)
                   ->where('status_aktif', 1)
                   ->findAll();
    }

    /**
     * Get category statistics
     */
    public function getCategoryStats()
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT 
                wcs.category_code,
                wcs.category_name,
                COUNT(wm.id) as total_entries,
                SUM(wm.volume_standardized) as total_volume,
                wcs.default_unit,
                AVG(CASE WHEN wm.processing_method_standard IN ('daur_ulang', 'kompos', 'biogas', 'reuse', 'reduce') 
                    THEN 1 ELSE 0 END) * 100 as recycling_rate
            FROM waste_categories_standard wcs
            LEFT JOIN waste_management wm ON wcs.category_code = wm.waste_category_standard
            WHERE wcs.status_aktif = 1
            AND (wm.status IN ('disetujui_tps', 'disetujui') OR wm.id IS NULL)
            GROUP BY wcs.category_code, wcs.category_name, wcs.default_unit
            ORDER BY wcs.category_code
        ");
        
        return $query->getResultArray();
    }

    /**
     * Get UIGM compliance data
     */
    public function getUigmComplianceData($year = null)
    {
        if (!$year) {
            $year = date('Y');
        }

        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT 
                wcs.uigm_mapping,
                wcs.category_name,
                COUNT(wm.id) as total_entries,
                SUM(wm.volume_standardized) as total_volume,
                SUM(CASE WHEN wm.processing_method_standard IN ('daur_ulang', 'kompos', 'biogas', 'reuse', 'reduce') 
                    THEN wm.volume_standardized ELSE 0 END) as processed_volume,
                wcs.default_unit
            FROM waste_categories_standard wcs
            LEFT JOIN waste_management wm ON wcs.category_code = wm.waste_category_standard
            WHERE wcs.status_aktif = 1
            AND (YEAR(wm.tanggal_input) = ? OR wm.id IS NULL)
            AND (wm.status IN ('disetujui_tps', 'disetujui') OR wm.id IS NULL)
            GROUP BY wcs.uigm_mapping, wcs.category_name, wcs.default_unit
            ORDER BY wcs.uigm_mapping
        ", [$year]);
        
        return $query->getResultArray();
    }
}