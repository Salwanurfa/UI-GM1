<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MasterLimbahB3Model;
use App\Models\LogModel;

class ManajemenLimbahB3 extends BaseController
{
    protected $limbahb3Model;

    public function __construct()
    {
        $this->limbahb3Model = new MasterLimbahB3Model();
    }

    /**
     * Tampilkan list Limbah B3
     */
    public function index()
    {
        // Cek session user
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        // Ambil semua limbah B3 dari database
        $limbahList = $this->limbahb3Model->findAll();

        // Opsi kategori bahaya
        $kategoriOptions = [
            'Bahaya 1' => 'Kategori Bahaya 1',
            'Bahaya 2' => 'Kategori Bahaya 2',
            'Bahaya 3' => 'Kategori Bahaya 3',
        ];

        // Data untuk view
        $data = [
            'title'              => 'Manajemen Master Limbah B3',
            'limbah'             => $limbahList,
            'kategori_options'   => $kategoriOptions,
        ];

        // Return view dengan path yang sesuai folder struktur
        return view('admin_pusat/manajemen_limbah_b3/index', $data);
    }

    /**
     * Tampilkan form create limbah (optional)
     */
    public function create()
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !isset($user['role']) || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $data = [
            'kategori_options' => [
                'Bahaya 1' => 'Kategori Bahaya 1',
                'Bahaya 2' => 'Kategori Bahaya 2',
                'Bahaya 3' => 'Kategori Bahaya 3',
            ],
        ];

        return $this->response->setJSON($data);
    }

    /**
     * Simpan limbah baru
     */
    public function store()
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !isset($user['role']) || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return redirect()->to('/auth/login')->with('error', 'Akses ditolak');
        }

        $rules = [
            'nama_limbah'     => 'required|max_length[255]',
            'kode_limbah'     => 'required|max_length[50]',
            'kategori_bahaya' => 'required',
            'karakteristik'   => 'max_length[1000]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nama_limbah'     => htmlspecialchars($this->request->getPost('nama_limbah')),
            'kode_limbah'     => htmlspecialchars($this->request->getPost('kode_limbah')),
            'kategori_bahaya' => htmlspecialchars($this->request->getPost('kategori_bahaya')),
            'karakteristik'   => htmlspecialchars($this->request->getPost('karakteristik')),
        ];

        if ($this->limbahb3Model->insert($data)) {
    $logModel = new LogModel();
    $logModel->insert([
        'user_id'    => session()->get('user_id'), // Pastikan session user_id ada
        'aksi'       => 'Tambah Limbah B3',
        'keterangan' => 'Menambahkan limbah baru: ' . $data['nama_limbah'],
        'created_at' => date('Y-m-d H:i:s')
    ]);

    return redirect()->to('/admin-pusat/manajemen-limbah-b3')->with('success', 'Data berhasil disimpan');
}

        return redirect()->back()->with('error', 'Gagal menyimpan limbah B3');
    }

    /**
     * Get limbah by ID (untuk edit)
     */
    public function get($id)
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !isset($user['role']) || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $limbah = $this->limbahb3Model->find($id);
        
        if (!$limbah) {
            return $this->response->setJSON(['success' => false, 'message' => 'Limbah tidak ditemukan']);
        }

        return $this->response->setJSON(['success' => true, 'data' => $limbah]);
    }

    /**
     * Update limbah
     */
    public function update($id)
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !isset($user['role']) || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return redirect()->to('/auth/login')->with('error', 'Akses ditolak');
        }

        $limbah = $this->limbahb3Model->find($id);
        if (!$limbah) {
            return redirect()->to('/admin-pusat/manajemen-limbah-b3')->with('error', 'Limbah tidak ditemukan');
        }

        $rules = [
            'nama_limbah'     => 'required|max_length[255]',
            'kode_limbah'     => 'required|max_length[50]',
            'kategori_bahaya' => 'required',
            'karakteristik'   => 'max_length[1000]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nama_limbah'     => htmlspecialchars($this->request->getPost('nama_limbah')),
            'kode_limbah'     => htmlspecialchars($this->request->getPost('kode_limbah')),
            'kategori_bahaya' => htmlspecialchars($this->request->getPost('kategori_bahaya')),
            'karakteristik'   => htmlspecialchars($this->request->getPost('karakteristik')),
        ];

        if ($this->limbahb3Model->update($id, $data)) {
    $logModel = new LogModel();
    $logModel->insert([
        'user_id'    => session()->get('user_id'),
        'aksi'       => 'Update Limbah B3',
        'keterangan' => 'Memperbarui data limbah ID: ' . $id . ' menjadi ' . $data['nama_limbah'],
        'created_at' => date('Y-m-d H:i:s')
    ]);

    return redirect()->to('/admin-pusat/manajemen-limbah-b3')->with('success', 'Data berhasil diperbarui');
}

        return redirect()->back()->with('error', 'Gagal memperbarui limbah B3');
    }

    /**
     * Delete limbah
     */
    public function delete($id)
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !isset($user['role']) || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return redirect()->to('/auth/login')->with('error', 'Akses ditolak');
        }

        $limbah = $this->limbahb3Model->find($id);
        if (!$limbah) {
            return redirect()->to('/admin-pusat/manajemen-limbah-b3')->with('error', 'Limbah tidak ditemukan');
        }

        $namaLimbah = $limbah['nama_limbah']; // Ambil nama sebelum dihapus
if ($this->limbahb3Model->delete($id)) {
    $logModel = new LogModel();
    $logModel->insert([
        'user_id'    => session()->get('user_id'),
        'aksi'       => 'Hapus Limbah B3',
        'keterangan' => 'Menghapus limbah: ' . $namaLimbah,
        'created_at' => date('Y-m-d H:i:s')
    ]);

    return redirect()->to('/admin-pusat/manajemen-limbah-b3')->with('success', 'Data berhasil dihapus');
}

        return redirect()->back()->with('error', 'Gagal menghapus limbah B3');
    }

    /**
     * Tampilkan riwayat perubahan limbah B3
     */
    public function logs()
{
    $logModel = new \App\Models\LogModel();
    
    $logs = $logModel->select('logs.*, users.username as nama_admin') // Ganti 'nama' menjadi 'username'
                     ->join('users', 'users.id = logs.user_id', 'left')
                     ->orderBy('logs.created_at', 'DESC')
                     ->findAll();

    $data = [
        'title' => 'Riwayat Perubahan Limbah B3',
        'logs'  => $logs,
    ];

    return view('admin_pusat/manajemen_limbah_b3/logs', $data);
}

    /**
     * Export PDF Laporan Data Limbah B3
     */
    public function exportPdf()
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !isset($user['role']) || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return redirect()->to('/auth/login')->with('error', 'Akses ditolak');
        }

        try {
            // Ambil data limbah B3 dengan join lengkap menggunakan method dari MasterLimbahB3Model
            $dataLimbah = $this->limbahb3Model->getDataForExport();

            // Hitung total timbulan
            $totalTimbulan = 0;
            foreach ($dataLimbah as $item) {
                $totalTimbulan += $item['timbulan'] ?? 0;
            }

            // Data untuk view PDF
            $data = [
                'title' => 'Laporan Data Limbah B3',
                'dataLimbah' => $dataLimbah,
                'totalTimbulan' => $totalTimbulan,
                'generated_at' => date('d/m/Y H:i:s'),
                'generated_by' => $user['nama_lengkap'] ?? $user['username']
            ];

            // Generate HTML untuk PDF
            $html = view('admin_pusat/manajemen_limbah_b3/export_pdf', $data);

            // Konfigurasi Dompdf
            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'Arial');
            
            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            
            // Set paper ke A4 landscape
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            // Output PDF
            $filename = 'Laporan_Limbah_B3_' . date('Y-m-d_H-i-s') . '.pdf';
            $dompdf->stream($filename, ['Attachment' => true]);

        } catch (\Exception $e) {
            log_message('error', 'Export PDF Limbah B3 Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat export PDF: ' . $e->getMessage());
        }
    }
}
