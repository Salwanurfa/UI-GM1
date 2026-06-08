<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;

class Waste extends BaseController
{
    public function __construct()
    {
        // Tidak perlu service lagi - langsung pakai model seperti Limbah Cair
    }

    public function index()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            $session = session();
            $user = $session->get('user');

            // AMBIL DATA USER INI SAJA - sama seperti Limbah Cair
            $wasteModel = new \App\Models\WasteModel();
            $waste_list = $wasteModel
                ->where('user_id', $user['id'])
                ->orderBy('id', 'DESC')
                ->findAll();

            // Get user's unit
            $unitModel = new \App\Models\UnitModel();
            $unit = $unitModel->find($user['unit_id']) ?? ['nama_unit' => 'Unit'];
            
            $hargaModel = new \App\Models\HargaSampahModel();
            
            // Pagination untuk categories (Informasi Harga Sampah - cards display)
            $perPage = 5; // Maksimal 5 cards per halaman
            $categories = $hargaModel->where('status_aktif', 1)
                                    ->orderBy('jenis_sampah', 'ASC')
                                    ->paginate($perPage, 'harga');
            $pagerHarga = $hargaModel->pager;
            
            // Semua categories untuk dropdown form (tidak pakai pagination)
            $allCategories = $hargaModel->where('status_aktif', 1)
                                       ->orderBy('jenis_sampah', 'ASC')
                                       ->findAll();
            
            // HITUNG JUMLAH DATA PER STATUS
            // HITUNG JUMLAH DATA PER STATUS - GUNAKAN whereIn untuk lebih aman
            $count_draft_dikirim = $wasteModel
                ->where('user_id', $user['id'])
                ->whereIn('status', ['draft', 'dikirim_ke_tps'])
                ->countAllResults();

            $count_disetujui_tps = $wasteModel
                ->where('user_id', $user['id'])
                ->where('status', 'disetujui_tps')
                ->countAllResults();

            $count_ditolak_tps = $wasteModel
                ->where('user_id', $user['id'])
                ->where('status', 'ditolak_tps')
                ->countAllResults();

            $count_disetujui_admin = $wasteModel
                ->where('user_id', $user['id'])
                ->where('status', 'disetujui_admin')
                ->countAllResults();

            $count_ditolak_admin = $wasteModel
                ->where('user_id', $user['id'])
                ->where('status', 'ditolak_admin')
                ->countAllResults();
            
            // Stats untuk dashboard
            $stats = [
                'total_entries' => count($waste_list),
                'pending_count' => $count_draft_dikirim,
                'approved_count' => $count_disetujui_admin,
                'rejected_count' => $count_ditolak_admin
            ];
            
            $viewData = [
                'title' => 'Manajemen Sampah User',
                'user' => $user,
                'unit' => $unit,
                'waste_list' => $waste_list,
                'categories' => $categories,
                'allCategories' => $allCategories,
                'pagerHarga' => $pagerHarga,
                'stats' => $stats,
                'count_draft_dikirim' => $count_draft_dikirim,
                'count_disetujui_tps' => $count_disetujui_tps,
                'count_ditolak_tps' => $count_ditolak_tps,
                'count_disetujui_admin' => $count_disetujui_admin,
                'count_ditolak_admin' => $count_ditolak_admin,
                'recent_activities' => []
            ];

            return view('user/waste', $viewData);

        } catch (\Exception $e) {
            log_message('error', 'User Waste Error: ' . $e->getMessage());
            
            return view('user/waste', [
                'title' => 'Manajemen Sampah User',
                'user' => session()->get('user'),
                'unit' => ['nama_unit' => 'Unit'],
                'waste_list' => [],
                'categories' => [],
                'allCategories' => [],
                'pagerHarga' => null,
                'stats' => [],
                'count_draft_dikirim' => 0,
                'count_disetujui_tps' => 0,
                'count_ditolak_tps' => 0,
                'count_disetujui_admin' => 0,
                'count_ditolak_admin' => 0,
                'error' => 'Terjadi kesalahan saat memuat data sampah'
            ]);
        }
    }

    public function get($id)
    {
        try {
            if (!$this->validateSession()) {
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Unauthorized']);
            }

            $wasteModel = new \App\Models\WasteModel();
            $waste = $wasteModel->find($id);
            
            if (!$waste) {
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON([
                        'success' => false,
                        'message' => 'Data tidak ditemukan'
                    ]);
            }
            
            // Check if user owns this data or has permission
            $user = session()->get('user');
            $canEdit = false;
            
            // User can edit their own data
            if (isset($waste['user_id']) && $waste['user_id'] == $user['id']) {
                $canEdit = true;
            }
            
            // User from same unit can edit
            if (isset($waste['unit_id']) && isset($user['unit_id']) && $waste['unit_id'] == $user['unit_id']) {
                $canEdit = true;
            }
            
            // Admin can edit all
            if (in_array($user['role'], ['admin_pusat', 'super_admin'])) {
                $canEdit = true;
            }
            
            if (!$canEdit) {
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses ke data ini'
                    ]);
            }

            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'success' => true,
                    'data' => $waste
                ]);

        } catch (\Exception $e) {
            log_message('error', 'User Waste Get Error: ' . $e->getMessage());
            
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
        }
    }

    public function save()
    {
        try {
            log_message('info', '=== User Waste Save START ===');
            
            if (!$this->validateSession()) {
                log_message('warning', 'User Waste Save - Session invalid');
                return redirect()->to('/auth/login')->with('error', 'Session tidak valid');
            }

            $user = session()->get('user');
            if (!$user || !isset($user['id'])) {
                log_message('error', 'User Waste Save - No user in session');
                return redirect()->back()->with('error', 'User session tidak valid');
            }

            $postData = $this->request->getPost();
            log_message('info', 'POST Data: ' . json_encode($postData));

            // Handle file upload
            $foto = $this->request->getFile('foto');
            $fotoBukti = null;
            
            log_message('info', 'File upload check - File object: ' . ($foto ? 'exists' : 'null'));
            
            if ($foto) {
                log_message('info', 'File details - Name: ' . $foto->getName() . ', Size: ' . $foto->getSize() . ', Type: ' . $foto->getMimeType());
                log_message('info', 'File validation - isValid: ' . ($foto->isValid() ? 'yes' : 'no') . ', hasMoved: ' . ($foto->hasMoved() ? 'yes' : 'no'));
                
                if ($foto->getError() !== UPLOAD_ERR_OK) {
                    $errorMessages = [
                        UPLOAD_ERR_INI_SIZE => 'File terlalu besar (melebihi upload_max_filesize di php.ini)',
                        UPLOAD_ERR_FORM_SIZE => 'File terlalu besar (melebihi MAX_FILE_SIZE di form)',
                        UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian',
                        UPLOAD_ERR_NO_FILE => 'Tidak ada file yang diupload',
                        UPLOAD_ERR_NO_TMP_DIR => 'Folder temporary tidak ditemukan',
                        UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk',
                        UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh ekstensi PHP'
                    ];
                    $errorMsg = $errorMessages[$foto->getError()] ?? 'Error upload tidak diketahui (code: ' . $foto->getError() . ')';
                    log_message('error', 'Upload error: ' . $errorMsg);
                    return redirect()->back()->with('error', 'Error upload foto: ' . $errorMsg)->withInput();
                }
            }
            
            if ($foto && $foto->isValid() && !$foto->hasMoved()) {
                // Validate file type
                $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!in_array($foto->getMimeType(), $allowedMimes)) {
                    log_message('error', 'Invalid mime type: ' . $foto->getMimeType());
                    return redirect()->back()->with('error', 'Format foto harus JPG, PNG, atau JPEG. Format Anda: ' . $foto->getMimeType())->withInput();
                }
                
                // Validate file size (2MB)
                if ($foto->getSize() > 2048000) {
                    $sizeMB = round($foto->getSize() / 1048576, 2);
                    log_message('error', 'File too large: ' . $sizeMB . 'MB');
                    return redirect()->back()->with('error', 'Ukuran foto maksimal 2MB. Ukuran file Anda: ' . $sizeMB . 'MB')->withInput();
                }
                
                // Create upload directory if not exists
                $uploadPath = FCPATH . 'uploads/waste/';
                if (!is_dir($uploadPath)) {
                    if (!mkdir($uploadPath, 0755, true)) {
                        log_message('error', 'Failed to create upload directory: ' . $uploadPath);
                        return redirect()->back()->with('error', 'Gagal membuat folder upload. Hubungi administrator.')->withInput();
                    }
                    log_message('info', 'Upload directory created: ' . $uploadPath);
                }
                
                // Check if directory is writable
                if (!is_writable($uploadPath)) {
                    log_message('error', 'Upload directory not writable: ' . $uploadPath);
                    return redirect()->back()->with('error', 'Folder upload tidak memiliki izin tulis. Hubungi administrator.')->withInput();
                }
                
                // Generate unique filename
                $newName = 'waste_' . $user['id'] . '_' . time() . '.' . $foto->getExtension();
                log_message('info', 'Attempting to move file to: ' . $uploadPath . $newName);
                
                // Move file
                if ($foto->move($uploadPath, $newName)) {
                    $fotoBukti = 'uploads/waste/' . $newName;
                    log_message('info', '✓ File uploaded successfully: ' . $fotoBukti);
                } else {
                    log_message('error', 'Failed to move uploaded file. Error: ' . $foto->getErrorString());
                    return redirect()->back()->with('error', 'Gagal memindahkan file: ' . $foto->getErrorString())->withInput();
                }
            } else {
                $reason = 'Unknown';
                if (!$foto) {
                    $reason = 'File object tidak ditemukan';
                } elseif (!$foto->isValid()) {
                    $reason = 'File tidak valid (Error: ' . $foto->getErrorString() . ')';
                } elseif ($foto->hasMoved()) {
                    $reason = 'File sudah dipindahkan sebelumnya';
                }
                log_message('warning', 'No valid file uploaded. Reason: ' . $reason);
                return redirect()->back()->with('error', 'Foto bukti wajib diupload. Detail: ' . $reason)->withInput();
            }

            // Get category info
            $hargaModel = new \App\Models\HargaSampahModel();
            $category = $hargaModel->find($postData['kategori_id']);
            
            if (!$category) {
                log_message('error', 'Kategori tidak ditemukan: ' . $postData['kategori_id']);
                return redirect()->back()->with('error', 'Kategori sampah tidak ditemukan');
            }

            // Tentukan status - SAMA SEPERTI LIMBAH CAIR
            $action = $postData['action'] ?? 'draft';
            $status = ($action === 'kirim') ? 'dikirim_ke_tps' : 'draft';
            
            log_message('info', 'Action: ' . $action . ', Status: ' . $status);
            
            // Get satuan from input
            $satuan = $postData['satuan'] ?? 'kg';
            $beratKg = isset($postData['berat_kg']) ? (float)$postData['berat_kg'] : 0;
            
            // Siapkan data untuk insert - LANGSUNG SEPERTI LIMBAH CAIR
            $data = [
                'unit_id'         => (int)$user['unit_id'],
                'user_id'         => (int)$user['id'],  // CRITICAL: user_id
                'berat_kg'        => $beratKg,
                'tanggal'         => date('Y-m-d'),
                'jenis_sampah'    => $category['jenis_sampah'],
                'nama_sampah'     => $category['nama_jenis'],
                'satuan'          => $satuan,
                'jumlah'          => $beratKg,
                'gedung'          => 'User Unit',
                'kategori_sampah' => $category['dapat_dijual'] ? 'bisa_dijual' : 'tidak_bisa_dijual',
                'status'          => $status,
                'foto_bukti'      => $fotoBukti,
            ];
            
            // Add nilai_rupiah if can be sold
            if ($category['dapat_dijual']) {
                $data['nilai_rupiah'] = $beratKg * $category['harga_per_satuan'];
            }
            
            log_message('info', 'Data yang akan disimpan: ' . json_encode($data));
            
            // INSERT ke database - LANGSUNG SEPERTI LIMBAH CAIR
            $wasteModel = new \App\Models\WasteModel();
            if (!$wasteModel->insert($data)) {
                // Jika gagal, tampilkan error
                log_message('error', 'Insert failed: ' . json_encode($wasteModel->errors()));
                dd($wasteModel->errors());
            }
            
            log_message('info', '✓ BERHASIL INSERT');
            
            // Redirect dengan pesan sukses - SAMA SEPERTI LIMBAH CAIR
            return redirect()->to(base_url('user/waste'))->with('success', 'Data Sampah berhasil disimpan!');

        } catch (\Exception $e) {
            log_message('error', 'User Waste Save Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            log_message('info', '=== User Waste Edit START ===');
            log_message('info', 'ID: ' . $id);
            
            if (!$this->validateSession()) {
                log_message('warning', 'Session invalid');
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Session invalid']);
            }

            $user = session()->get('user');
            $postData = $this->request->getPost();
            
            log_message('info', 'POST: ' . json_encode($postData));
            log_message('info', 'User ID: ' . ($user['id'] ?? 'null'));
            
            // Ambil ID dari POST
            $wasteId = $id;
            
            if (!$wasteId) {
                log_message('error', 'GAGAL: ID tidak ditemukan');
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'ID tidak ditemukan']);
            }
            
            // Get waste
            $wasteModel = new \App\Models\WasteModel();
            $waste = $wasteModel->find($wasteId);
            log_message('info', 'Waste found: ' . ($waste ? 'yes' : 'no'));
            
            if (!$waste) {
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
            }
            
            // Cek ownership
            if ($waste['user_id'] != $user['id']) {
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Data bukan milik Anda']);
            }
            
            if (!in_array($waste['status'], ['draft', 'perlu_revisi', 'ditolak_tps'])) {
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Data sudah disubmit tidak dapat diedit']);
            }
            
            // Get category
            $hargaModel = new \App\Models\HargaSampahModel();
            $category = $hargaModel->find($postData['kategori_id']);
            log_message('info', 'Category found: ' . ($category ? 'yes' : 'no'));
            
            if (!$category) {
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Kategori tidak ditemukan']);
            }
            
            // Tentukan status berdasarkan action - SAMA SEPERTI LIMBAH CAIR
            $action = $postData['action'] ?? 'draft';
            $status = ($action === 'kirim') ? 'dikirim_ke_tps' : 'draft';
            
            log_message('info', 'Action: ' . $action . ', Status: ' . $status);
            
            // berat_kg sudah dalam kg dari frontend
            $beratKg = isset($postData['berat_kg']) ? (float)$postData['berat_kg'] : $waste['berat_kg'];
            $satuan = $postData['satuan'] ?? $waste['satuan'] ?? 'kg';
            
            $updateData = [
                'berat_kg'        => $beratKg,
                'jumlah'          => $beratKg,
                'satuan'          => $satuan,
                'jenis_sampah'    => $category['jenis_sampah'],
                'kategori_sampah' => $category['dapat_dijual'] ? 'bisa_dijual' : 'tidak_dijual',
                'nilai_rupiah'    => $category['dapat_dijual'] ? ($beratKg * $category['harga_per_satuan']) : 0,
                'status'          => $status
            ];
            
            log_message('info', 'Update data: ' . json_encode($updateData));
            
            // Update - LANGSUNG SEPERTI LIMBAH CAIR
            $result = $wasteModel->update($wasteId, $updateData);
            log_message('info', 'Update result: ' . ($result ? 'success' : 'failed'));
            
            if ($result) {
                $message = $status === 'dikirim_ke_tps' 
                    ? 'Data berhasil diupdate dan dikirim ke TPS' 
                    : 'Data berhasil diupdate sebagai draft';
                log_message('info', '=== User Waste Edit SUCCESS ===');
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => true, 'message' => $message]);
            }
            
            log_message('error', '=== User Waste Edit FAILED ===');
            return $this->response
                ->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Gagal mengupdate data']);

        } catch (\Throwable $e) {
            log_message('error', '=== User Waste Edit EXCEPTION ===');
            log_message('error', 'Message: ' . $e->getMessage());
            log_message('error', 'File: ' . $e->getFile() . ':' . $e->getLine());
            log_message('error', 'Trace: ' . $e->getTraceAsString());
            
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
        }
    }

    public function delete($id)
    {
        try {
            log_message('info', 'User Delete Waste - ID: ' . $id);
            
            if (!$this->validateSession()) {
                log_message('warning', 'User Delete Waste - Session invalid');
                return $this->response
                    ->setStatusCode(401)
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Session invalid']);
            }

            $wasteModel = new \App\Models\WasteModel();
            $result = $wasteModel->delete($id);
            
            if ($result) {
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => true, 'message' => 'Data berhasil dihapus']);
            } else {
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Gagal menghapus data']);
            }

        } catch (\Exception $e) {
            log_message('error', 'User Waste Delete Error: ' . $e->getMessage());
            
            return $this->response
                ->setStatusCode(500)
                ->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function export()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            $user = session()->get('user');
            
            // Ambil data waste user
            $wasteModel = new \App\Models\WasteModel();
            $wasteData = $wasteModel
                ->where('user_id', $user['id'])
                ->orderBy('tanggal', 'DESC')
                ->findAll();

            // Generate CSV
            $csv = "No,Tanggal,Jenis Sampah,Berat (kg),Satuan,Kategori,Nilai (Rp),Status\n";
            $no = 1;
            foreach ($wasteData as $waste) {
                $csv .= sprintf(
                    "%d,%s,%s,%.2f,%s,%s,%.2f,%s\n",
                    $no++,
                    $waste['tanggal'],
                    $waste['jenis_sampah'],
                    $waste['berat_kg'],
                    $waste['satuan'],
                    $waste['kategori_sampah'],
                    $waste['nilai_rupiah'] ?? 0,
                    $waste['status']
                );
            }

            $filename = 'Data_Sampah_' . date('YmdHis') . '.csv';
            
            return $this->response
                ->setHeader('Content-Type', 'text/csv')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->setBody($csv);

        } catch (\Exception $e) {
            log_message('error', 'User Waste Export Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat export data');
        }
    }
    
    public function exportPdf()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            $user = session()->get('user');
            
            // Ambil data waste user
            $wasteModel = new \App\Models\WasteModel();
            $wasteData = $wasteModel
                ->where('user_id', $user['id'])
                ->orderBy('tanggal', 'DESC')
                ->findAll();

            // Get user's unit
            $unitModel = new \App\Models\UnitModel();
            $unit = $unitModel->find($user['unit_id']) ?? ['nama_unit' => 'Unit'];

            $data = [
                'title' => 'Laporan Data Sampah',
                'user' => $user,
                'unit' => $unit,
                'waste_data' => $wasteData,
                'tanggal_cetak' => date('d/m/Y H:i:s')
            ];

            // Load view untuk PDF
            $html = view('user/waste_pdf', $data);
            
            // Generate PDF menggunakan Dompdf
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            
            $filename = 'Data_Sampah_' . date('YmdHis') . '.pdf';
            $dompdf->stream($filename, ['Attachment' => true]);
            exit;

        } catch (\Exception $e) {
            log_message('error', 'User Waste Export PDF Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat export PDF');
        }
    }

    public function exportExcel()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            $user = session()->get('user');
            
            // Ambil data waste user
            $wasteModel = new \App\Models\WasteModel();
            $wasteData = $wasteModel
                ->where('user_id', $user['id'])
                ->orderBy('tanggal', 'DESC')
                ->findAll();

            // Load PhpSpreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set header
            $sheet->setCellValue('A1', 'No');
            $sheet->setCellValue('B1', 'Tanggal');
            $sheet->setCellValue('C1', 'Jenis Sampah');
            $sheet->setCellValue('D1', 'Berat (kg)');
            $sheet->setCellValue('E1', 'Satuan');
            $sheet->setCellValue('F1', 'Kategori');
            $sheet->setCellValue('G1', 'Nilai (Rp)');
            $sheet->setCellValue('H1', 'Status');
            
            // Style header
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '667eea']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ];
            $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);
            
            // Fill data
            $row = 2;
            $no = 1;
            foreach ($wasteData as $data) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($data['tanggal'])));
                $sheet->setCellValue('C' . $row, $data['jenis_sampah']);
                $sheet->setCellValue('D' . $row, $data['berat_kg']);
                $sheet->setCellValue('E' . $row, $data['satuan']);
                $sheet->setCellValue('F' . $row, $data['kategori_sampah']);
                $sheet->setCellValue('G' . $row, $data['nilai_rupiah'] ?? 0);
                $sheet->setCellValue('H' . $row, ucfirst(str_replace('_', ' ', $data['status'])));
                $row++;
            }
            
            // Auto size columns
            foreach (range('A', 'H') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Generate file
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $filename = 'Data_Sampah_' . date('YmdHis') . '.xlsx';
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            log_message('error', 'User Waste Export Excel Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export Excel: ' . $e->getMessage());
        }
    }

    private function validateSession(): bool
    {
        $session = session();
        $user = $session->get('user');
        
        return $session->get('isLoggedIn') && 
               isset($user['id'], $user['role'], $user['unit_id']) &&
               $user['role'] === 'user';
    }
}