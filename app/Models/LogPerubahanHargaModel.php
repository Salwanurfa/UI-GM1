<?php

namespace App\Models;

use CodeIgniter\Model;

class LogPerubahanHargaModel extends Model
{
    protected $table            = 'log_perubahan_harga';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'jenis_sampah',
        'harga_lama',
        'harga_baru',
        'aksi',
        'admin_id',
        'admin_nama',
        'keterangan'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'harga_lama' => '?float',
        'harga_baru' => 'float',
        'admin_id' => 'int'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    /**
     * Get recent changes
     */
    public function getRecentChanges($limit = 10)
    {
        return $this->orderBy('created_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }

    /**
     * Get changes by jenis sampah
     */
    public function getChangesByJenis($jenisSampah, $limit = 5)
    {
        return $this->where('jenis_sampah', $jenisSampah)
                   ->orderBy('created_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }

    /**
     * Get changes by admin
     */
    public function getChangesByAdmin($adminId, $limit = 10)
    {
        return $this->where('admin_id', $adminId)
                   ->orderBy('created_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }

    /**
     * Get statistics for dashboard
     */
    public function getStatistics()
    {
        $today = date('Y-m-d');
        $thisWeek = date('Y-m-d', strtotime('-7 days'));
        $thisMonth = date('Y-m-d', strtotime('-30 days'));
        
        return [
            'total_changes' => $this->countAllResults(),
            'changes_today' => $this->where('DATE(created_at)', $today)->countAllResults(),
            'changes_this_week' => $this->where('created_at >=', $thisWeek)->countAllResults(),
            'changes_this_month' => $this->where('created_at >=', $thisMonth)->countAllResults()
        ];
    }
}