<?php

namespace App\Services\Admin;

use App\Models\WasteModel;
use App\Models\UnitModel;

class LaporanWasteService
{
    protected $wasteModel;
    protected $unitModel;

    public function __construct()
    {
        $this->wasteModel = new WasteModel();
        $this->unitModel = new UnitModel();
    }

    public function getLaporanData(array $filters, array $pages, int $perPage = 10): array
    {
        try {
            $db = \Config\Database::connect();
            
            // Get data dari laporan_waste dengan pagination per section
            $dataDisetujui = $this->getDataByStatus('approved', $filters, $db, $pages['disetujui'], $perPage);
            $dataDitolak = $this->getDataByStatus('rejected', $filters, $db, $pages['ditolak'], $perPage);
            
            // Rekap per jenis sampah dengan pagination
            $rekapJenis = $this->getRekapPerJenis($filters, $db, $pages['rekap_jenis'], $perPage);
            
            // Rekap per unit dengan pagination
            $rekapUnit = $this->getRekapPerUnit($filters, $db, $pages['rekap_unit'], $perPage);
            
            // Detail rekap per gedung dan pelapor dengan pagination
            $detailRekap = $this->getDetailRekapGedungPelapor($filters, $db, $pages['detail_rekap'] ?? 1, $perPage);
            
            // Get all units for filter
            $units = $this->unitModel->findAll();
            
            // Summary
            $summary = $this->getSummary($filters, $db);
            
            // Count totals for pagination
            $totalDisetujui = $this->countDataByStatus('approved', $filters, $db);
            $totalDitolak = $this->countDataByStatus('rejected', $filters, $db);
            $totalRekapJenis = $this->countRekapPerJenis($filters, $db);
            $totalRekapUnit = $this->countRekapPerUnit($filters, $db);
            $totalDetailRekap = $this->countDetailRekapGedungPelapor($filters, $db);

            return [
                'data_disetujui' => $dataDisetujui,
                'data_ditolak' => $dataDitolak,
                'rekap_jenis' => $rekapJenis,
                'rekap_unit' => $rekapUnit,
                'detail_rekap' => $detailRekap,
                'units' => $units,
                'summary' => $summary,
                'pagination' => [
                    'pages' => $pages,
                    'per_page' => $perPage,
                    'total_disetujui' => $totalDisetujui,
                    'total_ditolak' => $totalDitolak,
                    'total_rekap_jenis' => $totalRekapJenis,
                    'total_rekap_unit' => $totalRekapUnit,
                    'total_detail_rekap' => $totalDetailRekap,
                    'total_pages_disetujui' => ceil($totalDisetujui / $perPage),
                    'total_pages_ditolak' => ceil($totalDitolak / $perPage),
                    'total_pages_rekap_jenis' => ceil($totalRekapJenis / $perPage),
                    'total_pages_rekap_unit' => ceil($totalRekapUnit / $perPage),
                    'total_pages_detail_rekap' => ceil($totalDetailRekap / $perPage)
                ]
            ];
        } catch (\Exception $e) {
            log_message('error', 'Get Laporan Data Error: ' . $e->getMessage());
            
            return [
                'data_disetujui' => [],
                'data_ditolak' => [],
                'rekap_jenis' => [],
                'rekap_unit' => [],
                'detail_rekap' => [],
                'units' => [],
                'summary' => [],
                'pagination' => [
                    'pages' => ['disetujui' => 1, 'ditolak' => 1, 'rekap_jenis' => 1, 'rekap_unit' => 1, 'detail_rekap' => 1],
                    'per_page' => 10,
                    'total_disetujui' => 0,
                    'total_ditolak' => 0,
                    'total_rekap_jenis' => 0,
                    'total_rekap_unit' => 0,
                    'total_detail_rekap' => 0,
                    'total_pages_disetujui' => 0,
                    'total_pages_ditolak' => 0,
                    'total_pages_rekap_jenis' => 0,
                    'total_pages_rekap_unit' => 0,
                    'total_pages_detail_rekap' => 0
                ]
            ];
        }
    }

    private function getDataByStatus(string $status, array $filters, $db, int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        
        // Query dari laporan_waste, bukan waste_management
        $builder = $db->table('laporan_waste')
            ->select('laporan_waste.*, units.nama_unit, users.nama_lengkap as created_by_name, reviewer.nama_lengkap as reviewed_by_name')
            ->join('units', 'units.id = laporan_waste.unit_id', 'left')
            ->join('users', 'users.id = laporan_waste.created_by', 'left')
            ->join('users as reviewer', 'reviewer.id = laporan_waste.reviewed_by', 'left')
            ->where('laporan_waste.status', $status);

        // Apply filters
        if (!empty($filters['start_date'])) {
            $builder->where('DATE(laporan_waste.tanggal_input) >=', $filters['start_date']);
        }
        
        if (!empty($filters['end_date'])) {
            $builder->where('DATE(laporan_waste.tanggal_input) <=', $filters['end_date']);
        }
        
        if (!empty($filters['unit_id'])) {
            $builder->where('laporan_waste.unit_id', $filters['unit_id']);
        }
        
        if (!empty($filters['jenis_sampah'])) {
            $builder->where('laporan_waste.jenis_sampah', $filters['jenis_sampah']);
        }

        return $builder->orderBy('laporan_waste.reviewed_at', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();
    }
    
    private function countDataByStatus(string $status, array $filters, $db): int
    {
        $builder = $db->table('laporan_waste')
            ->where('status', $status);

        if (!empty($filters['start_date'])) {
            $builder->where('DATE(tanggal_input) >=', $filters['start_date']);
        }
        
        if (!empty($filters['end_date'])) {
            $builder->where('DATE(tanggal_input) <=', $filters['end_date']);
        }
        
        if (!empty($filters['unit_id'])) {
            $builder->where('unit_id', $filters['unit_id']);
        }
        
        if (!empty($filters['jenis_sampah'])) {
            $builder->where('jenis_sampah', $filters['jenis_sampah']);
        }

        return $builder->countAllResults();
    }

    private function getRekapPerJenis(array $filters, $db, int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT 
                    jenis_sampah,
                    COUNT(*) as total_transaksi,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as total_disetujui,
                    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as total_ditolak,
                    SUM(CASE WHEN status = 'approved' THEN berat_kg ELSE 0 END) as total_berat_disetujui,
                    SUM(CASE WHEN status = 'rejected' THEN berat_kg ELSE 0 END) as total_berat_ditolak,
                    SUM(CASE WHEN status = 'approved' THEN nilai_rupiah ELSE 0 END) as total_nilai_disetujui,
                    SUM(CASE WHEN status = 'rejected' THEN nilai_rupiah ELSE 0 END) as total_nilai_ditolak
                FROM laporan_waste
                WHERE status IN ('approved', 'rejected')";
        
        $params = [];
        
        if (!empty($filters['start_date'])) {
            $sql .= " AND DATE(tanggal_input) >= ?";
            $params[] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $sql .= " AND DATE(tanggal_input) <= ?";
            $params[] = $filters['end_date'];
        }
        
        if (!empty($filters['unit_id'])) {
            $sql .= " AND unit_id = ?";
            $params[] = $filters['unit_id'];
        }
        
        $sql .= " GROUP BY jenis_sampah ORDER BY total_berat_disetujui DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $query = $db->query($sql, $params);
        return $query->getResultArray();
    }

    /**
     * Get detail rekap per jenis sampah (rincian per gedung dan pelapor)
     * 
     * @param string $jenisSampah
     * @param array $filters
     * @return array
     */
    public function getDetailRekapJenis(string $jenisSampah, array $filters): array
    {
        try {
            $db = \Config\Database::connect();
            
            // Query with COALESCE to handle NULL created_by
            $builder = $db->table('laporan_waste lw')
                ->select('u.nama_unit as gedung,
                         u.nama_unit,
                         COALESCE(users.nama_lengkap, "Data Lama (Tidak Ada Info Pelapor)") as nama_pelapor,
                         COALESCE(users.username, "-") as username,
                         lw.jenis_sampah as nama_sampah,
                         COUNT(*) as jumlah_laporan,
                         SUM(CASE WHEN lw.status = "approved" THEN 1 ELSE 0 END) as total_disetujui,
                         SUM(CASE WHEN lw.status = "rejected" THEN 1 ELSE 0 END) as total_ditolak,
                         SUM(CASE WHEN lw.status = "approved" THEN lw.berat_kg ELSE 0 END) as total_berat_disetujui,
                         SUM(CASE WHEN lw.status = "approved" THEN lw.nilai_rupiah ELSE 0 END) as total_nilai_disetujui,
                         MIN(lw.tanggal_input) as laporan_pertama,
                         MAX(lw.tanggal_input) as laporan_terakhir,
                         lw.created_by')
                ->join('units u', 'u.id = lw.unit_id', 'left')
                ->join('users', 'users.id = lw.created_by', 'left')
                ->where('lw.jenis_sampah', $jenisSampah)
                ->whereIn('lw.status', ['approved', 'rejected']);
            
            // Apply filters
            if (!empty($filters['start_date'])) {
                $builder->where('DATE(lw.tanggal_input) >=', $filters['start_date']);
            }
            
            if (!empty($filters['end_date'])) {
                $builder->where('DATE(lw.tanggal_input) <=', $filters['end_date']);
            }
            
            if (!empty($filters['unit_id'])) {
                $builder->where('lw.unit_id', $filters['unit_id']);
            }
            
            // Group by unit and created_by (to separate old data from new data with user info)
            $builder->groupBy('u.nama_unit, lw.created_by, lw.jenis_sampah')
                    ->orderBy('total_berat_disetujui', 'DESC')
                    ->orderBy('jumlah_laporan', 'DESC');
            
            $details = $builder->get()->getResultArray();
            
            // Get summary
            $builderSummary = $db->table('laporan_waste')
                ->select('COUNT(*) as total_transaksi,
                         SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as total_disetujui,
                         SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as total_ditolak,
                         SUM(CASE WHEN status = "approved" THEN berat_kg ELSE 0 END) as total_berat_disetujui,
                         SUM(CASE WHEN status = "approved" THEN nilai_rupiah ELSE 0 END) as total_nilai_disetujui,
                         COUNT(DISTINCT unit_id) as total_gedung,
                         COUNT(DISTINCT created_by) as total_pelapor')
                ->where('jenis_sampah', $jenisSampah)
                ->whereIn('status', ['approved', 'rejected']);
            
            if (!empty($filters['start_date'])) {
                $builderSummary->where('DATE(tanggal_input) >=', $filters['start_date']);
            }
            
            if (!empty($filters['end_date'])) {
                $builderSummary->where('DATE(tanggal_input) <=', $filters['end_date']);
            }
            
            if (!empty($filters['unit_id'])) {
                $builderSummary->where('unit_id', $filters['unit_id']);
            }
            
            $summary = $builderSummary->get()->getRowArray();
            
            return [
                'success' => true,
                'jenis_sampah' => $jenisSampah,
                'details' => $details,
                'summary' => $summary
            ];
            
        } catch (\Exception $e) {
            log_message('error', 'Get Detail Rekap Jenis Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat detail: ' . $e->getMessage(),
                'details' => [],
                'summary' => []
            ];
        }
    }
    
    private function countRekapPerJenis(array $filters, $db): int
    {
        $sql = "SELECT COUNT(DISTINCT jenis_sampah) as total
                FROM laporan_waste
                WHERE status IN ('approved', 'rejected')";
        
        $params = [];
        
        if (!empty($filters['start_date'])) {
            $sql .= " AND DATE(tanggal_input) >= ?";
            $params[] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $sql .= " AND DATE(tanggal_input) <= ?";
            $params[] = $filters['end_date'];
        }
        
        if (!empty($filters['unit_id'])) {
            $sql .= " AND unit_id = ?";
            $params[] = $filters['unit_id'];
        }
        
        $query = $db->query($sql, $params);
        $result = $query->getRow();
        return $result ? (int)$result->total : 0;
    }

    private function getRekapPerUnit(array $filters, $db, int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT 
                    u.id as unit_id,
                    u.nama_unit,
                    COUNT(lw.id) as total_transaksi,
                    SUM(CASE WHEN lw.status = 'approved' THEN 1 ELSE 0 END) as total_disetujui,
                    SUM(CASE WHEN lw.status = 'rejected' THEN 1 ELSE 0 END) as total_ditolak,
                    SUM(CASE WHEN lw.status = 'approved' THEN lw.berat_kg ELSE 0 END) as total_berat_disetujui,
                    SUM(CASE WHEN lw.status = 'rejected' THEN lw.berat_kg ELSE 0 END) as total_berat_ditolak,
                    SUM(CASE WHEN lw.status = 'approved' THEN lw.nilai_rupiah ELSE 0 END) as total_nilai_disetujui,
                    SUM(CASE WHEN lw.status = 'rejected' THEN lw.nilai_rupiah ELSE 0 END) as total_nilai_ditolak
                FROM units u
                LEFT JOIN laporan_waste lw ON lw.unit_id = u.id AND lw.status IN ('approved', 'rejected')";
        
        $params = [];
        $whereConditions = [];
        
        if (!empty($filters['start_date'])) {
            $whereConditions[] = "DATE(lw.tanggal_input) >= ?";
            $params[] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $whereConditions[] = "DATE(lw.tanggal_input) <= ?";
            $params[] = $filters['end_date'];
        }
        
        if (!empty($filters['unit_id'])) {
            $whereConditions[] = "u.id = ?";
            $params[] = $filters['unit_id'];
        }
        
        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(' AND ', $whereConditions);
        }
        
        $sql .= " GROUP BY u.id, u.nama_unit ORDER BY total_berat_disetujui DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $query = $db->query($sql, $params);
        return $query->getResultArray();
    }
    
    private function countRekapPerUnit(array $filters, $db): int
    {
        $sql = "SELECT COUNT(DISTINCT u.id) as total
                FROM units u
                LEFT JOIN laporan_waste lw ON lw.unit_id = u.id AND lw.status IN ('approved', 'rejected')";
        
        $params = [];
        $whereConditions = [];
        
        if (!empty($filters['start_date'])) {
            $whereConditions[] = "DATE(lw.tanggal_input) >= ?";
            $params[] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $whereConditions[] = "DATE(lw.tanggal_input) <= ?";
            $params[] = $filters['end_date'];
        }
        
        if (!empty($filters['unit_id'])) {
            $whereConditions[] = "u.id = ?";
            $params[] = $filters['unit_id'];
        }
        
        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(' AND ', $whereConditions);
        }
        
        $query = $db->query($sql, $params);
        $result = $query->getRow();
        return $result ? (int)$result->total : 0;
    }

    private function getSummary(array $filters, $db): array
    {
        $sql = "SELECT 
                    COUNT(*) as total_transaksi,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as total_disetujui,
                    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as total_ditolak,
                    SUM(CASE WHEN status = 'approved' THEN berat_kg ELSE 0 END) as total_berat_disetujui,
                    SUM(CASE WHEN status = 'rejected' THEN berat_kg ELSE 0 END) as total_berat_ditolak,
                    SUM(CASE WHEN status = 'approved' THEN nilai_rupiah ELSE 0 END) as total_nilai_disetujui,
                    SUM(CASE WHEN status = 'rejected' THEN nilai_rupiah ELSE 0 END) as total_nilai_ditolak
                FROM laporan_waste
                WHERE status IN ('approved', 'rejected')";
        
        $params = [];
        
        if (!empty($filters['start_date'])) {
            $sql .= " AND DATE(tanggal_input) >= ?";
            $params[] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $sql .= " AND DATE(tanggal_input) <= ?";
            $params[] = $filters['end_date'];
        }
        
        if (!empty($filters['unit_id'])) {
            $sql .= " AND unit_id = ?";
            $params[] = $filters['unit_id'];
        }
        
        $query = $db->query($sql, $params);
        $result = $query->getRowArray();
        
        return $result ?: [
            'total_transaksi' => 0,
            'total_disetujui' => 0,
            'total_ditolak' => 0,
            'total_berat_disetujui' => 0,
            'total_berat_ditolak' => 0,
            'total_nilai_disetujui' => 0,
            'total_nilai_ditolak' => 0
        ];
    }

    public function exportLaporan(array $filters): array
    {
        try {
            $data = $this->getLaporanData($filters);
            
            // Create CSV content
            $csvContent = "LAPORAN WASTE - UI GREENMETRIC POLBAN\n";
            $csvContent .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";
            
            // Summary
            $csvContent .= "=== RINGKASAN ===\n";
            $csvContent .= "Total Transaksi," . $data['summary']['total_transaksi'] . "\n";
            $csvContent .= "Total Disetujui," . $data['summary']['total_disetujui'] . "\n";
            $csvContent .= "Total Ditolak," . $data['summary']['total_ditolak'] . "\n";
            $csvContent .= "Total Berat Disetujui (kg)," . number_format($data['summary']['total_berat_disetujui'], 2) . "\n";
            $csvContent .= "Total Berat Ditolak (kg)," . number_format($data['summary']['total_berat_ditolak'], 2) . "\n";
            $csvContent .= "Total Nilai Disetujui (Rp)," . number_format($data['summary']['total_nilai_disetujui'], 0) . "\n";
            $csvContent .= "Total Nilai Ditolak (Rp)," . number_format($data['summary']['total_nilai_ditolak'], 0) . "\n\n";
            
            // Rekap per Jenis
            $csvContent .= "=== REKAP PER JENIS SAMPAH ===\n";
            $csvContent .= "Jenis Sampah,Total Transaksi,Disetujui,Ditolak,Berat Disetujui (kg),Berat Ditolak (kg),Nilai Disetujui (Rp),Nilai Ditolak (Rp)\n";
            foreach ($data['rekap_jenis'] as $item) {
                $csvContent .= sprintf(
                    "%s,%d,%d,%d,%s,%s,%s,%s\n",
                    $item['jenis_sampah'],
                    $item['total_transaksi'],
                    $item['total_disetujui'],
                    $item['total_ditolak'],
                    number_format($item['total_berat_disetujui'], 2),
                    number_format($item['total_berat_ditolak'], 2),
                    number_format($item['total_nilai_disetujui'], 0),
                    number_format($item['total_nilai_ditolak'], 0)
                );
            }
            
            $csvContent .= "\n=== REKAP PER UNIT ===\n";
            $csvContent .= "Unit,Total Transaksi,Disetujui,Ditolak,Berat Disetujui (kg),Berat Ditolak (kg),Nilai Disetujui (Rp),Nilai Ditolak (Rp)\n";
            foreach ($data['rekap_unit'] as $item) {
                $csvContent .= sprintf(
                    "%s,%d,%d,%d,%s,%s,%s,%s\n",
                    $item['nama_unit'],
                    $item['total_transaksi'],
                    $item['total_disetujui'],
                    $item['total_ditolak'],
                    number_format($item['total_berat_disetujui'], 2),
                    number_format($item['total_berat_ditolak'], 2),
                    number_format($item['total_nilai_disetujui'], 0),
                    number_format($item['total_nilai_ditolak'], 0)
                );
            }

            // Save to temp file
            $filename = 'laporan_waste_' . date('Y-m-d_H-i-s') . '.csv';
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
            log_message('error', 'Export Laporan Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan saat export laporan'];
        }
    }

    public function exportPdf(array $filters): array
    {
        try {
            // Untuk export PDF, ambil semua data tanpa pagination
            // Set pages ke 1 dan perPage ke nilai besar untuk ambil semua data
            $pages = [
                'disetujui' => 1,
                'ditolak' => 1,
                'rekap_jenis' => 1,
                'rekap_unit' => 1
            ];
            $perPage = 10000; // Ambil semua data
            
            $data = $this->getLaporanData($filters, $pages, $perPage);
            
            // Load view untuk PDF
            $html = view('admin_pusat/laporan_waste_pdf_new', [
                'data' => $data,
                'filters' => $filters,
                'generated_at' => date('d/m/Y H:i:s')
            ]);

            // Generate PDF menggunakan Dompdf
            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            
            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            // Save to temp file
            $filename = 'laporan_waste_' . date('Y-m-d_H-i-s') . '.pdf';
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
            log_message('error', 'Export PDF Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return ['success' => false, 'message' => 'Terjadi kesalahan saat export PDF: ' . $e->getMessage()];
        }
    }

    public function exportCsv(array $filters): array
    {
        // Sama dengan exportLaporan
        return $this->exportLaporan($filters);
    }

    public function exportExcel(array $filters): void
    {
        try {
            $db = \Config\Database::connect();
            
            // Get all data without pagination - use simple query
            $dataDisetujui = [];
            $dataDitolak = [];
            $rekapJenis = [];
            $rekapUnit = [];
            
            // Query data disetujui
            $query = "SELECT w.*, u.nama_unit, u.kode_unit 
                      FROM laporan_waste w 
                      LEFT JOIN units u ON u.id = w.unit_id 
                      WHERE w.status = 'approved'";
            
            if (!empty($filters['start_date'])) {
                $query .= " AND DATE(w.tanggal_input) >= '" . $db->escapeString($filters['start_date']) . "'";
            }
            if (!empty($filters['end_date'])) {
                $query .= " AND DATE(w.tanggal_input) <= '" . $db->escapeString($filters['end_date']) . "'";
            }
            if (!empty($filters['unit_id'])) {
                $query .= " AND w.unit_id = " . (int)$filters['unit_id'];
            }
            
            $query .= " ORDER BY w.tanggal_input DESC";
            $dataDisetujui = $db->query($query)->getResultArray();
            
            // Query data ditolak
            $query = "SELECT w.*, u.nama_unit, u.kode_unit 
                      FROM laporan_waste w 
                      LEFT JOIN units u ON u.id = w.unit_id 
                      WHERE w.status = 'rejected'";
            
            if (!empty($filters['start_date'])) {
                $query .= " AND DATE(w.tanggal_input) >= '" . $db->escapeString($filters['start_date']) . "'";
            }
            if (!empty($filters['end_date'])) {
                $query .= " AND DATE(w.tanggal_input) <= '" . $db->escapeString($filters['end_date']) . "'";
            }
            if (!empty($filters['unit_id'])) {
                $query .= " AND w.unit_id = " . (int)$filters['unit_id'];
            }
            
            $query .= " ORDER BY w.tanggal_input DESC";
            $dataDitolak = $db->query($query)->getResultArray();
            
            // Query rekap per jenis
            $query = "SELECT w.jenis_sampah, 
                             SUM(w.berat_kg) as total_berat,
                             SUM(w.nilai_rupiah) as total_nilai,
                             COUNT(*) as jumlah_data
                      FROM laporan_waste w 
                      WHERE w.status = 'approved'";
            
            if (!empty($filters['start_date'])) {
                $query .= " AND DATE(w.tanggal_input) >= '" . $db->escapeString($filters['start_date']) . "'";
            }
            if (!empty($filters['end_date'])) {
                $query .= " AND DATE(w.tanggal_input) <= '" . $db->escapeString($filters['end_date']) . "'";
            }
            if (!empty($filters['unit_id'])) {
                $query .= " AND w.unit_id = " . (int)$filters['unit_id'];
            }
            
            $query .= " GROUP BY w.jenis_sampah ORDER BY total_berat DESC";
            $rekapJenis = $db->query($query)->getResultArray();
            
            // Query rekap per unit
            $query = "SELECT u.nama_unit, u.kode_unit,
                             SUM(w.berat_kg) as total_berat,
                             SUM(w.nilai_rupiah) as total_nilai,
                             COUNT(*) as jumlah_data
                      FROM laporan_waste w 
                      LEFT JOIN units u ON u.id = w.unit_id 
                      WHERE w.status = 'approved'";
            
            if (!empty($filters['start_date'])) {
                $query .= " AND DATE(w.tanggal_input) >= '" . $db->escapeString($filters['start_date']) . "'";
            }
            if (!empty($filters['end_date'])) {
                $query .= " AND DATE(w.tanggal_input) <= '" . $db->escapeString($filters['end_date']) . "'";
            }
            if (!empty($filters['unit_id'])) {
                $query .= " AND w.unit_id = " . (int)$filters['unit_id'];
            }
            
            $query .= " GROUP BY w.unit_id ORDER BY total_berat DESC";
            $rekapUnit = $db->query($query)->getResultArray();
            
            // Prepare data for Excel
            $excelData = [];
            $headers = [];
            
            // Sheet 1: Rekap per Jenis Sampah
            $headers[] = ['No', 'Jenis Sampah', 'Total Berat (kg)', 'Total Nilai (Rp)', 'Jumlah Data'];
            $sheetData = [];
            $no = 1;
            foreach ($rekapJenis as $item) {
                $sheetData[] = [
                    $no++,
                    $item['jenis_sampah'] ?? '-',
                    number_format($item['total_berat'] ?? 0, 2, '.', ''),
                    number_format($item['total_nilai'] ?? 0, 0, '', ''),
                    $item['jumlah_data'] ?? 0
                ];
            }
            $excelData['rekap_jenis'] = ['headers' => $headers[0], 'data' => $sheetData];
            
            // Sheet 2: Rekap per Unit
            $headers[] = ['No', 'Unit', 'Total Berat (kg)', 'Total Nilai (Rp)', 'Jumlah Data'];
            $sheetData = [];
            $no = 1;
            foreach ($rekapUnit as $item) {
                $sheetData[] = [
                    $no++,
                    $item['nama_unit'] ?? '-',
                    number_format($item['total_berat'] ?? 0, 2, '.', ''),
                    number_format($item['total_nilai'] ?? 0, 0, '', ''),
                    $item['jumlah_data'] ?? 0
                ];
            }
            $excelData['rekap_unit'] = ['headers' => $headers[1], 'data' => $sheetData];
            
            // Sheet 3: Data Disetujui
            $headers[] = ['No', 'Tanggal', 'Unit', 'Jenis Sampah', 'Berat (kg)', 'Satuan', 'Nilai (Rp)', 'Status'];
            $sheetData = [];
            $no = 1;
            foreach ($dataDisetujui as $item) {
                $sheetData[] = [
                    $no++,
                    date('d/m/Y', strtotime($item['tanggal_input'] ?? 'now')),
                    $item['nama_unit'] ?? '-',
                    $item['jenis_sampah'] ?? '-',
                    number_format($item['berat_kg'] ?? 0, 2, '.', ''),
                    $item['satuan'] ?? 'kg',
                    number_format($item['nilai_rupiah'] ?? 0, 0, '', ''),
                    'Disetujui'
                ];
            }
            $excelData['data_disetujui'] = ['headers' => $headers[2], 'data' => $sheetData];
            
            // Sheet 4: Data Ditolak
            $headers[] = ['No', 'Tanggal', 'Unit', 'Jenis Sampah', 'Berat (kg)', 'Satuan', 'Nilai (Rp)', 'Status', 'Alasan'];
            $sheetData = [];
            $no = 1;
            foreach ($dataDitolak as $item) {
                $sheetData[] = [
                    $no++,
                    date('d/m/Y', strtotime($item['tanggal_input'] ?? 'now')),
                    $item['nama_unit'] ?? '-',
                    $item['jenis_sampah'] ?? '-',
                    number_format($item['berat_kg'] ?? 0, 2, '.', ''),
                    $item['satuan'] ?? 'kg',
                    number_format($item['nilai_rupiah'] ?? 0, 0, '', ''),
                    'Ditolak',
                    $item['review_notes'] ?? '-'
                ];
            }
            $excelData['data_ditolak'] = ['headers' => $headers[3], 'data' => $sheetData];
            
            // Generate filename
            $filename = 'Laporan_Waste_' . date('Y-m-d_His');
            
            // Export using helper
            helper('excel');
            $this->exportMultiSheetExcel($excelData, $filename);
            
        } catch (\Exception $e) {
            log_message('error', 'Export Excel Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            // Show error to user
            echo '<html><body>';
            echo '<h1>Error Export Excel</h1>';
            echo '<p>Terjadi kesalahan: ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<p><a href="javascript:history.back()">Kembali</a></p>';
            echo '</body></html>';
            exit;
        }
    }

    private function getAllDataByStatus(string $status, array $filters, $db): array
    {
        try {
            $builder = $db->table('laporan_waste w');
            $builder->select('w.*, u.nama_unit, u.kode_unit');
            $builder->join('units u', 'u.id = w.unit_id', 'left');
            $builder->where('w.status', $status);
            
            // Apply filters
            if (!empty($filters['start_date'])) {
                $builder->where('DATE(w.tanggal_input) >=', $filters['start_date']);
            }
            if (!empty($filters['end_date'])) {
                $builder->where('DATE(w.tanggal_input) <=', $filters['end_date']);
            }
            if (!empty($filters['unit_id'])) {
                $builder->where('w.unit_id', $filters['unit_id']);
            }
            
            $builder->orderBy('w.tanggal_input', 'DESC');
            
            return $builder->get()->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error getting all data by status: ' . $e->getMessage());
            return [];
        }
    }

    private function getAllRekapPerJenis(array $filters, $db): array
    {
        try {
            $builder = $db->table('laporan_waste w');
            $builder->select('w.jenis_sampah, 
                             SUM(w.berat_kg) as total_berat,
                             SUM(w.nilai_rupiah) as total_nilai,
                             COUNT(*) as jumlah_data');
            $builder->where('w.status', 'approved');
            
            // Apply filters
            if (!empty($filters['start_date'])) {
                $builder->where('DATE(w.tanggal_input) >=', $filters['start_date']);
            }
            if (!empty($filters['end_date'])) {
                $builder->where('DATE(w.tanggal_input) <=', $filters['end_date']);
            }
            if (!empty($filters['unit_id'])) {
                $builder->where('w.unit_id', $filters['unit_id']);
            }
            
            $builder->groupBy('w.jenis_sampah');
            $builder->orderBy('total_berat', 'DESC');
            
            return $builder->get()->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error getting rekap per jenis: ' . $e->getMessage());
            return [];
        }
    }

    private function getAllRekapPerUnit(array $filters, $db): array
    {
        try {
            $builder = $db->table('laporan_waste w');
            $builder->select('u.nama_unit, u.kode_unit,
                             SUM(w.berat_kg) as total_berat,
                             SUM(w.nilai_rupiah) as total_nilai,
                             COUNT(*) as jumlah_data');
            $builder->join('units u', 'u.id = w.unit_id', 'left');
            $builder->where('w.status', 'approved');
            
            // Apply filters
            if (!empty($filters['start_date'])) {
                $builder->where('DATE(w.tanggal_input) >=', $filters['start_date']);
            }
            if (!empty($filters['end_date'])) {
                $builder->where('DATE(w.tanggal_input) <=', $filters['end_date']);
            }
            if (!empty($filters['unit_id'])) {
                $builder->where('w.unit_id', $filters['unit_id']);
            }
            
            $builder->groupBy('w.unit_id');
            $builder->orderBy('total_berat', 'DESC');
            
            return $builder->get()->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error getting rekap per unit: ' . $e->getMessage());
            return [];
        }
    }

    private function exportMultiSheetExcel(array $sheets, string $filename): void
    {
        // Set headers for Excel download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
        header('Cache-Control: max-age=0');
        
        // Start HTML
        echo '<!DOCTYPE html>';
        echo '<html>';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo '<style>';
        echo 'table { border-collapse: collapse; width: 100%; margin-bottom: 30px; }';
        echo 'th, td { border: 1px solid #000; padding: 8px; text-align: left; }';
        echo 'th { background-color: #4472C4; color: white; font-weight: bold; }';
        echo '.sheet-title { font-size: 16px; font-weight: bold; margin: 20px 0 10px 0; background-color: #E7E6E6; padding: 10px; }';
        echo '.title { font-size: 18px; font-weight: bold; margin-bottom: 10px; }';
        echo '.number { mso-number-format:"0\.00"; }';
        echo '.currency { mso-number-format:"Rp\\ \#\,\#\#0"; }';
        echo '</style>';
        echo '</head>';
        echo '<body>';
        
        // Main Title
        echo '<div class="title">LAPORAN DATA SAMPAH</div>';
        echo '<div style="margin-bottom: 20px;">Dicetak pada: ' . date('d/m/Y H:i:s') . '</div>';
        
        // Sheets
        $sheetTitles = [
            'rekap_jenis' => 'Rekap per Jenis Sampah',
            'rekap_unit' => 'Rekap per Unit',
            'data_disetujui' => 'Data Disetujui',
            'data_ditolak' => 'Data Ditolak'
        ];
        
        foreach ($sheets as $sheetKey => $sheet) {
            echo '<div class="sheet-title">' . ($sheetTitles[$sheetKey] ?? $sheetKey) . '</div>';
            echo '<table>';
            
            // Headers
            echo '<thead><tr>';
            foreach ($sheet['headers'] as $header) {
                echo '<th>' . htmlspecialchars($header) . '</th>';
            }
            echo '</tr></thead>';
            
            // Data
            echo '<tbody>';
            if (!empty($sheet['data'])) {
                foreach ($sheet['data'] as $row) {
                    echo '<tr>';
                    foreach ($row as $idx => $cell) {
                        $class = '';
                        // Detect number/currency columns
                        if ($idx >= 2 && $idx <= 3 && is_numeric($cell)) {
                            $class = $idx == 3 ? 'currency' : 'number';
                        }
                        echo '<td class="' . $class . '">' . htmlspecialchars($cell) . '</td>';
                    }
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="' . count($sheet['headers']) . '" style="text-align: center;">Tidak ada data</td></tr>';
            }
            echo '</tbody>';
            echo '</table>';
        }
        
        echo '</body>';
        echo '</html>';
        
        exit;
    }

    /**
     * Get detail rekap per minggu dalam bulan (untuk tabel di halaman utama)
     */
    private function getDetailRekapGedungPelapor(array $filters, $db, int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        
        // Query dengan pengelompokan per minggu
        $builder = $db->table('laporan_waste lw')
            ->select('YEAR(lw.tanggal_input) as tahun,
                     MONTH(lw.tanggal_input) as bulan,
                     WEEK(lw.tanggal_input, 1) - WEEK(DATE_SUB(lw.tanggal_input, INTERVAL DAYOFMONTH(lw.tanggal_input) - 1 DAY), 1) + 1 as minggu_ke,
                     u.nama_unit as gedung,
                     u.nama_unit,
                     COALESCE(users.nama_lengkap, "Data Lama (Tidak Ada Info Pelapor)") as nama_pelapor,
                     COALESCE(users.username, "-") as username,
                     lw.jenis_sampah,
                     COUNT(*) as jumlah_laporan,
                     SUM(CASE WHEN lw.status = "approved" THEN 1 ELSE 0 END) as total_disetujui,
                     SUM(CASE WHEN lw.status = "rejected" THEN 1 ELSE 0 END) as total_ditolak,
                     SUM(CASE WHEN lw.status = "approved" THEN lw.berat_kg ELSE 0 END) as total_berat_disetujui,
                     SUM(CASE WHEN lw.status = "approved" THEN lw.nilai_rupiah ELSE 0 END) as total_nilai_disetujui,
                     MIN(lw.tanggal_input) as laporan_pertama,
                     MAX(lw.tanggal_input) as laporan_terakhir')
            ->join('units u', 'u.id = lw.unit_id', 'left')
            ->join('users', 'users.id = lw.created_by', 'left')
            ->whereIn('lw.status', ['approved', 'rejected']);
        
        // Apply filters
        if (!empty($filters['start_date'])) {
            $builder->where('DATE(lw.tanggal_input) >=', $filters['start_date']);
        }
        
        if (!empty($filters['end_date'])) {
            $builder->where('DATE(lw.tanggal_input) <=', $filters['end_date']);
        }
        
        if (!empty($filters['unit_id'])) {
            $builder->where('lw.unit_id', $filters['unit_id']);
        }
        
        if (!empty($filters['jenis_sampah'])) {
            $builder->where('lw.jenis_sampah', $filters['jenis_sampah']);
        }
        
        // Filter khusus tabel rekap mingguan
        if (!empty($filters['filter_bulan'])) {
            $builder->where('MONTH(lw.tanggal_input)', $filters['filter_bulan']);
        }
        
        if (!empty($filters['filter_tahun'])) {
            $builder->where('YEAR(lw.tanggal_input)', $filters['filter_tahun']);
        }
        
        if (!empty($filters['filter_minggu'])) {
            $builder->having('minggu_ke', $filters['filter_minggu']);
        }
        
        if (!empty($filters['filter_gedung'])) {
            $builder->like('u.nama_unit', $filters['filter_gedung']);
        }
        
        if (!empty($filters['filter_pelapor'])) {
            $builder->groupStart()
                    ->like('users.nama_lengkap', $filters['filter_pelapor'])
                    ->orLike('users.username', $filters['filter_pelapor'])
                    ->groupEnd();
        }
        
        // Group by tahun, bulan, minggu, unit, created_by, jenis_sampah
        $builder->groupBy('YEAR(lw.tanggal_input), MONTH(lw.tanggal_input), minggu_ke, u.nama_unit, lw.created_by, lw.jenis_sampah')
                ->orderBy('tahun', 'DESC')
                ->orderBy('bulan', 'DESC')
                ->orderBy('minggu_ke', 'DESC')
                ->orderBy('total_berat_disetujui', 'DESC')
                ->limit($perPage, $offset);
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Count detail rekap per minggu dalam bulan
     */
    private function countDetailRekapGedungPelapor(array $filters, $db): int
    {
        $builder = $db->table('laporan_waste lw')
            ->select('COUNT(DISTINCT CONCAT(
                YEAR(lw.tanggal_input), "-",
                MONTH(lw.tanggal_input), "-",
                WEEK(lw.tanggal_input, 1) - WEEK(DATE_SUB(lw.tanggal_input, INTERVAL DAYOFMONTH(lw.tanggal_input) - 1 DAY), 1) + 1, "-",
                lw.unit_id, "-", 
                COALESCE(lw.created_by, "null"), "-", 
                lw.jenis_sampah
            )) as total')
            ->join('units u', 'u.id = lw.unit_id', 'left')
            ->join('users', 'users.id = lw.created_by', 'left')
            ->whereIn('lw.status', ['approved', 'rejected']);
        
        if (!empty($filters['start_date'])) {
            $builder->where('DATE(lw.tanggal_input) >=', $filters['start_date']);
        }
        
        if (!empty($filters['end_date'])) {
            $builder->where('DATE(lw.tanggal_input) <=', $filters['end_date']);
        }
        
        if (!empty($filters['unit_id'])) {
            $builder->where('lw.unit_id', $filters['unit_id']);
        }
        
        if (!empty($filters['jenis_sampah'])) {
            $builder->where('lw.jenis_sampah', $filters['jenis_sampah']);
        }
        
        // Filter khusus tabel rekap mingguan
        if (!empty($filters['filter_bulan'])) {
            $builder->where('MONTH(lw.tanggal_input)', $filters['filter_bulan']);
        }
        
        if (!empty($filters['filter_tahun'])) {
            $builder->where('YEAR(lw.tanggal_input)', $filters['filter_tahun']);
        }
        
        if (!empty($filters['filter_gedung'])) {
            $builder->like('u.nama_unit', $filters['filter_gedung']);
        }
        
        if (!empty($filters['filter_pelapor'])) {
            $builder->groupStart()
                    ->like('users.nama_lengkap', $filters['filter_pelapor'])
                    ->orLike('users.username', $filters['filter_pelapor'])
                    ->groupEnd();
        }
        
        $result = $builder->get()->getRow();
        return $result ? (int)$result->total : 0;
    }
}
