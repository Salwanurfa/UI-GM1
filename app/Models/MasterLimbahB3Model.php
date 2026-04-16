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
        'bentuk_fisik',
        'kemasan',
        'default_satuan',
        'default_kemasan',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'nama_limbah'      => 'required|max_length[255]',
        'kode_limbah'      => 'required|max_length[50]',
        'kategori_bahaya'  => 'required',
        'karakteristik'    => 'max_length[1000]',
        'bentuk_fisik'     => 'max_length[100]',
        'kemasan'          => 'max_length[100]',
        'default_satuan'   => 'max_length[20]',
        'default_kemasan'  => 'max_length[50]',
    ];

    protected $validationMessages = [
        'nama_limbah'  => [
            'required'   => 'Nama limbah wajib diisi',
            'max_length' => 'Nama limbah maksimal 255 karakter',
        ],
        'kode_limbah'  => [
            'required'   => 'Kode limbah wajib diisi',
            'max_length' => 'Kode limbah maksimal 50 karakter',
        ],
        'kategori_bahaya'  => [
            'required' => 'Kategori bahaya wajib dipilih',
        ],
    ];

    public function getAllLimbah()
    {
        return $this->orderBy('nama_limbah', 'ASC')->findAll();
    }

    public function searchByName(string $keyword, int $limit = 10): array
    {
        return $this->like('nama_limbah', $keyword, 'both', null, true)
            ->orderBy('nama_limbah', 'ASC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get data for PDF export with complete information
     */
    public function getDataForExport(): array
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT 
                lb.id,
                lb.tanggal_input,
                lb.lokasi,
                lb.timbulan,
                lb.satuan,
                lb.bentuk_fisik,
                lb.kemasan,
                lb.status,
                lb.keterangan,
                mlb.nama_limbah,
                mlb.kode_limbah,
                mlb.karakteristik,
                mlb.kategori_bahaya,
                u.nama_lengkap as nama_user,
                un.nama_unit
            FROM limbah_b3 lb
            LEFT JOIN master_limbah_b3 mlb ON mlb.id = lb.master_b3_id
            LEFT JOIN users u ON u.id = lb.id_user
            LEFT JOIN unit un ON un.id = u.unit_id
            ORDER BY lb.tanggal_input DESC
        ");
        
        return $query->getResultArray();
    }
}
