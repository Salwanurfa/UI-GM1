<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestInsertLimbahCair extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:test-insert-limbah-cair';
    protected $description = 'Test insert data to limbah_cair table';

    public function run(array $params)
    {
        CLI::write('=== Testing Insert to limbah_cair ===', 'yellow');
        CLI::newLine();
        
        $model = new \App\Models\LimbahCairModel();
        
        // Data test
        $testData = [
            'id_user'        => 1, // Ganti dengan user ID yang valid
            'tanggal_input'  => date('Y-m-d H:i:s'),
            'lokasi'         => 'Lab Kimia - Test',
            'nama_limbah'    => 'Limbah Asam',
            'kode_limbah'    => 'A102D',
            'tingkat_bahaya' => '1',
            'karakteristik'  => 'Korosif',
            'pengolahan'     => 'IPAL',
            'timbulan'       => 25.5,
            'satuan'         => 'L/bulan',
            'bentuk_fisik'   => 'Cair',
            'kemasan'        => 'Jerigen @20L',
            'ph'             => 3.5,
            'bod'            => 150.0,
            'cod'            => 300.0,
            'tss'            => 50.0,
            'keterangan'     => 'Data test dari command',
            'status'         => 'dikirim_ke_tps', // WAJIB
        ];
        
        CLI::write('Data to insert:', 'green');
        print_r($testData);
        CLI::newLine();
        
        try {
            $result = $model->insert($testData);
            
            if ($result === false) {
                CLI::write('✗ INSERT FAILED!', 'red');
                CLI::write('Errors:', 'red');
                print_r($model->errors());
            } else {
                $insertId = $model->getInsertID();
                CLI::write('✓ INSERT SUCCESS!', 'green');
                CLI::write('Insert ID: ' . $insertId, 'green');
                
                // Verify data
                $inserted = $model->find($insertId);
                CLI::newLine();
                CLI::write('Verified data from database:', 'green');
                print_r($inserted);
            }
        } catch (\Exception $e) {
            CLI::write('✗ EXCEPTION: ' . $e->getMessage(), 'red');
            CLI::write('Stack trace:', 'red');
            CLI::write($e->getTraceAsString());
        }
        
        CLI::newLine();
        CLI::write('=== Done ===', 'yellow');
    }
}
