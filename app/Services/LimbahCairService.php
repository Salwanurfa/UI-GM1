<?php

namespace App\Services;

use App\Models\LimbahCairModel;
use App\Models\UnitModel;
use App\Models\UserModel;

/**
 * Service Layer untuk Limbah Cair
 * 
 * Menangani business logic untuk operasi Limbah Cair oleh user
 */
class LimbahCairService
{
    protected LimbahCairModel $limbahModel;
    protected UnitModel $unitModel;
    protected UserModel $userModel;

    public function __construct()
    {
        $this->limbahModel = new LimbahCairModel();
        $this->unitModel   = new UnitModel();
        $this->userModel   = new UserModel();
    }

    /**
     * Get data for user index page with pagination
     */
    public function getUserIndexData(int $perPage = 9, int $page = 1): array
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$user || !isset($user['id'])) {
            throw new \RuntimeException('User session tidak ditemukan');
        }

        $unitId = $user['unit_id'] ?? null;
        $unit   = $unitId ? $this->unitModel->find($unitId) : null;

        // Use pagination for limbah list with group name 'limbah_cair'
        $limbahList = $this->limbahModel
            ->where('id_user', $user['id'])
            ->orderBy('tanggal_input', 'DESC')
            ->paginate($perPage, 'limbah_cair');
        
        $pager = $this->limbahModel->pager;

        $stats = $this->getUserStats($user['id']);

        return [
            'user'        => $user,
            'unit'        => $unit,
            'limbah_list' => $limbahList,
            'stats'       => $stats,
            'pager'       => $pager,
        ];
    }

    /**
     * Get user statistics
     */
    public function getUserStats(int $userId): array
    {
        try {
            return [
                'count_by_status' => $this->limbahModel->getCountByStatus($userId),
                'volume_by_status' => $this->limbahModel->getTotalVolumeByStatus($userId),
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
                'volume_by_status' => [],
                'draft_count' => 0,
                'dikirim_count' => 0,
                'disetujui_count' => 0,
                'ditolak_count' => 0,
            ];
        }
    }

    /**
     * Save new limbah cair data
     */
    public function saveUser(array $data): array
    {
        log_message('info', '=== LimbahCairService::saveUser START ===');
        log_message('info', 'Input data: ' . json_encode($data));

        // Validation
        if (empty($data['nama_limbah'])) {
            return [
                'success' => false,
                'message' => 'Nama limbah cair harus diisi',
                'error_field' => 'nama_limbah',
            ];
        }

        if (empty($data['timbulan']) || !is_numeric($data['timbulan']) || (float)$data['timbulan'] <= 0) {
            return [
                'success' => false,
                'message' => 'Timbulan harus berupa angka lebih besar dari 0',
                'error_field' => 'timbulan',
            ];
        }

        if (empty($data['satuan'])) {
            return [
                'success' => false,
                'message' => 'Satuan harus dipilih',
                'error_field' => 'satuan',
            ];
        }

        // Get user from session
        $user = session()->get('user');
        if (!$user || !isset($user['id'])) {
            return [
                'success' => false,
                'message' => 'Session user tidak valid. Silakan login kembali.',
            ];
        }

        $userId = (int)$user['id'];

        // Determine status
        $status = 'draft';
        if (isset($data['action']) && $data['action'] === 'kirim_ke_tps') {
            $status = 'dikirim_ke_tps';
        }

        // Prepare payload
        $payload = [
            'id_user'        => $userId,
            'tanggal_input'  => $data['tanggal_input'] ?? date('Y-m-d H:i:s'),
            'lokasi'         => $data['lokasi'] ?? null,
            'nama_limbah'    => trim($data['nama_limbah']),
            'kode_limbah'    => $data['kode_limbah'] ?? null,
            'tingkat_bahaya' => $data['tingkat_bahaya'] ?? null,
            'karakteristik'  => $data['karakteristik'] ?? null,
            'pengolahan'     => $data['pengolahan'] ?? null,
            'timbulan'       => (float)$data['timbulan'],
            'satuan'         => trim($data['satuan']),
            'bentuk_fisik'   => $data['bentuk_fisik'] ?? 'Cair',
            'kemasan'        => $data['kemasan'] ?? null,
            'ph'             => !empty($data['ph']) ? (float)$data['ph'] : null,
            'bod'            => !empty($data['bod']) ? (float)$data['bod'] : null,
            'cod'            => !empty($data['cod']) ? (float)$data['cod'] : null,
            'tss'            => !empty($data['tss']) ? (float)$data['tss'] : null,
            'keterangan'     => $data['keterangan'] ?? null,
            'status'         => $status,
        ];

        log_message('info', 'Payload untuk insert: ' . json_encode($payload));

        // Insert to database
        try {
            $result = $this->limbahModel->insert($payload);

            if ($result === false) {
                $errors = $this->limbahModel->errors();
                $errorMsg = !empty($errors) ? implode('; ', $errors) : 'Gagal menyimpan data';
                
                log_message('error', 'Insert failed. Errors: ' . json_encode($errors));
                
                return [
                    'success' => false,
                    'message' => $errorMsg,
                    'errors' => $errors,
                ];
            }

            $successMsg = ($status === 'dikirim_ke_tps')
                ? 'Data Limbah Cair berhasil dikirim ke TPS'
                : 'Data Limbah Cair berhasil disimpan sebagai draft';

            log_message('info', 'Insert SUCCESS: ' . $successMsg);

            return [
                'success' => true,
                'message' => $successMsg,
                'data' => $payload,
            ];

        } catch (\Throwable $e) {
            log_message('error', 'EXCEPTION in insert: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get limbah cair detail
     */
    public function getUserDetail(int $id): ?array
    {
        try {
            $user = session()->get('user');
            if (!$user) {
                return null;
            }

            $limbah = $this->limbahModel->find($id);
            
            if (!$limbah) {
                return null;
            }

            // Verify ownership
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
     * Update limbah cair data
     */
    public function updateUser(int $id, array $data): array
    {
        log_message('info', '=== LimbahCairService::updateUser START ID=' . $id . ' ===');

        $user = session()->get('user');
        
        // Validate ownership and status
        $current = $this->limbahModel->find($id);
        if (!$current) {
            return [
                'success' => false,
                'message' => 'Data Limbah Cair tidak ditemukan',
            ];
        }

        if ((int)$current['id_user'] !== (int)$user['id']) {
            return [
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengedit data ini',
            ];
        }

        // Only draft or rejected can be edited
        $editableStatus = ['draft', 'ditolak_tps'];
        if (!in_array($current['status'], $editableStatus)) {
            return [
                'success' => false,
                'message' => 'Data tidak dapat diedit karena sudah disubmit (status: ' . $current['status'] . ')',
            ];
        }

        // Validation
        if (empty($data['jenis_limbah'])) {
            return [
                'success' => false,
                'message' => 'Jenis limbah cair harus diisi',
                'error_field' => 'jenis_limbah',
            ];
        }

        if (empty($data['volume']) || !is_numeric($data['volume']) || (float)$data['volume'] <= 0) {
            return [
                'success' => false,
                'message' => 'Volume harus berupa angka lebih besar dari 0',
                'error_field' => 'volume',
            ];
        }

        // Determine new status
        $newStatus = $current['status'];
        if (isset($data['action']) && $data['action'] === 'kirim_ke_tps') {
            $newStatus = 'dikirim_ke_tps';
        }

        // Prepare payload
        $payload = [
            'lokasi'       => $data['lokasi'] ?? $current['lokasi'],
            'jenis_limbah' => trim($data['jenis_limbah']),
            'volume'       => (float)$data['volume'],
            'satuan'       => trim($data['satuan']),
            'keterangan'   => $data['keterangan'] ?? $current['keterangan'],
            'status'       => $newStatus,
        ];

        // Update database
        try {
            $result = $this->limbahModel->update($id, $payload);

            if ($result === false) {
                $errors = $this->limbahModel->errors();
                return [
                    'success' => false,
                    'message' => !empty($errors) ? implode('; ', $errors) : 'Gagal mengupdate data',
                    'errors' => $errors,
                ];
            }

            $successMsg = ($newStatus === 'dikirim_ke_tps')
                ? 'Data Limbah Cair berhasil diupdate dan dikirim ke TPS'
                : 'Data Limbah Cair berhasil diupdate';

            return [
                'success' => true,
                'message' => $successMsg,
                'data' => $payload,
            ];

        } catch (\Throwable $e) {
            log_message('error', 'EXCEPTION in update: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Delete limbah cair data
     */
    public function deleteUser(int $id): array
    {
        log_message('info', '=== LimbahCairService::deleteUser START ID=' . $id . ' ===');

        $user = session()->get('user');
        
        // Validate ownership and status
        $current = $this->limbahModel->find($id);
        if (!$current) {
            return [
                'success' => false,
                'message' => 'Data Limbah Cair tidak ditemukan',
            ];
        }

        if ((int)$current['id_user'] !== (int)$user['id']) {
            return [
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk menghapus data ini',
            ];
        }

        // Only draft can be deleted
        if ($current['status'] !== 'draft') {
            return [
                'success' => false,
                'message' => 'Hanya data draft yang dapat dihapus. Status saat ini: ' . $current['status'],
            ];
        }

        // Delete
        try {
            $result = $this->limbahModel->delete($id);

            if ($result === false) {
                return [
                    'success' => false,
                    'message' => 'Gagal menghapus data Limbah Cair',
                ];
            }

            return [
                'success' => true,
                'message' => 'Data Limbah Cair berhasil dihapus',
            ];

        } catch (\Throwable $e) {
            log_message('error', 'EXCEPTION in delete: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Export to PDF
     */
    public function exportPdf(): array
    {
        try {
            $user = session()->get('user');
            if (!$user || !isset($user['id'])) {
                return ['success' => false, 'message' => 'User session tidak valid'];
            }

            $userId = (int)$user['id'];
            
            $limbahList = $this->limbahModel->getUserLimbah($userId);

            if (empty($limbahList)) {
                return ['success' => false, 'message' => 'Tidak ada data Limbah Cair untuk diekspor'];
            }

            // Calculate totals
            $totalVolume = 0;
            $statusCount = [
                'draft' => 0,
                'dikirim_ke_tps' => 0,
                'disetujui_tps' => 0,
                'ditolak_tps' => 0,
                'disetujui_admin' => 0,
            ];

            foreach ($limbahList as $item) {
                if (isset($item['volume'])) {
                    $totalVolume += (float)$item['volume'];
                }
                if (isset($item['status'], $statusCount[$item['status']])) {
                    $statusCount[$item['status']]++;
                }
            }

            $data = [
                'unit' => $this->unitModel->find($user['unit_id']),
                'user' => $user,
                'limbah_list' => $limbahList,
                'total_volume' => $totalVolume,
                'status_count' => $statusCount,
                'generated_at' => date('d/m/Y H:i:s')
            ];

            // Generate HTML for PDF
            $html = view('user/limbah_cair_pdf', $data);

            // Generate PDF using Dompdf
            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            
            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Save to temp file
            $filename = 'limbah_cair_export_' . $user['id'] . '_' . date('Y-m-d_H-i-s') . '.pdf';
            $filePath = WRITEPATH . 'uploads/' . $filename;
            
            if (!is_dir(WRITEPATH . 'uploads/')) {
                mkdir(WRITEPATH . 'uploads/', 0755, true);
            }
            
            file_put_contents($filePath, $dompdf->output());

            return [
                'success' => true,
                'file_path' => $filePath,
                'filename' => $filename
            ];

        } catch (\Exception $e) {
            log_message('error', 'Export PDF Limbah Cair Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan saat export PDF: ' . $e->getMessage()];
        }
    }

    /**
     * Export to Excel
     */
    public function exportExcel(): void
    {
        try {
            $user = session()->get('user');
            if (!$user || !isset($user['id'])) {
                echo '<script>alert("Session tidak valid"); window.history.back();</script>';
                exit;
            }

            $userId = (int)$user['id'];
            $unit = $this->unitModel->find($user['unit_id']);

            $limbahList = $this->limbahModel->getUserLimbah($userId);
            
            if (empty($limbahList)) {
                echo '<script>alert("Tidak ada data Limbah Cair untuk diekspor"); window.history.back();</script>';
                exit;
            }

            // Prepare data for Excel
            $headers = ['No', 'Tanggal', 'Jenis Limbah', 'Lokasi', 'Volume', 'Satuan', 'Status'];
            $data = [];
            $no = 1;
            
            foreach ($limbahList as $limbah) {
                $status = match($limbah['status'] ?? 'draft') {
                    'draft' => 'Draft',
                    'dikirim_ke_tps' => 'Menunggu Review TPS',
                    'disetujui_tps' => 'Disetujui TPS',
                    'ditolak_tps' => 'Ditolak TPS',
                    'disetujui_admin' => 'Disetujui Admin',
                    default => 'Unknown'
                };
                
                $data[] = [
                    $no++,
                    date('d/m/Y', strtotime($limbah['tanggal_input'])),
                    $limbah['jenis_limbah'] ?? '-',
                    $limbah['lokasi'] ?? '-',
                    number_format($limbah['volume'] ?? 0, 2, '.', ''),
                    $limbah['satuan'] ?? '-',
                    $status
                ];
            }

            $filename = 'Data_Limbah_Cair_' . ($unit['nama_unit'] ?? 'User') . '_' . date('Y-m-d_His');
            $title = 'LAPORAN DATA LIMBAH CAIR - ' . ($unit['nama_unit'] ?? 'User');
            
            helper('excel');
            exportToExcel($data, $headers, $filename, $title);

        } catch (\Exception $e) {
            log_message('error', 'Export Excel Limbah Cair Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
