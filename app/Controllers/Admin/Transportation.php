<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TransportStatsModel;
use App\Models\UserModel;

class Transportation extends BaseController
{
    protected $transportStatsModel;
    protected $userModel;

    public function __construct()
    {
        $this->transportStatsModel = new TransportStatsModel();
        $this->userModel = new UserModel();
    }

    /**
     * Transportation Management - Main CRUD page (like Waste Management)
     */
    public function index()
    {
        $data = [
            'title' => 'Manajemen Data Kendaraan Kampus',
            'user' => session()->get('user'),
            'all_records' => $this->getAllRecords(),
            'summary_stats' => $this->getSummaryStats(),
            'security_officers' => $this->getSecurityOfficers()
        ];

        return view('admin_pusat/transportation/index', $data);
    }

    /**
     * Manajemen Master Data - Kategori & Bahan Bakar
     * Auto-create tables if not exist
     */
    public function manajemen()
    {
        $db = \Config\Database::connect();
        
        // Auto-create transport_categories table if not exists
        $db->query("
            CREATE TABLE IF NOT EXISTS transport_categories (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                nama_kategori VARCHAR(100) NOT NULL,
                is_zev TINYINT(1) DEFAULT 0,
                status_aktif TINYINT(1) DEFAULT 1,
                created_at DATETIME NULL,
                updated_at DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        
        // Auto-create transport_fuels table if not exists
        $db->query("
            CREATE TABLE IF NOT EXISTS transport_fuels (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                nama_bahan_bakar VARCHAR(100) NOT NULL,
                is_zev TINYINT(1) DEFAULT 0,
                status_aktif TINYINT(1) DEFAULT 1,
                created_at DATETIME NULL,
                updated_at DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        
        // Insert default categories if table is empty
        $categoryCount = $db->table('transport_categories')->countAll();
        if ($categoryCount == 0) {
            $defaultCategories = [
                ['nama_kategori' => 'Sepeda Motor (Kategori L)', 'is_zev' => 0, 'created_at' => date('Y-m-d H:i:s')],
                ['nama_kategori' => 'Mobil Penumpang (Kategori M1)', 'is_zev' => 0, 'created_at' => date('Y-m-d H:i:s')],
                ['nama_kategori' => 'Mobil Bus (Kategori M2/M3)', 'is_zev' => 0, 'created_at' => date('Y-m-d H:i:s')],
                ['nama_kategori' => 'Kendaraan Bermotor Listrik (KBL)', 'is_zev' => 1, 'created_at' => date('Y-m-d H:i:s')],
                ['nama_kategori' => 'Sepeda (Tidak Bermotor)', 'is_zev' => 1, 'created_at' => date('Y-m-d H:i:s')],
            ];
            $db->table('transport_categories')->insertBatch($defaultCategories);
        }
        
        // Insert default fuels if table is empty
        $fuelCount = $db->table('transport_fuels')->countAll();
        if ($fuelCount == 0) {
            $defaultFuels = [
                ['nama_bahan_bakar' => 'Bensin', 'is_zev' => 0, 'created_at' => date('Y-m-d H:i:s')],
                ['nama_bahan_bakar' => 'Diesel', 'is_zev' => 0, 'created_at' => date('Y-m-d H:i:s')],
                ['nama_bahan_bakar' => 'Listrik', 'is_zev' => 1, 'created_at' => date('Y-m-d H:i:s')],
                ['nama_bahan_bakar' => 'Non-BBM', 'is_zev' => 1, 'created_at' => date('Y-m-d H:i:s')],
            ];
            $db->table('transport_fuels')->insertBatch($defaultFuels);
        }
        
        // Get data
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
            'title' => 'Manajemen Master Data Transportasi',
            'user' => session()->get('user'),
            'categories' => $categories,
            'fuels' => $fuels
        ];

        return view('admin_pusat/transportation/manajemen_transportasi', $data);
    }

    /**
     * Tambah/Edit Kategori
     */
    public function tambahKategori()
    {
        $db = \Config\Database::connect();
        
        $id = $this->request->getPost('id');
        $nama = $this->request->getPost('nama_kategori');
        $isZev = $this->request->getPost('is_zev') ?? 0;
        
        if (empty($nama)) {
            return redirect()->back()->with('error', 'Nama kategori harus diisi');
        }
        
        try {
            if ($id) {
                // Update
                $db->table('transport_categories')->update([
                    'nama_kategori' => $nama,
                    'is_zev' => $isZev,
                    'updated_at' => date('Y-m-d H:i:s')
                ], ['id' => $id]);
                $message = 'Kategori berhasil diperbarui';
            } else {
                // Insert
                $db->table('transport_categories')->insert([
                    'nama_kategori' => $nama,
                    'is_zev' => $isZev,
                    'status_aktif' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                $message = 'Kategori berhasil ditambahkan';
            }
            
            return redirect()->to('/admin-pusat/transportation/manajemen')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan kategori: ' . $e->getMessage());
        }
    }

    /**
     * Hapus Kategori
     */
    public function hapusKategori($id)
    {
        $db = \Config\Database::connect();
        
        try {
            $db->table('transport_categories')->delete(['id' => $id]);
            return redirect()->to('/admin-pusat/transportation/manajemen')->with('success', 'Kategori berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }

    /**
     * Tambah/Edit Bahan Bakar
     */
    public function tambahBahanBakar()
    {
        $db = \Config\Database::connect();
        
        $id = $this->request->getPost('id');
        $nama = $this->request->getPost('nama_bahan_bakar');
        $isZev = $this->request->getPost('is_zev') ?? 0;
        
        if (empty($nama)) {
            return redirect()->back()->with('error', 'Nama bahan bakar harus diisi');
        }
        
        try {
            if ($id) {
                // Update
                $db->table('transport_fuels')->update([
                    'nama_bahan_bakar' => $nama,
                    'is_zev' => $isZev,
                    'updated_at' => date('Y-m-d H:i:s')
                ], ['id' => $id]);
                $message = 'Bahan bakar berhasil diperbarui';
            } else {
                // Insert
                $db->table('transport_fuels')->insert([
                    'nama_bahan_bakar' => $nama,
                    'is_zev' => $isZev,
                    'status_aktif' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                $message = 'Bahan bakar berhasil ditambahkan';
            }
            
            return redirect()->to('/admin-pusat/transportation/manajemen')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan bahan bakar: ' . $e->getMessage());
        }
    }

    /**
     * Hapus Bahan Bakar
     */
    public function hapusBahanBakar($id)
    {
        $db = \Config\Database::connect();
        
        try {
            $db->table('transport_fuels')->delete(['id' => $id]);
            return redirect()->to('/admin-pusat/transportation/manajemen')->with('success', 'Bahan bakar berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus bahan bakar: ' . $e->getMessage());
        }
    }

    /**
     * Indikator Efisiensi Transportasi
     * Auto-create table if not exist
     */
    public function indikator()
    {
        $db = \Config\Database::connect();
        
        // Auto-create transport_indicators table if not exists
        $db->query("
            CREATE TABLE IF NOT EXISTS transport_indicators (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                kategori_kendaraan VARCHAR(100) NOT NULL,
                target_konsumsi DECIMAL(10,2) NOT NULL,
                satuan VARCHAR(50) NOT NULL,
                tahun VARCHAR(4) NOT NULL,
                status_aktif TINYINT(1) DEFAULT 1,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                INDEX idx_kategori (kategori_kendaraan),
                INDEX idx_tahun (tahun)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        
        // Get categories for dropdown
        $categories = $db->table('transport_categories')
            ->where('status_aktif', 1)
            ->orderBy('nama_kategori', 'ASC')
            ->get()
            ->getResultArray();
        
        // Get all indicators
        $indicators = $db->table('transport_indicators')
            ->orderBy('tahun', 'DESC')
            ->orderBy('kategori_kendaraan', 'ASC')
            ->get()
            ->getResultArray();
        
        $data = [
            'title' => 'Indikator Efisiensi Transportasi',
            'user' => session()->get('user'),
            'categories' => $categories,
            'indicators' => $indicators
        ];

        return view('admin_pusat/transportation/indikator_transportasi', $data);
    }

    /**
     * Simpan/Update Indikator
     */
    public function simpanIndikator()
    {
        $db = \Config\Database::connect();
        
        $id = $this->request->getPost('id');
        $kategori = $this->request->getPost('kategori_kendaraan');
        $target = $this->request->getPost('target_konsumsi');
        $satuan = $this->request->getPost('satuan');
        $tahun = $this->request->getPost('tahun');
        
        // Validation
        if (empty($kategori) || empty($target) || empty($satuan) || empty($tahun)) {
            return redirect()->back()->with('error', 'Semua field harus diisi');
        }
        
        try {
            if ($id) {
                // Update
                $db->table('transport_indicators')->update([
                    'kategori_kendaraan' => $kategori,
                    'target_konsumsi' => $target,
                    'satuan' => $satuan,
                    'tahun' => $tahun,
                    'updated_at' => date('Y-m-d H:i:s')
                ], ['id' => $id]);
                $message = 'Indikator berhasil diperbarui';
            } else {
                // Check if indicator already exists for this category and year
                $existing = $db->table('transport_indicators')
                    ->where('kategori_kendaraan', $kategori)
                    ->where('tahun', $tahun)
                    ->get()
                    ->getRowArray();
                
                if ($existing) {
                    return redirect()->back()->with('error', 'Indikator untuk kategori dan tahun ini sudah ada. Silakan edit yang sudah ada.');
                }
                
                // Insert
                $db->table('transport_indicators')->insert([
                    'kategori_kendaraan' => $kategori,
                    'target_konsumsi' => $target,
                    'satuan' => $satuan,
                    'tahun' => $tahun,
                    'status_aktif' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                $message = 'Indikator berhasil ditambahkan';
            }
            
            return redirect()->to('/admin-pusat/transportation/indikator')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan indikator: ' . $e->getMessage());
        }
    }

    /**
     * Hapus Indikator
     */
    public function hapusIndikator($id)
    {
        $db = \Config\Database::connect();
        
        try {
            $db->table('transport_indicators')->delete(['id' => $id]);
            return redirect()->to('/admin-pusat/transportation/indikator')->with('success', 'Indikator berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus indikator: ' . $e->getMessage());
        }
    }

    /**
     * Analisis & Skor UI GreenMetric - Unified Page with Tabs
     */
    public function analisisSkor()
    {
        $db = \Config\Database::connect();
        
        // Auto-create tables if not exist
        $this->createAnalysisTables($db);
        
        // Get data populasi
        $populasi = $db->table('transport_populasi')
            ->orderBy('tahun', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray() ?? [];
        
        // Calculate total populasi
        $total_populasi = ($populasi['jumlah_mahasiswa'] ?? 0) + 
                         ($populasi['jumlah_dosen'] ?? 0) + 
                         ($populasi['jumlah_staf'] ?? 0);
        
        // Get total kendaraan from transport_stats
        $kendaraanData = $db->table('transport_stats')
            ->selectSum('jumlah_total')
            ->where('jumlah_total IS NOT NULL')
            ->where('jumlah_total !=', '')
            ->get()
            ->getRowArray();
        
        $total_kendaraan = isset($kendaraanData['jumlah_total']) && is_numeric($kendaraanData['jumlah_total']) 
            ? (int)$kendaraanData['jumlah_total'] 
            : 0;
        
        // Get total ZEV
        $zevData = $db->table('transport_stats')
            ->selectSum('jumlah_total')
            ->where('is_zev', 1)
            ->where('jumlah_total IS NOT NULL')
            ->where('jumlah_total !=', '')
            ->get()
            ->getRowArray();
        
        $total_zev = isset($zevData['jumlah_total']) && is_numeric($zevData['jumlah_total']) 
            ? (int)$zevData['jumlah_total'] 
            : 0;
        
        // Calculate rasio kendaraan
        $rasio_kendaraan = $total_populasi > 0 
            ? number_format(($total_kendaraan / $total_populasi) * 1000, 2) 
            : '0.00';
        
        // Get data parkir
        $parkir = $db->table('transport_parkir')
            ->orderBy('tahun', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray() ?? [];
        
        // Calculate rasio parkir
        $rasio_parkir = isset($parkir['luas_parkir']) && isset($parkir['luas_kampus']) && $parkir['luas_kampus'] > 0
            ? number_format(($parkir['luas_parkir'] / $parkir['luas_kampus']) * 100, 2)
            : '0.00';
        
        // Get pedestrian data
        $pedestrian_data = $db->table('transport_pedestrian')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
        
        // Get documents
        $documents = $db->table('transport_documents')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
        
        $data = [
            'title' => 'Analisis & Skor UI GreenMetric Transportation',
            'user' => session()->get('user'),
            'admin' => session()->get('user'), // Add admin variable for view compatibility
            'populasi' => $populasi,
            'total_populasi' => $total_populasi,
            'total_kendaraan' => $total_kendaraan,
            'total_zev' => $total_zev,
            'rasio_kendaraan' => $rasio_kendaraan,
            'parkir' => $parkir,
            'rasio_parkir' => $rasio_parkir,
            'pedestrian_data' => $pedestrian_data,
            'documents' => $documents
        ];

        return view('admin_pusat/transportation/analisis_skor', $data);
    }

    /**
     * Create analysis tables if not exist
     */
    private function createAnalysisTables($db)
    {
        // Table: transport_populasi
        $db->query("
            CREATE TABLE IF NOT EXISTS transport_populasi (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                jumlah_mahasiswa INT(11) NOT NULL,
                jumlah_dosen INT(11) NOT NULL,
                jumlah_staf INT(11) NOT NULL,
                tahun VARCHAR(4) NOT NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                UNIQUE KEY unique_tahun (tahun)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        
        // Table: transport_parkir
        $db->query("
            CREATE TABLE IF NOT EXISTS transport_parkir (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                luas_parkir DECIMAL(10,2) NOT NULL,
                luas_kampus DECIMAL(10,2) NOT NULL,
                tahun VARCHAR(4) NOT NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                UNIQUE KEY unique_tahun (tahun)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        
        // Table: transport_pedestrian
        $db->query("
            CREATE TABLE IF NOT EXISTS transport_pedestrian (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                nama_jalur VARCHAR(200) NOT NULL,
                panjang_jalur DECIMAL(10,2) NOT NULL,
                lebar_jalur DECIMAL(10,2) NOT NULL,
                kondisi ENUM('Baik', 'Rusak Ringan', 'Rusak Berat') NOT NULL,
                foto_kondisi VARCHAR(255) NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        
        // Add foto_kondisi column if not exists (for existing tables)
        $columns = $db->query("SHOW COLUMNS FROM transport_pedestrian LIKE 'foto_kondisi'")->getResult();
        if (empty($columns)) {
            $db->query("ALTER TABLE transport_pedestrian ADD COLUMN foto_kondisi VARCHAR(255) NULL AFTER kondisi");
        }
        
        // Table: transport_documents
        $db->query("
            CREATE TABLE IF NOT EXISTS transport_documents (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                kategori VARCHAR(100) NOT NULL,
                nama_dokumen VARCHAR(200) NOT NULL,
                deskripsi TEXT NULL,
                tahun VARCHAR(4) NOT NULL,
                file_name VARCHAR(255) NOT NULL,
                file_path VARCHAR(255) NOT NULL,
                tipe_file VARCHAR(10) NOT NULL,
                ukuran_file INT(11) NOT NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                INDEX idx_kategori (kategori),
                INDEX idx_tahun (tahun)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    /**
     * Simpan Data Populasi
     */
    public function simpanPopulasi()
    {
        $db = \Config\Database::connect();
        
        $mahasiswa = $this->request->getPost('jumlah_mahasiswa');
        $dosen = $this->request->getPost('jumlah_dosen');
        $staf = $this->request->getPost('jumlah_staf');
        $tahun = $this->request->getPost('tahun');
        
        if (empty($mahasiswa) || empty($dosen) || empty($staf) || empty($tahun)) {
            return redirect()->back()->with('error', 'Semua field harus diisi');
        }
        
        try {
            // Check if data for this year exists
            $existing = $db->table('transport_populasi')->where('tahun', $tahun)->get()->getRowArray();
            
            if ($existing) {
                // Update
                $db->table('transport_populasi')->update([
                    'jumlah_mahasiswa' => $mahasiswa,
                    'jumlah_dosen' => $dosen,
                    'jumlah_staf' => $staf,
                    'updated_at' => date('Y-m-d H:i:s')
                ], ['tahun' => $tahun]);
                $message = 'Data populasi berhasil diperbarui';
            } else {
                // Insert
                $db->table('transport_populasi')->insert([
                    'jumlah_mahasiswa' => $mahasiswa,
                    'jumlah_dosen' => $dosen,
                    'jumlah_staf' => $staf,
                    'tahun' => $tahun,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                $message = 'Data populasi berhasil ditambahkan';
            }
            
            return redirect()->to('/admin-pusat/transportation/analisis-skor?tab=populasi')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Simpan Data Parkir
     */
    public function simpanParkir()
    {
        $db = \Config\Database::connect();
        
        $luas_parkir = $this->request->getPost('luas_parkir');
        $luas_kampus = $this->request->getPost('luas_kampus');
        $tahun = $this->request->getPost('tahun');
        
        if (empty($luas_parkir) || empty($luas_kampus) || empty($tahun)) {
            return redirect()->back()->with('error', 'Semua field harus diisi');
        }
        
        try {
            $existing = $db->table('transport_parkir')->where('tahun', $tahun)->get()->getRowArray();
            
            if ($existing) {
                $db->table('transport_parkir')->update([
                    'luas_parkir' => $luas_parkir,
                    'luas_kampus' => $luas_kampus,
                    'updated_at' => date('Y-m-d H:i:s')
                ], ['tahun' => $tahun]);
                $message = 'Data parkir berhasil diperbarui';
            } else {
                $db->table('transport_parkir')->insert([
                    'luas_parkir' => $luas_parkir,
                    'luas_kampus' => $luas_kampus,
                    'tahun' => $tahun,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                $message = 'Data parkir berhasil ditambahkan';
            }
            
            return redirect()->to('/admin-pusat/transportation/analisis-skor?tab=infrastruktur')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Simpan Data Pedestrian (with Photo Upload)
     */
    public function simpanPedestrian()
    {
        $db = \Config\Database::connect();
        
        $id = $this->request->getPost('id');
        $nama_jalur = $this->request->getPost('nama_jalur');
        $panjang_jalur = $this->request->getPost('panjang_jalur');
        $lebar_jalur = $this->request->getPost('lebar_jalur');
        $kondisi = $this->request->getPost('kondisi');
        
        if (empty($nama_jalur) || empty($panjang_jalur) || empty($lebar_jalur) || empty($kondisi)) {
            return redirect()->back()->with('error', 'Semua field harus diisi');
        }
        
        try {
            // Prepare data array
            $data = [
                'nama_jalur' => $nama_jalur,
                'panjang_jalur' => $panjang_jalur,
                'lebar_jalur' => $lebar_jalur,
                'kondisi' => $kondisi,
            ];
            
            // Handle file upload if present
            $file = $this->request->getFile('foto_jalur');
            if ($file && $file->isValid() && !$file->hasMoved()) {
                // Validate file
                if ($file->getSize() > 5 * 1024 * 1024) {
                    return redirect()->back()->with('error', 'Ukuran foto maksimal 5MB');
                }
                
                $allowedTypes = ['jpg', 'jpeg', 'png'];
                if (!in_array(strtolower($file->getClientExtension()), $allowedTypes)) {
                    return redirect()->back()->with('error', 'Format foto harus JPG, JPEG, atau PNG');
                }
                
                // If updating, delete old photo
                if ($id) {
                    $existing = $db->table('transport_pedestrian')->where('id', $id)->get()->getRowArray();
                    if ($existing && !empty($existing['foto_kondisi'])) {
                        $oldPhotoPath = FCPATH . 'uploads/pedestrian/' . $existing['foto_kondisi'];
                        if (file_exists($oldPhotoPath)) {
                            unlink($oldPhotoPath);
                        }
                    }
                }
                
                // Generate unique filename
                $newName = $file->getRandomName();
                
                // Create upload directory if not exists
                $uploadPath = FCPATH . 'uploads/pedestrian/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                // Move file to upload directory
                $file->move($uploadPath, $newName);
                
                // Add filename to data
                $data['foto_kondisi'] = $newName;
            }
            
            // Save to database
            if ($id) {
                $data['updated_at'] = date('Y-m-d H:i:s');
                $db->table('transport_pedestrian')->update($data, ['id' => $id]);
                $message = 'Data jalur pedestrian berhasil diperbarui';
            } else {
                $data['created_at'] = date('Y-m-d H:i:s');
                $db->table('transport_pedestrian')->insert($data);
                $message = 'Data jalur pedestrian berhasil ditambahkan';
            }
            
            return redirect()->to('/admin-pusat/transportation/analisis-skor?tab=infrastruktur')->with('success', $message);
        } catch (\Exception $e) {
            log_message('error', 'Simpan pedestrian error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Hapus Data Pedestrian (with Photo Deletion)
     */
    public function hapusPedestrian($id)
    {
        $db = \Config\Database::connect();
        
        try {
            // Get pedestrian data to check for photo
            $pedestrian = $db->table('transport_pedestrian')->where('id', $id)->get()->getRowArray();
            
            if ($pedestrian && !empty($pedestrian['foto_kondisi'])) {
                // Delete photo file if exists
                $photoPath = FCPATH . 'uploads/pedestrian/' . $pedestrian['foto_kondisi'];
                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }
            }
            
            // Delete database record
            $db->table('transport_pedestrian')->delete(['id' => $id]);
            return redirect()->to('/admin-pusat/transportation/analisis-skor?tab=infrastruktur')->with('success', 'Data jalur pedestrian berhasil dihapus');
        } catch (\Exception $e) {
            log_message('error', 'Hapus pedestrian error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Upload Dokumen
     */
    public function uploadDokumen()
    {
        $db = \Config\Database::connect();
        
        // Validation rules
        $rules = [
            'kategori' => 'required',
            'nama_dokumen' => 'required|max_length[200]',
            'tahun' => 'required|exact_length[4]',
            'file_dokumen' => [
                'rules' => 'uploaded[file_dokumen]|max_size[file_dokumen,5120]|ext_in[file_dokumen,pdf,jpg,jpeg,png]',
                'errors' => [
                    'uploaded' => 'File dokumen harus diupload',
                    'max_size' => 'Ukuran file maksimal 5MB',
                    'ext_in' => 'Format file harus PDF, JPG, JPEG, atau PNG'
                ]
            ]
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', implode('<br>', $this->validator->getErrors()));
        }
        
        try {
            $file = $this->request->getFile('file_dokumen');
            
            if (!$file->isValid()) {
                throw new \Exception('File tidak valid');
            }
            
            // Generate unique filename
            $newName = $file->getRandomName();
            
            // Create upload directory if not exists
            $uploadPath = WRITEPATH . 'uploads/transportation/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            // Move file to upload directory
            $file->move($uploadPath, $newName);
            
            // Get file info
            $fileExtension = $file->getClientExtension();
            $fileSize = $file->getSize();
            
            // Save to database
            $data = [
                'kategori' => $this->request->getPost('kategori'),
                'nama_dokumen' => $this->request->getPost('nama_dokumen'),
                'deskripsi' => $this->request->getPost('deskripsi') ?? '',
                'tahun' => $this->request->getPost('tahun'),
                'file_name' => $newName,
                'file_path' => $uploadPath . $newName,
                'tipe_file' => strtolower($fileExtension),
                'ukuran_file' => $fileSize,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $db->table('transport_documents')->insert($data);
            
            return redirect()->to('/admin-pusat/transportation/analisis-skor?tab=earchive')
                ->with('success', 'Dokumen berhasil diupload');
                
        } catch (\Exception $e) {
            log_message('error', 'Upload dokumen error: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupload dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Download Dokumen
     */
    public function downloadDokumen($id)
    {
        $db = \Config\Database::connect();
        
        try {
            $dokumen = $db->table('transport_documents')
                ->where('id', $id)
                ->get()
                ->getRowArray();
            
            if (!$dokumen) {
                return redirect()->back()->with('error', 'Dokumen tidak ditemukan');
            }
            
            $filePath = $dokumen['file_path'];
            
            if (!file_exists($filePath)) {
                return redirect()->back()->with('error', 'File tidak ditemukan di server');
            }
            
            // Force download
            return $this->response->download($filePath, null)->setFileName($dokumen['file_name']);
            
        } catch (\Exception $e) {
            log_message('error', 'Download dokumen error: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Gagal mendownload dokumen');
        }
    }

    /**
     * Hapus Dokumen
     */
    public function hapusDokumen($id)
    {
        $db = \Config\Database::connect();
        
        try {
            $dokumen = $db->table('transport_documents')
                ->where('id', $id)
                ->get()
                ->getRowArray();
            
            if (!$dokumen) {
                return redirect()->to('/admin-pusat/transportation/analisis-skor?tab=earchive')
                    ->with('error', 'Dokumen tidak ditemukan');
            }
            
            // Delete file from server
            $filePath = $dokumen['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            // Delete from database
            $db->table('transport_documents')->delete(['id' => $id]);
            
            return redirect()->to('/admin-pusat/transportation/analisis-skor?tab=earchive')
                ->with('success', 'Dokumen berhasil dihapus');
                
        } catch (\Exception $e) {
            log_message('error', 'Hapus dokumen error: ' . $e->getMessage());
            
            return redirect()->to('/admin-pusat/transportation/analisis-skor?tab=earchive')
                ->with('error', 'Gagal menghapus dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Transportation Statistics - Laporan Statistik page
     */
    public function statistics()
    {
        $data = [
            'title' => 'Laporan Statistik Transportasi',
            'user' => session()->get('user'),
            'transport_stats' => $this->getTransportStats(),
            'summary_stats' => $this->getSummaryStats(),
            'recent_entries' => $this->getRecentEntries()
        ];

        return view('admin_pusat/transportation/statistics', $data);
    }

    /**
     * Get all transport records with filters
     */
    private function getAllRecords()
    {
        $builder = $this->transportStatsModel
            ->select('transport_stats.*, users.nama_lengkap as petugas_nama, users.unit_id')
            ->join('users', 'users.id = transport_stats.input_by', 'left');

        // Apply filters if provided
        $filters = $this->request->getGet();
        
        if (!empty($filters['unit'])) {
            $builder->where('users.unit_id', $filters['unit']);
        }
        
        if (!empty($filters['kategori'])) {
            $builder->where('transport_stats.kategori_kendaraan', $filters['kategori']);
        }
        
        if (!empty($filters['periode'])) {
            $builder->where('transport_stats.periode', $filters['periode']);
        }
        
        if (!empty($filters['bulan'])) {
            $builder->where('transport_stats.bulan', $filters['bulan']);
        }
        
        if (!empty($filters['tahun'])) {
            $builder->where('transport_stats.tahun', $filters['tahun']);
        }

        return $builder->orderBy('transport_stats.created_at', 'DESC')->findAll();
    }

    /**
     * Get all security officers for filter
     */
    private function getSecurityOfficers()
    {
        return $this->userModel
            ->where('role', 'security')
            ->findAll();
    }

    /**
     * Edit transport record
     */
    public function edit($id)
    {
        $record = $this->transportStatsModel->find($id);
        
        if (!$record) {
            return redirect()->to('/admin-pusat/transportation')
                ->with('error', 'Data tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Data Kendaraan',
            'user' => session()->get('user'),
            'edit_data' => $record
        ];

        return view('admin_pusat/transportation/edit', $data);
    }

    /**
     * Update transport record
     */
    public function update($id)
    {
        $rules = [
            'periode' => 'required',
            'kategori_kendaraan' => 'required',
            'jenis_bahan_bakar' => 'required',
            'jumlah_total' => 'required|integer|greater_than[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'periode' => $this->request->getPost('periode'),
            'kategori_kendaraan' => $this->request->getPost('kategori_kendaraan'),
            'jenis_bahan_bakar' => $this->request->getPost('jenis_bahan_bakar'),
            'jumlah_total' => $this->request->getPost('jumlah_total'),
            'is_zev' => in_array($this->request->getPost('jenis_bahan_bakar'), ['Listrik', 'Non-BBM']) ? 1 : 0,
            'is_shuttle' => $this->request->getPost('is_shuttle') ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        try {
            $this->transportStatsModel->update($id, $data);
            
            return redirect()->to('/admin-pusat/transportation')
                ->with('success', 'Data kendaraan berhasil diperbarui');
        } catch (\Exception $e) {
            log_message('error', 'Transport update error: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data. Silakan coba lagi.');
        }
    }

    /**
     * Delete transport record
     */
    public function delete($id)
    {
        try {
            $record = $this->transportStatsModel->find($id);
            
            if (!$record) {
                return redirect()->to('/admin-pusat/transportation')
                    ->with('error', 'Data tidak ditemukan');
            }
            
            $this->transportStatsModel->delete($id);
            
            return redirect()->to('/admin-pusat/transportation')
                ->with('success', 'Data kendaraan berhasil dihapus');
        } catch (\Exception $e) {
            log_message('error', 'Transport delete error: ' . $e->getMessage());
            
            return redirect()->to('/admin-pusat/transportation')
                ->with('error', 'Gagal menghapus data. Silakan coba lagi.');
        }
    }

    /**
     * Get all transport statistics
     */
    private function getTransportStats()
    {
        return $this->transportStatsModel
            ->select('transport_stats.*, users.nama_lengkap as petugas_nama')
            ->join('users', 'users.id = transport_stats.input_by', 'left')
            ->orderBy('transport_stats.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get summary statistics
     */
    private function getSummaryStats()
    {
        $db = \Config\Database::connect();
        
        // Total vehicles by category
        $categoryStats = $db->table('transport_stats')
            ->select('kategori_kendaraan, SUM(CAST(jumlah_total AS UNSIGNED)) as total')
            ->where('jumlah_total IS NOT NULL')
            ->where('jumlah_total !=', '')
            ->groupBy('kategori_kendaraan')
            ->get()
            ->getResultArray();

        // Total vehicles by fuel type
        $fuelStats = $db->table('transport_stats')
            ->select('jenis_bahan_bakar, SUM(CAST(jumlah_total AS UNSIGNED)) as total')
            ->where('jumlah_total IS NOT NULL')
            ->where('jumlah_total !=', '')
            ->groupBy('jenis_bahan_bakar')
            ->get()
            ->getResultArray();

        // Overall totals with proper validation
        $totalVehiclesResult = $db->table('transport_stats')
            ->selectSum('jumlah_total')
            ->where('jumlah_total IS NOT NULL')
            ->where('jumlah_total !=', '')
            ->get()
            ->getRowArray();
        
        $totalVehicles = isset($totalVehiclesResult['jumlah_total']) && is_numeric($totalVehiclesResult['jumlah_total']) 
            ? (int)$totalVehiclesResult['jumlah_total'] 
            : 0;

        $totalEntries = $db->table('transport_stats')
            ->where('jumlah_total IS NOT NULL')
            ->where('jumlah_total !=', '')
            ->countAllResults();

        $totalSecurityOfficers = $db->table('transport_stats')
            ->select('input_by')
            ->where('jumlah_total IS NOT NULL')
            ->where('jumlah_total !=', '')
            ->distinct()
            ->countAllResults();

        // ZEV Statistics with validation
        $totalZevResult = $db->table('transport_stats')
            ->selectSum('jumlah_total')
            ->where('is_zev', 1)
            ->where('jumlah_total IS NOT NULL')
            ->where('jumlah_total !=', '')
            ->get()
            ->getRowArray();
            
        $totalZev = isset($totalZevResult['jumlah_total']) && is_numeric($totalZevResult['jumlah_total']) 
            ? (int)$totalZevResult['jumlah_total'] 
            : 0;

        // Shuttle Statistics with validation
        $totalShuttleResult = $db->table('transport_stats')
            ->selectSum('jumlah_total')
            ->where('is_shuttle', 1)
            ->where('jumlah_total IS NOT NULL')
            ->where('jumlah_total !=', '')
            ->get()
            ->getRowArray();
            
        $totalShuttle = isset($totalShuttleResult['jumlah_total']) && is_numeric($totalShuttleResult['jumlah_total']) 
            ? (int)$totalShuttleResult['jumlah_total'] 
            : 0;

        // Safe percentage calculations
        $zevPercentage = ($totalVehicles > 0 && $totalZev > 0) 
            ? round(($totalZev / $totalVehicles) * 100, 2) 
            : 0;
            
        $shuttlePercentage = ($totalVehicles > 0 && $totalShuttle > 0) 
            ? round(($totalShuttle / $totalVehicles) * 100, 2) 
            : 0;

        return [
            'total_vehicles' => $totalVehicles,
            'total_entries' => $totalEntries,
            'total_officers' => $totalSecurityOfficers,
            'total_zev' => $totalZev,
            'total_shuttle' => $totalShuttle,
            'zev_percentage' => $zevPercentage,
            'shuttle_percentage' => $shuttlePercentage,
            'category_breakdown' => $categoryStats,
            'fuel_breakdown' => $fuelStats
        ];
    }

    /**
     * Get recent entries for dashboard widget
     */
    private function getRecentEntries($limit = 5)
    {
        return $this->transportStatsModel
            ->select('transport_stats.*, users.nama_lengkap as petugas_nama')
            ->join('users', 'users.id = transport_stats.input_by', 'left')
            ->orderBy('transport_stats.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Export transportation data to PDF
     */
    public function exportPdf()
    {
        $data = [
            'title' => 'Laporan Data Kendaraan Kampus',
            'user' => session()->get('user'),
            'all_records' => $this->getAllRecords(),
            'summary_stats' => $this->getSummaryStats(),
            'generated_at' => date('d/m/Y H:i:s')
        ];

        return view('admin_pusat/transportation/export_pdf', $data);
    }

    /**
     * Export transportation data to Excel
     */
    public function exportExcel()
    {
        $allRecords = $this->getAllRecords();
        $summaryStats = $this->getSummaryStats();

        // Set headers for Excel download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Laporan_Data_Kendaraan_Kampus_' . date('Y-m-d') . '.xls"');
        header('Cache-Control: max-age=0');

        echo '<table border="1">';
        echo '<tr>';
        echo '<th colspan="8" style="text-align: center; font-weight: bold; font-size: 16px;">LAPORAN DATA KENDARAAN KAMPUS</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th colspan="8" style="text-align: center;">Politeknik Negeri Bandung - UI GreenMetric</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th colspan="8" style="text-align: center;">Tanggal Export: ' . date('d/m/Y H:i:s') . '</th>';
        echo '</tr>';
        echo '<tr><th></th></tr>'; // Empty row

        // Summary section
        echo '<tr>';
        echo '<th colspan="8" style="background-color: #1e3c72; color: white; text-align: center;">RINGKASAN STATISTIK</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<td><strong>Total Kendaraan Terdaftar:</strong></td>';
        echo '<td colspan="7">' . number_format($summaryStats['total_vehicles']) . ' Unit</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td><strong>Total Entry Data:</strong></td>';
        echo '<td colspan="7">' . number_format($summaryStats['total_entries']) . ' Record</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td><strong>Petugas Security Aktif:</strong></td>';
        echo '<td colspan="7">' . $summaryStats['total_officers'] . ' Orang</td>';
        echo '</tr>';
        echo '<tr><th></th></tr>'; // Empty row

        // Data table
        echo '<tr>';
        echo '<th colspan="8" style="background-color: #1e3c72; color: white; text-align: center;">DATA DETAIL</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th>No</th>';
        echo '<th>Tanggal</th>';
        echo '<th>Periode</th>';
        echo '<th>Kategori Kendaraan</th>';
        echo '<th>Jenis Bahan Bakar</th>';
        echo '<th>Jumlah Total</th>';
        echo '<th>ZEV</th>';
        echo '<th>Petugas Input</th>';
        echo '</tr>';

        foreach ($allRecords as $index => $row) {
            // Format tanggal
            $tanggal = '-';
            if ($row['periode'] === 'Harian' && !empty($row['tanggal_pencatatan'])) {
                $tanggal = date('d/m/Y', strtotime($row['tanggal_pencatatan']));
            } elseif ($row['periode'] === 'Bulanan (Back-up)' && !empty($row['bulan']) && !empty($row['tahun'])) {
                $tanggal = $row['bulan'] . ' ' . $row['tahun'];
            }

            echo '<tr>';
            echo '<td>' . ($index + 1) . '</td>';
            echo '<td>' . $tanggal . '</td>';
            echo '<td>' . esc($row['periode']) . '</td>';
            echo '<td>' . esc($row['kategori_kendaraan']) . '</td>';
            echo '<td>' . esc($row['jenis_bahan_bakar']) . '</td>';
            echo '<td>' . number_format($row['jumlah_total']) . '</td>';
            echo '<td>' . ($row['is_zev'] == 1 ? 'Ya' : 'Tidak') . '</td>';
            echo '<td>' . esc($row['petugas_nama']) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        exit;
    }

    /**
     * Monthly Summary Report - Ringkasan Bulanan (Informatif)
     * Mengelompokkan data berdasarkan Bulan dan Kategori Kendaraan
     */
    public function summary()
    {
        $data = [
            'title' => 'Laporan Monitoring Kendaraan - Ringkasan Bulanan',
            'user' => session()->get('user'),
            'monthly_summary' => $this->getMonthlySummary(),
            'chart_data' => $this->getChartData(),
            'summary_stats' => $this->getSummaryStats()
        ];

        return view('admin_pusat/transportation/summary_report', $data);
    }

    /**
     * Get monthly summary grouped by month and vehicle category
     * Fixed for sql_mode=only_full_group_by compatibility
     */
    private function getMonthlySummary()
    {
        $db = \Config\Database::connect();
        
        // Query yang kompatibel dengan only_full_group_by
        // Hanya gunakan fungsi agregasi dan kolom yang ada di GROUP BY
        $query = "
            SELECT 
                YEAR(created_at) as tahun,
                MONTH(created_at) as bulan_num,
                kategori_kendaraan,
                SUM(jumlah_total) as total_unit,
                SUM(CASE WHEN is_zev = 1 THEN jumlah_total ELSE 0 END) as total_zev,
                ROUND(
                    (SUM(CASE WHEN is_zev = 1 THEN jumlah_total ELSE 0 END) / 
                    NULLIF(SUM(jumlah_total), 0) * 100), 
                    2
                ) as persentase_zev,
                COUNT(*) as jumlah_entry
            FROM transport_stats
            WHERE jumlah_total IS NOT NULL 
            AND jumlah_total != ''
            GROUP BY YEAR(created_at), MONTH(created_at), kategori_kendaraan
            ORDER BY YEAR(created_at) DESC, MONTH(created_at) DESC, kategori_kendaraan ASC
        ";
        
        return $db->query($query)->getResultArray();
    }

    /**
     * Get chart data for monthly trend visualization
     * Fixed for sql_mode=only_full_group_by compatibility
     */
    private function getChartData()
    {
        $db = \Config\Database::connect();
        
        // Data untuk grafik batang - Total kendaraan per bulan
        // Hanya gunakan kolom yang ada di GROUP BY atau fungsi agregasi
        $query = "
            SELECT 
                YEAR(created_at) as tahun,
                MONTH(created_at) as bulan,
                SUM(jumlah_total) as total_kendaraan
            FROM transport_stats
            WHERE jumlah_total IS NOT NULL 
            AND jumlah_total != ''
            AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY YEAR(created_at), MONTH(created_at)
            ORDER BY YEAR(created_at) ASC, MONTH(created_at) ASC
        ";
        
        $monthlyTrend = $db->query($query)->getResultArray();
        
        // Data untuk breakdown kategori per bulan (untuk stacked bar chart)
        $queryCategory = "
            SELECT 
                YEAR(created_at) as tahun,
                MONTH(created_at) as bulan,
                kategori_kendaraan,
                SUM(jumlah_total) as total
            FROM transport_stats
            WHERE jumlah_total IS NOT NULL 
            AND jumlah_total != ''
            AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY YEAR(created_at), MONTH(created_at), kategori_kendaraan
            ORDER BY YEAR(created_at) ASC, MONTH(created_at) ASC
        ";
        
        $categoryBreakdown = $db->query($queryCategory)->getResultArray();
        
        return [
            'monthly_trend' => $monthlyTrend,
            'category_breakdown' => $categoryBreakdown
        ];
    }

    /**
     * Get transport stats for dashboard widget
     */
    public function getDashboardWidget()
    {
        return [
            'recent_entries' => $this->getRecentEntries(3),
            'summary_stats' => $this->getSummaryStats()
        ];
    }

    /**
     * Laporan Transportasi - Comprehensive Report with Filters
     * IDENTIK dengan struktur Laporan Waste Management
     */
    public function laporan()
    {
        // Get filter parameters (removed unit_id filter)
        $filters = [
            'start_date' => $this->request->getGet('start_date') ?? '',
            'end_date' => $this->request->getGet('end_date') ?? '',
            'kategori_kendaraan' => $this->request->getGet('kategori_kendaraan') ?? ''
        ];
        
        // Get comprehensive report data
        $reportData = $this->getComprehensiveReportData($filters);
        
        $data = [
            'title' => 'Laporan Data Transportasi Kampus',
            'user' => session()->get('user'),
            'filters' => $filters,
            'summary' => $reportData['summary'],
            'rekap_kategori' => $reportData['rekap_kategori'],
            'rekap_bahan_bakar' => $reportData['rekap_bahan_bakar'],
            'rekap_bulanan' => $reportData['rekap_bulanan']
        ];

        return view('admin_pusat/transportation/laporan_transportasi', $data);
    }

    /**
     * Get comprehensive filtered report data
     * Struktur identik dengan Laporan Waste
     */
    private function getComprehensiveReportData(array $filters)
    {
        $db = \Config\Database::connect();
        
        // Build base query with filters
        $baseBuilder = $db->table('transport_stats');
        
        // Apply date filters
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $baseBuilder->groupStart();
                // Harian: filter by tanggal_pencatatan
                $baseBuilder->groupStart();
                    $baseBuilder->where('transport_stats.periode', 'Harian');
                    $baseBuilder->where('transport_stats.tanggal_pencatatan >=', $filters['start_date']);
                    $baseBuilder->where('transport_stats.tanggal_pencatatan <=', $filters['end_date']);
                $baseBuilder->groupEnd();
                
                // Mingguan: filter by tanggal_mulai or tanggal_selesai
                $baseBuilder->orGroupStart();
                    $baseBuilder->where('transport_stats.periode', 'Mingguan (Back-up)');
                    $baseBuilder->where('transport_stats.tanggal_mulai >=', $filters['start_date']);
                    $baseBuilder->where('transport_stats.tanggal_selesai <=', $filters['end_date']);
                $baseBuilder->groupEnd();
                
                // Bulanan: filter by created_at (fallback)
                $baseBuilder->orGroupStart();
                    $baseBuilder->where('transport_stats.periode', 'Bulanan (Back-up)');
                    $baseBuilder->where('transport_stats.created_at >=', $filters['start_date']);
                    $baseBuilder->where('transport_stats.created_at <=', $filters['end_date']);
                $baseBuilder->groupEnd();
            $baseBuilder->groupEnd();
        }
        
        // Apply kategori filter
        if (!empty($filters['kategori_kendaraan'])) {
            $baseBuilder->where('transport_stats.kategori_kendaraan', $filters['kategori_kendaraan']);
        }
        
        // Get all filtered records (removed unit filter and join)
        $allRecords = $baseBuilder->select('transport_stats.*, users.nama_lengkap as petugas_nama')
            ->join('users', 'users.id = transport_stats.input_by', 'left')
            ->get()
            ->getResultArray();
        
        // SUMMARY CARDS
        $totalVehicles = array_sum(array_column($allRecords, 'jumlah_total'));
        $totalZev = array_sum(array_map(function($row) {
            return $row['is_zev'] == 1 ? $row['jumlah_total'] : 0;
        }, $allRecords));
        $totalNonZev = $totalVehicles - $totalZev;
        $persentaseKeberlanjutan = $totalVehicles > 0 ? round(($totalZev / $totalVehicles) * 100, 2) : 0;
        
        // REKAP PER KATEGORI KENDARAAN (Tabel A - Biru)
        $rekapKategori = [];
        foreach ($allRecords as $row) {
            $kategori = $row['kategori_kendaraan'];
            if (!isset($rekapKategori[$kategori])) {
                $rekapKategori[$kategori] = [
                    'kategori' => $kategori,
                    'total_transaksi' => 0,
                    'total_unit' => 0
                ];
            }
            $rekapKategori[$kategori]['total_transaksi']++;
            $rekapKategori[$kategori]['total_unit'] += $row['jumlah_total'];
        }
        $rekapKategori = array_values($rekapKategori);
        usort($rekapKategori, function($a, $b) {
            return $b['total_unit'] - $a['total_unit'];
        });
        
        // REKAP PER BAHAN BAKAR (Tabel B - Hijau)
        $rekapBahanBakar = [];
        foreach ($allRecords as $row) {
            $bahanBakar = $row['jenis_bahan_bakar'];
            if (!isset($rekapBahanBakar[$bahanBakar])) {
                $rekapBahanBakar[$bahanBakar] = [
                    'bahan_bakar' => $bahanBakar,
                    'total_transaksi' => 0,
                    'total_unit' => 0
                ];
            }
            $rekapBahanBakar[$bahanBakar]['total_transaksi']++;
            $rekapBahanBakar[$bahanBakar]['total_unit'] += $row['jumlah_total'];
        }
        $rekapBahanBakar = array_values($rekapBahanBakar);
        usort($rekapBahanBakar, function($a, $b) {
            return $b['total_unit'] - $a['total_unit'];
        });
        
        // REKAP BULANAN (Tabel D - Cyan)
        // Group by MONTH and YEAR (not by week)
        $rekapBulanan = [];
        $monthNames = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        
        foreach ($allRecords as $row) {
            // Determine date for grouping
            $date = null;
            if ($row['periode'] === 'Harian' && !empty($row['tanggal_pencatatan'])) {
                $date = $row['tanggal_pencatatan'];
            } elseif ($row['periode'] === 'Mingguan (Back-up)' && !empty($row['tanggal_mulai'])) {
                $date = $row['tanggal_mulai'];
            } elseif ($row['periode'] === 'Bulanan (Back-up)' && !empty($row['bulan']) && !empty($row['tahun'])) {
                // For Bulanan records, use bulan and tahun columns directly
                $monthNum = array_search($row['bulan'], $monthNames);
                if ($monthNum !== false) {
                    $date = $row['tahun'] . '-' . $monthNum . '-01';
                }
            } elseif (!empty($row['created_at'])) {
                $date = date('Y-m-d', strtotime($row['created_at']));
            }
            
            if ($date) {
                $year = date('Y', strtotime($date));
                $month = date('m', strtotime($date));
                $key = $year . '-' . $month;
                
                if (!isset($rekapBulanan[$key])) {
                    $rekapBulanan[$key] = [
                        'tahun' => $year,
                        'bulan' => $month,
                        'periode' => $monthNames[$month] . ' ' . $year,
                        'total_kendaraan' => 0,
                        'total_transaksi' => 0
                    ];
                }
                $rekapBulanan[$key]['total_kendaraan'] += $row['jumlah_total'];
                $rekapBulanan[$key]['total_transaksi']++;
            }
        }
        
        // Sort by year and month (newest first)
        $rekapBulanan = array_values($rekapBulanan);
        usort($rekapBulanan, function($a, $b) {
            $dateA = $a['tahun'] . $a['bulan'];
            $dateB = $b['tahun'] . $b['bulan'];
            return strcmp($dateB, $dateA); // Descending order (newest first)
        });
        
        return [
            'summary' => [
                'total_kendaraan' => $totalVehicles,
                'total_zev' => $totalZev,
                'total_non_zev' => $totalNonZev,
                'persentase_keberlanjutan' => $persentaseKeberlanjutan,
                'total_transaksi' => count($allRecords)
            ],
            'rekap_kategori' => $rekapKategori,
            'rekap_bahan_bakar' => $rekapBahanBakar,
            'rekap_bulanan' => $rekapBulanan
        ];
    }

    /**
     * Get Detail Laporan (AJAX)
     * Return detailed records based on filter type with summary statistics
     */
    public function getDetailLaporan()
    {
        // Only accept POST requests
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }
        
        try {
            $db = \Config\Database::connect();
            
            // Get parameters
            $type = $this->request->getPost('type'); // kategori, bahan_bakar, periode
            $value = $this->request->getPost('value');
            $startDate = $this->request->getPost('start_date');
            $endDate = $this->request->getPost('end_date');
            
            // Build query - transport_stats hanya punya data agregat, tidak ada nama_kendaraan
            $builder = $db->table('transport_stats');
            $builder->select('
                transport_stats.id,
                transport_stats.kategori_kendaraan,
                transport_stats.jenis_bahan_bakar,
                transport_stats.status_kendaraan,
                transport_stats.jumlah_total,
                transport_stats.periode,
                transport_stats.tanggal_pencatatan,
                transport_stats.bulan,
                transport_stats.tahun,
                transport_stats.is_zev,
                users.nama_lengkap as petugas_nama,
                DATE_FORMAT(transport_stats.created_at, "%d-%m-%Y %H:%i") as tanggal_input
            ');
            $builder->join('users', 'users.id = transport_stats.input_by', 'left');
            
            // Apply type filter
            if ($type === 'kategori') {
                $builder->where('transport_stats.kategori_kendaraan', $value);
            } elseif ($type === 'bahan_bakar') {
                $builder->where('transport_stats.jenis_bahan_bakar', $value);
            } elseif ($type === 'periode') {
                // Parse periode (e.g., "Januari 2026")
                $parts = explode(' ', $value);
                if (count($parts) === 2) {
                    $monthNames = [
                        'Januari' => '01', 'Februari' => '02', 'Maret' => '03', 'April' => '04',
                        'Mei' => '05', 'Juni' => '06', 'Juli' => '07', 'Agustus' => '08',
                        'September' => '09', 'Oktober' => '10', 'November' => '11', 'Desember' => '12'
                    ];
                    
                    $monthName = $parts[0];
                    $year = $parts[1];
                    
                    if (isset($monthNames[$monthName])) {
                        $month = $monthNames[$monthName];
                        
                        // Filter by month and year
                        $builder->groupStart();
                            // Harian: filter by tanggal_pencatatan
                            $builder->groupStart();
                                $builder->where('transport_stats.periode', 'Harian');
                                $builder->where('YEAR(transport_stats.tanggal_pencatatan)', $year);
                                $builder->where('MONTH(transport_stats.tanggal_pencatatan)', $month);
                            $builder->groupEnd();
                            
                            // Mingguan: filter by tanggal_mulai
                            $builder->orGroupStart();
                                $builder->where('transport_stats.periode', 'Mingguan (Back-up)');
                                $builder->where('YEAR(transport_stats.tanggal_mulai)', $year);
                                $builder->where('MONTH(transport_stats.tanggal_mulai)', $month);
                            $builder->groupEnd();
                            
                            // Bulanan: filter by bulan and tahun columns
                            $builder->orGroupStart();
                                $builder->where('transport_stats.periode', 'Bulanan (Back-up)');
                                $builder->where('transport_stats.bulan', $monthName);
                                $builder->where('transport_stats.tahun', $year);
                            $builder->groupEnd();
                        $builder->groupEnd();
                    }
                }
            }
            
            // Apply date range filter
            if (!empty($startDate) && !empty($endDate)) {
                $builder->groupStart();
                    // Harian: filter by tanggal_pencatatan
                    $builder->groupStart();
                        $builder->where('transport_stats.periode', 'Harian');
                        $builder->where('transport_stats.tanggal_pencatatan >=', $startDate);
                        $builder->where('transport_stats.tanggal_pencatatan <=', $endDate);
                    $builder->groupEnd();
                    
                    // Mingguan: filter by tanggal_mulai or tanggal_selesai
                    $builder->orGroupStart();
                        $builder->where('transport_stats.periode', 'Mingguan (Back-up)');
                        $builder->where('transport_stats.tanggal_mulai >=', $startDate);
                        $builder->where('transport_stats.tanggal_selesai <=', $endDate);
                    $builder->groupEnd();
                    
                    // Bulanan: filter by created_at (fallback)
                    $builder->orGroupStart();
                        $builder->where('transport_stats.periode', 'Bulanan (Back-up)');
                        $builder->where('transport_stats.created_at >=', $startDate);
                        $builder->where('transport_stats.created_at <=', $endDate);
                    $builder->groupEnd();
                $builder->groupEnd();
            }
            
            // Order by newest first
            $builder->orderBy('transport_stats.created_at', 'DESC');
            
            // Get results
            $results = $builder->get()->getResultArray();
            
            // Calculate summary statistics
            $totalUnit = 0;
            $totalZev = 0;
            $totalNonZev = 0;
            $kategoriSet = [];
            
            foreach ($results as $row) {
                $totalUnit += (int)$row['jumlah_total'];
                if (isset($row['is_zev']) && $row['is_zev'] == 1) {
                    $totalZev += (int)$row['jumlah_total'];
                } else {
                    $totalNonZev += (int)$row['jumlah_total'];
                }
                $kategoriSet[$row['kategori_kendaraan']] = true;
            }
            
            $totalKategori = count($kategoriSet);
            $persentaseZev = $totalUnit > 0 ? round(($totalZev / $totalUnit) * 100, 2) : 0;
            
            // Format data for display
            $data = [];
            foreach ($results as $row) {
                // Determine display date
                $displayDate = '-';
                if (!empty($row['tanggal_pencatatan'])) {
                    $displayDate = date('d-m-Y', strtotime($row['tanggal_pencatatan']));
                } elseif (!empty($row['bulan']) && !empty($row['tahun'])) {
                    $displayDate = $row['bulan'] . ' ' . $row['tahun'];
                }
                
                // Determine if ZEV based on bahan bakar
                $isZev = 0;
                if (isset($row['is_zev'])) {
                    $isZev = (int)$row['is_zev'];
                } else {
                    // Fallback: check bahan bakar
                    $bahanBakar = $row['jenis_bahan_bakar'];
                    if ($bahanBakar === 'Listrik' || $bahanBakar === 'Non-BBM') {
                        $isZev = 1;
                    }
                }
                
                $data[] = [
                    'id' => $row['id'],
                    'kategori' => $row['kategori_kendaraan'] ?: '-',
                    'bahan_bakar' => $row['jenis_bahan_bakar'] ?: '-',
                    'status_kendaraan' => $row['status_kendaraan'] ?: null,
                    'jumlah_total' => (int)$row['jumlah_total'],
                    'periode' => $row['periode'] ?: '-',
                    'tanggal_data' => $displayDate,
                    'is_zev' => $isZev,
                    'tanggal_input' => $row['tanggal_input'] ?: '-',
                    'petugas' => $row['petugas_nama'] ?: 'N/A'
                ];
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $data,
                'summary' => [
                    'total_unit' => $totalUnit,
                    'total_zev' => $totalZev,
                    'total_non_zev' => $totalNonZev,
                    'total_kategori' => $totalKategori,
                    'total_transaksi' => count($data),
                    'persentase_zev' => $persentaseZev
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get detail laporan error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'error_detail' => $e->getMessage()
            ]);
        }
    }

    /**
     * Export Laporan to Excel
     */
    public function exportLaporanExcel()
    {
        $filters = [
            'start_date' => $this->request->getGet('start_date') ?? '',
            'end_date' => $this->request->getGet('end_date') ?? '',
            'kategori_kendaraan' => $this->request->getGet('kategori_kendaraan') ?? ''
        ];
        
        $reportData = $this->getComprehensiveReportData($filters);
        
        // Set headers for Excel download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Laporan_Transportasi_' . date('Y-m-d_His') . '.xls"');
        header('Cache-Control: max-age=0');

        echo '<table border="1">';
        echo '<tr><th colspan="10" style="text-align: center; font-weight: bold; font-size: 16px;">LAPORAN DATA TRANSPORTASI KAMPUS</th></tr>';
        echo '<tr><th colspan="10" style="text-align: center;">Politeknik Negeri Bandung - UI GreenMetric</th></tr>';
        echo '<tr><th colspan="10" style="text-align: center;">Tanggal Export: ' . date('d/m/Y H:i:s') . '</th></tr>';
        
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            echo '<tr><th colspan="10" style="text-align: center;">Periode: ' . date('d/m/Y', strtotime($filters['start_date'])) . ' - ' . date('d/m/Y', strtotime($filters['end_date'])) . '</th></tr>';
        }
        
        echo '<tr><th></th></tr>'; // Empty row

        // Summary section
        echo '<tr><th colspan="10" style="background-color: #28a745; color: white; text-align: center;">RINGKASAN STATISTIK</th></tr>';
        echo '<tr><td><strong>Total Kendaraan:</strong></td><td colspan="9">' . number_format($reportData['summary']['total_kendaraan']) . ' Unit</td></tr>';
        echo '<tr><td><strong>Total ZEV (Listrik/Sepeda):</strong></td><td colspan="9">' . number_format($reportData['summary']['total_zev']) . ' Unit</td></tr>';
        echo '<tr><td><strong>Total Non-ZEV (BBM):</strong></td><td colspan="9">' . number_format($reportData['summary']['total_non_zev']) . ' Unit</td></tr>';
        echo '<tr><td><strong>Persentase Keberlanjutan:</strong></td><td colspan="9">' . number_format($reportData['summary']['persentase_keberlanjutan'], 2) . '%</td></tr>';
        echo '<tr><th></th></tr>'; // Empty row

        // Rekap Kategori
        echo '<tr><th colspan="10" style="background-color: #007bff; color: white; text-align: center;">REKAPITULASI PER KATEGORI KENDARAAN</th></tr>';
        echo '<tr><th>No</th><th>Kategori Kendaraan</th><th>Total Unit</th></tr>';
        foreach ($reportData['rekap_kategori'] as $index => $row) {
            echo '<tr>';
            echo '<td>' . ($index + 1) . '</td>';
            echo '<td>' . esc($row['kategori']) . '</td>';
            echo '<td>' . number_format($row['total_unit']) . '</td>';
            echo '</tr>';
        }
        echo '<tr><th></th></tr>'; // Empty row

        // Rekap Bahan Bakar
        echo '<tr><th colspan="10" style="background-color: #28a745; color: white; text-align: center;">REKAPITULASI PER JENIS BAHAN BAKAR</th></tr>';
        echo '<tr><th>No</th><th>Jenis Bahan Bakar</th><th>Total Unit</th></tr>';
        foreach ($reportData['rekap_bahan_bakar'] as $index => $row) {
            echo '<tr>';
            echo '<td>' . ($index + 1) . '</td>';
            echo '<td>' . esc($row['bahan_bakar']) . '</td>';
            echo '<td>' . number_format($row['total_unit']) . '</td>';
            echo '</tr>';
        }
        echo '<tr><th></th></tr>'; // Empty row

        // Rekap Bulanan
        echo '<tr><th colspan="10" style="background-color: #17a2b8; color: white; text-align: center;">REKAPITULASI BULANAN</th></tr>';
        echo '<tr><th>No</th><th>Periode</th><th>Total Kendaraan</th></tr>';
        foreach ($reportData['rekap_bulanan'] as $index => $row) {
            echo '<tr>';
            echo '<td>' . ($index + 1) . '</td>';
            echo '<td>' . esc($row['periode']) . '</td>';
            echo '<td>' . number_format($row['total_kendaraan']) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        exit;
    }

    /**
     * Export Laporan to PDF
     */
    public function exportLaporanPdf()
    {
        $filters = [
            'start_date' => $this->request->getGet('start_date') ?? '',
            'end_date' => $this->request->getGet('end_date') ?? '',
            'kategori_kendaraan' => $this->request->getGet('kategori_kendaraan') ?? ''
        ];
        
        $reportData = $this->getComprehensiveReportData($filters);
        
        $data = [
            'title' => 'Laporan Data Transportasi Kampus',
            'filters' => $filters,
            'report_data' => $reportData,
            'generated_at' => date('d/m/Y H:i:s')
        ];

        // Generate HTML from view
        $html = view('admin_pusat/transportation/export_laporan_pdf', $data);

        // Configure Dompdf
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');
        
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Output PDF
        $filename = 'Laporan_Transportasi_' . date('Y-m-d_His') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
    }

    /**
     * History Page - Riwayat lengkap data transportasi
     * Mirip dengan laporan waste dengan filter dan rekap mingguan
     */
    public function history()
    {
        $unitModel = new \App\Models\UnitModel();
        
        // Get filter parameters
        $filters = [
            'start_date' => $this->request->getGet('start_date') ?? '',
            'end_date' => $this->request->getGet('end_date') ?? '',
            'unit_id' => $this->request->getGet('unit_id') ?? '',
            'filter_bulan' => $this->request->getGet('filter_bulan') ?? '',
            'filter_tahun' => $this->request->getGet('filter_tahun') ?? '',
            'filter_minggu' => $this->request->getGet('filter_minggu') ?? '',
            'filter_petugas' => $this->request->getGet('filter_petugas') ?? ''
        ];
        
        // Get history data
        $historyData = $this->getHistoryData($filters);
        
        $data = [
            'title' => 'History Data Transportasi',
            'user' => session()->get('user'),
            'filters' => $filters,
            'units' => $unitModel->where('status_aktif', true)->findAll(),
            'summary' => $historyData['summary'],
            'rekap_kategori' => $historyData['rekap_kategori'],
            'detail_rekap' => $historyData['detail_rekap']
        ];

        return view('admin_pusat/transportation/history', $data);
    }

    /**
     * Get history data with filters
     */
    private function getHistoryData(array $filters)
    {
        $db = \Config\Database::connect();
        
        // Build base query
        $builder = $db->table('transport_stats');
        $builder->select('transport_stats.*, users.nama_lengkap as petugas_nama, unit.nama_unit');
        $builder->join('users', 'users.id = transport_stats.input_by', 'left');
        $builder->join('unit', 'unit.id = users.unit_id', 'left');
        
        // Apply date filters
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $builder->groupStart();
                $builder->groupStart();
                    $builder->where('transport_stats.periode', 'Harian');
                    $builder->where('transport_stats.tanggal_pencatatan >=', $filters['start_date']);
                    $builder->where('transport_stats.tanggal_pencatatan <=', $filters['end_date']);
                $builder->groupEnd();
                $builder->orGroupStart();
                    $builder->where('transport_stats.periode', 'Mingguan (Back-up)');
                    $builder->where('transport_stats.tanggal_mulai >=', $filters['start_date']);
                    $builder->where('transport_stats.tanggal_selesai <=', $filters['end_date']);
                $builder->groupEnd();
                $builder->orGroupStart();
                    $builder->where('transport_stats.periode', 'Bulanan (Back-up)');
                    $builder->where('transport_stats.created_at >=', $filters['start_date']);
                    $builder->where('transport_stats.created_at <=', $filters['end_date']);
                $builder->groupEnd();
            $builder->groupEnd();
        }
        
        // Apply unit filter
        if (!empty($filters['unit_id'])) {
            $builder->where('users.unit_id', $filters['unit_id']);
        }
        
        // Get all records
        $allRecords = $builder->orderBy('transport_stats.created_at', 'DESC')->get()->getResultArray();
        
        // Calculate summary
        $totalTransaksi = count($allRecords);
        $totalKendaraan = array_sum(array_column($allRecords, 'jumlah_total'));
        $totalZev = array_sum(array_map(function($row) {
            return $row['is_zev'] == 1 ? $row['jumlah_total'] : 0;
        }, $allRecords));
        $persentaseZev = $totalKendaraan > 0 ? ($totalZev / $totalKendaraan) * 100 : 0;
        
        // Rekap per kategori
        $rekapKategori = [];
        foreach ($allRecords as $row) {
            $kategori = $row['kategori_kendaraan'];
            if (!isset($rekapKategori[$kategori])) {
                $rekapKategori[$kategori] = [
                    'kategori' => $kategori,
                    'total_transaksi' => 0,
                    'total_unit' => 0,
                    'total_zev' => 0,
                    'total_non_zev' => 0
                ];
            }
            $rekapKategori[$kategori]['total_transaksi']++;
            $rekapKategori[$kategori]['total_unit'] += $row['jumlah_total'];
            if ($row['is_zev'] == 1) {
                $rekapKategori[$kategori]['total_zev'] += $row['jumlah_total'];
            } else {
                $rekapKategori[$kategori]['total_non_zev'] += $row['jumlah_total'];
            }
        }
        
        // Detail rekap mingguan
        $detailRekap = [];
        foreach ($allRecords as $row) {
            $date = null;
            if ($row['periode'] === 'Harian' && !empty($row['tanggal_pencatatan'])) {
                $date = $row['tanggal_pencatatan'];
            } elseif ($row['periode'] === 'Mingguan (Back-up)' && !empty($row['tanggal_mulai'])) {
                $date = $row['tanggal_mulai'];
            } elseif (!empty($row['created_at'])) {
                $date = date('Y-m-d', strtotime($row['created_at']));
            }
            
            if ($date) {
                $weekNum = date('W', strtotime($date));
                $year = date('Y', strtotime($date));
                $month = date('m', strtotime($date));
                $key = $year . '-' . $month . '-W' . $weekNum . '-' . $row['kategori_kendaraan'] . '-' . $row['nama_unit'];
                
                if (!isset($detailRekap[$key])) {
                    $detailRekap[$key] = [
                        'tahun' => $year,
                        'bulan' => (int)$month,
                        'minggu_ke' => $weekNum,
                        'nama_unit' => $row['nama_unit'] ?? 'Unit Tidak Diketahui',
                        'nama_petugas' => $row['petugas_nama'] ?? 'Tidak Diketahui',
                        'kategori_kendaraan' => $row['kategori_kendaraan'],
                        'jumlah_transaksi' => 0,
                        'total_unit' => 0,
                        'total_zev' => 0,
                        'total_non_zev' => 0,
                        'tanggal_mulai' => $date,
                        'tanggal_akhir' => $date
                    ];
                }
                $detailRekap[$key]['jumlah_transaksi']++;
                $detailRekap[$key]['total_unit'] += $row['jumlah_total'];
                if ($row['is_zev'] == 1) {
                    $detailRekap[$key]['total_zev'] += $row['jumlah_total'];
                } else {
                    $detailRekap[$key]['total_non_zev'] += $row['jumlah_total'];
                }
                
                // Update tanggal range
                if ($date < $detailRekap[$key]['tanggal_mulai']) {
                    $detailRekap[$key]['tanggal_mulai'] = $date;
                }
                if ($date > $detailRekap[$key]['tanggal_akhir']) {
                    $detailRekap[$key]['tanggal_akhir'] = $date;
                }
            }
        }
        
        // Apply additional filters for detail rekap
        if (!empty($filters['filter_bulan']) || !empty($filters['filter_tahun']) || !empty($filters['filter_minggu']) || !empty($filters['filter_petugas'])) {
            $detailRekap = array_filter($detailRekap, function($item) use ($filters) {
                if (!empty($filters['filter_bulan']) && $item['bulan'] != $filters['filter_bulan']) {
                    return false;
                }
                if (!empty($filters['filter_tahun']) && $item['tahun'] != $filters['filter_tahun']) {
                    return false;
                }
                if (!empty($filters['filter_minggu']) && $item['minggu_ke'] != $filters['filter_minggu']) {
                    return false;
                }
                if (!empty($filters['filter_petugas']) && stripos($item['nama_petugas'], $filters['filter_petugas']) === false) {
                    return false;
                }
                return true;
            });
        }
        
        // Sort detail rekap
        usort($detailRekap, function($a, $b) {
            return strcmp($b['tahun'] . $b['bulan'] . $b['minggu_ke'], $a['tahun'] . $a['bulan'] . $a['minggu_ke']);
        });
        
        return [
            'summary' => [
                'total_transaksi' => $totalTransaksi,
                'total_kendaraan' => $totalKendaraan,
                'total_zev' => $totalZev,
                'persentase_zev' => $persentaseZev
            ],
            'rekap_kategori' => array_values($rekapKategori),
            'detail_rekap' => array_values($detailRekap)
        ];
    }

    /**
     * ========================================
     * LOG HARIAN KENDARAAN
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
            'admin' => session()->get('user'),
            'today_summary' => $todaySummary,
            'total_masuk_hari_ini' => $totalMasukHariIni,
            'total_keluar_hari_ini' => $totalKeluarHariIni,
            'all_logs' => $allLogs,
            'monthly_summary' => $monthlySummary,
            'current_month' => $currentMonth,
            'current_year' => $currentYear
        ];

        return view('admin_pusat/transportation/log_harian', $data);
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
                // Update
                $data['updated_at'] = date('Y-m-d H:i:s');
                $db->table('transport_daily_logs')->update($data, ['id' => $id]);
                $message = 'Log harian berhasil diperbarui';
            } else {
                // Insert
                $data['created_at'] = date('Y-m-d H:i:s');
                $db->table('transport_daily_logs')->insert($data);
                $message = 'Log harian berhasil ditambahkan';
            }
            
            return redirect()->to('/admin-pusat/transportation/log-harian')->with('success', $message);
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
            $log = $db->table('transport_daily_logs')
                ->where('id', $id)
                ->get()
                ->getRowArray();
            
            if (!$log) {
                return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
            }
            
            return $this->response->setJSON(['success' => true, 'data' => $log]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Hapus Log Harian
     */
    public function hapusLogHarian($id)
    {
        $db = \Config\Database::connect();
        
        try {
            $db->table('transport_daily_logs')->delete(['id' => $id]);
            return redirect()->to('/admin-pusat/transportation/log-harian')->with('success', 'Log harian berhasil dihapus');
        } catch (\Exception $e) {
            log_message('error', 'Hapus log harian error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus log: ' . $e->getMessage());
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
                'admin' => session()->get('user')
            ];
            
            // Load view and render to HTML
            $html = view('admin_pusat/transportation/log_harian_pdf', $data);
            
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
     * Back-up Log Harian to Monthly Report
     * Aggregate daily logs and insert/update to transport_stats table
     */
    public function backupLogHarian()
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
            // Check if table exists
            if (!$db->tableExists('transport_daily_logs')) {
                return $this->response->setJSON([
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
            
            // Get all logs that haven't been backed up yet
            $query = $db->table('transport_daily_logs')
                ->where('is_backed_up', 0)
                ->orWhere('is_backed_up IS NULL')
                ->get();
            
            // Validate query result
            if (!$query) {
                $db->transRollback();
                log_message('error', 'Query failed: ' . $db->error());
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal mengambil data dari database: ' . $db->error()['message']
                ]);
            }
            
            $logs = $query->getResultArray();
            
            // Check if there's data to backup
            if (empty($logs)) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tidak ada data harian untuk di-backup. Semua data sudah di-backup atau belum ada data yang diinput.'
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
                    'success' => false,
                    'message' => 'Tidak ada data valid untuk di-backup. Periksa format data log harian.'
                ]);
            }
            
            log_message('info', 'Aggregated into ' . count($aggregated) . ' groups');
            
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
                
                if (!$existingQuery) {
                    throw new \Exception('Failed to check existing record: ' . $db->error()['message']);
                }
                
                $existing = $existingQuery->getRowArray();
                
                if ($existing) {
                    // Update existing record (add to existing values)
                    $newTotal = $existing['jumlah_total'] + $data['total_masuk'] + $data['total_keluar'];
                    
                    $updateResult = $db->table('transport_stats')->update([
                        'jumlah_total' => $newTotal,
                        'updated_at' => date('Y-m-d H:i:s')
                    ], ['id' => $existing['id']]);
                    
                    if (!$updateResult) {
                        throw new \Exception('Failed to update transport_stats: ' . $db->error()['message']);
                    }
                    
                    $totalUpdated++;
                    log_message('info', "Updated record ID {$existing['id']}: {$data['jenis_kendaraan']} - {$data['bulan']}/{$data['tahun']} = {$newTotal}");
                } else {
                    // Insert new record
                    $insertData = [
                        'kategori_kendaraan' => $data['jenis_kendaraan'],
                        'bahan_bakar' => 'Mixed', // Default value
                        'jumlah_total' => $data['total_masuk'] + $data['total_keluar'],
                        'is_zev' => 0, // Default value
                        'bulan' => $data['bulan'],
                        'tahun' => $data['tahun'],
                        'periode' => 'Bulanan (Back-up)',
                        'status' => 'disetujui',
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    
                    $insertResult = $db->table('transport_stats')->insert($insertData);
                    
                    if (!$insertResult) {
                        throw new \Exception('Failed to insert to transport_stats: ' . $db->error()['message']);
                    }
                    
                    $totalInserted++;
                    log_message('info', "Inserted new record: {$data['jenis_kendaraan']} - {$data['bulan']}/{$data['tahun']} = {$insertData['jumlah_total']}");
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
                
                if (!$updateResult) {
                    throw new \Exception('Failed to mark logs as backed up: ' . $db->error()['message']);
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
                'success' => false,
                'message' => 'Gagal melakukan back-up: ' . $e->getMessage()
            ]);
        }
    }
}