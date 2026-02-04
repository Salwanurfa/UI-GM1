<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PopulateNamaSampahFromMaster extends Migration
{
    public function up()
    {
        // Populate nama_sampah from master_harga_sampah for existing records
        // This is a safe, non-destructive migration
        
        $db = \Config\Database::connect();
        
        // Get all master harga sampah
        $masterHarga = $db->table('master_harga_sampah')
            ->where('status_aktif', 1)
            ->get()
            ->getResultArray();
        
        $updated = 0;
        
        foreach ($masterHarga as $master) {
            // Update waste_management records that match jenis_sampah
            $result = $db->table('waste_management')
                ->where('jenis_sampah', $master['jenis_sampah'])
                ->where('nama_sampah IS NULL')
                ->update([
                    'nama_sampah' => $master['nama_jenis'],
                    'nama_sampah_id' => $master['id']
                ]);
            
            $updated += $db->affectedRows();
        }
        
        // For records without match, use jenis_sampah as nama_sampah
        $db->table('waste_management')
            ->where('nama_sampah IS NULL')
            ->update(['nama_sampah' => $db->raw('jenis_sampah')]);
        
        $updated += $db->affectedRows();
        
        log_message('info', "Migration PopulateNamaSampahFromMaster: Updated {$updated} records");
    }

    public function down()
    {
        // Clear nama_sampah and nama_sampah_id
        $db = \Config\Database::connect();
        $db->table('waste_management')->update([
            'nama_sampah' => null,
            'nama_sampah_id' => null
        ]);
        
        log_message('info', 'Migration PopulateNamaSampahFromMaster: Rolled back');
    }
}
