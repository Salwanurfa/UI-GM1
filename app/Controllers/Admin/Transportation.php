<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TransportStatsModel;

class Transportation extends BaseController
{
    protected $transportStatsModel;

    public function __construct()
    {
        $this->transportStatsModel = new TransportStatsModel();
    }

    /**
     * Transportation statistics dashboard for Admin
     */
    public function index()
    {
        $data = [
            'title' => 'Statistik Transportasi Kampus',
            'user' => session()->get('user'),
            'transport_stats' => $this->getTransportStats(),
            'summary_stats' => $this->getSummaryStats(),
            'recent_entries' => $this->getRecentEntries()
        ];

        return view('admin_pusat/transportation/index', $data);
    }

    /**
     * Get all transport statistics
     */
    private function getTransportStats()
    {
        return $this->transportStatsModel
            ->select('transport_stats.*, users.nama_lengkap as petugas_nama')
            ->join('users', 'users.id = transport_stats.input_by', 'left')
            ->orderBy('transport_stats.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get summary statistics
     */
    private function getSummaryStats()
    {
        $db = \Config\Database::connect();
        
        // Total vehicles by category
        $categoryStats = $db->table('transport_stats')
            ->select('kategori_kendaraan, SUM(CAST(jumlah_total AS UNSIGNED)) as total')
            ->where('jumlah_total IS NOT NULL')
            ->where('jumlah_total !=', '')
            ->groupBy('kategori_kendaraan')
            ->get()
            ->getResultArray();

        // Total vehicles by fuel type
        $fuelStats = $db->table('transport_stats')
            ->select('jenis_bahan_bakar, SUM(CAST(jumlah_total AS UNSIGNED)) as total')
            ->where('jumlah_total IS NOT NULL')
            ->where('jumlah_total !=', '')
            ->groupBy('jenis_bahan_bakar')
            ->get()
            ->getResultArray();

        // Overall totals with proper validation
        $totalVehiclesResult = $db->table('transport_stats')
            ->selectSum('jumlah_total')
            ->where('jumlah_total IS NOT NULL')
            ->where('jumlah_total !=', '')
            ->get()
            ->getRowArray();
        
        $totalVehicles = isset($totalVehiclesResult['jumlah_total']) && is_numeric($totalVehiclesResult['jumlah_total']) 
            ? (int)$totalVehiclesResult['jumlah_total'] 
            : 0;

        $totalEntries = $db->table('transport_stats')
            ->where('jumlah_total IS NOT NULL')
            ->where('jumlah_total !=', '')
            ->countAllResults();

        $totalSecurityOfficers = $db->table('transport_stats')
            ->select('input_by')
            ->where('jumlah_total IS NOT NULL')
            ->where('jumlah_total !=', '')
            ->distinct()
            ->countAllResults();

        // ZEV Statistics with validation
        $totalZevResult = $db->table('transport_stats')
            ->selectSum('jumlah_total')
            ->where('is_zev', 1)
            ->where('jumlah_total IS NOT NULL')
            ->where('jumlah_total !=', '')
            ->get()
            ->getRowArray();
            
        $totalZev = isset($totalZevResult['jumlah_total']) && is_numeric($totalZevResult['jumlah_total']) 
            ? (int)$totalZevResult['jumlah_total'] 
            : 0;

        // Shuttle Statistics with validation
        $totalShuttleResult = $db->table('transport_stats')
            ->selectSum('jumlah_total')
            ->where('is_shuttle', 1)
            ->where('jumlah_total IS NOT NULL')
            ->where('jumlah_total !=', '')
            ->get()
            ->getRowArray();
            
        $totalShuttle = isset($totalShuttleResult['jumlah_total']) && is_numeric($totalShuttleResult['jumlah_total']) 
            ? (int)$totalShuttleResult['jumlah_total'] 
            : 0;

        // Safe percentage calculations
        $zevPercentage = ($totalVehicles > 0 && $totalZev > 0) 
            ? round(($totalZev / $totalVehicles) * 100, 2) 
            : 0;
            
        $shuttlePercentage = ($totalVehicles > 0 && $totalShuttle > 0) 
            ? round(($totalShuttle / $totalVehicles) * 100, 2) 
            : 0;

        return [
            'total_vehicles' => $totalVehicles,
            'total_entries' => $totalEntries,
            'total_officers' => $totalSecurityOfficers,
            'total_zev' => $totalZev,
            'total_shuttle' => $totalShuttle,
            'zev_percentage' => $zevPercentage,
            'shuttle_percentage' => $shuttlePercentage,
            'category_breakdown' => $categoryStats,
            'fuel_breakdown' => $fuelStats
        ];
    }

    /**
     * Get recent entries for dashboard widget
     */
    private function getRecentEntries($limit = 5)
    {
        return $this->transportStatsModel
            ->select('transport_stats.*, users.nama_lengkap as petugas_nama')
            ->join('users', 'users.id = transport_stats.input_by', 'left')
            ->orderBy('transport_stats.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Export transportation data to PDF
     */
    public function exportPdf()
    {
        $data = [
            'title' => 'Laporan Statistik Transportasi Kampus',
            'user' => session()->get('user'),
            'transport_stats' => $this->getTransportStats(),
            'summary_stats' => $this->getSummaryStats(),
            'generated_at' => date('d/m/Y H:i:s')
        ];

        return view('admin_pusat/transportation/export_pdf', $data);
    }

    /**
     * Export transportation data to Excel
     */
    public function exportExcel()
    {
        $transportStats = $this->getTransportStats();
        $summaryStats = $this->getSummaryStats();

        // Set headers for Excel download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Laporan_Statistik_Transportasi_Kampus_' . date('Y-m-d') . '.xls"');
        header('Cache-Control: max-age=0');

        echo '<table border="1">';
        echo '<tr>';
        echo '<th colspan="6" style="text-align: center; font-weight: bold; font-size: 16px;">LAPORAN STATISTIK TRANSPORTASI KAMPUS</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th colspan="6" style="text-align: center;">Politeknik Negeri Bandung - UI GreenMetric</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th colspan="6" style="text-align: center;">Tanggal Export: ' . date('d/m/Y H:i:s') . '</th>';
        echo '</tr>';
        echo '<tr><th></th></tr>'; // Empty row

        // Summary section
        echo '<tr>';
        echo '<th colspan="6" style="background-color: #1e3c72; color: white; text-align: center;">RINGKASAN STATISTIK</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<td><strong>Total Kendaraan Terdaftar:</strong></td>';
        echo '<td colspan="5">' . number_format($summaryStats['total_vehicles']) . ' Unit</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td><strong>Total Entry Data:</strong></td>';
        echo '<td colspan="5">' . number_format($summaryStats['total_entries']) . ' Record</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td><strong>Petugas Security Aktif:</strong></td>';
        echo '<td colspan="5">' . $summaryStats['total_officers'] . ' Orang</td>';
        echo '</tr>';
        echo '<tr><th></th></tr>'; // Empty row

        // Data table
        echo '<tr>';
        echo '<th colspan="6" style="background-color: #1e3c72; color: white; text-align: center;">DATA DETAIL</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th>No</th>';
        echo '<th>Periode</th>';
        echo '<th>Kategori Kendaraan</th>';
        echo '<th>Jenis Bahan Bakar</th>';
        echo '<th>Jumlah Total</th>';
        echo '<th>Petugas Input</th>';
        echo '</tr>';

        foreach ($transportStats as $index => $row) {
            echo '<tr>';
            echo '<td>' . ($index + 1) . '</td>';
            echo '<td>' . esc($row['periode']) . '</td>';
            echo '<td>' . esc($row['kategori_kendaraan']) . '</td>';
            echo '<td>' . esc($row['jenis_bahan_bakar']) . '</td>';
            echo '<td>' . number_format($row['jumlah_total']) . '</td>';
            echo '<td>' . esc($row['petugas_nama']) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        exit;
    }

    /**
     * Get transport stats for dashboard widget
     */
    public function getDashboardWidget()
    {
        return [
            'recent_entries' => $this->getRecentEntries(3),
            'summary_stats' => $this->getSummaryStats()
        ];
    }
}