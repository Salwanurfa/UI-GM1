<?php

namespace App\Controllers\TPS;

use App\Controllers\BaseController;
use App\Services\TPS\WasteService;

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
            
            // Fallback: if categories empty, get directly from database
            if (empty($categories)) {
                log_message('warning', 'TPS Waste - Categories empty from pagination, trying direct query');
                $db = \Config\Database::connect();
                $query = $db->query("SELECT * FROM master_harga_sampah WHERE status_aktif = 1 ORDER BY jenis_sampah ASC LIMIT 5");
                $categories = $query->getResultArray();
                log_message('info', 'TPS Waste - Direct query got ' . count($categories) . ' categories');
            }
            
            if (empty($allCategories)) {
                log_message('warning', 'TPS Waste - All categories empty, trying direct query');
                $db = \Config\Database::connect();
                $query = $db->query("SELECT * FROM master_harga_sampah WHERE status_aktif = 1 ORDER BY jenis_sampah ASC");
                $allCategories = $query->getResultArray();
                log_message('info', 'TPS Waste - Direct query got ' . count($allCategories) . ' all categories');
            }
            
            // Get unit list for dropdown
            $unitModel = new \App\Models\UnitModel();
            $unitList = $unitModel->where('status_aktif', 1)
                                  ->orderBy('nama_unit', 'ASC')
                                  ->findAll();
            
            $viewData = [
                'title' => 'Manajemen Sampah TPS',
                'waste_list' => $data['waste_list'],
                'categories' => $categories, // Untuk cards (dengan pagination)
                'allCategories' => $allCategories, // Untuk dropdown form (semua data)
                'unitList' => $unitList, // Untuk dropdown unit
                'pagerHarga' => $pagerHarga,
                'tps_info' => $data['tps_info'],
                'stats' => $data['stats']
            ];

            return view('pengelola_tps/waste', $viewData);

        } catch (\Exception $e) {
            log_message('error', 'TPS Waste Error: ' . $e->getMessage());
            
            // Last resort: get categories directly
            $categories = [];
            try {
                $db = \Config\Database::connect();
                $query = $db->query("SELECT * FROM master_harga_sampah WHERE status_aktif = 1 ORDER BY jenis_sampah ASC LIMIT 5");
                $categories = $query->getResultArray();
            } catch (\Exception $dbError) {
                log_message('error', 'TPS Waste - Even direct query failed: ' . $dbError->getMessage());
            }
            
            return view('pengelola_tps/waste', [
                'title' => 'Manajemen Sampah TPS',
                'waste_list' => [],
                'categories' => $categories,
                'allCategories' => [], // Fallback kosong
                'pagerHarga' => null,
                'tps_info' => null,
                'stats' => [],
                'error' => 'Terjadi kesalahan saat memuat data sampah'
            ]);
        }
    }

    public function form()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            // Get all categories for dropdown
            $hargaModel = new \App\Models\HargaSampahModel();
            $allCategories = $hargaModel->where('status_aktif', 1)
                                       ->orderBy('jenis_sampah', 'ASC')
                                       ->findAll();
            
            // Get unit list for dropdown
            $unitModel = new \App\Models\UnitModel();
            $unitList = $unitModel->where('status_aktif', 1)
                                  ->orderBy('nama_unit', 'ASC')
                                  ->findAll();
            
            $viewData = [
                'title' => 'Form Input Data Sampah TPS',
                'allCategories' => $allCategories,
                'unitList' => $unitList
            ];

            return view('pengelola_tps/form', $viewData);

        } catch (\Exception $e) {
            log_message('error', 'TPS Waste Form Error: ' . $e->getMessage());
            return redirect()->to('/pengelola-tps/waste')->with('error', 'Terjadi kesalahan saat memuat form');
        }
    }

    public function save()
    {
        try {
            if (!$this->validateSession()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Session invalid']);
            }

            // Get POST data
            $postData = $this->request->getPost();
            
            // Handle file upload
            $buktiFotoPath = null;
            $file = $this->request->getFile('bukti_foto');
            
            if ($file && $file->isValid() && !$file->hasMoved()) {
                // Validate file
                $validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!in_array($file->getMimeType(), $validTypes)) {
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['success' => false, 'message' => 'File harus berformat JPG atau PNG']);
                    }
                    return redirect()->back()->withInput()->with('error', 'File harus berformat JPG atau PNG');
                }
                
                // Check file size (max 5MB)
                if ($file->getSize() > 5242880) {
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['success' => false, 'message' => 'Ukuran file maksimal 5MB']);
                    }
                    return redirect()->back()->withInput()->with('error', 'Ukuran file maksimal 5MB');
                }
                
                // Generate unique filename
                $newName = 'waste_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $file->getExtension();
                
                // Create upload directory if not exists
                $uploadPath = WRITEPATH . 'uploads/waste_photos/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                // Move file
                $file->move($uploadPath, $newName);
                $buktiFotoPath = 'waste_photos/' . $newName;
                
                log_message('info', 'TPS Waste Save - File uploaded: ' . $buktiFotoPath);
            } else {
                log_message('error', 'TPS Waste Save - File upload failed or not provided');
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Bukti foto harus diupload']);
                }
                return redirect()->back()->withInput()->with('error', 'Bukti foto harus diupload');
            }
            
            // Determine status based on action button
            $action = $postData['action'] ?? 'draft';
            $status = ($action === 'kirim') ? 'dikirim_ke_tps' : 'draft';
            
            // Prepare data for service
            $data = [
                'kategori_id' => $postData['jenis_sampah'] ?? null,
                'berat_kg' => $postData['jumlah'] ?? 0,
                'satuan' => $postData['satuan'] ?? 'kg',
                'tanggal_waktu' => $postData['tanggal_waktu'] ?? date('Y-m-d H:i:s'),
                'nama_pelapor' => $postData['nama_pelapor'] ?? '',
                'gedung_pelapor' => $postData['gedung_pelapor'] ?? '',
                'bukti_foto' => $buktiFotoPath,
                'unit_pengirim' => $postData['unit_pengirim'] ?? null,
                'catatan' => $postData['catatan'] ?? '',
                'status_action' => $status
            ];
            
            log_message('info', 'TPS Waste Save - Received data: ' . json_encode($data));
            
            $result = $this->wasteService->saveWaste($data);
            
            // Check if this is AJAX request
            if ($this->request->isAJAX()) {
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON($result);
            }
            
            // Regular form submission
            if ($result['success']) {
                return redirect()->to('/pengelola-tps/waste')->with('success', $result['message']);
            } else {
                return redirect()->back()->withInput()->with('error', $result['message']);
            }

        } catch (\Exception $e) {
            log_message('error', 'TPS Waste Save Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            if ($this->request->isAJAX()) {
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON([
                        'success' => false,
                        'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
                    ]);
            }
            
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data');
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
            
            // Check if waste belongs to this TPS
            $user = session()->get('user');
            if ($waste['unit_id'] != $user['unit_id']) {
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
            log_message('error', 'TPS Waste Get Error: ' . $e->getMessage());
            
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
        }
    }

    public function edit($id)
    {
        try {
            if (!$this->validateSession()) {
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Session invalid']);
            }

            log_message('info', 'TPS Waste Edit - ID: ' . $id . ', POST data: ' . json_encode($this->request->getPost()));

            // Get POST data
            $data = $this->request->getPost();
            
            // Validate
            if (empty($data['kategori_id'])) {
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Kategori sampah harus dipilih']);
            }
            
            if (empty($data['berat_kg']) || $data['berat_kg'] <= 0) {
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Berat harus diisi']);
            }
            
            // Get user
            $user = session()->get('user');
            
            // Get waste
            $wasteModel = new \App\Models\WasteModel();
            $waste = $wasteModel->find($id);
            
            if (!$waste) {
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
            }
            
            if ($waste['unit_id'] != $user['unit_id']) {
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Bukan milik TPS Anda']);
            }
            
            if (!in_array($waste['status'], ['draft', 'perlu_revisi'])) {
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Data sudah disubmit tidak dapat diedit']);
            }
            
            // Get category
            $hargaModel = new \App\Models\HargaSampahModel();
            $category = $hargaModel->find($data['kategori_id']);
            
            if (!$category) {
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Kategori tidak ditemukan']);
            }
            
            // Prepare update data
            $status = (isset($data['status_action']) && $data['status_action'] === 'kirim') ? 'dikirim' : 'draft';
            
            // berat_kg sudah dalam kg dari frontend (sudah dikonversi)
            $beratKg = $data['berat_kg'];
            $satuan = $data['satuan'] ?? 'kg';
            
            $updateData = [
                'berat_kg' => $beratKg,
                'jumlah' => $beratKg,
                'satuan' => $satuan,
                'jenis_sampah' => $category['jenis_sampah'],
                'kategori_sampah' => $category['dapat_dijual'] ? 'bisa_dijual' : 'tidak_dijual',
                'nilai_rupiah' => $category['dapat_dijual'] ? ($beratKg * $category['harga_per_satuan']) : 0,
                'status' => $status
            ];
            
            log_message('info', 'TPS Waste Edit - Update data: ' . json_encode($updateData));
            
            // Update
            $result = $wasteModel->update($id, $updateData);
            
            if ($result) {
                $message = $status === 'dikirim' ? 'Data berhasil diupdate dan dikirim' : 'Data berhasil diupdate sebagai draft';
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => true, 'message' => $message]);
            }
            
            return $this->response
                ->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Gagal mengupdate data']);

        } catch (\Exception $e) {
            log_message('error', 'TPS Waste Edit Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
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
            log_message('info', 'TPS Delete Waste - ID: ' . $id);
            
            if (!$this->validateSession()) {
                log_message('warning', 'TPS Delete Waste - Session invalid');
                return $this->response
                    ->setStatusCode(401)
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Session invalid']);
            }

            log_message('info', 'TPS Delete Waste - Calling service...');
            $result = $this->wasteService->deleteWaste($id);
            log_message('info', 'TPS Delete Waste - Result: ' . json_encode($result));
            
            return $this->response
                ->setContentType('application/json')
                ->setJSON($result);

        } catch (\Exception $e) {
            log_message('error', 'TPS Waste Delete Error: ' . $e->getMessage());
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
            log_message('error', 'TPS Waste Export Error: ' . $e->getMessage());
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
            log_message('error', 'TPS Waste Export PDF Error: ' . $e->getMessage());
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
            log_message('error', 'TPS Waste Export Excel Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat export Excel');
        }
    }

    private function validateSession(): bool
    {
        $session = session();
        $user = $session->get('user');
        
        return $session->get('isLoggedIn') && 
               isset($user['id'], $user['role'], $user['unit_id']) &&
               $user['role'] === 'pengelola_tps';
    }
}