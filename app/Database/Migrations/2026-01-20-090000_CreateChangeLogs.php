<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateChangeLogs extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ],
            'user_name' => [
                'type' => 'VARCHAR',
                'constraint' => '150',
                'null' => true
            ],
            'action' => [
                'type' => 'ENUM',
                'constraint' => ['create', 'update', 'delete'],
                'null' => false
            ],
            'entity' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => false
            ],
            'entity_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ],
            'summary' => [
                'type' => 'TEXT',
                'null' => false
            ],
            'old_value' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'new_value' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('entity');
        $this->forge->addKey('created_at');
        
        $this->forge->createTable('change_logs');
    }

    public function down()
    {
        $this->forge->dropTable('change_logs');
    }
}
