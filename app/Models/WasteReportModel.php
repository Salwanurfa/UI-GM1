<?php

namespace App\Models;

use CodeIgniter\Model;

class WasteReportModel extends Model
{
    protected $table            = 'waste_management';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'unit_id', 'user_id', 'user_name', 'tanggal', 'jenis_sampah', 'nama_sampah', 
        'nama_sampah_id', 'satuan', 'jumlah', 'gedung', 'gedung_name', 'nilai_rupiah', 
        'status', 'catatan_admin', 'confirmed_at', 'created_at', 'updated_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'unit_id' => 'int',
        'user_id' => 'int',
        'jumlah' => '?float',
        'nilai_rupiah' => '?float'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get rekap sampah by filter with pagination
     * 
     * @param array $filters ['from', 'to', 'nama_sampah', 'search']
     * @param int $limit
     * @param int $offset
     * @return array ['rows' => [...], 'total' => int, 'aggregates' => [...]]
     */
    public function getRekapSampahByFilter(array $filters, int $limit = 5, int $offset = 0): array
    {
        $builder = $this->select('
            waste_management.id,
            waste_management.created_at,
            waste_management.confirmed_at,
            waste_management.user_name,
            waste_management.gedung_name,
            waste_management.nama_sampah,
            waste_management.jenis_sampah,
            waste_management.jumlah,
            waste_management.satuan,
            waste_management.nilai_rupiah,
            waste_management.status,
            waste_management.tanggal,
            users.username,
            users.nama_lengkap,
            unit.nama_unit
        ')
        ->join('users', 'users.id = waste_management.user_id', 'left')
        ->join('unit', 'unit.id = waste_management.unit_id', 'left')
        ->where('waste_management.status', 'disetujui'); // Only confirmed reports

        // Apply filters
        if (!empty($filters['from'])) {
            $builder->where('waste_management.created_at >=', $filters['from'] . ' 00:00:00');
        }

        if (!empty($filters['to'])) {
            $builder->where('waste_management.created_at <=', $filters['to'] . ' 23:59:59');
        }

        if (!empty($filters['nama_sampah'])) {
            $builder->like('waste_management.nama_sampah', $filters['nama_sampah']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('waste_management.nama_sampah', $filters['search'])
                ->orLike('waste_management.user_name', $filters['search'])
                ->orLike('waste_management.gedung_name', $filters['search'])
                ->orLike('users.nama_lengkap', $filters['search'])
                ->orLike('unit.nama_unit', $filters['search'])
                ->groupEnd();
        }

        // Get total count before pagination
        $total = $builder->countAllResults(false);

        // Get rows with pagination
        $rows = $builder->orderBy('waste_management.created_at', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();

        // Calculate aggregates for all filtered data (not just current page)
        $aggregates = $this->calculateAggregates($filters);

        return [
            'rows' => $rows,
            'total' => $total,
            'aggregates' => $aggregates
        ];
    }

    /**
     * Count rekap sampah by filter
     * 
     * @param array $filters
     * @return int
     */
    public function countRekapSampahByFilter(array $filters): int
    {
        $builder = $this->where('status', 'disetujui');

        if (!empty($filters['from'])) {
            $builder->where('created_at >=', $filters['from'] . ' 00:00:00');
        }

        if (!empty($filters['to'])) {
            $builder->where('created_at <=', $filters['to'] . ' 23:59:59');
        }

        if (!empty($filters['nama_sampah'])) {
            $builder->like('nama_sampah', $filters['nama_sampah']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('nama_sampah', $filters['search'])
                ->orLike('user_name', $filters['search'])
                ->orLike('gedung_name', $filters['search'])
                ->groupEnd();
        }

        return $builder->countAllResults();
    }

    /**
     * Get rekap per unit by filter with pagination
     * 
     * @param array $filters ['from', 'to', 'unit_id', 'search']
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getRekapUnitByFilter(array $filters, int $limit = 5, int $offset = 0): array
    {
        $builder = $this->select('
            unit.id as unit_id,
            unit.nama_unit,
            COUNT(waste_management.id) as total_laporan,
            SUM(waste_management.jumlah) as total_jumlah,
            SUM(waste_management.nilai_rupiah) as total_nilai,
            MIN(waste_management.created_at) as first_report,
            MAX(waste_management.created_at) as last_report
        ')
        ->join('unit', 'unit.id = waste_management.unit_id', 'left')
        ->where('waste_management.status', 'disetujui')
        ->groupBy('unit.id, unit.nama_unit');

        // Apply filters
        if (!empty($filters['from'])) {
            $builder->where('waste_management.created_at >=', $filters['from'] . ' 00:00:00');
        }

        if (!empty($filters['to'])) {
            $builder->where('waste_management.created_at <=', $filters['to'] . ' 23:59:59');
        }

        if (!empty($filters['unit_id'])) {
            $builder->where('unit.id', $filters['unit_id']);
        }

        if (!empty($filters['search'])) {
            $builder->like('unit.nama_unit', $filters['search']);
        }

        // Get total count
        $total = $builder->countAllResults(false);

        // Get rows with pagination
        $rows = $builder->orderBy('total_laporan', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();

        // Calculate aggregates
        $aggregates = $this->calculateUnitAggregates($filters);

        return [
            'rows' => $rows,
            'total' => $total,
            'aggregates' => $aggregates
        ];
    }

    /**
     * Count rekap unit by filter
     * 
     * @param array $filters
     * @return int
     */
    public function countRekapUnitByFilter(array $filters): int
    {
        $builder = $this->select('unit.id')
            ->join('unit', 'unit.id = waste_management.unit_id', 'left')
            ->where('waste_management.status', 'disetujui')
            ->groupBy('unit.id');

        if (!empty($filters['from'])) {
            $builder->where('waste_management.created_at >=', $filters['from'] . ' 00:00:00');
        }

        if (!empty($filters['to'])) {
            $builder->where('waste_management.created_at <=', $filters['to'] . ' 23:59:59');
        }

        if (!empty($filters['unit_id'])) {
            $builder->where('unit.id', $filters['unit_id']);
        }

        if (!empty($filters['search'])) {
            $builder->like('unit.nama_unit', $filters['search']);
        }

        return $builder->countAllResults();
    }

    /**
     * Calculate aggregates for rekap sampah
     * 
     * @param array $filters
     * @return array
     */
    private function calculateAggregates(array $filters): array
    {
        $builder = $this->select('
            SUM(jumlah) as sum_jumlah,
            SUM(nilai_rupiah) as sum_total_harga,
            COUNT(id) as total_records
        ')
        ->where('status', 'disetujui');

        if (!empty($filters['from'])) {
            $builder->where('created_at >=', $filters['from'] . ' 00:00:00');
        }

        if (!empty($filters['to'])) {
            $builder->where('created_at <=', $filters['to'] . ' 23:59:59');
        }

        if (!empty($filters['nama_sampah'])) {
            $builder->like('nama_sampah', $filters['nama_sampah']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('nama_sampah', $filters['search'])
                ->orLike('user_name', $filters['search'])
                ->orLike('gedung_name', $filters['search'])
                ->groupEnd();
        }

        $result = $builder->get()->getRowArray();

        return [
            'sum_jumlah' => (float)($result['sum_jumlah'] ?? 0),
            'sum_total_harga' => (float)($result['sum_total_harga'] ?? 0),
            'total_records' => (int)($result['total_records'] ?? 0)
        ];
    }

    /**
     * Calculate aggregates for rekap unit
     * 
     * @param array $filters
     * @return array
     */
    private function calculateUnitAggregates(array $filters): array
    {
        $builder = $this->select('
            COUNT(DISTINCT unit_id) as total_units,
            SUM(jumlah) as sum_jumlah,
            SUM(nilai_rupiah) as sum_total_harga,
            COUNT(id) as total_records
        ')
        ->where('status', 'disetujui');

        if (!empty($filters['from'])) {
            $builder->where('created_at >=', $filters['from'] . ' 00:00:00');
        }

        if (!empty($filters['to'])) {
            $builder->where('created_at <=', $filters['to'] . ' 23:59:59');
        }

        if (!empty($filters['unit_id'])) {
            $builder->where('unit_id', $filters['unit_id']);
        }

        $result = $builder->get()->getRowArray();

        return [
            'total_units' => (int)($result['total_units'] ?? 0),
            'sum_jumlah' => (float)($result['sum_jumlah'] ?? 0),
            'sum_total_harga' => (float)($result['sum_total_harga'] ?? 0),
            'total_records' => (int)($result['total_records'] ?? 0)
        ];
    }

    /**
     * Get all distinct nama_sampah for dropdown
     * 
     * @return array
     */
    public function getAllNamaSampah(): array
    {
        return $this->select('DISTINCT nama_sampah')
            ->where('nama_sampah IS NOT NULL')
            ->where('nama_sampah !=', '')
            ->orderBy('nama_sampah', 'ASC')
            ->findAll();
    }

    /**
     * Get report detail by ID
     * 
     * @param int $id
     * @return array|null
     */
    public function getReportDetail(int $id): ?array
    {
        return $this->select('
            waste_management.*,
            users.username,
            users.nama_lengkap,
            users.email,
            unit.nama_unit
        ')
        ->join('users', 'users.id = waste_management.user_id', 'left')
        ->join('unit', 'unit.id = waste_management.unit_id', 'left')
        ->where('waste_management.id', $id)
        ->first();
    }
}
