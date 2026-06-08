<?php

namespace App\Models;

use CodeIgniter\Model;

class LimbahCairModel extends Model
{
    protected $table            = 'limbah_cair';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    // SEMUA KOLOM DARI DATABASE - TIDAK BOLEH ADA YANG KURANG!
    protected $allowedFields = [
        'id_user',
        'tanggal_input',
        'lokasi',
        'nama_limbah',
        'kode_limbah',
        'tingkat_bahaya',
        'karakteristik',
        'pengolahan',
        'timbulan',
        'satuan',
        'bentuk_fisik',
        'kemasan',
        'ph',
        'bod',
        'cod',
        'tss',
        'keterangan',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // MATIKAN VALIDASI
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}
