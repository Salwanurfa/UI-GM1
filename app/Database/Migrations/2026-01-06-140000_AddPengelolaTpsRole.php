<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPengelolaTpsRole extends Migration
{
    public function up()
    {
        // Update role enum to include 'pengelola_tps'
        $this->db->query("ALTER TABLE users MODIFY COLUMN role ENUM('admin_pusat', 'admin_unit', 'super_admin', 'user', 'pengelola_tps') NOT NULL DEFAULT 'user'");
    }

    public function down()
    {
        // Revert role enum (remove pengelola_tps)
        $this->db->query("ALTER TABLE users MODIFY COLUMN role ENUM('admin_pusat', 'admin_unit', 'super_admin', 'user') NOT NULL DEFAULT 'user'");
    }
}