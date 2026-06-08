<?php

namespace App\Models;

use CodeIgniter\Model;

class TransportCategoryModel extends Model
{
    protected $table            = 'transport_categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_kategori',
        'kode_kategori',
        'deskripsi',
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
        'nama_kategori' => 'required|max_length[100]|is_unique[transport_categories.nama_kategori,id,{id}]',
        'is_zev' => 'in_list[0,1]',
        'status_aktif' => 'in_list[0,1]'
    ];

    protected $validationMessages = [
        'nama_kategori' => [
            'required' => 'Nama kategori harus diisi',
            'max_length' => 'Nama kategori terlalu panjang',
            'is_unique' => 'Nama kategori sudah ada'
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
     * Get active categories
     */
    public function getActive()
    {
        return $this->where('status_aktif', 1)
            ->orderBy('nama_kategori', 'ASC')
            ->findAll();
    }
}
