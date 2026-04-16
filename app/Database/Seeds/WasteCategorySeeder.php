<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class WasteCategorySeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'kategori_utama' => 'Limbah Organik',
                'sub_kategori' => 'Limbah Organik',
                'deskripsi' => 'Limbah yang berasal dari makhluk hidup dan dapat terurai secara alami',
                'metode_pengolahan_default' => 'kompos',
                'target_pengurangan' => 30.00,
                'status_aktif' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'kategori_utama' => 'Limbah Anorganik',
                'sub_kategori' => 'Limbah Anorganik',
                'deskripsi' => 'Limbah yang tidak dapat terurai secara alami seperti plastik, logam, kaca',
                'metode_pengolahan_default' => 'daur_ulang',
                'target_pengurangan' => 25.00,
                'status_aktif' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'kategori_utama' => 'Limbah B3',
                'sub_kategori' => 'Bahan Berbahaya & Beracun',
                'deskripsi' => 'Limbah yang mengandung bahan berbahaya dan beracun yang memerlukan penanganan khusus',
                'metode_pengolahan_default' => 'treatment_khusus',
                'target_pengurangan' => 50.00,
                'status_aktif' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'kategori_utama' => 'Limbah Cair',
                'sub_kategori' => 'Limbah Cair',
                'deskripsi' => 'Limbah dalam bentuk cair yang memerlukan pengolahan sebelum dibuang',
                'metode_pengolahan_default' => 'treatment_air',
                'target_pengurangan' => 20.00,
                'status_aktif' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'kategori_utama' => 'Pengurangan Kertas & Plastik',
                'sub_kategori' => 'Pengurangan Kertas & Plastik',
                'deskripsi' => 'Program khusus untuk mengurangi penggunaan kertas dan plastik sekali pakai',
                'metode_pengolahan_default' => 'reduce_reuse',
                'target_pengurangan' => 40.00,
                'status_aktif' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'kategori_utama' => 'Program 3R',
                'sub_kategori' => 'Reduce, Reuse, Recycle',
                'deskripsi' => 'Program komprehensif untuk mengurangi, menggunakan kembali, dan mendaur ulang limbah',
                'metode_pengolahan_default' => 'reduce_reuse_recycle',
                'target_pengurangan' => 35.00,
                'status_aktif' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Check if categories already exist to avoid duplicates
        foreach ($data as $category) {
            $existing = $this->db->table('waste_categories')
                                ->where('kategori_utama', $category['kategori_utama'])
                                ->get()
                                ->getRow();
            
            if (!$existing) {
                $this->db->table('waste_categories')->insert($category);
                echo "Inserted category: " . $category['kategori_utama'] . "\n";
            } else {
                echo "Category already exists: " . $category['kategori_utama'] . "\n";
            }
        }
    }
}