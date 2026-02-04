<?php

namespace App\Models;

use CodeIgniter\Model;

class NotifikasiModel extends Model
{
    protected $table            = 'notifikasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'tipe_notifikasi',
        'judul',
        'pesan',
        'data_terkait',
        'is_read',
        'tanggal_dibaca'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'is_read' => 'boolean',
        'data_terkait' => 'json',
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
        'user_id' => 'required|integer',
        'tipe_notifikasi' => 'required|in_list[data_masuk,revisi_selesai,deadline,approval,rejection]',
        'judul' => 'required|max_length[255]',
        'pesan' => 'required',
    ];
    protected $validationMessages   = [
        'user_id' => [
            'required' => 'User ID harus diisi',
            'integer' => 'User ID harus berupa angka'
        ],
        'tipe_notifikasi' => [
            'required' => 'Tipe notifikasi harus diisi',
            'in_list' => 'Tipe notifikasi tidak valid'
        ],
        'judul' => [
            'required' => 'Judul notifikasi harus diisi'
        ],
        'pesan' => [
            'required' => 'Pesan notifikasi harus diisi'
        ]
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
     * Create notification for user
     */
    public function createNotification(int $userId, string $tipe, string $judul, string $pesan, array $dataTerkait = null)
    {
        return $this->insert([
            'user_id' => $userId,
            'tipe_notifikasi' => $tipe,
            'judul' => $judul,
            'pesan' => $pesan,
            'data_terkait' => $dataTerkait,
            'is_read' => false
        ]);
    }

    /**
     * Get unread notifications for user
     */
    public function getUnreadNotifications(int $userId)
    {
        return $this->where('user_id', $userId)
            ->where('is_read', false)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get all notifications for user
     */
    public function getUserNotifications(int $userId, int $limit = 50)
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId)
    {
        return $this->update($notificationId, [
            'is_read' => true,
            'tanggal_dibaca' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Mark all notifications as read for user
     */
    public function markAllAsRead(int $userId)
    {
        return $this->where('user_id', $userId)
            ->where('is_read', false)
            ->set([
                'is_read' => true,
                'tanggal_dibaca' => date('Y-m-d H:i:s')
            ])
            ->update();
    }

    /**
     * Get unread count for user
     */
    public function getUnreadCount(int $userId)
    {
        return $this->where('user_id', $userId)
            ->where('is_read', false)
            ->countAllResults();
    }

    /**
     * Send notification for data submission
     */
    public function notifyDataSubmission(int $adminPusatId, int $unitId, string $namaUnit)
    {
        return $this->createNotification(
            $adminPusatId,
            'data_masuk',
            'Data Baru Masuk',
            "Data UIGM dari unit {$namaUnit} telah dikirim dan menunggu review.",
            ['unit_id' => $unitId, 'action' => 'review_data']
        );
    }

    /**
     * Send notification for revision completion
     */
    public function notifyRevisionComplete(int $adminPusatId, int $unitId, string $namaUnit)
    {
        return $this->createNotification(
            $adminPusatId,
            'revisi_selesai',
            'Revisi Selesai',
            "Unit {$namaUnit} telah menyelesaikan revisi data UIGM.",
            ['unit_id' => $unitId, 'action' => 'review_revision']
        );
    }

    /**
     * Send notification for approval
     */
    public function notifyApproval(int $adminUnitId, string $namaUnit)
    {
        return $this->createNotification(
            $adminUnitId,
            'approval',
            'Data Disetujui',
            "Data UIGM unit {$namaUnit} telah disetujui oleh Admin Pusat.",
            ['action' => 'view_result']
        );
    }

    /**
     * Send notification for rejection/revision request
     */
    public function notifyRevisionRequest(int $adminUnitId, string $namaUnit, array $categories)
    {
        $categoryNames = implode(', ', array_column($categories, 'nama_kategori'));

        return $this->createNotification(
            $adminUnitId,
            'rejection',
            'Perlu Revisi',
            "Data UIGM unit {$namaUnit} perlu direvisi pada kategori: {$categoryNames}",
            ['categories' => $categories, 'action' => 'revise_data']
        );
    }

    /**
     * Send deadline reminder
     */
    public function notifyDeadlineReminder(int $userId, string $deadline)
    {
        return $this->createNotification(
            $userId,
            'deadline',
            'Pengingat Deadline',
            "Deadline pengisian data UIGM: {$deadline}. Pastikan data Anda sudah lengkap.",
            ['deadline' => $deadline, 'action' => 'complete_data']
        );
    }

    /**
     * Get notifications by type
     */
    public function getNotificationsByType(string $tipe, int $userId = null)
    {
        $builder = $this->where('tipe_notifikasi', $tipe);

        if ($userId) {
            $builder->where('user_id', $userId);
        }

        return $builder->orderBy('created_at', 'DESC')->findAll();
    }

    /**
     * Delete old notifications (older than specified days)
     */
    public function deleteOldNotifications(int $days = 90)
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        return $this->where('created_at <', $cutoffDate)
            ->where('is_read', true)
            ->delete();
    }
}
