<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Profil extends BaseController
{
    public function index()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            $session = session();
            $user = $session->get('user');
            
            // Get user data from database
            $userModel = new \App\Models\UserModel();
            $userData = $userModel->find($user['id']);
            
            if (!$userData) {
                return redirect()->to('/auth/login')->with('error', 'User tidak ditemukan');
            }
            
            // Get unit data if exists
            $unitData = null;
            if (!empty($userData['unit_id'])) {
                $unitModel = new \App\Models\UnitModel();
                $unitData = $unitModel->find($userData['unit_id']);
            }
            
            $viewData = [
                'title' => 'Profil Akun',
                'user' => $userData,
                'unit' => $unitData
            ];

            return view('admin_pusat/profil', $viewData);

        } catch (\Exception $e) {
            log_message('error', 'Admin Profil Error: ' . $e->getMessage());
            
            return redirect()->to('/admin-pusat/dashboard')
                ->with('error', 'Terjadi kesalahan saat memuat profil');
        }
    }

    public function update()
    {
        try {
            if (!$this->validateSession()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Session invalid']);
            }

            $session = session();
            $user = $session->get('user');
            
            // Validate input
            $namaLengkap = $this->request->getPost('nama_lengkap');
            $email = $this->request->getPost('email');
            
            if (empty($namaLengkap) || empty($email)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Nama lengkap dan email wajib diisi'
                ]);
            }
            
            // Check email uniqueness (exclude current user)
            $userModel = new \App\Models\UserModel();
            $existingEmail = $userModel->where('email', $email)
                                      ->where('id !=', $user['id'])
                                      ->first();
            
            if ($existingEmail) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Email sudah digunakan oleh user lain'
                ]);
            }
            
            // Prepare update data
            $data = [
                'nama_lengkap' => $namaLengkap,
                'email' => $email,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // Update
            if ($userModel->update($user['id'], $data)) {
                // Update session
                $updatedUser = $userModel->find($user['id']);
                $session->set('user', $updatedUser);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Profil berhasil diperbarui'
                ]);
            }
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memperbarui profil'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Admin Profil Update Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui profil: ' . $e->getMessage()
            ]);
        }
    }

    public function changePassword()
    {
        try {
            if (!$this->validateSession()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Session invalid']);
            }

            $session = session();
            $user = $session->get('user');
            
            // Get input
            $passwordLama = $this->request->getPost('password_lama');
            $passwordBaru = $this->request->getPost('password_baru');
            $passwordKonfirmasi = $this->request->getPost('password_konfirmasi');
            
            // Validate
            if (empty($passwordLama) || empty($passwordBaru) || empty($passwordKonfirmasi)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Semua field password wajib diisi'
                ]);
            }
            
            if ($passwordBaru !== $passwordKonfirmasi) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Password baru dan konfirmasi tidak cocok'
                ]);
            }
            
            if (strlen($passwordBaru) < 6) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Password baru minimal 6 karakter'
                ]);
            }
            
            // Get current user data
            $userModel = new \App\Models\UserModel();
            $userData = $userModel->find($user['id']);
            
            // Verify old password (plain text comparison for development)
            if ($userData['password'] !== $passwordLama) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Password lama tidak sesuai'
                ]);
            }
            
            // Update password (plain text for development)
            $data = [
                'password' => $passwordBaru,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            if ($userModel->update($user['id'], $data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Password berhasil diubah'
                ]);
            }
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengubah password'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Admin Change Password Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah password: ' . $e->getMessage()
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
