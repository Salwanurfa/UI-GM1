<?php

namespace App\Models;

use CodeIgniter\Model;

class TpsWasteModel extends Model
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
        'sampah_dari_gedung',
        'jumlah_berat',
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
        'sampah_dari_gedung' => 'required|max_length[255]',
        'jumlah_berat' => 'required|decimal|greater_than[0]',
        'satuan' => 'required|in_list[kg,liter]',
        'nilai_rupiah' => 'permit_empty|decimal|greater_than_equal_to[0]',
        'status' => 'permit_empty|in_list[pending,approved,rejected]',
        'created_by' => 'required|max_length[100]'
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
        'sampah_dari_gedung' => [
            'required' => 'Gedung asal sampah harus diisi',
            'max_length' => 'Nama gedung terlalu panjang'
        ],
        'jumlah_berat' => [
            'required' => 'Jumlah berat harus diisi',
            'decimal' => 'Jumlah berat harus berupa angka',
            'greater_than' => 'Jumlah berat harus lebih dari 0'
        ],
        'satuan' => [
            'required' => 'Satuan harus diisi',
            'in_list' => 'Satuan harus kg atau liter'
        ],
        'nilai_rupiah' => [
            'decimal' => 'Nilai rupiah harus berupa angka',
            'greater_than_equal_to' => 'Nilai rupiah tidak boleh negatif'
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
     * Get TPS waste data with user information
     */
    public function getTpsWasteWithUser($userId = null)
    {
        $builder = $this->select('waste_tps.*, users.username, users.nama_lengkap')
                        ->join('users', 'users.id = waste_tps.user_id', 'left');
        
        if ($userId) {
            $builder->where('waste_tps.user_id', $userId);
        }
        
        return $builder->orderBy('waste_tps.created_at', 'DESC')->findAll();
    }

    /**
     * Get statistics for TPS waste
     */
    public function getTpsStatistics($userId = null)
    {
        $builder = $this->select('
            COUNT(*) as total_records,
            SUM(jumlah_berat) as total_berat,
            SUM(CASE WHEN nilai_rupiah IS NOT NULL THEN nilai_rupiah ELSE 0 END) as total_nilai,
            COUNT(CASE WHEN status = "pending" THEN 1 END) as pending_count,
            COUNT(CASE WHEN status = "approved" THEN 1 END) as approved_count,
            COUNT(CASE WHEN status = "rejected" THEN 1 END) as rejected_count
        ');
        
        if ($userId) {
            $builder->where('user_id', $userId);
        }
        
        return $builder->first();
    }

    /**
     * Get TPS waste data by date range
     */
    public function getTpsWasteByDateRange($startDate, $endDate, $userId = null)
    {
        $builder = $this->where('tanggal >=', $startDate)
                        ->where('tanggal <=', $endDate);
        
        if ($userId) {
            $builder->where('user_id', $userId);
        }
        
        return $builder->orderBy('tanggal', 'DESC')->findAll();
    }

    /**
     * Get TPS waste data by status
     */
    public function getTpsWasteByStatus($status, $userId = null)
    {
        $builder = $this->where('status', $status);
        
        if ($userId) {
            $builder->where('user_id', $userId);
        }
        
        return $builder->orderBy('created_at', 'DESC')->findAll();
    }
}