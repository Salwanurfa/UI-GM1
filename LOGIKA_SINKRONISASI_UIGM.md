# Logika Sinkronisasi Data Otomatis - 7 Indikator UIGM

## Overview
Sistem sinkronisasi otomatis yang menghubungkan input data user dengan 7 Indikator UIGM (UI GreenMetric) secara real-time. Data yang diinput user akan otomatis terkelompokkan berdasarkan kategori indikator yang telah distandarisasi.

## 7 Indikator UIGM

### 1. Indikator 1: Program 3R (Reduce, Reuse, Recycle)
- **Deskripsi**: Kegiatan Reduce, Reuse, Recycle
- **Keywords**: reduce, reuse, recycle, daur ulang, pakai ulang, pengurangan
- **Jenis Sampah**: Kertas Daur Ulang, Plastik Daur Ulang, Botol Bekas Pakai Ulang

### 2. Indikator 2: Pengurangan Kertas & Plastik
- **Deskripsi**: Upaya mengurangi penggunaan kertas dan plastik
- **Keywords**: kertas, paper, plastik, plastic, keyboard, kemasan
- **Jenis Sampah**: Kertas, Plastik, Keyboard Bekas, Kemasan Plastik

### 3. Indikator 3: Limbah Organik
- **Deskripsi**: Limbah yang dapat terurai secara alami
- **Keywords**: organik, makanan, sisa, dedaunan, taman, kompos
- **Jenis Sampah**: Organik, Sisa Makanan, Dedaunan, Limbah Taman

### 4. Indikator 4: Limbah Anorganik
- **Deskripsi**: Limbah non-organik umum
- **Keywords**: anorganik, logam, kaca, metal, kaleng, aluminium
- **Jenis Sampah**: Anorganik, Logam, Kaca, Kaleng, Aluminium

### 5. Indikator 5: Limbah B3
- **Deskripsi**: Bahan Berbahaya dan Beracun
- **Keywords**: b3, oli, baterai, medis, kimia, beracun, berbahaya
- **Jenis Sampah**: B3, Oli Bekas, Baterai, Limbah Medis, Limbah Kimia

### 6. Indikator 6: Limbah Cair
- **Deskripsi**: Air limbah dan cairan buangan
- **Keywords**: cair, air, limbah cair, cairan, liquid
- **Jenis Sampah**: Limbah Cair, Air Limbah, Limbah Laboratorium Cair

### 7. Indikator 7: Persentase Daur Ulang (Calculated)
- **Deskripsi**: Persentase limbah yang dapat didaur ulang
- **Formula**: (Indikator 1 + 2 + 3) / Total Limbah Padat × 100%

## Logika Mapping Otomatis

### 1. Exact Match (Prioritas Tertinggi)
```php
// Cek kecocokan exact dengan jenis_sampah yang terdaftar
if (strtolower($jenisPattern) === strtolower($jenisSampah)) {
    return $indicatorKey;
}
```

### 2. Keyword Match
```php
// Cek keyword dalam jenis_sampah dan nama_sampah
foreach ($config['keywords'] as $keyword) {
    if (strpos($combinedText, strtolower($keyword)) !== false) {
        return $indicatorKey;
    }
}
```

### 3. Fallback Logic
```php
// Default mapping berdasarkan kategori umum
if (strpos($combinedText, 'organik') !== false) return 'indikator_3';
if (strpos($combinedText, 'plastik') !== false) return 'indikator_2';
if (strpos($combinedText, 'b3') !== false) return 'indikator_5';
if (strpos($combinedText, 'cair') !== false) return 'indikator_6';
// Default: indikator_4 (Anorganik)
```

## Contoh Sinkronisasi Data

### Case 1: User Input "Plastik Bekas"
- **Input**: jenis_sampah = "Plastik", nama_sampah = "Plastik Bekas"
- **Mapping Logic**: Keyword "plastik" ditemukan
- **Result**: Masuk ke **Indikator 2** (Pengurangan Kertas & Plastik)

### Case 2: User Input "Oli Bekas Motor"
- **Input**: jenis_sampah = "B3", nama_sampah = "Oli Bekas Motor"
- **Mapping Logic**: Keyword "oli" dan "b3" ditemukan
- **Result**: Masuk ke **Indikator 5** (Limbah B3)

### Case 3: User Input "Sisa Makanan Kantin"
- **Input**: jenis_sampah = "Organik", nama_sampah = "Sisa Makanan Kantin"
- **Mapping Logic**: Exact match "Organik" dan keyword "makanan"
- **Result**: Masuk ke **Indikator 3** (Limbah Organik)

## Integrasi Database

### Query Filter
```sql
-- Hanya data yang sudah disetujui TPS
WHERE wm.status IN ('disetujui', 'disetujui_tps')

-- Filter berdasarkan tahun
AND YEAR(wm.tanggal) = ?

-- Mapping berdasarkan jenis_sampah dan keywords
AND (wm.jenis_sampah LIKE '%keyword%' OR wm.nama_sampah LIKE '%keyword%')
```

### Konversi Satuan Otomatis
```sql
-- Konversi ke Kg
CASE 
    WHEN wm.satuan = 'kg' THEN wm.jumlah 
    WHEN wm.satuan = 'g' THEN wm.jumlah/1000 
    WHEN wm.satuan = 'ton' THEN wm.jumlah*1000 
    ELSE 0 
END as total_kg

-- Konversi ke Liter
CASE 
    WHEN wm.satuan = 'L' THEN wm.jumlah 
    WHEN wm.satuan = 'ml' THEN wm.jumlah/1000 
    ELSE 0 
END as total_l
```

## API Endpoints

### 1. Get UIGM Indicator Data
- **URL**: `/admin-pusat/indikator-uigm/get-uigm-indicator-data`
- **Method**: GET
- **Parameter**: `tahun` (optional)
- **Response**: Data 7 indikator UIGM dengan statistik

### 2. Get Detailed Recap Data
- **URL**: `/admin-pusat/indikator-uigm/get-detailed-recap-data`
- **Method**: GET
- **Parameter**: `tahun` (optional)
- **Response**: Data detail per user/unit dengan mapping indikator

## Tampilan Admin Dashboard

### 1. Summary Cards (7 Indikator)
- Program 3R: Total Kg
- Pengurangan Kertas & Plastik: Total Kg
- Limbah Organik: Total Kg
- Limbah Anorganik: Total Kg
- Limbah B3: Total Kg
- Limbah Cair: Total L
- Persentase Daur Ulang: %

### 2. Tabel 7 Indikator UIGM
| No | Indikator | Total (Kg) | Total (L) | Records | Sumber | Bukti | Aksi |

### 3. Tabel Rekapan Detail
| No | Nama User/Unit | Jenis Sampah | Jumlah/Volume | Kategori Indikator | Waktu Input | Bukti | Gedung |

## Keunggulan Sistem

### 1. Sinkronisasi Real-time
- Data user langsung termapping ke indikator UIGM
- Tidak perlu input manual dari admin
- Update otomatis saat ada data baru

### 2. Mapping Intelligent
- Multiple level matching (exact, keyword, fallback)
- Flexible keyword detection
- Robust error handling

### 3. Standardisasi Profesional
- Berdasarkan UI GreenMetric standards
- Konsisten dengan regulasi lingkungan hidup
- Audit-ready data structure

### 4. Monitoring Komprehensif
- Track per user/unit
- Monitor bukti dukung
- Analisis persentase daur ulang

## File yang Dibuat/Dimodifikasi

### Services
- `app/Services/UIGMIndicatorMappingService.php` (BARU)
  - Logika mapping 7 indikator
  - Query data terstruktur
  - Perhitungan persentase daur ulang

### Controllers
- `app/Controllers/Admin/IndikatorUigm.php`
  - Method `getUIGMIndicatorData()`
  - Method `getDetailedRecapData()`

### Views
- `app/Views/admin_pusat/indikator_uigm/dashboard.php`
  - 7 summary cards
  - Tabel indikator UIGM
  - Tabel rekapan detail
  - AJAX real-time loading

### Routes
- `app/Config/Routes/Admin/indikator_uigm.php`
  - Endpoint untuk API baru

## Cara Kerja Sistem

1. **User Input Data**: User mengisi form waste management
2. **TPS Approval**: TPS mereview dan approve data
3. **Auto Mapping**: Sistem otomatis mapping ke 7 indikator UIGM
4. **Real-time Update**: Dashboard admin terupdate otomatis
5. **Monitoring**: Admin dapat monitor per indikator dan per user

Sistem ini memastikan bahwa setiap input user langsung terintegrasi dengan standar pelaporan UI GreenMetric tanpa intervensi manual dari admin.