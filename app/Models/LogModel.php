<?php

namespace App\Models;

use CodeIgniter\Model;

class LogModel extends Model
{
    protected $table      = 'logs';
    protected $primaryKey = 'id';

    protected $allowedFields = ['user_id', 'aksi', 'keterangan', 'created_at'];

    // Kita set false karena kita mengisi created_at secara manual atau via DB default
    protected $useTimestamps = false; 
}