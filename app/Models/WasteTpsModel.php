<?php

namespace App\Models;

use CodeIgniter\Model;

class WasteTpsModel extends Model
{
    protected $table            = 'waste_tps';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tanggal',
        'sampah_dari_gedung',
        'jumlah_berat',
        'satuan',
        'nilai_rupiah',
        'pengelola_id'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'jumlah_berat' => 'float',
        'nilai_rupiah' => '?float',
        'pengelola_id' => 'int'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'tanggal' => 'required|valid_date',
        'sampah_dari_gedung' => 'required|max_length[100]',
        'jumlah_berat' => 'required|decimal|greater_than[0]',
        'satuan' => 'required|in_list[kg,liter]',
        'nilai_rupiah' => 'permit_empty|decimal|greater_than_equal_to[0]',
        'pengelola_id' => 'required|integer'
    ];

    protected $validationMessages = [
        'tanggal' => [
            'required' => 'Tanggal harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'sampah_dari_gedung' => [
            'required' => 'Gedung asal sampah harus dipilih',
            'max_length' => 'Nama gedung terlalu panjang'
        ],
        'jumlah_berat' => [
            'required' => 'Jumlah berat harus diisi',
            'decimal' => 'Jumlah berat harus berupa angka',
            'greater_than' => 'Jumlah berat harus lebih dari 0'
        ],
        'satuan' => [
            'required' => 'Satuan harus dipilih',
            'in_list' => 'Satuan harus kg atau liter'
        ],
        'nilai_rupiah' => [
            'decimal' => 'Nilai rupiah harus berupa angka',
            'greater_than_equal_to' => 'Nilai rupiah tidak boleh negatif'
        ]
    ];

    /**
     * Get data waste TPS berdasarkan pengelola
     */
    public function getByPengelola(int $pengelolaId)
    {
        return $this->select('waste_tps.*, users.nama_lengkap as pengelola_nama')
                    ->join('users', 'users.id = waste_tps.pengelola_id', 'left')
                    ->where('pengelola_id', $pengelolaId)
                    ->orderBy('tanggal', 'DESC')
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get statistik TPS berdasarkan pengelola
     */
    public function getStatsByPengelola(int $pengelolaId)
    {
        $result = $this->select('
                        COUNT(*) as total_records,
                        SUM(jumlah_berat) as total_berat,
                        SUM(CASE WHEN nilai_rupiah IS NOT NULL THEN nilai_rupiah ELSE 0 END) as total_nilai,
                        COUNT(CASE WHEN nilai_rupiah IS NOT NULL THEN 1 END) as sampah_bisa_dijual
                    ')
                    ->where('pengelola_id', $pengelolaId)
                    ->first();

        return $result ?: [
            'total_records' => 0,
            'total_berat' => 0,
            'total_nilai' => 0,
            'sampah_bisa_dijual' => 0
        ];
    }

    /**
     * Get data berdasarkan gedung
     */
    public function getByGedung(string $gedung, int $pengelolaId = null)
    {
        $builder = $this->where('sampah_dari_gedung', $gedung);
        
        if ($pengelolaId) {
            $builder->where('pengelola_id', $pengelolaId);
        }
        
        return $builder->orderBy('tanggal', 'DESC')->findAll();
    }

    /**
     * Get data berdasarkan rentang tanggal
     */
    public function getByDateRange(string $startDate, string $endDate, int $pengelolaId = null)
    {
        $builder = $this->where('tanggal >=', $startDate)
                        ->where('tanggal <=', $endDate);
        
        if ($pengelolaId) {
            $builder->where('pengelola_id', $pengelolaId);
        }
        
        return $builder->orderBy('tanggal', 'DESC')->findAll();
    }
}