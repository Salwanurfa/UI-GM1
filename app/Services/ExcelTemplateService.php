<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * ExcelTemplateService
 * 
 * Service untuk generate file template Excel untuk import user.
 * Template berisi header, baris contoh, komentar sel, dan bagian catatan.
 * 
 * @package App\Services
 */
class ExcelTemplateService
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // Constructor untuk dependency injection jika diperlukan di masa depan
    }

    /**
     * Generate file template Excel
     * 
     * Membuat file template Excel dengan struktur lengkap:
     * - Baris header dengan styling
     * - Baris data contoh
     * - Komentar sel dengan instruksi
     * - Bagian catatan dengan aturan validasi
     * 
     * @return string Path ke file template yang dihasilkan
     * @throws \Exception Jika gagal membuat atau menyimpan file template
     */
    public function generateTemplate(): string
    {
        try {
            // Buat spreadsheet baru
            $spreadsheet = new Spreadsheet();
            
            // Dapatkan sheet aktif dan beri nama 'users'
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('users');
            
            // Tambahkan baris header dengan styling
            $this->addHeaderRow($sheet);
            
            // Tambahkan baris contoh
            $this->addExampleRows($sheet);
            
            // Tambahkan komentar sel
            $this->addCellComments($sheet);
            
            // Tambahkan bagian catatan
            $this->addNotesSection($sheet);
            
            // Simpan file ke direktori temporary
            $filename = 'user_import_template.xlsx';
            $filepath = WRITEPATH . 'tmp/' . $filename;
            
            // Pastikan direktori tmp ada
            if (!is_dir(WRITEPATH . 'tmp/')) {
                mkdir(WRITEPATH . 'tmp/', 0755, true);
            }
            
            // Tulis file Excel
            $writer = new Xlsx($spreadsheet);
            $writer->save($filepath);
            
            // Bersihkan memory
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            
            return $filepath;
            
        } catch (\Exception $e) {
            log_message('error', 'Gagal generate template Excel: ' . $e->getMessage());
            throw new \Exception('Gagal membuat template Excel: ' . $e->getMessage());
        }
    }

    /**
     * Tambahkan baris header dengan styling
     * 
     * Menambahkan baris header ke worksheet dengan:
     * - Kolom: username, email, nama_lengkap, role, unit_name, is_active, password
     * - Font bold
     * - Warna background
     * - Frozen pane
     * - Lebar kolom yang sesuai
     * 
     * @param Worksheet $sheet Worksheet Excel
     * @return void
     */
    private function addHeaderRow($sheet): void
    {
        // Definisi kolom header dalam urutan yang tepat
        $headers = [
            'username',
            'email',
            'nama_lengkap',
            'role',
            'nama_gedung',
            'is_active',
            'password'
        ];

        // Set nilai header di baris 1
        $columnLetters = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        for ($i = 0; $i < count($headers); $i++) {
            $sheet->setCellValue($columnLetters[$i] . '1', $headers[$i]);
        }

        // Terapkan styling ke baris header
        $headerRange = 'A1:G1'; // A sampai G untuk 7 kolom
        
        // Set font bold
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        
        // Set warna background (light blue/gray)
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFD9E1F2'); // Light blue color
        
        // Freeze header row pane (freeze baris 1)
        $sheet->freezePane('A2'); // Freeze semua baris di atas baris 2
        
        // Set lebar kolom untuk keterbacaan
        $sheet->getColumnDimension('A')->setWidth(25); // username (diperpanjang)
        $sheet->getColumnDimension('B')->setWidth(30); // email
        $sheet->getColumnDimension('C')->setWidth(30); // nama_lengkap
        $sheet->getColumnDimension('D')->setWidth(15); // role
        $sheet->getColumnDimension('E')->setWidth(30); // nama_gedung
        $sheet->getColumnDimension('F')->setWidth(12); // is_active
        $sheet->getColumnDimension('G')->setWidth(15); // password
    }

    /**
     * Tambahkan baris data contoh
     * 
     * Menambahkan 3 baris contoh dengan data DUMMY/TEMPLATE untuk role berbeda.
     * Data ini adalah CONTOH yang bisa diedit/dihapus oleh admin.
     * 
     * @param Worksheet $sheet Worksheet Excel
     * @return void
     */
    private function addExampleRows($sheet): void
    {
        // Data CONTOH/DUMMY untuk 3 role berbeda
        $exampleData = [
            // Baris 2: User biasa - CONTOH
            [
                'username' => 'user.contoh',
                'email' => 'user.contoh@example.com',
                'nama_lengkap' => 'User Contoh',
                'role' => 'user',
                'nama_gedung' => 'Gedung A',
                'is_active' => '1',
                'password' => 'password123'
            ],
            // Baris 3: Pengelola TPS - CONTOH
            [
                'username' => 'tps.contoh',
                'email' => 'tps.contoh@example.com',
                'nama_lengkap' => 'Pengelola TPS Contoh',
                'role' => 'pengelola_tps',
                'nama_gedung' => 'Gedung B',
                'is_active' => '1',
                'password' => 'password123'
            ],
            // Baris 4: Admin Pusat - CONTOH
            [
                'username' => 'admin.contoh',
                'email' => 'admin.contoh@example.com',
                'nama_lengkap' => 'Admin Contoh',
                'role' => 'admin_pusat',
                'nama_gedung' => 'Gedung C',
                'is_active' => '1',
                'password' => 'password123'
            ]
        ];

        // Kolom letters untuk mapping
        $columnLetters = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        $columnKeys = ['username', 'email', 'nama_lengkap', 'role', 'nama_gedung', 'is_active', 'password'];

        // Tambahkan setiap baris contoh mulai dari baris 2
        $rowNumber = 2;
        foreach ($exampleData as $data) {
            for ($i = 0; $i < count($columnKeys); $i++) {
                $cellAddress = $columnLetters[$i] . $rowNumber;
                $sheet->setCellValue($cellAddress, $data[$columnKeys[$i]]);
            }
            $rowNumber++;
        }
        
        // Tidak perlu tambah baris kosong, biarkan admin isi sendiri
    }

    /**
     * Tambahkan komentar sel dengan instruksi
     * 
     * Menambahkan komentar SANGAT RINGKAS ke header.
     * Detail lengkap ada di panduan modal.
     * 
     * @param Worksheet $sheet Worksheet Excel
     * @return void
     */
    private function addCellComments($sheet): void
    {
        // Komentar untuk kolom username (A1)
        $sheet->getComment('A1')->getText()->createTextRun(
            "WAJIB | 3-100 karakter | Boleh spasi | Harus unik"
        );
        $sheet->getComment('A1')->setWidth('200px');
        $sheet->getComment('A1')->setHeight('50px');

        // Komentar untuk kolom email (B1)
        $sheet->getComment('B1')->getText()->createTextRun(
            "WAJIB | Format email valid | Harus unik"
        );
        $sheet->getComment('B1')->setWidth('200px');
        $sheet->getComment('B1')->setHeight('50px');

        // Komentar untuk kolom nama_lengkap (C1)
        $sheet->getComment('C1')->getText()->createTextRun(
            "WAJIB | Max 150 karakter"
        );
        $sheet->getComment('C1')->setWidth('200px');
        $sheet->getComment('C1')->setHeight('40px');

        // Komentar untuk kolom role (D1)
        $sheet->getComment('D1')->getText()->createTextRun(
            "WAJIB | user | pengelola_tps | admin_pusat | super_admin"
        );
        $sheet->getComment('D1')->setWidth('200px');
        $sheet->getComment('D1')->setHeight('50px');

        // Komentar untuk kolom nama_gedung (E1)
        $sheet->getComment('E1')->getText()->createTextRun(
            "OPSIONAL | Boleh spasi | Auto-create jika tidak ada"
        );
        $sheet->getComment('E1')->setWidth('200px');
        $sheet->getComment('E1')->setHeight('50px');

        // Komentar untuk kolom is_active (F1)
        $sheet->getComment('F1')->getText()->createTextRun(
            "OPSIONAL | 1=Aktif | 0=Nonaktif | Default: 1"
        );
        $sheet->getComment('F1')->setWidth('200px');
        $sheet->getComment('F1')->setHeight('50px');

        // Komentar untuk kolom password (G1)
        $sheet->getComment('G1')->getText()->createTextRun(
            "OPSIONAL | Min 6 karakter | Kosongkan untuk auto-generate"
        );
        $sheet->getComment('G1')->setWidth('200px');
        $sheet->getComment('G1')->setHeight('50px');
    }

    /**
     * Tambahkan bagian catatan
     * 
     * TIDAK MENAMBAHKAN CATATAN LAGI!
     * Semua panduan sudah dipindahkan ke modal UI.
     * Template Excel hanya berisi header dan contoh data.
     * 
     * @param Worksheet $sheet Worksheet Excel
     * @return void
     */
    private function addNotesSection($sheet): void
    {
        // TIDAK ADA CATATAN DI EXCEL
        // Semua panduan ada di modal import
        // Template bersih, hanya data
    }
}
