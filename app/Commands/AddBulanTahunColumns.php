<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AddBulanTahunColumns extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'add:bulantahun';
    protected $description = 'Add bulan and tahun columns to transport_stats table for Bulanan (Back-up) mode';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $forge = \Config\Database::forge();

        CLI::write('Adding bulan and tahun columns to transport_stats table...', 'yellow');

        try {
            // Check if columns already exist
            if ($db->fieldExists('bulan', 'transport_stats')) {
                CLI::write('Column "bulan" already exists in transport_stats table.', 'yellow');
            } else {
                // Add bulan column
                $fields = [
                    'bulan' => [
                        'type' => 'VARCHAR',
                        'constraint' => 20,
                        'null' => true,
                        'after' => 'tanggal_selesai'
                    ]
                ];
                $forge->addColumn('transport_stats', $fields);
                CLI::write('Column "bulan" added successfully!', 'green');
            }

            if ($db->fieldExists('tahun', 'transport_stats')) {
                CLI::write('Column "tahun" already exists in transport_stats table.', 'yellow');
            } else {
                // Add tahun column
                $fields = [
                    'tahun' => [
                        'type' => 'INT',
                        'constraint' => 4,
                        'null' => true,
                        'after' => 'bulan'
                    ]
                ];
                $forge->addColumn('transport_stats', $fields);
                CLI::write('Column "tahun" added successfully!', 'green');
            }

            CLI::write('Migration completed successfully!', 'green');
            CLI::write('You can now use Bulanan (Back-up) mode with Bulan and Tahun dropdowns.', 'cyan');

        } catch (\Exception $e) {
            CLI::error('Error: ' . $e->getMessage());
            return EXIT_ERROR;
        }

        return EXIT_SUCCESS;
    }
}
