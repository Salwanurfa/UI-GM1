<?php

namespace App\Models;

use CodeIgniter\Model;

class DashboardSettingModel extends Model
{
    protected $table            = 'dashboard_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'role',
        'widget_key',
        'is_enabled',  // Fixed: use is_enabled instead of is_active
        'urutan',
        'custom_label',
        'custom_color',
        'widget_config'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'role' => 'required|in_list[user,tps]',
        'widget_key' => 'required|max_length[100]',
        'is_enabled' => 'required|in_list[0,1]',  // Fixed: use is_enabled
        'urutan' => 'required|integer',
        'custom_label' => 'permit_empty|max_length[255]',
        'custom_color' => 'permit_empty|max_length[50]',
    ];

    protected $validationMessages = [
        'role' => [
            'required' => 'Role harus diisi',
            'in_list' => 'Role harus user atau tps'
        ],
        'widget_key' => [
            'required' => 'Widget key harus diisi',
            'max_length' => 'Widget key maksimal 100 karakter'
        ],
        'is_enabled' => [  // Fixed: use is_enabled
            'required' => 'Status aktif harus diisi',
            'in_list' => 'Status aktif harus 0 atau 1'
        ],
        'urutan' => [
            'required' => 'Urutan harus diisi',
            'integer' => 'Urutan harus berupa angka'
        ]
    ];

    // Callbacks
    protected $beforeInsert = ['beforeInsert'];
    protected $beforeUpdate = ['beforeUpdate'];

    protected function beforeInsert(array $data)
    {
        return $this->processWidgetConfig($data);
    }

    protected function beforeUpdate(array $data)
    {
        return $this->processWidgetConfig($data);
    }

    private function processWidgetConfig(array $data)
    {
        if (isset($data['data']['widget_config']) && is_array($data['data']['widget_config'])) {
            $data['data']['widget_config'] = json_encode($data['data']['widget_config']);
        }
        return $data;
    }

    /**
     * Get dashboard configuration by role
     */
    public function getDashboardConfigByRole(string $role): array
    {
        $settings = $this->where('role', $role)
                        ->where('is_enabled', 1)  // Fixed: use is_enabled
                        ->orderBy('display_order', 'ASC')  // Fixed: use display_order
                        ->findAll();

        // Decode JSON config
        foreach ($settings as &$setting) {
            if (!empty($setting['widget_config'])) {
                $setting['widget_config'] = json_decode($setting['widget_config'], true);
            } else {
                $setting['widget_config'] = [];
            }
        }

        return $settings;
    }

    /**
     * Get all settings for admin management
     */
    public function getAllSettingsGroupedByRole(): array
    {
        $settings = $this->orderBy('role', 'ASC')
                        ->orderBy('display_order', 'ASC')  // Fixed: use display_order
                        ->findAll();

        $grouped = [];
        foreach ($settings as $setting) {
            if (!empty($setting['widget_config'])) {
                $setting['widget_config'] = json_decode($setting['widget_config'], true);
            } else {
                $setting['widget_config'] = [];
            }
            $grouped[$setting['role']][] = $setting;
        }

        return $grouped;
    }

    /**
     * Update widget order
     */
    public function updateWidgetOrder(string $role, array $widgetOrders): bool
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            foreach ($widgetOrders as $order => $widgetId) {
                $this->update($widgetId, ['display_order' => $order + 1]);  // Fixed: use display_order
            }

            $db->transComplete();
            return $db->transStatus();
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error updating widget order: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Toggle widget active status
     */
    public function toggleWidget(int $id): bool
    {
        $widget = $this->find($id);
        if (!$widget) {
            return false;
        }

        return $this->update($id, ['is_enabled' => !$widget['is_enabled']]);  // Fixed: use is_enabled
    }

    /**
     * Update widget configuration
     */
    public function updateWidgetConfig(int $id, array $config): bool
    {
        return $this->update($id, [
            'custom_label' => $config['custom_label'] ?? null,
            'custom_color' => $config['custom_color'] ?? null,
            'widget_config' => $config['widget_config'] ?? []
        ]);
    }

    /**
     * Get available widget types
     */
    public function getAvailableWidgetTypes(): array
    {
        return [
            'stat_cards' => [
                'name' => 'Kartu Statistik',
                'description' => 'Menampilkan statistik data dalam bentuk kartu',
                'icon' => 'fas fa-chart-bar',
                'configurable' => ['show_approved', 'show_pending', 'show_revision', 'show_draft']
            ],
            'waste_summary' => [
                'name' => 'Ringkasan Waste',
                'description' => 'Ringkasan data waste management per jenis',
                'icon' => 'fas fa-recycle',
                'configurable' => ['show_details', 'show_value_calculation']
            ],
            'recent_activity' => [
                'name' => 'Aktivitas Terbaru',
                'description' => 'Daftar aktivitas terbaru pengguna',
                'icon' => 'fas fa-history',
                'configurable' => ['max_items']
            ],
            'quick_actions' => [
                'name' => 'Aksi Cepat',
                'description' => 'Tombol aksi cepat untuk fitur utama',
                'icon' => 'fas fa-bolt',
                'configurable' => ['show_input_form', 'show_export', 'show_reports']
            ],
            'price_info' => [
                'name' => 'Informasi Harga',
                'description' => 'Informasi harga sampah terkini',
                'icon' => 'fas fa-money-bill-wave',
                'configurable' => ['show_current_prices', 'show_price_trends']
            ],
            'tps_operations' => [
                'name' => 'Operasional TPS',
                'description' => 'Informasi operasional khusus TPS',
                'icon' => 'fas fa-industry',
                'configurable' => ['show_capacity', 'show_schedule']
            ]
        ];
    }

    /**
     * Reset to default configuration
     */
    public function resetToDefault(string $role): bool
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Delete existing settings for role
            $this->where('role', $role)->delete();

            // Re-insert default settings
            $migration = new \App\Database\Migrations\CreateDashboardSettings();
            $reflection = new \ReflectionClass($migration);
            $method = $reflection->getMethod('insertDefaultSettings');
            $method->setAccessible(true);
            $method->invoke($migration);

            $db->transComplete();
            return $db->transStatus();
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error resetting dashboard settings: ' . $e->getMessage());
            return false;
        }
    }
}