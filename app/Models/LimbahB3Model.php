<?php

namespace App\Models;

use CodeIgniter\Model;

class LimbahB3Model extends Model
{
    protected $table            = 'limbah_b3';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_user',
        'master_b3_id',
        'lokasi',
        'timbulan',
        'satuan',
        'bentuk_fisik',
        'kemasan',
        'status',
        'keterangan',
        'tanggal_input',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'id'          => 'int',
        'id_user'     => 'int',
        'master_b3_id' => 'int',
        'timbulan'    => 'float',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = null;
    protected $updatedField  = null;

    protected $validationRules = [
        'id_user'      => 'required|integer',
        'master_b3_id' => 'required|integer',
        'timbulan'     => 'required|numeric|greater_than[0]',
        'satuan'       => 'required|max_length[20]',
        'status'       => 'required|in_list[draft,dikirim_ke_tps,disetujui_tps,ditolak_tps,disetujui_admin]',
        'tanggal_input' => 'required|valid_date',
    ];

    protected $validationMessages = [
        'timbulan' => [
            'required'     => 'Timbulan/berat wajib diisi',
            'numeric'      => 'Timbulan/berat harus berupa angka',
            'greater_than' => 'Timbulan/berat harus lebih besar dari 0',
        ],
        'status' => [
            'in_list' => 'Status tidak valid',
        ],
    ];

    /**
     * Ambil semua data Limbah B3 dengan info Master Limbah (JOIN).
     */
    public function getAllWithMaster(): array
    {
        return $this->select('limbah_b3.*, master_limbah_b3.nama_limbah, master_limbah_b3.kode_limbah, master_limbah_b3.kategori_bahaya')
            ->join('master_limbah_b3', 'master_limbah_b3.id = limbah_b3.master_b3_id', 'left')
            ->orderBy('limbah_b3.tanggal_input', 'DESC')
            ->findAll();
    }

    /**
     * Ambil data Limbah B3 milik user tertentu dengan info Master.
     */
    public function getUserLimbah(int $userId): array
    {
        return $this->select('limbah_b3.*, master_limbah_b3.nama_limbah, master_limbah_b3.kode_limbah, master_limbah_b3.kategori_bahaya')
            ->join('master_limbah_b3', 'master_limbah_b3.id = limbah_b3.master_b3_id', 'left')
            ->where('limbah_b3.id_user', $userId)
            ->orderBy('limbah_b3.tanggal_input', 'DESC')
            ->findAll();
    }

    /**
     * Ambil data Limbah B3 berdasarkan ID dengan info Master.
     */
    public function getDetailWithMaster(int $id): ?array
    {
        return $this->select('limbah_b3.*, master_limbah_b3.nama_limbah, master_limbah_b3.kode_limbah, master_limbah_b3.kategori_bahaya, master_limbah_b3.karakteristik')
            ->join('master_limbah_b3', 'master_limbah_b3.id = limbah_b3.master_b3_id', 'left')
            ->where('limbah_b3.id', $id)
            ->first();
    }

    /**
     * Hitung total limbah B3 berdasarkan status.
     */
    public function getCountByStatus(int $userId = null): array
    {
        $builder = $this->select('status, COUNT(*) as count')
            ->groupBy('status');

        if ($userId) {
            $builder->where('id_user', $userId);
        }

        $results = $builder->findAll();
        
        $stats = [
            'draf'             => 0,
            'dikirim_ke_tps'   => 0,
            'disetujui_tps'    => 0,
            'ditolak_tps'      => 0,
            'disetujui_admin'  => 0,
            'total'            => 0,
        ];

        foreach ($results as $row) {
            if (isset($stats[$row['status']])) {
                $stats[$row['status']] = (int) $row['count'];
            }
        }

        $stats['total'] = array_sum(array_slice($stats, 0, -1));
        return $stats;
    }

    /**
     * Hitung total timbulan limbah B3 berdasarkan status.
     */
    public function getTotalTimbulanByStatus(int $userId = null): array
    {
        $builder = $this->select('status, SUM(timbulan) as total_timbulan')
            ->groupBy('status');

        if ($userId) {
            $builder->where('id_user', $userId);
        }

        $results = $builder->findAll();
        
        $stats = [
            'draf'             => 0,
            'dikirim_ke_tps'   => 0,
            'disetujui_tps'    => 0,
            'ditolak_tps'      => 0,
            'disetujui_admin'  => 0,
        ];

        foreach ($results as $row) {
            if (isset($stats[$row['status']])) {
                $stats[$row['status']] = (float) round($row['total_timbulan'] ?? 0, 3);
            }
        }

        return $stats;
    }
}