<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDashboardSettings extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['user', 'tps'],
                'null'       => false,
            ],
            'widget_key' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => false,
            ],
            'is_active' => [
                'type'       => 'BOOLEAN',
                'default'    => true,
                'null'       => false,
            ],
            'urutan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'null'       => false,
            ],
            'custom_label' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'custom_color' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'widget_config' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['role', 'widget_key']);
        $this->forge->addKey('urutan');
        $this->forge->createTable('dashboard_settings');

        // Insert default dashboard configuration
        $this->insertDefaultSettings();
    }

    public function down()
    {
        $this->forge->dropTable('dashboard_settings');
    }

    private function insertDefaultSettings()
    {
        $defaultSettings = [
            // User Dashboard Widgets
            [
                'role' => 'user',
                'widget_key' => 'stat_cards',
                'is_active' => true,
                'urutan' => 1,
                'custom_label' => 'Statistik Data',
                'custom_color' => '#3498db',
                'widget_config' => json_encode([
                    'show_approved' => true,
                    'show_pending' => true,
                    'show_revision' => true,
                    'show_draft' => true
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'role' => 'user',
                'widget_key' => 'waste_summary',
                'is_active' => true,
                'urutan' => 2,
                'custom_label' => 'Ringkasan Waste Management',
                'custom_color' => '#2ecc71',
                'widget_config' => json_encode([
                    'show_details' => true,
                    'show_value_calculation' => true
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'role' => 'user',
                'widget_key' => 'recent_activity',
                'is_active' => true,
                'urutan' => 3,
                'custom_label' => 'Aktivitas Terbaru',
                'custom_color' => '#f39c12',
                'widget_config' => json_encode([
                    'max_items' => 5
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'role' => 'user',
                'widget_key' => 'quick_actions',
                'is_active' => true,
                'urutan' => 4,
                'custom_label' => 'Aksi Cepat',
                'custom_color' => '#9b59b6',
                'widget_config' => json_encode([
                    'show_input_form' => true,
                    'show_export' => true,
                    'show_reports' => true
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'role' => 'user',
                'widget_key' => 'price_info',
                'is_active' => true,
                'urutan' => 5,
                'custom_label' => 'Informasi Harga',
                'custom_color' => '#1abc9c',
                'widget_config' => json_encode([
                    'show_current_prices' => true,
                    'show_price_trends' => false
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],

            // TPS Dashboard Widgets (sama dengan User)
            [
                'role' => 'tps',
                'widget_key' => 'stat_cards',
                'is_active' => true,
                'urutan' => 1,
                'custom_label' => 'Statistik TPS',
                'custom_color' => '#3498db',
                'widget_config' => json_encode([
                    'show_approved' => true,
                    'show_pending' => true,
                    'show_revision' => true,
                    'show_draft' => true
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'role' => 'tps',
                'widget_key' => 'waste_summary',
                'is_active' => true,
                'urutan' => 2,
                'custom_label' => 'Ringkasan TPS',
                'custom_color' => '#2ecc71',
                'widget_config' => json_encode([
                    'show_details' => true,
                    'show_value_calculation' => true
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'role' => 'tps',
                'widget_key' => 'recent_activity',
                'is_active' => true,
                'urutan' => 3,
                'custom_label' => 'Aktivitas TPS',
                'custom_color' => '#f39c12',
                'widget_config' => json_encode([
                    'max_items' => 5
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'role' => 'tps',
                'widget_key' => 'quick_actions',
                'is_active' => true,
                'urutan' => 4,
                'custom_label' => 'Aksi Cepat TPS',
                'custom_color' => '#9b59b6',
                'widget_config' => json_encode([
                    'show_input_form' => true,
                    'show_export' => true,
                    'show_reports' => true
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'role' => 'tps',
                'widget_key' => 'tps_operations',
                'is_active' => true,
                'urutan' => 5,
                'custom_label' => 'Operasional TPS',
                'custom_color' => '#e74c3c',
                'widget_config' => json_encode([
                    'show_capacity' => true,
                    'show_schedule' => true
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $builder = \Config\Database::connect()->table('dashboard_settings');
        $builder->insertBatch($defaultSettings);
    }
}