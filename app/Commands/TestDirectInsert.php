<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestDirectInsert extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:test-direct-insert';
    protected $description = 'Test direct insert to limbah_cair';

    public function run(array $params)
    {
        CLI::write('=== Testing Direct Insert ===', 'yellow');
        CLI::newLine();
        
        try {
            $model = new \App\Models\LimbahCairModel();
            
            // Data test sederhana
            $data = [
                'id_user'        => 1,
                'tanggal_input'  => date('Y-m-d H:i:s'),
                'lokasi'         => 'Test Lokasi',
                'nama_limbah'    => 'Test Limbah',
                'timbulan'       => 10.5,
                'satuan'         => 'L/bulan',
                'status'         => 'draft',
            ];
            
            CLI::write('Attempting to insert:', 'green');
            print_r($data);
            CLI::newLine();
            
            $result = $model->insert($data);
            
            if ($result === false) {
                CLI::write('✗ INSERT FAILED!', 'red');
                CLI::write('Errors:', 'red');
                print_r($model->errors());
            } else {
                $insertId = $model->getInsertID();
                CLI::write('✓ INSERT SUCCESS!', 'green');
                CLI::write('Insert ID: ' . $insertId, 'green');
                
                // Verify
                $inserted = $model->find($insertId);
                CLI::newLine();
                CLI::write('Verified from database:', 'green');
                print_r($inserted);
            }
            
        } catch (\Exception $e) {
            CLI::write('✗ EXCEPTION!', 'red');
            CLI::write('Message: ' . $e->getMessage(), 'red');
            CLI::write('File: ' . $e->getFile() . ':' . $e->getLine(), 'red');
            CLI::newLine();
            CLI::write('Stack trace:', 'red');
            CLI::write($e->getTraceAsString());
        }
        
        CLI::newLine();
        CLI::write('=== Done ===', 'yellow');
    }
}
