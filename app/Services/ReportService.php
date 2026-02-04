<?php

namespace App\Services;

use App\Models\WasteReportModel;
use App\Models\MasterHargaSampahModel;
use App\Models\ChangeLogModel;

class ReportService
{
    protected $wasteReportModel;
    protected $masterHargaModel;
    protected $changeLogModel;

    public function __construct()
    {
        $this->wasteReportModel = new WasteReportModel();
        $this->masterHargaModel = new MasterHargaSampahModel();
        $this->changeLogModel = new ChangeLogModel();
    }

    /**
     * Get rekap sampah with filters and pagination
     * 
     * @param array $filters
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getRekapSampah(array $filters, int $page = 1, int $perPage = 5): array
    {
        try {
            // Normalize and validate filters
            $filters = $this->normalizeFilters($filters);

            // Validate date range
            if (!empty($filters['from']) && !empty($filters['to'])) {
                if (strtotime($filters['from']) > strtotime($filters['to'])) {
                    throw new \Exception('Tanggal mulai tidak boleh lebih besar dari tanggal akhir');
                }
            }

            // Validate page
            if ($page < 1) {
                $page = 1;
            }

            // Force perPage to 5
            $perPage = 5;

            // Calculate offset
            $offset = ($page - 1) * $perPage;

            // Get data from model
            $result = $this->wasteReportModel->getRekapSampahByFilter($filters, $perPage, $offset);

            // Format rows
            $formattedRows = array_map(function($row) {
                return [
                    'id' => $row['id'],
                    'created_at' => $row['created_at'],
                    'confirmed_at' => $row['confirmed_at'] ?? null,
                    'tanggal' => $row['tanggal'],
                    'user_name' => $row['user_name'] ?? $row['nama_lengkap'] ?? 'N/A',
                    'gedung_name' => $row['gedung_name'] ?? $row['nama_unit'] ?? 'N/A',
                    'nama_sampah' => $row['nama_sampah'] ?? $row['jenis_sampah'] ?? 'N/A',
                    'jumlah' => (float)($row['jumlah'] ?? 0),
                    'satuan' => $row['satuan'] ?? 'kg',
                    'total_harga' => (float)($row['nilai_rupiah'] ?? 0),
                    'status' => $row['status'] ?? 'draft'
                ];
            }, $result['rows']);

            return [
                'rows' => $formattedRows,
                'total' => $result['total'],
                'page' => $page,
                'perPage' => $perPage,
                'totalPages' => ceil($result['total'] / $perPage),
                'aggregates' => $result['aggregates']
            ];

        } catch (\Exception $e) {
            log_message('error', 'ReportService::getRekapSampah error: ' . $e->getMessage());
            
            return [
                'rows' => [],
                'total' => 0,
                'page' => $page,
                'perPage' => $perPage,
                'totalPages' => 0,
                'aggregates' => [
                    'sum_jumlah' => 0,
                    'sum_total_harga' => 0,
                    'total_records' => 0
                ],
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get rekap per unit with filters and pagination
     * 
     * @param array $filters
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getRekapUnit(array $filters, int $page = 1, int $perPage = 5): array
    {
        try {
            // Normalize and validate filters
            $filters = $this->normalizeFilters($filters);

            // Validate date range
            if (!empty($filters['from']) && !empty($filters['to'])) {
                if (strtotime($filters['from']) > strtotime($filters['to'])) {
                    throw new \Exception('Tanggal mulai tidak boleh lebih besar dari tanggal akhir');
                }
            }

            // Validate page
            if ($page < 1) {
                $page = 1;
            }

            // Force perPage to 5
            $perPage = 5;

            // Calculate offset
            $offset = ($page - 1) * $perPage;

            // Get data from model
            $result = $this->wasteReportModel->getRekapUnitByFilter($filters, $perPage, $offset);

            // Format rows
            $formattedRows = array_map(function($row) {
                return [
                    'unit_id' => $row['unit_id'],
                    'nama_unit' => $row['nama_unit'] ?? 'N/A',
                    'total_laporan' => (int)($row['total_laporan'] ?? 0),
                    'total_jumlah' => (float)($row['total_jumlah'] ?? 0),
                    'total_nilai' => (float)($row['total_nilai'] ?? 0),
                    'first_report' => $row['first_report'] ?? null,
                    'last_report' => $row['last_report'] ?? null
                ];
            }, $result['rows']);

            return [
                'rows' => $formattedRows,
                'total' => $result['total'],
                'page' => $page,
                'perPage' => $perPage,
                'totalPages' => ceil($result['total'] / $perPage),
                'aggregates' => $result['aggregates']
            ];

        } catch (\Exception $e) {
            log_message('error', 'ReportService::getRekapUnit error: ' . $e->getMessage());
            
            return [
                'rows' => [],
                'total' => 0,
                'page' => $page,
                'perPage' => $perPage,
                'totalPages' => 0,
                'aggregates' => [
                    'total_units' => 0,
                    'sum_jumlah' => 0,
                    'sum_total_harga' => 0,
                    'total_records' => 0
                ],
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Confirm report (set status to confirmed and log audit)
     * 
     * @param int $reportId
     * @param int $adminId
     * @param string $adminName
     * @return array
     */
    public function confirmReport(int $reportId, int $adminId, string $adminName): array
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Get report detail
            $report = $this->wasteReportModel->getReportDetail($reportId);

            if (!$report) {
                throw new \Exception('Laporan tidak ditemukan');
            }

            // Validate required fields
            if (empty($report['user_name'])) {
                throw new \Exception('Nama pelapor tidak ditemukan. Data tidak valid.');
            }

            if (empty($report['gedung_name']) && empty($report['gedung'])) {
                throw new \Exception('Nama gedung tidak ditemukan. Data tidak valid.');
            }

            // Check if already confirmed
            if ($report['status'] === 'disetujui' && !empty($report['confirmed_at'])) {
                throw new \Exception('Laporan sudah dikonfirmasi sebelumnya');
            }

            // Update report status
            $updateData = [
                'status' => 'disetujui',
                'confirmed_at' => date('Y-m-d H:i:s'),
                'admin_reviewed_by' => $adminId,
                'admin_reviewed_at' => date('Y-m-d H:i:s')
            ];

            $updated = $this->wasteReportModel->update($reportId, $updateData);

            if (!$updated) {
                throw new \Exception('Gagal mengupdate status laporan');
            }

            // Log to change_logs
            $logData = [
                'user_id' => $adminId,
                'user_name' => $adminName,
                'action' => 'update',
                'entity' => 'waste_report',
                'entity_id' => $reportId,
                'summary' => "Mengkonfirmasi laporan sampah ID #{$reportId} dari {$report['user_name']}",
                'old_value' => json_encode(['status' => $report['status']]),
                'new_value' => json_encode(['status' => 'disetujui', 'confirmed_at' => $updateData['confirmed_at']]),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->changeLogModel->insertLog($logData);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal');
            }

            log_message('info', "Report #{$reportId} confirmed by admin #{$adminId}");

            return [
                'success' => true,
                'message' => 'Laporan berhasil dikonfirmasi',
                'data' => [
                    'report_id' => $reportId,
                    'confirmed_at' => $updateData['confirmed_at']
                ]
            ];

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'ReportService::confirmReport error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get all nama sampah for dropdown
     * 
     * @return array
     */
    public function getAllNamaSampah(): array
    {
        try {
            // Get from master harga first (authoritative source)
            $masterList = $this->masterHargaModel->getAllHargaAktif();
            
            $namaSampahList = [];
            foreach ($masterList as $item) {
                $namaSampahList[] = [
                    'id' => $item['id'],
                    'nama' => $item['nama_jenis'],
                    'jenis' => $item['jenis_sampah']
                ];
            }

            // Also get distinct from waste_management for any custom entries
            $distinctList = $this->wasteReportModel->getAllNamaSampah();
            foreach ($distinctList as $item) {
                if (!empty($item['nama_sampah'])) {
                    // Check if not already in list
                    $exists = false;
                    foreach ($namaSampahList as $existing) {
                        if ($existing['nama'] === $item['nama_sampah']) {
                            $exists = true;
                            break;
                        }
                    }
                    
                    if (!$exists) {
                        $namaSampahList[] = [
                            'id' => null,
                            'nama' => $item['nama_sampah'],
                            'jenis' => null
                        ];
                    }
                }
            }

            return $namaSampahList;

        } catch (\Exception $e) {
            log_message('error', 'ReportService::getAllNamaSampah error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Normalize and sanitize filters
     * 
     * @param array $filters
     * @return array
     */
    private function normalizeFilters(array $filters): array
    {
        $normalized = [];

        // Date filters
        if (!empty($filters['from'])) {
            $normalized['from'] = date('Y-m-d', strtotime(trim($filters['from'])));
        }

        if (!empty($filters['to'])) {
            $normalized['to'] = date('Y-m-d', strtotime(trim($filters['to'])));
        }

        // Nama sampah filter
        if (!empty($filters['nama_sampah'])) {
            $normalized['nama_sampah'] = trim($filters['nama_sampah']);
        }

        // Unit ID filter
        if (!empty($filters['unit_id'])) {
            $normalized['unit_id'] = (int)$filters['unit_id'];
        }

        // Search filter
        if (!empty($filters['search'])) {
            $normalized['search'] = trim($filters['search']);
        }

        return $normalized;
    }
}
