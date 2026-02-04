<?php

namespace App\Models;

use CodeIgniter\Model;

class RiwayatVersiModel extends Model
{
    protected $table            = 'riwayat_versi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pengiriman_id',
        'versi',
        'aksi',
        'deskripsi_perubahan',
        'data_sebelum',
        'data_sesudah',
        'user_id',
        'ip_address',
        'user_agent'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'versi' => 'int',
        'data_sebelum' => 'json',
        'data_sesudah' => 'json',
    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = null; // No updated_at for audit logs
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'pengiriman_id' => 'required|integer',
        'versi' => 'required|integer',
        'aksi' => 'required|in_list[create,update,submit,review,approve,reject,finalize]',
        'user_id' => 'required|integer',
    ];
    protected $validationMessages   = [
        'pengiriman_id' => [
            'required' => 'Pengiriman ID harus diisi',
            'integer' => 'Pengiriman ID harus berupa angka'
        ],
        'aksi' => [
            'required' => 'Aksi harus diisi',
            'in_list' => 'Aksi tidak valid'
        ],
        'user_id' => [
            'required' => 'User ID harus diisi',
            'integer' => 'User ID harus berupa angka'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['addMetadata'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Add metadata before insert
     */
    protected function addMetadata(array $data)
    {
        $request = \Config\Services::request();

        if (!isset($data['data']['ip_address'])) {
            $data['data']['ip_address'] = $request->getIPAddress();
        }

        if (!isset($data['data']['user_agent'])) {
            $data['data']['user_agent'] = $request->getUserAgent()->getAgentString();
        }

        return $data;
    }

    /**
     * Log activity for submission
     */
    public function logActivity(int $pengirimanId, string $aksi, int $userId, string $deskripsi = null, array $dataSesudah = null, array $dataSebelum = null)
    {
        // Get current version
        $currentVersion = $this->getLatestVersion($pengirimanId);
        $newVersion = $currentVersion + 1;

        return $this->insert([
            'pengiriman_id' => $pengirimanId,
            'versi' => $newVersion,
            'aksi' => $aksi,
            'deskripsi_perubahan' => $deskripsi,
            'data_sebelum' => $dataSebelum,
            'data_sesudah' => $dataSesudah,
            'user_id' => $userId
        ]);
    }

    /**
     * Get latest version number for submission
     */
    public function getLatestVersion(int $pengirimanId)
    {
        $latest = $this->selectMax('versi')
            ->where('pengiriman_id', $pengirimanId)
            ->get()
            ->getRow();

        return $latest ? $latest->versi : 0;
    }

    /**
     * Get version history for submission
     */
    public function getVersionHistory(int $pengirimanId)
    {
        return $this->select('riwayat_versi.*, users.nama_lengkap as user_name')
            ->join('users', 'users.id = riwayat_versi.user_id')
            ->where('riwayat_versi.pengiriman_id', $pengirimanId)
            ->orderBy('riwayat_versi.versi', 'DESC')
            ->findAll();
    }

    /**
     * Get specific version data
     */
    public function getVersionData(int $pengirimanId, int $versi)
    {
        return $this->where('pengiriman_id', $pengirimanId)
            ->where('versi', $versi)
            ->first();
    }

    /**
     * Log submission creation
     */
    public function logSubmissionCreate(int $pengirimanId, int $userId, array $data)
    {
        return $this->logActivity(
            $pengirimanId,
            'create',
            $userId,
            'Pengiriman data UIGM dibuat',
            $data
        );
    }

    /**
     * Log submission update
     */
    public function logSubmissionUpdate(int $pengirimanId, int $userId, array $dataSebelum, array $dataSesudah, string $deskripsi = null)
    {
        return $this->logActivity(
            $pengirimanId,
            'update',
            $userId,
            $deskripsi ?: 'Data UIGM diperbarui',
            $dataSesudah,
            $dataSebelum
        );
    }

    /**
     * Log submission submit
     */
    public function logSubmissionSubmit(int $pengirimanId, int $userId)
    {
        return $this->logActivity(
            $pengirimanId,
            'submit',
            $userId,
            'Data UIGM dikirim untuk review'
        );
    }

    /**
     * Log review action
     */
    public function logReviewAction(int $pengirimanId, int $reviewerId, string $aksi, array $reviewData, string $deskripsi = null)
    {
        $aksiMap = [
            'disetujui' => 'approve',
            'perlu_revisi' => 'reject'
        ];

        $aksiLog = $aksiMap[$aksi] ?? 'review';
        $deskripsiDefault = $aksi === 'disetujui' ? 'Data UIGM disetujui' : 'Data UIGM perlu revisi';

        return $this->logActivity(
            $pengirimanId,
            $aksiLog,
            $reviewerId,
            $deskripsi ?: $deskripsiDefault,
            $reviewData
        );
    }

    /**
     * Log finalization
     */
    public function logFinalization(int $pengirimanId, int $userId)
    {
        return $this->logActivity(
            $pengirimanId,
            'finalize',
            $userId,
            'Data UIGM difinalisasi'
        );
    }

    /**
     * Get audit trail summary
     */
    public function getAuditTrail(int $pengirimanId)
    {
        return $this->select('riwayat_versi.*, 
                             users.nama_lengkap as user_name,
                             users.role as user_role')
            ->join('users', 'users.id = riwayat_versi.user_id')
            ->where('riwayat_versi.pengiriman_id', $pengirimanId)
            ->orderBy('riwayat_versi.created_at', 'ASC')
            ->findAll();
    }

    /**
     * Get activities by user
     */
    public function getActivitiesByUser(int $userId, int $limit = 50)
    {
        return $this->select('riwayat_versi.*, 
                             pengiriman_unit.unit_id,
                             unit.nama_unit')
            ->join('pengiriman_unit', 'pengiriman_unit.id = riwayat_versi.pengiriman_id')
            ->join('unit', 'unit.id = pengiriman_unit.unit_id')
            ->where('riwayat_versi.user_id', $userId)
            ->orderBy('riwayat_versi.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get activities by date range
     */
    public function getActivitiesByDateRange(string $startDate, string $endDate, int $limit = 100)
    {
        return $this->select('riwayat_versi.*, 
                             users.nama_lengkap as user_name,
                             pengiriman_unit.unit_id,
                             unit.nama_unit')
            ->join('users', 'users.id = riwayat_versi.user_id')
            ->join('pengiriman_unit', 'pengiriman_unit.id = riwayat_versi.pengiriman_id')
            ->join('unit', 'unit.id = pengiriman_unit.unit_id')
            ->where('riwayat_versi.created_at >=', $startDate)
            ->where('riwayat_versi.created_at <=', $endDate)
            ->orderBy('riwayat_versi.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get activity statistics
     */
    public function getActivityStats(int $tahunId = null)
    {
        $builder = $this->select('aksi, COUNT(*) as count')
            ->join('pengiriman_unit', 'pengiriman_unit.id = riwayat_versi.pengiriman_id');

        if ($tahunId) {
            $builder->where('pengiriman_unit.tahun_penilaian_id', $tahunId);
        }

        return $builder->groupBy('aksi')
            ->orderBy('count', 'DESC')
            ->findAll();
    }

    /**
     * Clean old audit logs (keep only recent ones)
     */
    public function cleanOldLogs(int $keepDays = 365)
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$keepDays} days"));

        return $this->where('created_at <', $cutoffDate)->delete();
    }
}
