<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LimbahCairModel;
use App\Models\UnitModel;
use App\Models\UserModel;

class ManajemenLimbahCair extends BaseController
{
    protected $limbahCairModel;
    protected $unitModel;
    protected $userModel;

    public function __construct()
    {
        $this->limbahCairModel = new LimbahCairModel();
        $this->unitModel = new UnitModel();
        $this->userModel = new UserModel();
    }

    /**
     * Halaman utama list data limbah cair dari semua user
     */
    public function index()
    {
        try {
            // Validasi session admin
            if (!$this->validateAdminSession()) {
                return redirect()->to('/auth/login')->with('error', 'Akses ditolak');
            }

            $user = session()->get('user');

            // Ambil semua data limbah cair dengan JOIN ke tabel users dan unit
            $db = \Config\Database::connect();
            $builder = $db->table('limbah_cair lc');
            $builder->select('lc.*, u.nama_lengkap as nama_user, u.username, unit.nama_unit');
            $builder->join('users u', 'u.id = lc.id_user', 'left');
            $builder->join('unit', 'unit.id = u.unit_id', 'left');
            $builder->orderBy('lc.tanggal_input', 'DESC');
            
            $query = $builder->get();
            $limbahCairList = $query->getResultArray();

            // Hitung statistik
            $stats = [
                'total' => count($limbahCairList),
                'menunggu' => 0,
                'disetujui' => 0,
                'ditolak' => 0,
                'total_timbulan' => 0
            ];

            foreach ($limbahCairList as $item) {
                if ($item['status'] === 'dikirim_ke_tps') {
                    $stats['menunggu']++;
                } elseif (in_array($item['status'], ['disetujui_tps', 'disetujui_admin'])) {
                    $stats['disetujui']++;
                } elseif (in_array($item['status'], ['ditolak_tps', 'ditolak_admin'])) {
                    $stats['ditolak']++;
                }
                $stats['total_timbulan'] += $item['timbulan'] ?? 0;
            }

            $data = [
                'title' => 'Manajemen Limbah Cair',
                'user' => $user,
                'limbah_cair_list' => $limbahCairList,
                'stats' => $stats
            ];

            return view('admin_pusat/manajemen_limbah_cair/index', $data);

        } catch (\Exception $e) {
            log_message('error', 'Admin Limbah Cair Index Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get detail limbah cair by ID (untuk modal detail)
     */
    public function get($id)
    {
        try {
            if (!$this->validateAdminSession()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
            }

            $db = \Config\Database::connect();
            $builder = $db->table('limbah_cair lc');
            $builder->select('lc.*, u.nama_lengkap as nama_user, u.username, unit.nama_unit');
            $builder->join('users u', 'u.id = lc.id_user', 'left');
            $builder->join('unit', 'unit.id = u.unit_id', 'left');
            $builder->where('lc.id', $id);
            
            $query = $builder->get();
            $limbahCair = $query->getRowArray();

            if (!$limbahCair) {
                return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
            }

            return $this->response->setJSON(['success' => true, 'data' => $limbahCair]);

        } catch (\Exception $e) {
            log_message('error', 'Admin Get Limbah Cair Error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Setujui data limbah cair
     */
    public function approve($id)
    {
        try {
            if (!$this->validateAdminSession()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
            }

            $user = session()->get('user');
            $limbahCair = $this->limbahCairModel->find($id);

            if (!$limbahCair) {
                return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
            }

            // Update status menjadi disetujui_admin
            $updateData = [
                'status' => 'disetujui_admin',
                'reviewed_by' => $user['id'],
                'reviewed_at' => date('Y-m-d H:i:s')
            ];

            if ($this->limbahCairModel->update($id, $updateData)) {
                log_message('info', 'Admin ' . $user['username'] . ' menyetujui limbah cair ID: ' . $id);
                return $this->response->setJSON(['success' => true, 'message' => 'Data berhasil disetujui']);
            }

            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyetujui data']);

        } catch (\Exception $e) {
            log_message('error', 'Admin Approve Limbah Cair Error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Tolak data limbah cair
     */
    public function reject($id)
    {
        try {
            if (!$this->validateAdminSession()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
            }

            $user = session()->get('user');
            $limbahCair = $this->limbahCairModel->find($id);

            if (!$limbahCair) {
                return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
            }

            // Ambil alasan penolakan dari POST
            $alasan = $this->request->getPost('rejection_reason') ?? 'Data tidak sesuai';

            // Update status menjadi ditolak_admin
            $updateData = [
                'status' => 'ditolak_admin',
                'rejection_reason' => $alasan,
                'reviewed_by' => $user['id'],
                'reviewed_at' => date('Y-m-d H:i:s')
            ];

            if ($this->limbahCairModel->update($id, $updateData)) {
                log_message('info', 'Admin ' . $user['username'] . ' menolak limbah cair ID: ' . $id);
                return $this->response->setJSON(['success' => true, 'message' => 'Data berhasil ditolak']);
            }

            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menolak data']);

        } catch (\Exception $e) {
            log_message('error', 'Admin Reject Limbah Cair Error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Export PDF Laporan Limbah Cair
     */
    public function exportPdf()
    {
        try {
            if (!$this->validateAdminSession()) {
                return redirect()->to('/auth/login')->with('error', 'Akses ditolak');
            }

            $user = session()->get('user');

            // Ambil semua data limbah cair dengan JOIN
            $db = \Config\Database::connect();
            $builder = $db->table('limbah_cair lc');
            $builder->select('lc.*, u.nama_lengkap as nama_user, u.username, unit.nama_unit');
            $builder->join('users u', 'u.id = lc.id_user', 'left');
            $builder->join('unit', 'unit.id = u.unit_id', 'left');
            $builder->orderBy('lc.tanggal_input', 'DESC');
            
            $query = $builder->get();
            $limbahCairList = $query->getResultArray();

            // Hitung total timbulan
            $totalTimbulan = 0;
            foreach ($limbahCairList as $item) {
                $totalTimbulan += $item['timbulan'] ?? 0;
            }

            $data = [
                'title' => 'Laporan Limbah Cair - Admin Pusat',
                'limbah_cair' => $limbahCairList,
                'total_timbulan' => $totalTimbulan,
                'tanggal_cetak' => date('d/m/Y H:i:s'),
                'admin' => $user['nama_lengkap'] ?? $user['username']
            ];

            // Load view untuk PDF
            $html = view('admin_pusat/manajemen_limbah_cair/export_pdf', $data);
            
            // Generate PDF menggunakan Dompdf
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            
            $filename = 'Laporan_Limbah_Cair_Admin_' . date('YmdHis') . '.pdf';
            $dompdf->stream($filename, ['Attachment' => true]);
            exit;

        } catch (\Exception $e) {
            log_message('error', 'Admin Export PDF Limbah Cair Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export PDF: ' . $e->getMessage());
        }
    }

    /**
     * Export Excel Laporan Limbah Cair
     */
    public function exportExcel()
    {
        try {
            if (!$this->validateAdminSession()) {
                return redirect()->to('/auth/login')->with('error', 'Akses ditolak');
            }

            // Ambil semua data limbah cair dengan JOIN
            $db = \Config\Database::connect();
            $builder = $db->table('limbah_cair lc');
            $builder->select('lc.*, u.nama_lengkap as nama_user, u.username, unit.nama_unit');
            $builder->join('users u', 'u.id = lc.id_user', 'left');
            $builder->join('unit', 'unit.id = u.unit_id', 'left');
            $builder->orderBy('lc.tanggal_input', 'DESC');
            
            $query = $builder->get();
            $limbahCairList = $query->getResultArray();

            // Load PhpSpreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set header
            $sheet->setCellValue('A1', 'No');
            $sheet->setCellValue('B1', 'Tanggal Input');
            $sheet->setCellValue('C1', 'Unit/Jurusan');
            $sheet->setCellValue('D1', 'Nama User');
            $sheet->setCellValue('E1', 'Nama Limbah');
            $sheet->setCellValue('F1', 'Kode Limbah');
            $sheet->setCellValue('G1', 'Lokasi');
            $sheet->setCellValue('H1', 'Timbulan');
            $sheet->setCellValue('I1', 'Satuan');
            $sheet->setCellValue('J1', 'pH');
            $sheet->setCellValue('K1', 'BOD (mg/L)');
            $sheet->setCellValue('L1', 'COD (mg/L)');
            $sheet->setCellValue('M1', 'TSS (mg/L)');
            $sheet->setCellValue('N1', 'Status');
            
            // Style header
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '667eea']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ];
            $sheet->getStyle('A1:N1')->applyFromArray($headerStyle);
            
            // Fill data
            $row = 2;
            $no = 1;
            foreach ($limbahCairList as $data) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($data['tanggal_input'])));
                $sheet->setCellValue('C' . $row, $data['nama_unit'] ?? '-');
                $sheet->setCellValue('D' . $row, $data['nama_user'] ?? $data['username']);
                $sheet->setCellValue('E' . $row, $data['nama_limbah']);
                $sheet->setCellValue('F' . $row, $data['kode_limbah']);
                $sheet->setCellValue('G' . $row, $data['lokasi']);
                $sheet->setCellValue('H' . $row, $data['timbulan']);
                $sheet->setCellValue('I' . $row, $data['satuan']);
                $sheet->setCellValue('J' . $row, $data['ph'] ?? '-');
                $sheet->setCellValue('K' . $row, $data['bod'] ?? '-');
                $sheet->setCellValue('L' . $row, $data['cod'] ?? '-');
                $sheet->setCellValue('M' . $row, $data['tss'] ?? '-');
                $sheet->setCellValue('N' . $row, ucfirst(str_replace('_', ' ', $data['status'])));
                $row++;
            }
            
            // Auto size columns
            foreach (range('A', 'N') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Generate file
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $filename = 'Laporan_Limbah_Cair_Admin_' . date('YmdHis') . '.xlsx';
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            log_message('error', 'Admin Export Excel Limbah Cair Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export Excel: ' . $e->getMessage());
        }
    }

    /**
     * Validasi session admin
     */
    private function validateAdminSession(): bool
    {
        $session = session();
        $user = $session->get('user');
        
        return $session->get('isLoggedIn') && 
               isset($user['id'], $user['role']) &&
               in_array($user['role'], ['admin_pusat', 'super_admin']);
    }
}
