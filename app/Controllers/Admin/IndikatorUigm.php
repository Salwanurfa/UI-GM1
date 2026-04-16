<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UigmCategoryModel;
use App\Models\UigmEvidenceModel;

class IndikatorUigm extends BaseController
{
    protected $categoryModel;
    protected $evidenceModel;

    public function __construct()
    {
        $this->categoryModel = new UigmCategoryModel();
        $this->evidenceModel = new UigmEvidenceModel();
    }

    public function index()
    {
        $session = session();
        $user = $session->get('user');
        
        // Check authorization
        if (!$session->get('isLoggedIn') || !isset($user['role']) || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return redirect()->to('/auth/login')->with('error', 'Silakan login terlebih dahulu');
        }

        try {
            // Get filter parameters
            $tahunFilter = $this->request->getGet('tahun') ?: date('Y');
            
            // Get categories with progress
            $categories = $this->categoryModel->getCategoriesWithProgress($tahunFilter);
            
            // Get dashboard statistics
            $stats = $this->categoryModel->getDashboardStats($tahunFilter);

            // Get automatic calculations from waste logging data
            $calculationService = new \App\Services\UigmCalculationService();
            $wasteDataSummary = $calculationService->getWasteDataSummary((int)$tahunFilter);
            $b3DataSummary = $calculationService->getB3WasteDataSummary((int)$tahunFilter);
            $auditReadiness = $calculationService->getAuditReadinessStatus((int)$tahunFilter);

            // Generate year options (current year + 5 years ahead)
            $currentYear = date('Y');
            $yearOptions = [];
            for ($i = 0; $i <= 5; $i++) {
                $year = $currentYear + $i;
                $yearOptions[$year] = $year;
            }

            $data = [
                'title' => 'Dashboard Indikator UIGM',
                'categories' => $categories,
                'year_options' => $yearOptions,
                'current_year' => $currentYear,
                'selected_year' => $tahunFilter,
                'stats' => $stats,
                // New automatic calculation data
                'waste_data_summary' => $wasteDataSummary,
                'b3_data_summary' => $b3DataSummary,
                'audit_readiness' => $auditReadiness
            ];

            return view('admin_pusat/indikator_uigm/dashboard', $data);

        } catch (\Exception $e) {
            log_message('error', 'Indikator UIGM Dashboard Error: ' . $e->getMessage());
            
            return view('admin_pusat/indikator_uigm/dashboard', [
                'title' => 'Dashboard Indikator UIGM',
                'categories' => [],
                'year_options' => [date('Y') => date('Y')],
                'current_year' => date('Y'),
                'selected_year' => date('Y'),
                'stats' => [
                    'total_categories' => 0,
                    'avg_target' => 0,
                    'total_evidence' => 0,
                    'uploaded_evidence' => 0,
                    'pending_evidence' => 0,
                    'completion_rate' => 0
                ],
                'waste_data_summary' => [],
                'b3_data_summary' => [],
                'audit_readiness' => [],
                'error' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage()
            ]);
        }
    }

    public function category($kodeKategori)
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !isset($user['role']) || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return redirect()->to('/auth/login')->with('error', 'Silakan login terlebih dahulu');
        }

        try {
            $tahun = $this->request->getGet('tahun') ?: date('Y');
            
            // Get category data
            $category = $this->categoryModel->getCategoryByCode($kodeKategori, $tahun);
            
            if (!$category) {
                return redirect()->to('/admin-pusat/indikator-uigm')
                    ->with('error', 'Kategori tidak ditemukan');
            }

            // Get evidence list for this category
            $evidenceList = $this->evidenceModel->getEvidenceByCategory($category['id']);
            
            // Get category statistics
            $categoryStats = $this->evidenceModel->getCategoryStats($category['id']);

            $data = [
                'title' => $category['nama_kategori'],
                'category' => $category,
                'evidence_list' => $evidenceList,
                'category_stats' => $categoryStats,
                'tahun' => $tahun
            ];

            return view('admin_pusat/indikator_uigm/category', $data);

        } catch (\Exception $e) {
            log_message('error', 'Category View Error: ' . $e->getMessage());
            
            return redirect()->to('/admin-pusat/indikator-uigm')
                ->with('error', 'Terjadi kesalahan saat memuat kategori: ' . $e->getMessage());
        }
    }

    public function removeEvidence($evidenceId)
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !isset($user['role']) || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $evidence = $this->evidenceModel->find($evidenceId);
            if (!$evidence) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data bukti tidak ditemukan'
                ]);
            }

            // Delete file if exists
            if ($evidence['file_path'] && file_exists(WRITEPATH . $evidence['file_path'])) {
                unlink(WRITEPATH . $evidence['file_path']);
            }

            if ($this->evidenceModel->removeEvidence($evidenceId)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'File berhasil dihapus'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus file'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function updateTarget()
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !isset($user['role']) || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $categoryId = $this->request->getPost('category_id');
            $targetCapaian = $this->request->getPost('target_capaian');
            
            if (!$categoryId || !$targetCapaian) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data tidak lengkap'
                ]);
            }

            if ($this->categoryModel->updateTarget($categoryId, $targetCapaian)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Target berhasil diupdate'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal mengupdate target'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function export()
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !isset($user['role']) || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return redirect()->to('/admin-pusat/indikator-uigm')->with('error', 'Unauthorized');
        }

        try {
            $tahun = $this->request->getGet('tahun') ?: date('Y');
            $kategoriFilter = $this->request->getGet('kategori'); // For single category export
            
            // Load PhpSpreadsheet
            if (!file_exists(ROOTPATH . 'vendor/autoload.php')) {
                throw new \Exception('Composer autoload not found. Please run: composer install');
            }
            
            require_once ROOTPATH . 'vendor/autoload.php';
            
            if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
                throw new \Exception('PhpSpreadsheet not installed. Please run: composer require phpoffice/phpspreadsheet');
            }
            
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator('POLBAN Green Metric System')
                ->setTitle('Bukti Dukung Indikator UIGM ' . $tahun)
                ->setSubject('Laporan Indikator UI GreenMetric')
                ->setDescription('Laporan lengkap bukti dukung indikator UI GreenMetric tahun ' . $tahun);

            // Set sheet title
            $sheet->setTitle('Indikator UIGM ' . $tahun);

            // Create main header
            $headerTitle = $kategoriFilter ? 
                'LAPORAN BUKTI DUKUNG INDIKATOR ' . strtoupper($kategoriFilter) : 
                'LAPORAN BUKTI DUKUNG INDIKATOR UI GREENMETRIC';
            
            $sheet->setCellValue('A1', $headerTitle);
            $sheet->setCellValue('A2', 'POLITEKNIK NEGERI BANDUNG');
            $sheet->setCellValue('A3', 'TAHUN ' . $tahun);
            
            // Merge main header cells
            $sheet->mergeCells('A1:F1');
            $sheet->mergeCells('A2:F2');
            $sheet->mergeCells('A3:F3');
            
            // Style main header
            $mainHeaderStyle = [
                'font' => [
                    'bold' => true,
                    'size' => 16,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1e3c72']
                ]
            ];
            
            $subHeaderStyle = [
                'font' => [
                    'bold' => true,
                    'size' => 14,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2a5298']
                ]
            ];
            
            $sheet->getStyle('A1')->applyFromArray($mainHeaderStyle);
            $sheet->getStyle('A2')->applyFromArray($subHeaderStyle);
            $sheet->getStyle('A3')->applyFromArray($subHeaderStyle);
            
            // Set row heights for headers
            $sheet->getRowDimension(1)->setRowHeight(30);
            $sheet->getRowDimension(2)->setRowHeight(25);
            $sheet->getRowDimension(3)->setRowHeight(25);
            $sheet->getRowDimension(4)->setRowHeight(10); // Empty row
            
            // Create table headers
            $currentRow = 5;
            $headers = ['No', 'Kategori Indikator', 'Sub-Bab', 'Nama Bukti Dukung', 'Status', 'Link Dokumen'];
            
            foreach ($headers as $col => $header) {
                $cellAddress = chr(65 + $col) . $currentRow;
                $sheet->setCellValue($cellAddress, $header);
            }
            
            // Style table headers
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4a90e2']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ];
            
            $sheet->getStyle('A' . $currentRow . ':F' . $currentRow)->applyFromArray($headerStyle);
            $sheet->getRowDimension($currentRow)->setRowHeight(20);
            
            // Get all categories with evidence grouped by sub-category
            $categories = $this->categoryModel->getCategoriesWithProgress($tahun);
            
            // Filter by specific category if requested
            if ($kategoriFilter) {
                $categories = array_filter($categories, function($category) use ($kategoriFilter) {
                    return $category['kode_kategori'] === $kategoriFilter;
                });
            }
            
            $currentRow++;
            $no = 1;
            
            foreach ($categories as $category) {
                // Get evidence grouped by sub-category
                $evidenceList = $this->evidenceModel->getEvidenceByCategory($category['id']);
                
                if (empty($evidenceList)) {
                    continue;
                }
                
                $categoryStartRow = $currentRow;
                $categoryRowCount = 0;
                
                // Count total rows for this category
                foreach ($evidenceList as $subKategori => $evidenceItems) {
                    $categoryRowCount += count($evidenceItems);
                }
                
                // Add evidence data
                foreach ($evidenceList as $subKategori => $evidenceItems) {
                    $subCategoryStartRow = $currentRow;
                    
                    foreach ($evidenceItems as $evidence) {
                        // No
                        $sheet->setCellValue('A' . $currentRow, $no++);
                        
                        // Sub-Bab (only on first row of sub-category)
                        if ($currentRow == $subCategoryStartRow) {
                            $sheet->setCellValue('C' . $currentRow, $subKategori);
                        }
                        
                        // Nama Bukti Dukung
                        $sheet->setCellValue('D' . $currentRow, $evidence['nama_bukti']);
                        
                        // Status
                        $status = '';
                        $statusColor = 'CCCCCC';
                        switch ($evidence['status_upload']) {
                            case 'sudah_upload':
                                $status = 'Terupload';
                                $statusColor = '28a745';
                                break;
                            case 'perlu_revisi':
                                $status = 'Perlu Revisi';
                                $statusColor = 'ffc107';
                                break;
                            default:
                                $status = 'Belum Upload';
                                $statusColor = '6c757d';
                        }
                        
                        $sheet->setCellValue('E' . $currentRow, $status);
                        
                        // Link Dokumen
                        if ($evidence['status_upload'] === 'sudah_upload' && $evidence['file_path']) {
                            $fileUrl = base_url($evidence['file_path']);
                            $sheet->setCellValue('F' . $currentRow, $fileUrl);
                            
                            // Make it a hyperlink
                            $sheet->getCell('F' . $currentRow)->getHyperlink()->setUrl($fileUrl);
                            $sheet->getStyle('F' . $currentRow)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('0000FF'));
                            $sheet->getStyle('F' . $currentRow)->getFont()->setUnderline(true);
                        } else {
                            $sheet->setCellValue('F' . $currentRow, '-');
                        }
                        
                        // Apply row styling
                        $rowStyle = [
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                    'color' => ['rgb' => '000000']
                                ]
                            ],
                            'alignment' => [
                                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                                'wrapText' => true
                            ]
                        ];
                        
                        $sheet->getStyle('A' . $currentRow . ':F' . $currentRow)->applyFromArray($rowStyle);
                        
                        // Status cell color
                        $sheet->getStyle('E' . $currentRow)->getFill()
                            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()->setRGB($statusColor);
                        
                        if ($statusColor !== 'CCCCCC') {
                            $sheet->getStyle('E' . $currentRow)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
                        }
                        
                        $currentRow++;
                    }
                    
                    // Merge sub-category cells if more than one row
                    $subCategoryEndRow = $currentRow - 1;
                    if ($subCategoryEndRow > $subCategoryStartRow) {
                        $sheet->mergeCells('C' . $subCategoryStartRow . ':C' . $subCategoryEndRow);
                        $sheet->getStyle('C' . $subCategoryStartRow)->getAlignment()
                            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                        $sheet->getStyle('C' . $subCategoryStartRow)->getFill()
                            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('e3f2fd');
                    }
                }
                
                // Merge category cells
                $categoryEndRow = $currentRow - 1;
                if ($categoryEndRow >= $categoryStartRow) {
                    $sheet->setCellValue('B' . $categoryStartRow, $category['kode_kategori'] . ' - ' . $category['nama_kategori']);
                    $sheet->mergeCells('B' . $categoryStartRow . ':B' . $categoryEndRow);
                    $sheet->getStyle('B' . $categoryStartRow)->getAlignment()
                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    $sheet->getStyle('B' . $categoryStartRow)->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('f3e5f5');
                    $sheet->getStyle('B' . $categoryStartRow)->getFont()->setBold(true);
                }
            }
            
            // Auto-size columns
            foreach (range('A', 'F') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Set minimum column widths
            $sheet->getColumnDimension('A')->setWidth(5);   // No
            $sheet->getColumnDimension('B')->setWidth(25);  // Kategori
            $sheet->getColumnDimension('C')->setWidth(20);  // Sub-Bab
            $sheet->getColumnDimension('D')->setWidth(35);  // Nama Bukti
            $sheet->getColumnDimension('E')->setWidth(15);  // Status
            $sheet->getColumnDimension('F')->setWidth(40);  // Link
            
            // Create summary sheet
            $summarySheet = $spreadsheet->createSheet();
            $summarySheet->setTitle('Ringkasan');
            
            // Summary headers
            $summarySheet->setCellValue('A1', 'RINGKASAN INDIKATOR UIGM ' . $tahun);
            $summarySheet->mergeCells('A1:E1');
            $summarySheet->getStyle('A1')->applyFromArray($mainHeaderStyle);
            
            $summaryHeaders = ['Kode', 'Nama Kategori', 'Total Bukti', 'Terupload', 'Progress (%)'];
            foreach ($summaryHeaders as $col => $header) {
                $summarySheet->setCellValue(chr(65 + $col) . '3', $header);
            }
            $summarySheet->getStyle('A3:E3')->applyFromArray($headerStyle);
            
            // Summary data
            $summaryRow = 4;
            foreach ($categories as $category) {
                $progress = $category['total_evidence'] > 0 ? 
                    round(($category['uploaded_evidence'] / $category['total_evidence']) * 100, 1) : 0;
                
                $summarySheet->setCellValue('A' . $summaryRow, $category['kode_kategori']);
                $summarySheet->setCellValue('B' . $summaryRow, $category['nama_kategori']);
                $summarySheet->setCellValue('C' . $summaryRow, $category['total_evidence']);
                $summarySheet->setCellValue('D' . $summaryRow, $category['uploaded_evidence']);
                $summarySheet->setCellValue('E' . $summaryRow, $progress . '%');
                
                // Style summary row
                $summarySheet->getStyle('A' . $summaryRow . ':E' . $summaryRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]
                    ]
                ]);
                
                $summaryRow++;
            }
            
            // Auto-size summary columns
            foreach (range('A', 'E') as $col) {
                $summarySheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Set active sheet back to main sheet
            $spreadsheet->setActiveSheetIndex(0);
            
            // Generate filename
            $filename = $kategoriFilter ? 
                'Bukti_Dukung_' . $kategoriFilter . '_' . $tahun . '_' . date('Y-m-d_H-i-s') . '.xlsx' :
                'Bukti_Dukung_Indikator_UIGM_' . $tahun . '_' . date('Y-m-d_H-i-s') . '.xlsx';
            
            // Set headers for download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            // Write file
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            log_message('error', 'UIGM Export Error: ' . $e->getMessage());
            return redirect()->to('/admin-pusat/indikator-uigm')->with('error', 'Export error: ' . $e->getMessage());
        }
    }

    // Legacy methods for backward compatibility
    public function save() 
    { 
        return $this->response->setJSON([
            'success' => false, 
            'message' => 'Method deprecated. Please use the new category-based system.'
        ]); 
    }
    
    public function get($id) 
    { 
        return $this->response->setJSON([
            'success' => false, 
            'message' => 'Method deprecated. Please use the new category-based system.'
        ]); 
    }
    
    public function edit($id) 
    { 
        return $this->response->setJSON([
            'success' => false, 
            'message' => 'Method deprecated. Please use the new category-based system.'
        ]); 
    }
    
    public function delete($id) 
    { 
        return $this->response->setJSON([
            'success' => false, 
            'message' => 'Method deprecated. Please use the new category-based system.'
        ]); 
    }

    /**
     * Link waste log data to UIGM evidence
     */
    public function linkWasteData()
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $evidenceId = $this->request->getPost('evidence_id');
            $wasteLogIds = $this->request->getPost('waste_log_ids');

            if (!$evidenceId || !is_array($wasteLogIds)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Data tidak valid']);
            }

            $calculationService = new \App\Services\UigmCalculationService();
            $result = $calculationService->linkWasteDataToEvidence((int)$evidenceId, $wasteLogIds);

            if ($result) {
                return $this->response->setJSON([
                    'success' => true, 
                    'message' => 'Data sampah berhasil ditautkan ke bukti dukung'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => 'Gagal menautkan data sampah'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Link Waste Data Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Terjadi kesalahan sistem'
            ]);
        }
    }

    /**
     * Get waste data for linking
     */
    public function getWasteDataForLinking()
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $year = $this->request->getGet('year') ?: date('Y');
            $category = $this->request->getGet('category'); // WS.3, WS.5, etc.

            $db = \Config\Database::connect();
            
            $wasteData = [];
            
            // Get waste management data based on category
            if ($category === 'WS.3') {
                // Organic waste data
                $query = $db->query("
                    SELECT id, tanggal_input, jenis_sampah, kategori_spesifik, berat_kg, 
                           volume_input, volume_output, metode_pengolahan, sumber_sampah, status
                    FROM waste_management 
                    WHERE YEAR(tanggal_input) = ? 
                    AND kategori_spesifik IN ('Organik Basah', 'Organik Kering')
                    AND status IN ('disetujui_tps', 'disetujui')
                    ORDER BY tanggal_input DESC
                ", [$year]);
                
                $wasteData = $query->getResultArray();
            } elseif ($category === 'WS.5') {
                // B3 waste data
                $query = $db->query("
                    SELECT lb.id, lb.tanggal_input, mlb.nama_limbah, lb.timbulan, lb.satuan,
                           lb.volume_limbah, lb.metode_penanganan, lb.sumber_limbah, lb.status
                    FROM limbah_b3 lb
                    JOIN master_limbah_b3 mlb ON lb.master_b3_id = mlb.id
                    WHERE YEAR(lb.tanggal_input) = ?
                    AND lb.status IN ('disetujui_tps', 'disetujui_admin')
                    ORDER BY lb.tanggal_input DESC
                ", [$year]);
                
                $wasteData = $query->getResultArray();
            } else {
                // General waste data
                $query = $db->query("
                    SELECT id, tanggal_input, jenis_sampah, kategori_spesifik, berat_kg, 
                           metode_pengolahan, sumber_sampah, status
                    FROM waste_management 
                    WHERE YEAR(tanggal_input) = ?
                    AND status IN ('disetujui_tps', 'disetujui')
                    ORDER BY tanggal_input DESC
                ", [$year]);
                
                $wasteData = $query->getResultArray();
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $wasteData
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Get Waste Data For Linking Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Terjadi kesalahan sistem'
            ]);
        }
    }
    /**
     * Get standardized waste data for dashboard
     */
    public function getStandardizedData()
    {
        $session = session();
        $user = $session->get('user');

        if (!$session->get('isLoggedIn') || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $year = (int)($this->request->getGet('tahun') ?: date('Y'));

            // Use the new UIGM indicator mapping service
            $mappingService = new \App\Services\UIGMIndicatorMappingService();

            // Get all 7 UIGM indicators data
            $indicatorData = $mappingService->getUIGMIndicatorData($year);

            return $this->response->setJSON([
                'success' => true,
                'data' => $indicatorData,
                'year' => $year
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Get UIGM Indicator Data Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }



    /**
     * Get organic waste data (standardized)
     */
    private function getOrganikData($db, $year)
    {
        // Map old categories to standardized organic waste
        $organikCategories = ['Organik', 'Organik Basah', 'Organik Kering', 'Sisa Makanan', 'Dedaunan'];

        $query = $db->query("
            SELECT
                SUM(CASE WHEN satuan = 'kg' THEN jumlah
                         WHEN satuan = 'g' THEN jumlah/1000
                         WHEN satuan = 'ton' THEN jumlah*1000
                         ELSE 0 END) as total_kg,
                COUNT(DISTINCT unit_id) as sources,
                COUNT(DISTINCT CASE WHEN bukti_foto IS NOT NULL THEN id END) > 0 as has_evidence
            FROM waste_management
            WHERE YEAR(tanggal) = ?
            AND jenis_sampah IN ('" . implode("','", $organikCategories) . "')
            AND status IN ('disetujui', 'disetujui_tps')
        ", [$year]);

        $result = $query->getRowArray();

        return [
            'total_kg' => round($result['total_kg'] ?? 0, 2),
            'sources' => $result['sources'] ?? 0,
            'has_evidence' => (bool)($result['has_evidence'] ?? false)
        ];
    }

    /**
     * Get inorganic waste data (standardized)
     */
    private function getAnorganikData($db, $year)
    {
        // Map old categories to standardized inorganic waste
        $anorganikCategories = ['Plastik', 'Kertas', 'Logam', 'Kaca', 'Anorganik'];

        $query = $db->query("
            SELECT
                SUM(CASE WHEN satuan = 'kg' THEN jumlah
                         WHEN satuan = 'g' THEN jumlah/1000
                         WHEN satuan = 'ton' THEN jumlah*1000
                         ELSE 0 END) as total_kg,
                COUNT(DISTINCT unit_id) as sources,
                COUNT(DISTINCT CASE WHEN bukti_foto IS NOT NULL THEN id END) > 0 as has_evidence
            FROM waste_management
            WHERE YEAR(tanggal) = ?
            AND jenis_sampah IN ('" . implode("','", $anorganikCategories) . "')
            AND status IN ('disetujui', 'disetujui_tps')
        ", [$year]);

        $result = $query->getRowArray();

        return [
            'total_kg' => round($result['total_kg'] ?? 0, 2),
            'sources' => $result['sources'] ?? 0,
            'has_evidence' => (bool)($result['has_evidence'] ?? false)
        ];
    }

    /**
     * Get B3 waste data (standardized)
     */
    private function getB3Data($db, $year)
    {
        // Get B3 data from both waste_management and limbah_b3 tables
        $wasteQuery = $db->query("
            SELECT
                SUM(CASE WHEN satuan = 'kg' THEN jumlah
                         WHEN satuan = 'g' THEN jumlah/1000
                         WHEN satuan = 'ton' THEN jumlah*1000
                         ELSE 0 END) as total_kg,
                SUM(CASE WHEN satuan = 'L' THEN jumlah
                         WHEN satuan = 'ml' THEN jumlah/1000
                         ELSE 0 END) as total_l,
                COUNT(DISTINCT unit_id) as sources,
                COUNT(DISTINCT CASE WHEN bukti_foto IS NOT NULL THEN id END) > 0 as has_evidence
            FROM waste_management
            WHERE YEAR(tanggal) = ?
            AND jenis_sampah = 'B3'
            AND status IN ('disetujui', 'disetujui_tps')
        ", [$year]);

        $wasteResult = $wasteQuery->getRowArray();

        // Get B3 data from limbah_b3 table
        $b3Query = $db->query("
            SELECT
                SUM(CASE WHEN satuan = 'kg' THEN timbulan
                         WHEN satuan = 'g' THEN timbulan/1000
                         WHEN satuan = 'ton' THEN timbulan*1000
                         ELSE 0 END) as total_kg_b3,
                SUM(CASE WHEN satuan = 'L' THEN timbulan
                         WHEN satuan = 'ml' THEN timbulan/1000
                         ELSE 0 END) as total_l_b3,
                COUNT(DISTINCT user_id) as sources_b3,
                COUNT(DISTINCT CASE WHEN bukti_foto IS NOT NULL THEN id END) > 0 as has_evidence_b3
            FROM limbah_b3
            WHERE YEAR(tanggal_input) = ?
            AND status IN ('disetujui_admin', 'disetujui_tps')
        ", [$year]);

        $b3Result = $b3Query->getRowArray();

        return [
            'total_kg' => round(($wasteResult['total_kg'] ?? 0) + ($b3Result['total_kg_b3'] ?? 0), 2),
            'total_l' => round(($wasteResult['total_l'] ?? 0) + ($b3Result['total_l_b3'] ?? 0), 2),
            'sources' => ($wasteResult['sources'] ?? 0) + ($b3Result['sources_b3'] ?? 0),
            'has_evidence' => (bool)(($wasteResult['has_evidence'] ?? false) || ($b3Result['has_evidence_b3'] ?? false))
        ];
    }

    /**
     * Get liquid waste data (standardized)
     */
    private function getCairData($db, $year)
    {
        $query = $db->query("
            SELECT
                SUM(CASE WHEN satuan = 'L' THEN jumlah
                         WHEN satuan = 'ml' THEN jumlah/1000
                         WHEN satuan = 'm³' THEN jumlah*1000
                         ELSE 0 END) as total_l,
                COUNT(DISTINCT unit_id) as sources,
                COUNT(DISTINCT CASE WHEN bukti_foto IS NOT NULL THEN id END) > 0 as has_evidence
            FROM waste_management
            WHERE YEAR(tanggal) = ?
            AND jenis_sampah = 'Limbah Cair'
            AND status IN ('disetujui', 'disetujui_tps')
        ", [$year]);

        $result = $query->getRowArray();

        return [
            'total_l' => round($result['total_l'] ?? 0, 2),
            'sources' => $result['sources'] ?? 0,
            'has_evidence' => (bool)($result['has_evidence'] ?? false)
        ];
    }

    /**
     * Get residue waste data (standardized)
     */
    private function getResiduData($db, $year)
    {
        $query = $db->query("
            SELECT
                SUM(CASE WHEN satuan = 'kg' THEN jumlah
                         WHEN satuan = 'g' THEN jumlah/1000
                         WHEN satuan = 'ton' THEN jumlah*1000
                         ELSE 0 END) as total_kg,
                COUNT(DISTINCT unit_id) as sources,
                COUNT(DISTINCT CASE WHEN bukti_foto IS NOT NULL THEN id END) > 0 as has_evidence
            FROM waste_management
            WHERE YEAR(tanggal) = ?
            AND jenis_sampah = 'Residu'
            AND status IN ('disetujui', 'disetujui_tps')
        ", [$year]);

        $result = $query->getRowArray();

        return [
            'total_kg' => round($result['total_kg'] ?? 0, 2),
            'sources' => $result['sources'] ?? 0,
            'has_evidence' => (bool)($result['has_evidence'] ?? false)
        ];
    }

    /**
     * Calculate summary statistics
     */
    private function calculateSummaryStats($data)
    {
        $totalKg = ($data['organik']['total_kg'] ?? 0) +
                   ($data['anorganik']['total_kg'] ?? 0) +
                   ($data['b3']['total_kg'] ?? 0) +
                   ($data['residu']['total_kg'] ?? 0);

        $totalL = ($data['b3']['total_l'] ?? 0) + ($data['cair']['total_l'] ?? 0);

        $totalSources = array_sum(array_column($data, 'sources'));

        // Calculate recycle rate (organic + inorganic vs total solid waste)
        $recyclableKg = ($data['organik']['total_kg'] ?? 0) + ($data['anorganik']['total_kg'] ?? 0);
        $recycleRate = $totalKg > 0 ? round(($recyclableKg / $totalKg) * 100, 1) : 0;

        return [
            'total_kg' => round($totalKg, 2),
            'total_l' => round($totalL, 2),
            'total_sources' => $totalSources,
            'recycle_rate' => $recycleRate
        ];
    }

    /**
     * Show detailed view for specific waste category
     */
    public function categoryDetail($category)
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return redirect()->to('/auth/login')->with('error', 'Silakan login terlebih dahulu');
        }

        try {
            $year = (int)($this->request->getGet('tahun') ?: date('Y'));
            
            // Use standardization service
            $standardizationService = new \App\Services\WasteStandardizationService();
            
            // Get category data
            $categoryData = $standardizationService->getCategoryData($category, $year);
            
            // Get breakdown by source
            $breakdown = $standardizationService->getCategoryBreakdownBySource($category, $year);
            
            // Get category mapping for description
            $categoryMapping = $standardizationService->getCategoryMapping();
            $categoryDescription = $categoryMapping[$category]['description'] ?? '';

            $data = [
                'title' => 'Detail Kategori ' . ucfirst($category),
                'category' => $category,
                'category_description' => $categoryDescription,
                'summary' => $categoryData,
                'breakdown' => $breakdown,
                'year' => $year
            ];

            return view('admin_pusat/indikator_uigm/category_detail', $data);

        } catch (\Exception $e) {
            log_message('error', 'Category Detail Error: ' . $e->getMessage());
            return redirect()->to('/admin-pusat/indikator-uigm')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show detailed view for specific UIGM indicator
     */
    public function detail($indicatorKey)
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return redirect()->to('/auth/login')->with('error', 'Silakan login terlebih dahulu');
        }

        try {
            $year = (int)($this->request->getGet('tahun') ?: date('Y'));
            
            // Validate indicator key
            $validIndicators = ['indikator_1', 'indikator_2', 'indikator_3', 'indikator_4', 'indikator_5', 'indikator_6', 'indikator_7'];
            if (!in_array($indicatorKey, $validIndicators)) {
                return redirect()->to('/admin-pusat/indikator-uigm')->with('error', 'Indikator tidak valid: ' . $indicatorKey);
            }
            
            // Use UIGM mapping service
            $mappingService = new \App\Services\UIGMIndicatorMappingService();
            
            // Get indicator configuration
            $indicatorMapping = $mappingService->getIndicatorMapping();
            $indicatorConfig = $indicatorMapping[$indicatorKey] ?? null;
            
            if (!$indicatorConfig) {
                return redirect()->to('/admin-pusat/indikator-uigm')->with('error', 'Konfigurasi indikator tidak ditemukan');
            }
            
            // Get detailed data for this indicator
            $indicatorData = $mappingService->getIndicatorData($indicatorKey, $year);
            
            // Get detailed records for this indicator
            $detailedRecords = $this->getIndicatorDetailedRecords($indicatorKey, $year, $mappingService);
            
            // Get evidence files
            $evidenceFiles = $this->getIndicatorEvidenceFiles($indicatorKey, $year, $mappingService);

            $data = [
                'title' => 'Detail ' . $indicatorConfig['name'],
                'indicator_key' => $indicatorKey,
                'indicator_config' => $indicatorConfig,
                'indicator_data' => $indicatorData,
                'detailed_records' => $detailedRecords,
                'evidence_files' => $evidenceFiles,
                'year' => $year,
                'year_options' => $this->getYearOptions()
            ];

            return view('admin_pusat/indikator_uigm/detail', $data);

        } catch (\Exception $e) {
            log_message('error', 'UIGM Indicator Detail Error: ' . $e->getMessage());
            return redirect()->to('/admin-pusat/indikator-uigm')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get detailed records for specific indicator
     */
    private function getIndicatorDetailedRecords($indicatorKey, $year, $mappingService)
    {
        $db = \Config\Database::connect();
        
        // Get all records for the year
        $allRecords = $mappingService->getDetailedRecapData($year);
        
        // Filter records that belong to this indicator
        $indicatorRecords = array_filter($allRecords, function($record) use ($indicatorKey) {
            return $record['indikator_key'] === $indicatorKey;
        });
        
        return array_values($indicatorRecords);
    }

    /**
     * Get evidence files for specific indicator
     */
    private function getIndicatorEvidenceFiles($indicatorKey, $year, $mappingService)
    {
        $records = $this->getIndicatorDetailedRecords($indicatorKey, $year, $mappingService);
        
        $evidenceFiles = [];
        foreach ($records as $record) {
            if (!empty($record['bukti_foto'])) {
                $evidenceFiles[] = [
                    'file_path' => $record['bukti_foto'],
                    'nama_unit' => $record['nama_unit'],
                    'nama_user' => $record['nama_user'],
                    'jenis_sampah' => $record['jenis_sampah'],
                    'tanggal' => $record['tanggal'],
                    'volume_kg' => $record['volume_kg'],
                    'volume_l' => $record['volume_l']
                ];
            }
        }
        
        return $evidenceFiles;
    }

    /**
     * Get standardized categorized data for admin analysis
     */
    public function getStandardizedCategorizedData()
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $year = (int)($this->request->getGet('tahun') ?: date('Y'));
            
            // Use new standardization service
            $standardizationService = new \App\Services\WasteStandardizationService();
            $standardizedData = $standardizationService->getStandardizedWasteData($year);

            // Also get UIGM indicator mapping for cross-reference
            $mappingService = new \App\Services\UIGMIndicatorMappingService();
            
            // Add UIGM indicator mapping to standardized data
            foreach ($standardizedData as &$record) {
                $indicatorKey = $mappingService->mapToIndicator($record['jenis_sampah'], $record['nama_sampah_detail']);
                $indicatorMapping = $mappingService->getIndicatorMapping();
                $record['uigm_indicator'] = $indicatorMapping[$indicatorKey]['name'] ?? 'Unknown';
                $record['uigm_indicator_key'] = $indicatorKey;
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $standardizedData,
                'year' => $year,
                'total_records' => count($standardizedData),
                'categories' => $standardizationService->getAllStandardCategories()
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Get Standardized Categorized Data Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get category summary with standardized categories
     */
    public function getCategorySummary()
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $year = (int)($this->request->getGet('tahun') ?: date('Y'));
            
            $standardizationService = new \App\Services\WasteStandardizationService();
            $standardizedData = $standardizationService->getStandardizedWasteData($year);
            
            // Group by standard categories
            $categorySummary = [];
            $allCategories = $standardizationService->getAllStandardCategories();
            
            // Initialize all categories with zero values
            foreach ($allCategories as $categoryKey => $categoryInfo) {
                $categorySummary[$categoryKey] = [
                    'name' => $categoryInfo['name'],
                    'description' => $categoryInfo['description'],
                    'reference' => $categoryInfo['reference'],
                    'color' => $categoryInfo['color'],
                    'icon' => $categoryInfo['icon'],
                    'total_kg' => 0,
                    'total_l' => 0,
                    'total_records' => 0,
                    'sources' => [],
                    'has_evidence' => false
                ];
            }
            
            // Aggregate data by standard categories
            foreach ($standardizedData as $record) {
                $category = $record['standard_category'];
                
                if (isset($categorySummary[$category])) {
                    $categorySummary[$category]['total_kg'] += $record['volume_kg'];
                    $categorySummary[$category]['total_l'] += $record['volume_l'];
                    $categorySummary[$category]['total_records']++;
                    
                    if (!in_array($record['nama_unit'], $categorySummary[$category]['sources'])) {
                        $categorySummary[$category]['sources'][] = $record['nama_unit'];
                    }
                    
                    if (!empty($record['bukti_foto'])) {
                        $categorySummary[$category]['has_evidence'] = true;
                    }
                }
            }
            
            // Convert sources array to count
            foreach ($categorySummary as &$summary) {
                $summary['source_count'] = count($summary['sources']);
                unset($summary['sources']); // Remove array, keep only count
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $categorySummary,
                'year' => $year,
                'reference_info' => [
                    'ui_greenmetric' => 'UI GreenMetric Ranking System',
                    'regulation' => 'UU No. 18/2008 tentang Pengelolaan Sampah',
                    'ministry_guideline' => 'Peraturan Menteri LHK tentang Pengelolaan Limbah',
                    'iso_standard' => 'ISO 14001 Environmental Management'
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Get Category Summary Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get categorized waste data with grouping logic
     */
    private function getCategorizedWasteData($year, $mappingService)
    {
        $db = \Config\Database::connect();
        
        // Check what columns exist in the users table for safe querying
        $columnsQuery = $db->query("SHOW COLUMNS FROM users");
        $columns = array_column($columnsQuery->getResultArray(), 'Field');
        
        // Build the user name selection based on available columns
        $userNameSelect = 'usr.username'; // fallback
        if (in_array('nama_lengkap', $columns)) {
            $userNameSelect = 'COALESCE(usr.nama_lengkap, usr.username, \'Unknown User\')';
        }
        if (in_array('full_name', $columns)) {
            $userNameSelect = 'COALESCE(usr.nama_lengkap, usr.full_name, usr.username, \'Unknown User\')';
        }

        // Query for categorized data with proper grouping
        $query = $db->query("
            SELECT 
                wm.id,
                COALESCE(u.nama_unit, 'Unknown Unit') as nama_penginput,
                wm.jenis_sampah,
                COALESCE(wm.nama_sampah, wm.jenis_sampah) as nama_sampah_detail,
                wm.jumlah,
                wm.satuan,
                CASE 
                    WHEN wm.satuan = 'kg' THEN wm.jumlah 
                    WHEN wm.satuan = 'g' THEN wm.jumlah/1000 
                    WHEN wm.satuan = 'ton' THEN wm.jumlah*1000 
                    ELSE 0 
                END as volume_kg,
                CASE 
                    WHEN wm.satuan = 'L' THEN wm.jumlah 
                    WHEN wm.satuan = 'ml' THEN wm.jumlah/1000 
                    ELSE 0 
                END as volume_l,
                wm.tanggal,
                wm.status,
                COALESCE(wm.gedung, 'Unknown') as gedung,
                {$userNameSelect} as nama_user,
                usr.role as user_role,
                CASE 
                    WHEN usr.role = 'pengelola_tps' THEN 'TPS'
                    WHEN usr.role = 'user' THEN 'USER'
                    WHEN usr.role = 'admin_unit' THEN 'USER'
                    WHEN usr.role = 'admin_pusat' THEN 'ADMIN PUSAT'
                    WHEN usr.role = 'super_admin' THEN 'ADMIN PUSAT'
                    ELSE 'USER'
                END as asal_data
            FROM waste_management wm
            LEFT JOIN unit u ON wm.unit_id = u.id
            INNER JOIN users usr ON wm.user_id = usr.id
            WHERE YEAR(wm.tanggal) = ?
            AND wm.status IN ('disetujui', 'disetujui_tps', 'approved')
            ORDER BY wm.tanggal DESC, COALESCE(u.nama_unit, 'Unknown Unit') ASC
        ", [$year]);

        $rawData = $query->getResultArray();
        
        // Process and categorize each record
        $categorizedData = [];
        foreach ($rawData as $record) {
            // Map to UIGM indicator category
            $indicatorKey = $mappingService->mapToIndicator($record['jenis_sampah'], $record['nama_sampah_detail']);
            $indicatorMapping = $mappingService->getIndicatorMapping();
            $indicatorName = $indicatorMapping[$indicatorKey]['name'] ?? 'Unknown';
            
            // Determine main waste category
            $mainCategory = $this->determineMainCategory($record['jenis_sampah'], $record['nama_sampah_detail']);
            
            $categorizedData[] = [
                'id' => $record['id'],
                'nama_penginput' => $record['nama_penginput'],
                'nama_user' => $record['nama_user'],
                'jenis_sampah' => $record['jenis_sampah'],
                'nama_sampah_detail' => $record['nama_sampah_detail'],
                'kategori_utama' => $mainCategory,
                'kategori_indikator' => $indicatorName,
                'indikator_key' => $indicatorKey,
                'volume_kg' => $record['volume_kg'],
                'volume_l' => $record['volume_l'],
                'jumlah_display' => $this->formatVolume($record['volume_kg'], $record['volume_l']),
                'waktu_input' => $record['tanggal'],
                'asal_data' => $record['asal_data'],
                'gedung' => $record['gedung'],
                'status' => $record['status']
            ];
        }
        
        return $categorizedData;
    }

    /**
     * Determine main waste category (Organik, Anorganik, B3, Cair, Residu)
     */
    private function determineMainCategory($jenisSampah, $namaSampah = '')
    {
        $jenisSampah = strtolower(trim($jenisSampah));
        $namaSampah = strtolower(trim($namaSampah));
        $combinedText = $jenisSampah . ' ' . $namaSampah;

        // B3 (Bahan Berbahaya & Beracun)
        if (strpos($combinedText, 'b3') !== false || 
            strpos($combinedText, 'oli') !== false || 
            strpos($combinedText, 'baterai') !== false || 
            strpos($combinedText, 'medis') !== false || 
            strpos($combinedText, 'kimia') !== false || 
            strpos($combinedText, 'beracun') !== false ||
            strpos($combinedText, 'berbahaya') !== false ||
            strpos($combinedText, 'neon') !== false ||
            strpos($combinedText, 'aki') !== false) {
            return 'B3';
        }

        // Limbah Cair
        if (strpos($combinedText, 'cair') !== false || 
            strpos($combinedText, 'air') !== false || 
            strpos($combinedText, 'liquid') !== false ||
            strpos($combinedText, 'cairan') !== false) {
            return 'Cair';
        }

        // Organik
        if (strpos($combinedText, 'organik') !== false || 
            strpos($combinedText, 'makanan') !== false || 
            strpos($combinedText, 'sisa') !== false || 
            strpos($combinedText, 'dedaunan') !== false || 
            strpos($combinedText, 'taman') !== false || 
            strpos($combinedText, 'kompos') !== false ||
            strpos($combinedText, 'dapur') !== false) {
            return 'Organik';
        }

        // Anorganik (includes recyclables)
        if (strpos($combinedText, 'anorganik') !== false || 
            strpos($combinedText, 'plastik') !== false || 
            strpos($combinedText, 'kertas') !== false || 
            strpos($combinedText, 'logam') !== false || 
            strpos($combinedText, 'kaca') !== false || 
            strpos($combinedText, 'kaleng') !== false ||
            strpos($combinedText, 'aluminium') !== false ||
            strpos($combinedText, 'kardus') !== false ||
            strpos($combinedText, 'botol') !== false) {
            return 'Anorganik';
        }

        // Default to Residu (waste that cannot be recycled)
        return 'Residu';
    }

    /**
     * Format volume display
     */
    private function formatVolume($volumeKg, $volumeL)
    {
        if ($volumeKg > 0) {
            return number_format($volumeKg, 2) . ' kg';
        } elseif ($volumeL > 0) {
            return number_format($volumeL, 2) . ' L';
        }
        return '0 kg';
    }

    /**
     * Get available year options for filtering
     */
    private function getYearOptions()
    {
        $currentYear = date('Y');
        $years = [];
        
        // Get years from database
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT DISTINCT YEAR(tanggal) as year 
            FROM waste_management 
            WHERE tanggal IS NOT NULL 
            ORDER BY year DESC
        ");
        
        $dbYears = $query->getResultArray();
        
        if (!empty($dbYears)) {
            foreach ($dbYears as $row) {
                $years[$row['year']] = $row['year'];
            }
        } else {
            // Fallback to current year if no data
            $years[$currentYear] = $currentYear;
        }
        
        return $years;
    }

    /**
     * Get UIGM indicator data with 7 indicators mapping
     */
    public function getUIGMIndicatorData()
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $year = (int)($this->request->getGet('tahun') ?: date('Y'));
            
            // Use the new UIGM indicator mapping service
            $mappingService = new \App\Services\UIGMIndicatorMappingService();
            
            // Get all 7 UIGM indicators data
            $indicatorData = $mappingService->getUIGMIndicatorData($year);

            return $this->response->setJSON([
                'success' => true,
                'data' => $indicatorData,
                'year' => $year
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Get UIGM Indicator Data Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get detailed recap data for admin table
     */
    public function getDetailedRecapData()
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $year = (int)($this->request->getGet('tahun') ?: date('Y'));
            
            // Use the mapping service for detailed data
            $mappingService = new \App\Services\UIGMIndicatorMappingService();
            
            // Get detailed recap data
            $recapData = $mappingService->getDetailedRecapData($year);

            return $this->response->setJSON([
                'success' => true,
                'data' => $recapData,
                'year' => $year,
                'total_records' => count($recapData)
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Get Detailed Recap Data Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Debug method to check database connection and data
     */
    public function debugData()
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $year = (int)($this->request->getGet('tahun') ?: date('Y'));
            $db = \Config\Database::connect();
            
            // Test basic query
            $testQuery = $db->query("
                SELECT COUNT(*) as total_records,
                       COUNT(CASE WHEN status IN ('disetujui', 'disetujui_tps', 'approved') THEN 1 END) as approved_records,
                       GROUP_CONCAT(DISTINCT jenis_sampah) as jenis_sampah_list,
                       GROUP_CONCAT(DISTINCT status) as status_list
                FROM waste_management 
                WHERE YEAR(tanggal) = ?
            ", [$year]);
            
            $testResult = $testQuery->getRowArray();
            
            // Test table structure
            $structureQuery = $db->query("DESCRIBE waste_management");
            $structure = $structureQuery->getResultArray();
            
            // Test users table structure
            $usersStructureQuery = $db->query("DESCRIBE users");
            $usersStructure = $usersStructureQuery->getResultArray();
            
            // Test sample data with safer column selection
            $sampleQuery = $db->query("
                SELECT wm.jenis_sampah, wm.nama_sampah, wm.status, u.nama_unit, usr.nama_lengkap, usr.username
                FROM waste_management wm
                LEFT JOIN unit u ON wm.unit_id = u.id
                LEFT JOIN users usr ON wm.user_id = usr.id
                WHERE YEAR(wm.tanggal) = ?
                LIMIT 5
            ", [$year]);
            
            $sampleData = $sampleQuery->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'year' => $year,
                'test_result' => $testResult,
                'waste_table_structure' => array_column($structure, 'Field'),
                'users_table_structure' => array_column($usersStructure, 'Field'),
                'sample_data' => $sampleData,
                'message' => 'Debug data retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Debug Error: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }


    /**
     * Upload evidence for waste data
     */
    public function uploadEvidence()
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $recordId = $this->request->getPost('record_id');
            $sourceTable = $this->request->getPost('source_table') ?: 'waste_management';
            $description = $this->request->getPost('description') ?: '';
            $file = $this->request->getFile('evidence_file');
            
            if (!$recordId || !$file || !$file->isValid()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data tidak lengkap atau file tidak valid'
                ]);
            }

            // Validate file
            $validationResult = $this->validateEvidenceFile($file);
            if (!$validationResult['valid']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $validationResult['message']
                ]);
            }

            // Create upload directory
            $uploadPath = ROOTPATH . 'public/uploads/bukti_uigm/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Generate unique filename
            $extension = $file->getClientExtension();
            $fileName = 'evidence_' . $sourceTable . '_' . $recordId . '_' . time() . '.' . $extension;
            
            // Move file
            if ($file->move($uploadPath, $fileName)) {
                // Update database
                $db = \Config\Database::connect();
                $relativePath = 'uploads/bukti_uigm/' . $fileName;
                
                if ($sourceTable === 'limbah_b3') {
                    $updateResult = $db->query("
                        UPDATE limbah_b3 
                        SET bukti_foto = ?, 
                            keterangan_bukti = ?,
                            updated_at = NOW()
                        WHERE id = ?
                    ", [$relativePath, $description, $recordId]);
                } else {
                    $updateResult = $db->query("
                        UPDATE waste_management 
                        SET bukti_foto = ?, 
                            keterangan_bukti = ?,
                            updated_at = NOW()
                        WHERE id = ?
                    ", [$relativePath, $description, $recordId]);
                }

                if ($updateResult) {
                    // Log the upload activity
                    $this->logEvidenceUpload($recordId, $sourceTable, $fileName, $user['id']);
                    
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Bukti dukung berhasil diunggah',
                        'file_name' => $file->getClientName(),
                        'file_path' => $relativePath,
                        'file_size' => $this->formatFileSize($file->getSize())
                    ]);
                } else {
                    // Delete uploaded file if database update fails
                    if (file_exists($uploadPath . $fileName)) {
                        unlink($uploadPath . $fileName);
                    }
                    
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Gagal menyimpan informasi file ke database'
                    ]);
                }
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal mengunggah file ke server'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Evidence Upload Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Validate evidence file
     */
    private function validateEvidenceFile($file): array
    {
        // Check file size (5MB max)
        $maxSize = 5 * 1024 * 1024; // 5MB in bytes
        if ($file->getSize() > $maxSize) {
            return [
                'valid' => false,
                'message' => 'Ukuran file terlalu besar. Maksimal 5 MB.'
            ];
        }

        // Check file type
        $allowedTypes = [
            'image/jpeg', 'image/jpg', 'image/png',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
        
        if (!in_array($file->getMimeType(), $allowedTypes) || 
            !in_array(strtolower($file->getClientExtension()), $allowedExtensions)) {
            return [
                'valid' => false,
                'message' => 'Format file tidak didukung. Gunakan JPG, PNG, PDF, DOC, atau DOCX.'
            ];
        }

        // Check if file is actually uploaded
        if (!$file->isValid()) {
            return [
                'valid' => false,
                'message' => 'File tidak valid atau rusak.'
            ];
        }

        return ['valid' => true];
    }

    /**
     * Log evidence upload activity
     */
    private function logEvidenceUpload(int $recordId, string $sourceTable, string $fileName, int $userId): void
    {
        try {
            $db = \Config\Database::connect();
            
            $db->query("
                INSERT INTO evidence_upload_log (
                    record_id, 
                    source_table, 
                    file_name, 
                    uploaded_by, 
                    upload_date
                ) VALUES (?, ?, ?, ?, NOW())
            ", [$recordId, $sourceTable, $fileName, $userId]);
            
        } catch (\Exception $e) {
            log_message('error', 'Failed to log evidence upload: ' . $e->getMessage());
        }
    }

    /**
     * Format file size for display
     */
    private function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function checkOrphanedData()
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $db = \Config\Database::connect();
            
            // Check orphaned waste_management records
            $orphanedWaste = $db->query("
                SELECT wm.id, wm.user_id, wm.jenis_sampah, wm.jumlah, wm.tanggal 
                FROM waste_management wm 
                LEFT JOIN users u ON wm.user_id = u.id 
                WHERE u.id IS NULL
            ")->getResultArray();
            
            // Check orphaned limbah_b3 records
            $orphanedB3 = [];
            try {
                $orphanedB3 = $db->query("
                    SELECT lb.id, lb.user_id, lb.timbulan, lb.tanggal_input 
                    FROM limbah_b3 lb 
                    LEFT JOIN users u ON lb.user_id = u.id 
                    WHERE u.id IS NULL
                ")->getResultArray();
            } catch (\Exception $e) {
                // Table might not exist
            }
            
            // Get total valid records
            $validWaste = $db->query("
                SELECT COUNT(*) as count 
                FROM waste_management wm 
                INNER JOIN users u ON wm.user_id = u.id
            ")->getRow()->count;
            
            $validB3 = 0;
            try {
                $validB3 = $db->query("
                    SELECT COUNT(*) as count 
                    FROM limbah_b3 lb 
                    INNER JOIN users u ON lb.user_id = u.id
                ")->getRow()->count;
            } catch (\Exception $e) {
                // Table might not exist
            }

            return $this->response->setJSON([
                'success' => true,
                'orphaned_waste' => $orphanedWaste,
                'orphaned_b3' => $orphanedB3,
                'valid_waste_count' => $validWaste,
                'valid_b3_count' => $validB3,
                'total_orphaned' => count($orphanedWaste) + count($orphanedB3)
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Check Orphaned Data Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }

    public function cleanOrphanedData()
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $db = \Config\Database::connect();
            
            // Delete orphaned waste_management records
            $deletedWaste = $db->query("
                DELETE wm FROM waste_management wm 
                LEFT JOIN users u ON wm.user_id = u.id 
                WHERE u.id IS NULL
            ");
            
            $wasteAffected = $db->affectedRows();
            
            // Delete orphaned limbah_b3 records
            $b3Affected = 0;
            try {
                $deletedB3 = $db->query("
                    DELETE lb FROM limbah_b3 lb 
                    LEFT JOIN users u ON lb.user_id = u.id 
                    WHERE u.id IS NULL
                ");
                $b3Affected = $db->affectedRows();
            } catch (\Exception $e) {
                // Table might not exist
            }

            return $this->response->setJSON([
                'success' => true,
                'deleted_waste_records' => $wasteAffected,
                'deleted_b3_records' => $b3Affected,
                'total_deleted' => $wasteAffected + $b3Affected,
                'message' => "Successfully deleted {$wasteAffected} orphaned waste records and {$b3Affected} orphaned B3 records"
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Clean Orphaned Data Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }
}