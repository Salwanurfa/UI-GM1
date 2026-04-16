<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SetupInfrastructurePopulation extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'setup:infrastructure';
    protected $description = 'Setup infrastructure and population tables for UIGM TR indicators';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        try {
            // Create infrastructure_data table
            if (!$db->tableExists('infrastructure_data')) {
                $createInfrastructureSQL = "
                CREATE TABLE infrastructure_data (
                    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    tahun_akademik VARCHAR(20) NOT NULL COMMENT 'Contoh: 2025/2026',
                    luas_total_kampus DECIMAL(15,2) NOT NULL COMMENT 'Luas total kampus dalam m²',
                    luas_area_parkir_total DECIMAL(15,2) NOT NULL COMMENT 'Total luas area parkir dalam m²',
                    luas_parkir_terbuka DECIMAL(15,2) DEFAULT 0 COMMENT 'Luas area parkir terbuka dalam m²',
                    luas_parkir_berkanopi DECIMAL(15,2) DEFAULT 0 COMMENT 'Luas area parkir berkanopi/gedung dalam m²',
                    keterangan TEXT NULL COMMENT 'Keterangan tambahan',
                    status_aktif TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=aktif, 0=nonaktif',
                    input_by INT(11) UNSIGNED NOT NULL COMMENT 'ID Admin yang menginput',
                    created_at DATETIME NULL,
                    updated_at DATETIME NULL,
                    PRIMARY KEY (id),
                    KEY tahun_akademik (tahun_akademik),
                    KEY status_aktif (status_aktif),
                    KEY input_by (input_by)
                ) DEFAULT CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci
                ";
                
                $db->query($createInfrastructureSQL);
                CLI::write('✓ infrastructure_data table created successfully', 'green');
            } else {
                CLI::write('✓ infrastructure_data table already exists', 'yellow');
            }

            // Create population_data table
            if (!$db->tableExists('population_data')) {
                $createPopulationSQL = "
                CREATE TABLE population_data (
                    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    tahun_akademik VARCHAR(20) NOT NULL COMMENT 'Contoh: 2025/2026',
                    jumlah_dosen INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Jumlah dosen aktif',
                    jumlah_mahasiswa INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Jumlah mahasiswa aktif',
                    jumlah_tenaga_kependidikan INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Jumlah tenaga kependidikan/staff',
                    total_populasi INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Total populasi kampus (auto calculated)',
                    keterangan TEXT NULL COMMENT 'Keterangan tambahan',
                    status_aktif TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=aktif, 0=nonaktif',
                    input_by INT(11) UNSIGNED NOT NULL COMMENT 'ID Admin yang menginput',
                    created_at DATETIME NULL,
                    updated_at DATETIME NULL,
                    PRIMARY KEY (id),
                    KEY tahun_akademik (tahun_akademik),
                    KEY status_aktif (status_aktif),
                    KEY input_by (input_by)
                ) DEFAULT CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci
                ";
                
                $db->query($createPopulationSQL);
                CLI::write('✓ population_data table created successfully', 'green');
            } else {
                CLI::write('✓ population_data table already exists', 'yellow');
            }

            // Insert sample data for testing
            $currentYear = date('Y');
            $nextYear = $currentYear + 1;
            $tahunAkademik = $currentYear . '/' . $nextYear;

            // Sample infrastructure data
            $sampleInfrastructure = [
                'tahun_akademik' => $tahunAkademik,
                'luas_total_kampus' => 150000.00, // 15 hectares
                'luas_area_parkir_total' => 12000.00, // 1.2 hectares
                'luas_parkir_terbuka' => 8000.00,
                'luas_parkir_berkanopi' => 4000.00,
                'keterangan' => 'Data sample untuk testing sistem UIGM',
                'status_aktif' => 1,
                'input_by' => 1, // Assuming admin user ID 1 exists
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Sample population data
            $samplePopulation = [
                'tahun_akademik' => $tahunAkademik,
                'jumlah_dosen' => 450,
                'jumlah_mahasiswa' => 8500,
                'jumlah_tenaga_kependidikan' => 350,
                'total_populasi' => 9300, // Auto calculated
                'keterangan' => 'Data sample untuk testing sistem UIGM',
                'status_aktif' => 1,
                'input_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Check if sample data already exists
            $existingInfra = $db->table('infrastructure_data')
                ->where('tahun_akademik', $tahunAkademik)
                ->countAllResults();

            if ($existingInfra == 0) {
                $db->table('infrastructure_data')->insert($sampleInfrastructure);
                CLI::write('✓ Sample infrastructure data inserted', 'green');
            } else {
                CLI::write('- Sample infrastructure data already exists', 'yellow');
            }

            $existingPop = $db->table('population_data')
                ->where('tahun_akademik', $tahunAkademik)
                ->countAllResults();

            if ($existingPop == 0) {
                $db->table('population_data')->insert($samplePopulation);
                CLI::write('✓ Sample population data inserted', 'green');
            } else {
                CLI::write('- Sample population data already exists', 'yellow');
            }
            
            CLI::newLine();
            CLI::write('✅ Infrastructure & Population system setup complete!', 'green');
            CLI::write('The system now supports UIGM TR 1, 2, 4, 5, and 6 indicators.', 'white');
            CLI::newLine();
            CLI::write('📊 UIGM Indicators Coverage:', 'cyan');
            CLI::write('  TR 1: Vehicle to Population Ratio', 'white');
            CLI::write('  TR 2: Campus Shuttle Service', 'white');
            CLI::write('  TR 4: Zero Emission Vehicle Ratio', 'white');
            CLI::write('  TR 5 & 6: Parking Area Ratio', 'white');
            
        } catch (\Exception $e) {
            CLI::error('❌ Error: ' . $e->getMessage());
        }
    }
}