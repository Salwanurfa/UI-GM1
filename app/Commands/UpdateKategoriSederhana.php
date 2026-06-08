<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class UpdateKategoriSederhana extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'update:kategori-sederhana';
    protected $description = 'Update kategori_sederhana untuk data transportasi yang sudah ada';

    public function run(array $params)
    {
        CLI::write('Starting kategori_sederhana update...', 'yellow');
        
        $db = \Config\Database::connect();
        
        try {
            // Check if column exists
            if (!$db->fieldExists('kategori_sederhana', 'transport_stats')) {
                CLI::error('Column kategori_sederhana does not exist. Running migration first...');
                
                // Run migration
                $migrate = \Config\Services::migrations();
                $migrate->latest();
                
                CLI::write('Migration completed!', 'green');
            }
            
            CLI::write('Updating existing data...', 'yellow');
            
            // Rule 1: Listrik atau Sepeda → Fasilitas Kampus
            $result1 = $db->query("
                UPDATE transport_stats 
                SET kategori_sederhana = 'Fasilitas Kampus' 
                WHERE jenis_bahan_bakar = 'Listrik' 
                   OR jenis_bahan_bakar = 'Non-BBM'
                   OR kategori_kendaraan LIKE '%Sepeda%'
            ");
            CLI::write('✓ Updated Fasilitas Kampus records: ' . $db->affectedRows(), 'green');
            
            // Rule 2: Sepeda Motor, Roda Dua → Roda Dua
            $result2 = $db->query("
                UPDATE transport_stats 
                SET kategori_sederhana = 'Roda Dua' 
                WHERE kategori_sederhana IS NULL
                  AND (kategori_kendaraan LIKE '%Motor%' 
                       OR kategori_kendaraan LIKE '%Roda Dua%'
                       OR kategori_kendaraan LIKE '%Roda 2%')
            ");
            CLI::write('✓ Updated Roda Dua records: ' . $db->affectedRows(), 'green');
            
            // Rule 3: Mobil, Bus, Truck → Roda Empat
            $result3 = $db->query("
                UPDATE transport_stats 
                SET kategori_sederhana = 'Roda Empat' 
                WHERE kategori_sederhana IS NULL
                  AND (kategori_kendaraan LIKE '%Mobil%' 
                       OR kategori_kendaraan LIKE '%Bus%'
                       OR kategori_kendaraan LIKE '%Truck%'
                       OR kategori_kendaraan LIKE '%Roda Empat%'
                       OR kategori_kendaraan LIKE '%Roda 4%')
            ");
            CLI::write('✓ Updated Roda Empat records: ' . $db->affectedRows(), 'green');
            
            // Default: Bensin → Roda Dua
            $result4 = $db->query("
                UPDATE transport_stats 
                SET kategori_sederhana = 'Roda Dua' 
                WHERE kategori_sederhana IS NULL
                  AND jenis_bahan_bakar = 'Bensin'
            ");
            CLI::write('✓ Updated default Bensin to Roda Dua: ' . $db->affectedRows(), 'green');
            
            // Default: Diesel → Roda Empat
            $result5 = $db->query("
                UPDATE transport_stats 
                SET kategori_sederhana = 'Roda Empat' 
                WHERE kategori_sederhana IS NULL
                  AND jenis_bahan_bakar = 'Diesel'
            ");
            CLI::write('✓ Updated default Diesel to Roda Empat: ' . $db->affectedRows(), 'green');
            
            // Final fallback: Any remaining NULL → Roda Empat
            $result6 = $db->query("
                UPDATE transport_stats 
                SET kategori_sederhana = 'Roda Empat' 
                WHERE kategori_sederhana IS NULL
            ");
            CLI::write('✓ Updated remaining NULL records: ' . $db->affectedRows(), 'green');
            
            // Show summary
            $total = $db->table('transport_stats')->countAll();
            $updated = $db->table('transport_stats')
                ->where('kategori_sederhana IS NOT NULL')
                ->countAllResults();
            
            CLI::newLine();
            CLI::write('========================================', 'cyan');
            CLI::write('Update Summary:', 'cyan');
            CLI::write('Total records: ' . $total, 'white');
            CLI::write('Updated records: ' . $updated, 'green');
            CLI::write('========================================', 'cyan');
            CLI::newLine();
            
            CLI::write('✓ Kategori sederhana update completed successfully!', 'green');
            
        } catch (\Exception $e) {
            CLI::error('Error: ' . $e->getMessage());
            return EXIT_ERROR;
        }
        
        return EXIT_SUCCESS;
    }
}
