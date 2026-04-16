<?php

namespace App\Models;

use CodeIgniter\Model;

class UigmCategoryModel extends Model
{
    protected $table = 'uigm_categories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'kode_kategori',
        'nama_kategori',
        'deskripsi',
        'icon_class',
        'color_class',
        'target_capaian',
        'tahun',
        'status_aktif',
        'created_by'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'kode_kategori' => 'required|in_list[WS.1,WS.2,WS.3,WS.4,WS.5,WS.6,WS.7]',
        'nama_kategori' => 'required|min_length[10]|max_length[255]',
        'target_capaian' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
        'tahun' => 'required|integer|greater_than[2020]|less_than[2050]',
        'created_by' => 'required|integer'
    ];

    protected $validationMessages = [
        'kode_kategori' => [
            'required' => 'Kode kategori harus dipilih',
            'in_list' => 'Kode kategori tidak valid'
        ],
        'nama_kategori' => [
            'required' => 'Nama kategori harus diisi',
            'min_length' => 'Nama kategori minimal 10 karakter',
            'max_length' => 'Nama kategori maksimal 255 karakter'
        ],
        'target_capaian' => [
            'required' => 'Target capaian harus diisi',
            'decimal' => 'Target capaian harus berupa angka desimal',
            'greater_than_equal_to' => 'Target capaian minimal 0%',
            'less_than_equal_to' => 'Target capaian maksimal 100%'
        ]
    ];

    // Skip validation
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;

    /**
     * Get categories with evidence progress
     */
    public function getCategoriesWithProgress($tahun = null)
    {
        $builder = $this->db->table($this->table . ' uc')
            ->select('uc.*, 
                     COUNT(ue.id) as total_evidence,
                     SUM(CASE WHEN ue.status_upload = "sudah_upload" THEN 1 ELSE 0 END) as uploaded_evidence,
                     u.nama_lengkap as created_by_name')
            ->join('uigm_evidence ue', 'ue.kategori_id = uc.id', 'left')
            ->join('users u', 'u.id = uc.created_by', 'left')
            ->where('uc.status_aktif', 1);
            
        if ($tahun) {
            $builder->where('uc.tahun', $tahun);
        }
        
        return $builder->groupBy('uc.id')
                      ->orderBy('uc.kode_kategori', 'ASC')
                      ->get()
                      ->getResultArray();
    }

    /**
     * Get category by code and year
     */
    public function getCategoryByCode($kodeKategori, $tahun = null)
    {
        $builder = $this->where('kode_kategori', $kodeKategori)
                       ->where('status_aktif', 1);
                       
        if ($tahun) {
            $builder->where('tahun', $tahun);
        }
        
        return $builder->first();
    }

    /**
     * Check if category exists for specific year and code
     */
    public function isCategoryExists($tahun, $kodeKategori, $excludeId = null)
    {
        $builder = $this->where('tahun', $tahun)
                       ->where('kode_kategori', $kodeKategori)
                       ->where('status_aktif', 1);
                       
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats($tahun = null)
    {
        $builder = $this->db->table($this->table . ' uc')
            ->select('COUNT(uc.id) as total_categories,
                     AVG(uc.target_capaian) as avg_target,
                     COUNT(ue.id) as total_evidence,
                     SUM(CASE WHEN ue.status_upload = "sudah_upload" THEN 1 ELSE 0 END) as uploaded_evidence')
            ->join('uigm_evidence ue', 'ue.kategori_id = uc.id', 'left')
            ->where('uc.status_aktif', 1);
            
        if ($tahun) {
            $builder->where('uc.tahun', $tahun);
        }
        
        $result = $builder->get()->getRowArray();
        
        return [
            'total_categories' => $result['total_categories'] ?? 0,
            'avg_target' => round($result['avg_target'] ?? 0, 1),
            'total_evidence' => $result['total_evidence'] ?? 0,
            'uploaded_evidence' => $result['uploaded_evidence'] ?? 0,
            'pending_evidence' => ($result['total_evidence'] ?? 0) - ($result['uploaded_evidence'] ?? 0),
            'completion_rate' => $result['total_evidence'] > 0 ? 
                round(($result['uploaded_evidence'] / $result['total_evidence']) * 100, 1) : 0
        ];
    }

    /**
     * Update target capaian
     */
    public function updateTarget($id, $targetCapaian)
    {
        return $this->update($id, ['target_capaian' => $targetCapaian]);
    }
}