<?php

namespace App\Services;

use App\Models\LimbahB3Model;
use App\Models\MasterLimbahB3Model;
use App\Models\UnitModel;
use App\Models\UserModel;

/**
 * Service Layer untuk Limbah B3
 * 
 * Menangani business logic untuk operasi Limbah B3 oleh user:
 * - Simpan data limbah baru (draft atau kirim ke TPS)
 * - Update data limbah eksisting
 * - Hapus data limbah (hanya yang masih draft)
 * - Retrieve master data untuk dropdown
 * 
 * @author System GreenMetric
 * @version 3.0
 */
class LimbahB3Service
{
    protected LimbahB3Model $limbahModel;
    protected MasterLimbahB3Model $masterModel;
    protected UnitModel $unitModel;
    protected UserModel $userModel;

    public function __construct()
    {
        $this->limbahModel  = new LimbahB3Model();
        $this->masterModel  = new MasterLimbahB3Model();
        $this->unitModel    = new UnitModel();
        $this->userModel    = new UserModel();
    }

    /**
     * Ambil data lengkap untuk halaman index Limbah B3
     * 
     * @return array Data dengan keys: user, unit, limbah_list, master_list, stats
     */
    public function getUserIndexData(): array
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$user || !isset($user['id'])) {
            throw new \RuntimeException('User session tidak ditemukan');
        }

        $unitId = $user['id_user'] ?? null;
        $unit   = $unitId ? $this->unitModel->find($unitId) : null;

        $limbahList = $this->limbahModel->getUserLimbah($user['id']);
        $masterList = $this->getActiveMasterList();

        $stats = $this->getUserStats($user['id']);

        return [
            'user'        => $user,
            'unit'        => $unit,
            'limbah_list' => $limbahList,
            'master_list' => $masterList,
            'stats'       => $stats,
        ];
    }

    /**
     * Hitung statistik Limbah B3 user berdasarkan status
     * 
     * Memberikan breakdown count dan timbulan untuk setiap status,
     * dengan fokus khusus pada draft yang belum dikirim
     * 
     * @param int $userId User ID
     * @return array Array dengan keys: count_by_status, timbulan_by_status, draft_count
     */
    public function getUserStats(int $userId): array
    {
        try {
            return [
                'count_by_status' => $this->limbahModel->getCountByStatus($userId),
                'timbulan_by_status' => $this->limbahModel->getTotalTimbulanByStatus($userId),
                'draft_count' => $this->limbahModel
                    ->where(['id_user' => $userId, 'status' => 'draft'])
                    ->countAllResults(),
                'dikirim_count' => $this->limbahModel
                    ->where(['id_user' => $userId, 'status' => 'dikirim_ke_tps'])
                    ->countAllResults(),
                'disetujui_count' => $this->limbahModel
                    ->where(['id_user' => $userId, 'status' => 'disetujui_tps'])
                    ->countAllResults(),
                'ditolak_count' => $this->limbahModel
                    ->where(['id_user' => $userId, 'status' => 'ditolak_tps'])
                    ->countAllResults(),
            ];
        } catch (\Throwable $e) {
            log_message('error', 'getUserStats error: ' . $e->getMessage());
            return [
                'count_by_status' => [],
                'timbulan_by_status' => [],
                'draft_count' => 0,
                'dikirim_count' => 0,
                'disetujui_count' => 0,
                'ditolak_count' => 0,
            ];
        }
    }

    /**
     * Ambil daftar Limbah B3 user dengan filter status opsional
     * 
     * Fungsi ini memungkinkan filtering berdasarkan tab status tertentu
     * - Jika $status = null → tampilkan semua
     * - Jika $status = 'draft' → hanya draft
     * - Jika $status = 'dikirim_ke_tps' → hanya yang dikirim
     * - dll
     * 
     * @param int $userId User ID
     * @param string|null $status Status filter (draft, dikirim_ke_tps, disetujui_tps, ditolak_tps, disetujui_admin)
     * @return array Array limbah B3 dengan join master data, urutkan desc by tanggal
     */
    public function getLimbahUserList(int $userId, ?string $status = null): array
    {
        try {
            $query = $this->limbahModel
                ->select('limbah_b3.*, master_limbah_b3.nama_limbah, master_limbah_b3.kode_limbah, master_limbah_b3.kategori_bahaya')
                ->join('master_limbah_b3', 'master_limbah_b3.id = limbah_b3.master_b3_id', 'left')
                ->where('limbah_b3.id_user', $userId);

            // Tambahkan filter status jika diberikan
            if ($status !== null && !empty($status)) {
                $query->where('limbah_b3.status', $status);
                log_message('info', 'getLimbahUserList filter: status=' . $status);
            } else {
                log_message('info', 'getLimbahUserList: tanpa filter status');
            }

            return $query
                ->orderBy('limbah_b3.tanggal_input', 'DESC')
                ->findAll();

        } catch (\Throwable $e) {
            log_message('error', 'getLimbahUserList error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Simpan data Limbah B3 baru untuk user
     * 
     * Logika status:
     * - action='simpan_draf' atau tidak ada → status='draft'
     * - action='kirim_ke_tps' → status='dikirim_ke_tps'
     * 
     * @param array $data Array field: master_b3_id, lokasi, timbulan, satuan, bentuk_fisik, kemasan, action, keterangan, tanggal_input
     * @return array Response: [success=>bool, message=>string, data=>array, errors=>array]
     */
    public function saveUser(array $data): array
    {
        log_message('info', '=== LimbahB3Service::saveUser START ===');
        log_message('info', 'Input data: ' . json_encode($data));

        // ===== VALIDASI FIELD WAJIB =====
        if (empty($data['master_b3_id']) || !is_numeric($data['master_b3_id'])) {
            log_message('warning', 'Validasi gagal: master_b3_id kosong atau bukan number');
            return [
                'success' => false,
                'message' => 'Jenis Limbah B3 harus dipilih',
                'error_field' => 'master_b3_id',
            ];
        }

        if (empty($data['timbulan'])) {
            log_message('warning', 'Validasi gagal: timbulan kosong');
            return [
                'success' => false,
                'message' => 'Timbulan/berat Limbah B3 harus diisi',
                'error_field' => 'timbulan',
            ];
        }

        if (!is_numeric($data['timbulan']) || (float)$data['timbulan'] <= 0) {
            log_message('warning', 'Validasi gagal: timbulan bukan angka positif. Value: ' . $data['timbulan']);
            return [
                'success' => false,
                'message' => 'Timbulan/berat harus berupa angka lebih besar dari 0',
                'error_field' => 'timbulan',
            ];
        }

        if (empty($data['satuan'])) {
            log_message('warning', 'Validasi gagal: satuan kosong');
            return [
                'success' => false,
                'message' => 'Satuan Limbah B3 harus dipilih',
                'error_field' => 'satuan',
            ];
        }

        // ===== AMBIL USER DARI SESSION =====
        $user = session()->get('user');
        if (!$user || !isset($user['id'])) {
            log_message('error', 'ERROR: User session tidak ditemukan');
            return [
                'success' => false,
                'message' => 'Session user tidak valid. Silakan login kembali.',
            ];
        }

        $userId = (int)$user['id'];
        log_message('info', 'User ID: ' . $userId);

        // ===== TENTUKAN STATUS BERDASARKAN ACTION =====
        $status = 'draft'; // Default
        if (isset($data['action']) && $data['action'] === 'kirim_ke_tps') {
            $status = 'dikirim_ke_tps';
            log_message('info', 'Action: kirim_ke_tps → Status: dikirim_ke_tps');
        } else {
            log_message('info', 'Action: simpan_draf atau tidak ada → Status: draft');
        }

        // ===== SIAPKAN PAYLOAD UNTUK INSERT =====
        // Field harus match dengan allowedFields di Model dan struktur table limbah_b3
        $payload = [
            'id_user'       => $userId,
            'master_b3_id'  => (int)$data['master_b3_id'],
            'lokasi'        => $data['lokasi'] ?? null,
            'timbulan'      => (float)$data['timbulan'],
            'satuan'        => trim($data['satuan']),
            'bentuk_fisik'  => $data['bentuk_fisik'] ?? null,
            'kemasan'       => $data['kemasan'] ?? null,
            'status'        => $status,
            'keterangan'    => $data['keterangan'] ?? null,
            'tanggal_input' => $data['tanggal_input'] ?? date('Y-m-d H:i:s'),
        ];

        log_message('info', 'Payload untuk insert: ' . json_encode($payload));

        // ===== INSERT KE DATABASE =====
        try {
            $result = $this->limbahModel->insert($payload);

            if ($result === false) {
                // Insert gagal, cek error dari model validation
                $errors = $this->limbahModel->errors();
                $errorMsg = !empty($errors) ? implode('; ', $errors) : 'Gagal menyimpan data';
                
                log_message('error', 'Insert failed. Errors: ' . json_encode($errors));
                
                return [
                    'success' => false,
                    'message' => $errorMsg,
                    'errors' => $errors,
                ];
            }

            // Insert berhasil
            $successMsg = ($status === 'dikirim_ke_tps')
                ? 'Data Limbah B3 berhasil dikirim ke TPS'
                : 'Data Limbah B3 berhasil disimpan sebagai draft';

            log_message('info', 'Insert SUCCESS: ' . $successMsg);
            log_message('info', '=== LimbahB3Service::saveUser END (SUCCESS) ===');

            return [
                'success' => true,
                'message' => $successMsg,
                'data' => $payload,
            ];

        } catch (\Throwable $e) {
            // Tangkap semua error (exception, error parsing, dll)
            $errorMsg = 'Database error: ' . $e->getMessage();
            $errorTrace = $e->getTraceAsString();
            
            log_message('error', 'EXCEPTION in insert: ' . $e->getMessage());
            log_message('error', 'Trace: ' . $errorTrace);
            log_message('info', '=== LimbahB3Service::saveUser END (EXCEPTION) ===');

            return [
                'success' => false,
                'message' => $errorMsg,
                'details' => $e->getMessage(), // Untuk debugging
            ];
        }
    }

    /**
     * Ambil detail Limbah B3 user berdasarkan ID
     * 
     * Verifikasi ownership: user hanya bisa akses data miliknya sendiri
     * 
     * @param int $id ID limbah_b3
     * @return array|null Detail limbah beserta master data, atau null jika tidak ada
     */
    public function getUserDetail(int $id): ?array
    {
        try {
            $user = session()->get('user');
            if (!$user) {
                return null;
            }

            $limbah = $this->limbahModel->getDetailWithMaster($id);
            
            if (!$limbah) {
                log_message('warning', 'Limbah ID ' . $id . ' tidak ditemukan');
                return null;
            }

            // Verifikasi kepemilikan
            if ((int)$limbah['id_user'] !== (int)$user['id']) {
                log_message('warning', 'User ' . $user['id'] . ' mencoba akses limbah milik user ' . $limbah['id_user']);
                return null;
            }

            return $limbah;

        } catch (\Throwable $e) {
            log_message('error', 'getUserDetail error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update data Limbah B3 user
     * 
     * Hanya data dengan status 'draft' atau 'ditolak_tps' yang bisa diedit.
     * Setelah dikirim, tidak bisa diedit kecuali ditolak.
     * 
     * @param int $id ID limbah_b3
     * @param array $data Array field yang akan diupdate
     * @return array Response: [success=>bool, message=>string, data=>array, errors=>array]
     */
    public function updateUser(int $id, array $data): array
    {
        log_message('info', '=== LimbahB3Service::updateUser START ID=' . $id . ' ===');
        log_message('info', 'Input data: ' . json_encode($data));

        $user = session()->get('user');
        
        // ===== VALIDASI KEPEMILIKAN DAN STATUS =====
        $current = $this->limbahModel->find($id);
        if (!$current) {
            log_message('warning', 'Update gagal: limbah ID ' . $id . ' tidak ditemukan');
            return [
                'success' => false,
                'message' => 'Data Limbah B3 tidak ditemukan',
            ];
        }

        if ((int)$current['id_user'] !== (int)$user['id']) {
            log_message('warning', 'User ' . $user['id'] . ' mencoba update limbah milik user ' . $current['id_user']);
            return [
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengedit data ini',
            ];
        }

        // Hanya draft atau ditolak yang bisa diedit
        $editableStatus = ['draft', 'ditolak_tps'];
        if (!in_array($current['status'], $editableStatus)) {
            log_message('warning', 'Update gagal: status ' . $current['status'] . ' tidak bisa diedit');
            return [
                'success' => false,
                'message' => 'Data tidak dapat diedit karena sudah disubmit (status: ' . $current['status'] . ')',
            ];
        }

        // ===== VALIDASI FIELD YANG DIUPDATE =====
        if (empty($data['master_b3_id']) || !is_numeric($data['master_b3_id'])) {
            return [
                'success' => false,
                'message' => 'Jenis Limbah B3 harus dipilih',
                'error_field' => 'master_b3_id',
            ];
        }

        if (empty($data['timbulan']) || !is_numeric($data['timbulan']) || (float)$data['timbulan'] <= 0) {
            return [
                'success' => false,
                'message' => 'Timbulan/berat harus berupa angka lebih besar dari 0',
                'error_field' => 'timbulan',
            ];
        }

        if (empty($data['satuan'])) {
            return [
                'success' => false,
                'message' => 'Satuan Limbah B3 harus dipilih',
                'error_field' => 'satuan',
            ];
        }

        // ===== TENTUKAN STATUS UNTUK UPDATE =====
        $newStatus = $current['status'];
        if (isset($data['action']) && $data['action'] === 'kirim_ke_tps') {
            $newStatus = 'dikirim_ke_tps';
            log_message('info', 'Action: kirim_ke_tps → Status akan diubah: ' . $current['status'] . ' → dikirim_ke_tps');
        } else {
            log_message('info', 'Action: simpan_draf → Status tetap: ' . $newStatus);
        }

        // ===== SIAPKAN PAYLOAD UPDATE =====
        $payload = [
            'master_b3_id' => (int)$data['master_b3_id'],
            'lokasi'       => $data['lokasi'] ?? $current['lokasi'],
            'timbulan'     => (float)$data['timbulan'],
            'satuan'       => trim($data['satuan']),
            'bentuk_fisik' => $data['bentuk_fisik'] ?? $current['bentuk_fisik'],
            'kemasan'      => $data['kemasan'] ?? $current['kemasan'],
            'status'       => $newStatus,
            'keterangan'   => $data['keterangan'] ?? ($current['keterangan'] ?? null),
        ];

        log_message('info', 'Payload untuk update: ' . json_encode($payload));

        // ===== UPDATE KE DATABASE =====
        try {
            $result = $this->limbahModel->update($id, $payload);

            if ($result === false) {
                $errors = $this->limbahModel->errors();
                $errorMsg = !empty($errors) ? implode('; ', $errors) : 'Gagal mengupdate data';
                
                log_message('error', 'Update failed. Errors: ' . json_encode($errors));
                
                return [
                    'success' => false,
                    'message' => $errorMsg,
                    'errors' => $errors,
                ];
            }

            // Update berhasil
            $successMsg = ($newStatus === 'dikirim_ke_tps')
                ? 'Data Limbah B3 berhasil diupdate dan dikirim ke TPS'
                : 'Data Limbah B3 berhasil diupdate';

            log_message('info', 'Update SUCCESS: ' . $successMsg);
            log_message('info', '=== LimbahB3Service::updateUser END (SUCCESS) ===');

            return [
                'success' => true,
                'message' => $successMsg,
                'data' => $payload,
            ];

        } catch (\Throwable $e) {
            $errorMsg = 'Database error: ' . $e->getMessage();
            
            log_message('error', 'EXCEPTION in update: ' . $e->getMessage());
            log_message('error', 'Trace: ' . $e->getTraceAsString());
            log_message('info', '=== LimbahB3Service::updateUser END (EXCEPTION) ===');

            return [
                'success' => false,
                'message' => $errorMsg,
                'details' => $e->getMessage(),
            ];
        }
    }

    /**
     * Hapus data Limbah B3 user
     * 
     * Hanya data dengan status 'draft' yang bisa dihapus.
     * Data yang sudah dikirim ke TPS atau statusnya berubah tidak bisa dihapus.
     * 
     * @param int $id ID limbah_b3
     * @return array Response: [success=>bool, message=>string]
     */
    public function deleteUser(int $id): array
    {
        log_message('info', '=== LimbahB3Service::deleteUser START ID=' . $id . ' ===');

        $user = session()->get('user');
        
        // ===== VALIDASI KEPEMILIKAN DAN STATUS =====
        $current = $this->limbahModel->find($id);
        if (!$current) {
            log_message('warning', 'Delete gagal: limbah ID ' . $id . ' tidak ditemukan');
            return [
                'success' => false,
                'message' => 'Data Limbah B3 tidak ditemukan',
            ];
        }

        if ((int)$current['id_user'] !== (int)$user['id']) {
            log_message('warning', 'User ' . $user['id'] . ' mencoba delete limbah milik user ' . $current['id_user']);
            return [
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk menghapus data ini',
            ];
        }

        // Hanya draft yang bisa dihapus
        if ($current['status'] !== 'draft') {
            $errorMsg = 'Hanya data draft yang dapat dihapus.';
            
            // Pesan error spesifik berdasarkan status
            if ($current['status'] === 'dikirim_ke_tps') {
                $errorMsg = 'Data tidak dapat dihapus karena sudah dikirim ke TPS. Hubungi TPS untuk membatalkan.';
            } elseif ($current['status'] === 'disetujui_tps') {
                $errorMsg = 'Data tidak dapat dihapus karena sudah disetujui oleh TPS.';
            } elseif ($current['status'] === 'ditolak_tps') {
                $errorMsg = 'Data tidak dapat dihapus karena sudah ditolak oleh TPS. Silakan buat data baru atau hubungi TPS.';
            } elseif ($current['status'] === 'disetujui_admin') {
                $errorMsg = 'Data tidak dapat dihapus karena sudah disetujui oleh Admin.';
            }

            log_message('warning', 'Delete gagal: hanya status draft yang bisa dihapus. Status saat ini: ' . $current['status']);
            return [
                'success' => false,
                'message' => $errorMsg,
            ];
        }

        // ===== DELETE =====
        try {
            $result = $this->limbahModel->delete($id);

            if ($result === false) {
                log_message('error', 'Delete gagal dari model');
                return [
                    'success' => false,
                    'message' => 'Gagal menghapus data Limbah B3',
                ];
            }

            log_message('info', 'Delete SUCCESS: Limbah ID ' . $id . ' dihapus');
            log_message('info', '=== LimbahB3Service::deleteUser END (SUCCESS) ===');

            return [
                'success' => true,
                'message' => 'Data Limbah B3 berhasil dihapus',
            ];

        } catch (\Throwable $e) {
            log_message('error', 'EXCEPTION in delete: ' . $e->getMessage());
            log_message('error', 'Trace: ' . $e->getTraceAsString());
            log_message('info', '=== LimbahB3Service::deleteUser END (EXCEPTION) ===');

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Ambil daftar Master Limbah B3 untuk dropdown form
     * 
     * @return array Array master limbah dengan field: id, nama_limbah, kode_limbah, kategori_bahaya, karakteristik
     */
    public function getActiveMasterList(): array
    {
        try {
            $masters = $this->masterModel
                ->orderBy('nama_limbah', 'ASC')
                ->findAll();

            log_message('info', 'Master list retrieved: ' . count($masters) . ' records');
            return $masters;

        } catch (\Throwable $e) {
            log_message('error', 'getActiveMasterList error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Ambil detail master Limbah B3 berdasarkan ID
     * Digunakan untuk AJAX lookup ketika user memilih master di dropdown
     * 
     * @param int $id ID master limbah
     * @return array|null Detail master atau null jika tidak ditemukan
     */
    public function getMasterById(int $id): ?array
    {
        try {
            return $this->masterModel->find($id);
        } catch (\Throwable $e) {
            log_message('error', 'getMasterById error: ' . $e->getMessage());
            return null;
        }
    }
}

