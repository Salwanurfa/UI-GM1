<?php

namespace App\Controllers\TPS;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\UnitModel;

class Profile extends BaseController
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
        if (!$this->validateSession()) {
            return redirect()->to('/auth/login');
        }

        $user = session()->get('user');
        $unit = null;
        
        if (isset($user['unit_id'])) {
            $unit = $this->unitModel->find($user['unit_id']);
        }

        return view('pengelola_tps/profile', [
            'title' => 'Edit Profil',
            'user' => $user,
            'unit' => $unit
        ]);
    }

    public function update()
    {
        if (!$this->validateSession()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Session invalid'
            ]);
        }

        $user = session()->get('user');
        
        $rules = [
            'nama_lengkap' => 'required|min_length[3]',
            'email' => 'required|valid_email'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $this->validator->getErrors())
            ]);
        }

        try {
            $data = [
                'nama_lengkap' => $this->request->getPost('nama_lengkap'),
                'email' => $this->request->getPost('email'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->userModel->update($user['id'], $data);

            // Update session
            $updatedUser = $this->userModel->find($user['id']);
            session()->set('user', $updatedUser);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Profil berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Update Profile Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui profil'
            ]);
        }
    }

    public function changePassword()
    {
        if (!$this->validateSession()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Session invalid'
            ]);
        }

        $user = session()->get('user');
        
        $rules = [
            'password_lama' => 'required',
            'password_baru' => 'required|min_length[6]',
            'password_konfirmasi' => 'required|matches[password_baru]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $this->validator->getErrors())
            ]);
        }

        try {
            $userData = $this->userModel->find($user['id']);
            $passwordLama = $this->request->getPost('password_lama');
            
            // Verify old password - support both plain text and hashed
            $isPasswordValid = false;
            
            // Check if password is hashed (bcrypt starts with $2y$)
            if (strlen($userData['password']) == 60 && substr($userData['password'], 0, 4) == '$2y$') {
                // Password is hashed, use password_verify
                $isPasswordValid = password_verify($passwordLama, $userData['password']);
            } else {
                // Password is plain text, compare directly
                $isPasswordValid = ($passwordLama === $userData['password']);
            }
            
            if (!$isPasswordValid) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Password lama tidak sesuai'
                ]);
            }

            // Update password (plain text for consistency with admin)
            $data = [
                'password' => $this->request->getPost('password_baru'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->userModel->update($user['id'], $data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Password berhasil diubah'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Change Password Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah password'
            ]);
        }
    }

    private function validateSession(): bool
    {
        $session = session();
        $user = $session->get('user');
        
        return $session->get('isLoggedIn') && 
               isset($user['id'], $user['role']) &&
               $user['role'] === 'pengelola_tps';
    }
}
