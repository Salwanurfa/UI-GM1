<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MasterLimbahCairModel;

class MasterLimbahCair extends BaseController
{
    protected $masterLimbahCairModel;

    public function __construct()
    {
        $this->masterLimbahCairModel = new MasterLimbahCairModel();
    }

    /**
     * Tampilkan list Master Limbah Cair
     */
    public function index()
    {
        // Validasi session admin
        if (!$this->validateAdminSession()) {
            return redirect()->to('/auth/login')->with('error', 'Akses ditolak');
        }

        // Ambil semua master limbah cair dari database
        $masterLimbahList = $this->masterLimbahCairModel->orderBy('nama_limbah', 'ASC')->findAll();

        // Opsi tingkat bahaya untuk dropdown
        $tingkatBahayaOptions = [
            'Bahaya 1' => 'Bahaya 1',
            'Bahaya 2' => 'Bahaya 2',
            'Bahaya 3' => 'Bahaya 3',

        ];

        // Data untuk view
        $data = [
            'title' => 'Manajemen Master Limbah Cair',
            'master_limbah' => $masterLimbahList,
            'tingkat_bahaya_options' => $tingkatBahayaOptions,
        ];

        return view('admin_pusat/master_limbah_cair/index', $data);
    }

    /**
     * Simpan master limbah cair baru
     */
    public function store()
    {
        if (!$this->validateAdminSession()) {
            return redirect()->to('/auth/login')->with('error', 'Akses ditolak');
        }

        $rules = [
            'nama_limbah' => 'required|max_length[255]',
            'kode_limbah' => 'required|max_length[50]',
            'tingkat_bahaya' => 'required|max_length[100]',
            'karakteristik' => 'required',
            'pengolahan' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nama_limbah' => htmlspecialchars($this->request->getPost('nama_limbah')),
            'kode_limbah' => htmlspecialchars($this->request->getPost('kode_limbah')),
            'tingkat_bahaya' => htmlspecialchars($this->request->getPost('tingkat_bahaya')),
            'karakteristik' => htmlspecialchars($this->request->getPost('karakteristik')),
            'pengolahan' => htmlspecialchars($this->request->getPost('pengolahan')),
        ];

        if ($this->masterLimbahCairModel->insert($data)) {
            log_message('info', 'Admin menambahkan master limbah cair: ' . $data['nama_limbah']);
            return redirect()->to('/admin-pusat/master-limbah-cair')->with('success', 'Data master limbah cair berhasil disimpan');
        }

        return redirect()->back()->with('error', 'Gagal menyimpan data master limbah cair');
    }

    /**
     * Get master limbah cair by ID (untuk edit)
     */
    public function get($id)
    {
        if (!$this->validateAdminSession()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $masterLimbah = $this->masterLimbahCairModel->find($id);
        
        if (!$masterLimbah) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        return $this->response->setJSON(['success' => true, 'data' => $masterLimbah]);
    }

    /**
     * Update master limbah cair
     */
    public function update($id)
    {
        if (!$this->validateAdminSession()) {
            return redirect()->to('/auth/login')->with('error', 'Akses ditolak');
        }

        $masterLimbah = $this->masterLimbahCairModel->find($id);
        if (!$masterLimbah) {
            return redirect()->to('/admin-pusat/master-limbah-cair')->with('error', 'Data tidak ditemukan');
        }

        $rules = [
            'nama_limbah' => 'required|max_length[255]',
            'kode_limbah' => 'required|max_length[50]',
            'tingkat_bahaya' => 'required|max_length[100]',
            'karakteristik' => 'required',
            'pengolahan' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nama_limbah' => htmlspecialchars($this->request->getPost('nama_limbah')),
            'kode_limbah' => htmlspecialchars($this->request->getPost('kode_limbah')),
            'tingkat_bahaya' => htmlspecialchars($this->request->getPost('tingkat_bahaya')),
            'karakteristik' => htmlspecialchars($this->request->getPost('karakteristik')),
            'pengolahan' => htmlspecialchars($this->request->getPost('pengolahan')),
        ];

        if ($this->masterLimbahCairModel->update($id, $data)) {
            log_message('info', 'Admin memperbarui master limbah cair ID: ' . $id);
            return redirect()->to('/admin-pusat/master-limbah-cair')->with('success', 'Data master limbah cair berhasil diperbarui');
        }

        return redirect()->back()->with('error', 'Gagal memperbarui data master limbah cair');
    }

    /**
     * Delete master limbah cair
     */
    public function delete($id)
    {
        if (!$this->validateAdminSession()) {
            return redirect()->to('/auth/login')->with('error', 'Akses ditolak');
        }

        $masterLimbah = $this->masterLimbahCairModel->find($id);
        if (!$masterLimbah) {
            return redirect()->to('/admin-pusat/master-limbah-cair')->with('error', 'Data tidak ditemukan');
        }

        $namaLimbah = $masterLimbah['nama_limbah'];
        
        if ($this->masterLimbahCairModel->delete($id)) {
            log_message('info', 'Admin menghapus master limbah cair: ' . $namaLimbah);
            return redirect()->to('/admin-pusat/master-limbah-cair')->with('success', 'Data master limbah cair berhasil dihapus');
        }

        return redirect()->back()->with('error', 'Gagal menghapus data master limbah cair');
    }

    /**
     * Validasi session admin
     */
    private function validateAdminSession(): bool
    {
        $session = session();
        $user = $session->get('user');
        
        return $session->get('isLoggedIn') && 
               isset($user['id'], $user['role']) &&
               in_array($user['role'], ['admin_pusat', 'super_admin']);
    }
}
