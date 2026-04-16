<?php

namespace App\Controllers\Security;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Profile extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        
        // Check if user is logged in and has security role
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        
        $userRole = session()->get('user')['role'] ?? null;
        if ($userRole !== 'security') {
            return redirect()->to('/auth/login')->with('error', 'Akses ditolak');
        }
    }

    /**
     * Security Profile page
     */
    public function index()
    {
        $userId = session()->get('user')['id'];
        $user = $this->userModel->find($userId);

        $data = [
            'title' => 'Profil Akun Security',
            'user' => $user
        ];

        return view('security/profile', $data);
    }

    /**
     * Update profile
     */
    /**
     * Update profile
     */
    public function update()
    {
        $userId = session()->get('user')['id'];

        // Basic validation rules (always required)
        $rules = [
            'nama_lengkap' => 'required|max_length[100]',
            'email' => 'required|valid_email|max_length[100]',
            'username' => 'required|max_length[50]'
        ];

        // Get password inputs
        $password = $this->request->getPost('password');
        $passwordConfirm = $this->request->getPost('password_confirm');

        // KONDISI VALIDASI: JIKA kolom password diisi, MAKA jalankan validasi
        // JIKA kolom password kosong, MAKA abaikan validasi password sama sekali
        if (!empty($password)) {
            $rules['password'] = 'required|min_length[5]|matches[password_confirm]';
            $rules['password_confirm'] = 'required';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // LOGIKA UPDATE DATABASE
        $data = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'username'     => $this->request->getPost('username'),
            'email'        => $this->request->getPost('email')
        ];

        // Hanya update password jika kolom diisi
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        try {
            $this->userModel->update($userId, $data);

            // Update session data
            $sessionUser = session()->get('user');
            $sessionUser['nama_lengkap'] = $data['nama_lengkap'];
            $sessionUser['email'] = $data['email'];
            $sessionUser['username'] = $data['username'];
            session()->set('user', $sessionUser);

            return redirect()->to('/security/profile')
                ->with('success', 'Profil berhasil diperbarui');

        } catch (\Exception $e) {
            log_message('error', 'Profile update error: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui profil. Silakan coba lagi.');
        }
    }

}