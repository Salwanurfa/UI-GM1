<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AddRentangTanggalColumns extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'add:rentangtanggal';
    protected $description = 'Add tanggal_mulai and tanggal_selesai columns to transport_stats table for backup feature';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        try {
            // Check if table exists
            if (!$db->tableExists('transport_stats')) {
                CLI::error('❌ transport_stats table does not exist');
                return;
            }

            // Check if columns already exist
            $needsUpdate = false;
            
            if (!$db->fieldExists('tanggal_mulai', 'transport_stats')) {
                $sql = "ALTER TABLE transport_stats 
                        ADD COLUMN tanggal_mulai DATE NULL 
                        COMMENT 'Tanggal mulai untuk rentang tanggal (backup)' 
                        AFTER tanggal_pencatatan";
                $db->query($sql);
                CLI::write('✓ tanggal_mulai column added successfully', 'green');
                $needsUpdate = true;
            } else {
                CLI::write('✓ tanggal_mulai column already exists', 'yellow');
            }

            if (!$db->fieldExists('tanggal_selesai', 'transport_stats')) {
                $sql = "ALTER TABLE transport_stats 
                        ADD COLUMN tanggal_selesai DATE NULL 
                        COMMENT 'Tanggal selesai untuk rentang tanggal (backup)' 
                        AFTER tanggal_mulai";
                $db->query($sql);
                CLI::write('✓ tanggal_selesai column added successfully', 'green');
                $needsUpdate = true;
            } else {
                CLI::write('✓ tanggal_selesai column already exists', 'yellow');
            }
            
            if ($needsUpdate) {
                CLI::newLine();
                CLI::write('✅ Migration completed successfully!', 'green');
                CLI::write('Columns tanggal_mulai and tanggal_selesai are now available for backup feature.', 'white');
            } else {
                CLI::newLine();
                CLI::write('✅ All columns already exist. No changes needed.', 'green');
            }
            
        } catch (\Exception $e) {
            CLI::error('❌ Error: ' . $e->getMessage());
        }
    }
}
