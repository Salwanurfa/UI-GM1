<?php

namespace App\Services\Admin;

use App\Models\HargaSampahModel;
use App\Models\HargaLogModel;
use App\Models\ChangeLogModel;

class HargaService
{
    protected $hargaModel;
    protected $logModel;
    protected $changeLogModel;

    public function __construct()
    {
        $this->hargaModel = new HargaSampahModel();
        $this->logModel = new HargaLogModel();
        $this->changeLogModel = new ChangeLogModel();
    }

    public function getHargaData(): array
    {
        try {
            return [
                'harga_list' => $this->hargaModel->findAll(),
                'categories' => $this->getCategories()
            ];
        } catch (\Exception $e) {
            log_message('error', 'Harga Service Error: ' . $e->getMessage());
            
            return [
                'harga_list' => [],
                'categories' => []
            ];
        }
    }

    public function createHarga(array $data): array
    {
        try {
            $validation = $this->validateHargaData($data);
            if (!$validation['valid']) {
                return ['success' => false, 'message' => $validation['message']];
            }

            $hargaData = [
                'jenis_sampah' => $data['jenis_sampah'],
                'nama_jenis' => $data['nama_jenis'] ?? $data['jenis_sampah'],
                'harga_per_satuan' => $data['harga_per_satuan'],
                'harga_per_kg' => $data['harga_per_kg'] ?? $data['harga_per_satuan'],
                'satuan' => $data['satuan'] ?? 'kg',
                'dapat_dijual' => $data['dapat_dijual'] ?? 1,
                'status_aktif' => 1,
                'deskripsi' => $data['deskripsi'] ?? null,
                'tanggal_berlaku' => date('Y-m-d')
            ];

            $result = $this->hargaModel->insert($hargaData);
            
            if ($result) {
                $insertId = $this->hargaModel->getInsertID();
                
                // Log to harga_log
                $userId = session()->get('user')['id'] ?? 1;
                $this->logModel->logPriceChange(
                    $insertId,
                    $hargaData['jenis_sampah'],
                    null,
                    $hargaData['harga_per_satuan'],
                    $userId,
                    'Harga baru ditambahkan'
                );
                
                // Log to change_logs
                $user = session()->get('user');
                $this->changeLogModel->insertLog([
                    'user_id' => $userId,
                    'user_name' => $user['nama_lengkap'] ?? $user['username'] ?? 'Admin',
                    'action' => 'create',
                    'entity' => 'harga_sampah',
                    'entity_id' => $insertId,
                    'summary' => "Menambahkan jenis sampah baru: {$hargaData['jenis_sampah']} - {$hargaData['nama_jenis']}",
                    'new_value' => json_encode([
                        'jenis_sampah' => $hargaData['jenis_sampah'],
                        'nama_jenis' => $hargaData['nama_jenis'],
                        'harga_per_satuan' => $hargaData['harga_per_satuan'],
                        'satuan' => $hargaData['satuan']
                    ])
                ]);
                
                return ['success' => true, 'message' => 'Harga berhasil ditambahkan'];
            }

            return ['success' => false, 'message' => 'Gagal menambahkan harga'];

        } catch (\Exception $e) {
            log_message('error', 'Create Harga Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem'];
        }
    }

    public function updateHarga(int $id, array $data): array
    {
        try {
            $validation = $this->validateHargaData($data);
            if (!$validation['valid']) {
                return ['success' => false, 'message' => $validation['message']];
            }

            $oldData = $this->hargaModel->find($id);
            if (!$oldData) {
                return ['success' => false, 'message' => 'Data harga tidak ditemukan'];
            }

            $hargaData = [
                'jenis_sampah' => $data['jenis_sampah'],
                'nama_jenis' => $data['nama_jenis'] ?? $data['jenis_sampah'],
                'harga_per_satuan' => $data['harga_per_satuan'],
                'harga_per_kg' => $data['harga_per_kg'] ?? $data['harga_per_satuan'],
                'satuan' => $data['satuan'] ?? 'kg',
                'dapat_dijual' => $data['dapat_dijual'] ?? 1,
                'deskripsi' => $data['deskripsi'] ?? null,
                'tanggal_berlaku' => date('Y-m-d')
            ];

            $result = $this->hargaModel->update($id, $hargaData);
            
            if ($result) {
                // Log to harga_log
                $userId = session()->get('user')['id'] ?? 1;
                $this->logModel->logPriceChange(
                    $id,
                    $hargaData['jenis_sampah'],
                    $oldData['harga_per_satuan'],
                    $hargaData['harga_per_satuan'],
                    $userId,
                    'Harga diupdate'
                );
                
                // Log to change_logs
                $user = session()->get('user');
                $changes = [];
                if ($oldData['harga_per_satuan'] != $hargaData['harga_per_satuan']) {
                    $changes[] = "Harga: Rp " . number_format($oldData['harga_per_satuan'], 0, ',', '.') . " → Rp " . number_format($hargaData['harga_per_satuan'], 0, ',', '.');
                }
                if ($oldData['jenis_sampah'] != $hargaData['jenis_sampah']) {
                    $changes[] = "Kategori: {$oldData['jenis_sampah']} → {$hargaData['jenis_sampah']}";
                }
                if ($oldData['nama_jenis'] != $hargaData['nama_jenis']) {
                    $changes[] = "Nama: {$oldData['nama_jenis']} → {$hargaData['nama_jenis']}";
                }
                
                $summary = "Mengupdate {$hargaData['jenis_sampah']} - {$hargaData['nama_jenis']}";
                if (!empty($changes)) {
                    $summary .= " (" . implode(', ', $changes) . ")";
                }
                
                $this->changeLogModel->insertLog([
                    'user_id' => $userId,
                    'user_name' => $user['nama_lengkap'] ?? $user['username'] ?? 'Admin',
                    'action' => 'update',
                    'entity' => 'harga_sampah',
                    'entity_id' => $id,
                    'summary' => $summary,
                    'old_value' => json_encode($oldData),
                    'new_value' => json_encode($hargaData)
                ]);
                
                return ['success' => true, 'message' => 'Harga berhasil diupdate'];
            }

            return ['success' => false, 'message' => 'Gagal mengupdate harga'];

        } catch (\Exception $e) {
            log_message('error', 'Update Harga Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem'];
        }
    }

    public function toggleStatus(int $id): array
    {
        try {
            $harga = $this->hargaModel->find($id);
            if (!$harga) {
                return ['success' => false, 'message' => 'Data harga tidak ditemukan'];
            }

            $newStatus = $harga['status_aktif'] ? 0 : 1;
            
            $result = $this->hargaModel->update($id, [
                'status_aktif' => $newStatus
            ]);

            if ($result) {
                // Log to harga_log
                $userId = session()->get('user')['id'] ?? 1;
                $this->logModel->logPriceChange(
                    $id,
                    $harga['jenis_sampah'],
                    $harga['harga_per_satuan'],
                    $harga['harga_per_satuan'],
                    $userId,
                    'Status diubah menjadi ' . ($newStatus ? 'aktif' : 'nonaktif')
                );
                
                // Log to change_logs
                $user = session()->get('user');
                $this->changeLogModel->insertLog([
                    'user_id' => $userId,
                    'user_name' => $user['nama_lengkap'] ?? $user['username'] ?? 'Admin',
                    'action' => 'update',
                    'entity' => 'harga_sampah',
                    'entity_id' => $id,
                    'summary' => "Mengubah status {$harga['jenis_sampah']} - {$harga['nama_jenis']} menjadi " . ($newStatus ? 'aktif' : 'nonaktif'),
                    'old_value' => json_encode(['status_aktif' => !$newStatus]),
                    'new_value' => json_encode(['status_aktif' => $newStatus])
                ]);
                
                return ['success' => true, 'message' => 'Status berhasil diubah'];
            }

            return ['success' => false, 'message' => 'Gagal mengubah status'];

        } catch (\Exception $e) {
            log_message('error', 'Toggle Harga Status Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem'];
        }
    }

    public function deleteHarga(int $id): array
    {
        try {
            $harga = $this->hargaModel->find($id);
            if (!$harga) {
                return ['success' => false, 'message' => 'Data harga tidak ditemukan'];
            }

            // Log before delete
            try {
                $userId = session()->get('user')['id'] ?? 1;
                $user = session()->get('user');
                
                // Log to harga_log
                if (isset($this->logModel)) {
                    $this->logModel->logPriceChange(
                        $id,
                        $harga['jenis_sampah'],
                        $harga['harga_per_satuan'],
                        0,
                        $userId,
                        'Harga dihapus'
                    );
                }
                
                // Log to change_logs
                $this->changeLogModel->insertLog([
                    'user_id' => $userId,
                    'user_name' => $user['nama_lengkap'] ?? $user['username'] ?? 'Admin',
                    'action' => 'delete',
                    'entity' => 'harga_sampah',
                    'entity_id' => $id,
                    'summary' => "Menghapus jenis sampah: {$harga['jenis_sampah']} - {$harga['nama_jenis']}",
                    'old_value' => json_encode($harga)
                ]);
            } catch (\Exception $logError) {
                // Log error tapi tetap lanjut hapus
                log_message('warning', 'Failed to log price change: ' . $logError->getMessage());
            }
            
            $result = $this->hargaModel->delete($id);
            
            if ($result) {
                return ['success' => true, 'message' => 'Harga berhasil dihapus'];
            }

            return ['success' => false, 'message' => 'Gagal menghapus harga'];

        } catch (\Exception $e) {
            log_message('error', 'Delete Harga Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return ['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()];
        }
    }

    public function getHargaLogs(): array
    {
        try {
            return [
                'logs' => $this->logModel->getRecentChanges(100)
            ];
        } catch (\Exception $e) {
            log_message('error', 'Harga Logs Error: ' . $e->getMessage());
            return ['logs' => []];
        }
    }

    private function validateHargaData(array $data): array
    {
        if (empty($data['jenis_sampah'])) {
            return ['valid' => false, 'message' => 'Jenis sampah harus diisi'];
        }

        if (empty($data['harga_per_satuan']) || !is_numeric($data['harga_per_satuan'])) {
            return ['valid' => false, 'message' => 'Harga per satuan harus berupa angka'];
        }

        if ($data['harga_per_satuan'] < 0) {
            return ['valid' => false, 'message' => 'Harga per satuan tidak boleh negatif'];
        }

        return ['valid' => true, 'message' => ''];
    }

    private function getCategories(): array
    {
        return [
            'Plastik',
            'Kertas',
            'Logam',
            'Organik',
            'Residu'
        ];
    }
}