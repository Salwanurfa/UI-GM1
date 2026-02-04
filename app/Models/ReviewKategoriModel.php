<?php

namespace App\Models;

use CodeIgniter\Model;

class ReviewKategoriModel extends Model
{
    protected $table            = 'review_kategori';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pengiriman_id',
        'indikator_id',
        'status_review',
        'catatan_review',
        'reviewer_id',
        'tanggal_review',
        'skor_review',
        'data_input'  // Field penting yang hilang!
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'skor_review' => '?float',  // Nullable float
        'pengiriman_id' => 'int',
        'indikator_id' => 'int',
        'reviewer_id' => '?int',    // Nullable int
        // Remove JSON casting to avoid DataCaster conflicts
        // 'data_input' will be handled manually with safe_json_decode()
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
        'pengiriman_id' => 'required|integer',
        'indikator_id' => 'required|integer',
        'status_review' => 'required|in_list[pending,disetujui,perlu_revisi]',
        'skor_review' => 'permit_empty|decimal|greater_than_equal_to[0]',
        'data_input' => 'permit_empty|valid_json',
    ];
    protected $validationMessages   = [
        'pengiriman_id' => [
            'required' => 'Pengiriman ID harus diisi',
            'integer' => 'Pengiriman ID harus berupa angka'
        ],
        'indikator_id' => [
            'required' => 'Indikator ID harus diisi',
            'integer' => 'Indikator ID harus berupa angka'
        ],
        'status_review' => [
            'required' => 'Status review harus diisi',
            'in_list' => 'Status review tidak valid'
        ],
        'data_input' => [
            'valid_json' => 'Data input harus berupa JSON yang valid'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = ['updateSubmissionProgress'];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = ['updateSubmissionProgress'];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Update submission progress after review changes
     */
    protected function updateSubmissionProgress(array $data)
    {
        if (isset($data['data']['pengiriman_id']) || isset($data['id'])) {
            $pengirimanId = $data['data']['pengiriman_id'] ?? $data['id'];
            $pengirimanModel = new PengirimanUnitModel();
            $pengirimanModel->calculateProgress($pengirimanId);
        }
        return $data;
    }

    /**
     * Get reviews by submission with category details
     */
    public function getReviewsBySubmission(int $pengirimanId)
    {
        return $this->select('review_kategori.*, 
                             indikator.nama_kategori, indikator.kode_kategori, 
                             indikator.bobot_persen as bobot, indikator.urutan,
                             users.nama_lengkap as reviewer_name')
            ->join('indikator', 'indikator.id = review_kategori.indikator_id')
            ->join('users', 'users.id = review_kategori.reviewer_id', 'left')
            ->where('review_kategori.pengiriman_id', $pengirimanId)
            ->orderBy('indikator.urutan')
            ->findAll();
    }

    /**
     * Update category review
     */
    public function updateCategoryReview(int $pengirimanId, int $indikatorId, array $data)
    {
        $existing = $this->where('pengiriman_id', $pengirimanId)
            ->where('indikator_id', $indikatorId)
            ->first();

        $reviewData = [
            'pengiriman_id' => $pengirimanId,
            'indikator_id' => $indikatorId,
            'status_review' => $data['status_review'],
            'catatan_review' => $data['catatan_review'] ?? null,
            'reviewer_id' => $data['reviewer_id'] ?? null,
            'tanggal_review' => date('Y-m-d H:i:s')
        ];

        // Handle skor_review dengan benar
        if (isset($data['skor_review'])) {
            if (is_numeric($data['skor_review']) && $data['skor_review'] !== '') {
                $reviewData['skor_review'] = (float)$data['skor_review'];
            } else {
                $reviewData['skor_review'] = null;
            }
        }

        // Handle data_input dengan benar
        if (isset($data['data_input'])) {
            if (is_string($data['data_input']) && !empty($data['data_input'])) {
                $reviewData['data_input'] = $data['data_input'];
            } elseif (is_array($data['data_input'])) {
                $reviewData['data_input'] = json_encode($data['data_input']);
            } else {
                $reviewData['data_input'] = '{}'; // Empty JSON object
            }
        } else {
            $reviewData['data_input'] = '{}'; // Default empty JSON object
        }

        if ($existing) {
            return $this->update($existing['id'], $reviewData);
        } else {
            return $this->insert($reviewData);
        }
    }

    /**
     * Get review summary for submission
     */
    public function getReviewSummary(int $pengirimanId)
    {
        $reviews = $this->where('pengiriman_id', $pengirimanId)->findAll();

        $summary = [
            'total_categories' => 6,
            'reviewed_categories' => 0,
            'approved_categories' => 0,
            'revision_categories' => 0,
            'pending_categories' => 0,
            'overall_status' => 'pending'
        ];

        foreach ($reviews as $review) {
            if ($review['status_review'] !== 'pending') {
                $summary['reviewed_categories']++;
            }

            switch ($review['status_review']) {
                case 'disetujui':
                    $summary['approved_categories']++;
                    break;
                case 'perlu_revisi':
                    $summary['revision_categories']++;
                    break;
                case 'pending':
                    $summary['pending_categories']++;
                    break;
            }
        }

        // Determine overall status
        if ($summary['approved_categories'] === $summary['total_categories']) {
            $summary['overall_status'] = 'disetujui';
        } elseif ($summary['revision_categories'] > 0) {
            $summary['overall_status'] = 'perlu_revisi';
        } elseif ($summary['reviewed_categories'] > 0) {
            $summary['overall_status'] = 'review';
        }

        return $summary;
    }

    /**
     * Get categories needing revision for a submission
     */
    public function getCategoriesNeedingRevision(int $pengirimanId)
    {
        return $this->select('review_kategori.*, indikator.nama_kategori, indikator.kode_kategori')
            ->join('indikator', 'indikator.id = review_kategori.indikator_id')
            ->where('review_kategori.pengiriman_id', $pengirimanId)
            ->where('review_kategori.status_review', 'perlu_revisi')
            ->orderBy('indikator.urutan')
            ->findAll();
    }

    /**
     * Bulk update reviews for submission
     */
    public function bulkUpdateReviews(int $pengirimanId, array $reviews, int $reviewerId)
    {
        $this->db->transStart();

        foreach ($reviews as $indikatorId => $reviewData) {
            $this->updateCategoryReview($pengirimanId, $indikatorId, [
                'status_review' => $reviewData['status'],
                'catatan_review' => $reviewData['catatan'] ?? null,
                'reviewer_id' => $reviewerId,
                'skor_review' => $reviewData['skor'] ?? null
            ]);
        }

        $this->db->transComplete();
        return $this->db->transStatus();
    }

    /**
     * Get review statistics by category
     */
    public function getReviewStatsByCategory(int $tahunId)
    {
        return $this->select('indikator.nama_kategori, indikator.kode_kategori,
                             COUNT(*) as total_reviews,
                             SUM(CASE WHEN review_kategori.status_review = "disetujui" THEN 1 ELSE 0 END) as approved,
                             SUM(CASE WHEN review_kategori.status_review = "perlu_revisi" THEN 1 ELSE 0 END) as revision,
                             SUM(CASE WHEN review_kategori.status_review = "pending" THEN 1 ELSE 0 END) as pending')
            ->join('indikator', 'indikator.id = review_kategori.indikator_id')
            ->join('pengiriman_unit', 'pengiriman_unit.id = review_kategori.pengiriman_id')
            ->where('pengiriman_unit.tahun_penilaian_id', $tahunId)
            ->groupBy('indikator.id')
            ->orderBy('indikator.urutan')
            ->findAll();
    }
}
