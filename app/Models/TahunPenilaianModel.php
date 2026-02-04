<?php

namespace App\Models;

use CodeIgniter\Model;

class TahunPenilaianModel extends Model
{
    protected $table            = 'tahun_penilaian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tahun',
        'nama_periode',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_aktif'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'status_aktif' => 'boolean',
    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'tahun' => 'required|integer|min_length[4]|max_length[4]',
        'nama_periode' => 'required|max_length[100]',
        'tanggal_mulai' => 'required|valid_date',
        'tanggal_selesai' => 'required|valid_date',
    ];
    protected $validationMessages   = [
        'tahun' => [
            'required' => 'Tahun harus diisi',
            'integer' => 'Tahun harus berupa angka'
        ],
        'nama_periode' => [
            'required' => 'Nama periode harus diisi'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get active assessment year
     */
    public function getActiveYear()
    {
        return $this->where('status_aktif', true)->first();
    }

    /**
     * Get all assessment years ordered by year
     */
    public function getAllYears()
    {
        return $this->orderBy('tahun', 'DESC')->findAll();
    }

    /**
     * Set active year (deactivate others first)
     */
    public function setActiveYear(int $id)
    {
        $this->db->transStart();

        // Deactivate all years
        $this->set('status_aktif', false)->update();

        // Activate selected year
        $this->update($id, ['status_aktif' => true]);

        $this->db->transComplete();
        return $this->db->transStatus();
    }

    /**
     * Check if year is within assessment period
     */
    public function isWithinAssessmentPeriod(int $tahunId = null)
    {
        $year = $tahunId ? $this->find($tahunId) : $this->getActiveYear();

        if (!$year) {
            return false;
        }

        $now = date('Y-m-d');
        return $now >= $year['tanggal_mulai'] && $now <= $year['tanggal_selesai'];
    }

    /**
     * Get years with submission statistics
     */
    public function getYearsWithStats()
    {
        return $this->select('tahun_penilaian.*,
                             COUNT(pengiriman_unit.id) as total_submissions,
                             SUM(CASE WHEN pengiriman_unit.status_pengiriman = "disetujui" THEN 1 ELSE 0 END) as approved_submissions')
            ->join('pengiriman_unit', 'pengiriman_unit.tahun_penilaian_id = tahun_penilaian.id', 'left')
            ->groupBy('tahun_penilaian.id')
            ->orderBy('tahun_penilaian.tahun', 'DESC')
            ->findAll();
    }
}
