<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'username',
        'email',
        'password',
        'nama_lengkap',
        'full_name',
        'unit_prodi',
        'gedung',
        'role',
        'unit_id',
        'status_aktif',
        'last_login'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'status_aktif' => 'boolean',
    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        // Removed validation rules to avoid conflicts with controller validation
    ];
    protected $validationMessages   = [
        // Removed validation messages to avoid conflicts
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get users by role
     */
    public function getUsersByRole(string $role)
    {
        return $this->where('role', $role)
            ->where('status_aktif', true)
            ->findAll();
    }

    /**
     * Get admin pusat users
     */
    public function getAdminPusat()
    {
        return $this->getUsersByRole('admin_pusat');
    }

    /**
     * Get admin unit users
     */
    public function getAdminUnit()
    {
        return $this->getUsersByRole('admin_unit');
    }

    /**
     * Get user with unit information
     */
    public function getUserWithUnit(int $userId)
    {
        return $this->select('users.*, unit.nama_unit, unit.kode_unit')
            ->join('unit', 'unit.id = users.unit_id', 'left')
            ->find($userId);
    }

    /**
     * Update last login
     */
    public function updateLastLogin(int $userId)
    {
        return $this->update($userId, ['last_login' => date('Y-m-d H:i:s')]);
    }

    /**
     * Get user for login with explicit role selection
     */
    public function getUserForLogin(string $login)
    {
        // Debug logging
        log_message('debug', "getUserForLogin called with: " . $login);
        
        // Explicit select untuk memastikan role terbaca
        // Fixed: Gunakan groupStart/groupEnd untuk OR condition yang benar
        $result = $this->select('id, username, email, password, nama_lengkap, role, unit_id, status_aktif, last_login')
            ->groupStart()
                ->where('email', $login)
                ->orWhere('username', $login)
            ->groupEnd()
            ->where('status_aktif', 1)
            ->first();
            
        // Debug logging
        if ($result) {
            log_message('debug', "User found - ID: " . $result['id'] . ", Username: " . $result['username'] . ", Role: " . $result['role']);
        } else {
            log_message('debug', "No user found for login: " . $login);
        }
        
        return $result;
    }

    /**
     * Verify password (supports both hashed and plain text)
     */
    public function verifyPassword(string $password, string $storedPassword): bool
    {
        // Log for debugging
        log_message('debug', "Password verification - Input length: " . strlen($password) . ", Stored length: " . strlen($storedPassword));
        
        // Check if stored password is hashed (bcrypt starts with $2y$ or $2a$)
        if (strlen($storedPassword) >= 60 && (strpos($storedPassword, '$2y$') === 0 || strpos($storedPassword, '$2a$') === 0)) {
            // Use password_verify for hashed passwords
            $result = password_verify($password, $storedPassword);
            log_message('debug', "Bcrypt verification result: " . ($result ? 'true' : 'false'));
            return $result;
        }
        
        // Check if stored password is MD5 hash (32 characters, hexadecimal)
        if (strlen($storedPassword) === 32 && ctype_xdigit($storedPassword)) {
            $result = md5($password) === $storedPassword;
            log_message('debug', "MD5 verification result: " . ($result ? 'true' : 'false'));
            return $result;
        }
        
        // Check if stored password is SHA1 hash (40 characters, hexadecimal)
        if (strlen($storedPassword) === 40 && ctype_xdigit($storedPassword)) {
            $result = sha1($password) === $storedPassword;
            log_message('debug', "SHA1 verification result: " . ($result ? 'true' : 'false'));
            return $result;
        }
        
        // Fallback to plain text comparison
        $result = $password === $storedPassword;
        log_message('debug', "Plain text verification result: " . ($result ? 'true' : 'false'));
        return $result;
    }
}