<?php

namespace App\Models;

use CodeIgniter\Model;

class UigmIndicatorModel extends Model
{
    protected $table = 'uigm_indicators';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'tahun',
        'kode_indikator', 
        'nama_indikator',
        'target_capaian',
        'bukti_dukung',
        'status_bukti',
        'created_by'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'tahun' => 'required|integer|greater_than[2020]|less_than[2050]',
        'kode_indikator' => 'required|in_list[WS.1,WS.2,WS.3,WS.4,WS.5,WS.6,WS.7]',
        'nama_indikator' => 'required|min_length[10]|max_length[255]',
        'target_capaian' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
        'created_by' => 'required|integer'
    ];

    protected $validationMessages = [
        'tahun' => [
            'required' => 'Tahun harus diisi',
            'integer' => 'Tahun harus berupa angka',
            'greater_than' => 'Tahun minimal 2021',
            'less_than' => 'Tahun maksimal 2049'
        ],
        'kode_indikator' => [
            'required' => 'Kode indikator harus dipilih',
            'in_list' => 'Kode indikator tidak valid'
        ],
        'nama_indikator' => [
            'required' => 'Nama indikator harus diisi',
            'min_length' => 'Nama indikator minimal 10 karakter',
            'max_length' => 'Nama indikator maksimal 255 karakter'
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
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Get indicators with user information
     */
    public function getIndicatorsWithUser($tahun = null)
    {
        $builder = $this->db->table($this->table . ' ui')
            ->select('ui.*, u.nama_lengkap as created_by_name')
            ->join('users u', 'u.id = ui.created_by', 'left');
            
        if ($tahun) {
            $builder->where('ui.tahun', $tahun);
        }
        
        return $builder->orderBy('ui.tahun', 'DESC')
                      ->orderBy('ui.kode_indikator', 'ASC')
                      ->get()
                      ->getResultArray();
    }

    /**
     * Check if indicator already exists for specific year and code
     */
    public function isIndicatorExists($tahun, $kodeIndikator, $excludeId = null)
    {
        $builder = $this->where('tahun', $tahun)
                       ->where('kode_indikator', $kodeIndikator);
                       
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Get statistics for dashboard
     */
    public function getStatistics($tahun = null)
    {
        $builder = $this->db->table($this->table);
        
        if ($tahun) {
            $builder->where('tahun', $tahun);
        }
        
        $total = $builder->countAllResults(false);
        $withEvidence = $builder->where('status_bukti', 'sudah_upload')->countAllResults(false);
        $avgTarget = $builder->selectAvg('target_capaian')->get()->getRow()->target_capaian ?? 0;
        
        return [
            'total_indicators' => $total,
            'with_evidence' => $withEvidence,
            'without_evidence' => $total - $withEvidence,
            'avg_target' => round($avgTarget, 1)
        ];
    }

    /**
     * Get available indicator options
     */
    public static function getIndicatorOptions()
    {
        return [
            'WS.1' => '(WS.1) Program 3R (Reduce, Reuse, Recycle)',
            'WS.2' => '(WS.2) Program Pengurangan Kertas & Plastik',
            'WS.3' => '(WS.3) Pengolahan Limbah Organik',
            'WS.4' => '(WS.4) Pengolahan Limbah Anorganik',
            'WS.5' => '(WS.5) Pengolahan Limbah B3',
            'WS.6' => '(WS.6) Pengolahan Limbah Cair',
            'WS.7' => '(WS.7) Persentase Sampah Didaur Ulang'
        ];
    }
}