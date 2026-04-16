<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InfrastructureDataModel;
use App\Models\PopulationDataModel;
use App\Models\TransportStatsModel;

class Infrastructure extends BaseController
{
    protected $infrastructureModel;
    protected $populationModel;
    protected $transportStatsModel;

    public function __construct()
    {
        $this->infrastructureModel = new InfrastructureDataModel();
        $this->populationModel = new PopulationDataModel();
        $this->transportStatsModel = new TransportStatsModel();
    }

    /**
     * Infrastructure & Population management dashboard
     */
    public function index()
    {
        $data = [
            'title' => 'Manajemen Infrastruktur & Populasi',
            'user' => session()->get('user'),
            'infrastructure_data' => $this->infrastructureModel->getAllWithAdmin(),
            'population_data' => $this->populationModel->getAllWithAdmin(),
            'infrastructure_stats' => $this->infrastructureModel->getParkingStats(),
            'population_stats' => $this->populationModel->getPopulationStats(),
            'uigm_ratios' => $this->calculateUIGMRatios()
        ];

        return view('admin_pusat/infrastructure/index', $data);
    }

    /**
     * Infrastructure data form
     */
    public function infrastructureForm($id = null)
    {
        $data = [
            'title' => $id ? 'Edit Data Infrastruktur' : 'Tambah Data Infrastruktur',
            'user' => session()->get('user'),
            'edit_data' => $id ? $this->infrastructureModel->find($id) : null
        ];

        return view('admin_pusat/infrastructure/infrastructure_form', $data);
    }

    /**
     * Population data form
     */
    public function populationForm($id = null)
    {
        $data = [
            'title' => $id ? 'Edit Data Populasi' : 'Tambah Data Populasi',
            'user' => session()->get('user'),
            'edit_data' => $id ? $this->populationModel->find($id) : null
        ];

        return view('admin_pusat/infrastructure/population_form', $data);
    }

    /**
     * Save infrastructure data
     */
    public function saveInfrastructure()
    {
        $rules = [
            'tahun_akademik' => 'required|max_length[20]',
            'luas_total_kampus' => 'required|decimal|greater_than[0]',
            'luas_area_parkir_total' => 'required|decimal|greater_than_equal_to[0]',
            'luas_parkir_terbuka' => 'decimal|greater_than_equal_to[0]',
            'luas_parkir_berkanopi' => 'decimal|greater_than_equal_to[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $userId = session()->get('user')['id'];
        $editId = $this->request->getPost('edit_id');
        
        $data = [
            'tahun_akademik' => $this->request->getPost('tahun_akademik'),
            'luas_total_kampus' => $this->request->getPost('luas_total_kampus'),
            'luas_area_parkir_total' => $this->request->getPost('luas_area_parkir_total'),
            'luas_parkir_terbuka' => $this->request->getPost('luas_parkir_terbuka') ?: 0,
            'luas_parkir_berkanopi' => $this->request->getPost('luas_parkir_berkanopi') ?: 0,
            'keterangan' => $this->request->getPost('keterangan'),
            'input_by' => $userId
        ];

        try {
            if ($editId) {
                $this->infrastructureModel->update($editId, $data);
                $message = 'Data infrastruktur berhasil diperbarui';
            } else {
                // Deactivate previous data for same academic year
                $this->infrastructureModel->where('tahun_akademik', $data['tahun_akademik'])
                    ->set(['status_aktif' => 0])
                    ->update();
                
                $this->infrastructureModel->insert($data);
                $message = 'Data infrastruktur berhasil disimpan';
            }
            
            return redirect()->to('/admin-pusat/infrastructure')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            log_message('error', 'Infrastructure save error: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data. Silakan coba lagi.');
        }
    }

    /**
     * Save population data
     */
    public function savePopulation()
    {
        $rules = [
            'tahun_akademik' => 'required|max_length[20]',
            'jumlah_dosen' => 'required|integer|greater_than_equal_to[0]',
            'jumlah_mahasiswa' => 'required|integer|greater_than_equal_to[0]',
            'jumlah_tenaga_kependidikan' => 'required|integer|greater_than_equal_to[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $userId = session()->get('user')['id'];
        $editId = $this->request->getPost('edit_id');
        
        $data = [
            'tahun_akademik' => $this->request->getPost('tahun_akademik'),
            'jumlah_dosen' => $this->request->getPost('jumlah_dosen'),
            'jumlah_mahasiswa' => $this->request->getPost('jumlah_mahasiswa'),
            'jumlah_tenaga_kependidikan' => $this->request->getPost('jumlah_tenaga_kependidikan'),
            'keterangan' => $this->request->getPost('keterangan'),
            'input_by' => $userId
        ];

        try {
            if ($editId) {
                $this->populationModel->update($editId, $data);
                $message = 'Data populasi berhasil diperbarui';
            } else {
                // Deactivate previous data for same academic year
                $this->populationModel->where('tahun_akademik', $data['tahun_akademik'])
                    ->set(['status_aktif' => 0])
                    ->update();
                
                $this->populationModel->insert($data);
                $message = 'Data populasi berhasil disimpan';
            }
            
            return redirect()->to('/admin-pusat/infrastructure')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            log_message('error', 'Population save error: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data. Silakan coba lagi.');
        }
    }

    /**
     * Delete infrastructure data
     */
    public function deleteInfrastructure($id)
    {
        try {
            $this->infrastructureModel->update($id, ['status_aktif' => 0]);
            
            return redirect()->to('/admin-pusat/infrastructure')
                ->with('success', 'Data infrastruktur berhasil dihapus');
                
        } catch (\Exception $e) {
            log_message('error', 'Infrastructure delete error: ' . $e->getMessage());
            
            return redirect()->to('/admin-pusat/infrastructure')
                ->with('error', 'Gagal menghapus data. Silakan coba lagi.');
        }
    }

    /**
     * Delete population data
     */
    public function deletePopulation($id)
    {
        try {
            $this->populationModel->update($id, ['status_aktif' => 0]);
            
            return redirect()->to('/admin-pusat/infrastructure')
                ->with('success', 'Data populasi berhasil dihapus');
                
        } catch (\Exception $e) {
            log_message('error', 'Population delete error: ' . $e->getMessage());
            
            return redirect()->to('/admin-pusat/infrastructure')
                ->with('error', 'Gagal menghapus data. Silakan coba lagi.');
        }
    }

    /**
     * Calculate UIGM ratios for TR indicators
     */
    private function calculateUIGMRatios()
    {
        $infrastructureStats = $this->infrastructureModel->getParkingStats();
        $populationStats = $this->populationModel->getPopulationStats();
        
        // Get total vehicles from transport stats
        $totalVehicles = $this->transportStatsModel->selectSum('jumlah_total')->first()['jumlah_total'] ?? 0;
        $totalZev = $this->transportStatsModel->selectSum('jumlah_total')->where('is_zev', 1)->first()['jumlah_total'] ?? 0;
        $totalShuttle = $this->transportStatsModel->selectSum('jumlah_total')->where('is_shuttle', 1)->first()['jumlah_total'] ?? 0;
        
        // Calculate ratios
        $vehicleToPopulationRatio = $populationStats['total_populasi'] > 0 ? 
            round(($totalVehicles / $populationStats['total_populasi']) * 100, 2) : 0;
        
        $zevRatio = $totalVehicles > 0 ? 
            round(($totalZev / $totalVehicles) * 100, 2) : 0;
        
        $shuttleRatio = $populationStats['total_populasi'] > 0 ? 
            round(($totalShuttle / $populationStats['total_populasi']) * 100, 2) : 0;
        
        return [
            'parking_ratio' => $infrastructureStats['parking_ratio'], // TR 5 & 6
            'vehicle_population_ratio' => $vehicleToPopulationRatio, // TR 1
            'zev_ratio' => $zevRatio, // TR 4
            'shuttle_ratio' => $shuttleRatio, // TR 2
            'total_vehicles' => $totalVehicles,
            'total_zev' => $totalZev,
            'total_shuttle' => $totalShuttle,
            'total_population' => $populationStats['total_populasi'],
            'campus_area' => $infrastructureStats['luas_total_kampus'],
            'parking_area' => $infrastructureStats['luas_area_parkir_total']
        ];
    }

    /**
     * Get dashboard widget data
     */
    public function getDashboardWidget()
    {
        return [
            'infrastructure_stats' => $this->infrastructureModel->getParkingStats(),
            'population_stats' => $this->populationModel->getPopulationStats(),
            'uigm_ratios' => $this->calculateUIGMRatios()
        ];
    }
}