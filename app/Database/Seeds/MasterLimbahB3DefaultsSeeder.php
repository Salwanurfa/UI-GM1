<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MasterLimbahB3DefaultsSeeder extends Seeder
{
    public function run()
    {
        // Data mapping untuk default satuan dan kemasan
        $defaultMappings = [
            // Oli dan Pelumas
            'Oli' => ['satuan' => 'liter', 'kemasan' => 'Jerrycan 20L'],
            'Oil' => ['satuan' => 'liter', 'kemasan' => 'Jerrycan 20L'],
            'Grease' => ['satuan' => 'kg', 'kemasan' => 'Drum 100L'],
            'Pelumas' => ['satuan' => 'liter', 'kemasan' => 'Jerrycan 20L'],
            
            // Kain dan Material Bekas
            'Used Rags' => ['satuan' => 'kg', 'kemasan' => 'Karung'],
            'Kain Majun' => ['satuan' => 'kg', 'kemasan' => 'Karung'],
            'Majun' => ['satuan' => 'kg', 'kemasan' => 'Karung'],
            
            // Karbon dan Filter
            'Karbon Aktif' => ['satuan' => 'kg', 'kemasan' => 'Karung'],
            'Carbon' => ['satuan' => 'kg', 'kemasan' => 'Karung'],
            'Filter' => ['satuan' => 'pcs', 'kemasan' => 'Karung'],
            
            // Limbah Asam dan Basa
            'Limbah Asam' => ['satuan' => 'liter', 'kemasan' => 'Drum 200L'],
            'Asam' => ['satuan' => 'liter', 'kemasan' => 'Jerrycan 5L'],
            'Limbah Basa' => ['satuan' => 'liter', 'kemasan' => 'Drum 200L'],
            'Basa' => ['satuan' => 'liter', 'kemasan' => 'Jerrycan 5L'],
            'Acid' => ['satuan' => 'liter', 'kemasan' => 'Jerrycan 5L'],
            'Base' => ['satuan' => 'liter', 'kemasan' => 'Jerrycan 5L'],
            
            // Sludge dan Lumpur
            'Limbah Organik' => ['satuan' => 'kg', 'kemasan' => 'Drum 200L'],
            'Sludge' => ['satuan' => 'kg', 'kemasan' => 'Drum 200L'],
            'Lumpur' => ['satuan' => 'kg', 'kemasan' => 'Drum 200L'],
            
            // Lab Pack
            'Lab-Pack' => ['satuan' => 'liter', 'kemasan' => 'Drum 100L'],
            'Lab Pack' => ['satuan' => 'liter', 'kemasan' => 'Drum 100L'],
            'Labpack' => ['satuan' => 'liter', 'kemasan' => 'Drum 100L'],
            
            // Lampu dan Elektronik
            'Lampu TL' => ['satuan' => 'pcs', 'kemasan' => 'Karung'],
            'Lampu' => ['satuan' => 'pcs', 'kemasan' => 'Karung'],
            'TL' => ['satuan' => 'pcs', 'kemasan' => 'Karung'],
            'Fluorescent' => ['satuan' => 'pcs', 'kemasan' => 'Karung'],
            'Limbah Elektronik' => ['satuan' => 'kg', 'kemasan' => 'Karung'],
            'Elektronik' => ['satuan' => 'kg', 'kemasan' => 'Karung'],
            'Electronic' => ['satuan' => 'kg', 'kemasan' => 'Karung'],
            'Baterai' => ['satuan' => 'pcs', 'kemasan' => 'Karung'],
            'Battery' => ['satuan' => 'pcs', 'kemasan' => 'Karung'],
            'Aki' => ['satuan' => 'pcs', 'kemasan' => 'Karung'],
            
            // Kimia Laboratorium
            'Methanol' => ['satuan' => 'liter', 'kemasan' => 'Jerrycan 20L'],
            'Ethanol' => ['satuan' => 'liter', 'kemasan' => 'Jerrycan 20L'],
            'Acetone' => ['satuan' => 'liter', 'kemasan' => 'Jerrycan 20L'],
            'Formalin' => ['satuan' => 'liter', 'kemasan' => 'Jerrycan 5L'],
            'Formaldehyde' => ['satuan' => 'liter', 'kemasan' => 'Jerrycan 5L'],
            
            // Cat dan Pelarut
            'Cat' => ['satuan' => 'liter', 'kemasan' => 'Drum 100L'],
            'Paint' => ['satuan' => 'liter', 'kemasan' => 'Drum 100L'],
            'Thinner' => ['satuan' => 'liter', 'kemasan' => 'Jerrycan 20L'],
            'Pelarut' => ['satuan' => 'liter', 'kemasan' => 'Jerrycan 20L'],
            'Solvent' => ['satuan' => 'liter', 'kemasan' => 'Jerrycan 20L'],
            'Tinta' => ['satuan' => 'liter', 'kemasan' => 'Botol'],
            'Ink' => ['satuan' => 'liter', 'kemasan' => 'Botol'],
            
            // Medis
            'Limbah Medis' => ['satuan' => 'kg', 'kemasan' => 'Karung'],
            'Medical' => ['satuan' => 'kg', 'kemasan' => 'Karung'],
            'Jarum' => ['satuan' => 'pcs', 'kemasan' => 'Karung'],
            'Syringe' => ['satuan' => 'pcs', 'kemasan' => 'Karung'],
            'Obat' => ['satuan' => 'kg', 'kemasan' => 'Karung'],
            'Medicine' => ['satuan' => 'kg', 'kemasan' => 'Karung']
        ];

        // Get all existing master limbah B3 data
        $db = \Config\Database::connect();
        $masterData = $db->table('master_limbah_b3')->get()->getResultArray();

        foreach ($masterData as $master) {
            $namaLimbah = $master['nama_limbah'];
            $defaultSatuan = null;
            $defaultKemasan = null;

            // Find matching default values based on nama_limbah (case-insensitive, partial match)
            foreach ($defaultMappings as $keyword => $defaults) {
                if (stripos($namaLimbah, $keyword) !== false) {
                    $defaultSatuan = $defaults['satuan'];
                    $defaultKemasan = $defaults['kemasan'];
                    break; // Use first match
                }
            }

            // Update the record if we found defaults
            if ($defaultSatuan && $defaultKemasan) {
                $db->table('master_limbah_b3')
                   ->where('id', $master['id'])
                   ->update([
                       'default_satuan' => $defaultSatuan,
                       'default_kemasan' => $defaultKemasan
                   ]);

                echo "Updated: {$namaLimbah} -> Satuan: {$defaultSatuan}, Kemasan: {$defaultKemasan}\n";
            } else {
                // Set generic defaults for unmatched items
                $genericDefaults = $this->getGenericDefaults($namaLimbah);
                $db->table('master_limbah_b3')
                   ->where('id', $master['id'])
                   ->update([
                       'default_satuan' => $genericDefaults['satuan'],
                       'default_kemasan' => $genericDefaults['kemasan']
                   ]);

                echo "Generic defaults for: {$namaLimbah} -> Satuan: {$genericDefaults['satuan']}, Kemasan: {$genericDefaults['kemasan']}\n";
            }
        }

        echo "Seeder completed successfully!\n";
    }

    /**
     * Get generic defaults based on common patterns
     */
    private function getGenericDefaults($namaLimbah)
    {
        $namaLower = strtolower($namaLimbah);

        // Liquid patterns
        if (strpos($namaLower, 'liquid') !== false || 
            strpos($namaLower, 'cair') !== false ||
            strpos($namaLower, 'solution') !== false ||
            strpos($namaLower, 'larutan') !== false) {
            return ['satuan' => 'liter', 'kemasan' => 'Jerrycan 20L'];
        }

        // Solid patterns
        if (strpos($namaLower, 'solid') !== false || 
            strpos($namaLower, 'padat') !== false ||
            strpos($namaLower, 'powder') !== false ||
            strpos($namaLower, 'serbuk') !== false) {
            return ['satuan' => 'kg', 'kemasan' => 'Karung'];
        }

        // Electronic/equipment patterns
        if (strpos($namaLower, 'electronic') !== false || 
            strpos($namaLower, 'equipment') !== false ||
            strpos($namaLower, 'device') !== false ||
            strpos($namaLower, 'alat') !== false) {
            return ['satuan' => 'pcs', 'kemasan' => 'Karung'];
        }

        // Default fallback
        return ['satuan' => 'kg', 'kemasan' => 'Karung'];
    }
}