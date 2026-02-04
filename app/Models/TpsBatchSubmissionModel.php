<?php

namespace App\Models;

use CodeIgniter\Model;

class TpsBatchSubmissionModel extends Model
{
    protected $table            = 'tps_batch_submissions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'batch_id',
        'tps_user_id',
        'submission_date',
        'total_items',
        'total_berat',
        'total_nilai',
        'periode_start',
        'periode_end',
        'catatan',
        'status',
        'reviewed_by',
        'reviewed_at'
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
    protected $validationRules      = [
        'batch_id' => 'required|max_length[50]|is_unique[tps_batch_submissions.batch_id]',
        'tps_user_id' => 'required|integer',
        'submission_date' => 'required|valid_date',
        'total_items' => 'required|integer',
        'total_berat' => 'required|decimal',
        'total_nilai' => 'required|decimal',
        'periode_start' => 'required|valid_date',
        'periode_end' => 'required|valid_date',
    ];
    
    protected $validationMessages   = [
        'batch_id' => [
            'required' => 'Batch ID harus diisi',
            'is_unique' => 'Batch ID sudah digunakan'
        ],
        'tps_user_id' => [
            'required' => 'TPS User ID harus diisi'
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
     * Get batch submissions with TPS user info
     */
    public function getBatchWithUser($batchId = null)
    {
        $builder = $this->select('tps_batch_submissions.*, users.nama_lengkap as tps_name, users.username as tps_username')
            ->join('users', 'users.id = tps_batch_submissions.tps_user_id', 'left');
        
        if ($batchId) {
            $builder->where('tps_batch_submissions.batch_id', $batchId);
            return $builder->first();
        }
        
        return $builder->orderBy('tps_batch_submissions.submission_date', 'DESC')->findAll();
    }

    /**
     * Get pending batches for admin review
     */
    public function getPendingBatches()
    {
        return $this->select('tps_batch_submissions.*, users.nama_lengkap as tps_name')
            ->join('users', 'users.id = tps_batch_submissions.tps_user_id', 'left')
            ->where('tps_batch_submissions.status', 'pending')
            ->orderBy('tps_batch_submissions.submission_date', 'ASC')
            ->findAll();
    }

    /**
     * Get batch items (waste data)
     */
    public function getBatchItems($batchId)
    {
        $db = \Config\Database::connect();
        return $db->table('waste_management')
            ->select('waste_management.*, unit.nama_unit, users.nama_lengkap as user_name')
            ->join('unit', 'unit.id = waste_management.unit_id', 'left')
            ->join('users', 'users.id = waste_management.created_by', 'left')
            ->where('waste_management.batch_id', $batchId)
            ->orderBy('waste_management.tanggal', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Generate unique batch ID
     */
    public function generateBatchId($tpsUserId)
    {
        $date = date('Ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));
        return "BATCH-{$date}-TPS{$tpsUserId}-{$random}";
    }

    /**
     * Get batch statistics
     */
    public function getBatchStatistics($batchId)
    {
        $db = \Config\Database::connect();
        
        $stats = $db->table('waste_management')
            ->select('
                COUNT(*) as total_items,
                SUM(berat) as total_berat,
                SUM(nilai_rupiah) as total_nilai,
                MIN(tanggal) as periode_start,
                MAX(tanggal) as periode_end
            ')
            ->where('batch_id', $batchId)
            ->get()
            ->getRowArray();
        
        return $stats;
    }
}
