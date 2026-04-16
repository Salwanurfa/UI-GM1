<?php

// Script untuk menjalankan migration secara manual
// Jalankan dengan: php run_migration.php

require_once 'vendor/autoload.php';

// Load CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();

// Load migration service
$migrate = \Config\Services::migrations();

try {
    // Jalankan migration
    $migrate->latest();
    echo "✅ Migration berhasil dijalankan!\n";
    echo "Tabel logbooks sudah dibuat di database.\n";
} catch (Exception $e) {
    echo "❌ Error migration: " . $e->getMessage() . "\n";
    echo "\nSilakan gunakan SQL script di phpMyAdmin:\n";
    echo "File: CREATE_LOGBOOKS_TABLE_FINAL.sql\n";
}