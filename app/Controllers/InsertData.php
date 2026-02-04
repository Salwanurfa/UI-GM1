<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class InsertData extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        // Check if table exists
        if (!$db->tableExists('master_harga_sampah')) {
            return "❌ Table master_harga_sampah tidak ditemukan. Jalankan migration terlebih dahulu.";
        }
        
        // Check existing data
        $existing = $db->table('master_harga_sampah')->countAllResults();
        
        if ($existing > 0) {
            $html = "<h2>✅ Data sudah ada ({$existing} records).</h2>";
            $html .= "<br><a href='/pengelola-tps/waste' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px;'>Lihat Halaman Waste TPS</a>";
            $html .= "<a href='/user/waste' style='padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;'>Lihat Halaman Waste User</a>";
            return $html;
        }
        
        // Insert data
        $data = [
            [
                'jenis_sampah' => 'Plastik',
                'nama_jenis' => 'Plastik (Botol, Kemasan)',
                'harga_per_satuan' => 2000.00,
                'harga_per_kg' => 2000.00,
                'satuan' => 'kg',
                'dapat_dijual' => 1,
                'status_aktif' => 1,
                'deskripsi' => 'Plastik yang dapat didaur ulang',
                'tanggal_berlaku' => date('Y-m-d'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'jenis_sampah' => 'Kertas',
                'nama_jenis' => 'Kertas (HVS, Koran, Kardus)',
                'harga_per_satuan' => 1500.00,
                'harga_per_kg' => 1500.00,
                'satuan' => 'kg',
                'dapat_dijual' => 1,
                'status_aktif' => 1,
                'deskripsi' => 'Kertas yang dapat didaur ulang',
                'tanggal_berlaku' => date('Y-m-d'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'jenis_sampah' => 'Logam',
                'nama_jenis' => 'Logam (Kaleng, Aluminium)',
                'harga_per_satuan' => 5000.00,
                'harga_per_kg' => 5000.00,
                'satuan' => 'kg',
                'dapat_dijual' => 1,
                'status_aktif' => 1,
                'deskripsi' => 'Logam yang dapat didaur ulang',
                'tanggal_berlaku' => date('Y-m-d'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'jenis_sampah' => 'Organik',
                'nama_jenis' => 'Sampah Organik',
                'harga_per_satuan' => 0.00,
                'harga_per_kg' => 0.00,
                'satuan' => 'kg',
                'dapat_dijual' => 0,
                'status_aktif' => 1,
                'deskripsi' => 'Sampah organik untuk kompos',
                'tanggal_berlaku' => date('Y-m-d'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'jenis_sampah' => 'Residu',
                'nama_jenis' => 'Sampah Residu',
                'harga_per_satuan' => 0.00,
                'harga_per_kg' => 0.00,
                'satuan' => 'kg',
                'dapat_dijual' => 0,
                'status_aktif' => 1,
                'deskripsi' => 'Sampah yang tidak dapat didaur ulang',
                'tanggal_berlaku' => date('Y-m-d'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        
        try {
            $db->table('master_harga_sampah')->insertBatch($data);
            
            $html = "<h2>✅ Berhasil menambahkan " . count($data) . " data master harga sampah!</h2>";
            
            $html .= "<h3>Data yang ditambahkan:</h3>";
            $html .= "<ul style='font-size: 16px;'>";
            foreach ($data as $item) {
                $html .= "<li><strong>{$item['jenis_sampah']}</strong> - Rp " . number_format($item['harga_per_satuan'], 0, ',', '.') . "/{$item['satuan']}";
                $html .= " - " . ($item['dapat_dijual'] ? '<span style="color:green; font-weight:bold;">✓ Bisa Dijual</span>' : '<span style="color:gray;">✗ Tidak Dijual</span>');
                $html .= "</li>";
            }
            $html .= "</ul>";
            
            $html .= "<br><br>";
            $html .= "<a href='/pengelola-tps/waste' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px;'>Lihat Halaman Waste TPS</a>";
            $html .= "<a href='/user/waste' style='padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;'>Lihat Halaman Waste User</a>";
            
            return $html;
            
        } catch (\Exception $e) {
            return "<h2>❌ Error: " . $e->getMessage() . "</h2><pre>" . $e->getTraceAsString() . "</pre>";
        }
    }
}
