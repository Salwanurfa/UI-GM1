<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\PenilaianModel;
use App\Models\UnitModel;
use App\Models\NotificationModel;

class Pengisian extends BaseController
{
    protected $penilaianModel;
    protected $unitModel;
    protected $notificationModel;

    public function __construct()
    {
        $this->penilaianModel = new PenilaianModel();
        $this->unitModel = new UnitModel();
        $this->notificationModel = new NotificationModel();
    }

    public function index($kategori = 'SI')
    {
        $user = session()->get('user');
        $unitId = $user['unit_id'];

        if (!$unitId) {
            return redirect()->to('/auth/login')
                ->with('error', 'Unit tidak ditemukan. Hubungi administrator.');
        }

        // Validate kategori
        $validKategori = ['SI', 'EC', 'WS', 'WR', 'TR', 'ED'];
        if (!in_array($kategori, $validKategori)) {
            return redirect()->to('/user/pengisian/SI')
                ->with('error', 'Kategori tidak valid.');
        }

        // Initialize indikator if not exists
        $this->penilaianModel->initializeIndikator($unitId, $kategori);

        // Get penilaian data
        $penilaianData = $this->penilaianModel->getPenilaianByUnitKategori($unitId, $kategori);
        
        // Get unit info
        $unit = $this->unitModel->find($unitId);

        // Category names
        $kategoriNames = [
            'SI' => 'Setting & Infrastructure',
            'EC' => 'Energy & Climate Change',
            'WS' => 'Waste',
            'WR' => 'Water',
            'TR' => 'Transportation',
            'ED' => 'Education'
        ];

        $data = [
            'title' => 'Pengisian Data - ' . $kategoriNames[$kategori],
            'user' => $user,
            'unit' => $unit,
            'kategori' => $kategori,
            'kategoriName' => $kategoriNames[$kategori],
            'kategoriList' => $kategoriNames,
            'penilaianData' => $penilaianData
        ];

        return view('user/pengisian', $data);
    }

    public function save()
    {
        $user = session()->get('user');
        $unitId = $user['unit_id'];

        // Validasi yang lebih ketat
        if (!$this->validate([
            'id' => 'required|integer',
            'nilai_input' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'action' => 'required|in_list[draft,kirim]'
        ])) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            $id = $this->request->getPost('id');
            $nilaiInput = $this->request->getPost('nilai_input');
            $action = $this->request->getPost('action');

            // Verify ownership
            $penilaian = $this->penilaianModel->find($id);
            if (!$penilaian || $penilaian['unit_id'] != $unitId) {
                return redirect()->back()
                    ->with('error', 'Data tidak ditemukan atau akses ditolak.');
            }

            // Validasi dan sanitasi nilai_input
            if ($nilaiInput === null || $nilaiInput === '' || !is_numeric($nilaiInput)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Nilai input harus diisi dengan angka yang valid (0-100).');
            }

            // Cast dan validasi range
            $nilaiInput = (float) $nilaiInput;
            if ($nilaiInput < 0 || $nilaiInput > 100) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Nilai input harus antara 0 sampai 100.');
            }

            // Determine status
            $status = ($action === 'kirim') ? 'dikirim' : 'draft';

            // Update data dengan nilai yang sudah divalidasi
            $updateData = [
                'nilai_input' => $nilaiInput, // Pastikan tidak null
                'status' => $status
            ];

            if ($this->penilaianModel->update($id, $updateData)) {
                // Send notification if data is submitted
                if ($action === 'kirim') {
                    $unit = $this->unitModel->find($unitId);
                    $this->notificationModel->createDataSubmissionNotification(
                        $unit['nama_unit'],
                        $penilaian['kategori_uigm'],
                        $penilaian['indikator'],
                        $id
                    );
                }

                $message = ($action === 'kirim') ? 
                    'Data berhasil dikirim ke Admin Pusat' : 
                    'Data berhasil disimpan sebagai draft';
                
                return redirect()->back()
                    ->with('success', $message);
            } else {
                $errors = $this->penilaianModel->errors();
                log_message('error', 'Penilaian update failed: ' . json_encode($errors));
                
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Gagal menyimpan data. Periksa data yang diisi.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Pengisian save error: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    public function submitKategori($kategori)
    {
        $user = session()->get('user');
        $unitId = $user['unit_id'];

        // Validate kategori
        $validKategori = ['SI', 'EC', 'WS', 'WR', 'TR', 'ED'];
        if (!in_array($kategori, $validKategori)) {
            return redirect()->back()
                ->with('error', 'Kategori tidak valid.');
        }

        try {
            // Submit all data in kategori
            if ($this->penilaianModel->submitKategori($unitId, $kategori)) {
                // Send notification for kategori submission
                $unit = $this->unitModel->find($unitId);
                $kategoriNames = [
                    'SI' => 'Setting & Infrastructure',
                    'EC' => 'Energy & Climate Change',
                    'WS' => 'Waste',
                    'WR' => 'Water',
                    'TR' => 'Transportation',
                    'ED' => 'Education'
                ];

                // Get all penilaian in this kategori for notification
                $penilaianList = $this->penilaianModel->getPenilaianByUnitKategori($unitId, $kategori);
                foreach ($penilaianList as $penilaian) {
                    $this->notificationModel->createDataSubmissionNotification(
                        $unit['nama_unit'],
                        $kategori,
                        $penilaian['indikator'],
                        $penilaian['id']
                    );
                }

                return redirect()->back()
                    ->with('success', 'Semua data kategori ' . $kategoriNames[$kategori] . ' berhasil dikirim ke Admin Pusat');
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal mengirim data kategori.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Submit kategori error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }
}