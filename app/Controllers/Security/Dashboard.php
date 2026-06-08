<?php

namespace App\Controllers\Security;

use App\Controllers\BaseController;
use App\Models\TransportStatsModel;

class Dashboard extends BaseController
{
    protected $transportStatsModel;

    public function __construct()
    {
        $this->transportStatsModel = new TransportStatsModel();
        
        // Check if user is logged in and has security role
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        
        $userRole = session()->get('user')['role'] ?? null;
        if ($userRole !== 'security') {
            return redirect()->to('/auth/login')->with('error', 'Akses ditolak');
        }
    }

    /**
     * Security Dashboard
     */
    public function index()
    {
        $data = [
            'title' => 'Dashboard Security',
            'user' => session()->get('user'),
            'stats' => $this->getDashboardStats(),
            'recent_entries' => $this->getRecentEntries()
        ];

        return view('security/dashboard', $data);
    }

    /**
     * Get dashboard statistics
     * Updated to show TOTAL ACCUMULATION from all time
     * FIXED: Dynamic category matching from database
     */
    private function getDashboardStats()
    {
        $userId = session()->get('user')['id'];
        $db = \Config\Database::connect();
        
        // Get basic stats (includes all time totals)
        $basicStats = $this->transportStatsModel->getStatsSummary($userId);
        
        // Get stats by category - ALL TIME (NO DATE FILTER)
        // FIXED: Use dynamic category matching instead of hardcoded names
        
        // Roda Empat - Match categories containing: Mobil, M1, M2, M3, Bus, Roda Empat
        $rodaEmpatCategories = $db->table('transport_categories')
            ->where('status_aktif', 1)
            ->groupStart()
                ->like('nama_kategori', 'Mobil', 'both')
                ->orLike('nama_kategori', 'M1', 'both')
                ->orLike('nama_kategori', 'M2', 'both')
                ->orLike('nama_kategori', 'M3', 'both')
                ->orLike('nama_kategori', 'Bus', 'both')
                ->orLike('nama_kategori', 'Roda Empat', 'both')
            ->groupEnd()
            ->select('nama_kategori')
            ->get()
            ->getResultArray();
        
        $rodaEmpatNames = array_column($rodaEmpatCategories, 'nama_kategori');
        
        $rodaEmpat = 0;
        if (!empty($rodaEmpatNames)) {
            $rodaEmpatQuery = $db->table('transport_stats')
                ->where('input_by', $userId)
                ->whereIn('kategori_kendaraan', $rodaEmpatNames)
                ->selectSum('jumlah_total')
                ->get()
                ->getRow();
            $rodaEmpat = $rodaEmpatQuery->jumlah_total ?? 0;
        }
        
        // Roda Dua - Match categories containing: Motor, Sepeda Motor, Kategori L, Roda Dua
        $rodaDuaCategories = $db->table('transport_categories')
            ->where('status_aktif', 1)
            ->groupStart()
                ->like('nama_kategori', 'Motor', 'both')
                ->orLike('nama_kategori', 'Kategori L', 'both')
                ->orLike('nama_kategori', 'Roda Dua', 'both')
            ->groupEnd()
            ->notLike('nama_kategori', 'Tidak Bermotor', 'both') // Exclude non-motorized
            ->select('nama_kategori')
            ->get()
            ->getResultArray();
        
        $rodaDuaNames = array_column($rodaDuaCategories, 'nama_kategori');
        
        $rodaDua = 0;
        if (!empty($rodaDuaNames)) {
            $rodaDuaQuery = $db->table('transport_stats')
                ->where('input_by', $userId)
                ->whereIn('kategori_kendaraan', $rodaDuaNames)
                ->selectSum('jumlah_total')
                ->get()
                ->getRow();
            $rodaDua = $rodaDuaQuery->jumlah_total ?? 0;
        }
        
        // Sepeda/Non-BBM - Match categories containing: Sepeda (non-motor), or fuel type Non-BBM
        $sepedaCategories = $db->table('transport_categories')
            ->where('status_aktif', 1)
            ->groupStart()
                ->like('nama_kategori', 'Sepeda', 'both')
                ->orLike('nama_kategori', 'Tidak Bermotor', 'both')
            ->groupEnd()
            ->notLike('nama_kategori', 'Motor', 'both') // Exclude motorized
            ->select('nama_kategori')
            ->get()
            ->getResultArray();
        
        $sepedaNames = array_column($sepedaCategories, 'nama_kategori');
        
        // Count by category OR by fuel type (Non-BBM)
        $sepeda = 0;
        
        // Count by category
        if (!empty($sepedaNames)) {
            $sepedaQuery = $db->table('transport_stats')
                ->where('input_by', $userId)
                ->whereIn('kategori_kendaraan', $sepedaNames)
                ->selectSum('jumlah_total')
                ->get()
                ->getRow();
            $sepeda += $sepedaQuery->jumlah_total ?? 0;
        }
        
        // Also count by fuel type (Non-BBM)
        $nonBBMQuery = $db->table('transport_stats')
            ->where('input_by', $userId)
            ->where('jenis_bahan_bakar', 'Non-BBM')
            ->selectSum('jumlah_total')
            ->get()
            ->getRow();
        $sepeda += $nonBBMQuery->jumlah_total ?? 0;
        
        return [
            // Entry counts (by time period)
            'today_entries' => $basicStats['today_entries'] ?? 0,
            'week_entries' => $basicStats['week_entries'] ?? 0,
            'month_entries' => $basicStats['month_entries'] ?? 0,
            
            // Total vehicles - ALL TIME ACCUMULATION
            'total_vehicles_all_time' => $basicStats['total_vehicles_all_time'] ?? 0,
            'total_vehicles_month' => $basicStats['total_vehicles_month'] ?? 0,
            'total_vehicles_today' => $basicStats['total_vehicles_today'] ?? 0,
            
            // Category breakdown - ALL TIME ACCUMULATION (FIXED: Dynamic matching)
            'roda_empat' => $rodaEmpat,
            'roda_dua' => $rodaDua,
            'sepeda' => $sepeda,
        ];
    }

    /**
     * Get recent transportation entries for current user
     */
    private function getRecentEntries()
    {
        $userId = session()->get('user')['id'];
        return $this->transportStatsModel->getStatsByUser($userId, 10);
    }

    /**
     * Delete transportation entry
     */
    public function deleteEntry($id)
    {
        $userId = session()->get('user')['id'];
        
        // Verify the entry belongs to current user
        $entry = $this->transportStatsModel->where('id', $id)
            ->where('input_by', $userId)
            ->first();
            
        if (!$entry) {
            return redirect()->to('/security/dashboard')
                ->with('error', 'Data tidak ditemukan atau Anda tidak memiliki akses');
        }
        
        try {
            $this->transportStatsModel->delete($id);
                
            return redirect()->to('/security/dashboard')
                ->with('success', 'Data berhasil dihapus');
                
        } catch (\Exception $e) {
            log_message('error', 'Transport stats delete error: ' . $e->getMessage());
            
            return redirect()->to('/security/dashboard')
                ->with('error', 'Gagal menghapus data. Silakan coba lagi.');
        }
    }

    /**
     * Export transportation data to PDF
     */
    public function exportPdf()
    {
        $userId = session()->get('user')['id'];
        $user = session()->get('user');
        
        // Get all transportation data for current user
        $data = $this->transportStatsModel->where('input_by', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $exportData = [
            'title' => 'Laporan Statistik Transportasi',
            'user' => $user,
            'data' => $data,
            'generated_at' => date('d/m/Y H:i:s')
        ];

        // Generate HTML from view
        $html = view('security/export_pdf_stats', $exportData);

        // Configure Dompdf
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');
        
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Output PDF
        $filename = 'Laporan_Statistik_Transportasi_' . date('Y-m-d_H-i-s') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
    }

    /**
     * Export transportation data to Excel
     */
    public function exportExcel()
    {
        $userId = session()->get('user')['id'];
        $user = session()->get('user');
        
        // Get all transportation data for current user
        $data = $this->transportStatsModel->where('input_by', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Set headers for Excel download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Laporan_Statistik_Transportasi_' . date('Y-m-d') . '.xls"');
        header('Cache-Control: max-age=0');

        echo '<table border="1">';
        echo '<tr>';
        echo '<th colspan="6" style="text-align: center; font-weight: bold; font-size: 16px;">LAPORAN STATISTIK TRANSPORTASI</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th colspan="6" style="text-align: center;">Petugas: ' . esc($user['nama_lengkap']) . '</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th colspan="6" style="text-align: center;">Tanggal Export: ' . date('d/m/Y H:i:s') . '</th>';
        echo '</tr>';
        echo '<tr><th></th></tr>'; // Empty row
        echo '<tr>';
        echo '<th>No</th>';
        echo '<th>Periode</th>';
        echo '<th>Kategori Kendaraan</th>';
        echo '<th>Jenis Bahan Bakar</th>';
        echo '<th>Jumlah Total</th>';
        echo '<th>Tanggal Input</th>';
        echo '</tr>';

        foreach ($data as $index => $row) {
            echo '<tr>';
            echo '<td>' . ($index + 1) . '</td>';
            echo '<td>' . esc($row['periode']) . '</td>';
            echo '<td>' . esc($row['kategori_kendaraan']) . '</td>';
            echo '<td>' . esc($row['jenis_bahan_bakar']) . '</td>';
            echo '<td>' . $row['jumlah_total'] . '</td>';
            echo '<td>' . date('d/m/Y H:i', strtotime($row['created_at'])) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        exit;
    }
}