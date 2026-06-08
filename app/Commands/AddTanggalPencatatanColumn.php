<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AddTanggalPencatatanColumn extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'add:tanggalpencatatan';
    protected $description = 'Add tanggal_pencatatan column to transport_stats table';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        try {
            // Check if table exists
            if (!$db->tableExists('transport_stats')) {
                CLI::error('❌ transport_stats table does not exist');
                return;
            }

            // Check if column already exists
            if ($db->fieldExists('tanggal_pencatatan', 'transport_stats')) {
                CLI::write('✓ tanggal_pencatatan column already exists', 'yellow');
                return;
            }

            // Add tanggal_pencatatan column
            $sql = "ALTER TABLE transport_stats 
                    ADD COLUMN tanggal_pencatatan DATE NULL 
                    COMMENT 'Tanggal kejadian/pencatatan transportasi' 
                    AFTER periode";
            
            $db->query($sql);
            CLI::write('✓ tanggal_pencatatan column added successfully', 'green');
            
            // Update existing records with created_at date
            $updateSQL = "UPDATE transport_stats 
                         SET tanggal_pencatatan = DATE(created_at) 
                         WHERE tanggal_pencatatan IS NULL";
            $db->query($updateSQL);
            CLI::write('✓ Existing records updated with tanggal_pencatatan', 'green');
            
            CLI::newLine();
            CLI::write('✅ Migration completed successfully!', 'green');
            
        } catch (\Exception $e) {
            CLI::error('❌ Error: ' . $e->getMessage());
        }
    }
}
