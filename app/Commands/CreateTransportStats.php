<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CreateTransportStats extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'create:transportstats';
    protected $description = 'Create transport_stats table for new transportation system';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        try {
            // Check if table already exists
            if ($db->tableExists('transport_stats')) {
                CLI::write('✓ transport_stats table already exists', 'yellow');
                return;
            }

            // Create transport_stats table
            $createTableSQL = "
            CREATE TABLE transport_stats (
                id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                periode VARCHAR(100) NOT NULL COMMENT 'Contoh: Minggu 1 - April 2026, Bulan Mei 2026',
                kategori_kendaraan ENUM('Roda Dua','Roda Empat','Sepeda','Kendaraan Umum') NOT NULL,
                jenis_bahan_bakar ENUM('Bensin','Diesel','Listrik','Non-BBM') NOT NULL,
                jumlah_total INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Jumlah kendaraan yang terhitung',
                input_by INT(11) UNSIGNED NOT NULL COMMENT 'ID Security yang menginput',
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                PRIMARY KEY (id),
                KEY periode (periode),
                KEY input_by (input_by),
                KEY created_at (created_at)
            ) DEFAULT CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci
            ";
            
            $db->query($createTableSQL);
            CLI::write('✓ transport_stats table created successfully', 'green');
            
            // Insert sample data for testing
            $sampleData = [
                [
                    'periode' => 'Minggu 1 - April 2026',
                    'kategori_kendaraan' => 'Roda Dua',
                    'jenis_bahan_bakar' => 'Bensin',
                    'jumlah_total' => 150,
                    'input_by' => 25, // security user ID
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'periode' => 'Minggu 1 - April 2026',
                    'kategori_kendaraan' => 'Roda Empat',
                    'jenis_bahan_bakar' => 'Bensin',
                    'jumlah_total' => 85,
                    'input_by' => 25,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'periode' => 'Minggu 1 - April 2026',
                    'kategori_kendaraan' => 'Sepeda',
                    'jenis_bahan_bakar' => 'Non-BBM',
                    'jumlah_total' => 25,
                    'input_by' => 25,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ];
            
            foreach ($sampleData as $data) {
                $db->table('transport_stats')->insert($data);
            }
            
            CLI::write('✓ Sample data inserted successfully', 'green');
            CLI::newLine();
            CLI::write('✅ New transportation statistics system ready!', 'green');
            CLI::write('The system now uses aggregate data instead of individual vehicle logs.', 'white');
            
        } catch (\Exception $e) {
            CLI::error('❌ Error: ' . $e->getMessage());
        }
    }
}