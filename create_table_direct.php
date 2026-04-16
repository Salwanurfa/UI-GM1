<?php

// Script untuk membuat tabel logbooks secara langsung
// Jalankan dengan: php create_table_direct.php

try {
    // Konfigurasi database (sesuaikan dengan .env Anda)
    $host = 'localhost';
    $dbname = 'uigm_polban';
    $username = 'root';  // Sesuaikan dengan username database Anda
    $password = '';      // Sesuaikan dengan password database Anda
    
    // Koneksi ke database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // SQL untuk membuat tabel
    $sql = "
    CREATE TABLE IF NOT EXISTS logbooks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        kategori ENUM('3R', 'B3', 'Cair') NOT NULL,
        tanggal DATE NOT NULL,
        sumber_sampah VARCHAR(255),
        jenis_material VARCHAR(255),
        berat_terkumpul DECIMAL(10,2) DEFAULT 0.00,
        tindakan VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;
    ";
    
    // Eksekusi SQL
    $pdo->exec($sql);
    
    // Insert data sample
    $sampleData = "
    INSERT INTO logbooks (kategori, tanggal, sumber_sampah, jenis_material, berat_terkumpul, tindakan) VALUES
    ('3R', '2026-04-15', 'Kantin Utama', 'Plastik', 2.50, 'Didaur ulang'),
    ('3R', '2026-04-14', 'Gedung A', 'Kertas', 1.20, 'Dijual ke Bank Sampah');
    ";
    
    $pdo->exec($sampleData);
    
    echo "✅ BERHASIL!\n";
    echo "Tabel logbooks sudah dibuat di database uigm_polban\n";
    echo "Data sample sudah ditambahkan\n";
    echo "Silakan refresh halaman logbook di browser\n";
    
} catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "\nSilakan gunakan phpMyAdmin dengan script SQL:\n";
    echo "File: CREATE_LOGBOOKS_TABLE_FINAL.sql\n";
}