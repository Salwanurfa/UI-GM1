<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUigmWasteLinksTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'evidence_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => false,
                'comment' => 'Reference to uigm_evidence table'
            ],
            'waste_log_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => false,
                'comment' => 'Reference to waste_management table'
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
        $this->forge->addKey('evidence_id');
        $this->forge->addKey('waste_log_id');
        $this->forge->addUniqueKey(['evidence_id', 'waste_log_id']);
        
        $this->forge->createTable('uigm_waste_links');
    }

    public function down()
    {
        $this->forge->dropTable('uigm_waste_links');
    }
}