-- Create bukti_dukung table
CREATE TABLE IF NOT EXISTS `bukti_dukung` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `judul` VARCHAR(255) NOT NULL,
    `periode` VARCHAR(20) NOT NULL,
    `nama_file` VARCHAR(255) NOT NULL,
    `ukuran_file` INT(11) NOT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;