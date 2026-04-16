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

    public function userLog($id)
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !isset($user['role']) || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return redirect()->to('/auth/login')->with('error', 'Silakan login terlebih dahulu');
        }

        try {
            // Get user information
            $targetUser = $this->userModel->getUserWithUnit($id);
            
            if (!$targetUser) {
                return redirect()->to('/admin-pusat/user-management')
                    ->with('error', 'User tidak ditemukan');
            }

            // Get user's limbah B3 activity log
            $db = \Config\Database::connect();
            
            // Query for limbah B3 data
            $limbahQuery = $db->query("
                SELECT 
                    lb.id,
                    lb.tanggal_input as tanggal,
                    lb.lokasi,
                    lb.timbulan,
                    lb.satuan,
                    lb.status,
                    mlb.nama_limbah as nama,
                    mlb.kode_limbah as kode,
                    mlb.karakteristik,
                    mlb.bentuk_fisik,
                    'Limbah B3' as kategori
                FROM limbah_b3 lb
                LEFT JOIN master_limbah_b3 mlb ON mlb.id = lb.master_b3_id
                WHERE lb.id_user = ?
            ", [$id]);
            
            $limbahLogs = $limbahQuery->getResultArray();
            
            // Query for waste management data
            $wasteQuery = $db->query("
                SELECT 
                    wm.id,
                    wm.tanggal as tanggal,
                    wm.gedung as lokasi,
                    wm.berat_kg as timbulan,
                    'kg' as satuan,
                    wm.status,
                    wm.nama_sampah as nama,
                    wm.jenis_sampah as kode,
                    '' as karakteristik,
                    '' as bentuk_fisik,
                    'Sampah' as kategori
                FROM waste_management wm
                WHERE wm.user_id = ?
            ", [$id]);
            
            $wasteLogs = $wasteQuery->getResultArray();
            
            // Combine both arrays
            $allLogs = array_merge($limbahLogs, $wasteLogs);
            
            // Sort by date descending
            usort($allLogs, function($a, $b) {
                return strtotime($b['tanggal']) - strtotime($a['tanggal']);
            });

            // Get activity statistics
            $stats = [
                'total_entries' => count($allLogs),
                'limbah_count' => count(array_filter($allLogs, fn($log) => $log['kategori'] === 'Limbah B3')),
                'waste_count' => count(array_filter($allLogs, fn($log) => $log['kategori'] === 'Sampah')),
                'draft_count' => count(array_filter($allLogs, fn($log) => $log['status'] === 'draft')),
                'submitted_count' => count(array_filter($allLogs, fn($log) => in_array($log['status'], ['dikirim_ke_tps', 'submitted']))),
                'approved_count' => count(array_filter($allLogs, fn($log) => in_array($log['status'], ['disetujui_tps', 'disetujui_admin', 'approved']))),
                'rejected_count' => count(array_filter($allLogs, fn($log) => in_array($log['status'], ['ditolak_tps', 'rejected']))),
                'total_timbulan' => array_sum(array_column($allLogs, 'timbulan'))
            ];

            $viewData = [
                'title' => 'Log Aktivitas User - ' . $targetUser['nama_lengkap'],
                'target_user' => $targetUser,
                'all_logs' => $allLogs,
                'stats' => $stats
            ];

            return view('admin_pusat/user_log', $viewData);

        } catch (\Exception $e) {
            log_message('error', 'User Log Error: ' . $e->getMessage());
            
            return redirect()->to('/admin-pusat/user-management')
                ->with('error', 'Terjadi kesalahan saat memuat log aktivitas: ' . $e->getMessage());
        }
    }
    public function exportUserLog($id)
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !isset($user['role']) || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return redirect()->to('/admin-pusat/user-management')->with('error', 'Unauthorized');
        }

        try {
            // Get user information
            $targetUser = $this->userModel->getUserWithUnit($id);
            
            if (!$targetUser) {
                return redirect()->to('/admin-pusat/user-management')->with('error', 'User tidak ditemukan');
            }

            // Get combined log data (same as userLog method)
            $db = \Config\Database::connect();
            
            // Query for limbah B3 data
            $limbahQuery = $db->query("
                SELECT 
                    lb.id,
                    lb.tanggal_input as tanggal,
                    lb.lokasi,
                    lb.timbulan,
                    lb.satuan,
                    lb.status,
                    mlb.nama_limbah as nama,
                    mlb.kode_limbah as kode,
                    mlb.karakteristik,
                    mlb.bentuk_fisik,
                    'Limbah B3' as kategori
                FROM limbah_b3 lb
                LEFT JOIN master_limbah_b3 mlb ON mlb.id = lb.master_b3_id
                WHERE lb.id_user = ?
            ", [$id]);
            
            $limbahLogs = $limbahQuery->getResultArray();
            
            // Query for waste management data
            $wasteQuery = $db->query("
                SELECT 
                    wm.id,
                    wm.tanggal as tanggal,
                    wm.gedung as lokasi,
                    wm.berat_kg as timbulan,
                    'kg' as satuan,
                    wm.status,
                    wm.nama_sampah as nama,
                    wm.jenis_sampah as kode,
                    '' as karakteristik,
                    '' as bentuk_fisik,
                    'Sampah' as kategori
                FROM waste_management wm
                WHERE wm.user_id = ?
            ", [$id]);
            
            $wasteLogs = $wasteQuery->getResultArray();
            
            // Combine both arrays
            $allLogs = array_merge($limbahLogs, $wasteLogs);
            
            // Sort by date descending
            usort($allLogs, function($a, $b) {
                return strtotime($b['tanggal']) - strtotime($a['tanggal']);
            });

            // Create CSV content
            $filename = 'Log_Aktivitas_' . str_replace(' ', '_', $targetUser['nama_lengkap']) . '_' . date('Y-m-d_H-i-s') . '.csv';
            
            // Set headers for CSV download
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            // Create file pointer connected to the output stream
            $output = fopen('php://output', 'w');
            
            // Add BOM for UTF-8 (helps with Excel compatibility)
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Add CSV headers
            fputcsv($output, [
                'No',
                'Tanggal',
                'Kategori',
                'Nama',
                'Kode',
                'Lokasi',
                'Timbulan',
                'Satuan',
                'Status',
                'Karakteristik'
            ]);
            
            // Add data rows
            $no = 1;
            foreach ($allLogs as $log) {
                fputcsv($output, [
                    $no++,
                    date('d/m/Y H:i', strtotime($log['tanggal'])),
                    $log['kategori'],
                    $log['nama'] ?? '-',
                    $log['kode'] ?? '-',
                    $log['lokasi'] ?? '-',
                    $log['timbulan'] ?? 0,
                    $log['satuan'] ?? '',
                    $log['status'] ?? '',
                    $log['karakteristik'] ?? ''
                ]);
            }
            
            fclose($output);
            exit;

        } catch (\Exception $e) {
            log_message('error', 'Export User Log Error: ' . $e->getMessage());
            
            return redirect()->to('/admin-pusat/user-management')
                ->with('error', 'Terjadi kesalahan saat export: ' . $e->getMessage());
        }
    }

    public function deleteLogEntry()
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !isset($user['role']) || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $entryId = $this->request->getPost('entry_id');
            $entryType = $this->request->getPost('entry_type'); // 'Limbah B3' or 'Sampah'
            
            if (!$entryId || !$entryType) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Parameter tidak lengkap'
                ]);
            }

            $db = \Config\Database::connect();
            
            if ($entryType === 'Limbah B3') {
                // Delete from limbah_b3 table
                $result = $db->table('limbah_b3')->where('id', $entryId)->delete();
            } elseif ($entryType === 'Sampah') {
                // Delete from waste_management table
                $result = $db->table('waste_management')->where('id', $entryId)->delete();
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tipe data tidak valid'
                ]);
            }

            if ($result) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data berhasil dihapus'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus data'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Delete Log Entry Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function bulkDeleteLog()
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !isset($user['role']) || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $selectedItemsJson = $this->request->getPost('selected_items');
            
            if (!$selectedItemsJson) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tidak ada data yang dipilih'
                ]);
            }

            // Parse JSON string to array
            $selectedItems = json_decode($selectedItemsJson, true);
            
            if (!$selectedItems || !is_array($selectedItems)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Format data tidak valid'
                ]);
            }

            $db = \Config\Database::connect();
            $deletedCount = 0;
            $errors = [];

            // Process each selected item
            foreach ($selectedItems as $item) {
                if (!isset($item['id']) || !isset($item['kategori'])) {
                    $errors[] = "Item tidak valid: " . json_encode($item);
                    continue;
                }

                $entryId = $item['id'];
                $entryType = $item['kategori'];

                try {
                    if ($entryType === 'Limbah B3') {
                        // Delete from limbah_b3 table
                        $result = $db->table('limbah_b3')->where('id', $entryId)->delete();
                    } elseif ($entryType === 'Sampah') {
                        // Delete from waste_management table
                        $result = $db->table('waste_management')->where('id', $entryId)->delete();
                    } else {
                        $errors[] = "Kategori tidak valid untuk ID $entryId: $entryType";
                        continue;
                    }

                    if ($result) {
                        $deletedCount++;
                    } else {
                        $errors[] = "Gagal menghapus $entryType dengan ID $entryId";
                    }

                } catch (\Exception $e) {
                    $errors[] = "Error menghapus ID $entryId: " . $e->getMessage();
                }
            }

            // Prepare response
            $totalSelected = count($selectedItems);
            $message = "Berhasil menghapus $deletedCount dari $totalSelected data yang dipilih";
            
            if (!empty($errors)) {
                $message .= ". Beberapa error: " . implode(', ', array_slice($errors, 0, 3));
                if (count($errors) > 3) {
                    $message .= " dan " . (count($errors) - 3) . " error lainnya";
                }
            }

            return $this->response->setJSON([
                'success' => $deletedCount > 0,
                'message' => $message,
                'deleted_count' => $deletedCount,
                'total_selected' => $totalSelected,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Bulk Delete Log Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
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
