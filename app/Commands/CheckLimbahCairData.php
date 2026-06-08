<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckLimbahCairData extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:check-limbah-cair-data';
    protected $description = 'Check limbah_cair table data';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        CLI::write('=== Checking limbah_cair table data ===', 'yellow');
        CLI::newLine();
        
        // Count total records
        $query = $db->query("SELECT COUNT(*) as total FROM limbah_cair");
        $result = $query->getRow();
        CLI::write('Total records: ' . $result->total, 'green');
        
        // Get latest 5 records
        $query = $db->query("SELECT id, id_user, nama_limbah, timbulan, satuan, lokasi, status, tanggal_input FROM limbah_cair ORDER BY id DESC LIMIT 5");
        $records = $query->getResultArray();
        
        if (!empty($records)) {
            CLI::newLine();
            CLI::write('Latest 5 records:', 'green');
            foreach ($records as $record) {
                CLI::write('  ID: ' . $record['id']);
                CLI::write('    User ID: ' . $record['id_user']);
                CLI::write('    Nama Limbah: ' . $record['nama_limbah']);
                CLI::write('    Timbulan: ' . $record['timbulan'] . ' ' . $record['satuan']);
                CLI::write('    Lokasi: ' . $record['lokasi']);
                CLI::write('    Status: ' . $record['status']);
                CLI::write('    Tanggal: ' . $record['tanggal_input']);
                CLI::newLine();
            }
        } else {
            CLI::newLine();
            CLI::write('No records found in the table.', 'yellow');
        }
        
        // Count by status
        $query = $db->query("SELECT status, COUNT(*) as count FROM limbah_cair GROUP BY status");
        $statusCounts = $query->getResultArray();
        
        if (!empty($statusCounts)) {
            CLI::newLine();
            CLI::write('Records by status:', 'green');
            foreach ($statusCounts as $status) {
                CLI::write('  ' . $status['status'] . ': ' . $status['count']);
            }
        }
        
        CLI::newLine();
        CLI::write('=== Done ===', 'yellow');
    }
}
