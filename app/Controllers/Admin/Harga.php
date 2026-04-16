<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\Admin\HargaService;

class Harga extends BaseController
{
    protected $hargaService;

    public function __construct()
    {
        $this->hargaService = new HargaService();
    }

    public function index()
    {
        // DEBUG: Log to detect if method is called twice
        $requestId = uniqid('REQ-', true);
        log_message('critical', "=== HARGA INDEX CALLED === Request ID: {$requestId} | URL: " . current_url() . " | Method: " . $this->request->getMethod());
        
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            // Get statistics from DashboardService (single call)
            $dashboardService = new \App\Services\Admin\DashboardService();
            $statsData = $dashboardService->getManajemenSampahStats();
            
            // Get recent price changes DIRECTLY from HargaLogModel (bypass DashboardService)
            $recentChanges = [];
            try {
                $hargaLogModel = new \App\Models\HargaLogModel();
                $recentChanges = $hargaLogModel->getRecentChanges(5);
                log_message('critical', "HargaController: Recent changes retrieved - Count: " . count($recentChanges) . " | Data: " . json_encode($recentChanges));
            } catch (\Exception $logError) {
                log_message('error', 'HargaController: Failed to get recent changes: ' . $logError->getMessage());
            }
            
            // Standardize to 'statistics' for view compatibility
            $statistics = [
                'total' => $statsData['total_jenis_sampah'] ?? 0,
                'aktif' => $statsData['harga_aktif'] ?? 0,
                'bisa_dijual' => $statsData['bisa_dijual'] ?? 0,
                'perubahan_hari_ini' => $statsData['perubahan_count'] ?? 0,
                'perubahan_total' => $statsData['perubahan_total'] ?? 0
            ];
            
            // Get paginated harga list - TAMPILKAN SEMUA (aktif + nonaktif)
            $hargaModel = new \App\Models\HargaSampahModel();
            $perPage = 10; // 10 items per page
            
            // Filter berdasarkan status (dari query string)
            $statusFilter = $this->request->getGet('status');
            if ($statusFilter === 'aktif') {
                $hargaModel->where('status_aktif', 1);
            } elseif ($statusFilter === 'nonaktif') {
                $hargaModel->where('status_aktif', 0);
            }
            // Jika tidak ada filter atau 'semua', tampilkan semua
            
            $hargaList = $hargaModel->orderBy('status_aktif', 'DESC')->orderBy('jenis_sampah', 'ASC')->paginate($perPage);
            $pager = $hargaModel->pager;
            
            // Get categories for dropdown
            $categoryModel = new \App\Models\WasteCategoryModel();
            $categories = $categoryModel->getCategoriesForDropdown();
            
            log_message('critical', 'HargaController: Sending to view - recentChanges count: ' . count($recentChanges) . " | Request ID: {$requestId}");
            
            $viewData = [
                'title' => 'Manajemen Sampah',
                'hargaSampah' => $hargaList,
                'pager' => $pager,
                'statistics' => $statistics,
                'recentChanges' => $recentChanges, // Direct from HargaLogModel
                'recentChangesCount' => count($recentChanges),
                'categories' => $categories,
                'requestId' => $requestId // Add request ID to view
            ];

            log_message('critical', "HargaController: About to return view | Request ID: {$requestId}");
            return view('admin_pusat/manajemen_harga/index', $viewData);

        } catch (\Exception $e) {
            log_message('error', 'Admin Harga Error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            
            return view('admin_pusat/manajemen_harga/index', [
                'title' => 'Manajemen Sampah',
                'hargaSampah' => [],
                'pager' => null,
                'statistics' => [
                    'total' => 0,
                    'aktif' => 0,
                    'bisa_dijual' => 0,
                    'perubahan_hari_ini' => 0,
                    'perubahan_total' => 0
                ],
                'recentChanges' => [],
                'recentChangesCount' => 0,
                'categories' => [],
                'error' => 'Terjadi kesalahan saat memuat data harga: ' . $e->getMessage()
            ]);
        }
    }

    public function testSimple()
    {
        // DEBUG: Test simple view without complex layout
        $requestId = uniqid('TEST-', true);
        log_message('critical', "=== HARGA TEST SIMPLE CALLED === Request ID: {$requestId}");
        
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            // Get statistics
            $dashboardService = new \App\Services\Admin\DashboardService();
            $statsData = $dashboardService->getManajemenSampahStats();
            
            // Get recent changes
            $recentChanges = [];
            try {
                $hargaLogModel = new \App\Models\HargaLogModel();
                $recentChanges = $hargaLogModel->getRecentChanges(5);
                log_message('critical', "TEST SIMPLE: Recent changes count: " . count($recentChanges));
            } catch (\Exception $logError) {
                log_message('error', 'TEST SIMPLE: Failed to get recent changes: ' . $logError->getMessage());
            }
            
            $statistics = [
                'total' => $statsData['total_jenis_sampah'] ?? 0,
                'aktif' => $statsData['harga_aktif'] ?? 0,
                'bisa_dijual' => $statsData['bisa_dijual'] ?? 0,
                'perubahan_hari_ini' => $statsData['perubahan_count'] ?? 0
            ];
            
            $viewData = [
                'requestId' => $requestId,
                'statistics' => $statistics,
                'recentChanges' => $recentChanges
            ];

            log_message('critical', "TEST SIMPLE: About to return view | Request ID: {$requestId}");
            return view('admin_pusat/manajemen_harga/test_simple', $viewData);

        } catch (\Exception $e) {
            log_message('error', 'TEST SIMPLE Error: ' . $e->getMessage());
            
            return view('admin_pusat/manajemen_harga/test_simple', [
                'requestId' => $requestId,
                'statistics' => ['total' => 0, 'aktif' => 0, 'bisa_dijual' => 0, 'perubahan_hari_ini' => 0],
                'recentChanges' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    public function get($id)
    {
        try {
            if (!$this->validateSession()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
            }

            $hargaModel = new \App\Models\HargaSampahModel();
            $harga = $hargaModel->find($id);
            
            if (!$harga) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data harga tidak ditemukan'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $harga
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Admin Harga Get Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function store()
    {
        try {
            if (!$this->validateSession()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Session invalid']);
            }

            // Validate input
            $jenisSampah = $this->request->getPost('jenis_sampah');
            $namaJenis = $this->request->getPost('nama_jenis');
            $hargaPerSatuan = $this->request->getPost('harga_per_satuan');
            $satuan = $this->request->getPost('satuan');
            
            // Validasi field wajib
            if (empty($jenisSampah) || empty($namaJenis) || empty($satuan)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Semua field wajib diisi',
                    'errors' => [
                        'jenis_sampah' => empty($jenisSampah) ? 'Kategori sampah harus diisi' : null,
                        'nama_jenis' => empty($namaJenis) ? 'Jenis sampah (nama lengkap) harus diisi' : null,
                        'satuan' => empty($satuan) ? 'Satuan harus dipilih' : null
                    ]
                ]);
            }
            
            // Validasi harga jika dapat dijual
            $dapatDijual = $this->request->getPost('dapat_dijual') ? 1 : 0;
            if ($dapatDijual && (empty($hargaPerSatuan) || $hargaPerSatuan <= 0)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Harga per satuan harus diisi untuk sampah yang dapat dijual'
                ]);
            }
            
            // Check if nama_jenis already exists (untuk menghindari duplikasi)
            $hargaModel = new \App\Models\HargaSampahModel();
            $existing = $hargaModel->where('nama_jenis', $namaJenis)->first();
            
            if ($existing) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Nama jenis sampah "' . $namaJenis . '" sudah ada. Gunakan nama yang berbeda.'
                ]);
            }
            
            // Prepare data
            $data = [
                'jenis_sampah' => $jenisSampah,
                'nama_jenis' => $namaJenis,
                'harga_per_satuan' => $hargaPerSatuan ?? 0,
                'satuan' => $satuan,
                'dapat_dijual' => $this->request->getPost('dapat_dijual') ? 1 : 0,
                'status_aktif' => $this->request->getPost('status_aktif') ? 1 : 0,
                'deskripsi' => $this->request->getPost('deskripsi') ?? ''
            ];
            
            // Log data yang akan diinsert
            log_message('info', 'Attempting to insert jenis sampah: ' . json_encode($data));
            
            // Insert (timestamps akan otomatis ditambahkan oleh model)
            $insertResult = $hargaModel->insert($data);
            
            // Log hasil insert
            log_message('info', 'Insert result: ' . ($insertResult ? 'SUCCESS (ID: ' . $hargaModel->getInsertID() . ')' : 'FAILED'));
            
            if ($insertResult) {
                $newId = $hargaModel->getInsertID();
                log_message('info', 'New jenis sampah ID: ' . $newId);
                
                // Log the creation
                try {
                    $logModel = new \App\Models\HargaLogModel();
                    $session = session();
                    $user = $session->get('user');
                    
                    $logModel->logPriceChange(
                        $newId,
                        $jenisSampah,
                        0,
                        $hargaPerSatuan,
                        $user['id'] ?? 0,
                        'Jenis sampah baru ditambahkan: ' . $namaJenis
                    );
                } catch (\Exception $logError) {
                    log_message('error', 'Failed to save creation log: ' . $logError->getMessage());
                }
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Jenis sampah berhasil ditambahkan',
                    'data' => [
                        'id' => $newId,
                        'jenis_sampah' => $jenisSampah,
                        'nama_jenis' => $namaJenis
                    ]
                ]);
            }
            
            // Get validation errors if any
            $errors = $hargaModel->errors();
            $errorMessage = 'Gagal menambahkan jenis sampah';
            
            if (!empty($errors)) {
                $errorMessage .= ': ' . implode(', ', $errors);
                log_message('error', 'Validation errors: ' . json_encode($errors));
            } else {
                log_message('error', 'Insert failed but no validation errors. Check database constraints.');
            }
            
            return $this->response->setJSON([
                'success' => false,
                'message' => $errorMessage,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Admin Harga Store Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ]);
        }
    }
    /**
     * Store new waste category
     */
    public function storeCategory()
    {
        try {
            if (!$this->validateSession()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Session invalid']);
            }

            // Validate input
            $kategoriUtama = trim($this->request->getPost('kategori_utama'));

            if (empty($kategoriUtama)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Nama kategori harus diisi'
                ]);
            }

            // Check if category already exists
            $categoryModel = new \App\Models\WasteCategoryModel();
            if ($categoryModel->categoryExists($kategoriUtama)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kategori "' . $kategoriUtama . '" sudah ada. Gunakan nama yang berbeda.'
                ]);
            }

            // Prepare data
            $data = [
                'kategori_utama' => $kategoriUtama,
                'sub_kategori' => $kategoriUtama, // Default sub kategori sama dengan kategori utama
                'deskripsi' => 'Kategori ' . $kategoriUtama,
                'status_aktif' => 1
            ];

            // Insert category
            $insertResult = $categoryModel->insert($data);

            if ($insertResult) {
                $newId = $categoryModel->getInsertID();

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Kategori sampah berhasil ditambahkan',
                    'data' => [
                        'id' => $newId,
                        'kategori_utama' => $kategoriUtama
                    ]
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menambahkan kategori sampah'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Store Category Error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan kategori: ' . $e->getMessage()
            ]);
        }
    }
    /**
     * Update waste category
     */
    public function updateCategory($id)
    {
        try {
            if (!$this->validateSession()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Session invalid']);
            }

            // Validate input
            $kategoriUtama = trim($this->request->getPost('kategori_utama'));

            if (empty($kategoriUtama)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Nama kategori harus diisi'
                ]);
            }

            $categoryModel = new \App\Models\WasteCategoryModel();

            // Check if category exists
            $category = $categoryModel->find($id);
            if (!$category) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kategori tidak ditemukan'
                ]);
            }

            // Check if new name already exists (excluding current category)
            if ($categoryModel->categoryExists($kategoriUtama, $id)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kategori "' . $kategoriUtama . '" sudah ada. Gunakan nama yang berbeda.'
                ]);
            }

            // Update category
            $updateData = [
                'kategori_utama' => $kategoriUtama,
                'sub_kategori' => $kategoriUtama,
                'deskripsi' => 'Kategori ' . $kategoriUtama
            ];

            $updateResult = $categoryModel->update($id, $updateData);

            if ($updateResult) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Kategori berhasil diperbarui',
                    'data' => [
                        'id' => $id,
                        'kategori_utama' => $kategoriUtama
                    ]
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memperbarui kategori'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Update Category Error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui kategori: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete waste category
     */
    public function deleteCategory($id)
    {
        try {
            if (!$this->validateSession()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Session invalid']);
            }

            $categoryModel = new \App\Models\WasteCategoryModel();

            // Check if category exists
            $category = $categoryModel->find($id);
            if (!$category) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kategori tidak ditemukan'
                ]);
            }

            // Check if category is being used
            if ($categoryModel->isUsedInWasteTypesAlternative($id)) {
                $usageCount = $categoryModel->getUsageCountAlternative($id);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kategori "' . $category['kategori_utama'] . '" tidak dapat dihapus karena masih digunakan oleh ' . $usageCount . ' jenis sampah. Hapus atau ubah jenis sampah tersebut terlebih dahulu.'
                ]);
            }

            // Delete category
            $deleteResult = $categoryModel->delete($id);

            if ($deleteResult) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Kategori "' . $category['kategori_utama'] . '" berhasil dihapus'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus kategori'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Delete Category Error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus kategori: ' . $e->getMessage()
            ]);
        }
    }
    /**
     * Seed standard waste categories (for development/setup)
     */
    public function seedCategories()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            $seeder = new \App\Database\Seeds\WasteCategorySeeder();
            $seeder->run();

            return redirect()->to('/admin-pusat/manajemen-harga')->with('success', 'Kategori standar berhasil ditambahkan');

        } catch (\Exception $e) {
            log_message('error', 'Seed Categories Error: ' . $e->getMessage());

            return redirect()->to('/admin-pusat/manajemen-harga')->with('error', 'Gagal menambahkan kategori standar: ' . $e->getMessage());
        }
    }
    /**
     * Fix database collation issues
     */
    public function fixCollation()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            $db = \Config\Database::connect();

            // Run the collation fix queries
            $queries = [
                "ALTER TABLE waste_categories CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci",
                "ALTER TABLE master_harga_sampah CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci"
            ];

            $success = true;
            $messages = [];

            foreach ($queries as $query) {
                try {
                    $db->query($query);
                    $tableName = $this->extractTableName($query);
                    $messages[] = "Fixed collation for table: {$tableName}";
                } catch (\Exception $e) {
                    $success = false;
                    $tableName = $this->extractTableName($query);
                    $messages[] = "Error fixing {$tableName}: " . $e->getMessage();
                    log_message('error', 'Collation Fix Error for ' . $tableName . ': ' . $e->getMessage());
                }
            }

            // Also try to fix other related tables
            $additionalTables = ['waste_management', 'limbah_b3', 'users'];
            foreach ($additionalTables as $table) {
                if ($db->tableExists($table)) {
                    try {
                        $db->query("ALTER TABLE {$table} CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
                        $messages[] = "Fixed collation for table: {$table}";
                    } catch (\Exception $e) {
                        $messages[] = "Warning for {$table}: " . $e->getMessage();
                    }
                }
            }

            $message = implode('<br>', $messages);

            if ($success) {
                return redirect()->to('/admin-pusat/manajemen-harga')->with('success', 'Collation berhasil diperbaiki:<br>' . $message);
            } else {
                return redirect()->to('/admin-pusat/manajemen-harga')->with('error', 'Beberapa tabel gagal diperbaiki:<br>' . $message);
            }

        } catch (\Exception $e) {
            log_message('error', 'Fix Collation Error: ' . $e->getMessage());

            return redirect()->to('/admin-pusat/manajemen-harga')->with('error', 'Gagal memperbaiki collation: ' . $e->getMessage());
        }
    }

    /**
     * Extract table name from ALTER TABLE query
     */
    private function extractTableName($query)
    {
        if (preg_match('/ALTER TABLE\s+(\w+)/', $query, $matches)) {
            return $matches[1];
        }
        return 'unknown';
    }

    /**
     * Debug collation issues
     */
    public function debugCollation()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            $db = \Config\Database::connect();
            
            // Check current collations
            $tables = ['waste_categories', 'master_harga_sampah', 'waste_management', 'users'];
            $collationInfo = [];
            
            foreach ($tables as $table) {
                if ($db->tableExists($table)) {
                    try {
                        $query = $db->query("SHOW TABLE STATUS LIKE '{$table}'");
                        $result = $query->getRow();
                        $collationInfo[$table] = [
                            'collation' => $result->Collation ?? 'Unknown',
                            'exists' => true
                        ];
                        
                    } catch (\Exception $e) {
                        $collationInfo[$table] = [
                            'error' => $e->getMessage(),
                            'exists' => false
                        ];
                    }
                } else {
                    $collationInfo[$table] = [
                        'exists' => false,
                        'message' => 'Table does not exist'
                    ];
                }
            }
            
            // Test the problematic query
            $testResults = [];
            try {
                $categoryModel = new \App\Models\WasteCategoryModel();
                $categories = $categoryModel->findAll();
                
                foreach ($categories as $category) {
                    try {
                        $isUsed = $categoryModel->isUsedInWasteTypesAlternative($category['id']);
                        $count = $categoryModel->getUsageCountAlternative($category['id']);
                        
                        $testResults[] = [
                            'category' => $category['kategori_utama'],
                            'is_used' => $isUsed,
                            'count' => $count,
                            'status' => 'OK'
                        ];
                    } catch (\Exception $e) {
                        $testResults[] = [
                            'category' => $category['kategori_utama'],
                            'error' => $e->getMessage(),
                            'status' => 'ERROR'
                        ];
                    }
                }
            } catch (\Exception $e) {
                $testResults = ['error' => $e->getMessage()];
            }
            
            // Return debug information
            return $this->response->setJSON([
                'collation_info' => $collationInfo,
                'test_results' => $testResults,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Debug Collation Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'error' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * Get category data for editing
     */
    public function getCategory($id)
    {
        try {
            if (!$this->validateSession()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Session invalid']);
            }

            $categoryModel = new \App\Models\WasteCategoryModel();
            $category = $categoryModel->find($id);

            if ($category) {
                return $this->response->setJSON([
                    'success' => true,
                    'data' => $category
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Get Category Error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data kategori'
            ]);
        }
    }
    /**
     * Get categories as JSON for AJAX requests
     */
    public function getCategories()
    {
        try {
            if (!$this->validateSession()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Session invalid']);
            }

            $categoryModel = new \App\Models\WasteCategoryModel();
            $categories = $categoryModel->findAll();

            return $this->response->setJSON($categories);

        } catch (\Exception $e) {
            log_message('error', 'Get Categories Error: ' . $e->getMessage());

            return $this->response->setJSON([]);
        }
    }


    public function update($id)
    {
        try {
            if (!$this->validateSession()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Session invalid']);
            }

            $hargaModel = new \App\Models\HargaSampahModel();
            
            // Get current data
            $currentData = $hargaModel->find($id);
            if (!$currentData) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data harga tidak ditemukan'
                ]);
            }
            
            // Set timezone to Asia/Jakarta
            date_default_timezone_set('Asia/Jakarta');
            
            // Prepare update data
            $data = [
                'jenis_sampah' => $this->request->getPost('jenis_sampah'),
                'nama_jenis' => $this->request->getPost('nama_jenis'),
                'harga_per_satuan' => $this->request->getPost('harga_per_satuan'),
                'satuan' => $this->request->getPost('satuan'),
                'dapat_dijual' => $this->request->getPost('dapat_dijual') ? 1 : 0,
                'status_aktif' => $this->request->getPost('status_aktif') ? 1 : 0,
                'deskripsi' => $this->request->getPost('deskripsi'),
                'updated_at' => date('Y-m-d H:i:s') // Force correct timezone
            ];
            
            // Update
            if ($hargaModel->update($id, $data)) {
                // Save log if price changed
                $oldPrice = $currentData['harga_per_satuan'];
                $newPrice = $data['harga_per_satuan'];
                
                if ($oldPrice != $newPrice) {
                    try {
                        $logModel = new \App\Models\HargaLogModel();
                        $session = session();
                        $user = $session->get('user');
                        
                        // Use model's logPriceChange method
                        $logModel->logPriceChange(
                            $id,
                            $data['jenis_sampah'],
                            $oldPrice,
                            $newPrice,
                            $user['id'] ?? 0,
                            'Update harga dari Rp ' . number_format($oldPrice, 0, ',', '.') . ' ke Rp ' . number_format($newPrice, 0, ',', '.')
                        );
                        
                        log_message('info', "Price change logged: {$data['jenis_sampah']} from {$oldPrice} to {$newPrice}");
                    } catch (\Exception $logError) {
                        log_message('error', 'Failed to save price change log: ' . $logError->getMessage());
                    }
                }
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Harga berhasil diupdate'
                ]);
            }
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengupdate harga'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Admin Harga Update Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate data: ' . $e->getMessage()
            ]);
        }
    }

    public function toggleStatus($id)
    {
        try {
            if (!$this->validateSession()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Session invalid']);
            }

            $result = $this->hargaService->toggleStatus($id);
            
            return $this->response->setJSON($result);

        } catch (\Exception $e) {
            log_message('error', 'Admin Harga Toggle Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah status'
            ]);
        }
    }

    public function delete($id)
    {
        try {
            if (!$this->validateSession()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Session invalid']);
            }

            $result = $this->hargaService->deleteHarga($id);
            
            return $this->response->setJSON($result);

        } catch (\Exception $e) {
            log_message('error', 'Admin Harga Delete Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data'
            ]);
        }
    }

    public function logs()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            // Get logs from ChangeLogModel
            $changeLogModel = new \App\Models\ChangeLogModel();
            $logs = $changeLogModel->getByEntity('harga_sampah', null, 100);
            
            // Get statistics
            $stats = $changeLogModel->getStatistics();
            
            $viewData = [
                'title' => 'Log Perubahan Harga',
                'logs' => $logs,
                'stats' => $stats
            ];

            return view('admin_pusat/manajemen_harga/logs', $viewData);

        } catch (\Exception $e) {
            log_message('error', 'Admin Harga Logs Error: ' . $e->getMessage());
            
            return view('admin_pusat/manajemen_harga/logs', [
                'title' => 'Log Perubahan Harga',
                'logs' => [],
                'stats' => [
                    'total' => 0,
                    'today' => 0,
                    'this_week' => 0,
                    'this_month' => 0
                ],
                'error' => 'Terjadi kesalahan saat memuat log'
            ]);
        }
    }

    /**
     * Search all harga sampah (for AJAX search - no pagination)
     */
    public function search()
    {
        try {
            if (!$this->validateSession()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
            }

            $searchTerm = $this->request->getGet('q');
            $statusFilter = $this->request->getGet('status');
            
            if (empty($searchTerm)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Search term is required'
                ]);
            }

            $hargaModel = new \App\Models\HargaSampahModel();
            
            // Apply status filter if provided
            if ($statusFilter === 'aktif') {
                $hargaModel->where('status_aktif', 1);
            } elseif ($statusFilter === 'nonaktif') {
                $hargaModel->where('status_aktif', 0);
            }
            
            // Search in multiple columns
            $results = $hargaModel
                ->groupStart()
                    ->like('jenis_sampah', $searchTerm)
                    ->orLike('nama_jenis', $searchTerm)
                    ->orLike('harga_per_satuan', $searchTerm)
                ->groupEnd()
                ->orderBy('status_aktif', 'DESC')
                ->orderBy('jenis_sampah', 'ASC')
                ->findAll(); // Get ALL results, no pagination

            return $this->response->setJSON([
                'success' => true,
                'count' => count($results),
                'results' => $results
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Admin Harga Search Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari data'
            ]);
        }
    }

    /**
     * Get recent changes as JSON (for AJAX polling)
     */
    public function recentChanges()
    {
        try {
            if (!$this->validateSession()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
            }

            $changeLogModel = new \App\Models\ChangeLogModel();
            $recentChanges = $changeLogModel->getByEntity('harga_sampah', null, 5);
            $stats = $changeLogModel->getStatistics();

            return $this->response->setJSON([
                'success' => true,
                'count' => count($recentChanges),
                'today_count' => $stats['today'] ?? 0,
                'total_count' => $stats['total'] ?? 0,
                'changes' => $recentChanges
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Admin Harga Recent Changes Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data'
            ]);
        }
    }

    private function validateSession(): bool
    {
        $session = session();
        $user = $session->get('user');
        
        return $session->get('isLoggedIn') && 
               isset($user['role']) &&
               in_array($user['role'], ['admin_pusat', 'super_admin']);
    }
}