<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterHargaSampahModel extends Model
{
    protected $table            = 'master_harga_sampah';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'jenis_sampah',
        'nama_jenis',
        'harga_per_satuan',
        'satuan',
        'status_aktif',
        'dapat_dijual',
        'deskripsi',
        'created_by',
        'updated_by'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'harga_per_satuan' => 'float',
        'status_aktif' => 'boolean',
        'dapat_dijual' => 'boolean',
        'created_by' => '?int',
        'updated_by' => '?int'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'jenis_sampah' => 'required|in_list[Plastik,Kertas,Logam,Organik,Residu]',
        'nama_jenis' => 'required|max_length[100]',
        'harga_per_satuan' => 'required|decimal|greater_than_equal_to[0]',
        'satuan' => 'required|max_length[20]',
        'status_aktif' => 'required|in_list[0,1]',
        'dapat_dijual' => 'required|in_list[0,1]'
    ];

    protected $validationMessages = [
        'jenis_sampah' => [
            'required' => 'Jenis sampah harus diisi',
            'in_list' => 'Jenis sampah tidak valid'
        ],
        'nama_jenis' => [
            'required' => 'Nama jenis sampah harus diisi',
            'max_length' => 'Nama jenis sampah maksimal 100 karakter'
        ],
        'harga_per_satuan' => [
            'required' => 'Harga per satuan harus diisi',
            'decimal' => 'Harga harus berupa angka desimal',
            'greater_than_equal_to' => 'Harga tidak boleh negatif'
        ],
        'satuan' => [
            'required' => 'Satuan harus diisi',
            'max_length' => 'Satuan maksimal 20 karakter'
        ]
    ];

    /**
     * Get harga sampah by jenis sampah
     */
    public function getHargaByJenis($jenisSampah)
    {
        return $this->where('jenis_sampah', $jenisSampah)
                   ->where('status_aktif', 1)
                   ->first();
    }

    /**
     * Get all harga sampah yang aktif
     */
    public function getAllHargaAktif()
    {
        return $this->where('status_aktif', 1)
                   ->orderBy('dapat_dijual', 'DESC')
                   ->orderBy('harga_per_satuan', 'DESC')
                   ->findAll();
    }

    /**
     * Get harga sampah yang bisa dijual saja
     */
    public function getHargaSampahBisaDijual()
    {
        return $this->where('status_aktif', 1)
                   ->where('dapat_dijual', 1)
                   ->orderBy('harga_per_satuan', 'DESC')
                   ->findAll();
    }

    /**
     * Get harga per kg untuk jenis sampah tertentu
     */
    public function getHargaPerKg($jenisSampah)
    {
        $harga = $this->getHargaByJenis($jenisSampah);
        
        if (!$harga || !$harga['dapat_dijual']) {
            return 0;
        }
        
        return (float) $harga['harga_per_satuan'];
    }

    /**
     * Check if jenis sampah can be sold
     */
    public function canBeSold($jenisSampah)
    {
        $harga = $this->getHargaByJenis($jenisSampah);
        
        if (!$harga) {
            return false;
        }
        
        return (bool) $harga['dapat_dijual'];
    }

    /**
     * Get all jenis sampah yang bisa dijual
     */
    public function getJenisSampahBisaDijual()
    {
        $result = $this->select('jenis_sampah')
                      ->where('status_aktif', 1)
                      ->where('dapat_dijual', 1)
                      ->findAll();
        
        return array_column($result, 'jenis_sampah');
    }

    /**
     * Update harga sampah (hanya untuk admin)
     */
    public function updateHarga($id, $data, $adminId)
    {
        // Get old data for logging
        $oldData = $this->find($id);
        
        if (!$oldData) {
            return false;
        }
        
        // Add admin info
        $data['updated_by'] = $adminId;
        
        // Update data
        $result = $this->update($id, $data);
        
        if ($result) {
            // Log the change
            $this->logPerubahanHarga($oldData, $data, 'update', $adminId);
        }
        
        return $result;
    }

    /**
     * Create new harga sampah (hanya untuk admin)
     */
    public function createHarga($data, $adminId)
    {
        $data['created_by'] = $adminId;
        $data['updated_by'] = $adminId;
        
        $result = $this->insert($data);
        
        if ($result) {
            // Log the change
            $this->logPerubahanHarga(null, $data, 'create', $adminId);
        }
        
        return $result;
    }

    /**
     * Toggle status aktif
     */
    public function toggleStatus($id, $adminId)
    {
        $data = $this->find($id);
        
        if (!$data) {
            return false;
        }
        
        $newStatus = !$data['status_aktif'];
        $updateData = [
            'status_aktif' => $newStatus,
            'updated_by' => $adminId
        ];
        
        $result = $this->update($id, $updateData);
        
        if ($result) {
            $action = $newStatus ? 'activate' : 'deactivate';
            $this->logPerubahanHarga($data, $updateData, $action, $adminId);
        }
        
        return $result;
    }

    /**
     * Log perubahan harga untuk audit trail
     */
    private function logPerubahanHarga($oldData, $newData, $action, $adminId)
    {
        $logModel = new \App\Models\LogPerubahanHargaModel();
        
        // Get admin info
        $userModel = new \App\Models\UserModel();
        $admin = $userModel->find($adminId);
        
        $logData = [
            'jenis_sampah' => $newData['jenis_sampah'] ?? $oldData['jenis_sampah'],
            'harga_lama' => $oldData ? $oldData['harga_per_satuan'] : null,
            'harga_baru' => $newData['harga_per_satuan'] ?? $oldData['harga_per_satuan'],
            'aksi' => $action,
            'admin_id' => $adminId,
            'admin_nama' => $admin ? $admin['nama_lengkap'] : 'Unknown',
            'keterangan' => $this->generateKeterangan($action, $oldData, $newData)
        ];
        
        try {
            $logModel->insert($logData);
        } catch (\Exception $e) {
            // Log error but don't fail the main operation
            log_message('error', 'Failed to log harga change: ' . $e->getMessage());
        }
    }

    /**
     * Generate keterangan for log
     */
    private function generateKeterangan($action, $oldData, $newData)
    {
        switch ($action) {
            case 'create':
                return "Membuat harga baru untuk {$newData['jenis_sampah']}: Rp " . number_format($newData['harga_per_satuan'], 0, ',', '.');
            
            case 'update':
                $oldHarga = number_format($oldData['harga_per_satuan'], 0, ',', '.');
                $newHarga = number_format($newData['harga_per_satuan'], 0, ',', '.');
                return "Mengubah harga {$oldData['jenis_sampah']} dari Rp {$oldHarga} menjadi Rp {$newHarga}";
            
            case 'activate':
                return "Mengaktifkan harga {$oldData['jenis_sampah']}";
            
            case 'deactivate':
                return "Menonaktifkan harga {$oldData['jenis_sampah']}";
            
            default:
                return "Aksi {$action} pada {$oldData['jenis_sampah']}";
        }
    }

    /**
     * Get statistics for dashboard
     */
    public function getStatistics()
    {
        $total = $this->countAllResults();
        $aktif = $this->where('status_aktif', 1)->countAllResults();
        $bisaDijual = $this->where('dapat_dijual', 1)->where('status_aktif', 1)->countAllResults();
        $tidakBisaDijual = $this->where('dapat_dijual', 0)->where('status_aktif', 1)->countAllResults();
        
        return [
            'total' => $total,
            'aktif' => $aktif,
            'nonaktif' => $total - $aktif,
            'bisa_dijual' => $bisaDijual,
            'tidak_bisa_dijual' => $tidakBisaDijual
        ];
    }
}