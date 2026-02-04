<?php

namespace App\Controllers\Debug;

use App\Controllers\BaseController;
use App\Models\PenilaianModel;
use App\Models\WasteModel;
use App\Models\UnitModel;
use App\Models\UserModel;

class DataController extends BaseController
{
    public function checkData()
    {
        // Only allow in development
        if (ENVIRONMENT !== 'development') {
            return $this->response->setStatusCode(404);
        }

        $penilaianModel = new PenilaianModel();
        $wasteModel = new WasteModel();
        $unitModel = new UnitModel();
        $userModel = new UserModel();

        // Check penilaian data with status 'dikirim'
        $penilaianDikirim = $penilaianModel
            ->select('penilaian_unit.*, unit.nama_unit')
            ->join('unit', 'unit.id = penilaian_unit.unit_id')
            ->where('penilaian_unit.status', 'dikirim')
            ->findAll();

        // Check waste data with status 'dikirim'
        $wasteDikirim = $wasteModel
            ->select('waste_management.*, unit.nama_unit')
            ->join('unit', 'unit.id = waste_management.unit_id')
            ->where('waste_management.status', 'dikirim')
            ->findAll();

        // Check all units
        $allUnits = $unitModel->findAll();

        // Check all users
        $allUsers = $userModel->findAll();

        // Check all penilaian statuses
        $statusCount = $penilaianModel
            ->select('status, COUNT(*) as count')
            ->groupBy('status')
            ->findAll();

        $data = [
            'title' => 'Debug Data Check',
            'penilaian_dikirim' => $penilaianDikirim,
            'waste_dikirim' => $wasteDikirim,
            'all_units' => $allUnits,
            'all_users' => $allUsers,
            'status_count' => $statusCount
        ];

        return view('debug/data_check', $data);
    }

    public function createSampleData()
    {
        // Only allow in development
        if (ENVIRONMENT !== 'development') {
            return $this->response->setStatusCode(404);
        }

        $penilaianModel = new PenilaianModel();
        $unitModel = new UnitModel();

        // Get first unit
        $unit = $unitModel->first();
        if (!$unit) {
            return 'No unit found. Please create a unit first.';
        }

        // Create sample penilaian data
        $sampleData = [
            [
                'unit_id' => $unit['id'],
                'kategori_uigm' => 'SI',
                'indikator' => 'Test Indikator 1',
                'nilai_input' => 85.5,
                'status' => 'dikirim'
            ],
            [
                'unit_id' => $unit['id'],
                'kategori_uigm' => 'EC',
                'indikator' => 'Test Indikator 2',
                'nilai_input' => 75.0,
                'status' => 'dikirim'
            ]
        ];

        foreach ($sampleData as $data) {
            $penilaianModel->insert($data);
        }

        return 'Sample data created successfully!';
    }
}