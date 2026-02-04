<?php

// File: app/Database/Seeds/UserSeeder.php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Data user yang sudah ada (jangan duplikasi)
        $existingUsers = [
            'superadmin',
            'adminpusat'
        ];

        // Cek user yang sudah ada
        $userModel = new \App\Models\UserModel();
        
        foreach ($existingUsers as $username) {
            $existing = $userModel->where('username', $username)->first();
            if (!$existing) {
                // Jika belum ada, buat user admin
                if ($username === 'superadmin') {
                    $userModel->insert([
                        'username' => 'superadmin',
                        'email' => 'super.admin@polban.ac.id',
                        'password' => password_hash('password123', PASSWORD_DEFAULT),
                        'role' => 'super_admin',
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                } elseif ($username === 'adminpusat') {
                    $userModel->insert([
                        'username' => 'adminpusat',
                        'email' => 'admin.pusat@polban.ac.id',
                        'password' => password_hash('password123', PASSWORD_DEFAULT),
                        'role' => 'admin_pusat',
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }

        // Tambahkan user dengan role 'user' untuk testing
        $testUsers = [
            [
                'username' => 'user1',
                'email' => 'user1@polban.ac.id',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'user',
                'full_name' => 'John Doe',
                'unit_prodi' => 'Teknik Informatika',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'user2',
                'email' => 'user2@polban.ac.id',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'user',
                'full_name' => 'Jane Smith',
                'unit_prodi' => 'Teknik Elektro',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'mahasiswa1',
                'email' => 'mahasiswa1@polban.ac.id',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'user',
                'full_name' => 'Ahmad Rizki',
                'unit_prodi' => 'Teknik Mesin',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'dosen1',
                'email' => 'dosen1@polban.ac.id',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'user',
                'full_name' => 'Dr. Siti Nurhaliza',
                'unit_prodi' => 'Teknik Kimia',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];

        foreach ($testUsers as $userData) {
            // Cek apakah user sudah ada
            $existing = $userModel->where('username', $userData['username'])->first();
            
            if (!$existing) {
                $userModel->insert($userData);
                echo "User {$userData['username']} berhasil ditambahkan.\n";
            } else {
                echo "User {$userData['username']} sudah ada.\n";
            }
        }
    }
}