<?php

// File: app/Controllers/User/Kriteria.php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\KriteriaModel;
use CodeIgniter\HTTP\ResponseInterface;

class Kriteria extends BaseController
{
    protected $kriteriaModel;
    protected $validation;

    public function __construct()
    {
        $this->kriteriaModel = new KriteriaModel();
        $this->validation = \Config\Services::validation();
    }

    /**
     * Menampilkan halaman utama kriteria user
     * Sidebar kategori + form input + tabel data user
     */
    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        // Validasi user login dan role
        if (!session()->get('isLoggedIn') || session()->get('user')['role'] !== 'user') {
            return redirect()->to('/auth/login')->with('error', 'Akses ditolak. Silakan login sebagai user.');
        }

        $userId = session()->get('user')['id'];
        $jenisFilter = $this->request->getGet('jenis') ?? 'Semua';

        // Ambil data kriteria berdasarkan user_id dan filter jenis
        $data = $this->getUserKriteriaData($userId, $jenisFilter);

        // Data untuk view
        $viewData = [
            'title' => 'Manajemen Data Sampah',
            'user_name' => session()->get('user')['username'],
            'kriteria_data' => $data,
            'jenis_filter' => $jenisFilter,
            'jenis_sampah_options' => $this->getJenisSampahOptions(),
            'gedung_options' => $this->getGedungOptions(),
            'validation' => $this->validation
        ];

        return view('user/kriteria', $viewData);
    }

    /**
     * Menyimpan data input user
     */
    public function save(): ResponseInterface
    {
        // Validasi user login dan role
        if (!session()->get('isLoggedIn') || session()->get('user')['role'] !== 'user') {
            return redirect()->to('/auth/login')->with('error', 'Akses ditolak.');
        }

        try {
            // Rules validasi
            $rules = [
            'tanggal' => [
                'rules' => 'required|valid_date[Y-m-d]',
                'errors' => [
                    'required' => 'Tanggal harus diisi.',
                    'valid_date' => 'Format tanggal tidak valid.'
                ]
            ],
            'unit_prodi' => [
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'Unit/Jurusan/Prodi harus diisi.',
                    'min_length' => 'Unit/Jurusan/Prodi minimal 3 karakter.',
                    'max_length' => 'Unit/Jurusan/Prodi maksimal 100 karakter.'
                ]
            ],
            'jenis_sampah' => [
                'rules' => 'required|max_length[100]',
                'errors' => [
                    'required' => 'Jenis sampah harus dipilih.',
                    'max_length' => 'Jenis sampah maksimal 100 karakter.'
                ]
            ],
            'satuan' => [
                'rules' => 'required|max_length[10]',
                'errors' => [
                    'required' => 'Satuan harus diisi.',
                    'max_length' => 'Satuan maksimal 10 karakter.'
                ]
            ],
            'jumlah' => [
                'rules' => 'required|numeric|greater_than[0]',
                'errors' => [
                    'required' => 'Jumlah harus diisi.',
                    'numeric' => 'Jumlah harus berupa angka.',
                    'greater_than' => 'Jumlah harus lebih dari 0.'
                ]
            ],
            'gedung' => [
                'rules' => 'required|max_length[50]',
                'errors' => [
                    'required' => 'Gedung harus dipilih.',
                    'max_length' => 'Nama gedung maksimal 50 karakter.'
                ]
            ]
        ];

        // Validasi input
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Siapkan data untuk disimpan
        $user = session()->get('user');
        $saveData = [
            'user_id' => $user['id'],
            'unit_id' => $user['unit_id'] ?? null, // Ambil unit_id dari session user
            'tanggal' => $this->request->getPost('tanggal'),
            'unit_prodi' => trim($this->request->getPost('unit_prodi')),
            'jenis_sampah' => $this->request->getPost('jenis_sampah'),
            'satuan' => $this->request->getPost('satuan'),
            'jumlah' => (float) $this->request->getPost('jumlah'),
            'gedung' => $this->request->getPost('gedung'),
            'status_review' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Simpan ke database
        try {
            $this->kriteriaModel->insert($saveData);
            return redirect()->to('/user/kriteria')->with('success', 'Data sampah berhasil disimpan.');
        } catch (\Exception $e) {
            log_message('error', 'Error saving kriteria data: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data. Silakan coba lagi.');
        }
        
        } catch (\Exception $e) {
            log_message('error', 'Error in save method: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    /**
     * Menghapus data milik user (dengan validasi kepemilikan dan status)
     */
    public function delete($id = null): ResponseInterface
    {
        // Validasi user login dan role
        if (!session()->get('isLoggedIn') || session()->get('user')['role'] !== 'user') {
            return redirect()->to('/auth/login')->with('error', 'Akses ditolak.');
        }

        // Validasi ID
        if (!$id || !is_numeric($id)) {
            return redirect()->to('/user/kriteria')->with('error', 'ID data tidak valid.');
        }

        $userId = session()->get('user')['id'];

        // Cek apakah data ada dan milik user yang login
        $data = $this->kriteriaModel->where(['id' => $id, 'user_id' => $userId])->first();

        if (!$data) {
            return redirect()->to('/user/kriteria')->with('error', 'Data tidak ditemukan atau bukan milik Anda.');
        }

        // Cek status review - hanya data pending yang bisa dihapus
        if ($data['status_review'] !== 'pending') {
            return redirect()->to('/user/kriteria')->with('error', 'Data yang sudah direview tidak dapat dihapus.');
        }

        // Hapus data
        try {
            $this->kriteriaModel->delete($id);
            return redirect()->to('/user/kriteria')->with('success', 'Data sampah berhasil dihapus.');
        } catch (\Exception $e) {
            log_message('error', 'Error deleting kriteria data: ' . $e->getMessage());
            return redirect()->to('/user/kriteria')->with('error', 'Gagal menghapus data. Silakan coba lagi.');
        }
    }

    /**
     * Ambil data kriteria user dengan filter jenis
     */
    private function getUserKriteriaData($userId, $jenisFilter = 'Semua'): array
    {
        return $this->kriteriaModel->getByUserId($userId, $jenisFilter);
    }

    /**
     * Opsi jenis sampah - Ambil dari master_harga_sampah
     */
    private function getJenisSampahOptions(): array
    {
        try {
            $hargaModel = new \App\Models\HargaSampahModel();
            $hargaList = $hargaModel->where('status_aktif', 1)->findAll();
            
            $options = [];
            foreach ($hargaList as $harga) {
                // Gunakan nama_jenis sebagai label, jenis_sampah sebagai value
                $options[$harga['nama_jenis']] = $harga['nama_jenis'] . ' (' . $harga['jenis_sampah'] . ')';
            }
            
            // Fallback jika tidak ada data
            if (empty($options)) {
                return [
                    'Kertas' => 'Kertas',
                    'Plastik' => 'Plastik',
                    'Organik' => 'Organik',
                    'Anorganik' => 'Anorganik',
                    'Limbah Cair' => 'Limbah Cair',
                    'B3' => 'B3 (Bahan Berbahaya dan Beracun)'
                ];
            }
            
            return $options;
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting jenis sampah options: ' . $e->getMessage());
            
            // Fallback ke hardcoded list
            return [
                'Kertas' => 'Kertas',
                'Plastik' => 'Plastik',
                'Organik' => 'Organik',
                'Anorganik' => 'Anorganik',
                'Limbah Cair' => 'Limbah Cair',
                'B3' => 'B3 (Bahan Berbahaya dan Beracun)'
            ];
        }
    }

    /**
     * Opsi gedung
     */
    private function getGedungOptions(): array
    {
        return [
            'Gedung A' => 'Gedung A',
            'Gedung B' => 'Gedung B',
            'Gedung C' => 'Gedung C',
            'Gedung D' => 'Gedung D',
            'Gedung E' => 'Gedung E',
            'Gedung F' => 'Gedung F',
            'Gedung Rektorat' => 'Gedung Rektorat',
            'Gedung Perpustakaan' => 'Gedung Perpustakaan',
            'Gedung Workshop' => 'Gedung Workshop',
            'Gedung Laboratorium' => 'Gedung Laboratorium'
        ];
    }
}