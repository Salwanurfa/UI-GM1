<?php

namespace App\Models;

use CodeIgniter\Model;

class HargaLogModel extends Model
{
    protected $table            = 'log_perubahan_harga';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'master_harga_id',
        'jenis_sampah',
        'harga_lama',
        'harga_baru',
        'perubahan_harga',
        'persentase_perubahan',
        'alasan_perubahan',
        'status_perubahan',
        'tanggal_berlaku',
        'created_by',
        'approved_by'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'master_harga_id' => 'int',
        'created_by' => 'int',
        'approved_by' => '?int',
        'harga_lama' => '?float',
        'harga_baru' => 'float',
        'perubahan_harga' => 'float',
        'persentase_perubahan' => 'float'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'master_harga_id' => 'required|integer',
        'jenis_sampah' => 'required|max_length[100]',
        'harga_baru' => 'required|decimal',
        'tanggal_berlaku' => 'required|valid_date',
        'created_by' => 'required|integer'
    ];

    protected $validationMessages = [
        'master_harga_id' => [
            'required' => 'Master Harga ID harus diisi',
            'integer' => 'Master Harga ID harus berupa angka'
        ],
        'jenis_sampah' => [
            'required' => 'Jenis sampah harus diisi'
        ],
        'harga_baru' => [
            'required' => 'Harga baru harus diisi',
            'decimal' => 'Harga harus berupa angka'
        ],
        'tanggal_berlaku' => [
            'required' => 'Tanggal berlaku harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'created_by' => [
            'required' => 'Created by harus diisi'
        ]
    ];

    /**
     * Log price change
     */
    public function logPriceChange(int $masterHargaId, string $jenisSampah, ?float $hargaLama, float $hargaBaru, int $createdBy, ?string $alasan = null): bool
    {
        $perubahanHarga = $hargaBaru - ($hargaLama ?? 0);
        $persentasePerubahan = 0;
        
        if ($hargaLama && $hargaLama > 0) {
            $persentasePerubahan = (($hargaBaru - $hargaLama) / $hargaLama) * 100;
        }
        
        $data = [
            'master_harga_id' => $masterHargaId,
            'jenis_sampah' => $jenisSampah,
            'harga_lama' => $hargaLama,
            'harga_baru' => $hargaBaru,
            'perubahan_harga' => $perubahanHarga,
            'persentase_perubahan' => $persentasePerubahan,
            'alasan_perubahan' => $alasan,
            'status_perubahan' => 'approved',
            'tanggal_berlaku' => date('Y-m-d'),
            'created_by' => $createdBy,
            'approved_by' => $createdBy
        ];

        return $this->insert($data) !== false;
    }

    /**
     * Get recent price changes
     */
    public function getRecentChanges(int $limit = 10): array
    {
        return $this->select('log_perubahan_harga.*, users.nama_lengkap as admin_nama, users.username')
            ->join('users', 'users.id = log_perubahan_harga.created_by', 'left')
            ->orderBy('log_perubahan_harga.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get price change history for specific waste type
     */
    public function getHistoryByHarga(int $masterHargaId): array
    {
        return $this->select('log_perubahan_harga.*, users.nama_lengkap as admin_nama')
            ->join('users', 'users.id = log_perubahan_harga.created_by', 'left')
            ->where('log_perubahan_harga.master_harga_id', $masterHargaId)
            ->orderBy('log_perubahan_harga.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get statistics
     */
    public function getStatistics(): array
    {
        $today = date('Y-m-d');
        $thisMonth = date('Y-m');

        return [
            'total_changes' => $this->countAllResults(),
            'changes_today' => $this->where('DATE(created_at)', $today)->countAllResults(),
            'changes_this_month' => $this->where('DATE_FORMAT(created_at, "%Y-%m")', $thisMonth)->countAllResults(),
            'most_active_admin' => $this->getMostActiveAdmin()
        ];
    }

    /**
     * Get most active admin
     */
    private function getMostActiveAdmin(): ?array
    {
        $result = $this->select('created_by, users.nama_lengkap, COUNT(*) as total_changes')
            ->join('users', 'users.id = log_perubahan_harga.created_by', 'left')
            ->groupBy('created_by')
            ->orderBy('total_changes', 'DESC')
            ->limit(1)
            ->first();

        return $result;
    }

    /**
     * Get changes by date range
     */
    public function getChangesByDateRange(string $startDate, string $endDate): array
    {
        return $this->select('log_perubahan_harga.*, users.nama_lengkap as admin_nama')
            ->join('users', 'users.id = log_perubahan_harga.created_by', 'left')
            ->where('DATE(log_perubahan_harga.created_at) >=', $startDate)
            ->where('DATE(log_perubahan_harga.created_at) <=', $endDate)
            ->orderBy('log_perubahan_harga.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Clean old logs (keep only last 6 months)
     */
    public function cleanOldLogs(): int
    {
        $sixMonthsAgo = date('Y-m-d', strtotime('-6 months'));
        
        $deletedCount = $this->where('DATE(created_at) <', $sixMonthsAgo)->countAllResults();
        $this->where('DATE(created_at) <', $sixMonthsAgo)->delete();
        
        return $deletedCount;
    }
}