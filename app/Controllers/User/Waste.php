<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Services\User\WasteService;

class Waste extends BaseController
{
    protected $wasteService;

    public function __construct()
    {
        $this->wasteService = new WasteService();
    }

    public function index()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            $data = $this->wasteService->getWasteData();
            
            $hargaModel = new \App\Models\HargaSampahModel();
            
            // Pagination untuk categories (Informasi Harga Sampah - cards display)
            $perPage = 5; // Maksimal 5 cards per halaman
            $categories = $hargaModel->where('status_aktif', 1)
                                    ->orderBy('jenis_sampah', 'ASC')
                                    ->paginate($perPage, 'harga');
            $pagerHarga = $hargaModel->pager;
            
            // Semua categories untuk dropdown form (tidak pakai pagination)
            $allCategories = $hargaModel->where('status_aktif', 1)
                                       ->orderBy('jenis_sampah', 'ASC')
                                       ->findAll();
            
            // DEBUG: Log pagination info
            log_message('info', 'User Waste - Categories (paginated): ' . count($categories));
            log_message('info', 'User Waste - All Categories (dropdown): ' . count($allCategories));
            log_message('info', 'User Waste - Pager exists: ' . (isset($pagerHarga) ? 'YES' : 'NO'));
            if (isset($pagerHarga)) {
                log_message('info', 'User Waste - Page count: ' . $pagerHarga->getPageCount('harga'));
                log_message('info', 'User Waste - Current page: ' . $pagerHarga->getCurrentPage('harga'));
                log_message('info', 'User Waste - Total: ' . $pagerHarga->getTotal('harga'));
            }
            
            $viewData = [
                'title' => 'Manajemen Sampah User',
                'user' => $data['user'],
                'unit' => $data['unit'],
                'waste_list' => $data['waste_list'],
                'categories' => $categories, // Untuk cards (dengan pagination)
                'allCategories' => $allCategories, // Untuk dropdown form (semua data)
                'pagerHarga' => $pagerHarga,
                'stats' => $data['stats'],
                'recent_activities' => $data['recent_activities'] ?? []
            ];

            return view('user/waste', $viewData);

        } catch (\Exception $e) {
            log_message('error', 'User Waste Error: ' . $e->getMessage());
            
            // Last resort: get categories directly
            $categories = [];
            try {
                $db = \Config\Database::connect();
                $query = $db->query("SELECT * FROM master_harga_sampah WHERE status_aktif = 1 ORDER BY jenis_sampah ASC LIMIT 5");
                $categories = $query->getResultArray();
            } catch (\Exception $dbError) {
                log_message('error', 'User Waste - Even direct query failed: ' . $dbError->getMessage());
            }
            
            return view('user/waste', [
                'title' => 'Manajemen Sampah User',
                'user' => session()->get('user'),
                'unit' => null,
                'waste_list' => [],
                'categories' => $categories,
                'allCategories' => [], // Fallback kosong
                'pagerHarga' => null,
                'stats' => [],
                'error' => 'Terjadi kesalahan saat memuat data sampah'
            ]);
        }
    }

    public function get($id)
    {
        try {
            if (!$this->validateSession()) {
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Unauthorized']);
            }

            $wasteModel = new \App\Models\WasteModel();
            $waste = $wasteModel->find($id);
            
            if (!$waste) {
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON([
                        'success' => false,
                        'message' => 'Data tidak ditemukan'
                    ]);
            }
            
            // Check if user owns this data or has permission
            $user = session()->get('user');
            $canEdit = false;
            
            // User can edit their own data
            if (isset($waste['user_id']) && $waste['user_id'] == $user['id']) {
                $canEdit = true;
            }
            
            // User from same unit can edit
            if (isset($waste['unit_id']) && isset($user['unit_id']) && $waste['unit_id'] == $user['unit_id']) {
                $canEdit = true;
            }
            
            // Admin can edit all
            if (in_array($user['role'], ['admin_pusat', 'super_admin'])) {
                $canEdit = true;
            }
            
            if (!$canEdit) {
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses ke data ini'
                    ]);
            }

            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'success' => true,
                    'data' => $waste
                ]);

        } catch (\Exception $e) {
            log_message('error', 'User Waste Get Error: ' . $e->getMessage());
            
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
        }
    }

    public function save()
    {
        try {
            log_message('info', '=== User Waste Save START ===');
            
            if (!$this->validateSession()) {
                log_message('warning', 'User Waste Save - Session invalid');
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Session invalid']);
            }

            $postData = $this->request->getPost();
            log_message('info', 'User Waste Save - POST data: ' . json_encode($postData));
            log_message('info', 'User Waste Save - User: ' . json_encode(session()->get('user')));
            
            $result = $this->wasteService->saveWaste($postData);
            
            log_message('info', 'User Waste Save - Result: ' . json_encode($result));
            
            return $this->response
                ->setContentType('application/json')
                ->setJSON($result);

        } catch (\Exception $e) {
            log_message('error', 'User Waste Save Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
                ]);
        }
    }

    public function edit($id)
    {
        try {
            log_message('info', '=== User Waste Edit START ===');
            log_message('info', 'ID: ' . $id);
            log_message('info', 'POST: ' . json_encode($this->request->getPost()));
            
            if (!$this->validateSession()) {
                log_message('warning', 'Session invalid');
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Session invalid']);
            }

            // Get POST data
            $data = $this->request->getPost();
            
            // Validate
            if (empty($data['kategori_id'])) {
                log_message('warning', 'kategori_id empty');
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Kategori sampah harus dipilih']);
            }
            
            if (empty($data['berat_kg']) || $data['berat_kg'] <= 0) {
                log_message('warning', 'berat_kg invalid: ' . ($data['berat_kg'] ?? 'null'));
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Jumlah/berat harus diisi']);
            }
            
            // Get user
            $user = session()->get('user');
            log_message('info', 'User ID: ' . ($user['id'] ?? 'null'));
            
            // Get waste
            $wasteModel = new \App\Models\WasteModel();
            $waste = $wasteModel->find($id);
            log_message('info', 'Waste found: ' . ($waste ? 'yes' : 'no'));
            
            if (!$waste) {
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
            }
            
            if ($waste['unit_id'] != $user['unit_id']) {
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Bukan milik unit Anda']);
            }
            
            if (!in_array($waste['status'], ['draft', 'perlu_revisi', 'ditolak_tps'])) {
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Data sudah disubmit tidak dapat diedit']);
            }
            
            // Get category
            $hargaModel = new \App\Models\HargaSampahModel();
            $category = $hargaModel->find($data['kategori_id']);
            log_message('info', 'Category found: ' . ($category ? 'yes' : 'no'));
            
            if (!$category) {
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Kategori tidak ditemukan']);
            }
            
            // Prepare update data
            // If editing rejected data and sending again, send back to TPS
            $status = 'draft';
            if (isset($data['status_action']) && $data['status_action'] === 'kirim') {
                if ($waste['status'] === 'ditolak_tps') {
                    $status = 'dikirim_ke_tps';
                } else {
                    $status = 'dikirim';
                }
            }
            
            // berat_kg sudah dalam kg dari frontend (sudah dikonversi)
            $beratKg = $data['berat_kg'];
            $satuan = $data['satuan'] ?? $waste['satuan'] ?? 'kg';
            
            $updateData = [
                'berat_kg' => $beratKg,
                'jumlah' => $beratKg,
                'satuan' => $satuan,
                'jenis_sampah' => $category['jenis_sampah'],
                'kategori_sampah' => $category['dapat_dijual'] ? 'bisa_dijual' : 'tidak_dijual',
                'nilai_rupiah' => $category['dapat_dijual'] ? ($beratKg * $category['harga_per_satuan']) : 0,
                'status' => $status
            ];
            
            log_message('info', 'Update data: ' . json_encode($updateData));
            
            // Update
            $result = $wasteModel->update($id, $updateData);
            log_message('info', 'Update result: ' . ($result ? 'success' : 'failed'));
            
            if ($result) {
                $message = $status === 'dikirim_ke_tps' 
                    ? 'Data berhasil diupdate dan dikirim ulang ke TPS' 
                    : ($status === 'dikirim' 
                        ? 'Data berhasil diupdate dan dikirim' 
                        : 'Data berhasil diupdate sebagai draft');
                log_message('info', '=== User Waste Edit SUCCESS ===');
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => true, 'message' => $message]);
            }
            
            log_message('error', '=== User Waste Edit FAILED ===');
            return $this->response
                ->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Gagal mengupdate data']);

        } catch (\Throwable $e) {
            log_message('error', '=== User Waste Edit EXCEPTION ===');
            log_message('error', 'Message: ' . $e->getMessage());
            log_message('error', 'File: ' . $e->getFile() . ':' . $e->getLine());
            log_message('error', 'Trace: ' . $e->getTraceAsString());
            
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
        }
    }

    public function delete($id)
    {
        try {
            log_message('info', 'User Delete Waste - ID: ' . $id);
            
            if (!$this->validateSession()) {
                log_message('warning', 'User Delete Waste - Session invalid');
                return $this->response
                    ->setStatusCode(401)
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Session invalid']);
            }

            log_message('info', 'User Delete Waste - Calling service...');
            $result = $this->wasteService->deleteWaste($id);
            log_message('info', 'User Delete Waste - Result: ' . json_encode($result));
            
            return $this->response
                ->setContentType('application/json')
                ->setJSON($result);

        } catch (\Exception $e) {
            log_message('error', 'User Waste Delete Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return $this->response
                ->setStatusCode(500)
                ->setContentType('application/json')
                ->setJSON([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
                ]);
        }
    }

    public function export()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            $result = $this->wasteService->exportWaste();
            
            if ($result['success']) {
                return $this->response->download($result['file_path'], null)->setFileName($result['filename']);
            }

            return redirect()->back()->with('error', $result['message']);

        } catch (\Exception $e) {
            log_message('error', 'User Waste Export Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat export data');
        }
    }
    
    public function exportPdf()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            $result = $this->wasteService->exportPdf();
            
            if ($result['success']) {
                return $this->response->download($result['file_path'], null)->setFileName($result['filename']);
            }

            return redirect()->back()->with('error', $result['message']);

        } catch (\Exception $e) {
            log_message('error', 'User Waste Export PDF Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat export PDF');
        }
    }

    public function exportExcel()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            helper('excel');
            $this->wasteService->exportExcel();

        } catch (\Exception $e) {
            log_message('error', 'User Waste Export Excel Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat export Excel');
        }
    }

    private function validateSession(): bool
    {
        $session = session();
        $user = $session->get('user');
        
        return $session->get('isLoggedIn') && 
               isset($user['id'], $user['role'], $user['unit_id']) &&
               $user['role'] === 'user';
    }
}