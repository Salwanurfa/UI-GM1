<?php

namespace App\Models;

use CodeIgniter\Model;

class TransportStatsModel extends Model
{
    protected $table            = 'transport_stats';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'periode',
        'kategori_kendaraan',
        'jenis_bahan_bakar',
        'jumlah_total',
        'input_by',
        'is_zev',
        'is_shuttle'
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
    protected $validationRules = [
        'periode' => 'required|max_length[100]',
        'kategori_kendaraan' => 'required|in_list[Roda Dua,Roda Empat,Sepeda,Kendaraan Umum]',
        'jenis_bahan_bakar' => 'required|in_list[Bensin,Diesel,Listrik,Non-BBM]',
        'jumlah_total' => 'required|integer|greater_than[0]',
        'input_by' => 'required|integer',
        'is_zev' => 'in_list[0,1]',
        'is_shuttle' => 'in_list[0,1]'
    ];

    protected $validationMessages = [
        'periode' => [
            'required' => 'Periode harus diisi',
            'max_length' => 'Periode terlalu panjang'
        ],
        'kategori_kendaraan' => [
            'required' => 'Kategori kendaraan harus dipilih',
            'in_list' => 'Kategori kendaraan tidak valid'
        ],
        'jenis_bahan_bakar' => [
            'required' => 'Jenis bahan bakar harus dipilih',
            'in_list' => 'Jenis bahan bakar tidak valid'
        ],
        'jumlah_total' => [
            'required' => 'Jumlah total harus diisi',
            'integer' => 'Jumlah total harus berupa angka',
            'greater_than' => 'Jumlah total harus lebih dari 0'
        ],
        'input_by' => [
            'required' => 'Input by harus diisi',
            'integer' => 'Input by harus berupa angka'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setTimezone'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['setTimezone'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Set timezone before insert/update
     */
    protected function setTimezone(array $data)
    {
        // Set timezone to Asia/Jakarta for accurate timestamp
        date_default_timezone_set('Asia/Jakarta');
        return $data;
    }

    /**
     * Get transport stats by user
     */
    public function getStatsByUser(int $userId, int $limit = 10)
    {
        return $this->select('transport_stats.*, users.nama_lengkap as petugas_nama')
            ->join('users', 'users.id = transport_stats.input_by', 'left')
            ->where('transport_stats.input_by', $userId)
            ->orderBy('transport_stats.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get stats summary for dashboard
     */
    public function getStatsSummary(int $userId)
    {
        $today = date('Y-m-d');
        $weekStart = date('Y-m-d', strtotime('monday this week'));
        $monthStart = date('Y-m-01');

        return [
            'today_entries' => $this->where('input_by', $userId)
                ->where('DATE(created_at)', $today)
                ->countAllResults(),
            'week_entries' => $this->where('input_by', $userId)
                ->where('created_at >=', $weekStart)
                ->countAllResults(),
            'month_entries' => $this->where('input_by', $userId)
                ->where('created_at >=', $monthStart)
                ->countAllResults(),
            'total_vehicles_today' => $this->selectSum('jumlah_total')
                ->where('input_by', $userId)
                ->where('DATE(created_at)', $today)
                ->get()
                ->getRowArray()['jumlah_total'] ?? 0
        ];
    }

    /**
     * Get ZEV and Shuttle statistics
     */
    public function getZevShuttleStats()
    {
        $db = \Config\Database::connect();
        
        // ZEV Statistics
        $zevStats = $db->table('transport_stats')
            ->select('SUM(jumlah_total) as total_zev')
            ->where('is_zev', 1)
            ->get()
            ->getRowArray()['total_zev'] ?? 0;

        // Shuttle Statistics
        $shuttleStats = $db->table('transport_stats')
            ->select('SUM(jumlah_total) as total_shuttle')
            ->where('is_shuttle', 1)
            ->get()
            ->getRowArray()['total_shuttle'] ?? 0;

        // Total vehicles for percentage calculation
        $totalVehicles = $db->table('transport_stats')
            ->selectSum('jumlah_total')
            ->get()
            ->getRowArray()['jumlah_total'] ?? 0;

        return [
            'total_zev' => $zevStats,
            'total_shuttle' => $shuttleStats,
            'total_vehicles' => $totalVehicles,
            'zev_percentage' => $totalVehicles > 0 ? round(($zevStats / $totalVehicles) * 100, 2) : 0,
            'shuttle_percentage' => $totalVehicles > 0 ? round(($shuttleStats / $totalVehicles) * 100, 2) : 0
        ];
    }

    /**
     * Get ZEV breakdown by fuel type
     */
    public function getZevBreakdown()
    {
        return $this->select('jenis_bahan_bakar, SUM(jumlah_total) as total')
            ->where('is_zev', 1)
            ->groupBy('jenis_bahan_bakar')
            ->findAll();
    }

    /**
     * Get available periods for dropdown
     */
    public function getAvailablePeriods()
    {
        $currentYear = date('Y');
        $currentMonth = date('n');
        
        $periods = [];
        
        // Generate weekly periods for current month
        $monthName = date('F Y');
        for ($week = 1; $week <= 4; $week++) {
            $periods[] = "Minggu {$week} - {$monthName}";
        }
        
        // Generate monthly periods
        $months = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        for ($i = 0; $i < 12; $i++) {
            $monthIndex = (($currentMonth - 1 + $i) % 12);
            $year = $currentYear + floor(($currentMonth - 1 + $i) / 12);
            $periods[] = "Bulan {$months[$monthIndex]} {$year}";
        }
        
        return $periods;
    }

    /**
     * Check if entry exists for period and category
     */
    public function entryExists(string $periode, string $kategori, string $bahanBakar, int $userId)
    {
        return $this->where('periode', $periode)
            ->where('kategori_kendaraan', $kategori)
            ->where('jenis_bahan_bakar', $bahanBakar)
            ->where('input_by', $userId)
            ->first();
    }
}