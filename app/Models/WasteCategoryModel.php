<?php

namespace App\Models;

use CodeIgniter\Model;

class WasteCategoryModel extends Model
{
    protected $table = 'waste_categories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'kategori_utama',
        'sub_kategori', 
        'deskripsi',
        'metode_pengolahan_default',
        'target_pengurangan',
        'status_aktif'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'kategori_utama' => 'required|max_length[50]',
        'sub_kategori' => 'required|max_length[100]',
        'status_aktif' => 'in_list[0,1]'
    ];

    protected $validationMessages = [
        'kategori_utama' => [
            'required' => 'Nama kategori harus diisi',
            'max_length' => 'Nama kategori maksimal 50 karakter'
        ],
        'sub_kategori' => [
            'required' => 'Sub kategori harus diisi',
            'max_length' => 'Sub kategori maksimal 100 karakter'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Get active categories
     */
    public function getActiveCategories()
    {
        return $this->where('status_aktif', 1)
                   ->orderBy('kategori_utama', 'ASC')
                   ->findAll();
    }

    /**
     * Get categories for dropdown
     */
    public function getCategoriesForDropdown()
    {
        return $this->select('id, kategori_utama, sub_kategori')
                   ->where('status_aktif', 1)
                   ->orderBy('kategori_utama', 'ASC')
                   ->findAll();
    }

    /**
     * Check if category name exists
     */
    public function categoryExists($kategoriUtama, $excludeId = null)
    {
        $builder = $this->where('kategori_utama', $kategoriUtama);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Check if category is being used in waste types (Alternative method using Query Builder)
     */
    public function isUsedInWasteTypesAlternative($categoryId)
    {
        // Get the category name first
        $category = $this->find($categoryId);
        if (!$category) {
            return false;
        }
        
        $db = \Config\Database::connect();
        
        // Use Query Builder to avoid collation issues
        $count = $db->table('master_harga_sampah')
                   ->where('jenis_sampah', $category['kategori_utama'])
                   ->countAllResults();
        
        return $count > 0;
    }

    /**
     * Get usage count for category (Alternative method using Query Builder)
     */
    public function getUsageCountAlternative($categoryId)
    {
        // Get the category name first
        $category = $this->find($categoryId);
        if (!$category) {
            return 0;
        }
        
        $db = \Config\Database::connect();
        
        // Use Query Builder to avoid collation issues
        $count = $db->table('master_harga_sampah')
                   ->where('jenis_sampah', $category['kategori_utama'])
                   ->countAllResults();
        
        return $count;
    }
}