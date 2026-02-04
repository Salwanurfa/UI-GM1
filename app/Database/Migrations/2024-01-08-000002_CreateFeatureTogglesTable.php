<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFeatureTogglesTable extends Migration
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
            'feature_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'feature_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'category' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'default'    => 'general',
            ],
            'is_enabled' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 0,
                'comment'    => '1=enabled, 0=disabled',
            ],
            'target_roles' => [
                'type' => 'JSON',
                'null' => true,
                'comment' => 'JSON array of roles that can use this feature',
            ],
            'config' => [
                'type' => 'JSON',
                'null' => true,
                'comment' => 'JSON configuration for the feature',
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'updated_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
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
        $this->forge->addUniqueKey('feature_key');
        $this->forge->addKey(['category', 'is_enabled']);
        $this->forge->createTable('feature_toggles');

        // Insert sample feature toggles
        $data = [
            [
                'feature_key' => 'dashboard_statistics_cards',
                'feature_name' => 'Dashboard Statistics Cards',
                'description' => 'Show/hide statistics cards on dashboard',
                'category' => 'dashboard',
                'is_enabled' => 1,
                'target_roles' => json_encode(['user', 'pengelola_tps', 'admin_pusat']),
                'config' => json_encode([
                    'show_approved' => true,
                    'show_pending' => true,
                    'show_revision' => true,
                    'show_draft' => true
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'feature_key' => 'dashboard_waste_summary',
                'feature_name' => 'Dashboard Waste Summary',
                'description' => 'Show/hide waste management summary on dashboard',
                'category' => 'dashboard',
                'is_enabled' => 1,
                'target_roles' => json_encode(['user', 'pengelola_tps']),
                'config' => json_encode([
                    'show_details' => true,
                    'show_value_calculation' => true
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'feature_key' => 'dashboard_recent_activity',
                'feature_name' => 'Dashboard Recent Activity',
                'description' => 'Show/hide recent activity feed on dashboard',
                'category' => 'dashboard',
                'is_enabled' => 1,
                'target_roles' => json_encode(['user', 'pengelola_tps', 'admin_pusat']),
                'config' => json_encode([
                    'max_items' => 5
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'feature_key' => 'waste_value_calculation',
                'feature_name' => 'Waste Value Calculation',
                'description' => 'Enable/disable automatic waste value calculation',
                'category' => 'waste_management',
                'is_enabled' => 1,
                'target_roles' => json_encode(['user', 'pengelola_tps']),
                'config' => json_encode([
                    'show_price_breakdown' => true,
                    'auto_calculate' => true
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'feature_key' => 'sidebar_quick_actions',
                'feature_name' => 'Sidebar Quick Actions',
                'description' => 'Show/hide quick action buttons in sidebar',
                'category' => 'ui_components',
                'is_enabled' => 1,
                'target_roles' => json_encode(['user', 'pengelola_tps']),
                'config' => json_encode([
                    'show_input_form' => true,
                    'show_export' => true,
                    'show_reports' => true
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'feature_key' => 'help_tooltips',
                'feature_name' => 'Help Tooltips',
                'description' => 'Show/hide help tooltips throughout the application',
                'category' => 'ui_components',
                'is_enabled' => 1,
                'target_roles' => json_encode(['user', 'pengelola_tps']),
                'config' => json_encode([
                    'show_advanced_help' => true
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('feature_toggles')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('feature_toggles');
    }
}