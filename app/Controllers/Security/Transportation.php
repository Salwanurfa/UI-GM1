<?php

namespace App\Controllers\Security;

use App\Controllers\BaseController;
use App\Models\TransportStatsModel;

class Transportation extends BaseController
{
    protected $transportStatsModel;

    public function __construct()
    {
        // Check if user is logged in and has security role
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        
        $userRole = session()->get('user')['role'] ?? null;
        if ($userRole !== 'security') {
            return redirect()->to('/auth/login')->with('error', 'Akses ditolak');
        }

        $this->transportStatsModel = new TransportStatsModel();
    }

    /**
     * Transportation aggregate input form
     */
    public function index()
    {
        $userId = session()->get('user')['id'];
        $db = \Config\Database::connect();
        
        // Get categories and fuels from database
        $categories = $db->table('transport_categories')
            ->where('status_aktif', 1)
            ->orderBy('nama_kategori', 'ASC')
            ->get()
            ->getResultArray();
        
        $fuels = $db->table('transport_fuels')
            ->where('status_aktif', 1)
            ->orderBy('nama_bahan_bakar', 'ASC')
            ->get()
            ->getResultArray();
        
        $data = [
            'title' => 'Input Statistik Transportasi',
            'user' => session()->get('user'),
            'edit_data' => null,
            'available_periods' => $this->transportStatsModel->getAvailablePeriods($userId),
            'categories' => $categories,
            'fuels' => $fuels
        ];

        // Check if this is an edit request
        $editId = $this->request->getGet('edit');
        if ($editId) {
            $editData = $this->transportStatsModel
                ->where('id', $editId)
                ->where('input_by', $userId)
                ->first();
                
            if ($editData) {
                $data['edit_data'] = $editData;
                $data['title'] = 'Edit Statistik Transportasi';
            }
        }

        return view('security/transportation', $data);
    }

    /**
     * Save transportation statistics
     */
    public function save()
    {
        // Get valid categories and fuels from database
        $db = \Config\Database::connect();
        
        $validCategories = $db->table('transport_categories')
            ->where('status_aktif', 1)
            ->select('nama_kategori')
            ->get()
            ->getResultArray();
        
        $validFuels = $db->table('transport_fuels')
            ->where('status_aktif', 1)
            ->select('nama_bahan_bakar')
            ->get()
            ->getResultArray();
        
        // Build validation rules dynamically
        $categoryList = array_column($validCategories, 'nama_kategori');
        $fuelList = array_column($validFuels, 'nama_bahan_bakar');
        
        $rules = [
            'tipe_pencatatan' => 'required|in_list[Harian,Mingguan (Back-up),Bulanan (Back-up)]',
            'kategori_kendaraan' => 'required',
            'jenis_bahan_bakar' => 'required',
            'jumlah_total' => 'required|integer|greater_than[0]'
        ];
        
        // Add conditional validation based on tipe_pencatatan
        $tipePencatatan = $this->request->getPost('tipe_pencatatan');
        if ($tipePencatatan === 'Harian') {
            $rules['tanggal_pencatatan'] = 'required|valid_date';
        } elseif ($tipePencatatan === 'Mingguan (Back-up)') {
            $rules['tanggal_mulai'] = 'required|valid_date';
            $rules['tanggal_selesai'] = 'required|valid_date';
        } elseif ($tipePencatatan === 'Bulanan (Back-up)') {
            $rules['bulan'] = 'required|in_list[Januari,Februari,Maret,April,Mei,Juni,Juli,Agustus,September,Oktober,November,Desember]';
            $rules['tahun'] = 'required|integer|greater_than_equal_to[2024]|less_than_equal_to[2030]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        // Additional validation: Check if kategori and bahan bakar exist in database
        $kategori = $this->request->getPost('kategori_kendaraan');
        $bahanBakar = $this->request->getPost('jenis_bahan_bakar');
        
        if (!in_array($kategori, $categoryList)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Kategori kendaraan tidak valid. Silakan pilih dari daftar yang tersedia.');
        }
        
        if (!in_array($bahanBakar, $fuelList)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Jenis bahan bakar tidak valid. Silakan pilih dari daftar yang tersedia.');
        }

        $userId = session()->get('user')['id'];
        $editId = $this->request->getPost('edit_id');
        
        $tipePencatatan = $this->request->getPost('tipe_pencatatan');
        $tanggalPencatatan = $this->request->getPost('tanggal_pencatatan');
        $tanggalMulai = $this->request->getPost('tanggal_mulai');
        $tanggalSelesai = $this->request->getPost('tanggal_selesai');
        $bulan = $this->request->getPost('bulan');
        $tahun = $this->request->getPost('tahun');
        $jumlahTotal = $this->request->getPost('jumlah_total');
        $isShuttle = $this->request->getPost('is_shuttle') ? 1 : 0;

        // Auto-determine ZEV status based on fuel type from database
        $fuelData = $db->table('transport_fuels')
            ->where('nama_bahan_bakar', $bahanBakar)
            ->where('status_aktif', 1)
            ->get()
            ->getRowArray();
        
        $isZev = $fuelData && $fuelData['is_zev'] == 1 ? 1 : 0;
        
        // Auto-determine kategori_sederhana if not provided or empty
        $kategoriSederhana = $this->request->getPost('kategori_sederhana');
        if (empty($kategoriSederhana)) {
            $jenisKendaraanLower = strtolower($kategori);
            
            // Rule 1: Listrik/Non-BBM/Sepeda → Fasilitas Kampus
            if ($bahanBakar === 'Listrik' || $bahanBakar === 'Non-BBM' || strpos($jenisKendaraanLower, 'sepeda') !== false) {
                $kategoriSederhana = 'Fasilitas Kampus';
            }
            // Rule 2: Motor → Roda Dua
            elseif (strpos($jenisKendaraanLower, 'motor') !== false || strpos($jenisKendaraanLower, 'roda dua') !== false || strpos($jenisKendaraanLower, 'roda 2') !== false) {
                $kategoriSederhana = 'Roda Dua';
            }
            // Rule 3: Mobil/Bus/Truck → Roda Empat
            elseif (strpos($jenisKendaraanLower, 'mobil') !== false || strpos($jenisKendaraanLower, 'bus') !== false || 
                    strpos($jenisKendaraanLower, 'truck') !== false || strpos($jenisKendaraanLower, 'roda empat') !== false || 
                    strpos($jenisKendaraanLower, 'roda 4') !== false) {
                $kategoriSederhana = 'Roda Empat';
            }
            // Rule 4: Bensin → Roda Dua
            elseif ($bahanBakar === 'Bensin') {
                $kategoriSederhana = 'Roda Dua';
            }
            // Rule 5: Default → Roda Empat
            else {
                $kategoriSederhana = 'Roda Empat';
            }
        }

        // Check if entry already exists (for new entries only)
        if (!$editId) {
            $existingEntry = $this->transportStatsModel->entryExists($tipePencatatan, $kategori, $bahanBakar, $userId);
            if ($existingEntry) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Data untuk tipe pencatatan, kategori kendaraan, dan jenis bahan bakar ini sudah ada. Silakan edit data yang sudah ada atau pilih kombinasi yang berbeda.');
            }
        }
        
        $data = [
            'periode' => $tipePencatatan,
            'tanggal_pencatatan' => $tipePencatatan === 'Harian' ? $tanggalPencatatan : null,
            'tanggal_mulai' => $tipePencatatan === 'Mingguan (Back-up)' ? $tanggalMulai : null,
            'tanggal_selesai' => $tipePencatatan === 'Mingguan (Back-up)' ? $tanggalSelesai : null,
            'bulan' => $tipePencatatan === 'Bulanan (Back-up)' ? $bulan : null,
            'tahun' => $tipePencatatan === 'Bulanan (Back-up)' ? $tahun : null,
            'kategori_sederhana' => $kategoriSederhana,
            'status_kendaraan' => $this->request->getPost('status_kendaraan') ?: null,
            'kategori_kendaraan' => $kategori,
            'jenis_bahan_bakar' => $bahanBakar,
            'jumlah_total' => $jumlahTotal,
            'input_by' => $userId,
            'is_zev' => $isZev,
            'is_shuttle' => $isShuttle
        ];

        // Set timezone to Asia/Jakarta for accurate timestamp
        date_default_timezone_set('Asia/Jakarta');
        
        // Add current timestamp in WIB
        if (!$editId) {
            $data['created_at'] = now_wib();
        }
        $data['updated_at'] = now_wib();

        try {
            if ($editId) {
                // Update existing record
                $result = $this->transportStatsModel->update($editId, $data);
                if (!$result) {
                    // Show validation errors
                    $errors = $this->transportStatsModel->errors();
                    log_message('error', 'Transport stats update validation errors: ' . print_r($errors, true));
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Gagal update data: ' . implode(', ', $errors));
                }
                $message = 'Statistik transportasi berhasil diperbarui';
            } else {
                // Insert new record
                $insertId = $this->transportStatsModel->insert($data);
                if (!$insertId) {
                    // Show validation errors
                    $errors = $this->transportStatsModel->errors();
                    log_message('error', 'Transport stats insert validation errors: ' . print_r($errors, true));
                    log_message('error', 'Transport stats insert data: ' . print_r($data, true));
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Gagal simpan data: ' . implode(', ', $errors));
                }
                $message = 'Statistik transportasi berhasil disimpan';
            }
            
            return redirect()->to('/security/dashboard')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            log_message('error', 'Transport stats save error: ' . $e->getMessage());
            log_message('error', 'Transport stats save trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'ERROR DATABASE: ' . $e->getMessage());
        }
    }

    /**
     * Delete transport stats entry
     */
    public function delete($id)
    {
        $userId = session()->get('user')['id'];
        
        try {
            // Verify ownership before deletion
            $entry = $this->transportStatsModel
                ->where('id', $id)
                ->where('input_by', $userId)
                ->first();
                
            if (!$entry) {
                return redirect()->back()
                    ->with('error', 'Data tidak ditemukan atau Anda tidak memiliki akses untuk menghapus data ini.');
            }
            
            $this->transportStatsModel->delete($id);
            
            return redirect()->back()
                ->with('success', 'Data statistik transportasi berhasil dihapus');
                
        } catch (\Exception $e) {
            log_message('error', 'Transport stats delete error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Gagal menghapus data. Silakan coba lagi.');
        }
    }

    /**
     * Bulk delete transport stats entries
     */
    public function bulkDelete()
    {
        // Only accept AJAX requests
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ])->setStatusCode(400);
        }

        // Check authentication
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu'
            ])->setStatusCode(401);
        }

        $userId = session()->get('user')['id'];
        $db = \Config\Database::connect();
        
        try {
            // Get request data
            $json = $this->request->getJSON(true);
            $ids = $json['ids'] ?? [];
            
            // Validate IDs
            if (empty($ids) || !is_array($ids)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tidak ada data yang dipilih untuk dihapus'
                ]);
            }
            
            // Sanitize IDs (ensure they are integers)
            $ids = array_map('intval', $ids);
            $ids = array_filter($ids, function($id) {
                return $id > 0;
            });
            
            if (empty($ids)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ID data tidak valid'
                ]);
            }
            
            log_message('info', 'Bulk delete request for IDs: ' . implode(', ', $ids) . ' by user: ' . $userId);
            
            // Start transaction
            $db->transStart();
            
            // Verify ownership and get entries to be deleted
            $entriesToDelete = $this->transportStatsModel
                ->whereIn('id', $ids)
                ->where('input_by', $userId)
                ->findAll();
            
            $foundCount = count($entriesToDelete);
            
            if ($foundCount === 0) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data yang dipilih tidak ditemukan atau Anda tidak memiliki akses untuk menghapusnya'
                ]);
            }
            
            // Extract IDs of entries that can be deleted
            $validIds = array_column($entriesToDelete, 'id');
            
            // Delete entries
            $this->transportStatsModel->whereIn('id', $validIds)->delete();
            
            // Complete transaction
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            log_message('info', 'Bulk delete successful: ' . $foundCount . ' entries deleted by user: ' . $userId);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Berhasil menghapus ' . $foundCount . ' data kendaraan',
                'deleted_count' => $foundCount,
                'requested_count' => count($ids)
            ]);
            
        } catch (\Exception $e) {
            if (isset($db)) {
                $db->transRollback();
            }
            log_message('error', 'Bulk delete transport stats error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Transportation history page - View all records with summary
     */
    public function history()
    {
        $userId = session()->get('user')['id'];
        
        // Get filter parameters
        $filterMonth = $this->request->getGet('filter_month');
        $filterYear = $this->request->getGet('filter_year');
        
        // Default to current month/year if no filter
        $currentMonth = $filterMonth ?: date('n'); // 1-12
        $currentYear = $filterYear ?: date('Y');
        
        // Get month name in Indonesian
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $currentMonthName = $monthNames[$currentMonth];
        
        // Get summary data for current month (grouped by category)
        $monthlySummary = $this->transportStatsModel->getMonthlySummary($userId, $currentMonth, $currentYear);
        
        // Get all records
        $allRecords = $this->transportStatsModel
            ->where('input_by', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
        
        $data = [
            'title' => 'Riwayat Transportasi',
            'user' => session()->get('user'),
            'all_records' => $allRecords,
            'monthly_summary' => $monthlySummary,
            'current_month' => $currentMonth,
            'current_year' => $currentYear,
            'current_month_name' => $currentMonthName,
            'filter_active' => ($filterMonth || $filterYear) ? true : false
        ];

        return view('security/history_transportation', $data);
    }

    /**
     * ========================================
     * LOG HARIAN KENDARAAN METHODS
     * ========================================
     */

    /**
     * Log Harian Kendaraan - Main Page
     */
    public function logHarian()
    {
        $db = \Config\Database::connect();
        
        // Auto-create table if not exists
        $this->createDailyLogTable($db);
        
        // Get today's date
        $today = date('Y-m-d');
        
        // Get today's summary
        $todaySummary = $db->table('transport_daily_logs')
            ->select('jenis_kendaraan, SUM(jumlah_masuk) as total_masuk, SUM(jumlah_keluar) as total_keluar')
            ->where('tanggal', $today)
            ->groupBy('jenis_kendaraan')
            ->get()
            ->getResultArray();
        
        // Calculate totals for today
        $totalMasukHariIni = 0;
        $totalKeluarHariIni = 0;
        foreach ($todaySummary as $item) {
            $totalMasukHariIni += $item['total_masuk'];
            $totalKeluarHariIni += $item['total_keluar'];
        }
        
        // Get all logs (recent first)
        $allLogs = $db->table('transport_daily_logs')
            ->orderBy('tanggal', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
        
        // Get monthly summary for current month
        $currentMonth = date('m');
        $currentYear = date('Y');
        
        $monthlySummary = $db->table('transport_daily_logs')
            ->select('jenis_kendaraan, SUM(jumlah_masuk) as total_masuk, SUM(jumlah_keluar) as total_keluar')
            ->where('MONTH(tanggal)', $currentMonth)
            ->where('YEAR(tanggal)', $currentYear)
            ->groupBy('jenis_kendaraan')
            ->get()
            ->getResultArray();
        
        $data = [
            'title' => 'Log Harian Kendaraan',
            'user' => session()->get('user'),
            'security' => session()->get('user'), // Changed from admin to security
            'today_summary' => $todaySummary,
            'total_masuk_hari_ini' => $totalMasukHariIni,
            'total_keluar_hari_ini' => $totalKeluarHariIni,
            'all_logs' => $allLogs,
            'monthly_summary' => $monthlySummary,
            'current_month' => $currentMonth,
            'current_year' => $currentYear
        ];

        return view('security/transportation/log_harian', $data);
    }

    /**
     * Create daily log table if not exists
     */
    private function createDailyLogTable($db)
    {
        $db->query("
            CREATE TABLE IF NOT EXISTS transport_daily_logs (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                tanggal DATE NOT NULL,
                jenis_kendaraan ENUM('Mobil', 'Motor', 'Sepeda', 'Bus') NOT NULL,
                jumlah_masuk INT(11) NOT NULL DEFAULT 0,
                jumlah_keluar INT(11) NOT NULL DEFAULT 0,
                keterangan TEXT NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                INDEX idx_tanggal (tanggal),
                INDEX idx_jenis (jenis_kendaraan)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    /**
     * Simpan Log Harian
     */
    public function simpanLogHarian()
    {
        $db = \Config\Database::connect();
        
        $id = $this->request->getPost('id');
        $tanggal = $this->request->getPost('tanggal');
        $jenisKendaraan = $this->request->getPost('jenis_kendaraan');
        $jumlahMasuk = $this->request->getPost('jumlah_masuk');
        $jumlahKeluar = $this->request->getPost('jumlah_keluar');
        $keterangan = $this->request->getPost('keterangan');
        
        // Validation
        if (empty($tanggal) || empty($jenisKendaraan)) {
            return redirect()->back()->with('error', 'Tanggal dan Jenis Kendaraan harus diisi');
        }
        
        if ($jumlahMasuk < 0 || $jumlahKeluar < 0) {
            return redirect()->back()->with('error', 'Jumlah tidak boleh negatif');
        }
        
        try {
            $data = [
                'tanggal' => $tanggal,
                'jenis_kendaraan' => $jenisKendaraan,
                'jumlah_masuk' => $jumlahMasuk ?? 0,
                'jumlah_keluar' => $jumlahKeluar ?? 0,
                'keterangan' => $keterangan
            ];
            
            if ($id) {
                // Update - Always allowed, even if backed up
                $data['updated_at'] = date('Y-m-d H:i:s');
                $db->table('transport_daily_logs')->update($data, ['id' => $id]);
                $message = 'Log harian berhasil diperbarui';
            } else {
                // Insert
                $data['created_at'] = date('Y-m-d H:i:s');
                $data['is_backed_up'] = 0; // Default: not backed up
                $db->table('transport_daily_logs')->insert($data);
                $message = 'Log harian berhasil ditambahkan';
            }
            
            return redirect()->to('/security/transportation/log-harian')->with('success', $message);
        } catch (\Exception $e) {
            log_message('error', 'Simpan log harian error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan log: ' . $e->getMessage());
        }
    }

    /**
     * Get Log Harian by ID (for edit)
     */
    public function getLogHarian($id)
    {
        $db = \Config\Database::connect();
        
        try {
            log_message('info', 'getLogHarian called with ID: ' . $id);
            
            $log = $db->table('transport_daily_logs')
                ->where('id', $id)
                ->get()
                ->getRowArray();
            
            log_message('info', 'Query result: ' . print_r($log, true));
            
            if (!$log) {
                log_message('warning', 'Log not found for ID: ' . $id);
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => 'Data tidak ditemukan dengan ID: ' . $id
                ]);
            }
            
            log_message('info', 'Returning log data for ID: ' . $id);
            return $this->response->setJSON([
                'success' => true, 
                'data' => $log
            ]);
        } catch (\Exception $e) {
            log_message('error', 'getLogHarian error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Hapus Log Harian
     */
    public function hapusLogHarian($id)
    {
        $db = \Config\Database::connect();
        
        try {
            // Check if data exists
            $log = $db->table('transport_daily_logs')
                ->where('id', $id)
                ->get()
                ->getRowArray();
            
            if (!$log) {
                return redirect()->back()->with('error', 'Data tidak ditemukan');
            }
            
            // Delete the log - Always allowed, even if backed up
            $db->table('transport_daily_logs')->delete(['id' => $id]);
            return redirect()->to('/security/transportation/log-harian')->with('success', 'Log harian berhasil dihapus');
        } catch (\Exception $e) {
            log_message('error', 'Hapus log harian error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus log: ' . $e->getMessage());
        }
    }
    
    /**
     * Bulk Delete Log Harian - Delete multiple logs at once
     */
    public function bulkDeleteLogHarian()
    {
        // Only accept AJAX requests
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ])->setStatusCode(400);
        }

        // Check authentication
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu'
            ])->setStatusCode(401);
        }

        $db = \Config\Database::connect();
        
        try {
            // Get request data
            $json = $this->request->getJSON(true);
            $ids = $json['ids'] ?? [];
            
            // Validate IDs
            if (empty($ids) || !is_array($ids)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tidak ada data yang dipilih untuk dihapus'
                ]);
            }
            
            // Sanitize IDs (ensure they are integers)
            $ids = array_map('intval', $ids);
            $ids = array_filter($ids, function($id) {
                return $id > 0;
            });
            
            if (empty($ids)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ID data tidak valid'
                ]);
            }
            
            log_message('info', 'Bulk delete request for IDs: ' . implode(', ', $ids));
            
            // Check if table exists
            if (!$db->tableExists('transport_daily_logs')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tabel transport_daily_logs tidak ditemukan'
                ]);
            }
            
            // Start transaction
            $db->transStart();
            
            // Get logs to be deleted (for logging purposes)
            $logsToDelete = $db->table('transport_daily_logs')
                ->whereIn('id', $ids)
                ->get()
                ->getResultArray();
            
            $foundCount = count($logsToDelete);
            
            if ($foundCount === 0) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data yang dipilih tidak ditemukan di database'
                ]);
            }
            
            // Delete logs - Always allowed, even if backed up
            $deleteResult = $db->table('transport_daily_logs')
                ->whereIn('id', $ids)
                ->delete();
            
            // Complete transaction
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            log_message('info', 'Bulk delete successful: ' . $foundCount . ' logs deleted');
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Berhasil menghapus data log harian',
                'deleted_count' => $foundCount,
                'requested_count' => count($ids)
            ]);
            
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Bulk delete log harian error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Export Log Harian to Excel
     */
    public function exportLogHarianExcel()
    {
        $db = \Config\Database::connect();
        
        try {
            // Get filter parameters
            $startDate = $this->request->getGet('start_date');
            $endDate = $this->request->getGet('end_date');
            
            $builder = $db->table('transport_daily_logs')
                ->orderBy('tanggal', 'DESC');
            
            if ($startDate && $endDate) {
                $builder->where('tanggal >=', $startDate)
                        ->where('tanggal <=', $endDate);
            }
            
            $logs = $builder->get()->getResultArray();
            
            // Load PhpSpreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set header
            $sheet->setCellValue('A1', 'No');
            $sheet->setCellValue('B1', 'Tanggal');
            $sheet->setCellValue('C1', 'Jenis Kendaraan');
            $sheet->setCellValue('D1', 'Jumlah Masuk');
            $sheet->setCellValue('E1', 'Jumlah Keluar');
            $sheet->setCellValue('F1', 'Total Aktivitas');
            $sheet->setCellValue('G1', 'Keterangan');
            
            // Style header
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4a5568']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ];
            $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);
            
            // Fill data
            $row = 2;
            $no = 1;
            foreach ($logs as $log) {
                $totalAktivitas = $log['jumlah_masuk'] + $log['jumlah_keluar'];
                
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($log['tanggal'])));
                $sheet->setCellValue('C' . $row, $log['jenis_kendaraan']);
                $sheet->setCellValue('D' . $row, $log['jumlah_masuk']);
                $sheet->setCellValue('E' . $row, $log['jumlah_keluar']);
                $sheet->setCellValue('F' . $row, $totalAktivitas);
                $sheet->setCellValue('G' . $row, $log['keterangan'] ?? '-');
                $row++;
            }
            
            // Auto size columns
            foreach (range('A', 'G') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Generate file
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $filename = 'Log_Harian_Kendaraan_' . date('YmdHis') . '.xlsx';
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            log_message('error', 'Export log harian Excel error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export Excel: ' . $e->getMessage());
        }
    }

    /**
     * Export Log Harian to PDF
     */
    public function exportLogHarianPdf()
    {
        $db = \Config\Database::connect();
        
        try {
            // Get filter parameters
            $startDate = $this->request->getGet('start_date');
            $endDate = $this->request->getGet('end_date');
            
            $builder = $db->table('transport_daily_logs')
                ->orderBy('tanggal', 'DESC');
            
            if ($startDate && $endDate) {
                $builder->where('tanggal >=', $startDate)
                        ->where('tanggal <=', $endDate);
            }
            
            $logs = $builder->get()->getResultArray();
            
            // Calculate summary
            $totalMasuk = 0;
            $totalKeluar = 0;
            foreach ($logs as $log) {
                $totalMasuk += $log['jumlah_masuk'];
                $totalKeluar += $log['jumlah_keluar'];
            }
            
            $summary = [
                'total_masuk' => $totalMasuk,
                'total_keluar' => $totalKeluar,
                'total_aktivitas' => $totalMasuk + $totalKeluar
            ];
            
            // Prepare data for view
            $data = [
                'logs' => $logs,
                'summary' => $summary,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'security' => session()->get('user') // Changed from admin to security
            ];
            
            // Load view and render to HTML
            $html = view('security/transportation/log_harian_pdf', $data);
            
            // Initialize Dompdf
            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'Arial');
            
            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            
            // Set paper size and orientation
            $dompdf->setPaper('A4', 'landscape');
            
            // Render PDF
            $dompdf->render();
            
            // Generate filename
            $filename = 'Log_Harian_Kendaraan_' . date('YmdHis') . '.pdf';
            
            // Output PDF for download
            $dompdf->stream($filename, ['Attachment' => true]);
            exit;

        } catch (\Exception $e) {
            log_message('error', 'Export log harian PDF error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export PDF: ' . $e->getMessage());
        }
    }

    /**
     * Backup and Download - Combined function for backup + Excel export
     */
    public function backupAndDownload()
    {
        // Only accept AJAX requests
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'success' => false,
                'message' => 'Invalid request method'
            ])->setStatusCode(400);
        }

        // Check authentication
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'status' => 'error',
                'success' => false,
                'message' => 'Silakan login terlebih dahulu'
            ])->setStatusCode(401);
        }

        $db = \Config\Database::connect();
        
        try {
            // Get request data
            $json = $this->request->getJSON(true);
            $tipe = $json['tipe'] ?? null;
            $kategori = $json['kategori'] ?? 'Semua';
            $tanggalHarian = $json['tanggal_harian'] ?? null;
            $tanggalMulai = $json['tanggal_mulai'] ?? null;
            $tanggalSelesai = $json['tanggal_selesai'] ?? null;
            $bulan = $json['bulan'] ?? null;
            $tahun = $json['tahun'] ?? null;
            
            // Log request parameters for debugging
            log_message('info', 'Backup Request: ' . json_encode($json));
            
            // Check if table exists
            if (!$db->tableExists('transport_daily_logs')) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'success' => false,
                    'message' => 'Tabel transport_daily_logs tidak ditemukan.'
                ]);
            }

            // Add is_backed_up column if not exists
            $fields = $db->getFieldNames('transport_daily_logs');
            if (!in_array('is_backed_up', $fields)) {
                $db->query("ALTER TABLE transport_daily_logs ADD COLUMN is_backed_up TINYINT(1) DEFAULT 0 AFTER keterangan");
            }
            
            // Build query to get logs based on filters
            $builder = $db->table('transport_daily_logs');
            
            // Only get non-backed-up data
            $builder->groupStart()
                ->where('is_backed_up', 0)
                ->orWhere('is_backed_up IS NULL', null, false)
                ->groupEnd();
            
            // Apply filters based on tipe
            if ($tipe === 'Harian' && $tanggalHarian) {
                // Single date filter
                $builder->where('tanggal', $tanggalHarian);
                log_message('info', 'Filter Harian: ' . $tanggalHarian);
            } elseif ($tipe === 'Mingguan') {
                // Date range filter using BETWEEN
                if ($tanggalMulai && $tanggalSelesai) {
                    $builder->where('tanggal >=', $tanggalMulai);
                    $builder->where('tanggal <=', $tanggalSelesai);
                    log_message('info', 'Filter Mingguan: ' . $tanggalMulai . ' to ' . $tanggalSelesai);
                }
            } elseif ($tipe === 'Bulanan' && $bulan && $tahun) {
                // Monthly filter - get all days in the month
                $firstDay = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-01';
                $lastDay = date('Y-m-t', strtotime($firstDay)); // Last day of month
                
                $builder->where('tanggal >=', $firstDay);
                $builder->where('tanggal <=', $lastDay);
                
                log_message('info', 'Filter Bulanan: ' . $firstDay . ' to ' . $lastDay);
            }
            
            // Apply kategori filter - ONLY if not "Semua"
            if ($kategori && $kategori !== 'Semua') {
                $builder->where('jenis_kendaraan', $kategori);
                log_message('info', 'Filter Kategori: ' . $kategori);
            } else {
                log_message('info', 'Filter Kategori: Semua (no filter applied)');
            }
            
            // Order by date
            $builder->orderBy('tanggal', 'ASC');
            $builder->orderBy('jenis_kendaraan', 'ASC');
            
            $logs = $builder->get()->getResultArray();
            
            // Log query for debugging
            log_message('info', 'Backup Query: ' . $db->getLastQuery());
            log_message('info', 'Total logs found: ' . count($logs));
            
            // Validate data exists
            if (empty($logs)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'success' => false,
                    'message' => 'Data tidak ditemukan untuk periode ini. Tidak ada data yang bisa di-backup atau semua data sudah di-backup sebelumnya.'
                ]);
            }
            
            // Start transaction for backup
            $db->transStart();
            
            // Group logs by month, year, and vehicle type for aggregation
            $aggregated = [];
            foreach ($logs as $log) {
                if (empty($log['tanggal']) || empty($log['jenis_kendaraan'])) {
                    continue;
                }
                
                $date = strtotime($log['tanggal']);
                if ($date === false) {
                    continue;
                }
                
                $month = date('m', $date);
                $year = date('Y', $date);
                $vehicle = $log['jenis_kendaraan'];
                
                $key = $year . '-' . $month . '-' . $vehicle;
                
                if (!isset($aggregated[$key])) {
                    $aggregated[$key] = [
                        'bulan' => $month,
                        'tahun' => $year,
                        'jenis_kendaraan' => $vehicle,
                        'total_masuk' => 0,
                        'total_keluar' => 0,
                        'periode' => 'Bulanan (Back-up)'
                    ];
                }
                
                $aggregated[$key]['total_masuk'] += (int)$log['jumlah_masuk'];
                $aggregated[$key]['total_keluar'] += (int)$log['jumlah_keluar'];
            }
            
            log_message('info', 'Aggregated groups: ' . count($aggregated));
            
            // Get current user ID
            $userId = session()->get('user')['id'] ?? null;
            
            // Insert or update aggregated data to transport_stats
            $totalBackedUp = 0;
            
            foreach ($aggregated as $data) {
                // Check if record already exists
                $existing = $db->table('transport_stats')
                    ->where('bulan', $data['bulan'])
                    ->where('tahun', $data['tahun'])
                    ->where('kategori_kendaraan', $data['jenis_kendaraan'])
                    ->where('periode', 'Bulanan (Back-up)')
                    ->get()
                    ->getRowArray();
                
                if ($existing) {
                    // Update existing record - REPLACE with new total (synchronize)
                    $newTotal = $data['total_masuk'] + $data['total_keluar'];
                    
                    $db->table('transport_stats')->update([
                        'jumlah_total' => $newTotal,
                        'updated_at' => date('Y-m-d H:i:s')
                    ], ['id' => $existing['id']]);
                    
                    log_message('info', 'Updated transport_stats ID ' . $existing['id'] . ': ' . $newTotal);
                } else {
                    // Insert new record
                    $insertData = [
                        'kategori_kendaraan' => $data['jenis_kendaraan'],
                        'jenis_bahan_bakar' => 'Mixed',
                        'jumlah_total' => $data['total_masuk'] + $data['total_keluar'],
                        'is_zev' => 0,
                        'is_shuttle' => 0,
                        'bulan' => $data['bulan'],
                        'tahun' => $data['tahun'],
                        'periode' => 'Bulanan (Back-up)',
                        'input_by' => $userId,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    
                    $db->table('transport_stats')->insert($insertData);
                    
                    log_message('info', 'Inserted new transport_stats: ' . json_encode($insertData));
                }
                
                $totalBackedUp++;
            }
            
            // Mark all logs as backed up
            $logIds = array_column($logs, 'id');
            if (!empty($logIds)) {
                $updateResult = $db->table('transport_daily_logs')
                    ->whereIn('id', $logIds)
                    ->update([
                        'is_backed_up' => 1, 
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                
                log_message('info', 'Marked ' . count($logIds) . ' logs as backed up');
            }
            
            // Complete transaction
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            // Generate filename based on tipe
            $filename = $this->generateBackupFilename($tipe, $tanggalHarian, $tanggalMulai, $tanggalSelesai, $bulan, $tahun);
            
            // Store data in session for Excel generation
            session()->set('backup_data', [
                'logs' => $logs,
                'filename' => $filename,
                'tipe' => $tipe,
                'kategori' => $kategori,
                'filters' => [
                    'tanggal_harian' => $tanggalHarian,
                    'tanggal_mulai' => $tanggalMulai,
                    'tanggal_selesai' => $tanggalSelesai,
                    'bulan' => $bulan,
                    'tahun' => $tahun
                ]
            ]);
            
            log_message('info', 'Backup successful: ' . count($logs) . ' logs backed up');
            
            // Return success with download URL
            return $this->response->setJSON([
                'status' => 'success',
                'success' => true,
                'message' => 'Back-up berhasil! Data telah direkap dan file Excel siap diunduh.',
                'total_backed_up' => count($logs),
                'download_url' => base_url('security/transportation/download-backup-excel')
            ]);
            
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Backup and download error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'status' => 'error',
                'success' => false,
                'message' => 'Gagal melakukan backup: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Generate dynamic filename for backup
     */
    private function generateBackupFilename($tipe, $tanggalHarian, $tanggalMulai, $tanggalSelesai, $bulan, $tahun)
    {
        $filename = 'Log_Kendaraan_';
        
        if ($tipe === 'Harian' && $tanggalHarian) {
            $date = date('d-m-Y', strtotime($tanggalHarian));
            $filename .= 'Harian_' . $date;
        } elseif ($tipe === 'Mingguan' && $tanggalMulai && $tanggalSelesai) {
            $dateStart = date('d-m-Y', strtotime($tanggalMulai));
            $dateEnd = date('d-m-Y', strtotime($tanggalSelesai));
            $filename .= 'Mingguan_' . $dateStart . '_sampai_' . $dateEnd;
        } elseif ($tipe === 'Bulanan' && $bulan && $tahun) {
            $monthNames = [
                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
            ];
            $bulanText = $monthNames[$bulan] ?? $bulan;
            $filename .= 'Bulanan_' . $bulanText . '_' . $tahun;
        } else {
            $filename .= date('d-m-Y_His');
        }
        
        $filename .= '.xlsx';
        
        return $filename;
    }
    
    /**
     * Download Backup Excel - Generate and download Excel file
     */
    public function downloadBackupExcel()
    {
        // Get backup data from session
        $backupData = session()->get('backup_data');
        
        if (!$backupData || empty($backupData['logs'])) {
            return redirect()->back()->with('error', 'Data backup tidak ditemukan. Silakan ulangi proses backup.');
        }
        
        $logs = $backupData['logs'];
        $filename = $backupData['filename'];
        $tipe = $backupData['tipe'];
        $kategori = $backupData['kategori'];
        $filters = $backupData['filters'];
        
        try {
            // Load PhpSpreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator('UI GreenMetric POLBAN')
                ->setTitle('Backup Log Harian Kendaraan')
                ->setSubject('Data Log Kendaraan')
                ->setDescription('Backup data log harian kendaraan kampus');
            
            // Header Section
            $sheet->setCellValue('A1', 'BACKUP LOG HARIAN KENDARAAN');
            $sheet->mergeCells('A1:H1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            $sheet->setCellValue('A2', 'Politeknik Negeri Bandung');
            $sheet->mergeCells('A2:H2');
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // Info Section
            $sheet->setCellValue('A3', 'Tipe Backup: ' . $tipe);
            $sheet->setCellValue('A4', 'Kategori: ' . $kategori);
            
            // Date info based on tipe
            if ($tipe === 'Harian' && !empty($filters['tanggal_harian'])) {
                $sheet->setCellValue('A5', 'Tanggal: ' . date('d/m/Y', strtotime($filters['tanggal_harian'])));
            } elseif ($tipe === 'Mingguan' && !empty($filters['tanggal_mulai']) && !empty($filters['tanggal_selesai'])) {
                $sheet->setCellValue('A5', 'Periode: ' . date('d/m/Y', strtotime($filters['tanggal_mulai'])) . ' - ' . date('d/m/Y', strtotime($filters['tanggal_selesai'])));
            } elseif ($tipe === 'Bulanan' && !empty($filters['bulan']) && !empty($filters['tahun'])) {
                $monthNames = [
                    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                ];
                $bulanText = $monthNames[$filters['bulan']] ?? $filters['bulan'];
                $sheet->setCellValue('A5', 'Bulan: ' . $bulanText . ' ' . $filters['tahun']);
            }
            
            $sheet->setCellValue('A6', 'Tanggal Export: ' . date('d/m/Y H:i:s'));
            
            // Table Header
            $headerRow = 8;
            $headers = ['No', 'Tanggal', 'Jenis Kendaraan', 'Jumlah Masuk', 'Jumlah Keluar', 'Total Aktivitas', 'Keterangan', 'Status'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . $headerRow, $header);
                $col++;
            }
            
            // Style header
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2c3e50']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
            ];
            $sheet->getStyle('A' . $headerRow . ':H' . $headerRow)->applyFromArray($headerStyle);
            
            // Fill data
            $row = $headerRow + 1;
            $no = 1;
            $totalMasuk = 0;
            $totalKeluar = 0;
            
            foreach ($logs as $log) {
                $totalAktivitas = $log['jumlah_masuk'] + $log['jumlah_keluar'];
                $totalMasuk += $log['jumlah_masuk'];
                $totalKeluar += $log['jumlah_keluar'];
                
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($log['tanggal'])));
                $sheet->setCellValue('C' . $row, $log['jenis_kendaraan']);
                $sheet->setCellValue('D' . $row, $log['jumlah_masuk']);
                $sheet->setCellValue('E' . $row, $log['jumlah_keluar']);
                $sheet->setCellValue('F' . $row, $totalAktivitas);
                $sheet->setCellValue('G' . $row, $log['keterangan'] ?? '-');
                $sheet->setCellValue('H' . $row, 'Backed-up');
                
                // Style data rows
                $sheet->getStyle('A' . $row . ':H' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('D' . $row . ':F' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                
                $row++;
            }
            
            // Summary Section
            $summaryRow = $row + 1;
            $sheet->setCellValue('A' . $summaryRow, 'RINGKASAN');
            $sheet->mergeCells('A' . $summaryRow . ':H' . $summaryRow);
            $sheet->getStyle('A' . $summaryRow)->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A' . $summaryRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('e8f0fe');
            
            $summaryRow++;
            $sheet->setCellValue('A' . $summaryRow, 'Total Data:');
            $sheet->setCellValue('B' . $summaryRow, count($logs) . ' record');
            $sheet->getStyle('A' . $summaryRow)->getFont()->setBold(true);
            
            $summaryRow++;
            $sheet->setCellValue('A' . $summaryRow, 'Total Kendaraan Masuk:');
            $sheet->setCellValue('B' . $summaryRow, number_format($totalMasuk) . ' unit');
            $sheet->getStyle('A' . $summaryRow)->getFont()->setBold(true);
            
            $summaryRow++;
            $sheet->setCellValue('A' . $summaryRow, 'Total Kendaraan Keluar:');
            $sheet->setCellValue('B' . $summaryRow, number_format($totalKeluar) . ' unit');
            $sheet->getStyle('A' . $summaryRow)->getFont()->setBold(true);
            
            $summaryRow++;
            $sheet->setCellValue('A' . $summaryRow, 'Total Aktivitas:');
            $sheet->setCellValue('B' . $summaryRow, number_format($totalMasuk + $totalKeluar) . ' unit');
            $sheet->getStyle('A' . $summaryRow)->getFont()->setBold(true);
            
            // Auto size columns
            foreach (range('A', 'H') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Clear session data
            session()->remove('backup_data');
            
            // Generate file
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            log_message('error', 'Download backup Excel error: ' . $e->getMessage());
            session()->remove('backup_data');
            return redirect()->back()->with('error', 'Gagal mengunduh file Excel: ' . $e->getMessage());
        }
    }

    /**
     * Back-up Log Harian to Monthly Report
     * Aggregate daily logs and insert/update to transport_stats table
     */
    public function backupLogs()
    {
        // Only accept AJAX requests
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'success' => false,
                'message' => 'Invalid request method'
            ])->setStatusCode(400);
        }

        // Check authentication
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'status' => 'error',
                'success' => false,
                'message' => 'Silakan login terlebih dahulu'
            ])->setStatusCode(401);
        }

        $db = \Config\Database::connect();
        
        try {
            // Get request data
            $json = $this->request->getJSON(true);
            $tipe = $json['tipe'] ?? null;
            $kategori = $json['kategori'] ?? 'Semua';
            $tanggalHarian = $json['tanggal_harian'] ?? null;
            $tanggalMulai = $json['tanggal_mulai'] ?? null;
            $tanggalSelesai = $json['tanggal_selesai'] ?? null;
            $bulan = $json['bulan'] ?? null;
            $tahun = $json['tahun'] ?? null;
            
            // Check if table exists
            if (!$db->tableExists('transport_daily_logs')) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'success' => false,
                    'message' => 'Tabel transport_daily_logs tidak ditemukan. Silakan buat data log harian terlebih dahulu.'
                ]);
            }

            // Add is_backed_up column if not exists
            $fields = $db->getFieldNames('transport_daily_logs');
            if (!in_array('is_backed_up', $fields)) {
                $db->query("ALTER TABLE transport_daily_logs ADD COLUMN is_backed_up TINYINT(1) DEFAULT 0 AFTER keterangan");
                log_message('info', 'Column is_backed_up added to transport_daily_logs');
            }
            
            // Start transaction
            $db->transStart();
            
            // Build query to get logs based on tipe
            $builder = $db->table('transport_daily_logs');
            $builder->groupStart()
                ->where('is_backed_up', 0)
                ->orWhere('is_backed_up IS NULL', null, false)
                ->groupEnd();
            
            // Apply filters based on tipe
            if ($tipe === 'Harian' && $tanggalHarian) {
                $builder->where('tanggal', $tanggalHarian);
            } elseif ($tipe === 'Mingguan') {
                if ($tanggalMulai) {
                    $builder->where('tanggal >=', $tanggalMulai);
                }
                if ($tanggalSelesai) {
                    $builder->where('tanggal <=', $tanggalSelesai);
                }
            } elseif ($tipe === 'Bulanan' && $bulan && $tahun) {
                $builder->where('MONTH(tanggal)', $bulan);
                $builder->where('YEAR(tanggal)', $tahun);
            }
            
            // Apply kategori filter
            if ($kategori && $kategori !== 'Semua') {
                $builder->where('jenis_kendaraan', $kategori);
            }
            
            $query = $builder->get();
            
            // Validate query result
            if ($query === false) {
                $db->transRollback();
                log_message('error', 'Query failed: ' . json_encode($db->error()));
                return $this->response->setJSON([
                    'status' => 'error',
                    'success' => false,
                    'message' => 'Gagal mengambil data dari database: ' . ($db->error()['message'] ?? 'Unknown error')
                ]);
            }
            
            $logs = $query->getResultArray();
            
            // Check if there's data to backup
            if (empty($logs)) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status' => 'error',
                    'success' => false,
                    'message' => 'Tidak ada data harian yang bisa di-backup. Semua data sudah di-backup atau belum ada data yang diinput.'
                ]);
            }
            
            log_message('info', 'Found ' . count($logs) . ' logs to backup');
            
            // Group logs by month, year, and vehicle type
            $aggregated = [];
            foreach ($logs as $log) {
                // Validate log data
                if (empty($log['tanggal']) || empty($log['jenis_kendaraan'])) {
                    log_message('warning', 'Skipping invalid log: ' . json_encode($log));
                    continue;
                }
                
                $date = strtotime($log['tanggal']);
                if ($date === false) {
                    log_message('warning', 'Invalid date format: ' . $log['tanggal']);
                    continue;
                }
                
                $month = date('m', $date);
                $year = date('Y', $date);
                $vehicle = $log['jenis_kendaraan'];
                
                $key = $year . '-' . $month . '-' . $vehicle;
                
                if (!isset($aggregated[$key])) {
                    $aggregated[$key] = [
                        'bulan' => $month,
                        'tahun' => $year,
                        'jenis_kendaraan' => $vehicle,
                        'total_masuk' => 0,
                        'total_keluar' => 0,
                        'periode' => 'Bulanan (Back-up)'
                    ];
                }
                
                $aggregated[$key]['total_masuk'] += (int)$log['jumlah_masuk'];
                $aggregated[$key]['total_keluar'] += (int)$log['jumlah_keluar'];
            }
            
            // Check if aggregation produced results
            if (empty($aggregated)) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status' => 'error',
                    'success' => false,
                    'message' => 'Tidak ada data valid untuk di-backup. Periksa format data log harian.'
                ]);
            }
            
            log_message('info', 'Aggregated into ' . count($aggregated) . ' groups');
            
            // Get current user ID from session
            $userId = session()->get('user')['id'] ?? null;
            
            // Insert or update aggregated data to transport_stats
            $totalBackedUp = 0;
            $totalInserted = 0;
            $totalUpdated = 0;
            
            foreach ($aggregated as $data) {
                // Check if record already exists
                $existingQuery = $db->table('transport_stats')
                    ->where('bulan', $data['bulan'])
                    ->where('tahun', $data['tahun'])
                    ->where('kategori_kendaraan', $data['jenis_kendaraan'])
                    ->where('periode', 'Bulanan (Back-up)')
                    ->get();
                
                if ($existingQuery === false) {
                    throw new \Exception('Failed to check existing record: ' . ($db->error()['message'] ?? 'Unknown error'));
                }
                
                $existing = $existingQuery->getRowArray();
                
                if ($existing) {
                    // Update existing record - REPLACE with new total (synchronize)
                    $newTotal = $data['total_masuk'] + $data['total_keluar'];
                    
                    $updateResult = $db->table('transport_stats')->update([
                        'jumlah_total' => $newTotal,
                        'updated_at' => date('Y-m-d H:i:s')
                    ], ['id' => $existing['id']]);
                    
                    if ($updateResult === false) {
                        throw new \Exception('Failed to update transport_stats: ' . ($db->error()['message'] ?? 'Unknown error'));
                    }
                    
                    $totalUpdated++;
                    log_message('info', "Updated (synchronized) record ID {$existing['id']}: {$data['jenis_kendaraan']} - {$data['bulan']}/{$data['tahun']} = {$newTotal}");
                } else {
                    // Insert new record
                    $insertData = [
                        'kategori_kendaraan' => $data['jenis_kendaraan'],
                        'jenis_bahan_bakar' => 'Mixed', // Default value
                        'jumlah_total' => $data['total_masuk'] + $data['total_keluar'],
                        'is_zev' => 0, // Default value
                        'is_shuttle' => 0, // Default value
                        'bulan' => $data['bulan'],
                        'tahun' => $data['tahun'],
                        'periode' => 'Bulanan (Back-up)',
                        'input_by' => $userId,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    
                    $insertResult = $db->table('transport_stats')->insert($insertData);
                    
                    if ($insertResult === false) {
                        throw new \Exception('Failed to insert to transport_stats: ' . ($db->error()['message'] ?? 'Unknown error'));
                    }
                    
                    $totalInserted++;
                    log_message('info', "Inserted new record: {$data['jenis_kendaraan']} - {$data['bulan']}/{$data['tahun']} = {$insertData['jumlah_total']}");
                }
                
                $totalBackedUp++;
            }
            
            // Mark all logs as backed up (for tracking purposes only, doesn't lock data)
            $logIds = array_column($logs, 'id');
            if (!empty($logIds)) {
                $updateResult = $db->table('transport_daily_logs')
                    ->whereIn('id', $logIds)
                    ->update([
                        'is_backed_up' => 1, 
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                
                if ($updateResult === false) {
                    throw new \Exception('Failed to mark logs as backed up: ' . ($db->error()['message'] ?? 'Unknown error'));
                }
                
                log_message('info', 'Marked ' . count($logIds) . ' logs as backed up');
            }
            
            // Complete transaction
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            log_message('info', "Backup completed successfully: {$totalBackedUp} groups, {$totalInserted} inserted, {$totalUpdated} updated");
            
            return $this->response->setJSON([
                'status' => 'success',
                'success' => true,
                'message' => 'Back-up berhasil! Data harian telah direkap ke laporan bulanan.',
                'total_backed_up' => count($logs),
                'total_aggregated' => $totalBackedUp,
                'total_inserted' => $totalInserted,
                'total_updated' => $totalUpdated
            ]);
            
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Backup log harian error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'status' => 'error',
                'success' => false,
                'message' => 'Gagal melakukan back-up: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get Backup Preview - Show summary before backup
     */
    public function backupPreview()
    {
        $db = \Config\Database::connect();
        
        try {
            // Get filter parameters
            $tipe = $this->request->getGet('tipe');
            $kategori = $this->request->getGet('kategori');
            $tanggalHarian = $this->request->getGet('tanggal_harian');
            $tanggalMulai = $this->request->getGet('tanggal_mulai');
            $tanggalSelesai = $this->request->getGet('tanggal_selesai');
            $bulan = $this->request->getGet('bulan');
            $tahun = $this->request->getGet('tahun');
            
            // Build query
            $builder = $db->table('transport_daily_logs');
            $builder->select('COUNT(*) as total_records, SUM(jumlah_masuk) as total_masuk, SUM(jumlah_keluar) as total_keluar');
            
            // Apply filters based on tipe
            if ($tipe === 'Harian' && $tanggalHarian) {
                // Single date filter
                $builder->where('tanggal', $tanggalHarian);
            } elseif ($tipe === 'Mingguan') {
                // Date range filter using BETWEEN
                if ($tanggalMulai && $tanggalSelesai) {
                    $builder->where('tanggal >=', $tanggalMulai);
                    $builder->where('tanggal <=', $tanggalSelesai);
                }
            } elseif ($tipe === 'Bulanan' && $bulan && $tahun) {
                // Monthly filter - get all days in the month
                $firstDay = $tahun . '-' . $bulan . '-01';
                $lastDay = date('Y-m-t', strtotime($firstDay)); // Last day of month
                
                $builder->where('tanggal >=', $firstDay);
                $builder->where('tanggal <=', $lastDay);
                
                // Alternative: Use MONTH() and YEAR() functions
                // $builder->where('MONTH(tanggal)', intval($bulan));
                // $builder->where('YEAR(tanggal)', intval($tahun));
            }
            
            // Apply kategori filter - ONLY if not "Semua"
            if ($kategori && $kategori !== 'Semua') {
                $builder->where('jenis_kendaraan', $kategori);
            }
            
            // Only count non-backed-up data
            $builder->groupStart()
                ->where('is_backed_up', 0)
                ->orWhere('is_backed_up IS NULL', null, false)
                ->groupEnd();
            
            $result = $builder->get()->getRowArray();
            
            // Log query for debugging
            log_message('info', 'Backup Preview Query: ' . $db->getLastQuery());
            log_message('info', 'Preview Result: ' . json_encode($result));
            
            return $this->response->setJSON([
                'success' => true,
                'total_records' => $result['total_records'] ?? 0,
                'total_masuk' => $result['total_masuk'] ?? 0,
                'total_keluar' => $result['total_keluar'] ?? 0
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Backup preview error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil preview: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Export Transportation Statistics to Excel
     */
    public function exportExcel()
    {
        $userId = session()->get('user')['id'];
        
        try {
            // Get all transport stats for this user
            $records = $this->transportStatsModel
                ->where('input_by', $userId)
                ->orderBy('created_at', 'DESC')
                ->findAll();
            
            if (empty($records)) {
                return redirect()->back()->with('error', 'Tidak ada data untuk di-export');
            }
            
            // Load PhpSpreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator('UI GreenMetric POLBAN')
                ->setTitle('Laporan Statistik Transportasi')
                ->setSubject('Data Kendaraan')
                ->setDescription('Laporan statistik transportasi kampus');
            
            // Set header
            $sheet->setCellValue('A1', 'LAPORAN STATISTIK TRANSPORTASI');
            $sheet->mergeCells('A1:J1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            $sheet->setCellValue('A2', 'Politeknik Negeri Bandung');
            $sheet->mergeCells('A2:J2');
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            $sheet->setCellValue('A3', 'Tanggal Export: ' . date('d/m/Y H:i:s'));
            $sheet->mergeCells('A3:J3');
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // Table header
            $headerRow = 5;
            $headers = ['No', 'Tipe Pencatatan', 'Tanggal/Periode', 'Kategori Kendaraan', 'Bahan Bakar', 'Jumlah', 'ZEV', 'Shuttle', 'Tanggal Input', 'Keterangan'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . $headerRow, $header);
                $col++;
            }
            
            // Style header
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2c3e50']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
            ];
            $sheet->getStyle('A' . $headerRow . ':J' . $headerRow)->applyFromArray($headerStyle);
            
            // Fill data
            $row = $headerRow + 1;
            $no = 1;
            foreach ($records as $record) {
                // Format tanggal/periode
                $periode = '';
                if ($record['periode'] === 'Harian' && !empty($record['tanggal_pencatatan'])) {
                    $periode = date('d/m/Y', strtotime($record['tanggal_pencatatan']));
                } elseif ($record['periode'] === 'Mingguan (Back-up)') {
                    $periode = date('d/m/Y', strtotime($record['tanggal_mulai'])) . ' - ' . date('d/m/Y', strtotime($record['tanggal_selesai']));
                } elseif ($record['periode'] === 'Bulanan (Back-up)') {
                    $periode = ($record['bulan'] ?? '-') . ' ' . ($record['tahun'] ?? '');
                }
                
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $record['periode']);
                $sheet->setCellValue('C' . $row, $periode);
                $sheet->setCellValue('D' . $row, $record['kategori_kendaraan']);
                $sheet->setCellValue('E' . $row, $record['jenis_bahan_bakar']);
                $sheet->setCellValue('F' . $row, $record['jumlah_total']);
                $sheet->setCellValue('G' . $row, $record['is_zev'] == 1 ? 'Ya' : 'Tidak');
                $sheet->setCellValue('H' . $row, $record['is_shuttle'] == 1 ? 'Ya' : 'Tidak');
                $sheet->setCellValue('I' . $row, date('d/m/Y H:i', strtotime($record['created_at'])));
                $sheet->setCellValue('J' . $row, '-');
                
                // Style data rows
                $sheet->getStyle('A' . $row . ':J' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                
                $row++;
            }
            
            // Add summary
            $summaryRow = $row + 2;
            $sheet->setCellValue('A' . $summaryRow, 'RINGKASAN');
            $sheet->getStyle('A' . $summaryRow)->getFont()->setBold(true);
            
            $summaryRow++;
            $sheet->setCellValue('A' . $summaryRow, 'Total Data:');
            $sheet->setCellValue('B' . $summaryRow, count($records) . ' record');
            
            $summaryRow++;
            $totalKendaraan = array_sum(array_column($records, 'jumlah_total'));
            $sheet->setCellValue('A' . $summaryRow, 'Total Kendaraan:');
            $sheet->setCellValue('B' . $summaryRow, number_format($totalKendaraan) . ' unit');
            
            $summaryRow++;
            $totalZev = count(array_filter($records, function($r) { return $r['is_zev'] == 1; }));
            $sheet->setCellValue('A' . $summaryRow, 'Total ZEV:');
            $sheet->setCellValue('B' . $summaryRow, $totalZev . ' record');
            
            $summaryRow++;
            $totalShuttle = count(array_filter($records, function($r) { return $r['is_shuttle'] == 1; }));
            $sheet->setCellValue('A' . $summaryRow, 'Total Shuttle:');
            $sheet->setCellValue('B' . $summaryRow, $totalShuttle . ' record');
            
            // Auto size columns
            foreach (range('A', 'J') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Generate file
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $filename = 'Statistik_Transportasi_' . date('YmdHis') . '.xlsx';
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            log_message('error', 'Export Excel error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export Excel: ' . $e->getMessage());
        }
    }

    /**
     * Export Transportation Statistics to PDF
     */
    public function exportPdf()
    {
        $userId = session()->get('user')['id'];
        
        try {
            // Get all transport stats for this user
            $records = $this->transportStatsModel
                ->where('input_by', $userId)
                ->orderBy('created_at', 'DESC')
                ->findAll();
            
            if (empty($records)) {
                return redirect()->back()->with('error', 'Tidak ada data untuk di-export');
            }
            
            // Calculate summary
            $totalKendaraan = array_sum(array_column($records, 'jumlah_total'));
            $totalZev = count(array_filter($records, function($r) { return $r['is_zev'] == 1; }));
            $totalShuttle = count(array_filter($records, function($r) { return $r['is_shuttle'] == 1; }));
            
            $summary = [
                'total_records' => count($records),
                'total_kendaraan' => $totalKendaraan,
                'total_zev' => $totalZev,
                'total_shuttle' => $totalShuttle
            ];
            
            // Prepare data for view
            $data = [
                'records' => $records,
                'summary' => $summary,
                'security' => session()->get('user'),
                'export_date' => date('d/m/Y H:i:s')
            ];
            
            // Load view and render to HTML
            $html = view('security/transportation/export_pdf', $data);
            
            // Initialize Dompdf
            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'Arial');
            
            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            
            // Set paper size and orientation
            $dompdf->setPaper('A4', 'landscape');
            
            // Render PDF
            $dompdf->render();
            
            // Generate filename
            $filename = 'Statistik_Transportasi_' . date('YmdHis') . '.pdf';
            
            // Output PDF for download
            $dompdf->stream($filename, ['Attachment' => true]);
            exit;

        } catch (\Exception $e) {
            log_message('error', 'Export PDF error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export PDF: ' . $e->getMessage());
        }
    }
}

