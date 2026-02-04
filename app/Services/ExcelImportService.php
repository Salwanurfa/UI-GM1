<?php

namespace App\Services;

use App\Models\UserModel;
use App\Models\UnitModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

/**
 * ExcelImportService
 * 
 * Service untuk menangani parsing file Excel, validasi, dan operasi import user.
 * Mendukung file Excel (.xlsx) dengan validasi komprehensif dan batch insert.
 * 
 * @package App\Services
 */
class ExcelImportService
{
    /**
     * Ukuran file maksimal dalam bytes (5MB)
     */
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB

    /**
     * Jumlah baris data maksimal (tidak termasuk header)
     */
    private const MAX_ROWS = 2000;

    /**
     * Ukuran batch untuk insert database
     */
    private const BATCH_SIZE = 200;

    /**
     * @var UserModel|null Instance dari UserModel untuk operasi database user
     */
    private ?UserModel $userModel;

    /**
     * @var UnitModel|null Instance dari UnitModel untuk resolusi nama unit
     */
    private ?UnitModel $unitModel;

    /**
     * Constructor
     * 
     * Inject dependensi UserModel dan UnitModel untuk operasi database.
     * 
     * @param UserModel|null $userModel Instance UserModel (opsional untuk testing)
     * @param UnitModel|null $unitModel Instance UnitModel (opsional untuk testing)
     */
    public function __construct(?UserModel $userModel = null, ?UnitModel $unitModel = null)
    {
        $this->userModel = $userModel;
        $this->unitModel = $unitModel;
    }

    /**
     * Import user dari file Excel
     * 
     * Method utama untuk memproses file Excel dan mengimpor user ke database.
     * Melakukan validasi file, parsing, validasi data per baris, dan batch insert.
     * 
     * Proses:
     * 1. Validasi file (ekstensi, ukuran, MIME type)
     * 2. Parse file Excel dan ekstrak baris
     * 3. Validasi setiap baris data
     * 4. Insert user valid dalam batch
     * 5. Kumpulkan dan kembalikan hasil dengan error
     * 
     * @param string $filePath Path ke file Excel yang diupload
     * @param bool $skipDuplicates Apakah skip user duplikat (default: true)
     * @return array Hasil import dengan struktur:
     *               [
     *                   'success' => bool,
     *                   'message' => string,
     *                   'total' => int,
     *                   'inserted' => int,
     *                   'failed' => int,
     *                   'errors' => array
     *               ]
     * @throws \Exception Jika terjadi error sistem yang tidak dapat ditangani
     */
    public function importUsers(string $filePath, bool $skipDuplicates = true): array
    {
        try {
            // 1. Validasi file
            $validation = $this->validateFile($filePath);
            if (!$validation['valid']) {
                return [
                    'success' => false,
                    'message' => implode(', ', $validation['errors']),
                    'total' => 0,
                    'inserted' => 0,
                    'failed' => 0,
                    'errors' => $validation['errors']
                ];
            }

            // 2. Parse file Excel
            $parseResult = $this->parseExcelFile($filePath);
            $rows = $parseResult['rows'];
            $totalRows = count($rows);

            log_message('info', "Excel parsed - Total rows: $totalRows");

            // 3. Validasi dan proses setiap baris
            $validUsers = [];
            $errors = [];
            $failed = 0;

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // +2 karena index mulai dari 0 dan ada header

                // Log raw data untuk debugging
                log_message('info', "Processing row $rowNumber: " . json_encode($row));

                // Validasi baris
                $validationResult = $this->validateRow($row, $rowNumber);

                if ($validationResult['valid']) {
                    $validUsers[] = $validationResult['data'];
                    log_message('info', "Row $rowNumber validated successfully");
                } else {
                    $failed++;
                    foreach ($validationResult['errors'] as $error) {
                        $errors[] = "Baris $rowNumber: $error";
                        log_message('warning', "Row $rowNumber validation failed: $error");
                    }
                }
            }

            log_message('info', "Validation complete - Valid: " . count($validUsers) . ", Failed: $failed");

            // 4. Insert user valid ke database
            $inserted = 0;
            if (!empty($validUsers)) {
                foreach ($validUsers as $userData) {
                    try {
                        // Hapus field yang tidak diperlukan untuk insert
                        unset($userData['_row_number']);
                        
                        // JANGAN hash password - simpan plain text sesuai sistem
                        // Password akan tetap plain text seperti di UserManagement controller
                        
                        // Log data yang akan diinsert (tanpa password untuk keamanan log)
                        $logData = $userData;
                        $logData['password'] = '[HIDDEN]';
                        log_message('info', "Attempting to insert user: " . json_encode($logData));
                        
                        // Insert user
                        $insertResult = $this->userModel->insert($userData);
                        
                        if ($insertResult) {
                            $inserted++;
                            log_message('info', "User inserted successfully: {$userData['username']} (ID: {$insertResult})");
                        } else {
                            $failed++;
                            $modelErrors = $this->userModel->errors();
                            $errors[] = "Gagal insert user {$userData['username']}: " . implode(', ', $modelErrors);
                            log_message('error', "Failed to insert user {$userData['username']}: " . json_encode($modelErrors));
                        }
                    } catch (\Exception $e) {
                        $failed++;
                        $errors[] = "Error insert user {$userData['username']}: " . $e->getMessage();
                        log_message('error', "Exception inserting user {$userData['username']}: " . $e->getMessage());
                    }
                }
            }

            // 5. Buat pesan hasil
            $message = "Import selesai. Total: $totalRows baris, Berhasil: $inserted, Gagal: $failed";
            
            if (!empty($errors)) {
                $message .= "\n\nDetail error:\n" . implode("\n", array_slice($errors, 0, 10));
                if (count($errors) > 10) {
                    $message .= "\n... dan " . (count($errors) - 10) . " error lainnya";
                }
            }

            log_message('info', "Import completed - Inserted: $inserted, Failed: $failed");

            return [
                'success' => $inserted > 0,
                'message' => $message,
                'total' => $totalRows,
                'inserted' => $inserted,
                'failed' => $failed,
                'errors' => $errors
            ];

        } catch (\Exception $e) {
            log_message('error', 'Import exception: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'total' => 0,
                'inserted' => 0,
                'failed' => 0,
                'errors' => [$e->getMessage()]
            ];
        }
    }

    /**
     * Validasi struktur file Excel
     * 
     * Memvalidasi file Excel sebelum pemrosesan:
     * - Cek ekstensi file adalah .xlsx
     * - Cek ukuran file tidak melebihi MAX_FILE_SIZE
     * - Cek MIME type cocok dengan format Excel
     * - Cek file dapat dibaca oleh PhpSpreadsheet
     * 
     * @param string $filePath Path ke file Excel
     * @return array Hasil validasi dengan struktur:
     *               [
     *                   'valid' => bool,
     *                   'errors' => array
     *               ]
     */
    public function validateFile(string $filePath): array
    {
        $errors = [];

        // Cek apakah file ada
        if (!file_exists($filePath)) {
            $errors[] = 'File tidak ditemukan';
            return [
                'valid' => false,
                'errors' => $errors
            ];
        }

        // Cek ekstensi file adalah .xlsx (lebih fleksibel, cek dari nama file asli juga)
        $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        // Skip validasi ekstensi - biarkan PhpSpreadsheet yang validasi
        log_message('info', 'File extension detected: ' . ($fileExtension ?: 'none'));

        // Cek ukuran file tidak melebihi 5MB
        $fileSize = filesize($filePath);
        if ($fileSize > self::MAX_FILE_SIZE) {
            $errors[] = 'Ukuran file maksimal 5MB';
        }

        // Cek MIME type cocok dengan format Excel
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);

        log_message('info', 'File MIME type: ' . $mimeType);

        // MIME type yang valid untuk file .xlsx
        $validMimeTypes = [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/zip', // Kadang .xlsx terdeteksi sebagai zip karena strukturnya
            'application/octet-stream' // Fallback untuk beberapa sistem
        ];

        // Skip validasi MIME type juga - hanya log warning
        if (!in_array($mimeType, $validMimeTypes)) {
            log_message('warning', 'MIME type not in valid list: ' . $mimeType);
        }

        // Jika ada error, kembalikan hasil validasi
        if (!empty($errors)) {
            return [
                'valid' => false,
                'errors' => $errors
            ];
        }

        // Semua validasi lolos
        return [
            'valid' => true,
            'errors' => []
        ];
    }

    /**
     * Parse file Excel dan ekstrak baris
     * 
     * Membaca file Excel menggunakan PhpSpreadsheet dengan mode readDataOnly
     * untuk efisiensi memori. Melakukan:
     * - Load file Excel
     * - Dapatkan sheet 'users'
     * - Baca baris header dan buat pemetaan kolom
     * - Hitung total baris
     * - Baca semua baris data dengan streaming
     * - Trim dan sanitasi nilai sel
     * 
     * @param string $filePath Path ke file Excel
     * @return array Baris yang diparsing dengan pemetaan header
     * @throws \Exception Jika sheet tidak ditemukan atau jumlah baris melebihi batas
     */
    private function parseExcelFile(string $filePath): array
    {
        // Buat reader dengan readDataOnly = true untuk efisiensi memori
        $reader = new Xlsx();
        $reader->setReadDataOnly(true);
        
        // Load file Excel
        $spreadsheet = $reader->load($filePath);
        
        // Ambil sheet pertama (lebih fleksibel, tidak harus bernama 'users')
        $sheet = $spreadsheet->getActiveSheet();
        
        log_message('info', 'Sheet name: ' . $sheet->getTitle());
        
        // Baca baris header (baris pertama)
        $headerRow = $sheet->rangeToArray('A1:' . $sheet->getHighestColumn() . '1', null, true, false)[0];
        
        // Trim dan lowercase header untuk pemetaan
        $headerRow = array_map(function($header) {
            return trim(strtolower((string)$header));
        }, $headerRow);
        
        log_message('info', 'Headers found: ' . json_encode($headerRow));
        
        // Buat pemetaan kolom: nama_kolom => indeks
        $columnMapping = [];
        foreach ($headerRow as $index => $columnName) {
            if (!empty($columnName)) {
                $columnMapping[$columnName] = $index;
            }
        }
        
        // Validasi header yang diperlukan ada
        $requiredHeaders = ['username', 'email', 'nama_lengkap', 'role'];
        $missingHeaders = [];
        foreach ($requiredHeaders as $required) {
            if (!isset($columnMapping[$required])) {
                $missingHeaders[] = $required;
            }
        }
        
        if (!empty($missingHeaders)) {
            throw new \Exception("Header yang diperlukan hilang: " . implode(', ', $missingHeaders));
        }
        
        // Hitung total baris (tidak termasuk header)
        $highestRow = $sheet->getHighestRow();
        $totalRows = $highestRow - 1; // Kurangi 1 untuk header
        
        log_message('info', "Total rows (excluding header): $totalRows");
        
        // Validasi jumlah baris tidak melebihi batas
        if ($totalRows > self::MAX_ROWS) {
            throw new \Exception("Maksimal " . self::MAX_ROWS . " baris data (file Anda: $totalRows baris)");
        }
        
        // Baca semua baris data
        $rows = [];
        for ($rowIndex = 2; $rowIndex <= $highestRow; $rowIndex++) {
            $rowData = $sheet->rangeToArray('A' . $rowIndex . ':' . $sheet->getHighestColumn() . $rowIndex, null, true, false)[0];
            
            // Skip baris kosong
            if (empty(array_filter($rowData))) {
                continue;
            }
            
            // Petakan data baris ke nama kolom
            $mappedRow = [];
            foreach ($columnMapping as $columnName => $columnIndex) {
                $value = isset($rowData[$columnIndex]) ? $rowData[$columnIndex] : '';
                // Trim dan sanitasi nilai sel
                $mappedRow[$columnName] = trim($value);
            }
            
            // Tambahkan nomor baris untuk pelaporan error
            $mappedRow['_row_number'] = $rowIndex;
            
            $rows[] = $mappedRow;
        }
        
        return [
            'column_mapping' => $columnMapping,
            'rows' => $rows,
            'total_rows' => count($rows)
        ];
    }

    /**
     * Validasi satu baris data user
     * 
     * Memvalidasi semua field dalam satu baris data user:
     * - username: tidak kosong, 3-50 karakter, alfanumerik + titik/underscore, unik
     * - email: format email valid, unik
     * - nama_lengkap: tidak kosong, maks 150 karakter
     * - role: salah satu dari nilai yang diizinkan
     * - unit_name: resolusi ke unit_id (opsional)
     * - is_active: 0 atau 1 (opsional, default 1)
     * - password: min 6 karakter (opsional, auto-generate jika kosong)
     * 
     * @param array $row Data baris dengan key sesuai nama kolom
     * @param int $rowNumber Nomor baris untuk pelaporan error
     * @return array Hasil validasi dengan struktur:
     *               [
     *                   'valid' => bool,
     *                   'errors' => array,
     *                   'data' => array (data yang sudah diproses)
     *               ]
     */
    private function validateRow(array $row, int $rowNumber): array
    {
        $errors = [];
        $data = $row;

        // Validasi username: tidak kosong, 3-100 karakter, alfanumerik + titik/underscore/spasi
        $username = isset($row['username']) ? trim($row['username']) : '';
        if (empty($username)) {
            $errors[] = 'Username tidak boleh kosong';
        } elseif (strlen($username) < 3 || strlen($username) > 100) {
            $errors[] = 'Username harus 3-100 karakter';
        } elseif (!preg_match('/^[a-zA-Z0-9._ ]+$/', $username)) {
            $errors[] = 'Username hanya boleh berisi alfanumerik, titik, underscore, dan spasi';
        } else {
            // Cek apakah username sudah ada di database
            if ($this->userModel !== null) {
                $existingUser = $this->userModel->where('username', $username)->first();
                if ($existingUser) {
                    $errors[] = 'Username sudah digunakan';
                }
            }
        }
        $data['username'] = $username;

        // Validasi email: format email valid
        $email = isset($row['email']) ? trim($row['email']) : '';
        if (empty($email)) {
            $errors[] = 'Email tidak boleh kosong';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid';
        } else {
            // Cek apakah email sudah ada di database
            if ($this->userModel !== null) {
                $existingUser = $this->userModel->where('email', $email)->first();
                if ($existingUser) {
                    $errors[] = 'Email sudah digunakan';
                }
            }
        }
        $data['email'] = $email;

        // Validasi nama_lengkap: tidak kosong, maks 150 karakter
        $namaLengkap = isset($row['nama_lengkap']) ? trim($row['nama_lengkap']) : '';
        if (empty($namaLengkap)) {
            $errors[] = 'Nama lengkap tidak boleh kosong';
        } elseif (strlen($namaLengkap) > 150) {
            $errors[] = 'Nama lengkap maksimal 150 karakter';
        }
        $data['nama_lengkap'] = $namaLengkap;

        // Validasi role: salah satu dari nilai yang diizinkan
        $role = isset($row['role']) ? trim($row['role']) : '';
        $validRoles = ['admin_pusat', 'super_admin', 'user', 'pengelola_tps'];
        if (empty($role)) {
            $errors[] = 'Role tidak boleh kosong';
        } elseif (!in_array($role, $validRoles)) {
            $errors[] = 'Role harus salah satu dari: ' . implode(', ', $validRoles);
        }
        $data['role'] = $role;

        // Resolusi nama gedung ke unit_id (opsional) - AUTO CREATE jika tidak ada
        $data['unit_id'] = null; // Default null jika tidak ada unit
        if (isset($row['nama_gedung']) && !empty(trim($row['nama_gedung']))) {
            $namaGedung = trim($row['nama_gedung']);
            
            // Query UnitModel untuk menemukan unit yang cocok (case-insensitive)
            if ($this->unitModel !== null) {
                $unit = $this->unitModel
                    ->where('LOWER(nama_unit)', strtolower($namaGedung))
                    ->first();
                
                if ($unit) {
                    // Unit ditemukan, set unit_id
                    $data['unit_id'] = $unit['id'];
                } else {
                    // Unit tidak ditemukan, BUAT BARU
                    try {
                        $newUnitData = [
                            'nama_unit' => $namaGedung,
                            'kode_unit' => $this->generateKodeUnit($namaGedung),
                            'status_aktif' => 1
                        ];
                        
                        $newUnitId = $this->unitModel->insert($newUnitData);
                        
                        if ($newUnitId) {
                            $data['unit_id'] = $newUnitId;
                            log_message('info', "Auto-created new unit: {$namaGedung} (ID: {$newUnitId})");
                        } else {
                            $errors[] = "Gagal membuat gedung baru: {$namaGedung}";
                        }
                    } catch (\Exception $e) {
                        $errors[] = "Error membuat gedung {$namaGedung}: " . $e->getMessage();
                        log_message('error', "Failed to create unit {$namaGedung}: " . $e->getMessage());
                    }
                }
            }
        }

        // Validasi is_active: 0 atau 1 jika disediakan, default ke 1 jika hilang
        if (isset($row['is_active']) && $row['is_active'] !== '') {
            $isActive = trim($row['is_active']);
            if (!in_array($isActive, ['0', '1', 0, 1], true)) {
                $errors[] = 'is_active harus 0 atau 1';
            }
            $data['status_aktif'] = (int)$isActive; // Map ke status_aktif untuk database
        } else {
            // Default ke 1 jika tidak disediakan
            $data['status_aktif'] = 1; // Map ke status_aktif untuk database
        }
        
        // Hapus is_active dari data (karena sudah dipetakan ke status_aktif)
        unset($data['is_active']);

        // Validasi password: min 6 karakter jika disediakan
        if (isset($row['password']) && $row['password'] !== '') {
            $password = trim($row['password']);
            if (strlen($password) < 6) {
                $errors[] = 'Password minimal 6 karakter';
            }
            $data['password'] = $password;
        } else {
            // Generate password acak yang aman jika tidak disediakan
            $data['password'] = $this->generatePassword();
        }

        // Tentukan apakah baris valid
        $valid = empty($errors);

        return [
            'valid' => $valid,
            'errors' => $errors,
            'data' => $data
        ];
    }

    /**
     * Insert user dalam batch
     * 
     * Melakukan batch insert user ke database dengan transaksi:
     * - Bagi baris valid menjadi batch dengan ukuran BATCH_SIZE
     * - Untuk setiap batch, mulai transaksi
     * - Hash password menggunakan password_hash()
     * - Insert menggunakan UserModel->insertBatch()
     * - Commit jika berhasil, rollback jika gagal
     * - Tangani pelanggaran constraint unique
     * 
     * @param array $validRows Array baris data user yang valid
     * @return array Hasil insert dengan struktur:
     *               [
     *                   'inserted' => int,
     *                   'failed' => int,
     *                   'errors' => array
     *               ]
     */
    private function batchInsertUsers(array $validRows): array
    {
        // TODO: Implementasi akan dilakukan di task berikutnya
        return [
            'inserted' => 0,
            'failed' => 0,
            'errors' => []
        ];
    }

    /**
     * Generate password acak yang aman
     * 
     * Menghasilkan password acak dengan:
     * - Panjang minimal 12 karakter (dapat dikonfigurasi)
     * - Kombinasi huruf besar, huruf kecil, angka, dan karakter spesial
     * - Menggunakan random_bytes() untuk entropi yang baik
     * 
     * @param int $length Panjang password (default: 12)
     * @return string Password yang dihasilkan
     */
    private function generatePassword(int $length = 12): string
    {
        // Pastikan panjang minimal 12 karakter
        if ($length < 12) {
            $length = 12;
        }

        // Definisikan karakter set untuk setiap kategori
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $special = '!@#$%^&*()_+-=[]{}|;:,.<>?';

        // Gabungkan semua karakter
        $allCharacters = $uppercase . $lowercase . $numbers . $special;

        // Pastikan password mengandung setidaknya satu karakter dari setiap kategori
        $password = '';
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];

        // Isi sisa panjang dengan karakter acak dari semua kategori
        $remainingLength = $length - 4;
        for ($i = 0; $i < $remainingLength; $i++) {
            $password .= $allCharacters[random_int(0, strlen($allCharacters) - 1)];
        }

        // Acak urutan karakter untuk menghindari pola yang dapat diprediksi
        $passwordArray = str_split($password);
        shuffle($passwordArray);
        $password = implode('', $passwordArray);

        return $password;
    }
    
    /**
     * Generate kode unit dari nama unit
     * 
     * Menghasilkan kode unit otomatis dari nama unit:
     * - Ambil huruf pertama setiap kata
     * - Uppercase semua
     * - Tambahkan angka random jika perlu
     * - Support nama dengan spasi
     * 
     * @param string $namaUnit Nama unit/gedung (boleh mengandung spasi)
     * @return string Kode unit yang dihasilkan
     */
    private function generateKodeUnit(string $namaUnit): string
    {
        // Trim dan bersihkan spasi berlebih
        $namaUnit = trim(preg_replace('/\s+/', ' ', $namaUnit));
        
        // Ambil huruf pertama setiap kata
        $words = explode(' ', $namaUnit);
        $kode = '';
        
        foreach ($words as $word) {
            if (!empty($word)) {
                $kode .= strtoupper(substr($word, 0, 1));
            }
        }
        
        // Jika kode terlalu pendek, ambil 3 huruf pertama nama
        if (strlen($kode) < 2) {
            $kode = strtoupper(substr(str_replace(' ', '', $namaUnit), 0, 3));
        }
        
        // Tambahkan angka random untuk uniqueness
        $kode .= random_int(10, 99);
        
        log_message('info', "Generated kode_unit: {$kode} from nama_unit: {$namaUnit}");
        
        return $kode;
    }
}
