<?php

namespace App\Models;

use CodeIgniter\Model;

class WasteRejectedModel extends Model
{
    protected $table            = 'waste_rejected';
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
        'alasan_reject',
        'tanggal_reject'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'waste_id' => 'int',
        'unit_id' => 'int',
        'user_id' => 'int',
        'admin_id' => 'int',
        'jumlah' => 'float',
        'berat_kg' => 'float'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get rejected waste by date range
     */
    public function getByDateRange($startDate, $endDate, $filters = [])
    {
        $builder = $this->select('waste_rejected.*, unit.nama_unit, users.nama_lengkap as user_name')
                        ->join('unit', 'unit.id = waste_rejected.unit_id', 'left')
                        ->join('users', 'users.id = waste_rejected.user_id', 'left')
                        ->where('waste_rejected.tanggal >=', $startDate)
                        ->where('waste_rejected.tanggal <=', $endDate);
        
        // Apply filters
        if (!empty($filters['unit_id'])) {
            $builder->where('waste_rejected.unit_id', $filters['unit_id']);
        }
        
        if (!empty($filters['jenis_sampah'])) {
            $builder->where('waste_rejected.jenis_sampah', $filters['jenis_sampah']);
        }
        
        return $builder->orderBy('waste_rejected.tanggal_reject', 'DESC')->findAll();
    }

    /**
     * Get statistics
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
        
        return [
            'total_rejected' => $builder->countAllResults()
        ];
    }
}
