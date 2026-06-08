<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class LogBook extends BaseController
{
    public function index()
    {
        // Check if user is logged in and has admin role
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        $user = session()->get('user');
        if (!in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return redirect()->to('/auth/login');
        }

        $db = \Config\Database::connect();
        
        // ========================================
        // LOGIKA BARU: TAMPILKAN SETIAP INPUT SEBAGAI BARIS TERPISAH
        // TIDAK ADA GROUPING - Setiap transaksi = 1 baris
        // ========================================
        
        // Ambil data Program 3R - HANYA HARI INI (SETIAP INPUT = 1 BARIS)
        try {
            $data['riwayat_3r'] = $db->table('waste_management wm')
                ->select('
                    wm.id,
                    wm.tanggal,
                    wm.jenis_sampah,
                    wm.nama_sampah,
                    wm.satuan,
                    wm.berat_kg,
                    wm.nilai_rupiah,
                    wm.status,
                    wm.created_at,
                    u.nama_lengkap as nama_user,
                    un.nama_unit
                ')
                ->join('users u', 'wm.user_id = u.id', 'left')
                ->join('unit un', 'wm.unit_id = un.id', 'left')
                ->where('DATE(wm.tanggal) = CURDATE()', null, false)  // FILTER HARI INI
                ->orderBy('wm.created_at', 'DESC')  // URUT BERDASARKAN WAKTU INPUT TERBARU
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error fetching waste_management summary: ' . $e->getMessage());
            $data['riwayat_3r'] = [];
        }

        // Ambil data Limbah B3 - HANYA HARI INI (SETIAP INPUT = 1 BARIS)
        try {
            $data['riwayat_b3'] = $db->table('limbah_b3 lb')
                ->select('
                    lb.id,
                    lb.tanggal_input,
                    mlb.nama_limbah,
                    mlb.kode_limbah,
                    mlb.kategori_bahaya,
                    lb.satuan,
                    lb.timbulan,
                    lb.status,
                    lb.lokasi,
                    lb.created_at,
                    u.nama_lengkap as nama_user,
                    un.nama_unit
                ')
                ->join('master_limbah_b3 mlb', 'lb.master_b3_id = mlb.id', 'left')
                ->join('users u', 'lb.id_user = u.id', 'left')
                ->join('unit un', 'u.unit_id = un.id', 'left')
                ->where('DATE(lb.tanggal_input) = CURDATE()', null, false)  // FILTER HARI INI
                ->orderBy('lb.created_at', 'DESC')  // URUT BERDASARKAN WAKTU INPUT TERBARU
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error fetching limbah_b3 summary: ' . $e->getMessage());
            $data['riwayat_b3'] = [];
        }

        // Ambil data Limbah Cair - HANYA HARI INI (SETIAP INPUT = 1 BARIS)
        try {
            $data['riwayat_cair'] = $db->table('limbah_cair lc')
                ->select('
                    lc.id,
                    lc.tanggal_input,
                    lc.nama_limbah,
                    lc.kode_limbah,
                    lc.satuan,
                    lc.timbulan,
                    lc.status,
                    lc.lokasi,
                    lc.ph,
                    lc.bod,
                    lc.cod,
                    lc.tss,
                    lc.created_at,
                    u.nama_lengkap as nama_user,
                    un.nama_unit
                ')
                ->join('users u', 'lc.id_user = u.id', 'left')
                ->join('unit un', 'u.unit_id = un.id', 'left')
                ->where('DATE(lc.tanggal_input) = CURDATE()', null, false)  // FILTER HARI INI
                ->orderBy('lc.created_at', 'DESC')  // URUT BERDASARKAN WAKTU INPUT TERBARU
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error fetching limbah_cair summary: ' . $e->getMessage());
            $data['riwayat_cair'] = [];
        }
        
        // Tambahkan informasi tanggal hari ini
        $data['tanggal_hari_ini'] = date('d/m/Y');
        $data['waktu_update'] = date('d/m/Y H:i:s');
        
        // TAMBAHAN: Hitung total data dengan status DIKIRIM_KE_TPS untuk hari ini
        // Ini untuk mencegah error "Undefined variable $totalDikirim" di View
        try {
            // Hitung Program 3R yang DIKIRIM_KE_TPS
            $count3r = $db->table('waste_management')
                ->where('DATE(tanggal) = CURDATE()', null, false)
                ->whereIn('status', ['dikirim_ke_tps', 'dikirim', 'disetujui', 'disetujui_admin'])
                ->countAllResults();
            
            // Hitung Limbah B3 yang DIKIRIM_KE_TPS
            $countB3 = $db->table('limbah_b3')
                ->where('DATE(tanggal_input) = CURDATE()', null, false)
                ->whereIn('status', ['dikirim_ke_tps', 'dikirim', 'disetujui', 'disetujui_admin', 'disetujui_tps'])
                ->countAllResults();
            
            // Hitung Limbah Cair yang DIKIRIM_KE_TPS
            $countCair = $db->table('limbah_cair')
                ->where('DATE(tanggal_input) = CURDATE()', null, false)
                ->whereIn('status', ['dikirim_ke_tps', 'dikirim', 'disetujui', 'disetujui_admin'])
                ->countAllResults();
            
            $data['totalDikirim'] = $count3r + $countB3 + $countCair;
            
            log_message('info', "Total DIKIRIM_KE_TPS hari ini: {$data['totalDikirim']} (3R: $count3r, B3: $countB3, Cair: $countCair)");
        } catch (\Exception $e) {
            log_message('error', 'Error counting DIKIRIM_KE_TPS: ' . $e->getMessage());
            $data['totalDikirim'] = 0;
        }
        
        return view('admin_pusat/logbook/index', $data);
    }

    /**
     * Get detail for preview modal - AJAX ONLY
     * @param string $type - Kategori data: '3r', 'b3', atau 'cair'
     * @param int $id - ID data yang akan ditampilkan
     * @return JSON response
     */
    public function getDetail($type, $id)
    {
        // Set header JSON untuk memastikan response adalah JSON
        $this->response->setContentType('application/json');
        
        // Log untuk debugging
        log_message('info', "getDetail called with type: {$type}, id: {$id}");
        
        // Validasi session
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Unauthorized - Silakan login terlebih dahulu'
            ]);
        }

        $db = \Config\Database::connect();
        $data = null;

        try {
            switch (strtolower($type)) {
                case '3r':
                case 'sampah':
                    $data = $db->table('waste_management wm')
                        ->select('wm.id, wm.tanggal as created_at, wm.jenis_sampah, wm.nama_sampah, wm.berat_kg, wm.satuan, wm.nilai_rupiah, wm.status, wm.bukti_foto as foto_bukti, wm.keterangan_bukti, u.nama_lengkap as nama_user, un.nama_unit')
                        ->join('users u', 'wm.user_id = u.id', 'left')
                        ->join('unit un', 'wm.unit_id = un.id', 'left')
                        ->where('wm.id', $id)
                        ->get()
                        ->getRowArray();
                    
                    log_message('info', "Query 3R executed, found: " . ($data ? 'yes' : 'no'));
                    break;

                case 'b3':
                case 'limbah_b3':
                    $data = $db->table('limbah_b3 lb')
                        ->select('lb.*, mlb.nama_limbah, mlb.kode_limbah, mlb.kategori_bahaya, mlb.karakteristik, u.nama_lengkap as nama_user, un.nama_unit')
                        ->join('master_limbah_b3 mlb', 'lb.master_b3_id = mlb.id', 'left')
                        ->join('users u', 'lb.id_user = u.id', 'left')
                        ->join('unit un', 'u.unit_id = un.id', 'left')
                        ->where('lb.id', $id)
                        ->get()
                        ->getRowArray();
                    
                    log_message('info', "Query B3 executed, found: " . ($data ? 'yes' : 'no'));
                    break;

                case 'cair':
                case 'limbah_cair':
                    $data = $db->table('limbah_cair lc')
                        ->select('lc.*, u.nama_lengkap as nama_user, un.nama_unit')
                        ->join('users u', 'lc.id_user = u.id', 'left')
                        ->join('unit un', 'u.unit_id = un.id', 'left')
                        ->where('lc.id', $id)
                        ->get()
                        ->getRowArray();
                    
                    log_message('info', "Query Cair executed, found: " . ($data ? 'yes' : 'no'));
                    break;
                
                default:
                    log_message('error', "Invalid type: {$type}");
                    return $this->response->setJSON([
                        'success' => false, 
                        'message' => "Tipe data tidak valid: {$type}. Gunakan '3r', 'b3', atau 'cair'"
                    ]);
            }

            if ($data) {
                return $this->response->setJSON([
                    'success' => true, 
                    'data' => $data,
                    'type' => $type
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => "Data dengan ID {$id} tidak ditemukan untuk kategori {$type}"
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error in getDetail: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Terjadi kesalahan database',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Export Logbook to Excel with FORMAL FORMAT
     * @param string $category - Kategori: '3r', 'b3', atau 'cair'
     */
    public function exportExcel($category = '3r')
    {
        // Check authentication
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        // CRITICAL: Clean output buffer to prevent corrupted Excel file
        if (ob_get_length()) {
            ob_end_clean();
        }

        // Increase time limit for large exports
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $db = \Config\Database::connect();
        
        // Get filter parameters from request
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        
        // ========================================
        // PERBAIKAN: HAPUS GROUPING - TAMPILKAN SETIAP INPUT SEBAGAI BARIS TERPISAH
        // Data harus SINKRON dengan halaman index (tanpa SUM, COUNT, GROUP BY)
        // ========================================
        
        $data = [];
        $filename = 'Logbook_';
        $periodInfo = '';
        
        switch (strtolower($category)) {
            case '3r':
                $query = $db->table('waste_management wm')
                    ->select('
                        wm.id,
                        wm.tanggal,
                        wm.jenis_sampah,
                        wm.nama_sampah,
                        wm.satuan,
                        wm.berat_kg,
                        wm.nilai_rupiah,
                        wm.status,
                        wm.created_at,
                        u.nama_lengkap as nama_user,
                        un.nama_unit
                    ')
                    ->join('users u', 'wm.user_id = u.id', 'left')
                    ->join('unit un', 'wm.unit_id = un.id', 'left');
                
                // Apply date filter - SAME AS INDEX PAGE
                if ($startDate && $endDate) {
                    $query->where('DATE(wm.tanggal) >=', $startDate)
                          ->where('DATE(wm.tanggal) <=', $endDate);
                    $periodInfo = date('d-m-Y', strtotime($startDate)) . '_sd_' . date('d-m-Y', strtotime($endDate));
                } else {
                    // Default: TODAY ONLY (same as index page)
                    $query->where('DATE(wm.tanggal) = CURDATE()', null, false);
                    $periodInfo = 'Hari_Ini_' . date('d-m-Y');
                }
                
                // NO GROUPING - Setiap input = 1 baris (SINKRON dengan index)
                $data = $query->orderBy('wm.created_at', 'DESC')
                    ->get()
                    ->getResultArray();
                
                $filename .= 'Program_3R_' . $periodInfo . '_';
                break;
                
            case 'b3':
                $query = $db->table('limbah_b3 lb')
                    ->select('
                        lb.id,
                        lb.tanggal_input,
                        mlb.nama_limbah,
                        mlb.kode_limbah,
                        mlb.kategori_bahaya,
                        lb.satuan,
                        lb.timbulan,
                        lb.status,
                        lb.lokasi,
                        lb.created_at,
                        u.nama_lengkap as nama_user,
                        un.nama_unit
                    ')
                    ->join('master_limbah_b3 mlb', 'lb.master_b3_id = mlb.id', 'left')
                    ->join('users u', 'lb.id_user = u.id', 'left')
                    ->join('unit un', 'u.unit_id = un.id', 'left');
                
                // Apply date filter - SAME AS INDEX PAGE
                if ($startDate && $endDate) {
                    $query->where('DATE(lb.tanggal_input) >=', $startDate)
                          ->where('DATE(lb.tanggal_input) <=', $endDate);
                    $periodInfo = date('d-m-Y', strtotime($startDate)) . '_sd_' . date('d-m-Y', strtotime($endDate));
                } else {
                    // Default: TODAY ONLY (same as index page)
                    $query->where('DATE(lb.tanggal_input) = CURDATE()', null, false);
                    $periodInfo = 'Hari_Ini_' . date('d-m-Y');
                }
                
                // NO GROUPING - Setiap input = 1 baris (SINKRON dengan index)
                $data = $query->orderBy('lb.created_at', 'DESC')
                    ->get()
                    ->getResultArray();
                
                $filename .= 'Limbah_B3_' . $periodInfo . '_';
                break;
                
            case 'cair':
                $query = $db->table('limbah_cair lc')
                    ->select('
                        lc.id,
                        lc.tanggal_input,
                        lc.nama_limbah,
                        lc.kode_limbah,
                        lc.satuan,
                        lc.timbulan,
                        lc.status,
                        lc.lokasi,
                        lc.ph,
                        lc.bod,
                        lc.cod,
                        lc.tss,
                        lc.created_at,
                        u.nama_lengkap as nama_user,
                        un.nama_unit
                    ')
                    ->join('users u', 'lc.id_user = u.id', 'left')
                    ->join('unit un', 'u.unit_id = un.id', 'left');
                
                // Apply date filter - SAME AS INDEX PAGE
                if ($startDate && $endDate) {
                    $query->where('DATE(lc.tanggal_input) >=', $startDate)
                          ->where('DATE(lc.tanggal_input) <=', $endDate);
                    $periodInfo = date('d-m-Y', strtotime($startDate)) . '_sd_' . date('d-m-Y', strtotime($endDate));
                } else {
                    // Default: TODAY ONLY (same as index page)
                    $query->where('DATE(lc.tanggal_input) = CURDATE()', null, false);
                    $periodInfo = 'Hari_Ini_' . date('d-m-Y');
                }
                
                // NO GROUPING - Setiap input = 1 baris (SINKRON dengan index)
                $data = $query->orderBy('lc.created_at', 'DESC')
                    ->get()
                    ->getResultArray();
                
                $filename .= 'Limbah_Cair_' . $periodInfo . '_';
                break;
                
            default:
                return redirect()->back()->with('error', 'Kategori tidak valid');
        }
        
        $filename .= date('YmdHis') . '.xlsx';
        
        // Create Excel file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('UIGM POLBAN System')
            ->setTitle('Logbook ' . ucfirst($category))
            ->setSubject('Logbook Export')
            ->setDescription('Export data logbook dari Sistem UIGM POLBAN');
        
        // Get title by category
        $documentTitle = $this->getTitleByCategory($category);
        
        // Determine last column based on category (TANPA KOLOM CHECKBOX DAN TRANSAKSI)
        $lastCol = 'I'; // Default for 3R and B3 (9 columns)
        if ($category === 'cair') {
            $lastCol = 'K'; // 11 columns for Limbah Cair
        }
        
        // ========================================
        // FORMAL DOCUMENT HEADER (Rows 1-7)
        // ========================================
        
        // Row 1: Institution Name
        $sheet->setCellValue('A1', 'POLITEKNIK NEGERI BANDUNG');
        $sheet->mergeCells('A1:' . $lastCol . '1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'name' => 'Calibri'],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);
        
        // Row 2: Document Title
        $sheet->setCellValue('A2', strtoupper($documentTitle));
        $sheet->mergeCells('A2:' . $lastCol . '2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'name' => 'Calibri'],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]
        ]);
        $sheet->getRowDimension(2)->setRowHeight(20);
        
        // Row 3: Address with double bottom border (like official letterhead)
        $sheet->setCellValue('A3', 'Jl. Gegerkalong Hilir, Ciwaruga, Kec. Parongpong, Kabupaten Bandung Barat, Jawa Barat 40559');
        $sheet->mergeCells('A3:' . $lastCol . '3');
        $sheet->getStyle('A3')->applyFromArray([
            'font' => ['size' => 9, 'name' => 'Calibri'],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
        $sheet->getRowDimension(3)->setRowHeight(18);
        
        // Row 4: Empty separator
        $sheet->getRowDimension(4)->setRowHeight(5);
        
        // Row 5-7: Meta Information
        $periodText = 'Periode Pencatatan: ';
        if ($startDate && $endDate) {
            $periodText .= date('d F Y', strtotime($startDate)) . ' s/d ' . date('d F Y', strtotime($endDate));
        } else {
            $periodText .= date('d F Y') . ' (Hari Ini)';
        }
        
        $sheet->setCellValue('A5', $periodText);
        $sheet->mergeCells('A5:' . $lastCol . '5');
        
        $sheet->setCellValue('A6', 'Tanggal Cetak: ' . date('d F Y, H:i') . ' WIB');
        $sheet->mergeCells('A6:' . $lastCol . '6');
        
        $sheet->setCellValue('A7', 'Dicetak Oleh: ' . (session()->get('user')['nama_lengkap'] ?? 'Admin'));
        $sheet->mergeCells('A7:' . $lastCol . '7');
        
        // Style meta info
        $sheet->getStyle('A5:A7')->applyFromArray([
            'font' => ['size' => 10]
        ]);
        
        // Row 8: Empty separator
        $sheet->getRowDimension(8)->setRowHeight(5);
        
        // ========================================
        // TABLE HEADER (Row 9) - TANPA KOLOM CHECKBOX DAN TRANSAKSI
        // ========================================
        
        $headerRow = 9;
        
        // Set header based on category (SINKRON dengan tabel index)
        if ($category === '3r') {
            $sheet->setCellValue('A' . $headerRow, 'No');
            $sheet->setCellValue('B' . $headerRow, 'Waktu Input');
            $sheet->setCellValue('C' . $headerRow, 'Jenis Sampah');
            $sheet->setCellValue('D' . $headerRow, 'Nama Sampah');
            $sheet->setCellValue('E' . $headerRow, 'Berat (kg)');
            $sheet->setCellValue('F' . $headerRow, 'Satuan');
            $sheet->setCellValue('G' . $headerRow, 'Nilai Ekonomis (Rp)');
            $sheet->setCellValue('H' . $headerRow, 'Status');
            $sheet->setCellValue('I' . $headerRow, 'Unit');
        } elseif ($category === 'b3') {
            $sheet->setCellValue('A' . $headerRow, 'No');
            $sheet->setCellValue('B' . $headerRow, 'Waktu Input');
            $sheet->setCellValue('C' . $headerRow, 'Nama Limbah');
            $sheet->setCellValue('D' . $headerRow, 'Kode Limbah');
            $sheet->setCellValue('E' . $headerRow, 'Timbulan (kg)');
            $sheet->setCellValue('F' . $headerRow, 'Satuan');
            $sheet->setCellValue('G' . $headerRow, 'Status');
            $sheet->setCellValue('H' . $headerRow, 'Lokasi');
            $sheet->setCellValue('I' . $headerRow, 'Unit');
        } else { // cair
            $sheet->setCellValue('A' . $headerRow, 'No');
            $sheet->setCellValue('B' . $headerRow, 'Waktu Input');
            $sheet->setCellValue('C' . $headerRow, 'Nama Limbah');
            $sheet->setCellValue('D' . $headerRow, 'Kode Limbah');
            $sheet->setCellValue('E' . $headerRow, 'Volume (L)');
            $sheet->setCellValue('F' . $headerRow, 'pH');
            $sheet->setCellValue('G' . $headerRow, 'BOD (mg/L)');
            $sheet->setCellValue('H' . $headerRow, 'COD (mg/L)');
            $sheet->setCellValue('I' . $headerRow, 'TSS (mg/L)');
            $sheet->setCellValue('J' . $headerRow, 'Status');
            $sheet->setCellValue('K' . $headerRow, 'Unit');
        }
        
        // Style header with professional colors and proper alignment
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11, 'name' => 'Calibri'],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
        ];
        $sheet->getStyle('A' . $headerRow . ':' . $lastCol . $headerRow)->applyFromArray($headerStyle);
        
        // Set row height for header (taller for better visibility)
        $sheet->getRowDimension($headerRow)->setRowHeight(35);
        
        // ========================================
        // DATA ROWS (Starting from row 10) - SINKRON DENGAN INDEX
        // ========================================
        
        $dataStartRow = $headerRow + 1;
        $row = $dataStartRow;
        $no = 1;
        
        // Variables for totals
        $totalBerat = 0;
        $totalNilai = 0;
        $totalVolume = 0;
        
        if (!empty($data)) {
            foreach ($data as $item) {
                $sheet->setCellValue('A' . $row, $no++);
                
                if ($category === '3r') {
                    // Waktu Input (Tanggal + Jam)
                    $waktuInput = date('d/m/Y', strtotime($item['tanggal'])) . ' ' . date('H:i', strtotime($item['created_at']));
                    $sheet->setCellValue('B' . $row, $waktuInput);
                    $sheet->setCellValue('C' . $row, $item['jenis_sampah']);
                    $sheet->setCellValue('D' . $row, $item['nama_sampah']);
                    $sheet->setCellValue('E' . $row, $item['berat_kg']);
                    $sheet->setCellValue('F' . $row, $item['satuan']);
                    $sheet->setCellValue('G' . $row, $item['nilai_rupiah']);
                    
                    // Status
                    $status = $item['status'] ?? 'dikirim_ke_tps';
                    if ($status === 'disetujui' || $status === 'disetujui_admin') {
                        $sheet->setCellValue('H' . $row, 'Disetujui');
                    } elseif ($status === 'ditolak') {
                        $sheet->setCellValue('H' . $row, 'Ditolak');
                    } elseif ($status === 'dikirim_ke_tps' || $status === 'dikirim') {
                        $sheet->setCellValue('H' . $row, 'Dikirim ke TPS');
                    } elseif ($status === 'menunggu_review') {
                        $sheet->setCellValue('H' . $row, 'Menunggu Review');
                    } else {
                        $sheet->setCellValue('H' . $row, ucfirst(str_replace('_', ' ', $status)));
                    }
                    
                    $sheet->setCellValue('I' . $row, $item['nama_unit'] ?? 'N/A');
                    
                    $totalBerat += $item['berat_kg'];
                    $totalNilai += $item['nilai_rupiah'];
                    
                    // Alignment
                    $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    
                } elseif ($category === 'b3') {
                    // Waktu Input (Tanggal + Jam)
                    $waktuInput = date('d/m/Y', strtotime($item['tanggal_input'])) . ' ' . date('H:i', strtotime($item['created_at']));
                    $sheet->setCellValue('B' . $row, $waktuInput);
                    $sheet->setCellValue('C' . $row, $item['nama_limbah']);
                    $sheet->setCellValue('D' . $row, $item['kode_limbah']);
                    $sheet->setCellValue('E' . $row, $item['timbulan']);
                    $sheet->setCellValue('F' . $row, $item['satuan']);
                    
                    // Status
                    $status = $item['status'] ?? 'dikirim_ke_tps';
                    if ($status === 'disetujui_admin' || $status === 'disetujui') {
                        $sheet->setCellValue('G' . $row, 'Disetujui');
                    } elseif ($status === 'ditolak_admin' || $status === 'ditolak_tps' || $status === 'ditolak') {
                        $sheet->setCellValue('G' . $row, 'Ditolak');
                    } elseif ($status === 'dikirim_ke_tps' || $status === 'dikirim') {
                        $sheet->setCellValue('G' . $row, 'Dikirim ke TPS');
                    } elseif ($status === 'disetujui_tps') {
                        $sheet->setCellValue('G' . $row, 'Disetujui TPS');
                    } elseif ($status === 'menunggu_review') {
                        $sheet->setCellValue('G' . $row, 'Menunggu Review');
                    } else {
                        $sheet->setCellValue('G' . $row, ucfirst(str_replace('_', ' ', $status)));
                    }
                    
                    $sheet->setCellValue('H' . $row, $item['lokasi'] ?? '-');
                    $sheet->setCellValue('I' . $row, $item['nama_unit'] ?? 'N/A');
                    
                    $totalBerat += $item['timbulan'];
                    
                    // Alignment
                    $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    
                } else { // cair
                    // Waktu Input (Tanggal + Jam)
                    $waktuInput = date('d/m/Y', strtotime($item['tanggal_input'])) . ' ' . date('H:i', strtotime($item['created_at']));
                    $sheet->setCellValue('B' . $row, $waktuInput);
                    $sheet->setCellValue('C' . $row, $item['nama_limbah']);
                    $sheet->setCellValue('D' . $row, $item['kode_limbah']);
                    $sheet->setCellValue('E' . $row, $item['timbulan']);
                    $sheet->setCellValue('F' . $row, $item['ph'] ? number_format($item['ph'], 1) : '-');
                    $sheet->setCellValue('G' . $row, $item['bod'] ? number_format($item['bod'], 1) : '-');
                    $sheet->setCellValue('H' . $row, $item['cod'] ? number_format($item['cod'], 1) : '-');
                    $sheet->setCellValue('I' . $row, $item['tss'] ? number_format($item['tss'], 1) : '-');
                    
                    // Status
                    $status = $item['status'] ?? 'dikirim_ke_tps';
                    if ($status === 'disetujui' || $status === 'disetujui_admin') {
                        $sheet->setCellValue('J' . $row, 'Disetujui');
                    } elseif ($status === 'ditolak') {
                        $sheet->setCellValue('J' . $row, 'Ditolak');
                    } elseif ($status === 'dikirim_ke_tps' || $status === 'dikirim') {
                        $sheet->setCellValue('J' . $row, 'Dikirim ke TPS');
                    } elseif ($status === 'menunggu_review') {
                        $sheet->setCellValue('J' . $row, 'Menunggu Review');
                    } else {
                        $sheet->setCellValue('J' . $row, ucfirst(str_replace('_', ' ', $status)));
                    }
                    
                    $sheet->setCellValue('K' . $row, $item['nama_unit'] ?? 'N/A');
                    
                    $totalVolume += $item['timbulan'];
                    
                    // Alignment
                    $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('F' . $row . ':J' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                }
                
                // Apply border to data rows (BLACK borders for formal look)
                $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                    'font' => ['name' => 'Calibri', 'size' => 11]
                ]);
                
                // Zebra striping for better readability
                if (($row - $dataStartRow) % 2 == 0) {
                    $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray([
                        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F2F2F2']]
                    ]);
                }
                
                $row++;
            }
            
            // ========================================
            // TOTAL ROW - TANPA KOLOM TRANSAKSI
            // ========================================
            
            $totalRow = $row;
            
            if ($category === '3r') {
                $sheet->setCellValue('A' . $totalRow, 'TOTAL:');
                $sheet->mergeCells('A' . $totalRow . ':D' . $totalRow);
                $sheet->setCellValue('E' . $totalRow, number_format($totalBerat, 2, ',', '.'));
                $sheet->setCellValue('F' . $totalRow, 'kg');
                $sheet->setCellValue('G' . $totalRow, number_format($totalNilai, 0, ',', '.'));
                $sheet->setCellValue('H' . $totalRow, '');
                $sheet->setCellValue('I' . $totalRow, '');
                
                $sheet->getStyle('A' . $totalRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('E' . $totalRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('G' . $totalRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('F' . $totalRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                
            } elseif ($category === 'b3') {
                $sheet->setCellValue('A' . $totalRow, 'TOTAL:');
                $sheet->mergeCells('A' . $totalRow . ':D' . $totalRow);
                $sheet->setCellValue('E' . $totalRow, number_format($totalBerat, 2, ',', '.'));
                $sheet->setCellValue('F' . $totalRow, 'kg');
                $sheet->setCellValue('G' . $totalRow, '');
                $sheet->setCellValue('H' . $totalRow, '');
                $sheet->setCellValue('I' . $totalRow, '');
                
                $sheet->getStyle('A' . $totalRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('E' . $totalRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                
            } else { // cair
                $sheet->setCellValue('A' . $totalRow, 'TOTAL:');
                $sheet->mergeCells('A' . $totalRow . ':D' . $totalRow);
                $sheet->setCellValue('E' . $totalRow, number_format($totalVolume, 2, ',', '.'));
                $sheet->setCellValue('F' . $totalRow, '-');
                $sheet->setCellValue('G' . $totalRow, '-');
                $sheet->setCellValue('H' . $totalRow, '-');
                $sheet->setCellValue('I' . $totalRow, '-');
                $sheet->setCellValue('J' . $totalRow, '');
                $sheet->setCellValue('K' . $totalRow, '');
                
                $sheet->getStyle('A' . $totalRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('E' . $totalRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('F' . $totalRow . ':I' . $totalRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }
            
            // Style total row
            $sheet->getStyle('A' . $totalRow . ':' . $lastCol . $totalRow)->applyFromArray([
                'font' => ['bold' => true, 'size' => 11, 'name' => 'Calibri'],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E7E6E6']],
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
            ]);
            
            // ========================================
            // CATATAN SECTION
            // ========================================
            
            $catatanRow = $totalRow + 2;
            
            $sheet->setCellValue('A' . $catatanRow, 'Catatan:');
            $sheet->getStyle('A' . $catatanRow)->applyFromArray([
                'font' => ['bold' => true, 'size' => 10, 'name' => 'Calibri']
            ]);
            
            if ($category === 'b3') {
                $sheet->setCellValue('A' . ($catatanRow + 1), 'â€¢ Data merupakan akumulasi harian dari transaksi limbah B3');
                $sheet->setCellValue('A' . ($catatanRow + 2), 'â€¢ Maksimal penyimpanan limbah B3 di TPS adalah 90 hari sesuai Peraturan Pemerintah');
            } elseif ($category === '3r') {
                $sheet->setCellValue('A' . ($catatanRow + 1), 'â€¢ Data merupakan akumulasi harian dari transaksi sampah 3R');
                $sheet->setCellValue('A' . ($catatanRow + 2), 'â€¢ Nilai ekonomis dihitung berdasarkan harga jual sampah per kilogram');
            } else { // cair
                $sheet->setCellValue('A' . ($catatanRow + 1), 'â€¢ Data merupakan akumulasi harian dari transaksi limbah cair');
                $sheet->setCellValue('A' . ($catatanRow + 2), 'â€¢ Parameter kualitas (pH, BOD, COD, TSS) merupakan nilai rata-rata dari seluruh transaksi');
            }
            
            $sheet->getStyle('A' . ($catatanRow + 1) . ':A' . ($catatanRow + 2))->applyFromArray([
                'font' => ['size' => 9, 'name' => 'Calibri']
            ]);
            
            // ========================================
            // SIGNATURE SECTION (3 rows after catatan)
            // ========================================
            
            $signatureRow = $catatanRow + 4;
            
            // Add empty row for spacing
            $sheet->getRowDimension($signatureRow - 1)->setRowHeight(10);
            
            // Left signature: Kepala Unit (Column B)
            $sheet->setCellValue('B' . $signatureRow, 'Mengetahui,');
            $sheet->setCellValue('B' . ($signatureRow + 1), 'Kepala Unit Pengelola');
            $sheet->setCellValue('B' . ($signatureRow + 5), '(______________________)');
            $sheet->setCellValue('B' . ($signatureRow + 6), 'NIP. .....................');
            
            // Right signature: Petugas (positioned based on category for proper alignment)
            if ($category === '3r') {
                $signatureCol = 'H';  // Column H for 3R (10 columns total)
            } elseif ($category === 'b3') {
                $signatureCol = 'G';  // Column G for B3 (9 columns total)
            } else { // cair
                $signatureCol = 'J';  // Column J for Cair (12 columns total)
            }
            
            $sheet->setCellValue($signatureCol . $signatureRow, 'Petugas Pencatat,');
            $sheet->setCellValue($signatureCol . ($signatureRow + 5), '(______________________)');
            $sheet->setCellValue($signatureCol . ($signatureRow + 6), 'NIP. .....................');
            
            // Style left signature
            $sheet->getStyle('B' . $signatureRow . ':B' . ($signatureRow + 6))->applyFromArray([
                'font' => ['size' => 11, 'name' => 'Calibri'],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT]
            ]);
            
            // Style right signature
            $sheet->getStyle($signatureCol . $signatureRow . ':' . $signatureCol . ($signatureRow + 6))->applyFromArray([
                'font' => ['size' => 11, 'name' => 'Calibri'],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT]
            ]);
            
            // Set row heights for signature area
            for ($i = 0; $i <= 6; $i++) {
                if ($i >= 2 && $i <= 4) {
                    // Rows for signature space (taller)
                    $sheet->getRowDimension($signatureRow + $i)->setRowHeight(20);
                } else {
                    // Other rows (normal)
                    $sheet->getRowDimension($signatureRow + $i)->setRowHeight(18);
                }
            }
        } else {
            // If no data, add a message
            $sheet->setCellValue('A' . $dataStartRow, 'Tidak ada data untuk periode yang dipilih');
            $sheet->mergeCells('A' . $dataStartRow . ':' . $lastCol . $dataStartRow);
            $sheet->getStyle('A' . $dataStartRow)->applyFromArray([
                'font' => ['italic' => true, 'color' => ['rgb' => '999999'], 'size' => 11, 'name' => 'Calibri'],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
            ]);
            $sheet->getRowDimension($dataStartRow)->setRowHeight(30);
        }
        
        // ========================================
        // COLUMN WIDTHS (Fixed widths for stable display)
        // ========================================
        
        $sheet->getColumnDimension('A')->setWidth(5);  // No
        
        if ($category === '3r') {
            $sheet->getColumnDimension('B')->setWidth(18); // Waktu Input
            $sheet->getColumnDimension('C')->setWidth(20); // Jenis Sampah
            $sheet->getColumnDimension('D')->setWidth(25); // Nama Sampah
            $sheet->getColumnDimension('E')->setWidth(15); // Berat
            $sheet->getColumnDimension('F')->setWidth(10); // Satuan
            $sheet->getColumnDimension('G')->setWidth(18); // Nilai Ekonomis
            $sheet->getColumnDimension('H')->setWidth(18); // Status
            $sheet->getColumnDimension('I')->setWidth(25); // Unit
        } elseif ($category === 'b3') {
            $sheet->getColumnDimension('B')->setWidth(18); // Waktu Input
            $sheet->getColumnDimension('C')->setWidth(25); // Nama Limbah
            $sheet->getColumnDimension('D')->setWidth(15); // Kode Limbah
            $sheet->getColumnDimension('E')->setWidth(15); // Timbulan
            $sheet->getColumnDimension('F')->setWidth(10); // Satuan
            $sheet->getColumnDimension('G')->setWidth(18); // Status
            $sheet->getColumnDimension('H')->setWidth(20); // Lokasi
            $sheet->getColumnDimension('I')->setWidth(25); // Unit
        } else { // cair
            $sheet->getColumnDimension('B')->setWidth(18); // Waktu Input
            $sheet->getColumnDimension('C')->setWidth(25); // Nama Limbah
            $sheet->getColumnDimension('D')->setWidth(15); // Kode Limbah
            $sheet->getColumnDimension('E')->setWidth(15); // Volume
            $sheet->getColumnDimension('F')->setWidth(10); // pH
            $sheet->getColumnDimension('G')->setWidth(12); // BOD
            $sheet->getColumnDimension('H')->setWidth(12); // COD
            $sheet->getColumnDimension('I')->setWidth(12); // TSS
            $sheet->getColumnDimension('J')->setWidth(18); // Status
            $sheet->getColumnDimension('K')->setWidth(25); // Unit
        }
        
        // Freeze header row (row 9)
        $sheet->freezePane('A' . $dataStartRow);
        
        // Generate file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        // Clear any output buffers
        if (ob_get_length()) {
            ob_end_clean();
        }
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Export Logbook to PDF
     * @param string $category - Kategori: '3r', 'b3', atau 'cair'
     */
    public function exportPdf($category = '3r')
    {
        // Check authentication
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        // Increase time limit and memory for large reports
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $db = \Config\Database::connect();
        
        // Get filter parameters from request
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        
        // ========================================
        // PERBAIKAN: HAPUS GROUPING - TAMPILKAN DATA DETAIL PER BARIS
        // Setiap input user = 1 baris terpisah (SAMA SEPERTI DASHBOARD)
        // ========================================
        
        $data = [];
        $title = '';
        $filename = 'Logbook_';
        
        switch (strtolower($category)) {
            case '3r':
                $query = $db->table('waste_management wm')
                    ->select('
                        wm.id,
                        wm.tanggal,
                        wm.jenis_sampah,
                        wm.nama_sampah,
                        wm.satuan,
                        wm.berat_kg,
                        wm.nilai_rupiah,
                        wm.status,
                        wm.created_at,
                        u.nama_lengkap as nama_user,
                        un.nama_unit
                    ')
                    ->join('users u', 'wm.user_id = u.id', 'left')
                    ->join('unit un', 'wm.unit_id = un.id', 'left');
                
                // Apply date filter if provided
                if ($startDate && $endDate) {
                    $query->where('DATE(wm.tanggal) >=', $startDate)
                          ->where('DATE(wm.tanggal) <=', $endDate);
                } else {
                    // Default: today only
                    $query->where('DATE(wm.tanggal) = CURDATE()', null, false);
                }
                
                // NO GROUPING - Setiap input = 1 baris
                $data = $query->orderBy('wm.created_at', 'DESC')
                    ->get()
                    ->getResultArray();
                
                $title = 'Logbook Program 3R';
                $filename .= 'Program_3R_';
                break;
                
            case 'b3':
                $query = $db->table('limbah_b3 lb')
                    ->select('
                        lb.id,
                        lb.tanggal_input,
                        mlb.nama_limbah,
                        mlb.kode_limbah,
                        mlb.kategori_bahaya,
                        lb.satuan,
                        lb.timbulan,
                        lb.status,
                        lb.lokasi,
                        lb.created_at,
                        u.nama_lengkap as nama_user,
                        un.nama_unit
                    ')
                    ->join('master_limbah_b3 mlb', 'lb.master_b3_id = mlb.id', 'left')
                    ->join('users u', 'lb.id_user = u.id', 'left')
                    ->join('unit un', 'u.unit_id = un.id', 'left');
                
                // Apply date filter if provided
                if ($startDate && $endDate) {
                    $query->where('DATE(lb.tanggal_input) >=', $startDate)
                          ->where('DATE(lb.tanggal_input) <=', $endDate);
                } else {
                    // Default: today only
                    $query->where('DATE(lb.tanggal_input) = CURDATE()', null, false);
                }
                
                // NO GROUPING - Setiap input = 1 baris
                $data = $query->orderBy('lb.created_at', 'DESC')
                    ->get()
                    ->getResultArray();
                
                $title = 'Logbook Limbah B3';
                $filename .= 'Limbah_B3_';
                break;
                
            case 'cair':
                $query = $db->table('limbah_cair lc')
                    ->select('
                        lc.id,
                        lc.tanggal_input,
                        lc.nama_limbah,
                        lc.kode_limbah,
                        lc.satuan,
                        lc.timbulan,
                        lc.status,
                        lc.lokasi,
                        lc.ph,
                        lc.bod,
                        lc.cod,
                        lc.tss,
                        lc.created_at,
                        u.nama_lengkap as nama_user,
                        un.nama_unit
                    ')
                    ->join('users u', 'lc.id_user = u.id', 'left')
                    ->join('unit un', 'u.unit_id = un.id', 'left');
                
                // Apply date filter if provided
                if ($startDate && $endDate) {
                    $query->where('DATE(lc.tanggal_input) >=', $startDate)
                          ->where('DATE(lc.tanggal_input) <=', $endDate);
                } else {
                    // Default: today only
                    $query->where('DATE(lc.tanggal_input) = CURDATE()', null, false);
                }
                
                // NO GROUPING - Setiap input = 1 baris
                $data = $query->orderBy('lc.created_at', 'DESC')
                    ->get()
                    ->getResultArray();
                
                $title = 'Logbook Limbah Cair';
                $filename .= 'Limbah_Cair_';
                break;
                
            default:
                return redirect()->back()->with('error', 'Kategori tidak valid');
        }
        
        $filename .= date('YmdHis') . '.pdf';
        
        // Prepare data for view (SAME AS PRINT FORMAL)
        $pdfData = [
            'title' => $this->getTitleByCategory($category),
            'category' => $category,
            'data' => $data,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'generated_at' => date('d/m/Y H:i:s'),
            'generated_by' => session()->get('user')['nama_lengkap'] ?? 'Admin'
        ];
        
        // USE THE SAME VIEW AS PRINT FORMAL
        $html = view('admin_pusat/logbook/print_formal', $pdfData);
        
        // Initialize Dompdf
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isFontSubsettingEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        
        // Set paper size and orientation to LANDSCAPE (CRITICAL for wide tables)
        $dompdf->setPaper('A4', 'landscape');
        
        // Render PDF
        $dompdf->render();
        
        // Output PDF
        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }

    /**
     * Helper function to get title by category
     */
    private function getTitleByCategory($category)
    {
        switch (strtolower($category)) {
            case '3r':
                return 'PENCATATAN PROGRAM 3R (REDUCE, REUSE, RECYCLE)';
            case 'b3':
                return 'PENCATATAN PENGELOLAAN LIMBAH B3 (LOG BOOK)';
            case 'cair':
                return 'PENCATATAN PENGELOLAAN LIMBAH CAIR (LOG BOOK)';
            default:
                return 'LOGBOOK';
        }
    }

    /**
     * Print Formal Logbook
     * Menampilkan halaman print formal tanpa elemen dashboard
     * @param string $category - Kategori: '3r', 'b3', atau 'cair'
     */
    public function printFormal($category = 'b3')
    {
        // Check authentication
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        $user = session()->get('user');
        if (!in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return redirect()->to('/auth/login');
        }

        $db = \Config\Database::connect();
        
        // Get filter parameters from request
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        
        // ========================================
        // PERBAIKAN: HAPUS GROUPING - TAMPILKAN DATA DETAIL PER BARIS
        // Setiap input user = 1 baris terpisah (SAMA SEPERTI DASHBOARD)
        // ========================================
        
        $data = [];
        $title = '';
        
        switch (strtolower($category)) {
            case '3r':
                $query = $db->table('waste_management wm')
                    ->select('
                        wm.id,
                        wm.tanggal,
                        wm.jenis_sampah,
                        wm.nama_sampah,
                        wm.satuan,
                        wm.berat_kg,
                        wm.nilai_rupiah,
                        wm.status,
                        wm.created_at,
                        u.nama_lengkap as nama_user,
                        un.nama_unit
                    ')
                    ->join('users u', 'wm.user_id = u.id', 'left')
                    ->join('unit un', 'wm.unit_id = un.id', 'left');
                
                if ($startDate && $endDate) {
                    $query->where('DATE(wm.tanggal) >=', $startDate)
                          ->where('DATE(wm.tanggal) <=', $endDate);
                } else {
                    $query->where('DATE(wm.tanggal) = CURDATE()', null, false);
                }
                
                // NO GROUPING - Setiap input = 1 baris
                $data = $query->orderBy('wm.created_at', 'DESC')
                    ->get()
                    ->getResultArray();
                
                $title = 'PENCATATAN PROGRAM 3R (REDUCE, REUSE, RECYCLE)';
                break;
                
            case 'b3':
                $query = $db->table('limbah_b3 lb')
                    ->select('
                        lb.id,
                        lb.tanggal_input,
                        mlb.nama_limbah,
                        mlb.kode_limbah,
                        mlb.kategori_bahaya,
                        lb.satuan,
                        lb.timbulan,
                        lb.status,
                        lb.lokasi,
                        lb.created_at,
                        u.nama_lengkap as nama_user,
                        un.nama_unit
                    ')
                    ->join('master_limbah_b3 mlb', 'lb.master_b3_id = mlb.id', 'left')
                    ->join('users u', 'lb.id_user = u.id', 'left')
                    ->join('unit un', 'u.unit_id = un.id', 'left');
                
                if ($startDate && $endDate) {
                    $query->where('DATE(lb.tanggal_input) >=', $startDate)
                          ->where('DATE(lb.tanggal_input) <=', $endDate);
                } else {
                    $query->where('DATE(lb.tanggal_input) = CURDATE()', null, false);
                }
                
                // NO GROUPING - Setiap input = 1 baris
                $data = $query->orderBy('lb.created_at', 'DESC')
                    ->get()
                    ->getResultArray();
                
                $title = 'PENCATATAN PENGELOLAAN LIMBAH B3 (LOG BOOK)';
                break;
                
            case 'cair':
                $query = $db->table('limbah_cair lc')
                    ->select('
                        lc.id,
                        lc.tanggal_input,
                        lc.nama_limbah,
                        lc.kode_limbah,
                        lc.satuan,
                        lc.timbulan,
                        lc.status,
                        lc.lokasi,
                        lc.ph,
                        lc.bod,
                        lc.cod,
                        lc.tss,
                        lc.created_at,
                        u.nama_lengkap as nama_user,
                        un.nama_unit
                    ')
                    ->join('users u', 'lc.id_user = u.id', 'left')
                    ->join('unit un', 'u.unit_id = un.id', 'left');
                
                if ($startDate && $endDate) {
                    $query->where('DATE(lc.tanggal_input) >=', $startDate)
                          ->where('DATE(lc.tanggal_input) <=', $endDate);
                } else {
                    $query->where('DATE(lc.tanggal_input) = CURDATE()', null, false);
                }
                
                // NO GROUPING - Setiap input = 1 baris
                $data = $query->orderBy('lc.created_at', 'DESC')
                    ->get()
                    ->getResultArray();
                
                $title = 'PENCATATAN PENGELOLAAN LIMBAH CAIR (LOG BOOK)';
                break;
                
            default:
                return redirect()->back()->with('error', 'Kategori tidak valid');
        }
        
        // Prepare data for view
        $viewData = [
            'title' => $title,
            'category' => $category,
            'data' => $data,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'generated_at' => date('d/m/Y H:i:s'),
            'generated_by' => $user['nama_lengkap'] ?? 'Admin'
        ];
        
        return view('admin_pusat/logbook/print_formal', $viewData);
    }

    /**
     * Backup Data Logbook
     * Export data ke Excel berdasarkan periode yang dipilih
     */
    public function backup()
    {
        // CRITICAL: Matikan SEMUA output buffer dan start fresh
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();
        
        // Check authentication
        if (!session()->get('isLoggedIn')) {
            // Return JSON untuk AJAX request
            if ($this->request->isAJAX() || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'UNAUTHORIZED',
                    'message' => 'Silakan login terlebih dahulu'
                ])->setStatusCode(401);
            }
            return redirect()->to('/auth/login');
        }

        // Increase time limit and memory for large reports
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $db = \Config\Database::connect();
        
        // Get parameters
        $kategori = $this->request->getGet('kategori') ?? '3r';
        $type = $this->request->getGet('type') ?? 'harian';
        $tanggal = $this->request->getGet('tanggal');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $bulan = $this->request->getGet('bulan');
        $tahun = $this->request->getGet('tahun');
        
        // Determine date range based on type
        $dateCondition = '';
        $filenameSuffix = '';
        $periodInfo = '';
        
        switch ($type) {
            case 'harian':
                if (!$tanggal) {
                    return $this->response->setJSON([
                        'success' => false,
                        'error' => 'VALIDATION_ERROR',
                        'message' => 'Tanggal harus diisi'
                    ])->setStatusCode(400);
                }
                $dateCondition = "DATE({table}.{date_field}) = '$tanggal'";
                $filenameSuffix = 'Harian_' . date('dmY', strtotime($tanggal));
                $periodInfo = date('d/m/Y', strtotime($tanggal));
                break;
                
            case 'mingguan':
                if (!$startDate || !$endDate) {
                    return $this->response->setJSON([
                        'success' => false,
                        'error' => 'VALIDATION_ERROR',
                        'message' => 'Tanggal mulai dan akhir harus diisi'
                    ])->setStatusCode(400);
                }
                $dateCondition = "DATE({table}.{date_field}) BETWEEN '$startDate' AND '$endDate'";
                $filenameSuffix = 'Mingguan_' . date('dmY', strtotime($startDate)) . '_' . date('dmY', strtotime($endDate));
                $periodInfo = date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate));
                break;
                
            case 'bulanan':
                if (!$bulan || !$tahun) {
                    return $this->response->setJSON([
                        'success' => false,
                        'error' => 'VALIDATION_ERROR',
                        'message' => 'Bulan dan tahun harus diisi'
                    ])->setStatusCode(400);
                }
                $dateCondition = "MONTH({table}.{date_field}) = '$bulan' AND YEAR({table}.{date_field}) = '$tahun'";
                $filenameSuffix = 'Bulanan_' . $bulan . '_' . $tahun;
                $periodInfo = "Bulan $bulan Tahun $tahun";
                break;
                
            default:
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'VALIDATION_ERROR',
                    'message' => 'Tipe backup tidak valid'
                ])->setStatusCode(400);
        }
        
        // Prepare data based on category
        $data = [];
        $totalRecords = 0;
        $totalDraft = 0;
        $totalAll = 0;
        $totalDikirim = 0;  // TAMBAHAN: Definisikan variabel ini
        $filename = 'Backup_Logbook_';
        
        // Query Program 3R - PERLUAS JANGKAUAN (semua status)
        if ($kategori === 'all' || $kategori === '3r') {
            $condition = str_replace(['{table}', '{date_field}'], ['wm', 'tanggal'], $dateCondition);
            
            log_message('info', "Query 3R Condition: $condition");
            
            // Query 1: Cek total data (semua status)
            $queryTotal = $db->query("
                SELECT COUNT(*) as total
                FROM waste_management wm
                WHERE $condition
            ");
            $totalAll = $queryTotal->getRow()->total;
            log_message('info', "Total data 3R (semua status): $totalAll");
            
            // Query 2: Cek data DRAFT
            $queryDraft = $db->query("
                SELECT COUNT(*) as total
                FROM waste_management wm
                WHERE $condition
                    AND wm.status = 'draft'
            ");
            $countDraft = $queryDraft->getRow()->total;
            log_message('info', "Data 3R DRAFT: $countDraft");
            
            // Query 3: Ambil data yang sudah DIKIRIM_KE_TPS
            $query = $db->query("
                SELECT 
                    wm.id,
                    wm.tanggal,
                    wm.jenis_sampah,
                    wm.nama_sampah,
                    wm.berat_kg,
                    wm.satuan,
                    wm.nilai_rupiah,
                    wm.gedung,
                    wm.status,
                    wm.created_at,
                    u.nama_lengkap as nama_user,
                    un.nama_unit
                FROM waste_management wm
                LEFT JOIN users u ON wm.user_id = u.id
                LEFT JOIN unit un ON wm.unit_id = un.id
                WHERE $condition
                    AND wm.status IN ('dikirim_ke_tps', 'dikirim', 'disetujui', 'disetujui_admin')
                ORDER BY wm.tanggal DESC, wm.created_at DESC
            ");
            
            $data3r = $query->getResultArray();
            $count3r = count($data3r);
            log_message('info', "Data 3R DIKIRIM_KE_TPS: $count3r records");
            
            if (!empty($data3r)) {
                $data['Program 3R'] = [
                    'headers' => ['No', 'Tanggal', 'Jenis Sampah', 'Nama Sampah', 'Berat (kg)', 'Satuan', 'Nilai (Rp)', 'Gedung', 'Unit', 'User', 'Status', 'Waktu Input'],
                    'data' => $data3r
                ];
                $totalRecords += $count3r;
                $totalDikirim += $count3r;
            }
            
            $totalDraft += $countDraft;
        }
        
        // Query Limbah B3 - PERLUAS JANGKAUAN
        if ($kategori === 'all' || $kategori === 'b3') {
            $condition = str_replace(['{table}', '{date_field}'], ['lb', 'tanggal_input'], $dateCondition);
            
            log_message('info', "Query B3 Condition: $condition");
            
            // Query 1: Cek total data
            $queryTotal = $db->query("
                SELECT COUNT(*) as total
                FROM limbah_b3 lb
                WHERE $condition
            ");
            $totalAll = $queryTotal->getRow()->total;
            log_message('info', "Total data B3 (semua status): $totalAll");
            
            // Query 2: Cek data DRAFT
            $queryDraft = $db->query("
                SELECT COUNT(*) as total
                FROM limbah_b3 lb
                WHERE $condition
                    AND lb.status = 'draft'
            ");
            $countDraft = $queryDraft->getRow()->total;
            log_message('info', "Data B3 DRAFT: $countDraft");
            
            // Query 3: Ambil data yang sudah DIKIRIM
            $query = $db->query("
                SELECT 
                    lb.id,
                    lb.tanggal_input,
                    mlb.nama_limbah,
                    mlb.kode_limbah,
                    mlb.kategori_bahaya,
                    lb.timbulan,
                    lb.satuan,
                    lb.lokasi,
                    lb.status,
                    lb.created_at,
                    u.nama_lengkap as nama_user,
                    un.nama_unit
                FROM limbah_b3 lb
                LEFT JOIN master_limbah_b3 mlb ON lb.master_b3_id = mlb.id
                LEFT JOIN users u ON lb.id_user = u.id
                LEFT JOIN unit un ON u.unit_id = un.id
                WHERE $condition
                    AND lb.status IN ('dikirim_ke_tps', 'dikirim', 'disetujui', 'disetujui_admin', 'disetujui_tps')
                ORDER BY lb.tanggal_input DESC, lb.created_at DESC
            ");
            
            $dataB3 = $query->getResultArray();
            $countB3 = count($dataB3);
            log_message('info', "Data B3 DIKIRIM_KE_TPS: $countB3 records");
            
            if (!empty($dataB3)) {
                $data['Limbah B3'] = [
                    'headers' => ['No', 'Tanggal', 'Nama Limbah', 'Kode Limbah', 'Kategori Bahaya', 'Timbulan', 'Satuan', 'Lokasi', 'Unit', 'User', 'Status', 'Waktu Input'],
                    'data' => $dataB3
                ];
                $totalRecords += $countB3;
                $totalDikirim += $countB3;
            }
            
            $totalDraft += $countDraft;
        }
        
        // Query Limbah Cair - PERLUAS JANGKAUAN
        if ($kategori === 'all' || $kategori === 'cair') {
            $condition = str_replace(['{table}', '{date_field}'], ['lc', 'tanggal_input'], $dateCondition);
            
            log_message('info', "Query Cair Condition: $condition");
            
            // Query 1: Cek total data
            $queryTotal = $db->query("
                SELECT COUNT(*) as total
                FROM limbah_cair lc
                WHERE $condition
            ");
            $totalAll = $queryTotal->getRow()->total;
            log_message('info', "Total data Cair (semua status): $totalAll");
            
            // Query 2: Cek data DRAFT
            $queryDraft = $db->query("
                SELECT COUNT(*) as total
                FROM limbah_cair lc
                WHERE $condition
                    AND lc.status = 'draft'
            ");
            $countDraft = $queryDraft->getRow()->total;
            log_message('info', "Data Cair DRAFT: $countDraft");
            
            // Query 3: Ambil data yang sudah DIKIRIM
            $query = $db->query("
                SELECT 
                    lc.id,
                    lc.tanggal_input,
                    lc.nama_limbah,
                    lc.kode_limbah,
                    lc.timbulan,
                    lc.satuan,
                    lc.lokasi,
                    lc.ph,
                    lc.bod,
                    lc.cod,
                    lc.tss,
                    lc.status,
                    lc.created_at,
                    u.nama_lengkap as nama_user,
                    un.nama_unit
                FROM limbah_cair lc
                LEFT JOIN users u ON lc.id_user = u.id
                LEFT JOIN unit un ON u.unit_id = un.id
                WHERE $condition
                    AND lc.status IN ('dikirim_ke_tps', 'dikirim', 'disetujui', 'disetujui_admin')
                ORDER BY lc.tanggal_input DESC, lc.created_at DESC
            ");
            
            $dataCair = $query->getResultArray();
            $countCair = count($dataCair);
            log_message('info', "Data Cair DIKIRIM_KE_TPS: $countCair records");
            
            if (!empty($dataCair)) {
                $data['Limbah Cair'] = [
                    'headers' => ['No', 'Tanggal', 'Nama Limbah', 'Kode Limbah', 'Timbulan', 'Satuan', 'Lokasi', 'pH', 'BOD', 'COD', 'TSS', 'Unit', 'User', 'Status', 'Waktu Input'],
                    'data' => $dataCair
                ];
                $totalRecords += $countCair;
                $totalDikirim += $countCair;
            }
            
            $totalDraft += $countDraft;
        }
        
        // VALIDASI AKHIR: Return JSON error yang JELAS
        log_message('info', "=== SUMMARY ===");
        log_message('info', "Total ALL: $totalAll, Total DRAFT: $totalDraft, Total DIKIRIM: $totalRecords");
        
        if (empty($data)) {
            // Jika ada data DRAFT
            if ($totalDraft > 0) {
                log_message('warning', "NO DATA: Ada $totalDraft data DRAFT");
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'NO_DATA_DIKIRIM',
                    'message' => "Tidak ada data dengan status DIKIRIM_KE_TPS pada periode $periodInfo",
                    'detail' => "Ditemukan $totalDraft data dengan status DRAFT. Silakan kirim data ke TPS terlebih dahulu.",
                    'total_all' => $totalAll,
                    'total_draft' => $totalDraft,
                    'total_dikirim' => 0
                ])->setStatusCode(404);
            }
            
            // Jika tidak ada data sama sekali
            return $this->response->setJSON([
                'success' => false,
                'error' => 'NO_DATA',
                'message' => "Tidak ada data untuk periode $periodInfo",
                'detail' => "Belum ada input data untuk tanggal tersebut. Silakan pilih tanggal lain atau input data terlebih dahulu.",
                'total_all' => 0,
                'total_draft' => 0,
                'total_dikirim' => 0
            ])->setStatusCode(404);
        }
        
        // Generate Excel file - BUAT NAMA FILE BERSIH TANPA EKSTENSI DULU
        $baseFilename = 'Backup_Logbook_' . $filenameSuffix;
        
        // Bersihkan nama file dari karakter ilegal
        $baseFilename = trim($baseFilename);
        $baseFilename = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|', ' '], '_', $baseFilename);
        $baseFilename = preg_replace('/_+/', '_', $baseFilename);
        $baseFilename = rtrim($baseFilename, '_');
        
        // Kirim TANPA ekstensi, akan ditambahkan di generateBackupExcel
        return $this->generateBackupExcel($data, $baseFilename);
    }

    /**
     * Generate Excel file for backup
     */
    /**
     * Generate Excel file for backup
     * PERBAIKAN: Tambah ob_end_clean() dan error handling
     */
    private function generateBackupExcel($data, $baseFilename)
    {
        // CRITICAL: Matikan SEMUA output buffer dan start fresh
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();
        
        try {
            if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
                throw new \Exception('Library PhpSpreadsheet tidak ditemukan.');
            }
            
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $spreadsheet->removeSheetByIndex(0);
            
            $sheetIndex = 0;
            
            foreach ($data as $sheetName => $sheetData) {
                $sheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $sheetName);
                $spreadsheet->addSheet($sheet, $sheetIndex++);
                
                $col = 'A';
                foreach ($sheetData['headers'] as $header) {
                    $sheet->setCellValue($col . '1', $header);
                    $sheet->getStyle($col . '1')->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
                    ]);
                    $col++;
                }
                
                $sheet->freezePane('A2');
                
                $row = 2;
                $no = 1;
                foreach ($sheetData['data'] as $item) {
                    $col = 'A';
                    $sheet->setCellValue($col++ . $row, $no++);
                    
                    if ($sheetName === 'Program 3R') {
                        $sheet->setCellValue($col++ . $row, date('d/m/Y', strtotime($item['tanggal'])));
                        $sheet->setCellValue($col++ . $row, $item['jenis_sampah'] ?? '-');
                        $sheet->setCellValue($col++ . $row, $item['nama_sampah'] ?? '-');
                        $sheet->setCellValue($col++ . $row, $item['berat_kg'] ?? 0);
                        $sheet->setCellValue($col++ . $row, $item['satuan'] ?? 'kg');
                        $sheet->setCellValue($col++ . $row, $item['nilai_rupiah'] ?? 0);
                        $sheet->setCellValue($col++ . $row, $item['gedung'] ?? '-');
                        $sheet->setCellValue($col++ . $row, $item['nama_unit'] ?? '-');
                        $sheet->setCellValue($col++ . $row, $item['nama_user'] ?? '-');
                        $sheet->setCellValue($col++ . $row, ucfirst(str_replace('_', ' ', $item['status'] ?? 'draft')));
                        $sheet->setCellValue($col++ . $row, date('d/m/Y H:i', strtotime($item['created_at'])));
                    } elseif ($sheetName === 'Limbah B3') {
                        $sheet->setCellValue($col++ . $row, date('d/m/Y', strtotime($item['tanggal_input'])));
                        $sheet->setCellValue($col++ . $row, $item['nama_limbah'] ?? '-');
                        $sheet->setCellValue($col++ . $row, $item['kode_limbah'] ?? '-');
                        $sheet->setCellValue($col++ . $row, $item['kategori_bahaya'] ?? '-');
                        $sheet->setCellValue($col++ . $row, $item['timbulan'] ?? 0);
                        $sheet->setCellValue($col++ . $row, $item['satuan'] ?? 'kg');
                        $sheet->setCellValue($col++ . $row, $item['lokasi'] ?? '-');
                        $sheet->setCellValue($col++ . $row, $item['nama_unit'] ?? '-');
                        $sheet->setCellValue($col++ . $row, $item['nama_user'] ?? '-');
                        $sheet->setCellValue($col++ . $row, ucfirst(str_replace('_', ' ', $item['status'] ?? 'draft')));
                        $sheet->setCellValue($col++ . $row, date('d/m/Y H:i', strtotime($item['created_at'])));
                    } elseif ($sheetName === 'Limbah Cair') {
                        $sheet->setCellValue($col++ . $row, date('d/m/Y', strtotime($item['tanggal_input'])));
                        $sheet->setCellValue($col++ . $row, $item['nama_limbah'] ?? '-');
                        $sheet->setCellValue($col++ . $row, $item['kode_limbah'] ?? '-');
                        $sheet->setCellValue($col++ . $row, $item['timbulan'] ?? 0);
                        $sheet->setCellValue($col++ . $row, $item['satuan'] ?? 'liter');
                        $sheet->setCellValue($col++ . $row, $item['lokasi'] ?? '-');
                        $sheet->setCellValue($col++ . $row, $item['ph'] ?? '-');
                        $sheet->setCellValue($col++ . $row, $item['bod'] ?? '-');
                        $sheet->setCellValue($col++ . $row, $item['cod'] ?? '-');
                        $sheet->setCellValue($col++ . $row, $item['tss'] ?? '-');
                        $sheet->setCellValue($col++ . $row, $item['nama_unit'] ?? '-');
                        $sheet->setCellValue($col++ . $row, $item['nama_user'] ?? '-');
                        $sheet->setCellValue($col++ . $row, ucfirst(str_replace('_', ' ', $item['status'] ?? 'draft')));
                        $sheet->setCellValue($col++ . $row, date('d/m/Y H:i', strtotime($item['created_at'])));
                    }
                    
                    $row++;
                }
                
                foreach (range('A', $sheet->getHighestColumn()) as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }
            }
            
            $spreadsheet->setActiveSheetIndex(0);
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            
            // Buang buffer yang sudah ditangkap
            ob_end_clean();
            
            // Set headers - RAPAT TANPA SPASI
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Backup_Logbook.xlsx"');
            header('Cache-Control: max-age=0');
            
            // Save dan EXIT
            $writer->save('php://output');
            exit;
            
        } catch (\Exception $e) {
            ob_end_clean();
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Gagal membuat file backup: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Bulk Delete - Hapus Data Terpilih Secara Massal
     * Hanya bisa diakses oleh Admin Pusat
     */
    public function bulkDelete()
    {
        // Check authentication
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized - Silakan login terlebih dahulu'
            ]);
        }

        // Check role - hanya Admin Pusat yang bisa akses
        $userRole = session()->get('user')['role'] ?? '';
        if ($userRole !== 'admin_pusat') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak - Fitur ini hanya untuk Admin Pusat'
            ]);
        }

        // Check if request is AJAX
        if (!$this->request->isAJAX()) {
            return redirect()->back()->with('error', 'Invalid request');
        }

        // Get JSON input
        $json = $this->request->getJSON();
        
        $kategori = $json->kategori ?? '';
        $ids = $json->ids ?? [];
        
        // Validasi input
        if (empty($kategori)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Kategori harus diisi'
            ]);
        }
        
        if (empty($ids) || !is_array($ids)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada data yang dipilih untuk dihapus'
            ]);
        }
        
        $db = \Config\Database::connect();
        
        // Start transaction
        $db->transStart();
        
        try {
            $deletedCount = 0;
            $tableName = '';
            
            // Tentukan tabel berdasarkan kategori
            switch ($kategori) {
                case '3r':
                    $tableName = 'waste_management';
                    $builder = $db->table($tableName);
                    $builder->whereIn('id', $ids);
                    $deletedCount = $builder->delete();
                    break;
                    
                case 'b3':
                    $tableName = 'limbah_b3';
                    $builder = $db->table($tableName);
                    $builder->whereIn('id', $ids);
                    $deletedCount = $builder->delete();
                    break;
                    
                case 'cair':
                    $tableName = 'limbah_cair';
                    $builder = $db->table($tableName);
                    $builder->whereIn('id', $ids);
                    $deletedCount = $builder->delete();
                    break;
                    
                default:
                    throw new \Exception('Kategori tidak valid');
            }
            
            // Complete transaction
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Gagal menghapus data. Terjadi kesalahan database.');
            }
            
            // Log activity
            $userName = session()->get('user')['nama_lengkap'] ?? 'Unknown User';
            $kategoriLabel = $kategori === '3r' ? 'Program 3R' : ($kategori === 'b3' ? 'Limbah B3' : 'Limbah Cair');
            log_message('info', "Bulk delete berhasil oleh $userName. Kategori: $kategoriLabel. Total: $deletedCount data. IDs: " . implode(', ', $ids));
            
            // Success response
            $message = '<div class="text-start">';
            $message .= '<p><strong>✅ Berhasil!</strong></p>';
            $message .= '<p>' . $deletedCount . ' data ' . $kategoriLabel . ' telah dihapus.</p>';
            $message .= '</div>';
            
            return $this->response->setJSON([
                'success' => true,
                'message' => $message,
                'deleted_count' => $deletedCount,
                'kategori' => $kategoriLabel
            ]);
            
        } catch (\Exception $e) {
            $db->transRollback();
            
            $errorMessage = $e->getMessage();
            log_message('error', 'Error bulk delete: ' . $errorMessage);
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $errorMessage,
                'error_detail' => ENVIRONMENT === 'development' ? $errorMessage : null
            ]);
        }
    }
}
