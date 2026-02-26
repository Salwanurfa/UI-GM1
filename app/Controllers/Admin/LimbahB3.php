<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\LimbahB3Service;

class LimbahB3 extends BaseController
{
    protected LimbahB3Service $service;

    public function __construct()
    {
        $this->service = new LimbahB3Service();
    }

    public function index()
    {
        if (!$this->validateSession()) {
            return redirect()->to('/auth/login');
        }

        $limbahModel = new \App\Models\LimbahB3Model();
        $limbahList  = $limbahModel->getAllWithMaster();

        $viewData = [
            'title'       => 'Limbah B3 Management',
            'limbah_list' => $limbahList,
        ];

        return view('admin_pusat/limbah_b3', $viewData);
    }

    /**
     * Show form to create new Limbah B3 record.
     */
    public function create()
    {
        if (!$this->validateSession()) {
            return redirect()->to('/auth/login');
        }

        $masterModel = new \App\Models\MasterLimbahB3Model();
        $masterList  = $masterModel->findAll();

        $viewData = [
            'title'        => 'Tambah Data Limbah B3',
            'master_list'  => $masterList,
            'satuan_list'  => ['kg', 'l', 'unit', 'drum', 'container', 'ton'],
        ];

        return view('admin_pusat/limbah_b3_form', $viewData);
    }

    /**
     * Store new Limbah B3 record.
     */
    public function store()
    {
        if (!$this->validateSession()) {
            return redirect()->to('/auth/login')->with('error', 'Session tidak valid');
        }

        // Ambil data dari POST
        $post = $this->request->getPost();

        // Validasi
        $errors = [];
        if (empty($post['master_limbah_id'])) {
            $errors[] = 'Jenis Limbah B3 harus dipilih';
        }
        if (empty($post['timbulan']) || !is_numeric($post['timbulan'])) {
            $errors[] = 'Timbulan/berat harus berupa angka';
        }
        if (empty($post['satuan'])) {
            $errors[] = 'Satuan harus dipilih';
        }
        if (empty($post['tanggal_input'])) {
            $errors[] = 'Tanggal input harus diisi';
        }

        if (!empty($errors)) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        // Simpan ke database
        $limbahModel = new \App\Models\LimbahB3Model();
        
        $data = [
            'master_limbah_id' => (int) $post['master_limbah_id'],
            'lokasi'           => $post['lokasi'] ?? null,
            'timbulan'         => (float) $post['timbulan'],
            'satuan'           => $post['satuan'],
            'bentuk_fisik'     => $post['bentuk_fisik'] ?? null,
            'kemasan'          => $post['kemasan'] ?? null,
            'tanggal_input'    => $post['tanggal_input'],
        ];

        if ($limbahModel->insert($data)) {
            return redirect()->to('/admin-pusat/limbah-b3')
                ->with('success', 'Data Limbah B3 berhasil ditambahkan');
        } else {
            $errors = $limbahModel->errors();
            return redirect()->back()
                ->withInput()
                ->with('error', !empty($errors) ? implode(', ', $errors) : 'Gagal menyimpan data');
        }
    }

    private function validateSession(): bool
    {
        $session = session();
        $user    = $session->get('user');

        return $session->get('isLoggedIn')
            && isset($user['id'], $user['role'])
            && in_array($user['role'], ['admin_pusat', 'super_admin'], true);
    }
}