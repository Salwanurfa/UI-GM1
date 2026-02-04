<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'title',
        'message',
        'type',
        'data',
        'is_read',
        'read_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'user_id' => 'int',
        'is_read' => 'boolean',
        'data' => 'json'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'user_id' => 'required|integer',
        'title' => 'required|max_length[255]',
        'message' => 'required|max_length[1000]',
        'type' => 'required|in_list[info,success,warning,danger]'
    ];

    /**
     * Create notification for admin pusat when data submitted
     */
    public function createDataSubmissionNotification($unitName, $kategori, $indikator, $penilaianId)
    {
        // Get all admin pusat users
        $userModel = new \App\Models\UserModel();
        $adminUsers = $userModel->where('role', 'admin_pusat')->findAll();

        foreach ($adminUsers as $admin) {
            $this->insert([
                'user_id' => $admin['id'],
                'title' => 'Data Baru Menunggu Review',
                'message' => "Unit {$unitName} telah mengirim data {$kategori} - {$indikator} untuk direview",
                'type' => 'info',
                'data' => [
                    'penilaian_id' => $penilaianId,
                    'unit_name' => $unitName,
                    'kategori' => $kategori,
                    'indikator' => $indikator,
                    'action_url' => '/admin-pusat/review/detail/' . $penilaianId
                ],
                'is_read' => false
            ]);
        }
    }

    /**
     * Create notification for waste submission
     */
    public function createWasteSubmissionNotification($unitName, $jenisSampah, $wasteId)
    {
        // Get all admin pusat users
        $userModel = new \App\Models\UserModel();
        $adminUsers = $userModel->where('role', 'admin_pusat')->findAll();

        foreach ($adminUsers as $admin) {
            $this->insert([
                'user_id' => $admin['id'],
                'title' => 'Data Waste Baru',
                'message' => "Unit {$unitName} telah mengirim data waste {$jenisSampah} untuk direview",
                'type' => 'info',
                'data' => [
                    'waste_id' => $wasteId,
                    'unit_name' => $unitName,
                    'jenis_sampah' => $jenisSampah,
                    'action_url' => '/admin-pusat/waste'
                ],
                'is_read' => false
            ]);
        }
    }

    /**
     * Get unread notifications for user
     */
    public function getUnreadNotifications($userId)
    {
        return $this->where('user_id', $userId)
                   ->where('is_read', false)
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }

    /**
     * Get unread count for user
     */
    public function getUnreadCount($userId)
    {
        return $this->where('user_id', $userId)
                   ->where('is_read', false)
                   ->countAllResults();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, $userId)
    {
        return $this->where('id', $notificationId)
                   ->where('user_id', $userId)
                   ->set([
                       'is_read' => true,
                       'read_at' => date('Y-m-d H:i:s')
                   ])
                   ->update();
    }

    /**
     * Mark all notifications as read for user
     */
    public function markAllAsRead($userId)
    {
        return $this->where('user_id', $userId)
                   ->where('is_read', false)
                   ->set([
                       'is_read' => true,
                       'read_at' => date('Y-m-d H:i:s')
                   ])
                   ->update();
    }
}