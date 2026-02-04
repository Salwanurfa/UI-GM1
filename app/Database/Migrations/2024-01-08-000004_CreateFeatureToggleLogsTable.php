<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFeatureToggleLogsTable extends Migration
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
            'action' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'old_value' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'new_value' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'admin_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'admin_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['feature_key', 'created_at']);
        $this->forge->addKey(['admin_id', 'created_at']);
        $this->forge->createTable('feature_toggle_logs');
    }

    public function down()
    {
        $this->forge->dropTable('feature_toggle_logs');
    }
}