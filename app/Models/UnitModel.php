<?php

namespace App\Models;

use CodeIgniter\Model;

class UnitModel extends Model
{
    protected $table            = 'unit';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_unit',
        'kode_unit',
        'deskripsi',
        'status_aktif'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'status_aktif' => 'boolean',
    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'nama_unit' => 'required|max_length[255]',
        'kode_unit' => 'required|max_length[20]|is_unique[unit.kode_unit,id,{id}]',
    ];
    protected $validationMessages   = [
        'nama_unit' => [
            'required' => 'Nama unit harus diisi',
            'max_length' => 'Nama unit maksimal 255 karakter'
        ],
        'kode_unit' => [
            'required' => 'Kode unit harus diisi',
            'is_unique' => 'Kode unit sudah digunakan'
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
     * Get units with submission status for specific year
     */
    public function getUnitsWithSubmissionStatus(int $tahunId)
    {
        return $this->select('unit.*, 
                             pengiriman_unit.status_pengiriman,
                             pengiriman_unit.progress_persen,
                             pengiriman_unit.tanggal_kirim,
                             pengiriman_unit.id as pengiriman_id,
                             users.nama_lengkap as admin_name')
            ->join('pengiriman_unit', 'pengiriman_unit.unit_id = unit.id', 'left')
            ->join('users', 'users.id = unit.admin_unit_id', 'left')
            ->where('unit.status_aktif', true)
            ->where('pengiriman_unit.tahun_penilaian_id', $tahunId)
            ->orderBy('pengiriman_unit.tanggal_kirim', 'DESC')
            ->findAll();
    }

    /**
     * Get unit statistics for dashboard
     */
    public function getUnitStatistics(int $tahunId)
    {
        $totalUnits = $this->where('status_aktif', true)->countAllResults();

        $submittedUnits = $this->db->table('pengiriman_unit')
            ->where('tahun_penilaian_id', $tahunId)
            ->whereIn('status_pengiriman', ['dikirim', 'review', 'perlu_revisi', 'disetujui', 'final'])
            ->countAllResults();

        $pendingReview = $this->db->table('pengiriman_unit')
            ->where('tahun_penilaian_id', $tahunId)
            ->whereIn('status_pengiriman', ['dikirim', 'review'])
            ->countAllResults();

        $notSubmitted = $totalUnits - $submittedUnits;

        return [
            'total_unit' => $totalUnits,
            'unit_sudah_kirim' => $submittedUnits,
            'unit_belum_kirim' => $notSubmitted,
            'data_menunggu_review' => $pendingReview
        ];
    }

    /**
     * Get units by type
     */
    public function getUnitsByType(string $tipeUnit)
    {
        return $this->where('tipe_unit', $tipeUnit)
            ->where('status_aktif', true)
            ->findAll();
    }

    /**
     * Get child units - DISABLED (parent_id column not available)
     */
    /*
    public function getChildUnits(int $parentId)
    {
        return $this->where('parent_id', $parentId)
            ->where('status_aktif', true)
            ->findAll();
    }
    */

    /**
     * Get unit hierarchy - DISABLED (parent_id column not available)
     */
    /*
    public function getUnitHierarchy()
    {
        return $this->select('unit.*, parent.nama_unit as parent_name')
            ->join('unit as parent', 'parent.id = unit.parent_id', 'left')
            ->where('unit.status_aktif', true)
            ->orderBy('unit.tipe_unit')
            ->orderBy('unit.nama_unit')
            ->findAll();
    }
    */

    /**
     * Get unit with admin information
     */
    public function getUnitWithAdmin(int $unitId)
    {
        return $this->select('unit.*, users.nama_lengkap as admin_name, users.email as admin_email')
            ->join('users', 'users.id = unit.admin_unit_id', 'left')
            ->find($unitId);
    }

    /**
     * Get gedung berdasarkan nama unit
     */
    public function getGedungByUnit(string $namaUnit)
    {
        // Mapping unit ke gedung berdasarkan nama unit
        $gedungMapping = [
            // Fakultas
            'Fakultas Teknik' => 'Gedung Teknik',
            'Fakultas Ekonomi' => 'Gedung Ekonomi', 
            'Fakultas MIPA' => 'Gedung MIPA',
            'Fakultas Hukum' => 'Gedung Hukum',
            'Fakultas Kedokteran' => 'Gedung Kedokteran',
            'Fakultas Pertanian' => 'Gedung Pertanian',
            'Fakultas Perikanan' => 'Gedung Perikanan',
            
            // Jurusan Teknik
            'Jurusan Teknik Informatika' => 'Gedung D – Teknik Komputer dan Informatika',
            'Jurusan Teknik Komputer' => 'Gedung D – Teknik Komputer dan Informatika',
            'Jurusan Teknik Mesin' => 'Gedung Laboratorium Teknik Mesin',
            'Jurusan Teknik Sipil' => 'Gedung Laboratorium Teknik Sipil',
            'Jurusan Teknik Kimia' => 'Gedung Laboratorium Teknik Kimia',
            'Jurusan Teknik Refrigerasi' => 'Gedung Laboratorium Teknik Refrigerasi dan Tata Udara',
            'Jurusan Aeronautika' => 'Hanggar Aeronautika',
            
            // Jurusan Ekonomi
            'Jurusan Akuntansi' => 'Gedung E – Akuntansi',
            'Jurusan Manajemen' => 'Gedung B – Administrasi Niaga',
            'Jurusan Administrasi Niaga' => 'Gedung B – Administrasi Niaga',
            
            // Unit Khusus
            'Pascasarjana' => 'Gedung H – Pascasarjana',
            'Rektorat' => 'Gedung Direktorat',
            'Administrasi' => 'Gedung B – Administrasi Niaga',
            'Perpustakaan' => 'Gedung A – Gedung Kuliah',
            'Unit Penelitian' => 'Gedung F – Gedung Kuliah Baru',
            'Unit Pengabdian Masyarakat' => 'Gedung C – Gedung Kuliah',
            
            // Default berdasarkan kata kunci
            'Laboratorium' => 'Gedung Laboratorium',
            'Lab' => 'Gedung Laboratorium'
        ];

        // Cari exact match dulu
        if (isset($gedungMapping[$namaUnit])) {
            return $gedungMapping[$namaUnit];
        }

        // Cari berdasarkan kata kunci
        $namaUnitLower = strtolower($namaUnit);
        
        foreach ($gedungMapping as $unitKey => $gedung) {
            $unitKeyLower = strtolower($unitKey);
            
            // Cek apakah nama unit mengandung kata kunci
            if (strpos($namaUnitLower, $unitKeyLower) !== false || 
                strpos($unitKeyLower, $namaUnitLower) !== false) {
                return $gedung;
            }
        }

        // Mapping berdasarkan kata kunci dalam nama unit
        if (strpos($namaUnitLower, 'teknik') !== false) {
            if (strpos($namaUnitLower, 'informatika') !== false || strpos($namaUnitLower, 'komputer') !== false) {
                return 'Gedung D – Teknik Komputer dan Informatika';
            } elseif (strpos($namaUnitLower, 'mesin') !== false) {
                return 'Gedung Laboratorium Teknik Mesin';
            } elseif (strpos($namaUnitLower, 'sipil') !== false) {
                return 'Gedung Laboratorium Teknik Sipil';
            } elseif (strpos($namaUnitLower, 'kimia') !== false) {
                return 'Gedung Laboratorium Teknik Kimia';
            } else {
                return 'Gedung Teknik';
            }
        }

        if (strpos($namaUnitLower, 'ekonomi') !== false || strpos($namaUnitLower, 'akuntansi') !== false) {
            return strpos($namaUnitLower, 'akuntansi') !== false ? 'Gedung E – Akuntansi' : 'Gedung Ekonomi';
        }

        if (strpos($namaUnitLower, 'hukum') !== false) {
            return 'Gedung Hukum';
        }

        if (strpos($namaUnitLower, 'mipa') !== false || strpos($namaUnitLower, 'matematika') !== false || strpos($namaUnitLower, 'fisika') !== false) {
            return 'Gedung MIPA';
        }

        if (strpos($namaUnitLower, 'pascasarjana') !== false || strpos($namaUnitLower, 'magister') !== false || strpos($namaUnitLower, 'doktor') !== false) {
            return 'Gedung H – Pascasarjana';
        }

        if (strpos($namaUnitLower, 'laboratorium') !== false || strpos($namaUnitLower, 'lab') !== false) {
            return 'Gedung Laboratorium';
        }

        if (strpos($namaUnitLower, 'administrasi') !== false || strpos($namaUnitLower, 'niaga') !== false) {
            return 'Gedung B – Administrasi Niaga';
        }

        if (strpos($namaUnitLower, 'rektorat') !== false || strpos($namaUnitLower, 'direktorat') !== false) {
            return 'Gedung Direktorat';
        }

        // Default fallback
        return 'Gedung A – Gedung Kuliah';
    }

    /**
     * Get gedung for user berdasarkan unit_id
     */
    public function getGedungForUser(int $unitId)
    {
        $unit = $this->find($unitId);
        
        if (!$unit) {
            return 'Gedung A – Gedung Kuliah'; // Default
        }

        return $this->getGedungByUnit($unit['nama_unit']);
    }
}
