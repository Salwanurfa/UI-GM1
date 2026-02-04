<?php

namespace App\Controllers;

use App\Models\UnitModel;
use App\Models\TahunPenilaianModel;
use App\Models\PengirimanUnitModel;
use App\Models\ReviewKategoriModel;
use App\Models\IndikatorModel;
use App\Models\NotifikasiModel;
use App\Models\UserModel;
use App\Models\JenisSampahModel;

class ApiController extends BaseController
{
    protected $unitModel;
    protected $tahunModel;
    protected $pengirimanModel;
    protected $reviewModel;
    protected $indikatorModel;
    protected $notifikasiModel;
    protected $userModel;
    protected $jenisSampahModel;

    public function __construct()
    {
        $this->unitModel = new UnitModel();
        $this->tahunModel = new TahunPenilaianModel();
        $this->pengirimanModel = new PengirimanUnitModel();
        $this->reviewModel = new ReviewKategoriModel();
        $this->indikatorModel = new IndikatorModel();
        $this->notifikasiModel = new NotifikasiModel();
        $this->userModel = new UserModel();
        $this->jenisSampahModel = new JenisSampahModel();
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats()
    {
        $user = session()->get('user');
        if (!$user) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $tahunAktif = $this->tahunModel->getActiveYear();
        if (!$tahunAktif) {
            return $this->response->setJSON(['error' => 'No active year']);
        }

        $stats = [];

        if ($user['role'] === 'admin_pusat') {
            $stats = $this->getAdminPusatStats($tahunAktif['id']);
        } elseif ($user['role'] === 'admin_unit') {
            $stats = $this->getAdminUnitStats($user['unit_id'], $tahunAktif['id']);
        } elseif ($user['role'] === 'super_admin') {
            $stats = $this->getSuperAdminStats();
        }

        return $this->response->setJSON($stats);
    }

    /**
     * Get notifications
     */
    public function getNotifications()
    {
        $user = session()->get('user');
        if (!$user) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $notifications = $this->notifikasiModel
            ->where('user_id', $user['id'])
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'notifications' => $notifications
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead($notifId)
    {
        $user = session()->get('user');
        if (!$user) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        try {
            $notification = $this->notifikasiModel->find($notifId);
            if (!$notification || $notification['user_id'] != $user['id']) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Notifikasi tidak ditemukan'
                ]);
            }

            $this->notifikasiModel->update($notifId, [
                'is_read' => true,
                'tanggal_dibaca' => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Notifikasi ditandai sudah dibaca'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal update notifikasi'
            ]);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead()
    {
        $user = session()->get('user');
        if (!$user) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        try {
            $this->notifikasiModel
                ->where('user_id', $user['id'])
                ->where('is_read', false)
                ->set([
                    'is_read' => true,
                    'tanggal_dibaca' => date('Y-m-d H:i:s')
                ])
                ->update();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Semua notifikasi ditandai sudah dibaca'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal update notifikasi'
            ]);
        }
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount()
    {
        $user = session()->get('user');
        if (!$user) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $unreadCount = $this->notifikasiModel
            ->where('user_id', $user['id'])
            ->where('is_read', false)
            ->countAllResults();

        return $this->response->setJSON([
            'success' => true,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Get unit progress
     */
    public function getUnitProgress($unitId = null)
    {
        $user = session()->get('user');
        if (!$user) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $tahunAktif = $this->tahunModel->getActiveYear();
        if (!$tahunAktif) {
            return $this->response->setJSON(['error' => 'No active year']);
        }

        // Jika admin unit, hanya bisa lihat unit sendiri
        if ($user['role'] === 'admin_unit') {
            $unitId = $user['unit_id'];
        }

        $query = $this->pengirimanModel
            ->select('pengiriman_unit.*, unit.nama_unit, unit.kode_unit')
            ->join('unit', 'unit.id = pengiriman_unit.unit_id')
            ->where('pengiriman_unit.tahun_penilaian_id', $tahunAktif['id']);

        if ($unitId) {
            $query->where('pengiriman_unit.unit_id', $unitId);
        }

        $progress = $query->findAll();

        return $this->response->setJSON([
            'success' => true,
            'progress' => $progress
        ]);
    }

    /**
     * Search units
     */
    public function searchUnits()
    {
        $search = $this->request->getGet('q');

        $units = $this->unitModel
            ->like('nama_unit', $search)
            ->orLike('kode_unit', $search)
            ->where('status_aktif', true)
            ->limit(10)
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'units' => $units
        ]);
    }

    /**
     * Get category details
     */
    public function getCategoryDetails($indikatorId)
    {
        $category = $this->indikatorModel->find($indikatorId);

        if (!$category) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Category not found']);
        }

        return $this->response->setJSON([
            'success' => true,
            'category' => $category
        ]);
    }

    /**
     * Admin Pusat Statistics
     */
    private function getAdminPusatStats($tahunId)
    {
        $totalUnit = $this->unitModel->where('status_aktif', true)->countAllResults();

        $pengirimanStats = $this->pengirimanModel
            ->select('status_pengiriman, COUNT(*) as jumlah')
            ->where('tahun_penilaian_id', $tahunId)
            ->groupBy('status_pengiriman')
            ->findAll();

        $stats = [
            'total_unit' => $totalUnit,
            'draft' => 0,
            'dikirim' => 0,
            'review' => 0,
            'perlu_revisi' => 0,
            'disetujui' => 0
        ];

        foreach ($pengirimanStats as $stat) {
            $stats[$stat['status_pengiriman']] = $stat['jumlah'];
        }

        $stats['belum_kirim'] = $totalUnit - array_sum(array_slice($stats, 1));
        $stats['progress_keseluruhan'] = $totalUnit > 0 ?
            round((($stats['disetujui'] + $stats['review'] + $stats['dikirim']) / $totalUnit) * 100, 1) : 0;

        return $stats;
    }

    /**
     * Admin Unit Statistics
     */
    private function getAdminUnitStats($unitId, $tahunId)
    {
        $pengiriman = $this->pengirimanModel
            ->where('unit_id', $unitId)
            ->where('tahun_penilaian_id', $tahunId)
            ->first();

        if (!$pengiriman) {
            return [
                'status' => 'belum_ada',
                'progress' => 0,
                'categories_completed' => 0,
                'total_categories' => 6
            ];
        }

        $reviewCount = $this->reviewModel
            ->where('pengiriman_id', $pengiriman['id'])
            ->where('data_input IS NOT NULL')
            ->countAllResults();

        return [
            'status' => $pengiriman['status_pengiriman'],
            'progress' => $pengiriman['progress_persen'],
            'categories_completed' => $reviewCount,
            'total_categories' => 6,
            'tanggal_kirim' => $pengiriman['tanggal_kirim'],
            'tanggal_review' => $pengiriman['tanggal_review']
        ];
    }

    /**
     * Super Admin Statistics
     */
    private function getSuperAdminStats()
    {
        $totalUsers = $this->userModel->where('status_aktif', true)->countAllResults();
        $totalUnits = $this->unitModel->where('status_aktif', true)->countAllResults();
        $totalSubmissions = $this->pengirimanModel->countAllResults();
        $totalCategories = $this->indikatorModel->where('status_aktif', true)->countAllResults();

        return [
            'total_users' => $totalUsers,
            'total_units' => $totalUnits,
            'total_submissions' => $totalSubmissions,
            'total_categories' => $totalCategories
        ];
    }

    /**
     * Get area sampah berdasarkan kategori
     */
    public function getAreaSampah($kategoriId)
    {
        try {
            $areas = $this->jenisSampahModel->getAreaSampah($kategoriId);

            return $this->response->setJSON([
                'success' => true,
                'data' => $areas
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data area sampah'
            ]);
        }
    }

    /**
     * Get detail sampah berdasarkan area
     */
    public function getDetailSampah($areaId)
    {
        try {
            $details = $this->jenisSampahModel->getDetailSampah($areaId);

            return $this->response->setJSON([
                'success' => true,
                'data' => $details
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data detail sampah'
            ]);
        }
    }

    /**
     * Get struktur lengkap sampah organik
     */
    public function getSampahOrganikStructure()
    {
        try {
            $structure = $this->jenisSampahModel->getSampahOrganikStructure();

            return $this->response->setJSON([
                'success' => true,
                'data' => $structure
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil struktur sampah organik'
            ]);
        }
    }
}
