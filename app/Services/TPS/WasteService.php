<?php

namespace App\Services\TPS;

use App\Models\WasteModel;
use App\Models\HargaSampahModel;

class WasteService
{
    protected $wasteModel;
    protected $hargaModel;

    public function __construct()
    {
        $this->wasteModel = new WasteModel();
        $this->hargaModel = new HargaSampahModel();
    }

    public function getWasteData(): array
    {
        try {
            $user = session()->get('user');
            $tpsId = $user['unit_id'];

            $categories = $this->getCategories();
            log_message('info', 'TPS getWasteData - Categories count: ' . count($categories));

            return [
                'waste_list' => $this->getWasteList($tpsId),
                'categories' => $categories,
                'tps_info' => $this->getTpsInfo($tpsId),
                'stats' => $this->getWasteStats($tpsId)
            ];
        } catch (\Exception $e) {
            log_message('error', 'TPS Waste Service Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            // Return empty but valid structure
            return [
                'waste_list' => [],
                'categories' => [],
                'tps_info' => null,
                'stats' => $this->getDefaultStats()
            ];
        }
    }

    public function saveWaste(array $data): array
    {
        try {
            log_message('info', 'TPS Save Waste - Data received: ' . json_encode($data));
            
            $validation = $this->validateWasteData($data);
            if (!$validation['valid']) {
                log_message('error', 'TPS Save Waste - Validation failed: ' . $validation['message']);
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
            if (isset($data['status_action']) && $data['status_action'] === 'dikirim_ke_tps') {
                $status = 'dikirim_ke_tps';
            }
            
            // Get satuan from input, default to 'kg' if not provided
            $satuan = $data['satuan'] ?? 'kg';
            
            // Get unit pengirim (optional)
            $unitPengirim = isset($data['unit_pengirim']) && !empty($data['unit_pengirim']) ? $data['unit_pengirim'] : null;
            
            // Extract date from datetime
            $tanggalWaktu = $data['tanggal_waktu'] ?? date('Y-m-d H:i:s');
            $tanggal = date('Y-m-d', strtotime($tanggalWaktu));
            
            $wasteData = [
                'unit_id' => $user['unit_id'],
                'unit_pengirim_id' => $unitPengirim,
                'berat_kg' => $data['berat_kg'],
                'tanggal' => $tanggal,
                'jenis_sampah' => $category['jenis_sampah'],
                'satuan' => $satuan,
                'jumlah' => $data['berat_kg'],
                'gedung' => $data['gedung_pelapor'] ?? 'TPS',
                'nama_pelapor' => $data['nama_pelapor'] ?? '',
                'gedung_pelapor' => $data['gedung_pelapor'] ?? '',
                'bukti_foto' => $data['bukti_foto'] ?? null,
                'kategori_sampah' => $category['dapat_dijual'] ? 'bisa_dijual' : 'tidak_bisa_dijual',
                'catatan' => $data['catatan'] ?? '',
                'status' => $status,
                'user_id' => $user['id'],
                'created_at' => $tanggalWaktu
            ];
            
            // Add nilai_rupiah if can be sold
            if ($category['dapat_dijual']) {
                $wasteData['nilai_rupiah'] = $data['berat_kg'] * $category['harga_per_satuan'];
            }

            log_message('info', 'TPS Save Waste - Prepared data: ' . json_encode($wasteData));

            $result = $this->wasteModel->insert($wasteData);
            
            if ($result) {
                log_message('info', 'TPS Save Waste - Success, ID: ' . $result);
                $message = $status === 'dikirim_ke_tps' ? 'Data sampah berhasil disimpan dan dikirim ke TPS' : 'Data sampah berhasil disimpan sebagai draft';
                return ['success' => true, 'message' => $message];
            }

            // Get database error if insert failed
            $db = \Config\Database::connect();
            $error = $db->error();
            log_message('error', 'TPS Save Waste - Insert failed. DB Error: ' . json_encode($error));
            
            return ['success' => false, 'message' => 'Gagal menyimpan data sampah: ' . ($error['message'] ?? 'Unknown error')];

        } catch (\Exception $e) {
            log_message('error', 'Save TPS Waste Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()];
        }
    }

    public function updateWaste(int $id, array $data): array
    {
        try {
            log_message('info', 'TPS Update Waste - Data received: ' . json_encode($data));
            
            // Validate required fields
            if (empty($data['kategori_id'])) {
                return ['success' => false, 'message' => 'Kategori sampah harus dipilih'];
            }
            
            if (empty($data['berat_kg']) || $data['berat_kg'] <= 0) {
                return ['success' => false, 'message' => 'Berat harus diisi dan lebih dari 0'];
            }

            $user = session()->get('user');
            
            // Check if waste belongs to this TPS (using unit_id only)
            $waste = $this->wasteModel->find($id);
            if (!$waste || $waste['unit_id'] != $user['unit_id']) {
                return ['success' => false, 'message' => 'Data sampah tidak ditemukan atau bukan milik TPS Anda'];
            }

            // Check if waste can be edited
            if (!in_array($waste['status'], ['draft', 'perlu_revisi'])) {
                return ['success' => false, 'message' => 'Data yang sudah disubmit tidak dapat diedit'];
            }

            // Get category info
            $category = $this->hargaModel->find($data['kategori_id']);
            
            if (!$category) {
                return ['success' => false, 'message' => 'Kategori sampah tidak ditemukan'];
            }
            
            // Determine status based on action
            $status = 'draft';
            if (isset($data['status_action']) && $data['status_action'] === 'kirim') {
                $status = 'dikirim';
            }
            
            // berat_kg sudah dalam kg dari frontend (sudah dikonversi)
            $beratKg = $data['berat_kg'];
            $satuan = $data['satuan'] ?? 'kg';
            
            $wasteData = [
                'berat_kg' => $beratKg,
                'jumlah' => $beratKg,
                'satuan' => $satuan,
                'jenis_sampah' => $category['jenis_sampah'],
                'kategori_sampah' => $category['dapat_dijual'] ? 'bisa_dijual' : 'tidak_bisa_dijual',
                'status' => $status
            ];
            
            // Update nilai_rupiah if can be sold
            if ($category['dapat_dijual']) {
                $wasteData['nilai_rupiah'] = $beratKg * $category['harga_per_satuan'];
            } else {
                $wasteData['nilai_rupiah'] = 0;
            }

            $result = $this->wasteModel->update($id, $wasteData);
            
            if ($result) {
                $message = $status === 'dikirim' ? 'Data sampah berhasil diupdate dan dikirim' : 'Data sampah berhasil diupdate sebagai draft';
                return ['success' => true, 'message' => $message];
            }

            return ['success' => false, 'message' => 'Gagal mengupdate data sampah'];

        } catch (\Exception $e) {
            log_message('error', 'Update TPS Waste Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()];
        }
    }

    public function deleteWaste(int $id): array
    {
        try {
            $user = session()->get('user');
            
            // Check if waste belongs to this TPS (using unit_id only)
            $waste = $this->wasteModel->find($id);
            if (!$waste || $waste['unit_id'] != $user['unit_id']) {
                return ['success' => false, 'message' => 'Data sampah tidak ditemukan atau bukan milik TPS Anda'];
            }

            $result = $this->wasteModel->delete($id);
            
            if ($result) {
                return ['success' => true, 'message' => 'Data sampah berhasil dihapus'];
            }

            return ['success' => false, 'message' => 'Gagal menghapus data sampah'];

        } catch (\Exception $e) {
            log_message('error', 'Delete TPS Waste Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem'];
        }
    }

    public function exportWaste(): array
    {
        try {
            $user = session()->get('user');
            $tpsId = $user['unit_id'];

            $wasteList = $this->getWasteList($tpsId);
            
            if (empty($wasteList)) {
                return ['success' => false, 'message' => 'Tidak ada data untuk diekspor'];
            }

            // Create CSV content
            $csvContent = "Data Sampah TPS Export\n";
            $csvContent .= "TPS: " . ($this->getTpsInfo($tpsId)['nama_unit'] ?? 'N/A') . "\n";
            $csvContent .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";
            $csvContent .= "Tanggal,Kategori,Berat (kg),Harga per kg,Total Nilai,Status,Keterangan\n";
            
            foreach ($wasteList as $waste) {
                $totalNilai = ($waste['berat_kg'] ?? 0) * ($waste['harga_per_kg'] ?? 0);
                $csvContent .= sprintf(
                    "%s,%s,%s,%s,%s,%s,%s\n",
                    $waste['created_at'],
                    $waste['kategori'] ?? 'N/A',
                    $waste['berat_kg'],
                    $waste['harga_per_kg'] ?? 0,
                    number_format($totalNilai, 0, ',', '.'),
                    $waste['status'] ?? 'N/A',
                    str_replace(['"', ',', "\n"], ['""', ';', ' '], $waste['keterangan'] ?? '')
                );
            }

            // Save to temp file
            $filename = 'tps_waste_export_' . $tpsId . '_' . date('Y-m-d_H-i-s') . '.csv';
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
            log_message('error', 'Export TPS Waste Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan saat export data'];
        }
    }
    
    public function exportPdf(): array
    {
        try {
            $user = session()->get('user');
            $tpsId = $user['unit_id'];
            $tpsInfo = $this->getTpsInfo($tpsId);

            $wasteList = $this->getWasteList($tpsId);
            
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

            // Get monthly summary
            $monthlySummary = $this->getMonthlySummary($tpsId);

            // Prepare data for PDF
            $data = [
                'title' => 'Laporan Data Sampah TPS',
                'tps_info' => $tpsInfo,
                'user' => $user,
                'waste_list' => $wasteList,
                'total_berat' => $totalBerat,
                'total_nilai' => $totalNilai,
                'status_count' => $statusCount,
                'monthly_summary' => $monthlySummary,
                'generated_at' => date('d/m/Y H:i:s')
            ];

            // Generate HTML for PDF
            $html = view('pengelola_tps/waste_pdf', $data);

            // Generate PDF using Dompdf
            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            
            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Save to temp file
            $filename = 'tps_waste_export_' . $tpsId . '_' . date('Y-m-d_H-i-s') . '.pdf';
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
            log_message('error', 'Export PDF TPS Waste Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return ['success' => false, 'message' => 'Terjadi kesalahan saat export PDF: ' . $e->getMessage()];
        }
    }

    public function exportExcel(): void
    {
        try {
            $user = session()->get('user');
            $tpsId = $user['unit_id'];
            $tpsInfo = $this->getTpsInfo($tpsId);

            $wasteList = $this->getWasteList($tpsId);
            
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

            $filename = 'Data_Sampah_TPS_' . ($tpsInfo['nama_unit'] ?? 'TPS') . '_' . date('Y-m-d_His');
            $title = 'LAPORAN DATA SAMPAH TPS - ' . ($tpsInfo['nama_unit'] ?? 'TPS');
            
            helper('excel');
            exportToExcel($data, $headers, $filename, $title);

        } catch (\Exception $e) {
            log_message('error', 'Export Excel TPS Waste Error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function getWasteList(int $tpsId): array
    {
        try {
            return $this->wasteModel
                ->where('unit_id', $tpsId)
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
            
            log_message('info', 'TPS Categories found: ' . count($categories));
            
            return $categories;
        } catch (\Exception $e) {
            log_message('error', 'Error getting categories: ' . $e->getMessage());
            return [];
        }
    }

    private function getTpsInfo(int $tpsId): ?array
    {
        $unitModel = new \App\Models\UnitModel();
        return $unitModel->find($tpsId);
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

    private function getWasteStats(int $tpsId): array
    {
        $today = date('Y-m-d');
        $thisMonth = date('Y-m');

        try {
            return [
                'total_entries' => $this->wasteModel->where('unit_id', $tpsId)->countAllResults(),
                'pending_count' => $this->wasteModel->where('unit_id', $tpsId)->whereIn('status', ['dikirim', 'review'])->countAllResults(),
                'approved_count' => $this->wasteModel->where('unit_id', $tpsId)->where('status', 'disetujui')->countAllResults(),
                'draft_count' => $this->wasteModel->where('unit_id', $tpsId)->where('status', 'draft')->countAllResults(),
                'total_weight' => $this->wasteModel
                    ->selectSum('berat_kg')
                    ->where('unit_id', $tpsId)
                    ->get()
                    ->getRow()
                    ->berat_kg ?? 0
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error getting TPS waste stats: ' . $e->getMessage());
            return $this->getDefaultStats();
        }
    }

    private function getDefaultStats(): array
    {
        return [
            'total_entries' => 0,
            'pending_count' => 0,
            'approved_count' => 0,
            'draft_count' => 0,
            'total_weight' => 0
        ];
    }

    private function getMonthlySummary(int $tpsId): array
    {
        $currentYear = date('Y');
        
        try {
            return $this->wasteModel
                ->select('MONTH(created_at) as month, COUNT(*) as count, SUM(berat_kg) as total_weight')
                ->where('unit_id', $tpsId)
                ->where('YEAR(created_at)', $currentYear)
                ->groupBy('MONTH(created_at)')
                ->orderBy('MONTH(created_at)', 'ASC')
                ->findAll();
        } catch (\Exception $e) {
            log_message('error', 'Error getting monthly summary: ' . $e->getMessage());
            return [];
        }
    }
}