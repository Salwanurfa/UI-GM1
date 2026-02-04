<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        try {
            $db = \Config\Database::connect();
            
            // Get statistics for landing page (only waste data that's real)
            $stats = [
                // Total data from waste_management table
                'total_data' => $db->table('waste_management')->countAllResults(),
                
                // Total weight from waste_management
                'total_berat' => $db->table('waste_management')
                    ->selectSum('berat_kg')
                    ->get()
                    ->getRow()
                    ->berat_kg ?? 0,
                
                // Total units (all units, not just TPS)
                'total_tps' => $db->table('unit')->countAllResults(),
                
                // Approved waste data
                'disetujui' => $db->table('waste_management')
                    ->where('status', 'disetujui')
                    ->countAllResults(),
                
                // Waste that can be sold (has economic value)
                'bisa_dijual' => $db->table('waste_management')
                    ->where('kategori_sampah', 'bisa_dijual')
                    ->countAllResults()
            ];
            
            return view('landing', ['stats' => $stats]);
            
        } catch (\Exception $e) {
            log_message('error', 'Landing Page Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            // Return with default stats if error
            return view('landing', [
                'stats' => [
                    'total_data' => 0,
                    'total_berat' => 0,
                    'total_tps' => 0,
                    'disetujui' => 0,
                    'bisa_dijual' => 0
                ]
            ]);
        }
    }
}
