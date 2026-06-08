<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckLimbahCairStructure extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:check-limbah-cair';
    protected $description = 'Check limbah_cair table structure';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        CLI::write('=== Checking limbah_cair table structure ===', 'yellow');
        CLI::newLine();
        
        // Get current fields
        $fields = $db->getFieldNames('limbah_cair');
        CLI::write('Current fields:', 'green');
        foreach ($fields as $field) {
            CLI::write('  - ' . $field);
        }
        
        // Check if new fields exist
        $newFields = ['nama_limbah', 'kode_limbah', 'tingkat_bahaya', 'karakteristik', 'pengolahan', 'timbulan', 'bentuk_fisik', 'kemasan', 'ph', 'bod', 'cod', 'tss'];
        
        CLI::newLine();
        CLI::write('Checking new fields:', 'green');
        $missingFields = [];
        foreach ($newFields as $field) {
            $exists = in_array($field, $fields);
            if ($exists) {
                CLI::write('  ✓ ' . $field . ': EXISTS', 'green');
            } else {
                CLI::write('  ✗ ' . $field . ': MISSING', 'red');
                $missingFields[] = $field;
            }
        }
        
        // Check if old fields exist
        $oldFields = ['jenis_limbah', 'volume'];
        CLI::newLine();
        CLI::write('Checking old fields (should be removed):', 'green');
        $oldFieldsExist = [];
        foreach ($oldFields as $field) {
            $exists = in_array($field, $fields);
            if ($exists) {
                CLI::write('  ✗ ' . $field . ': EXISTS (needs to be removed)', 'red');
                $oldFieldsExist[] = $field;
            } else {
                CLI::write('  ✓ ' . $field . ': NOT EXISTS (good)', 'green');
            }
        }
        
        CLI::newLine();
        if (empty($missingFields) && empty($oldFieldsExist)) {
            CLI::write('✓ Table structure is correct!', 'green');
        } else {
            CLI::write('✗ Table structure needs update!', 'red');
            if (!empty($missingFields)) {
                CLI::write('Missing fields: ' . implode(', ', $missingFields), 'red');
            }
            if (!empty($oldFieldsExist)) {
                CLI::write('Old fields to remove: ' . implode(', ', $oldFieldsExist), 'red');
            }
        }
        
        CLI::newLine();
        CLI::write('=== Done ===', 'yellow');
    }
}
