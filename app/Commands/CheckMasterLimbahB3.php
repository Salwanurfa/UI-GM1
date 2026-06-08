<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckMasterLimbahB3 extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'check:master-limbah';
    protected $description = 'Check master_limbah_b3 table structure and data';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        CLI::write('=================================================================', 'yellow');
        CLI::write('STRUKTUR TABEL master_limbah_b3', 'yellow');
        CLI::write('=================================================================', 'yellow');
        CLI::newLine();

        // Get field names
        $fields = $db->getFieldNames('master_limbah_b3');
        CLI::write('KOLOM-KOLOM:', 'green');
        foreach ($fields as $field) {
            CLI::write('  - ' . $field);
        }

        CLI::newLine();

        // Get sample data
        $query = $db->query("SELECT * FROM master_limbah_b3 LIMIT 3");
        $results = $query->getResultArray();

        CLI::write('SAMPLE DATA (3 baris pertama):', 'green');
        CLI::write('-----------------------------------------------------------------');
        foreach ($results as $i => $row) {
            CLI::newLine();
            CLI::write('Baris ' . ($i + 1) . ':', 'cyan');
            foreach ($row as $key => $value) {
                CLI::write('  ' . $key . ': ' . ($value ?? 'NULL'));
            }
        }

        CLI::newLine();
        CLI::write('=================================================================', 'yellow');
        CLI::write('CHECKING limbah_b3 TABLE', 'yellow');
        CLI::write('=================================================================', 'yellow');
        CLI::newLine();

        // Check limbah_b3 data
        $query2 = $db->query("
            SELECT 
                lb.id,
                lb.master_b3_id,
                lb.lokasi,
                lb.timbulan,
                lb.satuan,
                lb.status,
                lb.tanggal_input,
                mlb.nama_limbah,
                mlb.kode_limbah,
                mlb.kategori_bahaya
            FROM limbah_b3 lb
            LEFT JOIN master_limbah_b3 mlb ON mlb.id = lb.master_b3_id
            ORDER BY lb.tanggal_input DESC
            LIMIT 5
        ");

        $limbahResults = $query2->getResultArray();

        CLI::write('DATA LIMBAH B3 DENGAN JOIN (5 baris terbaru):', 'green');
        CLI::write('-----------------------------------------------------------------');
        foreach ($limbahResults as $i => $row) {
            CLI::newLine();
            CLI::write('Baris ' . ($i + 1) . ':', 'cyan');
            CLI::write('  ID: ' . $row['id']);
            CLI::write('  master_b3_id: ' . $row['master_b3_id']);
            CLI::write('  nama_limbah: ' . ($row['nama_limbah'] ?? 'NULL/KOSONG'), $row['nama_limbah'] ? 'green' : 'red');
            CLI::write('  kode_limbah: ' . ($row['kode_limbah'] ?? 'NULL/KOSONG'), $row['kode_limbah'] ? 'green' : 'red');
            CLI::write('  kategori_bahaya: ' . ($row['kategori_bahaya'] ?? 'NULL/KOSONG'));
            CLI::write('  lokasi: ' . $row['lokasi']);
            CLI::write('  timbulan: ' . $row['timbulan'] . ' ' . $row['satuan']);
            CLI::write('  status: ' . $row['status']);
            CLI::write('  tanggal_input: ' . $row['tanggal_input']);
        }

        CLI::newLine();
        CLI::write('=================================================================', 'yellow');
    }
}
