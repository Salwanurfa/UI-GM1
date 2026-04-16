-- Create bukti_dukung table for uigm_polban database
USE uigm_polban;

CREATE TABLE IF NOT EXISTS bukti_dukung (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    periode VARCHAR(50),
    nama_file VARCHAR(255) NOT NULL,
    ukuran_file VARCHAR(50),
    tipe_file VARCHAR(50),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;