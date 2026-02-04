<?php

namespace App\Models;

use CodeIgniter\Model;

class WasteApprovedModel extends Model
{
    protected $table            = 'waste_approved';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'waste_id',
        'unit_id',
        'tps_id',
        'kategori_id',
        'user_id',
        'admin_id',
        'tanggal',
        'jenis_sampah',
        'nama_jenis',
        'satuan',
        'jumlah',
        'berat_kg',
        'harga_per_kg',
        'nilai_ekonomis',
        'gedung',
        'kategori_sampah',
        'dapat_dijual',
        'catatan_admin',
        'tanggal_approve'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'waste_id' => 'int',
        'unit_id' => 'int',
        'user_id' => 'int',
        'admin_id' => 'int',
        'jumlah' => 'float',
        'berat_kg' => 'float',
        'harga_per_kg' => 'float',
        'nilai_ekonomis' => 'float',
        'dapat_dijual' => 'boolean'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get approved waste by date range
     */
    public function getByDateRange($startDate, $endDate, $filters = [])
    {
        $builder = $this->select('waste_approved.*, unit.nama_unit, users.nama_lengkap as user_name')
                        ->join('unit', 'unit.id = waste_approved.unit_id', 'left')
                        ->join('users', 'users.id = waste_approved.user_id', 'left')
                        ->where('waste_approved.tanggal >=', $startDate)
                        ->where('waste_approved.tanggal <=', $endDate);
        
        // Apply filters
        if (!empty($filters['unit_id'])) {
            $builder->where('waste_approved.unit_id', $filters['unit_id']);
        }
        
        if (!empty($filters['jenis_sampah'])) {
            $builder->where('waste_approved.jenis_sampah', $filters['jenis_sampah']);
        }
        
        return $builder->orderBy('waste_approved.tanggal', 'DESC')->findAll();
    }

    /**
     * Get statistics by date range
     */
    public function getStatistics($startDate, $endDate, $filters = [])
    {
        $builder = $this->where('tanggal >=', $startDate)
                        ->where('tanggal <=', $endDate);
        
        if (!empty($filters['unit_id'])) {
            $builder->where('unit_id', $filters['unit_id']);
        }
        
        if (!empty($filters['jenis_sampah'])) {
            $builder->where('jenis_sampah', $filters['jenis_sampah']);
        }
        
        $data = $builder->findAll();
        
        $totalBerat = 0;
        $totalNilai = 0;
        $totalData = count($data);
        $units = [];
        
        foreach ($data as $item) {
            $totalBerat += $item['berat_kg'];
            $totalNilai += $item['nilai_ekonomis'] ?? 0;
            if ($item['unit_id']) {
                $units[$item['unit_id']] = true;
            }
        }
        
        return [
            'total_data' => $totalData,
            'total_berat' => $totalBerat,
            'total_nilai' => $totalNilai,
            'total_unit' => count($units)
        ];
    }

    /**
     * Get summary by jenis sampah
     */
    public function getSummaryByJenis($startDate, $endDate, $filters = [])
    {
        $builder = $this->select('jenis_sampah, nama_jenis, 
                                  COUNT(*) as total_data,
                                  SUM(berat_kg) as total_berat,
                                  SUM(nilai_ekonomis) as total_nilai,
                                  COUNT(DISTINCT unit_id) as total_unit')
                        ->where('tanggal >=', $startDate)
                        ->where('tanggal <=', $endDate);
        
        if (!empty($filters['unit_id'])) {
            $builder->where('unit_id', $filters['unit_id']);
        }
        
        return $builder->groupBy('jenis_sampah')
                       ->orderBy('total_berat', 'DESC')
                       ->findAll();
    }

    /**
     * Get summary by unit
     */
    public function getSummaryByUnit($startDate, $endDate, $filters = [])
    {
        $builder = $this->select('waste_approved.unit_id, unit.nama_unit,
                                  COUNT(*) as total_data,
                                  SUM(waste_approved.berat_kg) as total_berat,
                                  SUM(waste_approved.nilai_ekonomis) as total_nilai')
                        ->join('unit', 'unit.id = waste_approved.unit_id', 'left')
                        ->where('waste_approved.tanggal >=', $startDate)
                        ->where('waste_approved.tanggal <=', $endDate);
        
        if (!empty($filters['jenis_sampah'])) {
            $builder->where('waste_approved.jenis_sampah', $filters['jenis_sampah']);
        }
        
        return $builder->groupBy('waste_approved.unit_id')
                       ->orderBy('total_berat', 'DESC')
                       ->findAll();
    }

    /**
     * Get monthly trend
     */
    public function getMonthlyTrend($year, $filters = [])
    {
        $builder = $this->select('MONTH(tanggal) as bulan, 
                                  COUNT(*) as total_data,
                                  SUM(berat_kg) as total_berat')
                        ->where('YEAR(tanggal)', $year);
        
        if (!empty($filters['unit_id'])) {
            $builder->where('unit_id', $filters['unit_id']);
        }
        
        if (!empty($filters['jenis_sampah'])) {
            $builder->where('jenis_sampah', $filters['jenis_sampah']);
        }
        
        return $builder->groupBy('MONTH(tanggal)')
                       ->orderBy('bulan', 'ASC')
                       ->findAll();
    }

    /**
     * Get all jenis sampah
     */
    public function getAllJenisSampah()
    {
        return $this->select('jenis_sampah, nama_jenis')
                    ->groupBy('jenis_sampah')
                    ->orderBy('jenis_sampah', 'ASC')
                    ->findAll();
    }
}
