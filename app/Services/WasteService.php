<?php

namespace App\Services;

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

    /**
     * Get User Waste Data
     */
    public function getUserWasteData(): array
    {
        try {
            $user = session()->get('user');
            $unitId = $user['unit_id'];

            $unit = $this->unitModel->find($unitId);
            if (!$unit) {
                throw new \Exception('Unit tidak ditemukan');
            }

            return [
                'user' => $user,
                'unit' => $unit,
                'waste_list' => $this->getUserWasteList($unitId),
                'categories' => $this->getCategories(),
                'stats' => $this->getUserWasteStats($unitId)
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

    /**
     * Get TPS Waste Data
     */
    public function getTpsWasteData(): array
    {
        try {
            $user = session()->get('user');
            $tpsId = $user['unit_id'];

            $tpsInfo = $this->unitModel->find($tpsId);
            if (!$tpsInfo) {
                throw new \Exception('TPS tidak ditemukan');
            }

            return [
                'user' => $user,
                'tps_info' => $tpsInfo,
                'waste_list' => $this->getTpsWasteList($tpsId),
                'categories' => $this->getCategories(),
                'stats' => $this->getTpsWasteStats($tpsId)
            ];
        } catch (\Exception $e) {
            log_message('error', 'TPS Waste Service Error: ' . $e->getMessage());
            
            return [
                'user' => session()->get('user'),
                'tps_info' => null,
                'waste_list' => [],
                'categories' => [],
                'stats' => $this->getDefaultStats()
            ];
        }
    }

    /**
     * Save Waste Data
     */
    public function saveWaste(array $data, string $userType = 'user'): array
    {
        try {
            $validation = $this->validateWasteData($data);
            if (!$validation['valid']) {
                return ['success' => false, 'message' => $validation['message']];
            }

            $user = session()->get('user');
            
            $wasteData = [
                'unit_id' => $user['unit_id'],
                'kategori_id' => $data['kategori_id'],
                'berat_kg' => $data['berat_kg'],
                'keterangan' => $data['keterangan'] ?? '',
                'status' => 'pending',
                'user_id' => $user['id'],  // Changed from created_by to user_id
                'created_at' => date('Y-m-d H:i:s')
            ];

            $result = $this->wasteModel->insert($wasteData);
            
            if ($result) {
                return ['success' => true, 'message' => 'Data sampah berhasil disimpan'];
            }

            return ['success' => false, 'message' => 'Gagal menyimpan data sampah'];

        } catch (\Exception $e) {
            log_message('error', 'Save Waste Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem'];
        }
    }

    /**
     * Update Waste Data
     */
    public function updateWaste(int $id, array $data, string $userType = 'user'): array
    {
        try {
            $validation = $this->validateWasteData($data);
            if (!$validation['valid']) {
                return ['success' => false, 'message' => $validation['message']];
            }

            $user = session()->get('user');
            
            // Check ownership
            $waste = $this->wasteModel->find($id);
            if (!$waste) {
                return ['success' => false, 'message' => 'Data sampah tidak ditemukan'];
            }

            // Verify ownership based on user type
            if ($waste['unit_id'] != $user['unit_id']) {
                return ['success' => false, 'message' => 'Data sampah bukan milik unit Anda'];
            }

            $wasteData = [
                'kategori_id' => $data['kategori_id'],
                'berat_kg' => $data['berat_kg'],
                'keterangan' => $data['keterangan'] ?? '',
                'updated_by' => $user['id'],
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $result = $this->wasteModel->update($id, $wasteData);
            
            if ($result) {
                return ['success' => true, 'message' => 'Data sampah berhasil diupdate'];
            }

            return ['success' => false, 'message' => 'Gagal mengupdate data sampah'];

        } catch (\Exception $e) {
            log_message('error', 'Update Waste Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem'];
        }
    }

    /**
     * Delete Waste Data
     */
    public function deleteWaste(int $id, string $userType = 'user'): array
    {
        try {
            $user = session()->get('user');
            
            // Check ownership
            $waste = $this->wasteModel->find($id);
            if (!$waste) {
                return ['success' => false, 'message' => 'Data sampah tidak ditemukan'];
            }

            // Verify ownership based on user type
            if ($waste['unit_id'] != $user['unit_id']) {
                return ['success' => false, 'message' => 'Data sampah bukan milik unit Anda'];
            }

            $result = $this->wasteModel->delete($id);
            
            if ($result) {
                return ['success' => true, 'message' => 'Data sampah berhasil dihapus'];
            }

            return ['success' => false, 'message' => 'Gagal menghapus data sampah'];

        } catch (\Exception $e) {
            log_message('error', 'Delete Waste Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem'];
        }
    }

    /**
     * Export Waste Data
     */
    public function exportWaste(string $userType = 'user'): array
    {
        try {
            $user = session()->get('user');
            $unitId = $user['unit_id'];

            if ($userType === 'user') {
                $wasteList = $this->getUserWasteList($unitId);
                $prefix = 'user_waste';
            } else {
                $wasteList = $this->getTpsWasteList($unitId);
                $prefix = 'tps_waste';
            }
            
            if (empty($wasteList)) {
                return ['success' => false, 'message' => 'Tidak ada data untuk diekspor'];
            }

            // Create CSV content
            $csvContent = "Data Sampah Export\n";
            $csvContent .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";
            $csvContent .= "Tanggal,Kategori,Berat (kg),Status,Keterangan\n";
            
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
            $filename = $prefix . '_export_' . $unitId . '_' . date('Y-m-d_H-i-s') . '.csv';
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
            log_message('error', 'Export Waste Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan saat export data'];
        }
    }

    // Private helper methods
    private function getUserWasteList(int $unitId): array
    {
        return $this->wasteModel
            ->select('waste.*, master_harga_sampah.jenis_sampah as kategori, master_harga_sampah.harga_per_satuan as harga_per_kg, users.nama_lengkap as created_by_name')
            ->join('master_harga_sampah', 'master_harga_sampah.id = waste.kategori_id', 'left')
            ->join('users', 'users.id = waste.user_id', 'left')
            ->where('waste.unit_id', $unitId)
            ->orderBy('waste.created_at', 'DESC')
            ->findAll();
    }

    private function getTpsWasteList(int $tpsId): array
    {
        return $this->wasteModel
            ->select('waste.*, master_harga_sampah.jenis_sampah as kategori, master_harga_sampah.harga_per_satuan as harga_per_kg, users.nama_lengkap as created_by_name')
            ->join('master_harga_sampah', 'master_harga_sampah.id = waste.kategori_id', 'left')
            ->join('users', 'users.id = waste.user_id', 'left')
            ->where('waste.unit_id', $tpsId)
            ->orderBy('waste.created_at', 'DESC')
            ->findAll();
    }

    private function getCategories(): array
    {
        return $this->hargaModel
            ->where('status', 'active')
            ->orderBy('kategori', 'ASC')
            ->findAll();
    }

    private function getUserWasteStats(int $unitId): array
    {
        $today = date('Y-m-d');
        $thisMonth = date('Y-m');

        return [
            'total_entries' => $this->wasteModel->where('unit_id', $unitId)->countAllResults(),
            'pending_count' => $this->wasteModel->where('unit_id', $unitId)->whereIn('status', ['dikirim', 'review'])->countAllResults(),
            'approved_count' => $this->wasteModel->where('unit_id', $unitId)->where('status', 'disetujui')->countAllResults(),
            'rejected_count' => $this->wasteModel->where('unit_id', $unitId)->where('status', 'ditolak')->countAllResults(),
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
    }

    private function getTpsWasteStats(int $tpsId): array
    {
        $today = date('Y-m-d');
        $thisMonth = date('Y-m');

        return [
            'total_waste_today' => $this->wasteModel
                ->where('unit_id', $tpsId)
                ->where('DATE(created_at)', $today)
                ->countAllResults(),
            
            'total_waste_month' => $this->wasteModel
                ->where('unit_id', $tpsId)
                ->where('DATE_FORMAT(created_at, "%Y-%m")', $thisMonth)
                ->countAllResults(),
            
            'total_weight_today' => $this->wasteModel
                ->selectSum('berat_kg')
                ->where('unit_id', $tpsId)
                ->where('DATE(created_at)', $today)
                ->get()
                ->getRow()
                ->berat_kg ?? 0,
            
            'total_weight_month' => $this->wasteModel
                ->selectSum('berat_kg')
                ->where('unit_id', $tpsId)
                ->where('DATE_FORMAT(created_at, "%Y-%m")', $thisMonth)
                ->get()
                ->getRow()
                ->berat_kg ?? 0
        ];
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
        if (!$category || $category['status'] !== 'active') {
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
            'total_weight' => 0,
            'weight_today' => 0,
            'weight_month' => 0
        ];
    }
}