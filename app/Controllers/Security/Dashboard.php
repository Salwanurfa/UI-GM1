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
     */
    private function getDashboardStats()
    {
        $userId = session()->get('user')['id'];
        return $this->transportStatsModel->getStatsSummary($userId);
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

        return view('security/export_pdf_stats', $exportData);
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