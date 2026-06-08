<?php
/**
 * DATABASE CHECKER - JALANKAN VIA BROWSER
 * URL: http://localhost/polban_greenmetric/check_db.php
 */

// Database config
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'polban_greenmetric';

// Connect
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h1>DATABASE STRUCTURE CHECK</h1>";
echo "<hr>";

// Check if columns exist
echo "<h2>1. Checking Columns in transport_stats</h2>";
$query = "SHOW COLUMNS FROM transport_stats WHERE Field IN ('kategori_sederhana', 'status_kendaraan', 'kategori_kendaraan', 'jenis_bahan_bakar')";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    
    $hasKategoriSederhana = false;
    $hasStatusKendaraan = false;
    
    while($row = $result->fetch_assoc()) {
        $highlight = '';
        if ($row['Field'] == 'kategori_sederhana') {
            $hasKategoriSederhana = true;
            $highlight = 'style="background-color: #90EE90;"';
        }
        if ($row['Field'] == 'status_kendaraan') {
            $hasStatusKendaraan = true;
            $highlight = 'style="background-color: #90EE90;"';
        }
        
        echo "<tr $highlight>";
        echo "<td><strong>" . $row['Field'] . "</strong></td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . ($row['Default'] ?: 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>Result:</h3>";
    echo "<p>" . ($hasKategoriSederhana ? "✅" : "❌") . " <strong>kategori_sederhana</strong> column " . ($hasKategoriSederhana ? "EXISTS" : "NOT FOUND") . "</p>";
    echo "<p>" . ($hasStatusKendaraan ? "✅" : "❌") . " <strong>status_kendaraan</strong> column " . ($hasStatusKendaraan ? "EXISTS" : "NOT FOUND") . "</p>";
    
    if (!$hasKategoriSederhana || !$hasStatusKendaraan) {
        echo "<div style='background: #ffcccc; padding: 20px; margin: 20px 0; border-left: 5px solid red;'>";
        echo "<h3>❌ MISSING COLUMNS DETECTED!</h3>";
        echo "<p><strong>This is why the form fails!</strong></p>";
        echo "<p>Run this SQL in phpMyAdmin:</p>";
        echo "<pre style='background: #f4f4f4; padding: 10px;'>";
        
        if (!$hasKategoriSederhana) {
            echo "ALTER TABLE transport_stats \n";
            echo "ADD COLUMN kategori_sederhana ENUM('Roda Dua', 'Roda Empat', 'Fasilitas Kampus') \n";
            echo "NULL DEFAULT NULL \n";
            echo "AFTER kategori_kendaraan;\n\n";
        }
        
        if (!$hasStatusKendaraan) {
            echo "ALTER TABLE transport_stats \n";
            echo "ADD COLUMN status_kendaraan ENUM(\n";
            echo "    'Milik Universitas',\n";
            echo "    'Milik Pribadi',\n";
            echo "    'Kendaraan Sewa',\n";
            echo "    'Kendaraan Umum'\n";
            echo ") NULL DEFAULT NULL \n";
            echo "AFTER kategori_sederhana;\n";
        }
        
        echo "</pre>";
        echo "</div>";
    } else {
        echo "<div style='background: #ccffcc; padding: 20px; margin: 20px 0; border-left: 5px solid green;'>";
        echo "<h3>✅ ALL COLUMNS EXIST!</h3>";
        echo "<p>Database structure is correct. The problem might be elsewhere.</p>";
        echo "</div>";
    }
} else {
    echo "<p style='color: red;'>❌ No columns found or table doesn't exist!</p>";
}

// Check Model file
echo "<hr>";
echo "<h2>2. Checking Model File</h2>";
$modelFile = '../app/Models/TransportStatsModel.php';
if (file_exists($modelFile)) {
    $modelContent = file_get_contents($modelFile);
    
    $hasStatusInModel = strpos($modelContent, "'status_kendaraan'") !== false;
    $hasKategoriInModel = strpos($modelContent, "'kategori_sederhana'") !== false;
    
    echo "<p>" . ($hasStatusInModel ? "✅" : "❌") . " <strong>status_kendaraan</strong> in Model \$allowedFields: " . ($hasStatusInModel ? "YES" : "NO") . "</p>";
    echo "<p>" . ($hasKategoriInModel ? "✅" : "❌") . " <strong>kategori_sederhana</strong> in Model \$allowedFields: " . ($hasKategoriInModel ? "YES" : "NO") . "</p>";
    
    if (!$hasStatusInModel || !$hasKategoriInModel) {
        echo "<div style='background: #ffcccc; padding: 20px; margin: 20px 0; border-left: 5px solid red;'>";
        echo "<h3>❌ FIELDS NOT IN MODEL!</h3>";
        echo "<p>Add these fields to \$allowedFields in app/Models/TransportStatsModel.php:</p>";
        echo "<pre style='background: #f4f4f4; padding: 10px;'>";
        if (!$hasKategoriInModel) echo "'kategori_sederhana',\n";
        if (!$hasStatusInModel) echo "'status_kendaraan',\n";
        echo "</pre>";
        echo "</div>";
    } else {
        echo "<div style='background: #ccffcc; padding: 20px; margin: 20px 0; border-left: 5px solid green;'>";
        echo "<h3>✅ MODEL IS CORRECT!</h3>";
        echo "<p>Both fields are in \$allowedFields.</p>";
        echo "</div>";
    }
} else {
    echo "<p style='color: red;'>❌ Model file not found!</p>";
}

// Check recent errors
echo "<hr>";
echo "<h2>3. Recent Error Logs</h2>";
$logFiles = glob('../writable/logs/*.log');
if (!empty($logFiles)) {
    $latestLog = end($logFiles);
    $logContent = file_get_contents($latestLog);
    $lines = explode("\n", $logContent);
    $errorLines = array_filter($lines, function($line) {
        return strpos($line, 'ERROR') !== false && 
               (strpos($line, 'Transport') !== false || 
                strpos($line, 'Unknown column') !== false);
    });
    
    if (!empty($errorLines)) {
        echo "<pre style='background: #f4f4f4; padding: 10px; max-height: 300px; overflow: auto;'>";
        echo implode("\n", array_slice($errorLines, -10));
        echo "</pre>";
    } else {
        echo "<p>No recent transport-related errors found.</p>";
    }
} else {
    echo "<p>No log files found.</p>";
}

// Summary
echo "<hr>";
echo "<h2>4. SUMMARY & ACTION REQUIRED</h2>";

$allGood = ($hasKategoriSederhana ?? false) && ($hasStatusKendaraan ?? false) && 
           ($hasStatusInModel ?? false) && ($hasKategoriInModel ?? false);

if ($allGood) {
    echo "<div style='background: #ccffcc; padding: 20px; border-left: 5px solid green;'>";
    echo "<h3>✅ ALL CHECKS PASSED!</h3>";
    echo "<p>Database and Model are correctly configured.</p>";
    echo "<p>If form still fails, check:</p>";
    echo "<ul>";
    echo "<li>Browser console for JavaScript errors</li>";
    echo "<li>Network tab for AJAX errors</li>";
    echo "<li>Form validation rules in Controller</li>";
    echo "</ul>";
    echo "</div>";
} else {
    echo "<div style='background: #ffcccc; padding: 20px; border-left: 5px solid red;'>";
    echo "<h3>❌ ISSUES FOUND!</h3>";
    echo "<p><strong>ACTION REQUIRED:</strong></p>";
    echo "<ol>";
    if (!($hasKategoriSederhana ?? false) || !($hasStatusKendaraan ?? false)) {
        echo "<li>Run the SQL script above in phpMyAdmin to add missing columns</li>";
    }
    if (!($hasStatusInModel ?? false) || !($hasKategoriInModel ?? false)) {
        echo "<li>Add missing fields to Model \$allowedFields</li>";
    }
    echo "<li>Test the form again after fixes</li>";
    echo "</ol>";
    echo "</div>";
}

$conn->close();

echo "<hr>";
echo "<p><strong>DELETE THIS FILE AFTER USE!</strong></p>";
echo "<p>File location: " . __FILE__ . "</p>";
?>
