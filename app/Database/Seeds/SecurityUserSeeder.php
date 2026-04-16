<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SecurityUserSeeder extends Seeder
{
    public function run()
    {
        // Delete existing security user if exists
        $this->db->table('users')
            ->where('username', 'security_polban')
            ->delete();

        // Insert security user
        $data = [
            'username' => 'security_polban',
            'email' => 'security@polban.ac.id',
            'password' => password_hash('security123', PASSWORD_DEFAULT),
            'nama_lengkap' => 'Security POLBAN',
            'role' => 'security',
            'status_aktif' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->table('users')->insert($data);
        
        echo "Security user created successfully!\n";
        echo "Username: security_polban\n";
        echo "Password: security123\n";
    }
}