<?php

namespace App\Services\User;

use App\Models\WasteModel;
use App\Models\UnitModel;
use App\Models\HargaSampahModel;

class WasteService
{
    protected $wasteModel;
    protected $unitModel;
    protected $hargaModel;

    public function __construct()
    {
        $this->wasteModel = new WasteModel();
        $this->unitModel = new UnitModel();
        $this->hargaModel = new HargaSampahModel();
    }

    public function getWasteData(): array
    {
        try {
            $user = session()->get('user');
            $unitId = $user['unit_id'];

            // Validate unit exists
            $unit = $this->unitModel->find($unitId);
            if (!$unit) {
                throw new \Exception('Unit tidak ditemukan');
            }

            return [
                'user' => $user,
                'unit' => $unit,
                'waste_list' => $this->getWasteList($unitId),
                'categories' => $this->getCategories(),
                'stats' => $this->getWasteStats($unitId),
                'recent_activities' => $this->getRecentActivities($user['id'])
            ];
        } catch (\Exception $e) {
            log_message('error', 'User Waste Service Error: ' . $e->getMessage());
            
            return [
                'user' => session()->get('user'),
                'unit' => null,
                'waste_list' => [],
                'categories' => [],
                'stats' => $this->getDefaultStats()
            ];
        }
    }

    public function saveWaste(array $data): array
    {
        try {
            log_message('info', 'User Save Waste - Data received: ' . json_encode($data));
            
            $validation = $this->validateWasteData($data);
            if (!$validation['valid']) {
                log_message('error', 'User Save Waste - Validation failed: ' . $validation['message']);
                return ['success' => false, 'message' => $validation['message']];
            }

            $user = session()->get('user');
            
            // Get category info
            $category = $this->hargaModel->find($data['kategori_id']);
            
            if (!$category) {
                return ['success' => false, 'message' => 'Kategori sampah tidak ditemukan'];
            }
            
            // Determine status based on action
            $status = 'draft';
            if (isset($data['status_action']) && $data['status_action'] === 'kirim') {
                $status = 'dikirim_ke_tps'; // Changed from 'dikirim' to 'dikirim_ke_tps'
            }
            
            // Get satuan from input, default to 'kg' if not provided
            $satuan = $data['satuan'] ?? 'kg';
            
            $wasteData = [
                'unit_id' => $user['unit_id'],
                'berat_kg' => $data['berat_kg'],
                'tanggal' => date('Y-m-d'),
                'jenis_sampah' => $category['jenis_sampah'],  // Kategori umum (Plastik, Kertas, dll)
                'nama_sampah' => $category['nama_jenis'],      // Nama detail (Keyboard Bekas, dll)
                'satuan' => $satuan,
                'jumlah' => $data['berat_kg'],
                'gedung' => 'User Unit',
                'kategori_sampah' => $category['dapat_dijual'] ? 'bisa_dijual' : 'tidak_bisa_dijual',
                'status' => $status,
                'user_id' => $user['id']  // Changed from created_by to user_id
            ];
            
            // Add nilai_rupiah if can be sold
            if ($category['dapat_dijual']) {
                $wasteData['nilai_rupiah'] = $data['berat_kg'] * $category['harga_per_satuan'];
            }

            log_message('info', 'User Save Waste - Prepared data: ' . json_encode($wasteData));

            $result = $this->wasteModel->insert($wasteData);
            
            if ($result) {
                log_message('info', 'User Save Waste - Success, ID: ' . $result);
                $message = $status === 'dikirim_ke_tps' ? 'Data sampah berhasil disimpan dan dikirim ke TPS' : 'Data sampah berhasil disimpan sebagai draft';
                return ['success' => true, 'message' => $message];
            }

            // Get database error if insert failed
            $db = \Config\Database::connect();
            $error = $db->error();
            $lastQuery = $db->getLastQuery();
            log_message('error', 'User Save Waste - Insert failed. DB Error: ' . json_encode($error));
            log_message('error', 'User Save Waste - Last Query: ' . $lastQuery);
            log_message('error', 'User Save Waste - Model errors: ' . json_encode($this->wasteModel->errors()));
            
            // Check if there are validation errors
            $validationErrors = $this->wasteModel->errors();
            if (!empty($validationErrors)) {
                return ['success' => false, 'message' => 'Validasi gagal: ' . implode(', ', $validationErrors)];
            }
            
            return ['success' => false, 'message' => 'Gagal menyimpan data sampah: ' . ($error['message'] ?? 'Unknown error')];

        } catch (\Exception $e) {
            log_message('error', 'Save User Waste Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()];
        }
    }

    public function updateWaste(int $id, array $data): array
    {
        try {
            log_message('info', 'User Update Waste - ID: ' . $id . ', Data received: ' . json_encode($data));
            
            // Validate required fields
            if (empty($data['kategori_id'])) {
                log_message('warning', 'User Update Waste - kategori_id is empty');
                return ['success' => false, 'message' => 'Kategori sampah harus dipilih'];
            }
            
            if (empty($data['berat_kg']) || $data['berat_kg'] <= 0) {
                log_message('warning', 'User Update Waste - berat_kg is invalid: ' . ($data['berat_kg'] ?? 'null'));
                return ['success' => false, 'message' => 'Jumlah/berat harus diisi dan lebih dari 0'];
            }

            $user = session()->get('user');
            log_message('info', 'User Update Waste - User: ' . json_encode($user));
            
            // Check if waste belongs to this user's unit
            $waste = $this->wasteModel->find($id);
            log_message('info', 'User Update Waste - Existing waste: ' . json_encode($waste));
            
            if (!$waste) {
                return ['success' => false, 'message' => 'Data sampah tidak ditemukan'];
            }
            
            if ($waste['unit_id'] != $user['unit_id']) {
                return ['success' => false, 'message' => 'Data sampah bukan milik unit Anda'];
            }

            // Check if waste can be edited
            if (!in_array($waste['status'], ['draft', 'perlu_revisi', 'ditolak_tps'])) {
                return ['success' => false, 'message' => 'Data yang sudah disubmit tidak dapat diedit'];
            }

            // Get category info
            $category = $this->hargaModel->find($data['kategori_id']);
            log_message('info', 'User Update Waste - Category: ' . json_encode($category));
            
            if (!$category) {
                return ['success' => false, 'message' => 'Kategori sampah tidak ditemukan'];
            }
            
            // Determine status based on action
            $status = 'draft';
            if (isset($data['status_action']) && $data['status_action'] === 'kirim') {
                // If editing rejected data and sending again, send back to TPS
                if ($waste['status'] === 'ditolak_tps') {
                    $status = 'dikirim_ke_tps';
                } else {
                    $status = 'dikirim';
                }
            }
            
            $wasteData = [
                'berat_kg' => $data['berat_kg'],
                'jumlah' => $data['berat_kg'],
                'satuan' => $data['satuan'] ?? $waste['satuan'] ?? 'kg',
                'jenis_sampah' => $category['jenis_sampah'],  // Kategori umum
                'nama_sampah' => $category['nama_jenis'],      // Nama detail
                'kategori_sampah' => $category['dapat_dijual'] ? 'bisa_dijual' : 'tidak_bisa_dijual',
                'status' => $status
            ];
            
            // Update nilai_rupiah if can be sold
            if ($category['dapat_dijual']) {
                $wasteData['nilai_rupiah'] = $data['berat_kg'] * $category['harga_per_satuan'];
            } else {
                $wasteData['nilai_rupiah'] = 0;
            }

            log_message('info', 'User Update Waste - Prepared data: ' . json_encode($wasteData));
            
            $result = $this->wasteModel->update($id, $wasteData);
            
            if ($result) {
                log_message('info', 'User Update Waste - Success');
                $message = $status === 'dikirim_ke_tps' 
                    ? 'Data sampah berhasil diupdate dan dikirim ulang ke TPS' 
                    : ($status === 'dikirim' 
                        ? 'Data sampah berhasil diupdate dan dikirim' 
                        : 'Data sampah berhasil diupdate sebagai draft');
                return ['success' => true, 'message' => $message];
            }

            log_message('error', 'User Update Waste - Update failed');
            return ['success' => false, 'message' => 'Gagal mengupdate data sampah'];

        } catch (\Exception $e) {
            log_message('error', 'Update User Waste Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()];
        }
    }

    public function deleteWaste(int $id): array
    {
        try {
            $user = session()->get('user');
            
            // Check if waste belongs to this user's unit
            $waste = $this->wasteModel->find($id);
            if (!$waste || $waste['unit_id'] != $user['unit_id']) {
                return ['success' => false, 'message' => 'Data sampah tidak ditemukan atau bukan milik unit Anda'];
            }

            // Check if waste can be deleted
            if (!in_array($waste['status'], ['draft', 'perlu_revisi', 'ditolak_tps'])) {
                return ['success' => false, 'message' => 'Data yang sudah disubmit tidak dapat dihapus'];
            }

            $result = $this->wasteModel->delete($id);
            
            if ($result) {
                return ['success' => true, 'message' => 'Data sampah berhasil dihapus'];
            }

            return ['success' => false, 'message' => 'Gagal menghapus data sampah'];

        } catch (\Exception $e) {
            log_message('error', 'Delete User Waste Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem'];
        }
    }

    public function exportWaste(): array
    {
        try {
            $user = session()->get('user');
            $unitId = $user['unit_id'];

            $wasteList = $this->getWasteList($unitId);
            
            if (empty($wasteList)) {
                return ['success' => false, 'message' => 'Tidak ada data untuk diekspor'];
            }

            // Create CSV content
            $csvContent = "Tanggal,Kategori,Berat (kg),Status,Keterangan\n";
            
            foreach ($wasteList as $waste) {
                $csvContent .= sprintf(
                    "%s,%s,%s,%s,%s\n",
                    $waste['created_at'],
                    $waste['kategori'] ?? 'N/A',
                    $waste['berat_kg'],
                    $waste['status'],
                    str_replace(['"', ',', "\n"], ['""', ';', ' '], $waste['keterangan'] ?? '')
                );
            }

            // Save to temp file
            $filename = 'waste_export_' . $user['unit_id'] . '_' . date('Y-m-d_H-i-s') . '.csv';
            $filePath = WRITEPATH . 'uploads/' . $filename;
            
            if (!is_dir(WRITEPATH . 'uploads/')) {
                mkdir(WRITEPATH . 'uploads/', 0755, true);
            }
            
            file_put_contents($filePath, $csvContent);

            return [
                'success' => true,
                'file_path' => $filePath,
                'filename' => $filename
            ];

        } catch (\Exception $e) {
            log_message('error', 'Export User Waste Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan saat export data'];
        }
    }
    
    public function exportPdf(): array
    {
        try {
            $user = session()->get('user');
            $unitId = $user['unit_id'];
            $unit = $this->unitModel->find($unitId);

            $wasteList = $this->getWasteList($unitId);
            
            if (empty($wasteList)) {
                return ['success' => false, 'message' => 'Tidak ada data untuk diekspor'];
            }

            // Calculate statistics
            $totalBerat = 0;
            $totalNilai = 0;
            $statusCount = ['draft' => 0, 'dikirim' => 0, 'review' => 0, 'disetujui' => 0, 'perlu_revisi' => 0];
            
            foreach ($wasteList as $waste) {
                $totalBerat += $waste['berat_kg'];
                $totalNilai += $waste['nilai_rupiah'] ?? 0;
                $status = $waste['status'] ?? 'draft';
                if (isset($statusCount[$status])) {
                    $statusCount[$status]++;
                }
            }

            // Prepare data for PDF
            $data = [
                'title' => 'Laporan Data Sampah',
                'unit' => $unit,
                'user' => $user,
                'waste_list' => $wasteList,
                'total_berat' => $totalBerat,
                'total_nilai' => $totalNilai,
                'status_count' => $statusCount,
                'generated_at' => date('d/m/Y H:i:s')
            ];

            // Generate HTML for PDF
            $html = view('user/waste_pdf', $data);

            // Generate PDF using Dompdf
            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            
            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Save to temp file
            $filename = 'waste_export_' . $user['unit_id'] . '_' . date('Y-m-d_H-i-s') . '.pdf';
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
            log_message('error', 'Export PDF User Waste Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return ['success' => false, 'message' => 'Terjadi kesalahan saat export PDF: ' . $e->getMessage()];
        }
    }

    public function exportExcel(): void
    {
        try {
            $user = session()->get('user');
            $unitId = $user['unit_id'];
            $unit = $this->unitModel->find($unitId);

            $wasteList = $this->getWasteList($unitId);
            
            if (empty($wasteList)) {
                echo '<script>alert("Tidak ada data untuk diekspor"); window.history.back();</script>';
                exit;
            }

            // Prepare data for Excel
            $headers = ['No', 'Tanggal', 'Jenis Sampah', 'Berat (kg)', 'Satuan', 'Nilai (Rp)', 'Status'];
            $data = [];
            $no = 1;
            
            foreach ($wasteList as $waste) {
                $status = match($waste['status'] ?? 'draft') {
                    'disetujui' => 'Disetujui',
                    'dikirim' => 'Dikirim',
                    'review' => 'Review',
                    'perlu_revisi' => 'Perlu Revisi',
                    'draft' => 'Draft',
                    default => 'Draft'
                };
                
                $data[] = [
                    $no++,
                    date('d/m/Y H:i', strtotime($waste['created_at'])),
                    $waste['jenis_sampah'] ?? '-',
                    number_format($waste['berat_kg'] ?? 0, 2, '.', ''),
                    $waste['satuan'] ?? 'kg',
                    number_format($waste['nilai_rupiah'] ?? 0, 0, '', ''),
                    $status
                ];
            }

            $filename = 'Data_Sampah_' . ($unit['nama_unit'] ?? 'User') . '_' . date('Y-m-d_His');
            $title = 'LAPORAN DATA SAMPAH - ' . ($unit['nama_unit'] ?? 'User');
            
            helper('excel');
            exportToExcel($data, $headers, $filename, $title);

        } catch (\Exception $e) {
            log_message('error', 'Export Excel User Waste Error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function getWasteList(int $unitId): array
    {
        try {
            return $this->wasteModel
                ->where('unit_id', $unitId)
                ->orderBy('created_at', 'DESC')
                ->findAll();
        } catch (\Exception $e) {
            log_message('error', 'Error getting waste list: ' . $e->getMessage());
            return [];
        }
    }

    private function getCategories(): array
    {
        try {
            // Use direct database query instead of model
            $db = \Config\Database::connect();
            $query = $db->query("SELECT * FROM master_harga_sampah WHERE status_aktif = 1 ORDER BY jenis_sampah ASC");
            $categories = $query->getResultArray();
            
            log_message('info', 'User Categories found: ' . count($categories));
            
            return $categories;
        } catch (\Exception $e) {
            log_message('error', 'Error getting categories: ' . $e->getMessage());
            return [];
        }
    }

    private function getWasteStats(int $unitId): array
    {
        $today = date('Y-m-d');
        $thisMonth = date('Y-m');

        try {
            return [
                'total_entries' => $this->wasteModel->where('unit_id', $unitId)->countAllResults(),
                'pending_count' => $this->wasteModel->where('unit_id', $unitId)->whereIn('status', ['dikirim', 'review'])->countAllResults(),
                'approved_count' => $this->wasteModel->where('unit_id', $unitId)->where('status', 'disetujui')->countAllResults(),
                'rejected_count' => $this->wasteModel->where('unit_id', $unitId)->where('status', 'ditolak')->countAllResults(),
                'draft_count' => $this->wasteModel->where('unit_id', $unitId)->where('status', 'draft')->countAllResults(),
                'total_weight' => $this->wasteModel
                    ->selectSum('berat_kg')
                    ->where('unit_id', $unitId)
                    ->get()
                    ->getRow()
                    ->berat_kg ?? 0,
                'weight_today' => $this->wasteModel
                    ->selectSum('berat_kg')
                    ->where('unit_id', $unitId)
                    ->where('DATE(created_at)', $today)
                    ->get()
                    ->getRow()
                    ->berat_kg ?? 0,
                'weight_month' => $this->wasteModel
                    ->selectSum('berat_kg')
                    ->where('unit_id', $unitId)
                    ->where('DATE_FORMAT(created_at, "%Y-%m")', $thisMonth)
                    ->get()
                    ->getRow()
                    ->berat_kg ?? 0
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error getting waste stats: ' . $e->getMessage());
            return $this->getDefaultStats();
        }
    }

    private function validateWasteData(array $data): array
    {
        if (empty($data['kategori_id'])) {
            return ['valid' => false, 'message' => 'Kategori sampah harus dipilih'];
        }

        if (empty($data['berat_kg']) || !is_numeric($data['berat_kg'])) {
            return ['valid' => false, 'message' => 'Berat sampah harus berupa angka'];
        }

        if ($data['berat_kg'] <= 0) {
            return ['valid' => false, 'message' => 'Berat sampah harus lebih dari 0'];
        }

        // Check if category exists and is active
        $category = $this->hargaModel->find($data['kategori_id']);
        if (!$category || !$category['status_aktif']) {
            return ['valid' => false, 'message' => 'Kategori sampah tidak valid atau tidak aktif'];
        }

        return ['valid' => true, 'message' => ''];
    }

    private function getDefaultStats(): array
    {
        return [
            'total_entries' => 0,
            'pending_count' => 0,
            'approved_count' => 0,
            'rejected_count' => 0,
            'draft_count' => 0,
            'total_weight' => 0,
            'weight_today' => 0,
            'weight_month' => 0
        ];
    }

    private function getRecentActivities(int $userId): array
    {
        try {
            $db = \Config\Database::connect();
            
            // Get recent activities from waste_management table
            $query = $db->query("
                SELECT 
                    wm.id,
                    wm.jenis_sampah,
                    wm.berat_kg,
                    wm.status,
                    wm.catatan_admin,
                    wm.created_at,
                    wm.updated_at,
                    wm.action_timestamp,
                    u.nama_lengkap as user_name
                FROM waste_management wm
                LEFT JOIN users u ON wm.user_id = u.id
                WHERE wm.user_id = ?
                ORDER BY 
                    COALESCE(wm.action_timestamp, wm.updated_at, wm.created_at) DESC
                LIMIT 10
            ", [$userId]);
            
            $activities = $query->getResultArray();
            
            // Format activities with descriptions
            $formattedActivities = [];
            foreach ($activities as $activity) {
                $timestamp = $activity['action_timestamp'] ?? $activity['updated_at'] ?? $activity['created_at'];
                $description = $this->getActivityDescription($activity);
                
                $formattedActivities[] = [
                    'id' => $activity['id'],
                    'description' => $description,
                    'status' => $activity['status'],
                    'timestamp' => $timestamp,
                    'jenis_sampah' => $activity['jenis_sampah'],
                    'berat_kg' => $activity['berat_kg'],
                    'catatan_admin' => $activity['catatan_admin']
                ];
            }
            
            return $formattedActivities;
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting recent activities: ' . $e->getMessage());
            return [];
        }
    }
    
    private function getActivityDescription(array $activity): string
    {
        $jenis = htmlspecialchars($activity['jenis_sampah']);
        $berat = number_format($activity['berat_kg'], 2);
        
        switch ($activity['status']) {
            case 'draft':
                return "Data sampah <strong>{$jenis}</strong> ({$berat} kg) disimpan sebagai draft";
            case 'dikirim':
                return "Data sampah <strong>{$jenis}</strong> ({$berat} kg) dikirim untuk review";
            case 'disetujui':
                return "Data sampah <strong>{$jenis}</strong> ({$berat} kg) disetujui oleh admin";
            case 'ditolak':
                $reason = !empty($activity['catatan_admin']) ? ': ' . htmlspecialchars($activity['catatan_admin']) : '';
                return "Data sampah <strong>{$jenis}</strong> ({$berat} kg) ditolak{$reason}";
            case 'perlu_revisi':
                return "Data sampah <strong>{$jenis}</strong> ({$berat} kg) perlu direvisi";
            default:
                return "Data sampah <strong>{$jenis}</strong> ({$berat} kg) - status: {$activity['status']}";
        }
    }
}
