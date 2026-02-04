<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Halaman login
     */
    public function login()
    {
        // Jika sudah login, redirect ke dashboard sesuai role
        if (session()->get('isLoggedIn')) {
            return $this->redirectToDashboard(session()->get('user')['role']);
        }

        return view('auth/login');
    }

    /**
     * Test login page (for debugging)
     */
    public function testLogin()
    {
        // Jika sudah login, redirect ke dashboard sesuai role
        if (session()->get('isLoggedIn')) {
            return $this->redirectToDashboard(session()->get('user')['role']);
        }

        return view('auth/test_login');
    }

    /**
     * Proses login
     */
    public function processLogin()
    {
        $clientIP = $this->request->getIPAddress();

        $rules = [
            'login' => [
                'rules' => 'required|max_length[100]',
                'errors' => [
                    'required' => 'Email atau username harus diisi',
                    'max_length' => 'Email atau username terlalu panjang'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[6]|max_length[255]',
                'errors' => [
                    'required' => 'Password harus diisi',
                    'min_length' => 'Password minimal 6 karakter',
                    'max_length' => 'Password terlalu panjang'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $login = trim($this->request->getPost('login'));
        $password = $this->request->getPost('password');

        try {
            // Cari user berdasarkan email atau username
            $user = $this->userModel->getUserForLogin($login);

            // Debug logging
            log_message('debug', "Login attempt for: " . $login);
            log_message('debug', "User found: " . ($user ? 'Yes' : 'No'));
            
            if ($user) {
                log_message('debug', "User ID: " . $user['id'] . ", Username: " . $user['username'] . ", Role: " . $user['role'] . ", Status: " . ($user['status_aktif'] ? 'Active' : 'Inactive'));
            }

            if (!$user) {
                log_message('debug', "Login failed: User not found for login: " . $login);
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Email/username atau password salah');
            }

            // Check if account is locked or inactive
            if (!$user['status_aktif']) {
                log_message('debug', "Login failed: Account inactive for user: " . $user['username']);
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Akun Anda tidak aktif. Hubungi administrator.');
            }

            // Verifikasi password
            log_message('debug', "Verifying password for user: " . $user['username']);
            $passwordMatch = $this->userModel->verifyPassword($password, $user['password']);
            log_message('debug', "Password verification result: " . ($passwordMatch ? 'Success' : 'Failed'));
            
            if (!$passwordMatch) {
                log_message('debug', "Login failed: Wrong password for user: " . $user['username']);
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Email/username atau password salah');
            }

            // Validasi role
            $allowedRoles = ['admin_pusat', 'super_admin', 'user', 'pengelola_tps'];
            
            if (empty($user['role'])) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Role user kosong. Hubungi administrator untuk memperbaiki data role.");
            }
            
            if (!in_array($user['role'], $allowedRoles)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Role '{$user['role']}' tidak diizinkan. Hubungi administrator.");
            }

            // Get user with unit information for session
            $userWithUnit = $this->userModel->getUserWithUnit($user['id']);

            // Get gedung berdasarkan unit (otomatis)
            $gedung = null;
            if ($user['unit_id']) {
                $unitModel = new \App\Models\UnitModel();
                $gedung = $unitModel->getGedungForUser($user['unit_id']);
            }

            // Set session with enhanced security
            $sessionData = [
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'nama_lengkap' => $user['nama_lengkap'],
                    'role' => $user['role'],
                    'unit_id' => $user['unit_id'],
                    'nama_unit' => $userWithUnit['nama_unit'] ?? null,
                    'kode_unit' => $userWithUnit['kode_unit'] ?? null,
                    'gedung' => $gedung
                ],
                'isLoggedIn' => true,
                'login_time' => time(),
                'last_activity' => time(),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString(),
                'last_regeneration' => time()
            ];

            session()->set($sessionData);

            // Update last login
            $this->userModel->updateLastLogin($user['id']);

            // Redirect ke dashboard sesuai role
            return $this->redirectToDashboard($user['role']);
            
        } catch (\Exception $e) {
            log_message('error', 'Login Error: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login')->with('success', 'Anda telah berhasil logout');
    }

    /**
     * Redirect ke dashboard sesuai role
     */
    private function redirectToDashboard($role)
    {
        switch ($role) {
            case 'admin_pusat':
            case 'super_admin':
                return redirect()->to('/admin-pusat/dashboard')
                    ->with('success', 'Selamat datang di Dashboard Admin');

            case 'user':
                return redirect()->to('/user/dashboard')
                    ->with('success', 'Selamat datang di Dashboard User');

            case 'pengelola_tps':
                return redirect()->to('/pengelola-tps/dashboard')
                    ->with('success', 'Selamat datang di Dashboard TPS');

            default:
                session()->destroy();
                return redirect()->to('/auth/login')
                    ->with('error', "Role '{$role}' tidak dikenali. Hubungi administrator.");
        }
    }

    /**
     * Middleware untuk cek login
     */
    public function checkAuth()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login')
                ->with('error', 'Silakan login terlebih dahulu');
        }
    }

    /**
     * Middleware untuk cek role
     */
    public function checkRole($allowedRoles = [])
    {
        $this->checkAuth();

        $userRole = session()->get('user')['role'] ?? null;

        if (!in_array($userRole, $allowedRoles)) {
            return redirect()->to('/auth/login')
                ->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }
    }
}