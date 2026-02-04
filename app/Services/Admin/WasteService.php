<?php

namespace App\Services\Admin;

use App\Models\WasteModel;
use App\Models\HargaSampahModel;
use App\Models\UnitModel;

class WasteService
{
    protected $wasteModel;
    protected $hargaModel;
    protected $unitModel;

    public function __construct()
    {
        $this->wasteModel = new WasteModel();
        $this->hargaModel = new HargaSampahModel();
        $this->unitModel = new UnitModel();
    }

    public function getWasteData(int $page = 1, int $perPage = 10): array
    {
        try {
            log_message('info', 'Admin - Starting getWasteData...');
            
            // Debug: cek total data
            $totalData = $this->wasteModel->countAllResults(false);
            log_message('info', 'Admin - Total data in waste_management: ' . $totalData);
            
            // Debug: cek data per status
            $statusCounts = [];
            foreach (['draft', 'dikirim', 'review', 'disetujui', 'perlu_revisi'] as $status) {
                $count = $this->wasteModel->where('status', $status)->countAllResults(false);
                $statusCounts[$status] = $count;
                log_message('info', "Admin - Status '$status': $count records");
            }
            
            return [
                'waste_list' => $this->getWasteList($page, $perPage),
                'summary' => $this->getWasteSummary(),
                'filters' => $this->getFilterOptions(),
                'statistics' => $this->getWasteStatistics(),
                'pager' => $this->wasteModel->pager
            ];
        } catch (\Exception $e) {
            log_message('error', 'Admin Waste Service Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return [
                'waste_list' => [],
                'summary' => $this->getDefaultSummary(),
                'filters' => [],
                'statistics' => [],
                'pager' => null
            ];
        }
    }

    public function exportWaste(): array
    {
        try {
            $wasteList = $this->getWasteList();
            
            if (empty($wasteList)) {
                return ['success' => false, 'message' => 'Tidak ada data untuk diekspor'];
            }

            // Create CSV content
            $csvContent = "Data Waste Management - Admin Export\n";
            $csvContent .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";
            $csvContent .= "ID,Tanggal,Unit,Jenis Sampah,Berat (kg),Nilai Rupiah,Status,Gedung\n";
            
            foreach ($wasteList as $waste) {
                $csvContent .= sprintf(
                    "%s,%s,%s,%s,%s,%s,%s,%s\n",
                    $waste['id'],
                    $waste['tanggal'],
                    $waste['nama_unit'] ?? 'N/A',
                    $waste['jenis_sampah'] ?? 'N/A',
                    $waste['berat_kg'],
                    $waste['nilai_rupiah'] ?? 0,
                    $waste['status'],
                    $waste['gedung'] ?? 'N/A'
                );
            }

            // Save to temp file
            $filename = 'admin_waste_export_' . date('Y-m-d_H-i-s') . '.csv';
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
            log_message('error', 'Export Admin Waste Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan saat export data'];
        }
    }

    public function approveWaste(int $id, array $data): array
    {
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            $waste = $this->wasteModel->find($id);
            
            if (!$waste) {
                $db->transRollback();
                return ['success' => false, 'message' => 'Data sampah tidak ditemukan'];
            }

            if (!in_array($waste['status'], ['dikirim', 'review', 'draft'])) {
                $db->transRollback();
                return ['success' => false, 'message' => 'Data sampah tidak dapat disetujui'];
            }

            // 1. Insert ke laporan_waste
            $laporanData = [
                'waste_id' => $waste['id'],
                'unit_id' => $waste['unit_id'],
                'kategori_id' => null,
                'jenis_sampah' => $waste['jenis_sampah'],
                'berat_kg' => $waste['berat_kg'],
                'satuan' => $waste['satuan'] ?? 'kg',
                'jumlah' => $waste['jumlah'] ?? $waste['berat_kg'],
                'nilai_rupiah' => $waste['nilai_rupiah'] ?? 0,
                'tanggal_input' => $waste['tanggal'] ?? date('Y-m-d'),
                'status' => 'approved',
                'reviewed_by' => session()->get('user')['id'],
                'reviewed_at' => date('Y-m-d H:i:s'),
                'review_notes' => $data['catatan'] ?? 'Disetujui oleh admin',
                'created_by' => $waste['user_id'] ?? $waste['created_by'] ?? null,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $db->table('laporan_waste')->insert($laporanData);
            
            // 2. Update status and set action_timestamp (will be auto-deleted after 2 days)
            $this->wasteModel->update($id, [
                'status' => 'disetujui',
                'action_timestamp' => date('Y-m-d H:i:s'),
                'catatan_admin' => $data['catatan'] ?? 'Disetujui oleh admin',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                return ['success' => false, 'message' => 'Gagal menyetujui data sampah'];
            }

            return ['success' => true, 'message' => 'Data sampah berhasil disetujui'];

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Approve Waste Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()];
        }
    }

    public function rejectWaste(int $id, array $data): array
    {
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            $waste = $this->wasteModel->find($id);
            
            if (!$waste) {
                $db->transRollback();
                return ['success' => false, 'message' => 'Data sampah tidak ditemukan'];
            }

            if (!in_array($waste['status'], ['dikirim', 'review', 'draft'])) {
                $db->transRollback();
                return ['success' => false, 'message' => 'Data sampah tidak dapat ditolak'];
            }

            if (empty($data['catatan'])) {
                $db->transRollback();
                return ['success' => false, 'message' => 'Catatan penolakan harus diisi'];
            }

            // 1. Insert ke laporan_waste dengan status rejected
            $laporanData = [
                'waste_id' => $waste['id'],
                'unit_id' => $waste['unit_id'],
                'kategori_id' => null,
                'jenis_sampah' => $waste['jenis_sampah'],
                'berat_kg' => $waste['berat_kg'],
                'satuan' => $waste['satuan'] ?? 'kg',
                'jumlah' => $waste['jumlah'] ?? $waste['berat_kg'],
                'nilai_rupiah' => 0, // Rejected waste has no value
                'tanggal_input' => $waste['tanggal'] ?? date('Y-m-d'),
                'status' => 'rejected',
                'reviewed_by' => session()->get('user')['id'],
                'reviewed_at' => date('Y-m-d H:i:s'),
                'review_notes' => $data['catatan'],
                'created_by' => $waste['user_id'] ?? $waste['created_by'] ?? null,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $db->table('laporan_waste')->insert($laporanData);
            
            // 2. Hapus data dari waste_management (sama seperti approve)
            $this->wasteModel->delete($id);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                return ['success' => false, 'message' => 'Gagal menolak data sampah'];
            }

            return ['success' => true, 'message' => 'Data sampah berhasil ditolak'];

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Reject Waste Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()];
        }
    }

    public function deleteWaste(int $id): array
    {
        try {
            $waste = $this->wasteModel->find($id);
            
            if (!$waste) {
                return ['success' => false, 'message' => 'Data sampah tidak ditemukan'];
            }

            // Hapus langsung tanpa pindah ke laporan_waste
            $this->wasteModel->delete($id);
            
            return ['success' => true, 'message' => 'Data sampah berhasil dihapus'];

        } catch (\Exception $e) {
            log_message('error', 'Delete Waste Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()];
        }
    }

    private function getWasteList(int $page = 1, int $perPage = 10): array
    {
        try {
            log_message('info', 'Admin - Getting waste list with unit names...');
            
            $db = \Config\Database::connect();
            
            // Debug: cek total data dulu
            $totalData = $db->table('waste_management')->countAllResults(false);
            log_message('info', 'Admin - Total data in waste_management: ' . $totalData);
            
            // Debug: cek data per status
            $statusQuery = $db->table('waste_management')
                ->select('status, COUNT(*) as count')
                ->groupBy('status')
                ->get()
                ->getResultArray();
            log_message('info', 'Admin - Status breakdown: ' . json_encode($statusQuery));
            
            // Calculate offset
            $offset = ($page - 1) * $perPage;
            
            // Query utama dengan pagination
            // Show: pending data OR approved/rejected data less than 2 days old
            $twoDaysAgo = date('Y-m-d H:i:s', strtotime('-2 days'));
            
            $result = $db->table('waste_management')
                ->select('waste_management.*, unit.nama_unit, master_harga_sampah.nama_jenis')
                ->join('unit', 'unit.id = waste_management.unit_id', 'left')
                ->join('master_harga_sampah', 'master_harga_sampah.jenis_sampah = waste_management.jenis_sampah', 'left')
                ->groupStart()
                    ->whereIn('waste_management.status', ['draft', 'dikirim', 'review'])
                    ->orGroupStart()
                        ->whereIn('waste_management.status', ['disetujui', 'ditolak'])
                        ->where('waste_management.action_timestamp IS NOT NULL')
                        ->where('waste_management.action_timestamp >=', $twoDaysAgo)
                    ->groupEnd()
                ->groupEnd()
                ->orderBy('waste_management.created_at', 'DESC')
                ->limit($perPage, $offset)
                ->get()
                ->getResultArray();
            
            log_message('info', 'Admin - Query found ' . count($result) . ' records (page ' . $page . ')');
            
            if (!empty($result)) {
                log_message('info', 'Admin - First record: ' . json_encode($result[0]));
            }
            
            return $result;
            
        } catch (\Exception $e) {
            log_message('error', 'Error in getWasteList: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return [];
        }
    }

    private function getWasteSummary(): array
    {
        try {
            $today = date('Y-m-d');
            $thisMonth = date('Y-m');
            $thisYear = date('Y');

            return [
                'total_today' => $this->wasteModel
                    ->where('tanggal', $today)
                    ->countAllResults(false) ?? 0,
                
                'total_month' => $this->wasteModel
                    ->where('DATE_FORMAT(tanggal, "%Y-%m")', $thisMonth)
                    ->countAllResults(false) ?? 0,
                
                'total_year' => $this->wasteModel
                    ->where('YEAR(tanggal)', $thisYear)
                    ->countAllResults(false) ?? 0,
                
                'weight_today' => $this->wasteModel
                    ->selectSum('berat_kg')
                    ->where('tanggal', $today)
                    ->get()
                    ->getRow()
                    ->berat_kg ?? 0,
                
                'weight_month' => $this->wasteModel
                    ->selectSum('berat_kg')
                    ->where('DATE_FORMAT(tanggal, "%Y-%m")', $thisMonth)
                    ->get()
                    ->getRow()
                    ->berat_kg ?? 0,
                
                'weight_year' => $this->wasteModel
                    ->selectSum('berat_kg')
                    ->where('YEAR(tanggal)', $thisYear)
                    ->get()
                    ->getRow()
                    ->berat_kg ?? 0,

                'dikirim_count' => $this->wasteModel->where('status', 'dikirim')->countAllResults(false) ?? 0,
                'disetujui_count' => 0, // Tidak ada lagi di waste_management
                'perlu_revisi_count' => $this->wasteModel->where('status', 'perlu_revisi')->countAllResults(false) ?? 0
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error in getWasteSummary: ' . $e->getMessage());
            return $this->getDefaultSummary();
        }
    }

    private function getFilterOptions(): array
    {
        try {
            return [
                'categories' => [],
                'units' => $this->unitModel->where('status_aktif', 1)->findAll() ?? [],
                'statuses' => [
                    'draft' => 'Draft',
                    'dikirim' => 'Dikirim',
                    'review' => 'Review'
                ]
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error in getFilterOptions: ' . $e->getMessage());
            return [
                'categories' => [],
                'units' => [],
                'statuses' => []
            ];
        }
    }

    private function getWasteStatistics(): array
    {
        try {
            // Simplified statistics - no complex joins
            return [
                'by_category' => [],
                'by_unit' => []
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error in getWasteStatistics: ' . $e->getMessage());
            return [
                'by_category' => [],
                'by_unit' => []
            ];
        }
    }

    private function getDefaultSummary(): array
    {
        return [
            'total_today' => 0,
            'total_month' => 0,
            'total_year' => 0,
            'weight_today' => 0,
            'weight_month' => 0,
            'weight_year' => 0,
            'pending_count' => 0,
            'approved_count' => 0,
            'rejected_count' => 0
        ];
    }
}