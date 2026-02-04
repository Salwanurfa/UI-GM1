<?php

namespace App\Services\Admin;

use App\Models\UserModel;
use App\Models\UnitModel;

class UserManagementService
{
    protected $userModel;
    protected $unitModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->unitModel = new UnitModel();
    }

    public function getUserById(int $id): array
    {
        try {
            $user = $this->userModel
                ->select('users.*, units.nama_unit')
                ->join('units', 'units.id = users.unit_id', 'left')
                ->find($id);

            if (!$user) {
                return ['success' => false, 'message' => 'User tidak ditemukan'];
            }

            return ['success' => true, 'data' => $user];

        } catch (\Exception $e) {
            log_message('error', 'Get User By ID Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem'];
        }
    }

    public function getUserData(): array
    {
        try {
            return [
                'users' => $this->getUsersWithUnit(),
                'roles' => $this->getAvailableRoles(),
                'units' => $this->unitModel->findAll()
            ];
        } catch (\Exception $e) {
            log_message('error', 'User Management Service Error: ' . $e->getMessage());
            
            return [
                'users' => [],
                'roles' => [],
                'units' => []
            ];
        }
    }

    public function createUser(array $data): array
    {
        try {
            $validation = $this->validateUserData($data);
            if (!$validation['valid']) {
                return ['success' => false, 'message' => $validation['message']];
            }

            // Check if username or email already exists
            if ($this->userModel->where('username', $data['username'])->first()) {
                return ['success' => false, 'message' => 'Username sudah digunakan'];
            }

            if ($this->userModel->where('email', $data['email'])->first()) {
                return ['success' => false, 'message' => 'Email sudah digunakan'];
            }

            $userData = [
                'username' => $data['username'],
                'email' => $data['email'],
                'nama_lengkap' => $data['nama_lengkap'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'role' => $data['role'],
                'unit_id' => $data['unit_id'],
                'status_aktif' => 1,
                'created_by' => session()->get('user')['id'],
                'created_at' => date('Y-m-d H:i:s')
            ];

            $result = $this->userModel->insert($userData);
            
            if ($result) {
                return ['success' => true, 'message' => 'User berhasil dibuat'];
            }

            return ['success' => false, 'message' => 'Gagal membuat user'];

        } catch (\Exception $e) {
            log_message('error', 'Create User Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem'];
        }
    }

    public function updateUser(int $id, array $data): array
    {
        try {
            $validation = $this->validateUserData($data, $id);
            if (!$validation['valid']) {
                return ['success' => false, 'message' => $validation['message']];
            }

            $user = $this->userModel->find($id);
            if (!$user) {
                return ['success' => false, 'message' => 'User tidak ditemukan'];
            }

            // Check if username or email already exists (excluding current user)
            $existingUser = $this->userModel->where('username', $data['username'])
                                          ->where('id !=', $id)
                                          ->first();
            if ($existingUser) {
                return ['success' => false, 'message' => 'Username sudah digunakan'];
            }

            $existingEmail = $this->userModel->where('email', $data['email'])
                                           ->where('id !=', $id)
                                           ->first();
            if ($existingEmail) {
                return ['success' => false, 'message' => 'Email sudah digunakan'];
            }

            $userData = [
                'username' => $data['username'],
                'email' => $data['email'],
                'nama_lengkap' => $data['nama_lengkap'],
                'role' => $data['role'],
                'unit_id' => $data['unit_id'],
                'updated_by' => session()->get('user')['id'],
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Update password if provided
            if (!empty($data['password'])) {
                $userData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            $result = $this->userModel->update($id, $userData);
            
            if ($result) {
                return ['success' => true, 'message' => 'User berhasil diupdate'];
            }

            return ['success' => false, 'message' => 'Gagal mengupdate user'];

        } catch (\Exception $e) {
            log_message('error', 'Update User Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem'];
        }
    }

    public function toggleUserStatus(int $id): array
    {
        try {
            $user = $this->userModel->find($id);
            if (!$user) {
                return ['success' => false, 'message' => 'User tidak ditemukan'];
            }

            // Prevent deactivating own account
            $currentUser = session()->get('user');
            if ($id == $currentUser['id']) {
                return ['success' => false, 'message' => 'Tidak dapat mengubah status akun sendiri'];
            }

            $newStatus = $user['status_aktif'] ? 0 : 1;
            
            $result = $this->userModel->update($id, [
                'status_aktif' => $newStatus,
                'updated_by' => $currentUser['id'],
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if ($result) {
                $status = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
                return ['success' => true, 'message' => "User berhasil {$status}"];
            }

            return ['success' => false, 'message' => 'Gagal mengubah status user'];

        } catch (\Exception $e) {
            log_message('error', 'Toggle User Status Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem'];
        }
    }

    public function deleteUser(int $id): array
    {
        try {
            $user = $this->userModel->find($id);
            if (!$user) {
                return ['success' => false, 'message' => 'User tidak ditemukan'];
            }

            // Prevent deleting own account
            $currentUser = session()->get('user');
            if ($id == $currentUser['id']) {
                return ['success' => false, 'message' => 'Tidak dapat menghapus akun sendiri'];
            }

            $result = $this->userModel->delete($id);
            
            if ($result) {
                return ['success' => true, 'message' => 'User berhasil dihapus'];
            }

            return ['success' => false, 'message' => 'Gagal menghapus user'];

        } catch (\Exception $e) {
            log_message('error', 'Delete User Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem'];
        }
    }

    private function getUsersWithUnit(): array
    {
        return $this->userModel
            ->select('users.*, units.nama_unit')
            ->join('units', 'units.id = users.unit_id', 'left')
            ->where('users.role !=', 'admin_pusat')
            ->orderBy('users.created_at', 'DESC')
            ->findAll();
    }

    private function getAvailableRoles(): array
    {
        return [
            'user' => 'User (Petugas Gedung)',
            'pengelola_tps' => 'Pengelola TPS'
        ];
    }

    private function validateUserData(array $data, int $excludeId = null): array
    {
        if (empty($data['username'])) {
            return ['valid' => false, 'message' => 'Username harus diisi'];
        }

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'message' => 'Email harus diisi dan valid'];
        }

        if (empty($data['nama_lengkap'])) {
            return ['valid' => false, 'message' => 'Nama lengkap harus diisi'];
        }

        if (empty($data['role'])) {
            return ['valid' => false, 'message' => 'Role harus dipilih'];
        }

        if (empty($data['unit_id'])) {
            return ['valid' => false, 'message' => 'Unit harus dipilih'];
        }

        // Validate password for new user
        if ($excludeId === null && empty($data['password'])) {
            return ['valid' => false, 'message' => 'Password harus diisi'];
        }

        if (!empty($data['password']) && strlen($data['password']) < 6) {
            return ['valid' => false, 'message' => 'Password minimal 6 karakter'];
        }

        return ['valid' => true, 'message' => ''];
    }
}