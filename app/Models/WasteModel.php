<?php

namespace App\Models;

use CodeIgniter\Model;

class WasteModel extends Model
{
    protected $table            = 'waste_management';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'unit_id',
        'tps_id',
        'kategori_id',
        'user_id',
        'created_by',
        'tanggal',
        'jenis_sampah',
        'nama_sampah',  // Nama detail sampah (Keyboard Bekas, dll)
        'satuan',
        'jumlah',
        'berat_kg',
        'gedung',
        'pengirim_gedung',
        'kategori_sampah',
        'nilai_rupiah',
        'status',
        'catatan_admin',
        'tps_reviewed_by',
        'tps_reviewed_at',
        'tps_catatan',
        'admin_reviewed_by',
        'admin_reviewed_at',
        'admin_catatan',
        'batch_id',
        'bukti_foto',
        'nama_pelapor',
        'gedung_pelapor',
        'reviewed_by',
        'reviewed_at',
        'action_timestamp',
        'unit_pengirim_id'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'unit_id' => 'int',
        'jumlah' => '?float',
        'nilai_rupiah' => '?float'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'unit_id' => 'required|integer',
        'tanggal' => 'required|valid_date',
        'jenis_sampah' => 'required|max_length[100]',
        'satuan' => 'required|max_length[10]',
        'jumlah' => 'required|numeric|greater_than[0]',
        'gedung' => 'required|max_length[50]'
        // Status validation removed - will be set by application logic
    ];

    protected $validationMessages = [
        'unit_id' => [
            'required' => 'Unit ID harus diisi',
            'integer' => 'Unit ID harus berupa angka'
        ],
        'tanggal' => [
            'required' => 'Tanggal harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'jenis_sampah' => [
            'required' => 'Jenis sampah harus diisi',
            'max_length' => 'Jenis sampah maksimal 100 karakter'
        ],
        'jumlah' => [
            'required' => 'Jumlah harus diisi',
            'numeric' => 'Jumlah harus berupa angka yang valid',
            'greater_than' => 'Jumlah harus lebih dari 0'
        ],
        'gedung' => [
            'required' => 'Gedung harus diisi',
            'max_length' => 'Gedung maksimal 50 karakter'
        ]
    ];

    /**
     * Get waste data by unit
     */
    public function getWasteByUnit($unitId, $jenisSampah = null)
    {
        $builder = $this->where('unit_id', $unitId);
        
        if ($jenisSampah) {
            $builder->where('jenis_sampah', $jenisSampah);
        }

        return $builder->orderBy('tanggal', 'DESC')->findAll();
    }

    /**
     * Get available satuan options
     */
    public function getAvailableSatuan()
    {
        return [
            // Berat
            'mg' => 'Miligram (mg)',
            'g' => 'Gram (g)', 
            'kg' => 'Kilogram (kg)',
            'ton' => 'Ton',
            
            // Volume
            'ml' => 'Mililiter (ml)',
            'L' => 'Liter (L)',
            'm³' => 'Meter Kubik (m³)',
            
            // Jumlah
            'pcs' => 'Pieces (pcs)',
            'unit' => 'Unit',
            'lembar' => 'Lembar',
            'buah' => 'Buah'
        ];
    }

    /**
     * Get default satuan by jenis sampah
     */
    public function getDefaultSatuanByJenis($jenisSampah)
    {
        $defaultSatuanMap = [
            'Kertas' => 'kg',
            'Plastik' => 'kg',
            'Organik' => 'kg',
            'Anorganik' => 'kg',
            'Limbah Cair' => 'L',
            'B3' => 'kg'
        ];

        return $defaultSatuanMap[$jenisSampah] ?? 'kg';
    }

    /**
     * Get recommended satuan by jenis sampah
     */
    public function getRecommendedSatuanByJenis($jenisSampah)
    {
        $recommendedMap = [
            'Kertas' => ['g', 'kg', 'ton', 'lembar', 'pcs'],
            'Plastik' => ['g', 'kg', 'ton', 'pcs', 'unit'],
            'Organik' => ['g', 'kg', 'ton'],
            'Anorganik' => ['g', 'kg', 'ton', 'pcs', 'unit'],
            'Limbah Cair' => ['ml', 'L', 'm³'],
            'B3' => ['mg', 'g', 'kg', 'ml', 'L', 'pcs']
        ];

        return $recommendedMap[$jenisSampah] ?? ['kg'];
    }

    /**
     * Get waste statistics by unit
     */
    public function getWasteStatsByUnit($unitId)
    {
        $jenisSampah = ['Kertas', 'Plastik', 'Organik', 'Anorganik', 'Limbah Cair', 'B3', 'Logam', 'Residu'];
        $stats = [];

        foreach ($jenisSampah as $jenis) {
            $total = $this->where('unit_id', $unitId)
                         ->where('jenis_sampah', $jenis)
                         ->countAllResults();

            $disetujui = $this->where('unit_id', $unitId)
                             ->where('jenis_sampah', $jenis)
                             ->where('status', 'disetujui')
                             ->countAllResults();

            $perlu_revisi = $this->where('unit_id', $unitId)
                                ->where('jenis_sampah', $jenis)
                                ->where('status', 'perlu_revisi')
                                ->countAllResults();

            $dikirim = $this->where('unit_id', $unitId)
                           ->where('jenis_sampah', $jenis)
                           ->where('status', 'dikirim')
                           ->countAllResults();

            $draft = $this->where('unit_id', $unitId)
                         ->where('jenis_sampah', $jenis)
                         ->where('status', 'draft')
                         ->countAllResults();

            $stats[$jenis] = [
                'total' => $total,
                'disetujui' => $disetujui,
                'perlu_revisi' => $perlu_revisi,
                'dikirim' => $dikirim,
                'draft' => $draft
            ];
        }

        return $stats;
    }

    /**
     * Get overall waste statistics by unit
     */
    public function getOverallWasteStats($unitId)
    {
        $disetujui = $this->where('unit_id', $unitId)
                         ->where('status', 'disetujui')
                         ->countAllResults();

        $perlu_revisi = $this->where('unit_id', $unitId)
                            ->where('status', 'perlu_revisi')
                            ->countAllResults();

        $dikirim = $this->where('unit_id', $unitId)
                       ->where('status', 'dikirim')
                       ->countAllResults();

        $draft = $this->where('unit_id', $unitId)
                     ->where('status', 'draft')
                     ->countAllResults();

        return [
            'disetujui' => $disetujui,
            'perlu_revisi' => $perlu_revisi,
            'dikirim' => $dikirim,
            'draft' => $draft,
            'total' => $disetujui + $perlu_revisi + $dikirim + $draft
        ];
    }

    /**
     * Validate and sanitize waste data before insert/update
     */
    public function validateWasteData($data)
    {
        // Sanitize jumlah field
        if (isset($data['jumlah'])) {
            $data['jumlah'] = $this->sanitizeJumlah($data['jumlah']);
        }

        // Sanitize gedung field
        if (isset($data['gedung'])) {
            $data['gedung'] = trim($data['gedung']);
        }

        return $data;
    }

    /**
     * Sanitize jumlah field to prevent null values
     */
    private function sanitizeJumlah($jumlah)
    {
        if ($jumlah === null || $jumlah === '' || !is_numeric($jumlah)) {
            return 0; // Default value instead of null
        }

        return (float) $jumlah;
    }

    /**
     * Override insert method to validate data
     */
    public function insert($data = null, bool $returnID = true)
    {
        if ($data !== null) {
            $data = $this->validateWasteData($data);
        }

        return parent::insert($data, $returnID);
    }

    /**
     * Override update method to validate data
     */
    public function update($id = null, $data = null): bool
    {
        if ($data !== null) {
            $data = $this->validateWasteData($data);
        }

        return parent::update($id, $data);
    }

    /**
     * Get available gedung options for TPS
     */
    public function getAvailableGedungTps()
    {
        return [
            'Gedung A – Gedung Kuliah',
            'Gedung B – Administrasi Niaga',
            'Gedung C – Gedung Kuliah',
            'Gedung D – Teknik Komputer dan Informatika',
            'Gedung E – Akuntansi',
            'Gedung F – Gedung Kuliah Baru',
            'Gedung H – Pascasarjana',
            'Gedung Laboratorium Teknik Mesin',
            'Gedung Laboratorium Teknik Sipil',
            'Gedung Laboratorium Teknik Kimia',
            'Gedung Laboratorium Teknik Refrigerasi dan Tata Udara',
            'Hanggar Aeronautika',
            'Gedung Direktorat'
        ];
    }

    /**
     * Get available jenis sampah for TPS
     */
    public function getJenisSampahTps()
    {
        return [
            'Plastik',
            'Kertas',
            'Logam',
            'Organik',
            'Residu'
        ];
    }

    /**
     * Get available satuan for TPS
     */
    public function getSatuanTps()
    {
        return [
            'kg' => 'Kilogram (kg)',
            'ton' => 'Ton'
        ];
    }

    /**
     * Get harga per kg for different jenis sampah - FROM MASTER HARGA
     */
    public function getHargaPerKg($jenisSampah)
    {
        $hargaModel = new \App\Models\MasterHargaSampahModel();
        return $hargaModel->getHargaPerKg($jenisSampah);
    }

    /**
     * Calculate nilai rupiah for TPS waste
     */
    public function calculateNilaiRupiah($jenisSampah, $jumlah, $satuan, $kategoriSampah)
    {
        // Hanya hitung jika kategori bisa dijual
        if ($kategoriSampah !== 'bisa_dijual') {
            return null;
        }

        $hargaPerKg = $this->getHargaPerKg($jenisSampah);
        
        if ($hargaPerKg <= 0) {
            return null;
        }

        // Convert to kg if needed
        $beratKg = $jumlah;
        if ($satuan === 'ton') {
            $beratKg = $jumlah * 1000;
        }

        return $beratKg * $hargaPerKg;
    }

    /**
     * Check if jenis sampah can be sold - FROM MASTER HARGA
     */
    public function canBeSold($jenisSampah)
    {
        $hargaModel = new \App\Models\MasterHargaSampahModel();
        return $hargaModel->canBeSold($jenisSampah);
    }

    /**
     * Get TPS waste data with additional fields - SAFE VERSION (No pengirim_gedung dependency)
     */
    public function getTpsWasteData($unitId, $jenisSampah = null)
    {
        // Use basic query without pengirim_gedung dependency
        $builder = $this->select('waste_management.*')
                        ->where('unit_id', $unitId);
        
        if ($jenisSampah) {
            $builder->where('jenis_sampah', $jenisSampah);
        }

        return $builder->orderBy('tanggal', 'DESC')->findAll();
    }
}