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

        // Get user's unit and gedung information from session
        $session = session();
        $user = $session->get('user');
        $userUnit = $user['nama_unit'] ?? null;
        $userGedung = $user['gedung'] ?? null;

        // If gedung not in session, get it from database using unit_id
        if (!$userGedung && $user['unit_id']) {
            $unitModel = new \App\Models\UnitModel();
            $userGedung = $unitModel->getGedungForUser($user['unit_id']);
        }

        // Use gedung as lokasi, fallback to unit name if gedung not available
        $userLokasi = $userGedung ?: $userUnit ?: 'Gedung A – Gedung Kuliah';

        $viewData = [
            'title'       => 'Limbah B3 - User',
            'user'        => $data['user'],
            'unit'        => $data['unit'],
            'limbah_list' => $data['limbah_list'],
            'master_list' => $data['master_list'],
            'stats'       => $data['stats'],
            'user_gedung' => $userGedung, // Keep for backward compatibility
            'user_unit'   => $userUnit,   // Unit name from session
            'user_lokasi' => $userLokasi, // Final lokasi to use (gedung preferred, unit as fallback)
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

    /**
     * Export data Limbah B3 ke PDF
     */
    public function exportPdf()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            $result = $this->service->exportPdf();
            
            if ($result['success']) {
                return $this->response->download($result['file_path'], null)->setFileName($result['filename']);
            }

            return redirect()->back()->with('error', $result['message']);

        } catch (\Exception $e) {
            log_message('error', 'User Limbah B3 Export PDF Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat export PDF');
        }
    }

    /**
     * Export data Limbah B3 ke Excel
     */
    public function exportExcel()
    {
        try {
            if (!$this->validateSession()) {
                return redirect()->to('/auth/login');
            }

            helper('excel');
            $this->service->exportExcel();

        } catch (\Exception $e) {
            log_message('error', 'User Limbah B3 Export Excel Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat export Excel');
        }
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

