<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UigmTablesSeeder extends Seeder
{
    public function run()
    {
        // Create uigm_categories table
        $sql1 = "
        CREATE TABLE IF NOT EXISTS uigm_categories (
            id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            kode_kategori VARCHAR(10) NOT NULL COMMENT 'WS.1, WS.2, WS.3, etc.',
            nama_kategori VARCHAR(255) NOT NULL,
            deskripsi TEXT NULL,
            icon_class VARCHAR(50) NOT NULL DEFAULT 'fa-leaf',
            color_class VARCHAR(20) NOT NULL DEFAULT 'primary',
            target_capaian DECIMAL(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Target dalam persentase (0.00 - 100.00)',
            tahun YEAR(4) NOT NULL,
            status_aktif TINYINT(1) NOT NULL DEFAULT 1,
            created_by INT(11) UNSIGNED NOT NULL,
            created_at DATETIME NULL,
            updated_at DATETIME NULL,
            PRIMARY KEY (id),
            UNIQUE KEY tahun_kode (tahun, kode_kategori)
        ) DEFAULT CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci;
        ";

        // Create uigm_evidence table
        $sql2 = "
        CREATE TABLE IF NOT EXISTS uigm_evidence (
            id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            kategori_id INT(11) UNSIGNED NOT NULL,
            nama_bukti VARCHAR(255) NOT NULL COMMENT 'Nama dokumen bukti (SK, SOP, Foto, dll)',
            deskripsi_bukti TEXT NULL COMMENT 'Deskripsi detail bukti yang dibutuhkan',
            file_path VARCHAR(255) NULL COMMENT 'Path file bukti dukung',
            file_name VARCHAR(255) NULL COMMENT 'Nama file asli',
            file_size INT(11) NULL COMMENT 'Ukuran file dalam bytes',
            file_type VARCHAR(50) NULL COMMENT 'MIME type file',
            status_upload ENUM('belum_upload', 'sudah_upload', 'perlu_revisi') NOT NULL DEFAULT 'belum_upload',
            keterangan TEXT NULL COMMENT 'Catatan atau keterangan tambahan',
            urutan INT(3) NOT NULL DEFAULT 1 COMMENT 'Urutan tampil bukti',
            uploaded_by INT(11) UNSIGNED NULL,
            uploaded_at DATETIME NULL,
            created_at DATETIME NULL,
            updated_at DATETIME NULL,
            PRIMARY KEY (id),
            KEY kategori_id (kategori_id),
            KEY urutan (urutan)
        ) DEFAULT CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci;
        ";

        try {
            $this->db->query($sql1);
            echo "Table uigm_categories created successfully\n";
        } catch (\Exception $e) {
            echo "Error creating uigm_categories: " . $e->getMessage() . "\n";
        }

        try {
            $this->db->query($sql2);
            echo "Table uigm_evidence created successfully\n";
        } catch (\Exception $e) {
            echo "Error creating uigm_evidence: " . $e->getMessage() . "\n";
        }

        // Insert categories data
        $currentYear = date('Y');
        $categories = [
            [
                'kode_kategori' => 'WS.1',
                'nama_kategori' => 'Program 3R (Reduce, Reuse, Recycle)',
                'deskripsi' => 'Program pengurangan, penggunaan kembali, dan daur ulang sampah',
                'icon_class' => 'fa-recycle',
                'color_class' => 'success',
                'target_capaian' => 80.00,
                'tahun' => $currentYear,
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'kode_kategori' => 'WS.2',
                'nama_kategori' => 'Program Pengurangan Kertas & Plastik',
                'deskripsi' => 'Program pengurangan penggunaan kertas dan plastik',
                'icon_class' => 'fa-file-alt',
                'color_class' => 'info',
                'target_capaian' => 75.00,
                'tahun' => $currentYear,
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'kode_kategori' => 'WS.3',
                'nama_kategori' => 'Pengolahan Limbah Organik',
                'deskripsi' => 'Program pengolahan dan pengelolaan limbah organik',
                'icon_class' => 'fa-seedling',
                'color_class' => 'warning',
                'target_capaian' => 85.00,
                'tahun' => $currentYear,
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'kode_kategori' => 'WS.4',
                'nama_kategori' => 'Pengolahan Limbah Anorganik',
                'deskripsi' => 'Program pengolahan dan pengelolaan limbah anorganik',
                'icon_class' => 'fa-trash-alt',
                'color_class' => 'danger',
                'target_capaian' => 70.00,
                'tahun' => $currentYear,
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'kode_kategori' => 'WS.5',
                'nama_kategori' => 'Pengolahan Limbah B3',
                'deskripsi' => 'Program pengolahan dan pengelolaan limbah bahan berbahaya dan beracun',
                'icon_class' => 'fa-exclamation-triangle',
                'color_class' => 'dark',
                'target_capaian' => 90.00,
                'tahun' => $currentYear,
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'kode_kategori' => 'WS.6',
                'nama_kategori' => 'Pengolahan Limbah Cair',
                'deskripsi' => 'Program pengolahan dan pengelolaan limbah cair',
                'icon_class' => 'fa-tint',
                'color_class' => 'primary',
                'target_capaian' => 85.00,
                'tahun' => $currentYear,
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'kode_kategori' => 'WS.7',
                'nama_kategori' => 'Persentase Sampah Didaur Ulang',
                'deskripsi' => 'Program monitoring dan peningkatan persentase sampah yang didaur ulang',
                'icon_class' => 'fa-chart-pie',
                'color_class' => 'secondary',
                'target_capaian' => 60.00,
                'tahun' => $currentYear,
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Insert categories
        foreach ($categories as $category) {
            try {
                $this->db->table('uigm_categories')->insert($category);
                echo "Category {$category['kode_kategori']} inserted successfully\n";
            } catch (\Exception $e) {
                echo "Error inserting category {$category['kode_kategori']}: " . $e->getMessage() . "\n";
            }
        }

        echo "UIGM Tables and initial data created successfully!\n";
    }
}