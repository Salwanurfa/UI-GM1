<?php

namespace App\Services\Admin;

use App\Models\WasteModel;
use App\Models\UnitModel;

class LaporanService
{
    protected $wasteModel;
    protected $unitModel;

    public function __construct()
    {
        $this->wasteModel = new WasteModel();
        $this->unitModel = new UnitModel();
    }

    public function getLaporanData(): array
    {
        try {
            return [
                'monthly_report' => $this->getMonthlyReport(),
                'yearly_report' => $this->getYearlyReport(),
                'tps_report' => $this->getTpsReport(),
                'summary_stats' => $this->getSummaryStats()
            ];
        } catch (\Exception $e) {
            log_message('error', 'Admin Laporan Service Error: ' . $e->getMessage());
            
            return [
                'monthly_report' => [],
                'yearly_report' => [],
                'tps_report' => [],
                'summary_stats' => []
            ];
        }
    }

    public function exportLaporan(): array
    {
        try {
            $data = $this->getLaporanData();
            
            // Create CSV content
            $csvContent = "Laporan Sistem Sampah\n";
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
        
        return [
            'total_entries' => $this->wasteModel->countAllResults(),
            'total_weight' => $this->wasteModel->selectSum('berat_kg')->get()->getRow()->berat_kg ?? 0,
            'entries_this_year' => $this->wasteModel->where('YEAR(created_at)', $currentYear)->countAllResults(),
            'entries_this_month' => $this->wasteModel->where('DATE_FORMAT(created_at, "%Y-%m")', $currentMonth)->countAllResults(),
            'active_tps' => $this->unitModel->where('jenis_unit', 'TPS')->where('status_aktif', 1)->countAllResults()
        ];
    }
}