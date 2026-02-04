<?php

namespace App\Models;

use CodeIgniter\Model;

class HargaSampahModel extends Model
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
        'harga_per_kg',
        'satuan',
        'status_aktif',
        'dapat_dijual',
        'deskripsi',
        'tanggal_berlaku',
        'created_at',
        'updated_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'harga_per_satuan' => 'float',
        'harga_per_kg' => 'float',
        'status_aktif' => 'boolean',
        'dapat_dijual' => 'boolean'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'jenis_sampah' => 'required|max_length[100]',
        'harga_per_satuan' => 'permit_empty|decimal',
        'satuan' => 'required|in_list[kg,gram,ton,liter,pcs,karung]',
        'status_aktif' => 'permit_empty|in_list[0,1]',
        'dapat_dijual' => 'permit_empty|in_list[0,1]'
    ];

    protected $validationMessages = [
        'jenis_sampah' => [
            'required' => 'Jenis sampah harus diisi',
            'max_length' => 'Jenis sampah maksimal 100 karakter'
        ],
        'harga_per_satuan' => [
            'required' => 'Harga per satuan harus diisi',
            'decimal' => 'Harga harus berupa angka'
        ]
    ];

    /**
     * Get active prices
     */
    public function getActiveHarga(): array
    {
        return $this->where('status_aktif', 1)
                   ->orderBy('jenis_sampah', 'ASC')
                   ->findAll();
    }

    /**
     * Get sellable waste prices
     */
    public function getSellableHarga(): array
    {
        return $this->where('status_aktif', 1)
                   ->where('dapat_dijual', 1)
                   ->orderBy('jenis_sampah', 'ASC')
                   ->findAll();
    }

    /**
     * Get price by waste type
     */
    public function getHargaByJenis(string $jenisSampah): ?array
    {
        return $this->where('jenis_sampah', $jenisSampah)
                   ->where('status_aktif', 1)
                   ->first();
    }

    /**
     * Get price map for calculation
     */
    public function getPriceMap(): array
    {
        $prices = $this->getActiveHarga();
        $map = [];
        
        foreach ($prices as $price) {
            $map[$price['jenis_sampah']] = [
                'harga_per_satuan' => $price['harga_per_satuan'],
                'harga_per_kg' => $price['harga_per_kg'],
                'satuan' => $price['satuan'],
                'dapat_dijual' => $price['dapat_dijual']
            ];
        }
        
        return $map;
    }

    /**
     * Calculate waste value
     */
    public function calculateValue(string $jenisSampah, float $berat): float
    {
        $harga = $this->getHargaByJenis($jenisSampah);
        
        if (!$harga || !$harga['dapat_dijual']) {
            return 0;
        }
        
        // Convert to kg if needed
        $beratKg = $berat;
        if ($harga['satuan'] === 'ton') {
            $beratKg = $berat * 1000;
        } elseif ($harga['satuan'] === 'liter') {
            // Assume 1 liter = 1 kg for simplicity
            $beratKg = $berat;
        }
        
        return $beratKg * $harga['harga_per_satuan'];
    }

    /**
     * Get statistics
     */
    public function getStatistics(): array
    {
        return [
            'total_jenis' => $this->countAllResults(),
            'aktif' => $this->where('status_aktif', 1)->countAllResults(),
            'dapat_dijual' => $this->where('dapat_dijual', 1)->countAllResults(),
            'rata_rata_harga' => $this->where('status_aktif', 1)->selectAvg('harga_per_satuan')->first()['harga_per_satuan'] ?? 0
        ];
    }

    /**
     * Count all waste types
     */
    public function countAllTypes(): int
    {
        try {
            return $this->countAllResults(false);
        } catch (\Exception $e) {
            log_message('error', 'HargaSampahModel countAllTypes error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Count active prices
     */
    public function countActivePrices(): int
    {
        try {
            return $this->where('status_aktif', 1)->countAllResults(false);
        } catch (\Exception $e) {
            log_message('error', 'HargaSampahModel countActivePrices error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Count sellable waste types
     */
    public function countSellable(): int
    {
        try {
            return $this->where('dapat_dijual', 1)->where('status_aktif', 1)->countAllResults(false);
        } catch (\Exception $e) {
            log_message('error', 'HargaSampahModel countSellable error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Update price
     */
    public function updateHarga(int $id, float $hargaBaru): bool
    {
        $data = [
            'harga_per_satuan' => $hargaBaru,
            'harga_per_kg' => $hargaBaru
        ];
        
        return $this->update($id, $data);
    }

    /**
     * Toggle status
     */
    public function toggleStatus(int $id): bool
    {
        $current = $this->find($id);
        if (!$current) {
            return false;
        }
        
        $data = [
            'status_aktif' => !$current['status_aktif']
        ];
        
        return $this->update($id, $data);
    }

    /**
     * Get waste types for dropdown
     */
    public function getJenisOptions(): array
    {
        $data = $this->where('status_aktif', 1)
                    ->select('jenis_sampah, nama_jenis')
                    ->orderBy('jenis_sampah', 'ASC')
                    ->findAll();
        
        $options = [];
        foreach ($data as $item) {
            $options[$item['jenis_sampah']] = $item['nama_jenis'];
        }
        
        return $options;
    }
}