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

            // Debug: Cek data langsung dari model
            $wasteModel = new \App\Models\WasteModel();
            $user = session()->get('user');
            
            log_message('info', 'TPS Waste Index - User session: ' . json_encode($user));
            
            // Ambil semua data untuk debugging
            $allWasteData = $wasteModel->orderBy('id', 'DESC')->findAll();
            log_message('info', 'TPS Waste Index - Total waste records: ' . count($allWasteData));
            
            // Ambil data dengan filter unit_id
            $userUnitId = $user['unit_id'] ?? null;
            $filteredWasteData = [];
            if ($userUnitId) {
                $filteredWasteData = $wasteModel->where('unit_id', $userUnitId)->orderBy('id', 'DESC')->findAll();
                log_message('info', 'TPS Waste Index - Filtered waste records for unit_id ' . $userUnitId . ': ' . count($filteredWasteData));
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
                return $this->response->setJSON(['status' => 'error', 'message' => 'Session invalid']);
            }

            // Get session user data
            $user = session()->get('user');
            if (!$user || !isset($user['id'])) {
                log_message('error', 'TPS Waste Save - No user in session');
                return $this->response->setJSON(['status' => 'error', 'message' => 'User session tidak valid']);
            }

            // 1. Ambil File Foto
            $fileFoto = $this->request->getFile('foto');
            
            // 2. Validasi: Pastikan Foto Diunggah (Sesuai Peringatan di Gambar)
            if ($fileFoto && $fileFoto->isValid() && !$fileFoto->hasMoved()) {
                // Validate file type
                $validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!in_array($fileFoto->getMimeType(), $validTypes)) {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'File harus berformat JPG atau PNG']);
                }
                
                // Check file size (max 5MB)
                if ($fileFoto->getSize() > 5242880) {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'Ukuran file maksimal 5MB']);
                }
                
                $newName = $fileFoto->getRandomName();
                
                // Pastikan folder uploads/waste/ ada
                $uploadPath = 'uploads/waste/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                $fileFoto->move($uploadPath, $newName);
            } else {
                // Jika foto gagal/tidak ada, kirim pesan error spesifik
                return $this->response->setJSON(['status' => 'error', 'message' => 'Foto bukti wajib diupload!']);
            }

            // 3. Siapkan Data (Samakan dengan kolom Database uigm_polban)
            $data = [
                'user_id' => $user['id'],
                'unit_id' => $user['unit_id'] ?? $user['id'], // Pastikan unit_id terisi
                'jenis_sampah' => $this->request->getPost('jenis_sampah') ?? 'Anorganik', // Default value
                'nama_sampah' => $this->request->getPost('jenis_sampah') ?? 'Anorganik',
                'jumlah' => $this->request->getPost('jumlah'),
                'satuan' => $this->request->getPost('satuan') ?? 'kg',
                'harga' => $this->request->getPost('harga') ?? 0,
                'total_nilai' => $this->request->getPost('total_nilai') ?? 0,
                'nilai_rupiah' => $this->request->getPost('total_nilai') ?? 0, // Kolom database yang benar
                'tanggal' => $this->request->getPost('tanggal_waktu') ?? date('Y-m-d H:i:s'),
                'gedung' => $this->request->getPost('gedung') ?? 'TPS Pusat', // Default value
                'nama_pelapor' => $this->request->getPost('nama_pelapor') ?? 'Pengelola TPS',
                'gedung_pelapor' => $this->request->getPost('gedung_pelapor') ?? 'TPS Pusat',
                'foto_bukti' => $newName, // Simpan nama filenya saja
                'bukti_foto' => $newName, // Untuk kompatibilitas
                'catatan_admin' => $this->request->getPost('catatan') ?? '',
                'status' => 'dikirim', // Status yang dikenali Admin
                'action_timestamp' => date('Y-m-d H:i:s')
            ];

            // Debug logging untuk memastikan data terkirim
            log_message('info', 'TPS Waste Save - User session: ' . json_encode($user));
            log_message('info', 'TPS Waste Save - POST data received: ' . json_encode($this->request->getPost()));
            log_message('info', 'TPS Waste Save - Data prepared: ' . json_encode($data));

            // 4. Proses Simpan dengan Error Reporting
            $model = new \App\Models\WasteModel();
            if ($model->save($data)) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Data berhasil disimpan']);
            } else {
                // Tampilkan error database asli jika gagal
                return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal ke Database: ' . implode(', ', $model->errors())]);
            }

        } catch (\Exception $e) {
            log_message('error', 'TPS Waste Save Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
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

    public function testDatabase()
    {
        try {
            $wasteModel = new \App\Models\WasteModel();
            
            // Test database connection
            $db = \Config\Database::connect();
            $query = $db->query("SELECT 1 as test");
            $result = $query->getRow();
            
            if (!$result) {
                return $this->response->setJSON(['success' => false, 'message' => 'Database connection failed']);
            }
            
            // Test table structure
            $tableQuery = $db->query("DESCRIBE waste_management");
            $columns = $tableQuery->getResultArray();
            
            // Test user session
            $user = session()->get('user');
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Database test successful',
                'data' => [
                    'database_connected' => true,
                    'table_columns' => array_column($columns, 'Field'),
                    'user_session' => $user ? 'Valid' : 'Invalid',
                    'user_id' => $user['id'] ?? 'Not set',
                    'upload_path' => WRITEPATH . 'uploads/waste_photos/',
                    'upload_path_exists' => is_dir(WRITEPATH . 'uploads/waste_photos/'),
                    'upload_path_writable' => is_writable(WRITEPATH . 'uploads/waste_photos/')
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database test failed: ' . $e->getMessage()
            ]);
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