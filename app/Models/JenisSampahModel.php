<?php

namespace App\Models;

use CodeIgniter\Model;

class JenisSampahModel extends Model
{
    protected $table            = 'jenis_sampah';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'parent_id',
        'kode',
        'nama',
        'level',
        'urutan',
        'status_aktif'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'parent_id' => '?int',
        'level' => 'int',
        'urutan' => 'int',
        'status_aktif' => 'boolean'
    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'kode' => 'required|max_length[50]|is_unique[jenis_sampah.kode,id,{id}]',
        'nama' => 'required|max_length[255]',
        'level' => 'required|integer|in_list[1,2,3]',
        'urutan' => 'integer',
        'status_aktif' => 'in_list[0,1]'
    ];

    protected $validationMessages = [
        'kode' => [
            'required' => 'Kode jenis sampah harus diisi',
            'is_unique' => 'Kode jenis sampah sudah digunakan'
        ],
        'nama' => [
            'required' => 'Nama jenis sampah harus diisi'
        ],
        'level' => [
            'required' => 'Level harus diisi',
            'in_list' => 'Level harus 1, 2, atau 3'
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
     * Get kategori utama (level 1)
     */
    public function getKategoriUtama()
    {
        return $this->where('level', 1)
            ->where('status_aktif', 1)
            ->orderBy('urutan', 'ASC')
            ->findAll();
    }

    /**
     * Get area sampah berdasarkan parent (level 2)
     */
    public function getAreaSampah($parentId)
    {
        return $this->where('parent_id', $parentId)
            ->where('level', 2)
            ->where('status_aktif', 1)
            ->orderBy('urutan', 'ASC')
            ->findAll();
    }

    /**
     * Get detail sampah berdasarkan parent (level 3)
     */
    public function getDetailSampah($parentId)
    {
        return $this->where('parent_id', $parentId)
            ->where('level', 3)
            ->where('status_aktif', 1)
            ->orderBy('urutan', 'ASC')
            ->findAll();
    }

    /**
     * Get children by parent ID
     */
    public function getChildren($parentId)
    {
        return $this->where('parent_id', $parentId)
            ->where('status_aktif', 1)
            ->orderBy('urutan', 'ASC')
            ->findAll();
    }

    /**
     * Get full hierarchy tree
     */
    public function getHierarchyTree()
    {
        $categories = $this->where('status_aktif', 1)
            ->orderBy('level', 'ASC')
            ->orderBy('urutan', 'ASC')
            ->findAll();

        return $this->buildTree($categories);
    }

    /**
     * Build tree structure from flat array
     */
    private function buildTree($elements, $parentId = null)
    {
        $branch = [];

        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }

    /**
     * Get sampah organik structure for dropdown
     */
    public function getSampahOrganikStructure()
    {
        // Get organik category
        $organik = $this->where('kode', 'organik')->first();
        if (!$organik) {
            return [];
        }

        // Get areas (kantin, lingkungan kampus)
        $areas = $this->getAreaSampah($organik['id']);

        $structure = [];
        foreach ($areas as $area) {
            $area['details'] = $this->getDetailSampah($area['id']);
            $structure[] = $area;
        }

        return $structure;
    }

    /**
     * Validate hierarchy integrity
     */
    public function validateHierarchy($data)
    {
        // Check if parent exists when parent_id is provided
        if (!empty($data['parent_id'])) {
            $parent = $this->find($data['parent_id']);
            if (!$parent) {
                return ['error' => 'Parent tidak ditemukan'];
            }

            // Validate level consistency
            $expectedLevel = $parent['level'] + 1;
            if ($data['level'] != $expectedLevel) {
                return ['error' => "Level harus {$expectedLevel} untuk parent ini"];
            }
        } else {
            // Root level should be 1
            if ($data['level'] != 1) {
                return ['error' => 'Root level harus 1'];
            }
        }

        return ['success' => true];
    }

    /**
     * Get breadcrumb path for an item
     */
    public function getBreadcrumb($id)
    {
        $item = $this->find($id);
        if (!$item) {
            return [];
        }

        $breadcrumb = [$item];

        while ($item['parent_id']) {
            $item = $this->find($item['parent_id']);
            if ($item) {
                array_unshift($breadcrumb, $item);
            } else {
                break;
            }
        }

        return $breadcrumb;
    }

    /**
     * Search jenis sampah
     */
    public function searchJenisSampah($keyword)
    {
        return $this->like('nama', $keyword)
            ->orLike('kode', $keyword)
            ->where('status_aktif', 1)
            ->orderBy('level', 'ASC')
            ->orderBy('urutan', 'ASC')
            ->findAll();
    }
}
