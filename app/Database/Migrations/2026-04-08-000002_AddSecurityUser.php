<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSecurityUser extends Migration
{
    public function up()
    {
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
    }

    public function down()
    {
        $this->db->table('users')->where('username', 'security_polban')->delete();
    }
}