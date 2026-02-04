<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UnitModel;

class UnitManagement extends BaseController
{
    protected $unitModel;

    public function __construct()
    {
        $this->unitModel = new UnitModel();
    }

    public function index()
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !isset($user['role']) || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return redirect()->to('/auth/login')->with('error', 'Silakan login terlebih dahulu');
        }

        try {
            // Get filter parameters
            $tipeFilter = $this->request->getGet('tipe');
            $statusFilter = $this->request->getGet('status');

            // Build query
            $builder = $this->unitModel;

            // Apply filters
            if (!empty($tipeFilter)) {
                $builder->where('tipe_unit', $tipeFilter);
            }

            if ($statusFilter !== '' && $statusFilter !== null) {
                $builder->where('status_aktif', $statusFilter);
            }

            $units = $builder->orderBy('nama_unit', 'ASC')->findAll();

            // Calculate statistics
            $stats = [
                'total' => count($units),
                'active' => count(array_filter($units, fn($u) => $u['status_aktif'] == 1)),
                'inactive' => count(array_filter($units, fn($u) => $u['status_aktif'] == 0)),
                'fakultas' => count(array_filter($units, fn($u) => $u['tipe_unit'] == 'fakultas')),
                'jurusan' => count(array_filter($units, fn($u) => $u['tipe_unit'] == 'jurusan')),
                'unit_kerja' => count(array_filter($units, fn($u) => $u['tipe_unit'] == 'unit_kerja')),
                'lembaga' => count(array_filter($units, fn($u) => $u['tipe_unit'] == 'lembaga'))
            ];

            $data = [
                'title' => 'Manajemen Unit',
                'units' => $units,
                'stats' => $stats,
                'allTipes' => [
                    'fakultas' => 'Fakultas',
                    'jurusan' => 'Jurusan',
                    'unit_kerja' => 'Unit Kerja',
                    'lembaga' => 'Lembaga'
                ],
                'allStatus' => [
                    '1' => 'Aktif',
                    '0' => 'Tidak Aktif'
                ],
                'filters' => [
                    'tipe' => $tipeFilter ?? '',
                    'status' => $statusFilter ?? ''
                ]
            ];

            return view('admin_pusat/unit_management', $data);

        } catch (\Exception $e) {
            log_message('error', 'Unit Management Error: ' . $e->getMessage());
            
            return view('admin_pusat/unit_management', [
                'title' => 'Manajemen Unit',
                'units' => [],
                'stats' => [
                    'total' => 0,
                    'active' => 0,
                    'inactive' => 0,
                    'fakultas' => 0,
                    'jurusan' => 0,
                    'unit_kerja' => 0,
                    'lembaga' => 0
                ],
                'allTipes' => [
                    'fakultas' => 'Fakultas',
                    'jurusan' => 'Jurusan',
                    'unit_kerja' => 'Unit Kerja',
                    'lembaga' => 'Lembaga'
                ],
                'allStatus' => [
                    '1' => 'Aktif',
                    '0' => 'Tidak Aktif'
                ],
                'filters' => [
                    'tipe' => '',
                    'status' => ''
                ],
                'error' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage()
            ]);
        }
    }

    public function getUnit($id)
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !isset($user['role']) || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $unit = $this->unitModel->find($id);
            
            if (!$unit) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unit tidak ditemukan'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $unit
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function create()
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !isset($user['role']) || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $nama_unit = $this->request->getPost('nama_unit');
            $kode_unit = $this->request->getPost('kode_unit');
            $tipe_unit = $this->request->getPost('tipe_unit') ?? null;
            $deskripsi = $this->request->getPost('deskripsi');
            
            // Validation
            if (empty($nama_unit) || empty($kode_unit)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Nama gedung dan kode gedung wajib diisi'
                ]);
            }
            
            // Check if kode_unit already exists
            $existingUnit = $this->unitModel->where('kode_unit', $kode_unit)->first();
            if ($existingUnit) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kode gedung sudah digunakan'
                ]);
            }
            
            $data = [
                'nama_unit' => $nama_unit,
                'kode_unit' => $kode_unit,
                'status_aktif' => 1
            ];
            
            // Only add tipe_unit if column exists and value is provided
            if ($tipe_unit) {
                $data['tipe_unit'] = $tipe_unit;
            }
            
            // Only add deskripsi if provided
            if ($deskripsi) {
                $data['deskripsi'] = $deskripsi;
            }

            $insertId = $this->unitModel->insert($data);
            
            if ($insertId) {
                log_message('info', 'Unit created successfully: ' . $nama_unit . ' (ID: ' . $insertId . ')');
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Gedung berhasil ditambahkan'
                ]);
            }
            
            // If insert failed, get validation errors
            $errors = $this->unitModel->errors();
            log_message('error', 'Unit insert failed: ' . json_encode($errors));

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menambahkan gedung: ' . implode(', ', $errors)
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Unit create exception: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function update($id)
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !isset($user['role']) || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $data = [
                'nama_unit' => $this->request->getPost('nama_unit'),
                'kode_unit' => $this->request->getPost('kode_unit'),
                'tipe_unit' => $this->request->getPost('tipe_unit'),
                'deskripsi' => $this->request->getPost('deskripsi')
            ];

            if ($this->unitModel->update($id, $data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Unit berhasil diupdate'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengupdate unit'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function toggleStatus($id)
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !isset($user['role']) || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $unit = $this->unitModel->find($id);
            
            if (!$unit) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unit tidak ditemukan'
                ]);
            }

            $newStatus = !$unit['status_aktif'];
            
            if ($this->unitModel->update($id, ['status_aktif' => $newStatus])) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Status unit berhasil diubah'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengubah status unit'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function delete($id)
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$session->get('isLoggedIn') || !isset($user['role']) || !in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            // Check if unit is being used by any user
            $userModel = new \App\Models\UserModel();
            $usersInUnit = $userModel->where('unit_id', $id)->countAllResults();
            
            if ($usersInUnit > 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "Unit tidak dapat dihapus karena masih digunakan oleh $usersInUnit user. Nonaktifkan unit terlebih dahulu."
                ]);
            }

            if ($this->unitModel->delete($id)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Unit berhasil dihapus'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus unit'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}
