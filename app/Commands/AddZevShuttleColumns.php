<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AddZevShuttleColumns extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'add:zevshuttle';
    protected $description = 'Add is_zev and is_shuttle columns to transport_stats table';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        try {
            // Check if columns already exist
            $fields = $db->getFieldData('transport_stats');
            $columnNames = array_column($fields, 'name');
            
            if (in_array('is_zev', $columnNames) && in_array('is_shuttle', $columnNames)) {
                CLI::write('✓ ZEV and Shuttle columns already exist', 'yellow');
                return;
            }

            // Add is_zev column if not exists
            if (!in_array('is_zev', $columnNames)) {
                $addZevSQL = "ALTER TABLE transport_stats ADD COLUMN is_zev TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1=Zero Emission Vehicle (ZEV), 0=Conventional Vehicle'";
                $db->query($addZevSQL);
                CLI::write('✓ is_zev column added successfully', 'green');
            }

            // Add is_shuttle column if not exists
            if (!in_array('is_shuttle', $columnNames)) {
                $addShuttleSQL = "ALTER TABLE transport_stats ADD COLUMN is_shuttle TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1=Shuttle/Campus Transportation Service, 0=Regular Vehicle'";
                $db->query($addShuttleSQL);
                CLI::write('✓ is_shuttle column added successfully', 'green');
            }

            // Add indexes for better performance
            try {
                $db->query("CREATE INDEX idx_transport_stats_zev ON transport_stats (is_zev)");
                CLI::write('✓ ZEV index created', 'green');
            } catch (\Exception $e) {
                CLI::write('- ZEV index already exists', 'yellow');
            }

            try {
                $db->query("CREATE INDEX idx_transport_stats_shuttle ON transport_stats (is_shuttle)");
                CLI::write('✓ Shuttle index created', 'green');
            } catch (\Exception $e) {
                CLI::write('- Shuttle index already exists', 'yellow');
            }

            // Update existing records to set ZEV status based on fuel type
            $updateZevSQL = "UPDATE transport_stats SET is_zev = 1 WHERE jenis_bahan_bakar IN ('Listrik', 'Non-BBM')";
            $db->query($updateZevSQL);
            CLI::write('✓ Existing records updated with ZEV status', 'green');
            
            CLI::newLine();
            CLI::write('✅ ZEV and Shuttle columns setup complete!', 'green');
            CLI::write('The system now supports UIGM TR 2, TR 3, and TR 4 indicators.', 'white');
            
        } catch (\Exception $e) {
            CLI::error('❌ Error: ' . $e->getMessage());
        }
    }
}