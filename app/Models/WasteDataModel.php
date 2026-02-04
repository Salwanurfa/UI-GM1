<?php

namespace App\Models;

use CodeIgniter\Model;

class WasteDataModel extends Model
{
    protected $table            = 'waste_tps';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'tanggal',
        'jenis_sampah',
        'sampah_dari_gedung',
        'jumlah_berat',
        'berat',
        'satuan',
        'nilai_rupiah',
        'status',
        'created_by',
        'catatan'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'user_id' => 'int',
        'jumlah_berat' => 'float',
        'berat' => 'float',
        'nilai_rupiah' => '?float'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'user_id' => 'required|integer',
        'tanggal' => 'required|valid_date',
        'jenis_sampah' => 'required|max_length[100]',
        'sampah_dari_gedung' => 'required|max_length[255]',
        'jumlah_berat' => 'required|decimal|greater_than[0]',
        'satuan' => 'required|in_list[kg,liter,ton]',
        'nilai_rupiah' => 'permit_empty|decimal|greater_than_equal_to[0]',
        'status' => 'permit_empty|in_list[draft,dikirim,disetujui,perlu_revisi]',
    ];

    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID harus diisi',
            'integer' => 'User ID harus berupa angka'
        ],
        'tanggal' => [
            'required' => 'Tanggal harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'jenis_sampah' => [
            'required' => 'Jenis sampah harus diisi'
        ],
        'jumlah_berat' => [
            'required' => 'Jumlah berat harus diisi',
            'decimal' => 'Jumlah berat harus berupa angka',
            'greater_than' => 'Jumlah berat harus lebih dari 0'
        ]
    ];

    /**
     * Get waste data by user
     */
    public function getByUser(int $userId, array $filters = []): array
    {
        $builder = $this->where('user_id', $userId);
        
        if (!empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }
        
        if (!empty($filters['jenis_sampah'])) {
            $builder->where('jenis_sampah', $filters['jenis_sampah']);
        }
        
        if (!empty($filters['start_date'])) {
            $builder->where('tanggal >=', $filters['start_date']);
        }
        
        if (!empty($filters['end_date'])) {
            $builder->where('tanggal <=', $filters['end_date']);
        }
        
        return $builder->orderBy('created_at', 'DESC')->findAll();
    }

    /**
     * Get statistics for user
     */
    public function getUserStatistics(int $userId): array
    {
        $builder = $this->where('user_id', $userId);
        
        return [
            'total' => $builder->countAllResults(false),
            'disetujui' => $builder->where('status', 'disetujui')->countAllResults(false),
            'dikirim' => $builder->where('status', 'dikirim')->countAllResults(false),
            'perlu_revisi' => $builder->where('status', 'perlu_revisi')->countAllResults(false),
            'draft' => $builder->where('status', 'draft')->countAllResults(false),
            'total_berat' => $this->where('user_id', $userId)->selectSum('jumlah_berat')->first()['jumlah_berat'] ?? 0,
            'total_nilai' => $this->where('user_id', $userId)->selectSum('nilai_rupiah')->first()['nilai_rupiah'] ?? 0
        ];
    }

    /**
     * Get waste data by type
     */
    public function getByType(int $userId, string $jenisampah): array
    {
        return $this->where('user_id', $userId)
                   ->where('jenis_sampah', $jenisampah)
                   ->orderBy('tanggal', 'DESC')
                   ->findAll();
    }

    /**
     * Get recent waste data
     */
    public function getRecent(int $userId, int $limit = 10): array
    {
        return $this->where('user_id', $userId)
                   ->orderBy('updated_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }

    /**
     * Update status
     */
    public function updateStatus(int $id, string $status, string $catatan = ''): bool
    {
        $data = [
            'status' => $status,
            'catatan' => $catatan
        ];
        
        return $this->update($id, $data);
    }

    /**
     * Get monthly summary
     */
    public function getMonthlySummary(int $userId, int $year, int $month): array
    {
        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate));
        
        return $this->where('user_id', $userId)
                   ->where('tanggal >=', $startDate)
                   ->where('tanggal <=', $endDate)
                   ->select('jenis_sampah, SUM(jumlah_berat) as total_berat, SUM(nilai_rupiah) as total_nilai, COUNT(*) as total_data')
                   ->groupBy('jenis_sampah')
                   ->findAll();
    }

    /**
     * Check if user can edit data
     */
    public function canEdit(int $id, int $userId): bool
    {
        $data = $this->find($id);
        
        if (!$data || $data['user_id'] != $userId) {
            return false;
        }
        
        // Can only edit draft or revision required data
        return in_array($data['status'], ['draft', 'perlu_revisi']);
    }
}