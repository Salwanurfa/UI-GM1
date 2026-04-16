<?php

namespace App\Models;

use CodeIgniter\Model;

class InfrastructureDataModel extends Model
{
    protected $table            = 'infrastructure_data';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tahun_akademik',
        'luas_total_kampus',
        'luas_area_parkir_total',
        'luas_parkir_terbuka',
        'luas_parkir_berkanopi',
        'keterangan',
        'status_aktif',
        'input_by'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'tahun_akademik' => 'required|max_length[20]',
        'luas_total_kampus' => 'required|decimal|greater_than[0]',
        'luas_area_parkir_total' => 'required|decimal|greater_than_equal_to[0]',
        'luas_parkir_terbuka' => 'decimal|greater_than_equal_to[0]',
        'luas_parkir_berkanopi' => 'decimal|greater_than_equal_to[0]',
        'input_by' => 'required|integer'
    ];

    protected $validationMessages = [
        'tahun_akademik' => [
            'required' => 'Tahun akademik harus diisi',
            'max_length' => 'Tahun akademik terlalu panjang'
        ],
        'luas_total_kampus' => [
            'required' => 'Luas total kampus harus diisi',
            'decimal' => 'Luas total kampus harus berupa angka',
            'greater_than' => 'Luas total kampus harus lebih dari 0'
        ],
        'luas_area_parkir_total' => [
            'required' => 'Luas area parkir total harus diisi',
            'decimal' => 'Luas area parkir harus berupa angka',
            'greater_than_equal_to' => 'Luas area parkir tidak boleh negatif'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['calculateParkingBreakdown'];
    protected $beforeUpdate   = ['calculateParkingBreakdown'];
    protected $afterInsert    = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Calculate parking breakdown before save
     */
    protected function calculateParkingBreakdown(array $data)
    {
        if (isset($data['data'])) {
            $luasTerbuka = $data['data']['luas_parkir_terbuka'] ?? 0;
            $luasBerkanopi = $data['data']['luas_parkir_berkanopi'] ?? 0;
            $luasTotal = $data['data']['luas_area_parkir_total'] ?? 0;
            
            // Auto-calculate if breakdown not provided
            if ($luasTerbuka == 0 && $luasBerkanopi == 0 && $luasTotal > 0) {
                // Default: assume all parking is open area
                $data['data']['luas_parkir_terbuka'] = $luasTotal;
                $data['data']['luas_parkir_berkanopi'] = 0;
            }
        }
        
        return $data;
    }

    /**
     * Get active infrastructure data by academic year
     */
    public function getByAcademicYear(string $tahunAkademik)
    {
        return $this->where('tahun_akademik', $tahunAkademik)
            ->where('status_aktif', 1)
            ->first();
    }

    /**
     * Get latest active infrastructure data
     */
    public function getLatest()
    {
        return $this->where('status_aktif', 1)
            ->orderBy('created_at', 'DESC')
            ->first();
    }

    /**
     * Get all active infrastructure data with admin info
     */
    public function getAllWithAdmin()
    {
        return $this->select('infrastructure_data.*, users.nama_lengkap as admin_nama')
            ->join('users', 'users.id = infrastructure_data.input_by', 'left')
            ->where('infrastructure_data.status_aktif', 1)
            ->orderBy('infrastructure_data.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Calculate parking ratio
     */
    public function calculateParkingRatio(array $data = null)
    {
        if (!$data) {
            $data = $this->getLatest();
        }
        
        if (!$data || $data['luas_total_kampus'] <= 0) {
            return 0;
        }
        
        return round(($data['luas_area_parkir_total'] / $data['luas_total_kampus']) * 100, 2);
    }

    /**
     * Get parking statistics
     */
    public function getParkingStats()
    {
        $data = $this->getLatest();
        
        if (!$data) {
            return [
                'luas_total_kampus' => 0,
                'luas_area_parkir_total' => 0,
                'luas_parkir_terbuka' => 0,
                'luas_parkir_berkanopi' => 0,
                'parking_ratio' => 0,
                'open_parking_percentage' => 0,
                'covered_parking_percentage' => 0
            ];
        }
        
        $parkingRatio = $this->calculateParkingRatio($data);
        $openPercentage = $data['luas_area_parkir_total'] > 0 ? 
            round(($data['luas_parkir_terbuka'] / $data['luas_area_parkir_total']) * 100, 2) : 0;
        $coveredPercentage = $data['luas_area_parkir_total'] > 0 ? 
            round(($data['luas_parkir_berkanopi'] / $data['luas_area_parkir_total']) * 100, 2) : 0;
        
        return [
            'luas_total_kampus' => $data['luas_total_kampus'],
            'luas_area_parkir_total' => $data['luas_area_parkir_total'],
            'luas_parkir_terbuka' => $data['luas_parkir_terbuka'],
            'luas_parkir_berkanopi' => $data['luas_parkir_berkanopi'],
            'parking_ratio' => $parkingRatio,
            'open_parking_percentage' => $openPercentage,
            'covered_parking_percentage' => $coveredPercentage,
            'tahun_akademik' => $data['tahun_akademik']
        ];
    }
}