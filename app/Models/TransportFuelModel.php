<?php

namespace App\Models;

use CodeIgniter\Model;

class TransportFuelModel extends Model
{
    protected $table            = 'transport_fuels';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_bahan_bakar',
        'kode_bahan_bakar',
        'is_zev',
        'status_aktif'
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
        'nama_bahan_bakar' => 'required|max_length[100]|is_unique[transport_fuels.nama_bahan_bakar,id,{id}]',
        'is_zev' => 'in_list[0,1]',
        'status_aktif' => 'in_list[0,1]'
    ];

    protected $validationMessages = [
        'nama_bahan_bakar' => [
            'required' => 'Nama bahan bakar harus diisi',
            'max_length' => 'Nama bahan bakar terlalu panjang',
            'is_unique' => 'Nama bahan bakar sudah ada'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get active fuels
     */
    public function getActive()
    {
        return $this->where('status_aktif', 1)
            ->orderBy('nama_bahan_bakar', 'ASC')
            ->findAll();
    }
}
