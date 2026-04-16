<?php

namespace App\Models;

use CodeIgniter\Model;

class PopulationDataModel extends Model
{
    protected $table            = 'population_data';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tahun_akademik',
        'jumlah_dosen',
        'jumlah_mahasiswa',
        'jumlah_tenaga_kependidikan',
        'total_populasi',
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
        'jumlah_dosen' => 'required|integer|greater_than_equal_to[0]',
        'jumlah_mahasiswa' => 'required|integer|greater_than_equal_to[0]',
        'jumlah_tenaga_kependidikan' => 'required|integer|greater_than_equal_to[0]',
        'input_by' => 'required|integer'
    ];

    protected $validationMessages = [
        'tahun_akademik' => [
            'required' => 'Tahun akademik harus diisi',
            'max_length' => 'Tahun akademik terlalu panjang'
        ],
        'jumlah_dosen' => [
            'required' => 'Jumlah dosen harus diisi',
            'integer' => 'Jumlah dosen harus berupa angka',
            'greater_than_equal_to' => 'Jumlah dosen tidak boleh negatif'
        ],
        'jumlah_mahasiswa' => [
            'required' => 'Jumlah mahasiswa harus diisi',
            'integer' => 'Jumlah mahasiswa harus berupa angka',
            'greater_than_equal_to' => 'Jumlah mahasiswa tidak boleh negatif'
        ],
        'jumlah_tenaga_kependidikan' => [
            'required' => 'Jumlah tenaga kependidikan harus diisi',
            'integer' => 'Jumlah tenaga kependidikan harus berupa angka',
            'greater_than_equal_to' => 'Jumlah tenaga kependidikan tidak boleh negatif'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['calculateTotalPopulation'];
    protected $beforeUpdate   = ['calculateTotalPopulation'];
    protected $afterInsert    = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Calculate total population before save
     */
    protected function calculateTotalPopulation(array $data)
    {
        if (isset($data['data'])) {
            $dosen = $data['data']['jumlah_dosen'] ?? 0;
            $mahasiswa = $data['data']['jumlah_mahasiswa'] ?? 0;
            $tendik = $data['data']['jumlah_tenaga_kependidikan'] ?? 0;
            
            $data['data']['total_populasi'] = $dosen + $mahasiswa + $tendik;
        }
        
        return $data;
    }

    /**
     * Get active population data by academic year
     */
    public function getByAcademicYear(string $tahunAkademik)
    {
        return $this->where('tahun_akademik', $tahunAkademik)
            ->where('status_aktif', 1)
            ->first();
    }

    /**
     * Get latest active population data
     */
    public function getLatest()
    {
        return $this->where('status_aktif', 1)
            ->orderBy('created_at', 'DESC')
            ->first();
    }

    /**
     * Get all active population data with admin info
     */
    public function getAllWithAdmin()
    {
        return $this->select('population_data.*, users.nama_lengkap as admin_nama')
            ->join('users', 'users.id = population_data.input_by', 'left')
            ->where('population_data.status_aktif', 1)
            ->orderBy('population_data.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get population statistics
     */
    public function getPopulationStats()
    {
        $data = $this->getLatest();
        
        if (!$data) {
            return [
                'jumlah_dosen' => 0,
                'jumlah_mahasiswa' => 0,
                'jumlah_tenaga_kependidikan' => 0,
                'total_populasi' => 0,
                'dosen_percentage' => 0,
                'mahasiswa_percentage' => 0,
                'tendik_percentage' => 0,
                'tahun_akademik' => 'N/A'
            ];
        }
        
        $total = $data['total_populasi'];
        
        return [
            'jumlah_dosen' => $data['jumlah_dosen'],
            'jumlah_mahasiswa' => $data['jumlah_mahasiswa'],
            'jumlah_tenaga_kependidikan' => $data['jumlah_tenaga_kependidikan'],
            'total_populasi' => $total,
            'dosen_percentage' => $total > 0 ? round(($data['jumlah_dosen'] / $total) * 100, 2) : 0,
            'mahasiswa_percentage' => $total > 0 ? round(($data['jumlah_mahasiswa'] / $total) * 100, 2) : 0,
            'tendik_percentage' => $total > 0 ? round(($data['jumlah_tenaga_kependidikan'] / $total) * 100, 2) : 0,
            'tahun_akademik' => $data['tahun_akademik']
        ];
    }

    /**
     * Calculate vehicle to population ratio
     */
    public function calculateVehicleRatio(int $totalVehicles)
    {
        $data = $this->getLatest();
        
        if (!$data || $data['total_populasi'] <= 0) {
            return 0;
        }
        
        return round(($totalVehicles / $data['total_populasi']) * 100, 2);
    }

    /**
     * Get year-over-year comparison
     */
    public function getYearComparison()
    {
        return $this->select('tahun_akademik, total_populasi, jumlah_dosen, jumlah_mahasiswa, jumlah_tenaga_kependidikan')
            ->where('status_aktif', 1)
            ->orderBy('tahun_akademik', 'DESC')
            ->limit(5)
            ->findAll();
    }
}