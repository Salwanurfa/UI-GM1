<?php

// File: app/Database/Migrations/2026-01-02-110000_AddUserFields.php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserFields extends Migration
{
    public function up()
    {
        // Add new fields to users table
        $fields = [
            'full_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'nama_lengkap'
            ],
            'unit_prodi' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'full_name'
            ]
        ];
        
        $this->forge->addColumn('users', $fields);
        
        // Update role enum to include 'user'
        $this->db->query("ALTER TABLE users MODIFY COLUMN role ENUM('admin_pusat', 'admin_unit', 'super_admin', 'user') NOT NULL");
    }

    public function down()
    {
        // Remove added fields
        $this->forge->dropColumn('users', ['full_name', 'unit_prodi']);
        
        // Revert role enum
        $this->db->query("ALTER TABLE users MODIFY COLUMN role ENUM('admin_pusat', 'admin_unit', 'super_admin') NOT NULL");
    }
}