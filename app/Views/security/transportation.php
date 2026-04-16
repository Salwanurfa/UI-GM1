<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - UI GreenMetric POLBAN</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background: #f4f6f9;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 30px;
            min-height: 100vh;
        }
        
        .profile-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .profile-header {
            text-align: center;
            border-bottom: 2px solid #1e3c72;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: white;
            margin: 0 auto 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #1e3c72;
            box-shadow: 0 0 0 0.2rem rgba(30, 60, 114, 0.25);
        }
        
        .btn-update {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(30, 60, 114, 0.3);
            color: white;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .info-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .password-section {
            background: #fff3cd;
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
            border-left: 4px solid #ffc107;
        }
    </style>
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <?= $this->extend('layouts/admin_pusat_new') ?>

<?= $this->section('content') ?>

<!-- Content Wrapper untuk Security Layout -->
<div class="content-wrapper" style="margin-left: 260px; padding: 20px;">

<!-- Page Header - Security Input Form -->
<div class="page-header mb-4 ms-3">
    <h4><i class="fas fa-edit"></i> Input Statistik Transportasi</h4>
    <p>Input jumlah total kendaraan berdasarkan kategori dan jenis bahan bakar dalam periode tertentu</p>
</div>

<!-- Success/Error Messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Terjadi kesalahan:</strong>
        <ul class="mb-0 mt-2">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Transportation Input Form Card -->
<div class="card shadow-sm border-0 p-4">
    <!-- Header Petunjuk -->
    <div class="alert alert-info d-flex align-items-center mb-4">
        <i class="fas fa-info-circle me-3 fs-5"></i>
        <div>
            <strong>Petunjuk Penggunaan</strong><br>
            <small>Sistem ini digunakan untuk mencatat statistik agregat kendaraan, bukan kendaraan individual. 
            Pilih periode waktu, kategori kendaraan, jenis bahan bakar, dan masukkan jumlah total kendaraan yang terhitung.</small>
        </div>
    </div>

    <form action="<?= base_url('/security/transportation/save') ?>" method="POST">
        <?= csrf_field() ?>
        <?php if (isset($edit_data) && $edit_data): ?>
            <input type="hidden" name="edit_id" value="<?= $edit_data['id'] ?>">
        <?php endif; ?>
        
        <!-- Row 1: Periode Waktu (Kiri) & Waktu Input (Kanan) -->
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="periode" class="form-label fw-bold mb-2">
                    <i class="fas fa-calendar-alt me-2 text-primary"></i>Periode Waktu
                </label>
                <input type="date" 
                       class="form-control" 
                       id="periode" 
                       name="periode" 
                       required>
            </div>
            
            <div class="col-md-6">
                <label for="waktu_input" class="form-label fw-bold mb-2">
                    <i class="fas fa-clock me-2 text-primary"></i>Waktu Input
                </label>
                <input type="text" 
                       class="form-control" 
                       id="waktu_input" 
                       name="waktu_input" 
                       value="<?= date('d/m/Y H:i'); ?>"
                       readonly
                       style="background-color: #f8f9fa;">
                <small class="text-muted">Waktu otomatis terisi saat form dibuka (WIB)</small>
            </div>
        </div>

        <!-- Row 2: Kategori Kendaraan (Kiri) & Jenis Bahan Bakar (Kanan) -->
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="kategori_kendaraan" class="form-label fw-bold mb-2">
                    <i class="fas fa-car me-2 text-primary"></i>Kategori Kendaraan
                </label>
                <select class="form-control" id="kategori_kendaraan" name="kategori_kendaraan" required>
                    <option value="">Pilih Kategori Kendaraan</option>
                    <option value="Roda Dua">Motor (Sepeda Motor)</option>
                    <option value="Roda Empat">Mobil (Mobil Pribadi, Truk)</option>
                    <option value="Sepeda">Sepeda</option>
                    <option value="Kendaraan Umum">Bus Kampus (Angkot, Bus Kota)</option>
                </select>
            </div>
            
            <div class="col-md-6">
                <label for="jenis_bahan_bakar" class="form-label fw-bold mb-2">
                    <i class="fas fa-gas-pump me-2 text-primary"></i>Jenis Bahan Bakar
                </label>
                <select class="form-control" id="jenis_bahan_bakar" name="jenis_bahan_bakar" required onchange="updateZevStatus()">
                    <option value="">Pilih Jenis Bahan Bakar</option>
                    <option value="Bensin">Bensin</option>
                    <option value="Diesel">Diesel</option>
                    <option value="Listrik">Listrik (ZEV)</option>
                    <option value="Non-BBM">Non-BBM (Sepeda, Manual - ZEV)</option>
                </select>
                <small class="text-muted">ZEV = Zero Emission Vehicle (Kendaraan Tanpa Emisi)</small>
            </div>
        </div>

        <!-- Baris Bawah: Jumlah Total & Layanan Shuttle (Sejajar) -->
        <div class="row align-items-end mb-4">
            <div class="col-md-6">
                <label for="jumlah_total" class="form-label fw-bold mb-2">
                    <i class="fas fa-calculator me-2 text-primary"></i>Jumlah Total Kendaraan
                </label>
                <input type="number" 
                       class="form-control" 
                       id="jumlah_total" 
                       name="jumlah_total" 
                       placeholder="Masukkan jumlah total kendaraan" 
                       min="1" 
                       required>
                <small class="text-muted">Jumlah kendaraan yang terhitung dalam periode ini</small>
            </div>
            
            <div class="col-md-6 d-flex align-items-center">
                <div>
                    <label class="form-label fw-bold mb-2">
                        <i class="fas fa-bus me-2 text-primary"></i>Layanan Transportasi
                    </label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="is_shuttle" 
                               name="is_shuttle" 
                               value="1">
                        <label class="form-check-label fw-bold" for="is_shuttle">
                            Layanan Antar-Jemput (Shuttle)
                        </label>
                    </div>
                    <small class="text-muted">Centang jika kendaraan ini adalah layanan shuttle kampus</small>
                </div>
            </div>
        </div>

        <!-- ZEV Status Display -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="zev-status-display" id="zevStatusDisplay" style="display: none;">
                    <div class="alert alert-success d-flex align-items-center">
                        <i class="fas fa-leaf me-2"></i>
                        <div>
                            <strong>Zero Emission Vehicle (ZEV)</strong><br>
                            <small>Kendaraan ini akan otomatis ditandai sebagai ZEV untuk indikator UIGM TR 3 & TR 4</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="d-flex gap-3 pt-3 border-top">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-save me-2"></i>Simpan Statistik
            </button>
            
            <button type="reset" class="btn btn-secondary btn-sm">
                <i class="fas fa-undo me-2"></i>Reset Form
            </button>
            
            <a href="<?= base_url('/security/dashboard') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
            </a>
        </div>
    </form>
</div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>