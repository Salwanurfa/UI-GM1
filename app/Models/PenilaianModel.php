<?php

namespace App\Models;

use CodeIgniter\Model;

class PenilaianModel extends Model
{
    protected $table            = 'penilaian_unit';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'unit_id',
        'kategori_uigm',
        'indikator',
        'nilai_input',
        'status',
        'catatan_admin'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'unit_id' => 'int',
        'nilai_input' => '?float' // Nullable float untuk menghindari error casting
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'unit_id' => 'required|integer',
        'kategori_uigm' => 'required|in_list[SI,EC,WS,WR,TR,ED]',
        'indikator' => 'required|max_length[255]',
        'nilai_input' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
        'status' => 'required|in_list[draft,dikirim,review,disetujui,perlu_revisi]'
    ];

    protected $validationMessages = [
        'unit_id' => [
            'required' => 'Unit ID harus diisi',
            'integer' => 'Unit ID harus berupa angka'
        ],
        'kategori_uigm' => [
            'required' => 'Kategori UIGM harus diisi',
            'in_list' => 'Kategori UIGM tidak valid'
        ],
        'indikator' => [
            'required' => 'Indikator harus diisi',
            'max_length' => 'Indikator maksimal 255 karakter'
        ],
        'nilai_input' => [
            'required' => 'Nilai input harus diisi',
            'numeric' => 'Nilai input harus berupa angka',
            'greater_than_equal_to' => 'Nilai input minimal 0',
            'less_than_equal_to' => 'Nilai input maksimal 100'
        ],
        'status' => [
            'required' => 'Status harus diisi',
            'in_list' => 'Status tidak valid'
        ]
    ];

    /**
     * Get penilaian by unit and kategori
     */
    public function getPenilaianByUnitKategori($unitId, $kategori)
    {
        return $this->where('unit_id', $unitId)
                   ->where('kategori_uigm', $kategori)
                   ->findAll();
    }

    /**
     * Get progress by unit
     */
    public function getProgressByUnit($unitId)
    {
        $categories = ['SI', 'EC', 'WS', 'WR', 'TR', 'ED'];
        $progress = [];

        foreach ($categories as $kategori) {
            $total = $this->where('unit_id', $unitId)
                         ->where('kategori_uigm', $kategori)
                         ->countAllResults();

            $selesai = $this->where('unit_id', $unitId)
                           ->where('kategori_uigm', $kategori)
                           ->where('status', 'disetujui')
                           ->countAllResults();

            $perlu_revisi = $this->where('unit_id', $unitId)
                                ->where('kategori_uigm', $kategori)
                                ->where('status', 'perlu_revisi')
                                ->countAllResults();

            $progress[$kategori] = [
                'total' => $total,
                'selesai' => $selesai,
                'perlu_revisi' => $perlu_revisi,
                'belum_diisi' => $total - $selesai - $perlu_revisi,
                'progress_persen' => $total > 0 ? round(($selesai / $total) * 100, 1) : 0
            ];
        }

        return $progress;
    }

    /**
     * Get overall progress by unit
     */
    public function getOverallProgress($unitId)
    {
        $total = $this->where('unit_id', $unitId)->countAllResults();
        $selesai = $this->where('unit_id', $unitId)
                       ->where('status', 'disetujui')
                       ->countAllResults();

        return [
            'total' => $total,
            'selesai' => $selesai,
            'progress_persen' => $total > 0 ? round(($selesai / $total) * 100, 1) : 0
        ];
    }

    /**
     * Get indikator template by kategori
     */
    public function getIndikatorTemplate($kategori)
    {
        $templates = [
            'SI' => [
                'Rasio Ruang Terbuka Hijau',
                'Jumlah Gedung Hijau',
                'Kebijakan Lingkungan',
                'Program Konservasi'
            ],
            'EC' => [
                'Penggunaan Energi Terbarukan',
                'Efisiensi Energi Gedung',
                'Program Mitigasi Karbon',
                'Emisi Gas Rumah Kaca'
            ],
            'WS' => [
                'Program Daur Ulang',
                'Pengelolaan Sampah Organik',
                'Pengurangan Sampah Plastik',
                'Pengelolaan Limbah B3'
            ],
            'WR' => [
                'Konservasi Air',
                'Penggunaan Air Daur Ulang',
                'Program Penghematan Air',
                'Kualitas Air'
            ],
            'TR' => [
                'Transportasi Ramah Lingkungan',
                'Kebijakan Kendaraan',
                'Fasilitas Sepeda',
                'Program Car Free Day'
            ],
            'ED' => [
                'Kurikulum Lingkungan',
                'Penelitian Berkelanjutan',
                'Program Edukasi Lingkungan',
                'Publikasi Ilmiah Lingkungan'
            ]
        ];

        return $templates[$kategori] ?? [];
    }

    /**
     * Initialize indikator for unit
     */
    public function initializeIndikator($unitId, $kategori)
    {
        $indikators = $this->getIndikatorTemplate($kategori);
        
        foreach ($indikators as $indikator) {
            $existing = $this->where('unit_id', $unitId)
                           ->where('kategori_uigm', $kategori)
                           ->where('indikator', $indikator)
                           ->first();

            if (!$existing) {
                $this->insert([
                    'unit_id' => $unitId,
                    'kategori_uigm' => $kategori,
                    'indikator' => $indikator,
                    'nilai_input' => 0.0, // Default value bukan null
                    'status' => 'draft'
                ]);
            }
        }
    }

    /**
     * Validate and sanitize penilaian data before insert/update
     */
    public function validatePenilaianData($data)
    {
        // Sanitize nilai_input field
        if (isset($data['nilai_input'])) {
            $data['nilai_input'] = $this->sanitizeNilaiInput($data['nilai_input']);
        }

        return $data;
    }

    /**
     * Sanitize nilai_input field to prevent null values
     */
    private function sanitizeNilaiInput($nilaiInput)
    {
        if ($nilaiInput === null || $nilaiInput === '' || !is_numeric($nilaiInput)) {
            return 0.0; // Default value instead of null
        }

        $nilai = (float) $nilaiInput;
        
        // Clamp value between 0 and 100
        if ($nilai < 0) return 0.0;
        if ($nilai > 100) return 100.0;
        
        return $nilai;
    }

    /**
     * Override insert method to validate data
     */
    public function insert($data = null, bool $returnID = true)
    {
        if ($data !== null) {
            $data = $this->validatePenilaianData($data);
        }

        return parent::insert($data, $returnID);
    }

    /**
     * Override update method to validate data
     */
    public function update($id = null, $data = null): bool
    {
        if ($data !== null) {
            $data = $this->validatePenilaianData($data);
        }

        return parent::update($id, $data);
    }

    /**
     * Submit kategori to admin
     */
    public function submitKategori($unitId, $kategori)
    {
        return $this->where('unit_id', $unitId)
                   ->where('kategori_uigm', $kategori)
                   ->set('status', 'dikirim')
                   ->update();
    }
}