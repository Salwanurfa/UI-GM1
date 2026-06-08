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

        // Get filter kategori from query string
        $filterKategori = $this->request->getGet('kategori');
        
        // Get data with optional filter
        if ($filterKategori && $filterKategori !== 'semua') {
            $buktiDukungList = $this->buktiDukungModel
                ->where('kategori', $filterKategori)
                ->orderBy('uploaded_at', 'DESC')
                ->findAll();
            
            // Format file size
            foreach ($buktiDukungList as &$item) {
                // File size already formatted in database
            }
        } else {
            $buktiDukungList = $this->buktiDukungModel->getAllFormatted();
        }

        $data = [
            'title' => 'Bukti Dukung',
            'user' => $user,
            'bukti_dukung_list' => $buktiDukungList,
            'filter_kategori' => $filterKategori ?? 'semua'
        ];

        return view('admin_pusat/bukti_dukung/index', $data);
    }

    public function upload()
    {
        if ($this->request->getMethod() !== 'POST') {
            return redirect()->back();
        }

        $judul = $this->request->getPost('judul');
        $periode = $this->request->getPost('periode');
        $kategori = $this->request->getPost('kategori');
        
        if (empty($judul) || empty($periode) || empty($kategori)) {
            session()->setFlashdata('error', 'Judul, periode, dan kategori wajib diisi');
            return redirect()->back();
        }

        // Handle file upload
        $file = $this->request->getFile('file_bukti');
        
        // Check if file exists and is valid
        if (!$file || !$file->isValid()) {
            $error = $file ? $file->getErrorString() : 'No file uploaded';
            log_message('error', 'File upload validation failed: ' . $error);
            session()->setFlashdata('error', 'File bukti dukung wajib diupload dan harus valid. Error: ' . $error);
            return redirect()->back();
        }

        // Check if file has moved (security check)
        if ($file->hasMoved()) {
            log_message('error', 'File has already been moved: ' . $file->getClientName());
            session()->setFlashdata('error', 'File sudah dipindahkan sebelumnya');
            return redirect()->back();
        }

        // Additional file existence check
        $tempPath = $file->getTempName();
        if (!file_exists($tempPath)) {
            log_message('error', 'Temporary file does not exist: ' . $tempPath);
            session()->setFlashdata('error', 'File temporary tidak ditemukan');
            return redirect()->back();
        }

        // Get file size
        $fileSize = $file->getSize();

        // Validate file size (max from config or 10MB)
        $maxFileSize = env('upload.maxFileSize', 10485760); // 10MB default
        if ($fileSize > $maxFileSize) {
            $maxSizeMB = round($maxFileSize / (1024 * 1024), 1);
            session()->setFlashdata('error', "Ukuran file terlalu besar! Maksimal {$maxSizeMB}MB");
            return redirect()->back();
        }

        // Get file extension for validation (more reliable than MIME type)
        $fileExtension = strtolower($file->getClientExtension());
        $allowedTypesConfig = env('upload.allowedTypes', 'pdf|doc|docx|xls|xlsx|jpg|jpeg|png');
        $allowedExtensions = explode('|', $allowedTypesConfig);
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            session()->setFlashdata('error', 'Format file tidak didukung! Gunakan: ' . strtoupper(implode(', ', $allowedExtensions)));
            return redirect()->back();
        }

        // Get MIME type safely with error handling
        $mimeType = '';
        try {
            // Check if fileinfo extension is available
            if (!extension_loaded('fileinfo')) {
                log_message('warning', 'Fileinfo extension not loaded, using fallback MIME detection');
                throw new Exception('Fileinfo extension not available');
            }
            
            $mimeType = $file->getMimeType();
            log_message('info', "MIME type detected: $mimeType for file: " . $file->getClientName());
        } catch (Exception $e) {
            log_message('error', 'MIME type detection failed: ' . $e->getMessage());
            // Fallback to extension-based MIME type
            $mimeTypes = [
                'pdf' => 'application/pdf',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'xls' => 'application/vnd.ms-excel',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png'
            ];
            $mimeType = $mimeTypes[$fileExtension] ?? 'application/octet-stream';
            log_message('info', "Using fallback MIME type: $mimeType for extension: $fileExtension");
        }

        // Create upload directory if not exists - using public folder
        $uploadPath = 'public/uploads/bukti_dukung/';
        if (!is_dir($uploadPath)) {
            if (!mkdir($uploadPath, 0755, true)) {
                session()->setFlashdata('error', 'Gagal membuat direktori upload');
                return redirect()->back();
            }
        }

        // Generate unique filename
        $fileName = time() . '_' . $file->getRandomName();
        
        // Move file
        if ($file->move($uploadPath, $fileName)) {
            // Format file size for storage
            $fileSize = $this->buktiDukungModel->formatFileSize($fileSize);
            
            // Save to database with new structure
            $data = [
                'judul' => $judul,
                'periode' => $periode,
                'kategori' => $kategori,
                'nama_file' => $fileName,
                'ukuran_file' => $fileSize,
                'tipe_file' => $mimeType,
                'uploaded_at' => date('Y-m-d H:i:s')
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

    /**
     * Serve file for preview (inline display)
     */
    public function preview($id)
    {
        log_message('info', "Preview called for ID: {$id}");
        
        $buktiDukung = $this->buktiDukungModel->find($id);
        
        if (!$buktiDukung) {
            log_message('error', "File not found in database for ID: {$id}");
            return $this->response->setStatusCode(404)->setBody('File tidak ditemukan');
        }

        $filePath = 'public/uploads/bukti_dukung/' . $buktiDukung['nama_file'];
        log_message('info', "Looking for file at: {$filePath}");
        
        if (!file_exists($filePath)) {
            log_message('error', "File not found on filesystem: {$filePath}");
            return $this->response->setStatusCode(404)->setBody('File tidak ditemukan di server. Path: ' . $filePath);
        }

        // Get file info
        $fileExtension = strtolower(pathinfo($buktiDukung['nama_file'], PATHINFO_EXTENSION));
        $mimeType = $buktiDukung['tipe_file'] ?? 'application/octet-stream';
        
        log_message('info', "Serving file: {$buktiDukung['nama_file']}, MIME: {$mimeType}, Extension: {$fileExtension}");
        
        // Set appropriate headers for inline display
        $this->response->setHeader('Content-Type', $mimeType);
        $this->response->setHeader('Content-Disposition', 'inline; filename="' . $buktiDukung['nama_file'] . '"');
        $this->response->setHeader('Content-Length', filesize($filePath));
        $this->response->setHeader('Cache-Control', 'public, max-age=3600');
        
        // For PDFs, add additional headers
        if ($fileExtension === 'pdf') {
            $this->response->setHeader('X-Content-Type-Options', 'nosniff');
        }
        
        // For images, add CORS headers
        if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $this->response->setHeader('Access-Control-Allow-Origin', '*');
        }
        
        // Read and output file
        $this->response->setBody(file_get_contents($filePath));
        
        log_message('info', "File served successfully: {$buktiDukung['nama_file']}");
        
        return $this->response;
    }
}