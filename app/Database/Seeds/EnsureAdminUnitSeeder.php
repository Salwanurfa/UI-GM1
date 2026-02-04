<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EnsureAdminUnitSeeder extends Seeder
{
    public function run()
    {
        echo "Ensuring admin_unit user exists and is properly configured...\n";

        // Check if admin_unit user exists
        $adminUnit = $this->db->table('users')->where('username', 'admin_unit')->get()->getRowArray();

        if (!$adminUnit) {
            echo "admin_unit user not found, checking for other admin_unit role users...\n";

            $adminUnitRole = $this->db->table('users')->where('role', 'admin_unit')->get()->getRowArray();

            if ($adminUnitRole) {
                echo "Found admin_unit role user: {$adminUnitRole['username']}\n";
                echo "Updating username to 'admin_unit' for easier testing...\n";

                $this->db->table('users')
                    ->where('id', $adminUnitRole['id'])
                    ->update([
                        'username' => 'admin_unit',
                        'password' => 'password123', // Plain text as requested
                        'status_aktif' => true,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                echo "Updated user to username: 'admin_unit'\n";
            } else {
                echo "No admin_unit role user found, creating one...\n";

                // Get or create a unit
                $unit = $this->db->table('unit')->where('status_aktif', true)->get()->getRowArray();
                if (!$unit) {
                    $this->db->table('unit')->insert([
                        'kode_unit' => 'TI',
                        'nama_unit' => 'Teknik Informatika',
                        'tipe_unit' => 'akademik',
                        'status_aktif' => true,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    $unitId = $this->db->insertID();
                    echo "Created unit: Teknik Informatika\n";
                } else {
                    $unitId = $unit['id'];
                    echo "Using existing unit: {$unit['nama_unit']}\n";
                }

                // Create admin_unit user
                $this->db->table('users')->insert([
                    'username' => 'admin_unit',
                    'password' => 'password123',
                    'email' => 'admin.unit@uigm.ac.id',
                    'nama_lengkap' => 'Admin Unit Test',
                    'role' => 'admin_unit',
                    'unit_id' => $unitId,
                    'status_aktif' => true,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                echo "Created admin_unit user\n";
            }
        } else {
            echo "admin_unit user exists: {$adminUnit['nama_lengkap']}\n";
            echo "Unit ID: {$adminUnit['unit_id']}\n";
            echo "Status: " . ($adminUnit['status_aktif'] ? 'Active' : 'Inactive') . "\n";

            // Ensure user is active and password is correct
            $this->db->table('users')
                ->where('id', $adminUnit['id'])
                ->update([
                    'password' => 'password123',
                    'status_aktif' => true,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            echo "Updated admin_unit user credentials\n";
        }

        // Verify final state
        $finalUser = $this->db->table('users')->where('username', 'admin_unit')->get()->getRowArray();
        if ($finalUser) {
            echo "\nFinal admin_unit user configuration:\n";
            echo "ID: {$finalUser['id']}\n";
            echo "Username: {$finalUser['username']}\n";
            echo "Role: {$finalUser['role']}\n";
            echo "Unit ID: {$finalUser['unit_id']}\n";
            echo "Status: " . ($finalUser['status_aktif'] ? 'Active' : 'Inactive') . "\n";
            echo "Email: {$finalUser['email']}\n";

            // Check unit details
            if ($finalUser['unit_id']) {
                $unit = $this->db->table('unit')->where('id', $finalUser['unit_id'])->get()->getRowArray();
                if ($unit) {
                    echo "Unit: {$unit['nama_unit']} ({$unit['kode_unit']})\n";
                }
            }
        }

        echo "\nAdmin Unit user setup complete!\n";
        echo "Login credentials: admin_unit / password123\n";
    }
}
