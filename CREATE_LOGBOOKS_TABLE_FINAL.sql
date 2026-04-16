-- SQL Script untuk membuat tabel logbooks
-- Jalankan di phpMyAdmin pada database uigm_polban

-- Hapus tabel jika sudah ada (untuk memastikan struktur bersih)
DROP TABLE IF EXISTS logbooks;

-- Buat tabel logbooks dengan struktur yang benar
CREATE TABLE logbooks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kategori ENUM('3R', 'B3', 'Cair') NOT NULL,
    tanggal DATE NOT NULL,
    sumber_sampah VARCHAR(255),
    jenis_material VARCHAR(255),
    berat_terkumpul DECIMAL(10,2) DEFAULT 0.00,
    tindakan VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insert data sample untuk testing (opsional)
INSERT INTO logbooks (kategori, tanggal, sumber_sampah, jenis_material, berat_terkumpul, tindakan) VALUES
('3R', '2026-04-15', 'Kantin Utama', 'Plastik', 2.50, 'Didaur ulang'),
('3R', '2026-04-14', 'Gedung A', 'Kertas', 1.20, 'Dijual ke Bank Sampah');

-- Pesan konfirmasi
SELECT 'Tabel logbooks berhasil dibuat dengan data sample!' AS status;