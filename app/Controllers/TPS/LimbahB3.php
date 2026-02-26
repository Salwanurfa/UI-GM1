<?php

namespace App\Controllers\TPS;

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

        $data = $this->service->getTpsIndexData();

        $viewData = [
            'title'       => 'Limbah B3 - TPS',
            'user'        => $data['user'],
            'tps_info'    => $data['tps_info'],
            'limbah_list' => $data['limbah_list'],
        ];

        return view('pengelola_tps/limbah_b3', $viewData);
    }

    public function approve($id)
    {
        if (!$this->validateSession()) {
            return $this->response
                ->setStatusCode(401)
                ->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Session invalid']);
        }

        $post   = $this->request->getPost();
        $result = $this->service->reviewByTps((int) $id, 'approve', $post['keterangan'] ?? null);

        return $this->response
            ->setContentType('application/json')
            ->setJSON($result);
    }

    public function reject($id)
    {
        if (!$this->validateSession()) {
            return $this->response
                ->setStatusCode(401)
                ->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Session invalid']);
        }

        $post   = $this->request->getPost();
        $result = $this->service->reviewByTps((int) $id, 'reject', $post['keterangan'] ?? null);

        return $this->response
            ->setContentType('application/json')
            ->setJSON($result);
    }

    private function validateSession(): bool
    {
        $session = session();
        $user    = $session->get('user');

        return $session->get('isLoggedIn')
            && isset($user['id'], $user['role'], $user['unit_id'])
            && $user['role'] === 'pengelola_tps';
    }
}

