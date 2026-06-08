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
        'tanggal_pencatatan',
        'tanggal_mulai',
        'tanggal_selesai',
        'bulan',
        'tahun',
        'kategori_kendaraan',
        'kategori_sederhana',
        'status_kendaraan',
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
        'kategori_kendaraan' => 'required',
        'jenis_bahan_bakar' => 'required',
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
            'required' => 'Kategori kendaraan harus dipilih'
        ],
        'jenis_bahan_bakar' => [
            'required' => 'Jenis bahan bakar harus dipilih'
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
     * FIXED: Added LEFT JOIN with transport_categories for data integrity
     */
    public function getStatsByUser(int $userId, int $limit = 10)
    {
        return $this->select('transport_stats.*, users.nama_lengkap as petugas_nama')
            ->join('users', 'users.id = transport_stats.input_by', 'left')
            ->where('transport_stats.input_by', $userId)
            ->where('transport_stats.kategori_kendaraan IS NOT NULL')  // Exclude NULL categories
            ->orderBy('transport_stats.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get stats summary for dashboard
     * Updated to show TOTAL ACCUMULATION from all time
     * FIXED: Use proper date comparison for today's data
     */
    public function getStatsSummary(int $userId)
    {
        $db = \Config\Database::connect();
        $today = date('Y-m-d');
        $weekStart = date('Y-m-d', strtotime('monday this week'));
        $monthStart = date('Y-m-01');

        return [
            // Today's entries count - FIXED: Use DATE() function properly
            'today_entries' => $db->table($this->table)
                ->where('input_by', $userId)
                ->where('DATE(created_at)', $today)
                ->countAllResults(),
            
            // This week's entries count
            'week_entries' => $db->table($this->table)
                ->where('input_by', $userId)
                ->where('DATE(created_at) >=', $weekStart)
                ->countAllResults(),
            
            // This month's entries count
            'month_entries' => $db->table($this->table)
                ->where('input_by', $userId)
                ->where('DATE(created_at) >=', $monthStart)
                ->countAllResults(),
            
            // TOTAL ACCUMULATION - All time total vehicles (NO DATE FILTER)
            'total_vehicles_all_time' => $db->table($this->table)
                ->selectSum('jumlah_total')
                ->where('input_by', $userId)
                ->get()
                ->getRowArray()['jumlah_total'] ?? 0,
            
            // This month's total vehicles
            'total_vehicles_month' => $db->table($this->table)
                ->selectSum('jumlah_total')
                ->where('input_by', $userId)
                ->where('DATE(created_at) >=', $monthStart)
                ->get()
                ->getRowArray()['jumlah_total'] ?? 0,
            
            // Today's total vehicles (for comparison) - FIXED: Use DATE() function properly
            'total_vehicles_today' => $db->table($this->table)
                ->selectSum('jumlah_total')
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
     * Get available periods (previously entered data)
     * Returns array of arrays for display in history table
     */
    public function getAvailablePeriods(int $userId = null)
    {
        $builder = $this->select('transport_stats.*')
            ->orderBy('transport_stats.created_at', 'DESC')
            ->limit(20);
        
        // Filter by user if provided
        if ($userId) {
            $builder->where('transport_stats.input_by', $userId);
        }
        
        // Return as array of arrays (not objects)
        return $builder->findAll();
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

    /**
     * Get monthly summary grouped by category
     * Returns aggregated data for a specific month/year
     */
    public function getMonthlySummary(int $userId, int $month, int $year)
    {
        $db = \Config\Database::connect();
        
        // Build query to get data for the specified month/year
        // We need to check multiple date fields based on periode type
        $builder = $db->table($this->table);
        $builder->select('kategori_kendaraan, jenis_bahan_bakar, SUM(jumlah_total) as total_unit, SUM(is_zev * jumlah_total) as total_zev, SUM(is_shuttle * jumlah_total) as total_shuttle');
        $builder->where('input_by', $userId);
        
        // Complex WHERE clause to handle different periode types
        $builder->groupStart();
            // Harian: check tanggal_pencatatan
            $builder->groupStart();
                $builder->where('periode', 'Harian');
                $builder->where('MONTH(tanggal_pencatatan)', $month);
                $builder->where('YEAR(tanggal_pencatatan)', $year);
            $builder->groupEnd();
            
            // Mingguan: check tanggal_mulai or tanggal_selesai
            $builder->orGroupStart();
                $builder->where('periode', 'Mingguan (Back-up)');
                $builder->groupStart();
                    $builder->where('MONTH(tanggal_mulai)', $month);
                    $builder->where('YEAR(tanggal_mulai)', $year);
                $builder->groupEnd();
                $builder->orGroupStart();
                    $builder->where('MONTH(tanggal_selesai)', $month);
                    $builder->where('YEAR(tanggal_selesai)', $year);
                $builder->groupEnd();
            $builder->groupEnd();
            
            // Bulanan: check bulan and tahun columns
            $builder->orGroupStart();
                $builder->where('periode', 'Bulanan (Back-up)');
                
                // Convert month number to Indonesian month name
                $monthNames = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ];
                $builder->where('bulan', $monthNames[$month]);
                $builder->where('tahun', $year);
            $builder->groupEnd();
        $builder->groupEnd();
        
        $builder->groupBy('kategori_kendaraan, jenis_bahan_bakar');
        $builder->orderBy('total_unit', 'DESC');
        
        $results = $builder->get()->getResultArray();
        
        // Calculate totals and percentages
        $totalAllVehicles = array_sum(array_column($results, 'total_unit'));
        $totalZevVehicles = array_sum(array_column($results, 'total_zev'));
        
        // Add percentage to each row
        foreach ($results as &$row) {
            $row['percentage'] = $totalAllVehicles > 0 ? round(($row['total_unit'] / $totalAllVehicles) * 100, 2) : 0;
            $row['zev_percentage'] = $row['total_unit'] > 0 ? round(($row['total_zev'] / $row['total_unit']) * 100, 2) : 0;
        }
        
        return [
            'data' => $results,
            'total_vehicles' => $totalAllVehicles,
            'total_zev' => $totalZevVehicles,
            'zev_percentage' => $totalAllVehicles > 0 ? round(($totalZevVehicles / $totalAllVehicles) * 100, 2) : 0
        ];
    }

    /**
     * Get category summary for current month (for summary cards)
     */
    public function getCategorySummary(int $userId, int $month, int $year)
    {
        $db = \Config\Database::connect();
        
        $builder = $db->table($this->table);
        $builder->select('kategori_kendaraan, SUM(jumlah_total) as total');
        $builder->where('input_by', $userId);
        
        // Same complex WHERE clause as getMonthlySummary
        $builder->groupStart();
            $builder->groupStart();
                $builder->where('periode', 'Harian');
                $builder->where('MONTH(tanggal_pencatatan)', $month);
                $builder->where('YEAR(tanggal_pencatatan)', $year);
            $builder->groupEnd();
            
            $builder->orGroupStart();
                $builder->where('periode', 'Mingguan (Back-up)');
                $builder->groupStart();
                    $builder->where('MONTH(tanggal_mulai)', $month);
                    $builder->where('YEAR(tanggal_mulai)', $year);
                $builder->groupEnd();
                $builder->orGroupStart();
                    $builder->where('MONTH(tanggal_selesai)', $month);
                    $builder->where('YEAR(tanggal_selesai)', $year);
                $builder->groupEnd();
            $builder->groupEnd();
            
            $builder->orGroupStart();
                $builder->where('periode', 'Bulanan (Back-up)');
                $monthNames = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ];
                $builder->where('bulan', $monthNames[$month]);
                $builder->where('tahun', $year);
            $builder->groupEnd();
        $builder->groupEnd();
        
        $builder->groupBy('kategori_kendaraan');
        
        return $builder->get()->getResultArray();
    }
}