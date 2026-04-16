<?php
// Database connection
$db = new mysqli('localhost', 'root', '', 'uigm_polban');
if ($db->connect_error) {
    die('Connection failed: ' . $db->connect_error);
}

echo "=== CLEANUP UNKNOWN USERS SCRIPT ===\n\n";

// Step 1: Identify orphaned waste records
echo "1. IDENTIFYING ORPHANED WASTE RECORDS:\n";
$orphanQuery = "
    SELECT 
        w.id,
        w.unit_id,
        w.user_id,
        w.jenis_sampah,
        w.nama_sampah,
        w.berat_kg,
        w.tanggal,
        w.status,
        u.nama_lengkap,
        unit.nama_unit
    FROM waste_management w
    LEFT JOIN users u ON w.user_id = u.id
    LEFT JOIN unit ON w.unit_id = unit.id
    WHERE w.status IN ('disetujui', 'disetujui_tps', 'approved')
    AND (w.user_id IS NULL OR u.id IS NULL)
    ORDER BY w.tanggal DESC
";

$orphans = $db->query($orphanQuery);
$orphanCount = $orphans->num_rows;

echo "   Found $orphanCount orphaned records (no valid user):\n";

if ($orphanCount > 0) {
    $orphanIds = [];
    while ($orphan = $orphans->fetch_assoc()) {
        echo "   - ID {$orphan['id']}: Unit {$orphan['nama_unit']} - {$orphan['jenis_sampah']} ({$orphan['berat_kg']} kg) - User ID: {$orphan['user_id']}\n";
        $orphanIds[] = $orphan['id'];
    }
    
    // Step 2: Delete orphaned records
    echo "\n2. DELETING ORPHANED RECORDS:\n";
    $deletedCount = 0;
    
    foreach ($orphanIds as $orphanId) {
        $deleteQuery = "DELETE FROM waste_management WHERE id = ?";
        $stmt = $db->prepare($deleteQuery);
        $stmt->bind_param('i', $orphanId);
        
        if ($stmt->execute()) {
            $deletedCount++;
            echo "   ✓ Deleted orphaned record ID: $orphanId\n";
        } else {
            echo "   ✗ Failed to delete record ID: $orphanId - " . $stmt->error . "\n";
        }
        $stmt->close();
    }
    
    echo "\n   Successfully deleted $deletedCount orphaned records\n";
} else {
    echo "   No orphaned records found!\n";
}

// Step 3: Check for records with invalid unit_id
echo "\n3. CHECKING FOR INVALID UNIT REFERENCES:\n";
$invalidUnitQuery = "
    SELECT 
        w.id,
        w.unit_id,
        w.user_id,
        w.jenis_sampah,
        w.berat_kg,
        w.tanggal,
        u.nama_lengkap
    FROM waste_management w
    LEFT JOIN unit ON w.unit_id = unit.id
    LEFT JOIN users u ON w.user_id = u.id
    WHERE w.status IN ('disetujui', 'disetujui_tps', 'approved')
    AND (w.unit_id IS NULL OR unit.id IS NULL)
    ORDER BY w.tanggal DESC
";

$invalidUnits = $db->query($invalidUnitQuery);
$invalidUnitCount = $invalidUnits->num_rows;

echo "   Found $invalidUnitCount records with invalid unit references:\n";

if ($invalidUnitCount > 0) {
    $invalidUnitIds = [];
    while ($invalid = $invalidUnits->fetch_assoc()) {
        echo "   - ID {$invalid['id']}: Unit ID {$invalid['unit_id']} - {$invalid['jenis_sampah']} ({$invalid['berat_kg']} kg) - User: {$invalid['nama_lengkap']}\n";
        $invalidUnitIds[] = $invalid['id'];
    }
    
    // Delete invalid unit records
    echo "\n4. DELETING INVALID UNIT RECORDS:\n";
    $deletedUnitCount = 0;
    
    foreach ($invalidUnitIds as $invalidId) {
        $deleteQuery = "DELETE FROM waste_management WHERE id = ?";
        $stmt = $db->prepare($deleteQuery);
        $stmt->bind_param('i', $invalidId);
        
        if ($stmt->execute()) {
            $deletedUnitCount++;
            echo "   ✓ Deleted invalid unit record ID: $invalidId\n";
        } else {
            echo "   ✗ Failed to delete record ID: $invalidId - " . $stmt->error . "\n";
        }
        $stmt->close();
    }
    
    echo "\n   Successfully deleted $deletedUnitCount invalid unit records\n";
} else {
    echo "   No invalid unit references found!\n";
}

// Step 4: Final verification - show remaining valid data
echo "\n5. FINAL VERIFICATION - REMAINING VALID DATA:\n";
$validQuery = "
    SELECT 
        COUNT(*) as total_records,
        COUNT(DISTINCT w.unit_id) as unique_units,
        COUNT(DISTINCT w.user_id) as unique_users
    FROM waste_management w
    INNER JOIN users u ON w.user_id = u.id
    INNER JOIN unit ON w.unit_id = unit.id
    WHERE w.status IN ('disetujui', 'disetujui_tps', 'approved')
";

$valid = $db->query($validQuery);
$validStats = $valid->fetch_assoc();

echo "   Total valid records: {$validStats['total_records']}\n";
echo "   Unique units: {$validStats['unique_units']}\n";
echo "   Unique users: {$validStats['unique_users']}\n";

// Step 5: Show summary by unit with user names
echo "\n6. VALID DATA SUMMARY BY UNIT:\n";
$summaryQuery = "
    SELECT 
        unit.nama_unit,
        u.nama_lengkap,
        u.role,
        COUNT(*) as record_count,
        GROUP_CONCAT(DISTINCT w.jenis_sampah) as waste_types
    FROM waste_management w
    INNER JOIN users u ON w.user_id = u.id
    INNER JOIN unit ON w.unit_id = unit.id
    WHERE w.status IN ('disetujui', 'disetujui_tps', 'approved')
    GROUP BY w.unit_id, w.user_id, unit.nama_unit, u.nama_lengkap, u.role
    ORDER BY record_count DESC
";

$summary = $db->query($summaryQuery);
while ($sum = $summary->fetch_assoc()) {
    echo "   Unit: {$sum['nama_unit']}\n";
    echo "     User: {$sum['nama_lengkap']} (Role: {$sum['role']})\n";
    echo "     Records: {$sum['record_count']}\n";
    echo "     Waste Types: {$sum['waste_types']}\n\n";
}

$db->close();
echo "=== CLEANUP COMPLETED ===\n";
?>