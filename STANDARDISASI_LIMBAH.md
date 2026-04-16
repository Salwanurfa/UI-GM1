# Sistem Standardisasi Kategori Limbah - Indikator UIGM

## Overview
Sistem ini telah dibangun ulang dengan standardisasi kategori limbah berdasarkan:
- **UI GreenMetric Standards**
- **Regulasi Pemerintah Indonesia**
- **Best Practices Pengelolaan Limbah**

## Kategori Limbah Berstandar

### 1. Limbah Organik
- **Deskripsi**: Sisa makanan, dedaunan, limbah taman
- **Mapping dari kategori lama**: Organik, Organik Basah, Organik Kering, Sisa Makanan, Dedaunan, Limbah Taman
- **Satuan**: kg, g, ton
- **Dapat didaur ulang**: Ya

### 2. Limbah Anorganik
- **Deskripsi**: Plastik, kertas, logam, kaca
- **Mapping dari kategori lama**: Plastik, Kertas, Logam, Kaca, Anorganik
- **Satuan**: kg, g, ton, pcs, unit
- **Dapat didaur ulang**: Ya

### 3. Limbah B3 (Bahan Berbahaya & Beracun)
- **Deskripsi**: Baterai, lampu neon, limbah medis, elektronik
- **Mapping dari kategori lama**: B3, Baterai, Lampu Neon, Limbah Medis, Elektronik
- **Satuan**: kg, g, L, ml, pcs
- **Berbahaya**: Ya
- **Sumber data**: waste_management + limbah_b3 tables

### 4. Limbah Cair
- **Deskripsi**: Air limbah domestik/laboratorium
- **Mapping dari kategori lama**: Limbah Cair, Air Limbah, Limbah Laboratorium
- **Satuan**: L, ml, m³
- **Jenis**: Cair

### 5. Limbah Residu
- **Deskripsi**: Sampah akhir yang tidak bisa diolah kembali
- **Mapping dari kategori lama**: Residu, Sampah Akhir, Non-Recyclable
- **Satuan**: kg, g, ton
- **Dapat didaur ulang**: Tidak

## Fitur Sistem

### Dashboard Utama
- **URL**: `/admin-pusat/indikator-uigm`
- **Fitur**:
  - Tabel kategori limbah berstandar
  - Real-time data loading via AJAX
  - Summary statistics (Total Kg, Total L, Tingkat Daur Ulang, Sumber Data Aktif)
  - Filter berdasarkan tahun

### Detail Kategori
- **URL**: `/admin-pusat/indikator-uigm/detail/{category}`
- **Fitur**:
  - Breakdown data per unit/sumber
  - Summary per kategori
  - Informasi bukti foto

### API Endpoints
- **GET** `/admin-pusat/indikator-uigm/get-standardized-data`
  - Parameter: `tahun` (optional)
  - Response: Data limbah berstandar + summary statistics

## Logika Perhitungan

### Konversi Satuan
- **Berat**: g → kg (÷1000), ton → kg (×1000)
- **Volume**: ml → L (÷1000), m³ → L (×1000)

### Tingkat Daur Ulang
```
Recycle Rate = (Limbah Organik + Limbah Anorganik) / Total Limbah Padat × 100%
```

### Status Bukti Dukung
- **Ada Bukti**: Jika ada minimal 1 record dengan bukti_foto
- **Belum Ada**: Jika tidak ada bukti_foto

## File yang Dimodifikasi/Dibuat

### Controllers
- `app/Controllers/Admin/IndikatorUigm.php`
  - Method `getStandardizedData()` - API endpoint
  - Method `categoryDetail()` - Detail view

### Services
- `app/Services/WasteStandardizationService.php` (BARU)
  - Logika standardisasi kategori
  - Mapping kategori lama ke baru
  - Perhitungan statistik

### Views
- `app/Views/admin_pusat/indikator_uigm/dashboard.php` (DIBANGUN ULANG)
  - Dashboard dengan tabel berstandar
  - AJAX loading
  - Summary cards
- `app/Views/admin_pusat/indikator_uigm/category_detail.php` (BARU)
  - Detail breakdown per sumber

### Routes
- `app/Config/Routes/Admin/indikator_uigm.php`
  - Route untuk API dan detail view

## Keunggulan Sistem Baru

1. **Standardisasi Profesional**: Mengikuti standar UI GreenMetric
2. **Integrasi Data**: Menggabungkan data dari multiple tables
3. **Real-time Loading**: AJAX untuk performa optimal
4. **Responsive Design**: Bootstrap 5 dengan design modern
5. **Modular Architecture**: Service layer untuk reusability
6. **Accurate Calculations**: Konversi satuan yang tepat
7. **Evidence Tracking**: Monitoring bukti dukung per kategori

## Cara Penggunaan

1. **Akses Dashboard**: Login sebagai admin_pusat/super_admin
2. **Pilih Tahun**: Gunakan filter tahun untuk melihat data spesifik
3. **Lihat Summary**: Cards menampilkan total limbah dan tingkat daur ulang
4. **Detail Kategori**: Klik tombol mata untuk melihat breakdown per sumber
5. **Monitor Bukti**: Status badge menunjukkan kelengkapan bukti dukung

Sistem ini memberikan landasan yang kuat untuk pelaporan UI GreenMetric dengan data yang akurat dan terstandarisasi.