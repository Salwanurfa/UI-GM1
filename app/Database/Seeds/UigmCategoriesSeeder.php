<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UigmCategoriesSeeder extends Seeder
{
    public function run()
    {
        $currentYear = date('Y');
        $adminUserId = 1; // Assuming admin user ID is 1
        
        // Categories data with icons and colors
        $categories = [
            [
                'kode_kategori' => 'WS.1',
                'nama_kategori' => 'Program 3R (Reduce, Reuse, Recycle)',
                'deskripsi' => 'Program pengurangan, penggunaan kembali, dan daur ulang sampah',
                'icon_class' => 'fa-recycle',
                'color_class' => 'success',
                'target_capaian' => 75.00,
                'tahun' => $currentYear,
                'created_by' => $adminUserId
            ],
            [
                'kode_kategori' => 'WS.2',
                'nama_kategori' => 'Program Pengurangan Kertas & Plastik',
                'deskripsi' => 'Inisiatif mengurangi penggunaan kertas dan plastik sekali pakai',
                'icon_class' => 'fa-leaf',
                'color_class' => 'primary',
                'target_capaian' => 80.00,
                'tahun' => $currentYear,
                'created_by' => $adminUserId
            ],
            [
                'kode_kategori' => 'WS.3',
                'nama_kategori' => 'Pengolahan Limbah Organik',
                'deskripsi' => 'Pengelolaan dan pengolahan sampah organik menjadi kompos',
                'icon_class' => 'fa-seedling',
                'color_class' => 'warning',
                'target_capaian' => 70.00,
                'tahun' => $currentYear,
                'created_by' => $adminUserId
            ],
            [
                'kode_kategori' => 'WS.4',
                'nama_kategori' => 'Pengolahan Limbah Anorganik',
                'deskripsi' => 'Pengelolaan sampah anorganik dan material daur ulang',
                'icon_class' => 'fa-cubes',
                'color_class' => 'info',
                'target_capaian' => 65.00,
                'tahun' => $currentYear,
                'created_by' => $adminUserId
            ],
            [
                'kode_kategori' => 'WS.5',
                'nama_kategori' => 'Pengolahan Limbah B3',
                'deskripsi' => 'Pengelolaan limbah bahan berbahaya dan beracun',
                'icon_class' => 'fa-skull-crossbones',
                'color_class' => 'danger',
                'target_capaian' => 90.00,
                'tahun' => $currentYear,
                'created_by' => $adminUserId
            ],
            [
                'kode_kategori' => 'WS.6',
                'nama_kategori' => 'Pengolahan Limbah Cair',
                'deskripsi' => 'Sistem pengolahan air limbah dan air hujan',
                'icon_class' => 'fa-tint',
                'color_class' => 'secondary',
                'target_capaian' => 85.00,
                'tahun' => $currentYear,
                'created_by' => $adminUserId
            ],
            [
                'kode_kategori' => 'WS.7',
                'nama_kategori' => 'Persentase Sampah Didaur Ulang',
                'deskripsi' => 'Monitoring persentase sampah yang berhasil didaur ulang',
                'icon_class' => 'fa-chart-pie',
                'color_class' => 'dark',
                'target_capaian' => 60.00,
                'tahun' => $currentYear,
                'created_by' => $adminUserId
            ]
        ];

        // Insert categories
        foreach ($categories as $category) {
            $this->db->table('uigm_categories')->insert($category);
        }

        // Get inserted category IDs for evidence templates
        $insertedCategories = $this->db->table('uigm_categories')
            ->where('tahun', $currentYear)
            ->get()
            ->getResultArray();

        // Evidence templates for each category
        $evidenceTemplates = [
            'WS.1' => [
                ['nama_bukti' => 'SK Program 3R', 'deskripsi_bukti' => 'Surat Keputusan tentang Program 3R di kampus', 'urutan' => 1],
                ['nama_bukti' => 'SOP Reduce', 'deskripsi_bukti' => 'Standard Operating Procedure untuk pengurangan sampah', 'urutan' => 2],
                ['nama_bukti' => 'SOP Reuse', 'deskripsi_bukti' => 'Standard Operating Procedure untuk penggunaan kembali', 'urutan' => 3],
                ['nama_bukti' => 'SOP Recycle', 'deskripsi_bukti' => 'Standard Operating Procedure untuk daur ulang', 'urutan' => 4],
                ['nama_bukti' => 'Foto Implementasi 3R', 'deskripsi_bukti' => 'Dokumentasi foto kegiatan 3R di kampus', 'urutan' => 5],
                ['nama_bukti' => 'Laporan Monitoring 3R', 'deskripsi_bukti' => 'Laporan hasil monitoring program 3R', 'urutan' => 6],
                ['nama_bukti' => 'Data Pengurangan Sampah', 'deskripsi_bukti' => 'Data kuantitatif pengurangan volume sampah', 'urutan' => 7],
                ['nama_bukti' => 'Sertifikat Pelatihan 3R', 'deskripsi_bukti' => 'Sertifikat pelatihan 3R untuk petugas', 'urutan' => 8],
                ['nama_bukti' => 'Dokumentasi Sosialisasi', 'deskripsi_bukti' => 'Foto dan video sosialisasi program 3R', 'urutan' => 9],
                ['nama_bukti' => 'Evaluasi Program 3R', 'deskripsi_bukti' => 'Dokumen evaluasi efektivitas program 3R', 'urutan' => 10]
            ],
            'WS.2' => [
                ['nama_bukti' => 'SK Pengurangan Kertas & Plastik', 'deskripsi_bukti' => 'Surat Keputusan program pengurangan kertas dan plastik', 'urutan' => 1],
                ['nama_bukti' => 'SOP Paperless Office', 'deskripsi_bukti' => 'Prosedur implementasi kantor tanpa kertas', 'urutan' => 2],
                ['nama_bukti' => 'SOP Pengurangan Plastik', 'deskripsi_bukti' => 'Prosedur pengurangan penggunaan plastik sekali pakai', 'urutan' => 3],
                ['nama_bukti' => 'Data Konsumsi Kertas', 'deskripsi_bukti' => 'Data penggunaan kertas sebelum dan sesudah program', 'urutan' => 4],
                ['nama_bukti' => 'Data Konsumsi Plastik', 'deskripsi_bukti' => 'Data penggunaan plastik sebelum dan sesudah program', 'urutan' => 5],
                ['nama_bukti' => 'Foto Implementasi Digital', 'deskripsi_bukti' => 'Dokumentasi penerapan sistem digital', 'urutan' => 6],
                ['nama_bukti' => 'Foto Pengganti Plastik', 'deskripsi_bukti' => 'Dokumentasi penggunaan alternatif ramah lingkungan', 'urutan' => 7],
                ['nama_bukti' => 'Laporan Penghematan', 'deskripsi_bukti' => 'Laporan penghematan kertas dan plastik', 'urutan' => 8],
                ['nama_bukti' => 'Sertifikat ISO 14001', 'deskripsi_bukti' => 'Sertifikat manajemen lingkungan terkait', 'urutan' => 9],
                ['nama_bukti' => 'Evaluasi Program', 'deskripsi_bukti' => 'Evaluasi efektivitas program pengurangan', 'urutan' => 10]
            ],
            'WS.3' => [
                ['nama_bukti' => 'SK Pengolahan Limbah Organik', 'deskripsi_bukti' => 'Surat Keputusan program pengolahan limbah organik', 'urutan' => 1],
                ['nama_bukti' => 'SOP Composting', 'deskripsi_bukti' => 'Prosedur pembuatan kompos dari limbah organik', 'urutan' => 2],
                ['nama_bukti' => 'Desain Fasilitas Kompos', 'deskripsi_bukti' => 'Gambar teknis fasilitas pengomposan', 'urutan' => 3],
                ['nama_bukti' => 'Foto Fasilitas Kompos', 'deskripsi_bukti' => 'Dokumentasi fasilitas pengomposan', 'urutan' => 4],
                ['nama_bukti' => 'Data Produksi Kompos', 'deskripsi_bukti' => 'Data volume kompos yang dihasilkan', 'urutan' => 5],
                ['nama_bukti' => 'Hasil Uji Kualitas Kompos', 'deskripsi_bukti' => 'Laporan uji laboratorium kualitas kompos', 'urutan' => 6],
                ['nama_bukti' => 'Foto Proses Composting', 'deskripsi_bukti' => 'Dokumentasi proses pembuatan kompos', 'urutan' => 7],
                ['nama_bukti' => 'Sertifikat Pelatihan', 'deskripsi_bukti' => 'Sertifikat pelatihan composting untuk petugas', 'urutan' => 8],
                ['nama_bukti' => 'Laporan Monitoring', 'deskripsi_bukti' => 'Laporan monitoring proses pengomposan', 'urutan' => 9],
                ['nama_bukti' => 'Dokumentasi Distribusi', 'deskripsi_bukti' => 'Foto distribusi kompos untuk pertamanan', 'urutan' => 10]
            ],
            'WS.4' => [
                ['nama_bukti' => 'SK Pengolahan Limbah Anorganik', 'deskripsi_bukti' => 'Surat Keputusan program pengolahan limbah anorganik', 'urutan' => 1],
                ['nama_bukti' => 'SOP Pemilahan Anorganik', 'deskripsi_bukti' => 'Prosedur pemilahan sampah anorganik', 'urutan' => 2],
                ['nama_bukti' => 'Desain TPS Anorganik', 'deskripsi_bukti' => 'Gambar teknis tempat pengumpulan sampah anorganik', 'urutan' => 3],
                ['nama_bukti' => 'Foto Fasilitas TPS', 'deskripsi_bukti' => 'Dokumentasi fasilitas TPS anorganik', 'urutan' => 4],
                ['nama_bukti' => 'Data Pengumpulan Anorganik', 'deskripsi_bukti' => 'Data volume sampah anorganik terkumpul', 'urutan' => 5],
                ['nama_bukti' => 'Kontrak Pengangkutan', 'deskripsi_bukti' => 'Kontrak dengan pihak ketiga pengangkut sampah', 'urutan' => 6],
                ['nama_bukti' => 'Foto Proses Pemilahan', 'deskripsi_bukti' => 'Dokumentasi proses pemilahan sampah', 'urutan' => 7],
                ['nama_bukti' => 'Laporan Penjualan Recycle', 'deskripsi_bukti' => 'Laporan hasil penjualan material daur ulang', 'urutan' => 8],
                ['nama_bukti' => 'Sertifikat Bank Sampah', 'deskripsi_bukti' => 'Sertifikat kerjasama dengan bank sampah', 'urutan' => 9],
                ['nama_bukti' => 'Evaluasi Program', 'deskripsi_bukti' => 'Evaluasi efektivitas pengolahan anorganik', 'urutan' => 10]
            ],
            'WS.5' => [
                ['nama_bukti' => 'SK Pengelolaan Limbah B3', 'deskripsi_bukti' => 'Surat Keputusan pengelolaan limbah B3', 'urutan' => 1],
                ['nama_bukti' => 'Izin Pengelolaan B3', 'deskripsi_bukti' => 'Izin dari instansi berwenang untuk mengelola B3', 'urutan' => 2],
                ['nama_bukti' => 'SOP Penanganan B3', 'deskripsi_bukti' => 'Prosedur penanganan limbah B3', 'urutan' => 3],
                ['nama_bukti' => 'Desain Fasilitas B3', 'deskripsi_bukti' => 'Gambar teknis fasilitas penyimpanan B3', 'urutan' => 4],
                ['nama_bukti' => 'Foto Fasilitas B3', 'deskripsi_bukti' => 'Dokumentasi fasilitas penyimpanan B3', 'urutan' => 5],
                ['nama_bukti' => 'Manifest Limbah B3', 'deskripsi_bukti' => 'Dokumen manifest pengangkutan limbah B3', 'urutan' => 6],
                ['nama_bukti' => 'Kontrak Pengolah B3', 'deskripsi_bukti' => 'Kontrak dengan pengolah limbah B3 berizin', 'urutan' => 7],
                ['nama_bukti' => 'Sertifikat Pelatihan B3', 'deskripsi_bukti' => 'Sertifikat pelatihan penanganan B3', 'urutan' => 8],
                ['nama_bukti' => 'Laporan Monitoring B3', 'deskripsi_bukti' => 'Laporan monitoring pengelolaan B3', 'urutan' => 9],
                ['nama_bukti' => 'Audit Lingkungan B3', 'deskripsi_bukti' => 'Hasil audit pengelolaan limbah B3', 'urutan' => 10]
            ],
            'WS.6' => [
                ['nama_bukti' => 'SK Pengolahan Limbah Cair', 'deskripsi_bukti' => 'Surat Keputusan program pengolahan limbah cair', 'urutan' => 1],
                ['nama_bukti' => 'Izin Pembuangan Air Limbah', 'deskripsi_bukti' => 'Izin pembuangan air limbah dari instansi berwenang', 'urutan' => 2],
                ['nama_bukti' => 'SOP Pengolahan Air Limbah', 'deskripsi_bukti' => 'Prosedur pengolahan air limbah', 'urutan' => 3],
                ['nama_bukti' => 'Desain IPAL', 'deskripsi_bukti' => 'Gambar teknis Instalasi Pengolahan Air Limbah', 'urutan' => 4],
                ['nama_bukti' => 'Foto Fasilitas IPAL', 'deskripsi_bukti' => 'Dokumentasi fasilitas IPAL', 'urutan' => 5],
                ['nama_bukti' => 'Hasil Uji Kualitas Air', 'deskripsi_bukti' => 'Laporan uji laboratorium kualitas air limbah', 'urutan' => 6],
                ['nama_bukti' => 'SOP Pemeliharaan IPAL', 'deskripsi_bukti' => 'Prosedur pemeliharaan fasilitas IPAL', 'urutan' => 7],
                ['nama_bukti' => 'Laporan Monitoring Air', 'deskripsi_bukti' => 'Laporan monitoring kualitas air limbah', 'urutan' => 8],
                ['nama_bukti' => 'Sertifikat Operator IPAL', 'deskripsi_bukti' => 'Sertifikat kompetensi operator IPAL', 'urutan' => 9],
                ['nama_bukti' => 'Evaluasi Kinerja IPAL', 'deskripsi_bukti' => 'Evaluasi kinerja sistem pengolahan air limbah', 'urutan' => 10]
            ],
            'WS.7' => [
                ['nama_bukti' => 'SK Program Daur Ulang', 'deskripsi_bukti' => 'Surat Keputusan program daur ulang sampah', 'urutan' => 1],
                ['nama_bukti' => 'SOP Monitoring Recycle', 'deskripsi_bukti' => 'Prosedur monitoring persentase daur ulang', 'urutan' => 2],
                ['nama_bukti' => 'Data Baseline Sampah', 'deskripsi_bukti' => 'Data awal volume sampah sebelum program', 'urutan' => 3],
                ['nama_bukti' => 'Data Volume Recycle', 'deskripsi_bukti' => 'Data volume sampah yang berhasil didaur ulang', 'urutan' => 4],
                ['nama_bukti' => 'Laporan Perhitungan %', 'deskripsi_bukti' => 'Laporan perhitungan persentase daur ulang', 'urutan' => 5],
                ['nama_bukti' => 'Foto Proses Recycle', 'deskripsi_bukti' => 'Dokumentasi proses daur ulang', 'urutan' => 6],
                ['nama_bukti' => 'Kontrak Mitra Recycle', 'deskripsi_bukti' => 'Kontrak kerjasama dengan mitra daur ulang', 'urutan' => 7],
                ['nama_bukti' => 'Sertifikat Verifikasi', 'deskripsi_bukti' => 'Sertifikat verifikasi dari pihak ketiga', 'urutan' => 8],
                ['nama_bukti' => 'Laporan Triwulan', 'deskripsi_bukti' => 'Laporan triwulan persentase daur ulang', 'urutan' => 9],
                ['nama_bukti' => 'Evaluasi Target', 'deskripsi_bukti' => 'Evaluasi pencapaian target persentase daur ulang', 'urutan' => 10]
            ]
        ];

        // Insert evidence templates
        foreach ($insertedCategories as $category) {
            $kodeKategori = $category['kode_kategori'];
            if (isset($evidenceTemplates[$kodeKategori])) {
                foreach ($evidenceTemplates[$kodeKategori] as $evidence) {
                    $evidence['kategori_id'] = $category['id'];
                    $this->db->table('uigm_evidence')->insert($evidence);
                }
            }
        }
    }
}