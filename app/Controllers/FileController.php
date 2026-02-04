<?php

namespace App\Controllers;

use CodeIgniter\Files\File;

class FileController extends BaseController
{
    protected $allowedTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png'];
    protected $maxSize = 5120; // 5MB in KB

    /**
     * Upload file dokumen pendukung
     */
    public function upload()
    {
        $request = $this->request;
        $pengirimanId = $request->getPost('pengiriman_id');
        $indikatorId = $request->getPost('indikator_id');

        if (!$pengirimanId || !$indikatorId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parameter tidak lengkap'
            ]);
        }

        $file = $request->getFile('file');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File tidak valid'
            ]);
        }

        // Validasi tipe file
        if (!in_array($file->getExtension(), $this->allowedTypes)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tipe file tidak diizinkan. Gunakan: ' . implode(', ', $this->allowedTypes)
            ]);
        }

        // Validasi ukuran file
        if ($file->getSizeByUnit('kb') > $this->maxSize) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Ukuran file terlalu besar. Maksimal 5MB'
            ]);
        }

        try {
            // Buat direktori jika belum ada
            $uploadPath = WRITEPATH . 'uploads/documents/' . $pengirimanId . '/' . $indikatorId;
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Generate nama file unik
            $fileName = time() . '_' . $file->getRandomName();

            // Pindahkan file
            $file->move($uploadPath, $fileName);

            // Simpan info file ke database (opsional - bisa ditambahkan tabel file_uploads)
            $fileInfo = [
                'pengiriman_id' => $pengirimanId,
                'indikator_id' => $indikatorId,
                'original_name' => $file->getClientName(),
                'file_name' => $fileName,
                'file_path' => $uploadPath . '/' . $fileName,
                'file_size' => $file->getSizeByUnit('kb'),
                'file_type' => $file->getClientMimeType(),
                'uploaded_at' => date('Y-m-d H:i:s')
            ];

            return $this->response->setJSON([
                'success' => true,
                'message' => 'File berhasil diupload',
                'file_info' => $fileInfo
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal upload file: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Download file
     */
    public function download($pengirimanId, $indikatorId, $fileName)
    {
        $filePath = WRITEPATH . 'uploads/documents/' . $pengirimanId . '/' . $indikatorId . '/' . $fileName;

        if (!file_exists($filePath)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('File tidak ditemukan');
        }

        return $this->response->download($filePath, null);
    }

    /**
     * Delete file
     */
    public function delete()
    {
        $request = $this->request;
        $pengirimanId = $request->getPost('pengiriman_id');
        $indikatorId = $request->getPost('indikator_id');
        $fileName = $request->getPost('file_name');

        $filePath = WRITEPATH . 'uploads/documents/' . $pengirimanId . '/' . $indikatorId . '/' . $fileName;

        if (file_exists($filePath)) {
            unlink($filePath);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'File berhasil dihapus'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'File tidak ditemukan'
        ]);
    }

    /**
     * List files untuk kategori tertentu
     */
    public function listFiles($pengirimanId, $indikatorId)
    {
        $uploadPath = WRITEPATH . 'uploads/documents/' . $pengirimanId . '/' . $indikatorId;

        if (!is_dir($uploadPath)) {
            return $this->response->setJSON([
                'success' => true,
                'files' => []
            ]);
        }

        $files = [];
        $directory = new \DirectoryIterator($uploadPath);

        foreach ($directory as $file) {
            if ($file->isDot()) continue;

            $files[] = [
                'name' => $file->getFilename(),
                'size' => $file->getSize(),
                'modified' => date('Y-m-d H:i:s', $file->getMTime()),
                'download_url' => base_url("file/download/{$pengirimanId}/{$indikatorId}/" . $file->getFilename())
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'files' => $files
        ]);
    }
}
