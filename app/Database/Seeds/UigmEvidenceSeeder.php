<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UigmEvidenceSeeder extends Seeder
{
    public function run()
    {
        // Get categories
        $categories = $this->db->table('uigm_categories')->get()->getResultArray();
        
        if (empty($categories)) {
            echo "No categories found. Please run UigmTablesSeeder first.\n";
            return;
        }

        // Evidence templates for each category
        $evidenceTemplates = [
            'WS.1' => [
                'SK Pembentukan Tim 3R',
                'SOP Program Reduce',
                'SOP Program Reuse', 
                'SOP Program Recycle',
                'Dokumentasi Kegiatan 3R',
                'Laporan Monitoring 3R',
                'Data Pengurangan Sampah',
                'Foto Implementasi 3R',
                'Sertifikat Pelatihan 3R',
                'Evaluasi Program 3R'
            ],
            'WS.2' => [
                'SK Pengurangan Kertas',
                'SK Pengurangan Plastik',
                'SOP Paperless Office',
                'SOP Penggunaan Plastik',
                'Data Konsumsi Kertas',
                'Data Konsumsi Plastik',
                'Dokumentasi Kampanye',
                'Laporan Pengurangan',
                'Foto Implementasi',
                'Evaluasi Program'
            ],
            'WS.3' => [
                'SK Pengelolaan Limbah Organik',
                'SOP Pengomposan',
                'SOP Biogas',
                'Desain Fasilitas Kompos',
                'Data Produksi Kompos',
                'Dokumentasi Proses',
                'Laporan Kualitas Kompos',
                'Foto Fasilitas',
                'Sertifikat Kompos',
                'Evaluasi Program'
            ],
            'WS.4' => [
                'SK Pengelolaan Limbah Anorganik',
                'SOP Pemilahan Sampah',
                'SOP Pengolahan Anorganik',
                'Desain TPS Anorganik',
                'Data Sampah Anorganik',
                'Dokumentasi Pemilahan',
                'Laporan Daur Ulang',
                'Foto Fasilitas TPS',
                'Kerjasama Pemulung',
                'Evaluasi Program'
            ],
            'WS.5' => [
                'SK Pengelolaan Limbah B3',
                'SOP Penanganan B3',
                'SOP Penyimpanan B3',
                'Izin Pengelolaan B3',
                'Data Limbah B3',
                'Manifest Limbah B3',
                'Laporan B3 ke KLHK',
                'Foto Fasilitas B3',
                'Sertifikat Pelatihan B3',
                'Evaluasi Program B3'
            ],
            'WS.6' => [
                'SK Pengelolaan Limbah Cair',
                'SOP IPAL',
                'SOP Monitoring Kualitas',
                'Desain IPAL',
                'Data Kualitas Air',
                'Hasil Uji Lab',
                'Laporan Monitoring',
                'Foto Fasilitas IPAL',
                'Izin Pembuangan',
                'Evaluasi Program'
            ],
            'WS.7' => [
                'Data Sampah Masuk',
                'Data Sampah Didaur Ulang',
                'Laporan Persentase Daur Ulang',
                'Dokumentasi Proses',
                'Kerjasama Industri Daur Ulang',
                'Sertifikat Daur Ulang',
                'Foto Produk Daur Ulang',
                'Laporan Ekonomi Daur Ulang',
                'Monitoring Bulanan',
                'Evaluasi Tahunan'
            ]
        ];

        foreach ($categories as $category) {
            $kodeKategori = $category['kode_kategori'];
            
            if (!isset($evidenceTemplates[$kodeKategori])) {
                echo "No evidence templates found for category {$kodeKategori}\n";
                continue;
            }

            $templates = $evidenceTemplates[$kodeKategori];
            
            foreach ($templates as $index => $template) {
                $evidenceData = [
                    'kategori_id' => $category['id'],
                    'nama_bukti' => $template,
                    'deskripsi_bukti' => "Bukti dukung untuk {$template} dalam kategori {$category['nama_kategori']}",
                    'status_upload' => 'belum_upload',
                    'urutan' => $index + 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                try {
                    $this->db->table('uigm_evidence')->insert($evidenceData);
                    echo "Evidence '{$template}' for {$kodeKategori} inserted successfully\n";
                } catch (\Exception $e) {
                    echo "Error inserting evidence '{$template}' for {$kodeKategori}: " . $e->getMessage() . "\n";
                }
            }
        }

        echo "UIGM Evidence templates created successfully!\n";
    }
}