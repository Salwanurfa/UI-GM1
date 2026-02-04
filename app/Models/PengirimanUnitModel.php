<?php

namespace App\Models;

use CodeIgniter\Model;

class PengirimanUnitModel extends Model
{
    protected $table            = 'pengiriman_unit';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'unit_id',
        'tahun_penilaian_id',
        'status_pengiriman',
        'progress_persen',
        'tanggal_kirim',
        'tanggal_review',
        'tanggal_disetujui',
        'reviewer_id',
        'catatan_admin_pusat',
        'catatan_admin_unit',
        'catatan_revisi',
        'versi',
        'created_by'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'progress_persen' => 'float',
        'versi' => 'int',
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
        'unit_id' => 'required|integer',
        'tahun_penilaian_id' => 'required|integer',
        'status_pengiriman' => 'required|in_list[draft,dikirim,review,perlu_revisi,disetujui,ditolak]',
        'progress_persen' => 'permit_empty|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
    ];
    protected $validationMessages   = [
        'unit_id' => [
            'required' => 'Unit ID harus diisi',
            'integer' => 'Unit ID harus berupa angka'
        ],
        'progress_persen' => [
            'decimal' => 'Progress harus berupa angka desimal',
            'greater_than_equal_to' => 'Progress tidak boleh kurang dari 0',
            'less_than_equal_to' => 'Progress tidak boleh lebih dari 100'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['updateTimestamps'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Update timestamps based on status changes
     */
    protected function updateTimestamps(array $data)
    {
        if (isset($data['data']['status_pengiriman'])) {
            $status = $data['data']['status_pengiriman'];
            $now = date('Y-m-d H:i:s');

            switch ($status) {
                case 'dikirim':
                    if (!isset($data['data']['tanggal_kirim'])) {
                        $data['data']['tanggal_kirim'] = $now;
                    }
                    break;
                case 'review':
                case 'perlu_revisi':
                    if (!isset($data['data']['tanggal_review'])) {
                        $data['data']['tanggal_review'] = $now;
                    }
                    break;
                case 'disetujui':
                case 'final':
                    if (!isset($data['data']['tanggal_disetujui'])) {
                        $data['data']['tanggal_disetujui'] = $now;
                    }
                    break;
            }
        }
        return $data;
    }

    /**
     * Get submissions for review by year
     */
    public function getSubmissionsForReview(int $tahunId)
    {
        return $this->select('pengiriman_unit.*, 
                             unit.nama_unit, unit.kode_unit,
                             users.nama_lengkap as admin_name,
                             reviewer.nama_lengkap as reviewer_name')
            ->join('unit', 'unit.id = pengiriman_unit.unit_id')
            ->join('users', 'users.id = unit.admin_unit_id', 'left')
            ->join('users as reviewer', 'reviewer.id = pengiriman_unit.reviewer_id', 'left')
            ->where('pengiriman_unit.tahun_penilaian_id', $tahunId)
            ->whereIn('pengiriman_unit.status_pengiriman', ['dikirim', 'review', 'perlu_revisi'])
            ->orderBy('pengiriman_unit.tanggal_kirim', 'DESC')
            ->findAll();
    }

    /**
     * Update submission status with notes
     */
    public function updateSubmissionStatus(int $id, string $status, string $catatan = '', int $reviewerId = null)
    {
        $data = [
            'status_pengiriman' => $status,
            'catatan_admin_pusat' => $catatan
        ];

        if ($reviewerId) {
            $data['reviewer_id'] = $reviewerId;
        }

        return $this->update($id, $data);
    }

    /**
     * Get submission with unit and category details
     */
    public function getSubmissionWithDetails(int $pengirimanId)
    {
        return $this->select('pengiriman_unit.*, 
                             unit.nama_unit, unit.kode_unit,
                             tahun_penilaian.tahun, tahun_penilaian.nama_periode,
                             users.nama_lengkap as admin_name,
                             reviewer.nama_lengkap as reviewer_name')
            ->join('unit', 'unit.id = pengiriman_unit.unit_id')
            ->join('tahun_penilaian', 'tahun_penilaian.id = pengiriman_unit.tahun_penilaian_id')
            ->join('users', 'users.id = unit.admin_unit_id', 'left')
            ->join('users as reviewer', 'reviewer.id = pengiriman_unit.reviewer_id', 'left')
            ->find($pengirimanId);
    }

    /**
     * Calculate progress based on category reviews
     */
    public function calculateProgress(int $pengirimanId)
    {
        $reviewModel = new ReviewKategoriModel();
        $reviews = $reviewModel->where('pengiriman_id', $pengirimanId)->findAll();

        if (empty($reviews)) {
            return 0;
        }

        $totalCategories = 6; // UIGM has 6 categories
        $completedCategories = 0;

        foreach ($reviews as $review) {
            if ($review['status_review'] === 'disetujui') {
                $completedCategories++;
            }
        }

        $progress = ($completedCategories / $totalCategories) * 100;

        // Update progress in database
        $this->update($pengirimanId, ['progress_persen' => $progress]);

        return $progress;
    }

    /**
     * Get submissions by status
     */
    public function getSubmissionsByStatus(string $status, int $tahunId = null)
    {
        $builder = $this->where('status_pengiriman', $status);

        if ($tahunId) {
            $builder->where('tahun_penilaian_id', $tahunId);
        }

        return $builder->findAll();
    }

    /**
     * Get institutional progress summary
     */
    public function getInstitutionalProgress(int $tahunId)
    {
        $submissions = $this->where('tahun_penilaian_id', $tahunId)->findAll();

        if (empty($submissions)) {
            return 0;
        }

        $totalProgress = array_sum(array_column($submissions, 'progress_persen'));
        return $totalProgress / count($submissions);
    }
}
