<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SetupSecurity extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'setup:security';
    protected $description = 'Setup Security role and transportation table';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        try {
            // Create transportation_data table
            $createTableSQL = "
            CREATE TABLE IF NOT EXISTS transportation_data (
                id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                tanggal DATE NOT NULL,
                waktu TIME NOT NULL,
                jenis_kendaraan ENUM('motor','mobil','bus','truk','sepeda') NOT NULL,
                plat_nomor VARCHAR(20) NOT NULL,
                tujuan VARCHAR(100) NOT NULL,
                status ENUM('masuk','keluar') NOT NULL,
                keterangan TEXT NULL,
                petugas_id INT(11) UNSIGNED NOT NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                PRIMARY KEY (id),
                KEY tanggal (tanggal),
                KEY petugas_id (petugas_id)
            ) DEFAULT CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci
            ";
            
            $db->query($createTableSQL);
            CLI::write('✓ Transportation table created successfully', 'green');
            
            // Check if security user already exists
            $existingUser = $db->table('users')
                ->where('username', 'security_polban')
                ->get()
                ->getRowArray();
            
            if (!$existingUser) {
                // Insert security user
                $userData = [
                    'username' => 'security_polban',
                    'email' => 'security@polban.ac.id',
                    'password' => password_hash('security123', PASSWORD_DEFAULT),
                    'nama_lengkap' => 'Security POLBAN',
                    'role' => 'security',
                    'status_aktif' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                $db->table('users')->insert($userData);
                CLI::write('✓ Security user created successfully', 'green');
                CLI::write('  Username: security_polban', 'yellow');
                CLI::write('  Password: security123', 'yellow');
            } else {
                CLI::write('✓ Security user already exists', 'yellow');
            }
            
            CLI::newLine();
            CLI::write('✅ Security role setup completed!', 'green');
            CLI::write('You can now login with:', 'white');
            CLI::write('Username: security_polban', 'cyan');
            CLI::write('Password: security123', 'cyan');
            
        } catch (\Exception $e) {
            CLI::error('❌ Error: ' . $e->getMessage());
        }
    }
}