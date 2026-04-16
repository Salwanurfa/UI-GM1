<?php

namespace App\Services\Admin;

use App\Models\WasteModel;
use App\Models\UnitModel;
use App\Models\LimbahB3Model;
use App\Models\MasterLimbahB3Model;

class LaporanService
{
    protected $wasteModel;
    protected $unitModel;
    protected $limbahB3Model;
    protected $masterLimbahB3Model;

    public function __construct()
    {
        $this->wasteModel = new WasteModel();
        $this->unitModel = new UnitModel();
        $this->limbahB3Model = new LimbahB3Model();
        $this->masterLimbahB3Model = new MasterLimbahB3Model();
    }

    public function getLaporanData(): array
    {
        try {
            return [
                'monthly_report' => $this->getMonthlyReport(),
                'yearly_report' => $this->getYearlyReport(),
                'tps_report' => $this->getTpsReport(),
                'summary_stats' => $this->getSummaryStats(),
                'recap_limbah_b3' => $this->getRekapLimbahB3()
            ];
        } catch (\Exception $e) {
            log_message('error', 'Admin Laporan Service Error: ' . $e->getMessage());
            
            return [
                'monthly_report' => [],
                'yearly_report' => [],
                'tps_report' => [],
                'summary_stats' => [],
                'recap_limbah_b3' => []
            ];
        }
    }

    public function exportLaporan(): array
    {
        try {
            $data = $this->getLaporanData();
            
            // Create CSV content
            $csvContent = "Laporan Sistem Sampah dan Limbah B3\n";
            $csvContent .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";
            
            // Summary Stats
            $csvContent .= "RINGKASAN STATISTIK\n";
            foreach ($data['summary_stats'] as $key => $value) {
                $csvContent .= ucfirst(str_replace('_', ' ', $key)) . "," . $value . "\n";
            }
            $csvContent .= "\n";
            
            // Monthly Report
            $csvContent .= "LAPORAN BULANAN\n";
            $csvContent .= "Bulan,Jumlah Entry,Total Berat (kg)\n";
            foreach ($data['monthly_report'] as $month) {
                $csvContent .= $month['month'] . "," . $month['total_entries'] . "," . $month['total_weight'] . "\n";
            }
            $csvContent .= "\n";
            
            // TPS Report
            $csvContent .= "LAPORAN PER TPS\n";
            $csvContent .= "Nama TPS,Jumlah Entry,Total Berat (kg)\n";
            foreach ($data['tps_report'] as $tps) {
                $csvContent .= $tps['nama_unit'] . "," . $tps['total_entries'] . "," . $tps['total_weight'] . "\n";
            }
            $csvContent .= "\n";
            
            // Rekap Limbah B3
            $csvContent .= "REKAP LIMBAH B3\n";
            $csvContent .= "Jenis Limbah,Total Transaksi,Disetujui,Ditolak,Berat Disetujui,Berat Ditolak\n";
            foreach ($data['recap_limbah_b3'] as $limbah) {
                $csvContent .= ($limbah['nama_limbah'] ?? 'N/A') . "," 
                    . $limbah['total_transaksi'] . "," 
                    . $limbah['total_disetujui'] . "," 
                    . $limbah['total_ditolak'] . "," 
                    . $limbah['total_berat_disetujui'] . "," 
                    . $limbah['total_berat_ditolak'] . "\n";
            }
            
            // Save to temp file
            $filename = 'laporan_sistem_' . date('Y-m-d_H-i-s') . '.csv';
            $filePath = WRITEPATH . 'uploads/' . $filename;
            
            if (!is_dir(WRITEPATH . 'uploads/')) {
                mkdir(WRITEPATH . 'uploads/', 0755, true);
            }
            
            file_put_contents($filePath, $csvContent);

            return [
                'success' => true,
                'file_path' => $filePath,
                'filename' => $filename
            ];

        } catch (\Exception $e) {
            log_message('error', 'Export Laporan Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan saat export laporan'];
        }
    }

    private function getMonthlyReport(): array
    {
        $currentYear = date('Y');
        
        return $this->wasteModel
            ->select('MONTH(created_at) as month, COUNT(*) as total_entries, SUM(berat_kg) as total_weight')
            ->where('YEAR(created_at)', $currentYear)
            ->groupBy('MONTH(created_at)')
            ->orderBy('MONTH(created_at)', 'ASC')
            ->findAll();
    }

    private function getYearlyReport(): array
    {
        return $this->wasteModel
            ->select('YEAR(created_at) as year, COUNT(*) as total_entries, SUM(berat_kg) as total_weight')
            ->groupBy('YEAR(created_at)')
            ->orderBy('YEAR(created_at)', 'DESC')
            ->limit(5)
            ->findAll();
    }

    private function getTpsReport(): array
    {
        return $this->wasteModel
            ->select('units.nama_unit, COUNT(waste_management.id) as total_entries, SUM(waste_management.berat_kg) as total_weight')
            ->join('units', 'units.id = waste_management.unit_id', 'left')
            ->where('units.jenis_unit', 'TPS')
            ->groupBy('waste_management.unit_id')
            ->orderBy('total_weight', 'DESC')
            ->findAll();
    }

    private function getSummaryStats(): array
    {
        $currentYear = date('Y');
        $currentMonth = date('Y-m');
        
        // ===== WASTE MANAGEMENT DATA =====
        $waste_total = $this->wasteModel->countAllResults();
        $waste_total_weight = $this->wasteModel->selectSum('berat_kg')->get()->getRow()->berat_kg ?? 0;
        $waste_this_year = $this->wasteModel->where('YEAR(created_at)', $currentYear)->countAllResults();
        $waste_this_month = $this->wasteModel->where('DATE_FORMAT(created_at, "%Y-%m")', $currentMonth)->countAllResults();
        
        // ===== LIMBAH B3 DATA =====
        $limbah_total = $this->limbahB3Model->countAllResults();
        $limbah_total_weight = $this->limbahB3Model->selectSum('timbulan')->get()->getRow()->timbulan ?? 0;
        $limbah_this_year = $this->limbahB3Model->where('YEAR(tanggal_input)', $currentYear)->countAllResults();
        $limbah_this_month = $this->limbahB3Model->where('DATE_FORMAT(tanggal_input, "%Y-%m")', $currentMonth)->countAllResults();
        
        // ===== GABUNGAN WASTE + LIMBAH B3 =====
        return [
            'total_entries' => $waste_total + $limbah_total,
            'total_weight' => $waste_total_weight + $limbah_total_weight,
            'entries_this_year' => $waste_this_year + $limbah_this_year,
            'entries_this_month' => $waste_this_month + $limbah_this_month,
            'active_tps' => $this->unitModel->where('jenis_unit', 'TPS')->where('status_aktif', 1)->countAllResults()
        ];
    }

    /**
     * Rekap Limbah B3 per Jenis Limbah
     * Group by nama limbah dengan status disetujui/ditolak
     */
    private function getRekapLimbahB3(): array
    {
        try {
            $db = \Config\Database::connect();
            
            $query = $db->query("
                SELECT 
                    master_limbah_b3.nama_limbah,
                    master_limbah_b3.kode_limbah,
                    COUNT(limbah_b3.id) as total_transaksi,
                    SUM(CASE WHEN limbah_b3.status IN ('disetujui_tps', 'disetujui_admin') THEN 1 ELSE 0 END) as total_disetujui,
                    SUM(CASE WHEN limbah_b3.status = 'ditolak_tps' THEN 1 ELSE 0 END) as total_ditolak,
                    SUM(CASE WHEN limbah_b3.status IN ('disetujui_tps', 'disetujui_admin') THEN limbah_b3.timbulan ELSE 0 END) as total_berat_disetujui,
                    SUM(CASE WHEN limbah_b3.status = 'ditolak_tps' THEN limbah_b3.timbulan ELSE 0 END) as total_berat_ditolak
                FROM limbah_b3
                LEFT JOIN master_limbah_b3 ON master_limbah_b3.id = limbah_b3.master_b3_id
                GROUP BY limbah_b3.master_b3_id, master_limbah_b3.nama_limbah
                ORDER BY total_transaksi DESC
            ");
            
            return $query->getResultArray() ?? [];
            
        } catch (\Exception $e) {
            log_message('error', 'getRekapLimbahB3 error: ' . $e->getMessage());
            return [];
        }
    }
}