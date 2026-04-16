-- Add kemasan column to master_limbah_b3 table
USE uigm_polban;

-- Add kemasan column if it doesn't exist
ALTER TABLE master_limbah_b3 
ADD COLUMN IF NOT EXISTS kemasan VARCHAR(100) NULL COMMENT 'Default kemasan untuk auto-fill form' 
AFTER bentuk_fisik;

-- Update existing data with sample bentuk_fisik and kemasan values
UPDATE master_limbah_b3 SET 
    bentuk_fisik = CASE 
        WHEN nama_limbah LIKE '%Oli%' THEN 'Cair'
        WHEN nama_limbah LIKE '%Gemuk%' THEN 'Pasta'
        WHEN nama_limbah LIKE '%Kain%' THEN 'Padat'
        WHEN nama_limbah LIKE '%Karbon%' THEN 'Bubuk'
        WHEN nama_limbah LIKE '%Asam%' OR nama_limbah LIKE '%Basa%' THEN 'Cair'
        ELSE 'Padat'
    END,
    kemasan = CASE 
        WHEN nama_limbah LIKE '%Oli%' THEN 'Drum 200L'
        WHEN nama_limbah LIKE '%Gemuk%' THEN 'Drum 100L'
        WHEN nama_limbah LIKE '%Kain%' THEN 'Karung'
        WHEN nama_limbah LIKE '%Karbon%' THEN 'Karung'
        WHEN nama_limbah LIKE '%Asam%' OR nama_limbah LIKE '%Basa%' THEN 'Jerrycan 20L'
        ELSE 'Karung'
    END
WHERE bentuk_fisik IS NULL OR kemasan IS NULL;

-- Verify the update
SELECT id, nama_limbah, kode_limbah, kategori_bahaya, bentuk_fisik, kemasan 
FROM master_limbah_b3 
ORDER BY nama_limbah;