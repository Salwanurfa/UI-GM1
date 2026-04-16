<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixCollationIssues extends Migration
{
    public function up()
    {
        // Fix collation for waste_categories table
        $this->db->query("ALTER TABLE waste_categories CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
        
        // Fix collation for master_harga_sampah table
        $this->db->query("ALTER TABLE master_harga_sampah CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
        
        // Fix collation for other related tables that might have collation issues
        $tables = [
            'waste_management',
            'limbah_b3',
            'users',
            'waste_categories_standard'
        ];
        
        foreach ($tables as $table) {
            if ($this->db->tableExists($table)) {
                try {
                    $this->db->query("ALTER TABLE {$table} CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
                    echo "Fixed collation for table: {$table}\n";
                } catch (\Exception $e) {
                    echo "Warning: Could not fix collation for table {$table}: " . $e->getMessage() . "\n";
                }
            }
        }
        
        echo "Collation fix migration completed successfully.\n";
    }

    public function down()
    {
        // This migration cannot be easily reversed as it changes character sets
        // If needed, you would need to manually revert the collations
        echo "Collation changes cannot be automatically reverted.\n";
    }
}