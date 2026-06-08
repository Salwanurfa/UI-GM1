<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterLimbahCairModel extends Model
{
    protected $table            = 'master_limbah_cair';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    protected $allowedFields = [
        'nama_limbah',
        'kode_limbah',
        'tingkat_bahaya',
        'karakteristik',
        'pengolahan',
        'created_at',
        'updated_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'nama_limbah' => 'required|max_length[255]',
        'kode_limbah' => 'required|max_length[50]',
        'tingkat_bahaya' => 'required|max_length[100]',
        'karakteristik' => 'required',
        'pengolahan' => 'required'
    ];

    protected $validationMessages = [
        'nama_limbah' => [
            'required' => 'Nama limbah harus diisi',
            'max_length' => 'Nama limbah maksimal 255 karakter'
        ],
        'kode_limbah' => [
            'required' => 'Kode limbah harus diisi',
            'max_length' => 'Kode limbah maksimal 50 karakter'
        ],
        'tingkat_bahaya' => [
            'required' => 'Tingkat bahaya harus diisi',
            'max_length' => 'Tingkat bahaya maksimal 100 karakter'
        ],
        'karakteristik' => [
            'required' => 'Karakteristik harus diisi'
        ],
        'pengolahan' => [
            'required' => 'Pengolahan harus diisi'
        ]
    ];

    protected $skipValidation = false;
}
