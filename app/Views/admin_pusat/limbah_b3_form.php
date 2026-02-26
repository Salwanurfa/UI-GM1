<?php
/**
 * Admin Pusat - Form Tambah/Edit Limbah B3
 */

$master_list  = $master_list ?? [];
$satuan_list  = $satuan_list ?? [];
$errors       = session()->getFlashdata('errors') ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Form Limbah B3' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?= $this->include('partials/sidebar') ?>

<div class="main-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h1><i class="fas fa-skull-crossbones"></i> <?= $title ?></h1>
            <p>Input data Limbah B3 baru ke dalam sistem</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <i class="fas fa-file-alt me-2"></i>
            <h3 class="mb-0">Form Input Data Limbah B3</h3>
        </div>
        <div class="card-body">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h4 class="alert-heading mb-3">
                        <i class="fas fa-exclamation-circle me-2"></i>Terjadi Kesalahan
                    </h4>
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= base_url('/admin-pusat/limbah-b3/store') ?>" class="needs-validation" novalidate>
                <?= csrf_field() ?>

                <!-- Row 1: Master Limbah B3 -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <label for="master_limbah_id" class="form-label">
                            <i class="fas fa-list me-2 text-primary"></i>Jenis Limbah B3 <span class="text-danger">*</span>
                        </label>
                        <select class="form-control <?= (old('master_limbah_id') !== null) ? 'is-valid' : '' ?>" 
                                id="master_limbah_id" name="master_limbah_id" required>
                            <option value="">-- Pilih Jenis Limbah --</option>
                            <?php foreach ($master_list as $master): ?>
                                <option value="<?= $master['id'] ?>" 
                                        <?= (old('master_limbah_id') == $master['id']) ? 'selected' : '' ?>>
                                    <?= esc($master['nama_limbah']) ?> (<?= esc($master['kode_limbah'] ?? '-') ?>) - <?= esc($master['kategori_bahaya'] ?? '-') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            Jenis Limbah B3 harus dipilih
                        </div>
                    </div>
                </div>

                <!-- Row 2: Lokasi & Bentuk Fisik -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="lokasi" class="form-label">
                            <i class="fas fa-map-marker-alt me-2 text-primary"></i>Lokasi
                        </label>
                        <input type="text" class="form-control" id="lokasi" name="lokasi" 
                               placeholder="Contoh: Ruang Lab Kimia" 
                               value="<?= esc(old('lokasi') ?? '') ?>">
                        <small class="form-text text-muted">Opsional</small>
                    </div>

                    <div class="col-md-6">
                        <label for="bentuk_fisik" class="form-label">
                            <i class="fas fa-cube me-2 text-primary"></i>Bentuk Fisik
                        </label>
                        <input type="text" class="form-control" id="bentuk_fisik" name="bentuk_fisik" 
                               placeholder="Contoh: Cair, Padat, Gas" 
                               value="<?= esc(old('bentuk_fisik') ?? '') ?>">
                        <small class="form-text text-muted">Opsional</small>
                    </div>
                </div>

                <!-- Row 3: Timbulan & Satuan -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="timbulan" class="form-label">
                            <i class="fas fa-weight me-2 text-primary"></i>Timbulan/Berat <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control <?= (old('timbulan') !== null) ? 'is-valid' : '' ?>" 
                               id="timbulan" name="timbulan" step="0.01" min="0" 
                               placeholder="0.00" required 
                               value="<?= esc(old('timbulan') ?? '') ?>">
                        <div class="invalid-feedback">
                            Timbulan/berat harus berupa angka > 0
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="satuan" class="form-label">
                            <i class="fas fa-ruler-horizontal me-2 text-primary"></i>Satuan <span class="text-danger">*</span>
                        </label>
                        <select class="form-control <?= (old('satuan') !== null) ? 'is-valid' : '' ?>" 
                                id="satuan" name="satuan" required>
                            <option value="">-- Pilih Satuan --</option>
                            <?php foreach ($satuan_list as $satuan): ?>
                                <option value="<?= $satuan ?>" 
                                        <?= (old('satuan') == $satuan) ? 'selected' : '' ?>>
                                    <?= ucfirst($satuan) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            Satuan harus dipilih
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="kemasan" class="form-label">
                            <i class="fas fa-box me-2 text-primary"></i>Kemasan
                        </label>
                        <input type="text" class="form-control" id="kemasan" name="kemasan" 
                               placeholder="Contoh: Drum, Kantong, Botol" 
                               value="<?= esc(old('kemasan') ?? '') ?>">
                        <small class="form-text text-muted">Opsional</small>
                    </div>
                </div>

                <!-- Row 4: Tanggal Input -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <label for="tanggal_input" class="form-label">
                            <i class="fas fa-calendar-alt me-2 text-primary"></i>Tanggal Input <span class="text-danger">*</span>
                        </label>
                        <input type="datetime-local" class="form-control <?= (old('tanggal_input') !== null) ? 'is-valid' : '' ?>" 
                               id="tanggal_input" name="tanggal_input"
                               value="<?= old('tanggal_input') ?? date('Y-m-d\TH:i') ?>" required>
                        <div class="invalid-feedback">
                            Tanggal input harus diisi
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="row mt-5 mb-3">
                    <div class="col-12">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="<?= base_url('/admin-pusat/limbah-b3') ?>" class="btn btn-secondary btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>Batal
                            </a>
                            <button type="reset" class="btn btn-warning btn-lg">
                                <i class="fas fa-redo me-2"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Simpan Data
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .main-content {
        margin-left: 280px;
        padding: 30px;
        min-height: 100vh;
        background: #f8f9fa;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding: 20px 0;
        border-bottom: 2px solid #e9ecef;
    }

    .page-header h1 {
        color: #2c3e50;
        margin-bottom: 10px;
        font-size: 28px;
        font-weight: 700;
    }

    .page-header p {
        color: #6c757d;
        font-size: 16px;
        margin: 0;
    }

    .card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border: none;
        overflow: hidden;
    }

    .card-header {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        padding: 20px 25px;
        border: none;
    }

    .card-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
    }

    .card-body {
        padding: 30px;
    }

    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
    }

    .form-control, .form-select {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 10px 15px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .form-control.is-valid, .form-select.is-valid {
        border-color: #28a745;
    }

    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 12px;
        margin-top: 5px;
    }

    .form-text {
        font-size: 12px;
        color: #6c757d;
        display: block;
        margin-top: 5px;
    }

    .text-danger {
        color: #dc3545;
        font-weight: bold;
    }

    .btn {
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
    }

    .btn-warning {
        background: #ffc107;
        color: #212529;
    }

    .btn-warning:hover {
        background: #e0a800;
        transform: translateY(-2px);
    }

    .alert {
        border-radius: 10px;
        margin-bottom: 20px;
        border: none;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
    }

    .alert-heading {
        font-weight: 700;
        font-size: 16px;
    }

    .alert ul {
        padding-left: 20px;
    }

    .alert ul li {
        margin-bottom: 5px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
            padding: 20px;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .page-header h1 {
            font-size: 22px;
        }

        .card-body {
            padding: 20px;
        }

        .btn-lg {
            width: 100%;
            margin-top: 10px;
        }

        .d-flex.gap-2 {
            flex-direction: column;
        }

        .d-flex.gap-2 .btn {
            width: 100%;
        }
    }
</style>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Form validation
    (function () {
        'use strict';
        window.addEventListener('load', function () {
            const forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
        }, false);
    }());

    // Auto-hide alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function () {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.remove();
                }, 300);
            }, 5000);
        });
    });
</script>
