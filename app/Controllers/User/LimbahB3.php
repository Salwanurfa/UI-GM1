<?php

namespace App\Controllers\User;

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

        $data = $this->service->getUserIndexData();

        $viewData = [
            'title'       => 'Limbah B3 - User',
            'user'        => $data['user'],
            'unit'        => $data['unit'],
            'limbah_list' => $data['limbah_list'],
            'master_list' => $data['master_list'],
            'stats'       => $data['stats'],
        ];

        return view('user/limbah_b3', $viewData);
    }

    public function save()
    {
        if (!$this->validateSession()) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Session invalid']);
        }

        $post = $this->request->getPost();

        $result = $this->service->saveUser([
            'master_b3_id'  => $post['master_b3_id'] ?? null,
            'lokasi'        => $post['lokasi'] ?? null,
            'timbulan'      => $post['timbulan'] ?? null,
            'satuan'        => $post['satuan'] ?? null,
            'bentuk_fisik'  => $post['bentuk_fisik'] ?? null,
            'kemasan'       => $post['kemasan'] ?? null,
            'action'        => $post['action'] ?? 'simpan_draf',
            'tanggal_input' => date('Y-m-d H:i:s'),
        ]);

        return $this->response
            ->setContentType('application/json')
            ->setJSON($result);
    }

    public function get($id)
    {
        if (!$this->validateSession()) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $row = $this->service->getUserDetail((int) $id);
        if (!$row) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        return $this->response
            ->setContentType('application/json')
            ->setJSON(['success' => true, 'data' => $row]);
    }

    public function edit($id)
    {
        if (!$this->validateSession()) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Session invalid']);
        }

        $post = $this->request->getPost();

        $result = $this->service->updateUser((int) $id, [
            'master_b3_id' => $post['master_b3_id'] ?? null,
            'lokasi'       => $post['lokasi'] ?? null,
            'timbulan'     => $post['timbulan'] ?? null,
            'satuan'       => $post['satuan'] ?? null,
            'bentuk_fisik' => $post['bentuk_fisik'] ?? null,
            'kemasan'      => $post['kemasan'] ?? null,
            'action'       => $post['action'] ?? 'simpan_draf',
        ]);

        return $this->response
            ->setContentType('application/json')
            ->setJSON($result);
    }

    public function delete($id)
    {
        if (!$this->validateSession()) {
            return $this->response
                ->setStatusCode(401)
                ->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Session invalid']);
        }

        $result = $this->service->deleteUser((int) $id);

        return $this->response
            ->setContentType('application/json')
            ->setJSON($result);
    }

    /**
     * Endpoint AJAX untuk ambil detail master Limbah B3.
     */
    public function master($id)
    {
        if (!$this->validateSession()) {
            return $this->response
                ->setStatusCode(401)
                ->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $service = $this->service;
        $row     = $service->getMasterById((int) $id);
        if (!$row) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Master Limbah B3 tidak ditemukan']);
        }

        return $this->response
            ->setContentType('application/json')
            ->setJSON(['success' => true, 'data' => $row]);
    }

    private function validateSession(): bool
    {
        $session = session();
        $user    = $session->get('user');

        return $session->get('isLoggedIn')
            && isset($user['id'], $user['role'], $user['unit_id'])
            && $user['role'] === 'user';
    }
}

