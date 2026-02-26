<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterLimbahB3Model extends Model
{
    protected $table            = 'master_limbah_b3';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_limbah',
        'kode_limbah',
        'kategori_bahaya',
        'karakteristik',
        'status_aktif',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'status_aktif' => 'int',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'nama_limbah' => 'required|max_length[255]',
    ];

    /**
     * Cari master Limbah B3 berdasarkan nama (case-insensitive, partial match).
     */
    public function searchByName(string $keyword, int $limit = 10): array
    {
        return $this->where('status_aktif', 1)
            ->like('nama_limbah', $keyword, 'both', null, true)
            ->orderBy('nama_limbah', 'ASC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Ambil satu baris master berdasarkan nama persis (case-insensitive).
     */
    public function findByExactName(string $name): ?array
    {
        return $this->where('status_aktif', 1)
            ->where('LOWER(nama_limbah)', mb_strtolower($name))
            ->first();
    }
}

