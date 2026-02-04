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
            
            log_message('critical', 'HargaController: Sending to view - recentChanges count: ' . count($recentChanges) . " | Request ID: {$requestId}");
            
            $viewData = [
                'title' => 'Manajemen Sampah',
                'hargaSampah' => $hargaList,
                'pager' => $pager,
                'statistics' => $statistics,
                'recentChanges' => $recentChanges, // Direct from HargaLogModel
                'recentChangesCount' => count($recentChanges),
                'categories' => [],
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