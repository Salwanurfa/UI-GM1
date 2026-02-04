<?php

namespace App\Models;

use CodeIgniter\Model;

class IndikatorModel extends Model
{
    protected $table            = 'indikator';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode_kategori',
        'nama_kategori',
        'deskripsi',
        'bobot_persen',
        'warna',
        'urutan',
        'status_aktif'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'bobot_persen' => 'float',
        'urutan' => 'int',
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
        'kode_kategori' => 'required|max_length[10]|is_unique[indikator.kode_kategori,id,{id}]',
        'nama_kategori' => 'required|max_length[255]',
        'bobot_persen' => 'required|decimal|greater_than[0]|less_than_equal_to[100]',
        'urutan' => 'required|integer|greater_than[0]',
    ];
    protected $validationMessages   = [
        'kode_kategori' => [
            'required' => 'Kode kategori harus diisi',
            'is_unique' => 'Kode kategori sudah digunakan'
        ],
        'nama_kategori' => [
            'required' => 'Nama kategori harus diisi'
        ],
        'bobot_persen' => [
            'required' => 'Bobot harus diisi',
            'greater_than' => 'Bobot harus lebih dari 0'
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
     * Get all active UIGM categories in order
     */
    public function getUIGMCategories()
    {
        return $this->where('status_aktif', true)
            ->orderBy('urutan')
            ->findAll();
    }

    /**
     * Get category by code
     */
    public function getCategoryByCode(string $kodeKategori)
    {
        return $this->where('kode_kategori', $kodeKategori)
            ->where('status_aktif', true)
            ->first();
    }

    /**
     * Get categories with review statistics
     */
    public function getCategoriesWithStats(int $tahunId)
    {
        return $this->select('indikator.*,
                             COUNT(review_kategori.id) as total_reviews,
                             SUM(CASE WHEN review_kategori.status_review = "disetujui" THEN 1 ELSE 0 END) as approved_count,
                             SUM(CASE WHEN review_kategori.status_review = "perlu_revisi" THEN 1 ELSE 0 END) as revision_count,
                             SUM(CASE WHEN review_kategori.status_review = "pending" THEN 1 ELSE 0 END) as pending_count')
            ->join('review_kategori', 'review_kategori.indikator_id = indikator.id', 'left')
            ->join('pengiriman_unit', 'pengiriman_unit.id = review_kategori.pengiriman_id', 'left')
            ->where('indikator.status_aktif', true)
            ->where('pengiriman_unit.tahun_penilaian_id', $tahunId)
            ->groupBy('indikator.id')
            ->orderBy('indikator.urutan')
            ->findAll();
    }

    /**
     * Initialize default UIGM categories
     */
    public function initializeUIGMCategories()
    {
        $categories = [
            [
                'kode_kategori' => 'SI',
                'nama_kategori' => 'Setting & Infrastructure',
                'deskripsi' => 'Pengaturan dan Infrastruktur kampus yang mendukung keberlanjutan',
                'bobot_persen' => 15.00,
                'warna' => '#2E8B57',
                'urutan' => 1,
                'status_aktif' => true
            ],
            [
                'kode_kategori' => 'EC',
                'nama_kategori' => 'Energy & Climate Change',
                'deskripsi' => 'Penggunaan energi dan upaya mitigasi perubahan iklim',
                'bobot_persen' => 21.00,
                'warna' => '#FFD700',
                'urutan' => 2,
                'status_aktif' => true
            ],
            [
                'kode_kategori' => 'WS',
                'nama_kategori' => 'Waste',
                'deskripsi' => 'Pengelolaan limbah dan daur ulang',
                'bobot_persen' => 18.00,
                'warna' => '#8B4513',
                'urutan' => 3,
                'status_aktif' => true
            ],
            [
                'kode_kategori' => 'WR',
                'nama_kategori' => 'Water',
                'deskripsi' => 'Konservasi dan penggunaan air',
                'bobot_persen' => 10.00,
                'warna' => '#1E90FF',
                'urutan' => 4,
                'status_aktif' => true
            ],
            [
                'kode_kategori' => 'TR',
                'nama_kategori' => 'Transportation',
                'deskripsi' => 'Kebijakan transportasi berkelanjutan',
                'bobot_persen' => 18.00,
                'warna' => '#DC143C',
                'urutan' => 5,
                'status_aktif' => true
            ],
            [
                'kode_kategori' => 'ED',
                'nama_kategori' => 'Education & Research',
                'deskripsi' => 'Pendidikan dan penelitian keberlanjutan',
                'bobot_persen' => 18.00,
                'warna' => '#5c8cbf',
                'urutan' => 6,
                'status_aktif' => true
            ]
        ];

        foreach ($categories as $category) {
            $existing = $this->where('kode_kategori', $category['kode_kategori'])->first();
            if (!$existing) {
                $this->insert($category);
            }
        }

        return true;
    }

    /**
     * Validate total weight equals 100%
     */
    public function validateTotalWeight()
    {
        $totalWeight = $this->selectSum('bobot_persen')
            ->where('status_aktif', true)
            ->get()
            ->getRow()
            ->bobot_persen;

        return abs($totalWeight - 100.00) < 0.01; // Allow small floating point differences
    }

    /**
     * Get category progress summary
     */
    public function getCategoryProgressSummary(int $tahunId)
    {
        $categories = $this->getUIGMCategories();
        $summary = [];

        foreach ($categories as $category) {
            $reviewModel = new ReviewKategoriModel();
            $stats = $reviewModel->select('COUNT(*) as total,
                                         SUM(CASE WHEN status_review = "disetujui" THEN 1 ELSE 0 END) as approved')
                ->join('pengiriman_unit', 'pengiriman_unit.id = review_kategori.pengiriman_id')
                ->where('review_kategori.indikator_id', $category['id'])
                ->where('pengiriman_unit.tahun_penilaian_id', $tahunId)
                ->get()
                ->getRowArray();

            $summary[] = [
                'category' => $category,
                'total_submissions' => $stats['total'],
                'approved_submissions' => $stats['approved'],
                'completion_percentage' => $stats['total'] > 0 ? ($stats['approved'] / $stats['total']) * 100 : 0
            ];
        }

        return $summary;
    }
}
