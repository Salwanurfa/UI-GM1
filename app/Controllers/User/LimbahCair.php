<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;

class LimbahCair extends BaseController
{
    protected $limbahCairModel;

    protected $masterLimbahCairModel;

    public function __construct()
    {
        $this->limbahCairModel = new \App\Models\LimbahCairModel();
        $this->masterLimbahCairModel = new \App\Models\MasterLimbahCairModel();
    }

    // AMBIL DATA USER SAJA
    public function index()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            $session = session();
            $user = $session->get('user');

            // AMBIL DATA USER INI SAJA
            $limbah_cair = $this->limbahCairModel
                ->where('id_user', $user['id'])
                ->orderBy('id', 'DESC')
                ->findAll();

            // Get user's unit
            $unitModel = new \App\Models\UnitModel();
            $unit = $unitModel->find($user['unit_id']) ?? ['nama_unit' => 'Unit'];

            // AMBIL DATA MASTER LIMBAH CAIR DARI DATABASE
            $master_limbah_cair = $this->masterLimbahCairModel
                ->orderBy('nama_limbah', 'ASC')
                ->findAll();

            // HITUNG JUMLAH DATA PER STATUS
            $count_draft_dikirim = $this->limbahCairModel
                ->where('id_user', $user['id'])
                ->groupStart()
                    ->where('status', 'draft')
                    ->orWhere('status', 'dikirim_ke_tps')
                ->groupEnd()
                ->countAllResults();

            $count_disetujui_tps = $this->limbahCairModel
                ->where('id_user', $user['id'])
                ->where('status', 'disetujui_tps')
                ->countAllResults();

            $count_ditolak_tps = $this->limbahCairModel
                ->where('id_user', $user['id'])
                ->where('status', 'ditolak_tps')
                ->countAllResults();

            $count_disetujui_admin = $this->limbahCairModel
                ->where('id_user', $user['id'])
                ->where('status', 'disetujui_admin')
                ->countAllResults();

            $count_ditolak_admin = $this->limbahCairModel
                ->where('id_user', $user['id'])
                ->where('status', 'ditolak_admin')
                ->countAllResults();

            // HITUNG STATISTIK UNTUK CARD
            // Total Transaksi
            $total_transaksi = count($limbah_cair);
            
            // Total Air Terolah (m³) - dari timbulan yang sudah disetujui
            $total_air_terolah = $this->limbahCairModel
                ->selectSum('timbulan')
                ->where('id_user', $user['id'])
                ->whereIn('status', ['disetujui_tps', 'disetujui_admin'])
                ->get()
                ->getRow()
                ->timbulan ?? 0;
            
            // Jumlah Lokasi Pembuangan (unique locations)
            $jumlah_lokasi = $this->limbahCairModel
                ->select('lokasi')
                ->where('id_user', $user['id'])
                ->where('lokasi !=', '')
                ->groupBy('lokasi')
                ->countAllResults();
            
            // Rasio Efisiensi (persentase data yang disetujui dari total)
            $total_disetujui = $count_disetujui_tps + $count_disetujui_admin;
            $rasio_efisiensi = $total_transaksi > 0 ? round(($total_disetujui / $total_transaksi) * 100, 2) : 0;

            $data = [
                'title'                  => 'Limbah Cair - User',
                'user'                   => $user,
                'unit'                   => $unit,
                'limbah_cair'            => $limbah_cair,
                'master_limbah_cair'     => $master_limbah_cair, // DATA MASTER UNTUK DROPDOWN
                'count_draft_dikirim'    => $count_draft_dikirim,
                'count_disetujui_tps'    => $count_disetujui_tps,
                'count_ditolak_tps'      => $count_ditolak_tps,
                'count_disetujui_admin'  => $count_disetujui_admin,
                'count_ditolak_admin'    => $count_ditolak_admin,
                // Statistik untuk card
                'total_transaksi'        => $total_transaksi,
                'total_air_terolah'      => $total_air_terolah,
                'jumlah_lokasi'          => $jumlah_lokasi,
                'rasio_efisiensi'        => $rasio_efisiensi,
            ];

            return view('user/limbah_cair', $data);

        } catch (\Exception $e) {
            log_message('error', 'User Limbah Cair Error: ' . $e->getMessage());
            
            return view('user/limbah_cair', [
                'title' => 'Limbah Cair - User',
                'user' => session()->get('user'),
                'unit' => ['nama_unit' => 'Unit'],
                'limbah_cair' => [],
                'master_limbah_cair' => [], // DATA MASTER KOSONG UNTUK ERROR STATE
                'count_draft_dikirim' => 0,
                'count_disetujui_tps' => 0,
                'count_ditolak_tps' => 0,
                'count_disetujui_admin' => 0,
                'count_ditolak_admin' => 0,
                'total_transaksi' => 0,
                'total_air_terolah' => 0,
                'jumlah_lokasi' => 0,
                'rasio_efisiensi' => 0,
                'error' => 'Terjadi kesalahan saat memuat data'
            ]);
        }
    }

    public function save()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login')->with('error', 'Session invalid');
            }

            $user = session()->get('user');
            if (!$user || !isset($user['id'])) {
                return redirect()->back()->with('error', 'User session tidak valid');
            }

            $postData = $this->request->getPost();
            
            // Tentukan status
            $action = $postData['action'] ?? 'simpan_draf';
            $status = ($action === 'kirim_ke_tps') ? 'dikirim_ke_tps' : 'draft';
            
            // Siapkan data untuk insert
            $data = [
                'id_user'        => (int)$user['id'],
                'tanggal_input'  => date('Y-m-d H:i:s'),
                'lokasi'         => $postData['lokasi'] ?? '',
                'nama_limbah'    => $postData['nama_limbah'] ?? '',
                'kode_limbah'    => $postData['kode_limbah'] ?? '',
                'tingkat_bahaya' => $postData['tingkat_bahaya'] ?? '',
                'karakteristik'  => $postData['karakteristik'] ?? '',
                'pengolahan'     => $postData['pengolahan'] ?? '',
                'timbulan'       => isset($postData['timbulan']) ? (float)$postData['timbulan'] : 0,
                'satuan'         => $postData['satuan'] ?? 'L/bulan',
                'bentuk_fisik'   => $postData['bentuk_fisik'] ?? 'Cair',
                'kemasan'        => $postData['kemasan'] ?? '',
                'ph'             => isset($postData['ph']) && $postData['ph'] !== '' ? (float)$postData['ph'] : null,
                'bod'            => isset($postData['bod']) && $postData['bod'] !== '' ? (float)$postData['bod'] : null,
                'cod'            => isset($postData['cod']) && $postData['cod'] !== '' ? (float)$postData['cod'] : null,
                'tss'            => isset($postData['tss']) && $postData['tss'] !== '' ? (float)$postData['tss'] : null,
                'keterangan'     => $postData['keterangan'] ?? '',
                'status'         => $status,
            ];
            
            // INSERT ke database
            if (!$this->limbahCairModel->insert($data)) {
                // Jika gagal, tampilkan error
                dd($this->limbahCairModel->errors());
            }
            
            // Redirect dengan pesan sukses
            return redirect()->to(base_url('user/limbah-cair'))->with('success', 'Data Limbah Cair berhasil disimpan!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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

            $limbah = $this->limbahCairModel->find($id);
            
            if (!$limbah) {
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
            }

            return $this->response
                ->setContentType('application/json')
                ->setJSON(['success' => true, 'data' => $limbah]);

        } catch (\Exception $e) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        try {
            if (!$this->validateSession()) {
                return $this->response
                    ->setContentType('application/json')
                    ->setJSON(['success' => false, 'message' => 'Unauthorized']);
            }

            $result = $this->limbahCairModel->delete($id);
            
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
            return $this->response
                ->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function update()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login')->with('error', 'Session tidak valid');
            }

            $user = session()->get('user');
            $postData = $this->request->getPost();
            
            // Log untuk debugging
            log_message('info', '========== UPDATE LIMBAH CAIR ==========');
            log_message('info', 'POST Data: ' . json_encode($postData));
            log_message('info', 'User ID: ' . $user['id']);
            
            // Ambil ID dari POST - PENTING!
            $id = $postData['id'] ?? null;
            
            if (!$id) {
                log_message('error', 'GAGAL: ID tidak ditemukan di POST data');
                return redirect()->back()->with('error', 'ID tidak ditemukan. Pastikan data sudah dipilih.');
            }
            
            log_message('info', 'ID ditemukan: ' . $id);

            // Cek apakah data ada di database
            $existing = $this->limbahCairModel->find($id);
            if (!$existing) {
                log_message('error', 'GAGAL: Data dengan ID ' . $id . ' tidak ditemukan di database');
                return redirect()->back()->with('error', 'Data tidak ditemukan di database');
            }
            
            log_message('info', 'Data existing ditemukan');
            
            // Cek ownership
            if ($existing['id_user'] != $user['id']) {
                log_message('error', 'GAGAL: Data bukan milik user. Owner: ' . $existing['id_user'] . ', Current: ' . $user['id']);
                return redirect()->back()->with('error', 'Data bukan milik Anda');
            }
            
            log_message('info', 'Ownership verified');

            // Tentukan status berdasarkan action
            $action = $postData['action'] ?? 'draft';
            $status = ($action === 'kirim' || $action === 'kirim_ke_tps') ? 'dikirim_ke_tps' : 'draft';
            
            log_message('info', 'Action: ' . $action . ', Status: ' . $status);
            
            // Siapkan data untuk update - SEMUA FIELD HARUS ADA!
            $data = [
                'lokasi'         => $postData['lokasi'] ?? $existing['lokasi'],
                'nama_limbah'    => $postData['nama_limbah'] ?? $existing['nama_limbah'],
                'kode_limbah'    => $postData['kode_limbah'] ?? $existing['kode_limbah'],
                'tingkat_bahaya' => $postData['tingkat_bahaya'] ?? $existing['tingkat_bahaya'],
                'karakteristik'  => $postData['karakteristik'] ?? $existing['karakteristik'],
                'pengolahan'     => $postData['pengolahan'] ?? $existing['pengolahan'],
                'timbulan'       => isset($postData['timbulan']) && $postData['timbulan'] !== '' ? (float)$postData['timbulan'] : $existing['timbulan'],
                'satuan'         => $postData['satuan'] ?? $existing['satuan'],
                'bentuk_fisik'   => $postData['bentuk_fisik'] ?? $existing['bentuk_fisik'],
                'kemasan'        => $postData['kemasan'] ?? $existing['kemasan'],
                'ph'             => isset($postData['ph']) && $postData['ph'] !== '' ? (float)$postData['ph'] : null,
                'bod'            => isset($postData['bod']) && $postData['bod'] !== '' ? (float)$postData['bod'] : null,
                'cod'            => isset($postData['cod']) && $postData['cod'] !== '' ? (float)$postData['cod'] : null,
                'tss'            => isset($postData['tss']) && $postData['tss'] !== '' ? (float)$postData['tss'] : null,
                'keterangan'     => $postData['keterangan'] ?? $existing['keterangan'],
                'status'         => $status,
            ];
            
            log_message('info', 'Data yang akan diupdate:');
            log_message('info', json_encode($data, JSON_PRETTY_PRINT));
            
            // UPDATE ke database - PAKSA SIMPAN!
            $updateResult = $this->limbahCairModel->update($id, $data);
            
            log_message('info', 'Update result: ' . ($updateResult ? 'TRUE' : 'FALSE'));
            
            if ($updateResult) {
                log_message('info', '✓ BERHASIL UPDATE ID: ' . $id);
                
                // Verifikasi data berubah
                $updated = $this->limbahCairModel->find($id);
                log_message('info', 'Data setelah update: ' . json_encode($updated));
                
                // Redirect dengan pesan sukses
                return redirect()->to(base_url('user/limbah-cair'))
                    ->with('success', 'Data Limbah Cair Berhasil Diperbarui!');
                    
            } else {
                // Jika gagal, tampilkan error dari model
                $errors = $this->limbahCairModel->errors();
                log_message('error', '✗ GAGAL UPDATE!');
                log_message('error', 'Model errors: ' . json_encode($errors));
                
                // Tampilkan error untuk debugging
                dd([
                    'message' => 'Gagal mengupdate data',
                    'errors' => $errors,
                    'data' => $data
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Update exception: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function exportExcel()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            $user = session()->get('user');
            
            // Ambil data limbah cair user
            $limbahCair = $this->limbahCairModel
                ->where('id_user', $user['id'])
                ->orderBy('tanggal_input', 'DESC')
                ->findAll();

            // Load PhpSpreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set header
            $sheet->setCellValue('A1', 'No');
            $sheet->setCellValue('B1', 'Tanggal Input');
            $sheet->setCellValue('C1', 'Lokasi');
            $sheet->setCellValue('D1', 'Nama Limbah');
            $sheet->setCellValue('E1', 'Kode Limbah');
            $sheet->setCellValue('F1', 'Timbulan');
            $sheet->setCellValue('G1', 'Satuan');
            $sheet->setCellValue('H1', 'pH');
            $sheet->setCellValue('I1', 'BOD (mg/L)');
            $sheet->setCellValue('J1', 'COD (mg/L)');
            $sheet->setCellValue('K1', 'TSS (mg/L)');
            $sheet->setCellValue('L1', 'Status');
            
            // Style header
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '667eea']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ];
            $sheet->getStyle('A1:L1')->applyFromArray($headerStyle);
            
            // Fill data
            $row = 2;
            $no = 1;
            foreach ($limbahCair as $data) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, date('d/m/Y H:i', strtotime($data['tanggal_input'])));
                $sheet->setCellValue('C' . $row, $data['lokasi']);
                $sheet->setCellValue('D' . $row, $data['nama_limbah']);
                $sheet->setCellValue('E' . $row, $data['kode_limbah']);
                $sheet->setCellValue('F' . $row, $data['timbulan']);
                $sheet->setCellValue('G' . $row, $data['satuan']);
                $sheet->setCellValue('H' . $row, $data['ph'] ?? '-');
                $sheet->setCellValue('I' . $row, $data['bod'] ?? '-');
                $sheet->setCellValue('J' . $row, $data['cod'] ?? '-');
                $sheet->setCellValue('K' . $row, $data['tss'] ?? '-');
                $sheet->setCellValue('L' . $row, ucfirst(str_replace('_', ' ', $data['status'])));
                $row++;
            }
            
            // Auto size columns
            foreach (range('A', 'L') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Generate file
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $filename = 'Limbah_Cair_' . date('YmdHis') . '.xlsx';
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            log_message('error', 'Export Excel Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export Excel: ' . $e->getMessage());
        }
    }

    public function exportPdf()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            $user = session()->get('user');
            
            // Ambil data limbah cair user
            $limbahCair = $this->limbahCairModel
                ->where('id_user', $user['id'])
                ->orderBy('tanggal_input', 'DESC')
                ->findAll();

            // Get user's unit
            $unitModel = new \App\Models\UnitModel();
            $unit = $unitModel->find($user['unit_id']) ?? ['nama_unit' => 'Unit'];

            $data = [
                'title' => 'Laporan Limbah Cair',
                'user' => [
                    'nama' => $user['nama'] ?? $user['nama_lengkap'] ?? $user['username'] ?? 'User',
                    'unit_id' => $user['unit_id'] ?? null
                ],
                'unit' => $unit,
                'limbah_cair' => $limbahCair,
                'tanggal_cetak' => date('d/m/Y H:i:s')
            ];

            // Load view untuk PDF
            $html = view('user/limbah_cair_pdf', $data);
            
            // Generate PDF menggunakan Dompdf
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            
            $filename = 'Limbah_Cair_' . date('YmdHis') . '.pdf';
            $dompdf->stream($filename, ['Attachment' => true]);
            exit;

        } catch (\Exception $e) {
            log_message('error', 'Export PDF Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export PDF: ' . $e->getMessage());
        }
    }

    private function validateSession(): bool
    {
        $session = session();
        $user    = $session->get('user');

        return $session->get('isLoggedIn')
            && isset($user['id'], $user['role'], $user['unit_id'])
            && $user['role'] === 'user';
    }
}
