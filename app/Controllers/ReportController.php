<?php

namespace App\Controllers;

use App\Models\UnitModel;
use App\Models\TahunPenilaianModel;
use App\Models\PengirimanUnitModel;
use App\Models\ReviewKategoriModel;
use App\Models\IndikatorModel;

class ReportController extends BaseController
{
    protected $unitModel;
    protected $tahunModel;
    protected $pengirimanModel;
    protected $reviewModel;
    protected $indikatorModel;

    public function __construct()
    {
        $this->unitModel = new UnitModel();
        $this->tahunModel = new TahunPenilaianModel();
        $this->pengirimanModel = new PengirimanUnitModel();
        $this->reviewModel = new ReviewKategoriModel();
        $this->indikatorModel = new IndikatorModel();
    }

    /**
     * Export data ke CSV
     */
    public function exportCSV()
    {
        $user = session()->get('user');
        if (!$user || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return redirect()->to('/auth/login')->with('error', 'Akses ditolak.');
        }

        $tahunId = $this->request->getGet('tahun_id');
        $unitId = $this->request->getGet('unit_id');

        $tahunAktif = $tahunId ? $this->tahunModel->find($tahunId) : $this->tahunModel->getActiveYear();

        if (!$tahunAktif) {
            return redirect()->back()->with('error', 'Tahun penilaian tidak ditemukan');
        }

        // Query data
        $query = $this->pengirimanModel
            ->select('pengiriman_unit.*, unit.nama_unit, unit.kode_unit, tahun_penilaian.tahun')
            ->join('unit', 'unit.id = pengiriman_unit.unit_id')
            ->join('tahun_penilaian', 'tahun_penilaian.id = pengiriman_unit.tahun_penilaian_id')
            ->where('pengiriman_unit.tahun_penilaian_id', $tahunAktif['id']);

        if ($unitId) {
            $query->where('pengiriman_unit.unit_id', $unitId);
        }

        $data = $query->findAll();

        // Generate CSV
        $filename = 'laporan_uigm_' . $tahunAktif['tahun'] . '_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Header CSV
        fputcsv($output, [
            'Unit',
            'Kode Unit',
            'Status',
            'Progress (%)',
            'Tanggal Kirim',
            'Tanggal Review',
            'Tanggal Disetujui',
            'Versi'
        ]);

        // Data CSV
        foreach ($data as $row) {
            fputcsv($output, [
                $row['nama_unit'],
                $row['kode_unit'],
                ucfirst(str_replace('_', ' ', $row['status_pengiriman'])),
                number_format($row['progress_persen'], 2),
                $row['tanggal_kirim'] ? date('d/m/Y H:i', strtotime($row['tanggal_kirim'])) : '-',
                $row['tanggal_review'] ? date('d/m/Y H:i', strtotime($row['tanggal_review'])) : '-',
                $row['tanggal_disetujui'] ? date('d/m/Y H:i', strtotime($row['tanggal_disetujui'])) : '-',
                $row['versi']
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Generate laporan PDF
     */
    public function generatePDF()
    {
        $user = session()->get('user');
        if (!$user || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return redirect()->to('/auth/login')->with('error', 'Akses ditolak.');
        }

        $tahunId = $this->request->getGet('tahun_id');
        $unitId = $this->request->getGet('unit_id');

        $tahunAktif = $tahunId ? $this->tahunModel->find($tahunId) : $this->tahunModel->getActiveYear();

        if (!$tahunAktif) {
            return redirect()->back()->with('error', 'Tahun penilaian tidak ditemukan');
        }

        // Query data
        $query = $this->pengirimanModel
            ->select('pengiriman_unit.*, unit.nama_unit, unit.kode_unit')
            ->join('unit', 'unit.id = pengiriman_unit.unit_id')
            ->where('pengiriman_unit.tahun_penilaian_id', $tahunAktif['id']);

        if ($unitId) {
            $query->where('pengiriman_unit.unit_id', $unitId);
        }

        $data = $query->findAll();

        // Get categories
        $categories = $this->indikatorModel->getUIGMCategories();

        // Generate HTML untuk PDF
        $html = $this->generateReportHTML($tahunAktif, $data, $categories, $unitId);

        // Untuk sementara return HTML (bisa diintegrasikan dengan library PDF seperti TCPDF atau mPDF)
        return $this->response->setContentType('text/html')->setBody($html);
    }

    /**
     * Generate HTML untuk laporan
     */
    private function generateReportHTML($tahun, $data, $categories, $unitId = null)
    {
        $unitName = '';
        if ($unitId) {
            $unit = $this->unitModel->find($unitId);
            $unitName = $unit ? ' - ' . $unit['nama_unit'] : '';
        }

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Laporan UIGM ' . $tahun['tahun'] . $unitName . '</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .header h1 { color: #5c8cbf; margin: 0; }
                .header p { margin: 5px 0; color: #666; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #5c8cbf; color: white; }
                tr:nth-child(even) { background-color: #f9f9f9; }
                .status { padding: 4px 8px; border-radius: 4px; font-size: 12px; }
                .status-draft { background: #f5f5f5; color: #666; }
                .status-dikirim { background: #e3f2fd; color: #1976d2; }
                .status-review { background: #fff3e0; color: #f57c00; }
                .status-disetujui { background: #e8f5e8; color: #388e3c; }
                .status-perlu_revisi { background: #ffebee; color: #d32f2f; }
                .summary { margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 8px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Laporan UI GreenMetric</h1>
                <p>Tahun Penilaian: ' . $tahun['tahun'] . ' - ' . $tahun['nama_periode'] . $unitName . '</p>
                <p>Tanggal Cetak: ' . date('d/m/Y H:i:s') . '</p>
            </div>

            <div class="summary">
                <h3>Ringkasan</h3>
                <p>Total Unit: ' . count($data) . '</p>
                <p>Periode: ' . date('d/m/Y', strtotime($tahun['tanggal_mulai'])) . ' - ' . date('d/m/Y', strtotime($tahun['tanggal_selesai'])) . '</p>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Unit</th>
                        <th>Kode</th>
                        <th>Status</th>
                        <th>Progress</th>
                        <th>Tanggal Kirim</th>
                        <th>Tanggal Review</th>
                    </tr>
                </thead>
                <tbody>';

        $no = 1;
        foreach ($data as $row) {
            $statusClass = 'status-' . $row['status_pengiriman'];
            $statusText = ucfirst(str_replace('_', ' ', $row['status_pengiriman']));

            $html .= '
                    <tr>
                        <td>' . $no++ . '</td>
                        <td>' . $row['nama_unit'] . '</td>
                        <td>' . $row['kode_unit'] . '</td>
                        <td><span class="status ' . $statusClass . '">' . $statusText . '</span></td>
                        <td>' . number_format($row['progress_persen'], 1) . '%</td>
                        <td>' . ($row['tanggal_kirim'] ? date('d/m/Y', strtotime($row['tanggal_kirim'])) : '-') . '</td>
                        <td>' . ($row['tanggal_review'] ? date('d/m/Y', strtotime($row['tanggal_review'])) : '-') . '</td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>

            <div class="summary">
                <h3>Kategori UIGM</h3>
                <ol>';

        foreach ($categories as $category) {
            $html .= '<li>' . $category['nama_kategori'] . ' (' . $category['kode_kategori'] . ') - Bobot: ' . $category['bobot_persen'] . '%</li>';
        }

        $html .= '
                </ol>
            </div>
        </body>
        </html>';

        return $html;
    }

    /**
     * Dashboard laporan
     */
    public function index()
    {
        $user = session()->get('user');
        if (!$user || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return redirect()->to('/auth/login')->with('error', 'Akses ditolak.');
        }

        $tahunList = $this->tahunModel->orderBy('tahun', 'DESC')->findAll();
        $unitList = $this->unitModel->where('status_aktif', true)->orderBy('nama_unit')->findAll();

        $data = [
            'title' => 'Laporan UIGM',
            'user' => $user,
            'tahunList' => $tahunList,
            'unitList' => $unitList
        ];

        return view('report/index', $data);
    }
}
