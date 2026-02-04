<?php

// File: app/Models/KriteriaModel.php

namespace App\Models;

use CodeIgniter\Model;

class KriteriaModel extends Model
{
    protected $table = 'kriteria_uigm';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id',
        'unit_id',
        'tanggal',
        'unit_prodi',
        'jenis_sampah',
        'satuan',
        'jumlah',
        'gedung',
        'status_review',
        'reviewed_by',
        'reviewed_at',
        'catatan_review',
        'created_at'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = '';
    protected $deletedField = '';

    // Validation
    protected $validationRules = [
        'user_id' => 'required|integer',
        'tanggal' => 'required|valid_date[Y-m-d]',
        'unit_prodi' => 'required|min_length[3]|max_length[100]',
        'jenis_sampah' => 'required|in_list[Kertas,Plastik,Organik,Anorganik,Limbah Cair,B3]',
        'satuan' => 'required|max_length[10]',
        'jumlah' => 'required|numeric|greater_than[0]',
        'gedung' => 'required|max_length[50]'
    ];

    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID harus diisi.',
            'integer' => 'User ID harus berupa angka.'
        ],
        'tanggal' => [
            'required' => 'Tanggal harus diisi.',
            'valid_date' => 'Format tanggal tidak valid.'
        ],
        'unit_prodi' => [
            'required' => 'Unit/Jurusan/Prodi harus diisi.',
            'min_length' => 'Unit/Jurusan/Prodi minimal 3 karakter.',
            'max_length' => 'Unit/Jurusan/Prodi maksimal 100 karakter.'
        ],
        'jenis_sampah' => [
            'required' => 'Jenis sampah harus dipilih.',
            'in_list' => 'Jenis sampah tidak valid.'
        ],
        'satuan' => [
            'required' => 'Satuan harus diisi.',
            'max_length' => 'Satuan maksimal 10 karakter.'
        ],
        'jumlah' => [
            'required' => 'Jumlah harus diisi.',
            'numeric' => 'Jumlah harus berupa angka.',
            'greater_than' => 'Jumlah harus lebih dari 0.'
        ],
        'gedung' => [
            'required' => 'Gedung harus dipilih.',
            'max_length' => 'Nama gedung maksimal 50 karakter.'
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
     * Ambil data berdasarkan user_id
     */
    public function getByUserId($userId, $jenisFilter = null)
    {
        $builder = $this->select('kriteria_uigm.*, unit.nama_unit')
                       ->join('unit', 'unit.id = kriteria_uigm.unit_id', 'left')
                       ->where('kriteria_uigm.user_id', $userId);
        
        if ($jenisFilter && $jenisFilter !== 'Semua') {
            $builder->where('kriteria_uigm.jenis_sampah', $jenisFilter);
        }
        
        return $builder->orderBy('kriteria_uigm.created_at', 'DESC')->findAll();
    }

    /**
     * Ambil data untuk admin pusat dengan filter
     */
    public function getForAdminPusat($filters = [])
    {
        $builder = $this->select('kriteria_uigm.*, users.nama_lengkap, users.username, unit.nama_unit, unit.kode_unit')
                       ->join('users', 'users.id = kriteria_uigm.user_id')
                       ->join('unit', 'unit.id = kriteria_uigm.unit_id', 'left');

        // Apply filters
        if (!empty($filters['unit_id'])) {
            $builder->where('kriteria_uigm.unit_id', $filters['unit_id']);
        }

        if (!empty($filters['status_review'])) {
            $builder->where('kriteria_uigm.status_review', $filters['status_review']);
        }

        if (!empty($filters['jenis_sampah'])) {
            $builder->where('kriteria_uigm.jenis_sampah', $filters['jenis_sampah']);
        }

        if (!empty($filters['tanggal_dari'])) {
            $builder->where('kriteria_uigm.tanggal >=', $filters['tanggal_dari']);
        }

        if (!empty($filters['tanggal_sampai'])) {
            $builder->where('kriteria_uigm.tanggal <=', $filters['tanggal_sampai']);
        }

        return $builder->orderBy('kriteria_uigm.created_at', 'DESC')->findAll();
    }

    /**
     * Statistik untuk dashboard admin pusat
     */
    public function getStatsForAdminPusat()
    {
        $stats = [];
        
        // Total data
        $stats['total_data'] = $this->countAllResults();
        
        // Data berdasarkan status review
        $statusStats = $this->select('status_review, COUNT(*) as jumlah')
                           ->groupBy('status_review')
                           ->findAll();
        
        $stats['pending'] = 0;
        $stats['approved'] = 0;
        $stats['rejected'] = 0;
        
        foreach ($statusStats as $stat) {
            $stats[$stat['status_review']] = $stat['jumlah'];
        }
        
        // Data berdasarkan jenis sampah
        $jenisStats = $this->select('jenis_sampah, COUNT(*) as jumlah, SUM(jumlah) as total_berat')
                          ->groupBy('jenis_sampah')
                          ->findAll();
        
        $stats['jenis_sampah'] = $jenisStats;
        
        // Data berdasarkan unit
        $unitStats = $this->select('unit.nama_unit, COUNT(*) as jumlah_data, SUM(kriteria_uigm.jumlah) as total_sampah')
                         ->join('unit', 'unit.id = kriteria_uigm.unit_id', 'left')
                         ->groupBy('kriteria_uigm.unit_id')
                         ->findAll();
        
        $stats['unit_stats'] = $unitStats;
        
        return $stats;
    }

    /**
     * Update status review
     */
    public function updateReviewStatus($id, $status, $reviewerId, $catatan = null)
    {
        $data = [
            'status_review' => $status,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => date('Y-m-d H:i:s'),
            'catatan_review' => $catatan
        ];
        
        return $this->update($id, $data);
    }

    /**
     * Ambil statistik data user
     */
    public function getUserStats($userId)
    {
        return [
            'total_data' => $this->where('user_id', $userId)->countAllResults(),
            'total_berat_kg' => $this->where('user_id', $userId)
                                   ->whereIn('satuan', ['kg'])
                                   ->selectSum('jumlah', 'total')
                                   ->first()['total'] ?? 0,
            'total_volume_l' => $this->where('user_id', $userId)
                                    ->whereIn('satuan', ['L', 'mL'])
                                    ->selectSum('jumlah', 'total')
                                    ->first()['total'] ?? 0,
            'jenis_terbanyak' => $this->where('user_id', $userId)
                                     ->select('jenis_sampah, COUNT(*) as count')
                                     ->groupBy('jenis_sampah')
                                     ->orderBy('count', 'DESC')
                                     ->first()['jenis_sampah'] ?? '-'
        ];
    }

    /**
     * Validasi kepemilikan data
     */
    public function isOwnedByUser($id, $userId)
    {
        $data = $this->where(['id' => $id, 'user_id' => $userId])->first();
        return !empty($data);
    }
}