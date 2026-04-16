<?php

namespace App\Controllers\Security;

use App\Controllers\BaseController;
use App\Models\TransportStatsModel;

class Transportation extends BaseController
{
    protected $transportStatsModel;

    public function __construct()
    {
        // Check if user is logged in and has security role
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        
        $userRole = session()->get('user')['role'] ?? null;
        if ($userRole !== 'security') {
            return redirect()->to('/auth/login')->with('error', 'Akses ditolak');
        }

        $this->transportStatsModel = new TransportStatsModel();
    }

    /**
     * Transportation aggregate input form
     */
    public function index()
    {
        $data = [
            'title' => 'Input Statistik Transportasi',
            'user' => session()->get('user'),
            'edit_data' => null,
            'available_periods' => $this->transportStatsModel->getAvailablePeriods()
        ];

        // Check if this is an edit request
        $editId = $this->request->getGet('edit');
        if ($editId) {
            $userId = session()->get('user')['id'];
            
            $editData = $this->transportStatsModel
                ->where('id', $editId)
                ->where('input_by', $userId)
                ->first();
                
            if ($editData) {
                $data['edit_data'] = $editData;
                $data['title'] = 'Edit Statistik Transportasi';
            }
        }

        return view('security/transportation', $data);
    }

    /**
     * Save transportation statistics
     */
    public function save()
    {
        $rules = [
            'periode' => 'required|max_length[100]',
            'kategori_kendaraan' => 'required|in_list[Roda Dua,Roda Empat,Sepeda,Kendaraan Umum]',
            'jenis_bahan_bakar' => 'required|in_list[Bensin,Diesel,Listrik,Non-BBM]',
            'jumlah_total' => 'required|integer|greater_than[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $userId = session()->get('user')['id'];
        $editId = $this->request->getPost('edit_id');
        
        $periode = $this->request->getPost('periode');
        $kategori = $this->request->getPost('kategori_kendaraan');
        $bahanBakar = $this->request->getPost('jenis_bahan_bakar');
        $jumlahTotal = $this->request->getPost('jumlah_total');
        $isShuttle = $this->request->getPost('is_shuttle') ? 1 : 0;

        // Auto-determine ZEV status based on fuel type
        $isZev = in_array($bahanBakar, ['Listrik', 'Non-BBM']) ? 1 : 0;

        // Check if entry already exists (for new entries only)
        if (!$editId) {
            $existingEntry = $this->transportStatsModel->entryExists($periode, $kategori, $bahanBakar, $userId);
            if ($existingEntry) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Data untuk periode, kategori kendaraan, dan jenis bahan bakar ini sudah ada. Silakan edit data yang sudah ada atau pilih kombinasi yang berbeda.');
            }
        }
        
        $data = [
            'periode' => $periode,
            'kategori_kendaraan' => $kategori,
            'jenis_bahan_bakar' => $bahanBakar,
            'jumlah_total' => $jumlahTotal,
            'input_by' => $userId,
            'is_zev' => $isZev,
            'is_shuttle' => $isShuttle
        ];

        // Set timezone to Asia/Jakarta for accurate timestamp
        date_default_timezone_set('Asia/Jakarta');
        
        // Add current timestamp in WIB
        if (!$editId) {
            $data['created_at'] = now_wib();
        }
        $data['updated_at'] = now_wib();

        try {
            if ($editId) {
                // Update existing record
                $this->transportStatsModel->update($editId, $data);
                $message = 'Statistik transportasi berhasil diperbarui';
            } else {
                // Insert new record
                $this->transportStatsModel->insert($data);
                $message = 'Statistik transportasi berhasil disimpan';
            }
            
            return redirect()->to('/security/dashboard')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            log_message('error', 'Transport stats save error: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data. Silakan coba lagi.');
        }
    }

    /**
     * Delete transport stats entry
     */
    public function delete($id)
    {
        $userId = session()->get('user')['id'];
        
        try {
            // Verify ownership before deletion
            $entry = $this->transportStatsModel
                ->where('id', $id)
                ->where('input_by', $userId)
                ->first();
                
            if (!$entry) {
                return redirect()->to('/security/dashboard')
                    ->with('error', 'Data tidak ditemukan atau Anda tidak memiliki akses untuk menghapus data ini.');
            }
            
            $this->transportStatsModel->delete($id);
            
            return redirect()->to('/security/dashboard')
                ->with('success', 'Data statistik transportasi berhasil dihapus');
                
        } catch (\Exception $e) {
            log_message('error', 'Transport stats delete error: ' . $e->getMessage());
            
            return redirect()->to('/security/dashboard')
                ->with('error', 'Gagal menghapus data. Silakan coba lagi.');
        }
    }
}