<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\UnitModel;

class UserManagement extends BaseController
{
    protected $userModel;
    protected $unitModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->unitModel = new UnitModel();
    }

    public function index()
    {
        log_message('info', '=== UserManagement::index() called ===');
        
        $session = session();
        $user = $session->get('user');
        
        log_message('info', 'Session isLoggedIn: ' . ($session->get('isLoggedIn') ? 'true' : 'false'));
        log_message('info', 'User data: ' . json_encode($user));
        
        if (!$session->get('isLoggedIn') || !isset($user['role']) || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            log_message('warning', 'Unauthorized access attempt to user management');
            return redirect()->to('/auth/login')->with('error', 'Silakan login terlebih dahulu');
        }

        log_message('info', 'User authorized, loading user management page');

        try {
            // Get filter parameters
            $roleFilter = $this->request->getGet('role');
            $statusFilter = $this->request->getGet('status');
            $unitFilter = $this->request->getGet('unit');

            // Build query
            $builder = $this->userModel
                ->select('users.*, unit.nama_unit')
                ->join('unit', 'unit.id = users.unit_id', 'left');

            // Apply filters
            if (!empty($roleFilter)) {
                $builder->where('users.role', $roleFilter);
            }

            if ($statusFilter !== '' && $statusFilter !== null) {
                $builder->where('users.status_aktif', $statusFilter);
            }

            if (!empty($unitFilter)) {
                $builder->where('users.unit_id', $unitFilter);
            }

            // Pagination
            $perPage = 10;
            $users = $builder->orderBy('users.created_at', 'DESC')->paginate($perPage);
            $pager = $this->userModel->pager;

            $units = $this->unitModel->where('status_aktif', 1)->findAll();

            // Calculate statistics
            $allUsers = $this->userModel->findAll(); // Get all users for stats
            $stats = [
                'total' => count($allUsers),
                'active' => count(array_filter($allUsers, fn($u) => $u['status_aktif'] == 1)),
                'inactive' => count(array_filter($allUsers, fn($u) => $u['status_aktif'] == 0)),
                'admin_role' => count(array_filter($allUsers, fn($u) => $u['role'] == 'admin_pusat')),
                'tps_role' => count(array_filter($allUsers, fn($u) => $u['role'] == 'pengelola_tps')),
                'user_role' => count(array_filter($allUsers, fn($u) => $u['role'] == 'user'))
            ];

            $data = [
                'title' => 'User Management',
                'users' => $users,
                'pager' => $pager,
                'units' => $units,
                'allUnits' => $units, // Untuk dropdown filter
                'stats' => $stats,
                'allRoles' => [
                    'admin_pusat' => 'Admin Pusat',
                    'pengelola_tps' => 'Pengelola TPS',
                    'user' => 'User'
                ],
                'allStatus' => [
                    '1' => 'Aktif',
                    '0' => 'Tidak Aktif'
                ],
                'filters' => [
                    'role' => $roleFilter ?? '',
                    'status' => $statusFilter ?? '',
                    'unit' => $unitFilter ?? ''
                ]
            ];

            return view('admin_pusat/user_management', $data);

        } catch (\Exception $e) {
            log_message('error', 'User Management Error: ' . $e->getMessage());
            
            return view('admin_pusat/user_management', [
                'title' => 'User Management',
                'users' => [],
                'units' => [],
                'allUnits' => [], // Untuk dropdown filter
                'stats' => [
                    'total' => 0, 
                    'active' => 0, 
                    'inactive' => 0, 
                    'admin_role' => 0,
                    'tps_role' => 0,
                    'user_role' => 0
                ],
                'allRoles' => [
                    'admin_pusat' => 'Admin Pusat',
                    'pengelola_tps' => 'Pengelola TPS',
                    'user' => 'User'
                ],
                'allStatus' => [
                    '1' => 'Aktif',
                    '0' => 'Tidak Aktif'
                ],
                'filters' => [
                    'role' => '',
                    'status' => '',
                    'unit' => ''
                ],
                'error' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage()
            ]);
        }
    }

    public function getUser($id)
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !isset($user['role']) || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $user = $this->userModel->find($id);
            
            if (!$user) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $user
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function create()
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !isset($user['role']) || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $password = $this->request->getPost('password');
            $username = $this->request->getPost('username');
            $email = $this->request->getPost('email');
            $nama_lengkap = $this->request->getPost('nama_lengkap');
            $role = $this->request->getPost('role');
            $unit_id = $this->request->getPost('unit_id');
            
            // Validation
            if (empty($username) || empty($email) || empty($password) || empty($nama_lengkap) || empty($role)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Semua field wajib diisi'
                ]);
            }
            
            // Check if username already exists
            $existingUser = $this->userModel->where('username', $username)->first();
            if ($existingUser) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Username sudah digunakan'
                ]);
            }
            
            // Check if email already exists
            $existingEmail = $this->userModel->where('email', $email)->first();
            if ($existingEmail) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Email sudah digunakan'
                ]);
            }
            
            $data = [
                'username' => $username,
                'email' => $email,
                'password' => $password, // Plain text password, no hash
                'nama_lengkap' => $nama_lengkap,
                'role' => $role,
                'unit_id' => $unit_id ?: null,
                'status_aktif' => 1
            ];

            $insertId = $this->userModel->insert($data);
            
            if ($insertId) {
                log_message('info', 'User created successfully: ' . $username . ' (ID: ' . $insertId . ')');
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'User berhasil ditambahkan',
                    'password' => $password,
                    'username' => $username
                ]);
            }
            
            // If insert failed, get validation errors
            $errors = $this->userModel->errors();
            log_message('error', 'User insert failed: ' . json_encode($errors));

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menambahkan user: ' . implode(', ', $errors)
            ]);

        } catch (\Exception $e) {
            log_message('error', 'User create exception: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function update($id)
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !isset($user['role']) || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $data = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'nama_lengkap' => $this->request->getPost('nama_lengkap'),
                'role' => $this->request->getPost('role'),
                'unit_id' => $this->request->getPost('unit_id')
            ];

            $passwordInfo = '';
            
            // Update password only if provided
            $password = $this->request->getPost('password');
            if (!empty($password)) {
                $data['password'] = $password; // Plain text password, no hash
                $passwordInfo = " Password baru: $password (Catat password ini!)";
            }

            if ($this->userModel->update($id, $data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'User berhasil diupdate.' . $passwordInfo
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengupdate user'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function toggleStatus($id)
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !isset($user['role']) || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $user = $this->userModel->find($id);
            
            if (!$user) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ]);
            }

            $newStatus = !$user['status_aktif'];
            
            if ($this->userModel->update($id, ['status_aktif' => $newStatus])) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Status user berhasil diubah'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengubah status user'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function resetPassword($id)
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !isset($user['role']) || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $user = $this->userModel->find($id);
            
            if (!$user) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ]);
            }

            // Reset password to default: password123
            $defaultPassword = 'password123';
            $data = [
                'password' => $defaultPassword // Plain text password, no hash
            ];

            if ($this->userModel->update($id, $data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Password berhasil direset ke: password123',
                    'default_password' => $defaultPassword
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mereset password'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function delete($id)
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !isset($user['role']) || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            if ($this->userModel->delete($id)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'User berhasil dihapus'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus user'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function import()
    {
        // Set JSON response header
        $this->response->setContentType('application/json');
        
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !isset($user['role']) || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $file = $this->request->getFile('excel_file');
            $skipDuplicates = $this->request->getPost('skip_duplicates') === 'on';

            log_message('info', 'Import started - File: ' . ($file ? $file->getName() : 'null'));

            if (!$file || !$file->isValid()) {
                log_message('error', 'File not valid: ' . ($file ? $file->getErrorString() : 'null'));
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'File Excel tidak valid: ' . ($file ? $file->getErrorString() : 'File tidak ditemukan')
                ]);
            }

            // Validate file size (max 5MB)
            $fileSize = $file->getSize();
            
            // Validate file - cek MIME type saja (lebih reliable daripada ekstensi)
            $clientMimeType = $file->getClientMimeType();
            $originalName = $file->getClientName();
            
            log_message('info', 'File upload - Name: ' . $originalName . ', MIME: ' . $clientMimeType . ', Size: ' . $fileSize);
            
            // MIME type yang valid untuk Excel .xlsx
            $validMimeTypes = [
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/zip', // .xlsx kadang terdeteksi sebagai zip
                'application/octet-stream' // Fallback
            ];
            
            if (!in_array($clientMimeType, $validMimeTypes)) {
                log_message('warning', 'Invalid MIME type: ' . $clientMimeType);
                // Hanya warning, tetap lanjut (biar PhpSpreadsheet yang validasi)
            }
            
            if ($fileSize > 5 * 1024 * 1024) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Ukuran file maksimal 5MB (file: ' . round($fileSize / 1024 / 1024, 2) . 'MB)'
                ]);
            }

            // Use ExcelImportService
            $excelImportService = new \App\Services\ExcelImportService($this->userModel, $this->unitModel);
            
            // Import users from Excel
            $result = $excelImportService->importUsers($file->getTempName(), $skipDuplicates);
            
            log_message('info', 'Import result: ' . json_encode($result));

            return $this->response->setJSON($result);

        } catch (\Exception $e) {
            log_message('error', 'User import exception: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function downloadTemplate()
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !isset($user['role']) || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return redirect()->to('/auth/login')->with('error', 'Silakan login terlebih dahulu');
        }

        try {
            // Use ExcelTemplateService to generate Excel template
            $excelTemplateService = new \App\Services\ExcelTemplateService();
            $filepath = $excelTemplateService->generateTemplate();
            
            // Set headers for Excel download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="template_import_users.xlsx"');
            header('Cache-Control: max-age=0');
            
            // Output file
            readfile($filepath);
            
            // Delete temporary file
            unlink($filepath);
            
            exit;

        } catch (\Exception $e) {
            log_message('error', 'Download template error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal download template: ' . $e->getMessage());
        }
    }
}
