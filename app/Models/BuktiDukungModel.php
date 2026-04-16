<?php

namespace App\Models;

use CodeIgniter\Model;

class BuktiDukungModel extends Model
{
    protected $table = 'bukti_dukung';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'judul',
        'periode', 
        'nama_file',
        'ukuran_file',
        'tipe_file'
    ];

    // Dates - using uploaded_at instead of created_at/updated_at
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'uploaded_at';
    protected $updatedField = '';

    // Validation
    protected $validationRules = [
        'judul' => 'required|max_length[255]',
        'periode' => 'max_length[50]',
        'nama_file' => 'required|max_length[255]',
        'ukuran_file' => 'max_length[50]',
        'tipe_file' => 'max_length[50]'
    ];

    protected $validationMessages = [
        'judul' => [
            'required' => 'Judul bukti dukung wajib diisi',
            'max_length' => 'Judul terlalu panjang (maksimal 255 karakter)'
        ],
        'periode' => [
            'max_length' => 'Periode terlalu panjang (maksimal 50 karakter)'
        ],
        'nama_file' => [
            'required' => 'Nama file wajib diisi',
            'max_length' => 'Nama file terlalu panjang (maksimal 255 karakter)'
        ],
        'ukuran_file' => [
            'max_length' => 'Ukuran file terlalu panjang (maksimal 50 karakter)'
        ],
        'tipe_file' => [
            'max_length' => 'Tipe file terlalu panjang (maksimal 50 karakter)'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Get all bukti dukung with formatted data
     */
    public function getAllFormatted()
    {
        $results = $this->orderBy('uploaded_at', 'DESC')->findAll();
        
        foreach ($results as &$item) {
            // ukuran_file is already formatted as string, no need to convert
            $item['file_size_formatted'] = $item['ukuran_file'];
        }
        
        return $results;
    }

    /**
     * Format file size to human readable format
     */
    public function formatFileSize($bytes)
    {
        if (is_numeric($bytes)) {
            if ($bytes >= 1073741824) {
                return number_format($bytes / 1073741824, 2) . ' GB';
            } elseif ($bytes >= 1048576) {
                return number_format($bytes / 1048576, 2) . ' MB';
            } elseif ($bytes >= 1024) {
                return number_format($bytes / 1024, 2) . ' KB';
            } else {
                return $bytes . ' bytes';
            }
        }
        return $bytes; // Return as-is if already formatted
    }
}