<?php
// Create the required upload directory for bukti_dukung

$uploadDir = 'public/uploads/bukti_dukung/';

if (!is_dir($uploadDir)) {
    if (mkdir($uploadDir, 0755, true)) {
        echo "✓ Directory created successfully: $uploadDir\n";
    } else {
        echo "✗ Failed to create directory: $uploadDir\n";
    }
} else {
    echo "✓ Directory already exists: $uploadDir\n";
}

// Set proper permissions
if (is_dir($uploadDir)) {
    chmod($uploadDir, 0755);
    echo "✓ Permissions set to 755 for: $uploadDir\n";
}

// Create .htaccess for security
$htaccessContent = "# Prevent direct access to uploaded files\n";
$htaccessContent .= "Options -Indexes\n";
$htaccessContent .= "# Allow common file types\n";
$htaccessContent .= "<FilesMatch \"\\.(pdf|doc|docx|xls|xlsx|jpg|jpeg|png)$\">\n";
$htaccessContent .= "    Order Allow,Deny\n";
$htaccessContent .= "    Allow from all\n";
$htaccessContent .= "</FilesMatch>\n";

file_put_contents($uploadDir . '.htaccess', $htaccessContent);
echo "✓ Security .htaccess created\n";

echo "\nUpload directory setup completed!\n";
?>