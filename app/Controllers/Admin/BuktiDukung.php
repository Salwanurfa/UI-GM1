<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BuktiDukungModel;

class BuktiDukung extends BaseController
{
    protected $buktiDukungModel;

    public function __construct()
    {
        $this->buktiDukungModel = new BuktiDukungModel();
    }

    public function index()
    {
        // Check if user is logged in and has admin role
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        $user = session()->get('user');
        if (!in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return redirect()->to('/auth/login');
        }

        $data = [
            'title' => 'Bukti Dukung',
            'user' => $user,
            'bukti_dukung_list' => $this->buktiDukungModel->getAllFormatted()
        ];

        return view('admin_pusat/bukti_dukung', $data);
    }

    public function upload()
    {
        if (!$this->request->getMethod() === 'POST') {
            return redirect()->back();
        }

        $judul = $this->request->getPost('judul');
        $periode = $this->request->getPost('periode');
        
        if (empty($judul) || empty($periode)) {
            session()->setFlashdata('error', 'Judul dan periode wajib diisi');
            return redirect()->back();
        }

        // Handle file upload
        $file = $this->request->getFile('file_bukti');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Validate file size (max 10MB)
            if ($file->getSize() > 10 * 1024 * 1024) {
                session()->setFlashdata('error', 'Ukuran file terlalu besar! Maksimal 10MB');
                return redirect()->back();
            }

            // Validate file type
            $allowedMimes = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'image/jpeg',
                'image/png'
            ];

            if (!in_array($file->getMimeType(), $allowedMimes)) {
                session()->setFlashdata('error', 'Format file tidak didukung! Gunakan PDF, DOC, DOCX, XLS, XLSX, JPG, atau PNG');
                return redirect()->back();
            }

            // Create upload directory if not exists - using public folder
            $uploadPath = 'public/uploads/bukti_dukung/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Generate unique filename
            $fileName = time() . '_' . $file->getRandomName();
            
            // Move file
            if ($file->move($uploadPath, $fileName)) {
                // Format file size for storage
                $fileSize = $this->buktiDukungModel->formatFileSize($file->getSize());
                
                // Save to database with new structure
                $data = [
                    'judul' => $judul,
                    'periode' => $periode,
                    'nama_file' => $fileName,
                    'ukuran_file' => $fileSize,
                    'tipe_file' => $file->getMimeType()
                ];

                if ($this->buktiDukungModel->insert($data)) {
                    session()->setFlashdata('success', 'Bukti dukung berhasil diupload');
                } else {
                    // Delete uploaded file if database insert fails
                    unlink($uploadPath . $fileName);
                    session()->setFlashdata('error', 'Gagal menyimpan data ke database');
                }
            } else {
                session()->setFlashdata('error', 'Gagal mengupload file');
            }
        } else {
            session()->setFlashdata('error', 'File bukti dukung wajib diupload');
        }

        return redirect()->to('/admin-pusat/bukti-dukung');
    }

    public function delete($id)
    {
        // Get file info before deleting
        $buktiDukung = $this->buktiDukungModel->find($id);
        
        if (!$buktiDukung) {
            session()->setFlashdata('error', 'Data bukti dukung tidak ditemukan');
            return redirect()->to('/admin-pusat/bukti-dukung');
        }

        // Delete file from filesystem - using public folder
        $filePath = 'public/uploads/bukti_dukung/' . $buktiDukung['nama_file'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete from database
        if ($this->buktiDukungModel->delete($id)) {
            session()->setFlashdata('success', 'Bukti dukung berhasil dihapus');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus bukti dukung');
        }

        return redirect()->to('/admin-pusat/bukti-dukung');
    }

    public function download($id)
    {
        $buktiDukung = $this->buktiDukungModel->find($id);
        
        if (!$buktiDukung) {
            session()->setFlashdata('error', 'File tidak ditemukan');
            return redirect()->to('/admin-pusat/bukti-dukung');
        }

        $filePath = 'public/uploads/bukti_dukung/' . $buktiDukung['nama_file'];
        
        if (!file_exists($filePath)) {
            session()->setFlashdata('error', 'File tidak ditemukan di server');
            return redirect()->to('/admin-pusat/bukti-dukung');
        }

        // Get original filename for download
        $originalName = $buktiDukung['judul'] . '_' . $buktiDukung['periode'] . '.' . pathinfo($buktiDukung['nama_file'], PATHINFO_EXTENSION);
        
        return $this->response->download($filePath, null)->setFileName($originalName);
    }
}