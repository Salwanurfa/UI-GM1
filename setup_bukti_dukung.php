<?php
// Setup script for Bukti Dukung functionality

// Create uploads directory
$uploadDir = 'uploads/bukti_dukung/';
if (!is_dir($uploadDir)) {
    if (mkdir($uploadDir, 0755, true)) {
        echo "✓ Directory created: $uploadDir\n";
    } else {
        echo "✗ Failed to create directory: $uploadDir\n";
    }
} else {
    echo "✓ Directory already exists: $uploadDir\n";
}

// Set proper permissions
if (is_dir($uploadDir)) {
    chmod($uploadDir, 0755);
    echo "✓ Permissions set for: $uploadDir\n";
}

echo "\nSetup completed!\n";
echo "Next steps:\n";
echo "1. Run the SQL script to create the bukti_dukung table\n";
echo "2. Test the CRUD functionality\n";
?>