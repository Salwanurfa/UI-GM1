<?php
echo "=== SETUP BUKTI DUKUNG COMPLETE ===\n\n";

// 1. Create upload directory
echo "1. Creating upload directory...\n";
$uploadDir = 'public/uploads/bukti_dukung/';

if (!is_dir($uploadDir)) {
    if (mkdir($uploadDir, 0755, true)) {
        echo "   ✓ Directory created: $uploadDir\n";
    } else {
        echo "   ✗ Failed to create directory: $uploadDir\n";
    }
} else {
    echo "   ✓ Directory already exists: $uploadDir\n";
}

// Set permissions
if (is_dir($uploadDir)) {
    chmod($uploadDir, 0755);
    echo "   ✓ Permissions set to 755\n";
}

// 2. Create .htaccess for security
echo "\n2. Creating security .htaccess...\n";
$htaccessContent = "# Prevent direct access to uploaded files\n";
$htaccessContent .= "Options -Indexes\n";
$htaccessContent .= "# Allow common file types\n";
$htaccessContent .= "<FilesMatch \"\\.(pdf|doc|docx|xls|xlsx|jpg|jpeg|png)$\">\n";
$htaccessContent .= "    Order Allow,Deny\n";
$htaccessContent .= "    Allow from all\n";
$htaccessContent .= "</FilesMatch>\n";

if (file_put_contents($uploadDir . '.htaccess', $htaccessContent)) {
    echo "   ✓ Security .htaccess created\n";
} else {
    echo "   ✗ Failed to create .htaccess\n";
}

// 3. Check database configuration
echo "\n3. Checking database configuration...\n";
if (file_exists('app/Config/Database.php')) {
    echo "   ✓ Database config found\n";
    
    // Read database config to show current database
    $dbConfig = file_get_contents('app/Config/Database.php');
    if (preg_match("/'database'\s*=>\s*'([^']+)'/", $dbConfig, $matches)) {
        $currentDb = $matches[1];
        echo "   ℹ Current database: $currentDb\n";
        
        if ($currentDb !== 'uigm_polban') {
            echo "   ⚠ WARNING: Database should be 'uigm_polban' but is '$currentDb'\n";
            echo "   → Please update app/Config/Database.php to use 'uigm_polban'\n";
        } else {
            echo "   ✓ Database correctly set to 'uigm_polban'\n";
        }
    }
} else {
    echo "   ✗ Database config not found\n";
}

echo "\n=== NEXT STEPS ===\n";
echo "1. Run this SQL in phpMyAdmin for database 'uigm_polban':\n";
echo "   → Open: create_bukti_dukung_uigm_polban.sql\n";
echo "   → Copy and paste the SQL into phpMyAdmin\n\n";

echo "2. Test the functionality:\n";
echo "   → Login as admin_pusat or super_admin\n";
echo "   → Go to /admin-pusat/bukti-dukung\n";
echo "   → Try uploading a file\n\n";

echo "3. If you get database connection errors:\n";
echo "   → Check app/Config/Database.php\n";
echo "   → Make sure database is set to 'uigm_polban'\n";
echo "   → Verify MySQL credentials\n\n";

echo "Setup completed! 🎉\n";
?>