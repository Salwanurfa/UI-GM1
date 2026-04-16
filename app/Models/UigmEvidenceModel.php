<?php

namespace App\Models;

use CodeIgniter\Model;

class UigmEvidenceModel extends Model
{
    protected $table = 'uigm_evidence';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'kategori_id',
        'sub_kategori',
        'nama_bukti',
        'deskripsi_bukti',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'status_upload',
        'keterangan',
        'urutan',
        'uploaded_by',
        'uploaded_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'kategori_id' => 'required|integer',
        'nama_bukti' => 'required|min_length[5]|max_length[255]',
        'urutan' => 'required|integer|greater_than[0]'
    ];

    protected $validationMessages = [
        'kategori_id' => [
            'required' => 'Kategori harus dipilih',
            'integer' => 'Kategori tidak valid'
        ],
        'nama_bukti' => [
            'required' => 'Nama bukti harus diisi',
            'min_length' => 'Nama bukti minimal 5 karakter',
            'max_length' => 'Nama bukti maksimal 255 karakter'
        ],
        'urutan' => [
            'required' => 'Urutan harus diisi',
            'integer' => 'Urutan harus berupa angka',
            'greater_than' => 'Urutan minimal 1'
        ]
    ];

    // Skip validation
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;

    /**
     * Get evidence by category grouped by sub-category
     */
    public function getEvidenceByCategory($kategoriId)
    {
        $evidence = $this->db->table($this->table . ' ue')
            ->select('ue.*, u.nama_lengkap as uploaded_by_name')
            ->join('users u', 'u.id = ue.uploaded_by', 'left')
            ->where('ue.kategori_id', $kategoriId)
            ->orderBy('ue.sub_kategori', 'ASC')
            ->orderBy('ue.urutan', 'ASC')
            ->get()
            ->getResultArray();

        // Group by sub-category
        $grouped = [];
        foreach ($evidence as $item) {
            $subKategori = $item['sub_kategori'];
            if (!isset($grouped[$subKategori])) {
                $grouped[$subKategori] = [];
            }
            $grouped[$subKategori][] = $item;
        }

        return $grouped;
    }

    /**
     * Get evidence by category (flat list) - for backward compatibility
     */
    public function getEvidenceByCategoryFlat($kategoriId)
    {
        return $this->db->table($this->table . ' ue')
            ->select('ue.*, u.nama_lengkap as uploaded_by_name')
            ->join('users u', 'u.id = ue.uploaded_by', 'left')
            ->where('ue.kategori_id', $kategoriId)
            ->orderBy('ue.urutan', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Upload file evidence
     */
    public function uploadEvidence($evidenceId, $fileData, $uploadedBy)
    {
        $data = [
            'file_path' => $fileData['file_path'],
            'file_name' => $fileData['file_name'],
            'file_size' => $fileData['file_size'],
            'file_type' => $fileData['file_type'],
            'status_upload' => 'sudah_upload',
            'uploaded_by' => $uploadedBy,
            'uploaded_at' => date('Y-m-d H:i:s')
        ];

        return $this->update($evidenceId, $data);
    }

    /**
     * Remove file evidence
     */
    public function removeEvidence($evidenceId)
    {
        $data = [
            'file_path' => null,
            'file_name' => null,
            'file_size' => null,
            'file_type' => null,
            'status_upload' => 'belum_upload',
            'uploaded_by' => null,
            'uploaded_at' => null
        ];

        return $this->update($evidenceId, $data);
    }

    /**
     * Update evidence description or notes
     */
    public function updateEvidence($evidenceId, $data)
    {
        $allowedFields = ['nama_bukti', 'deskripsi_bukti', 'keterangan', 'urutan'];
        $updateData = [];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }

        return $this->update($evidenceId, $updateData);
    }

    /**
     * Get evidence statistics for category
     */
    public function getCategoryStats($kategoriId)
    {
        $total = $this->where('kategori_id', $kategoriId)->countAllResults(false);
        $uploaded = $this->where('status_upload', 'sudah_upload')->countAllResults(false);
        $pending = $this->where('status_upload', 'belum_upload')->countAllResults(false);
        $revision = $this->where('status_upload', 'perlu_revisi')->countAllResults();

        return [
            'total' => $total,
            'uploaded' => $uploaded,
            'pending' => $pending,
            'revision' => $revision,
            'completion_rate' => $total > 0 ? round(($uploaded / $total) * 100, 1) : 0
        ];
    }

    /**
     * Get file size in human readable format
     */
    public function formatFileSize($bytes)
    {
        if ($bytes == 0) return '0 Bytes';
        
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    /**
     * Validate file upload
     */
    public function validateFile($file)
    {
        $errors = [];
        
        // Check file size (max 10MB)
        $maxSize = 10 * 1024 * 1024;
        if ($file->getSize() > $maxSize) {
            $errors[] = 'Ukuran file maksimal 10MB';
        }

        // Check file type
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (!in_array($file->getClientMimeType(), $allowedTypes)) {
            $errors[] = 'Format file tidak didukung. Gunakan PDF, JPG, PNG, DOC, atau DOCX';
        }

        return $errors;
    }
}