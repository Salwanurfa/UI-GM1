<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckTableName extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:check-table-name';
    protected $description = 'Check if limbah_cair table exists';

    public function run(array $params)
    {
        CLI::write('=== Checking Table Name ===', 'yellow');
        CLI::newLine();
        
        $db = \Config\Database::connect();
        
        // Get all tables
        $tables = $db->listTables();
        
        CLI::write('All tables in database:', 'green');
        foreach ($tables as $table) {
            if (strpos($table, 'limbah') !== false) {
                CLI::write('  ✓ ' . $table, 'green');
            } else {
                CLI::write('  - ' . $table);
            }
        }
        
        CLI::newLine();
        
        // Check if limbah_cair exists
        if (in_array('limbah_cair', $tables)) {
            CLI::write('✓ Table "limbah_cair" EXISTS!', 'green');
        } else {
            CLI::write('✗ Table "limbah_cair" NOT FOUND!', 'red');
            CLI::write('Looking for similar names...', 'yellow');
            foreach ($tables as $table) {
                if (stripos($table, 'limbah') !== false || stripos($table, 'cair') !== false) {
                    CLI::write('  Found: ' . $table, 'yellow');
                }
            }
        }
        
        CLI::newLine();
        CLI::write('=== Done ===', 'yellow');
    }
}
