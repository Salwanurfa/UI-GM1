<?php
/**
 * Security Profile Page
 * Halaman profil untuk Petugas Keamanan
 * Layout: Two Column (Left: Profile Card, Right: Edit Forms)
 */
$title = $title ?? 'Profil Akun Security';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - UI GreenMetric POLBAN</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        body {
            background: #f8f9fc;
            color: #5a5c69;
        }

        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            padding: 0;
        }

        .page-container {
            padding: 2rem;
        }

        /* ===== PAGE HEADER ===== */
        .page-header {
            margin-bottom: 2rem;
        }

        .page-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            color: #858796;
            font-size: 0.95rem;
            margin: 0;
        }

        /* ===== PROFILE CARD (LEFT COLUMN) ===== */
        .profile-card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem rgba(58, 59, 69, 0.15);
            overflow: hidden;
        }

        .profile-card-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 1.5rem;
            text-align: center;
        }

        .profile-card-header h5 {
            margin: 0;
            font-size: 1.125rem;
            font-weight: 700;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3.5rem;
            margin: 1.5rem auto;
            border: 4px solid rgba(255, 255, 255, 0.3);
        }

        .profile-name {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .profile-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            display: inline-block;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .profile-card-body {
            padding: 1.5rem;
        }

        .profile-info-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #e3e6f0;
        }

        .profile-info-item:last-child {
            border-bottom: none;
        }

        .profile-info-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .profile-info-content {
            flex: 1;
        }

        .profile-info-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #858796;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .profile-info-value {
            font-size: 0.95rem;
            font-weight: 600;
            color: #2c3e50;
        }

        /* ===== FORM CARDS (RIGHT COLUMN) ===== */
        .form-card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem rgba(58, 59, 69, 0.15);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .form-card-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-card-header h5 {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
        }

        .form-card-body {
            padding: 1.5rem;
        }

        /* ===== FORM ELEMENTS ===== */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.875rem;
            color: #5a5c69;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-label i {
            color: #28a745;
        }

        .form-control {
            border-radius: 0.35rem;
            border: 1px solid #d1d3e2;
            padding: 0.625rem 0.875rem;
            font-size: 0.875rem;
        }

        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }

        .form-control:disabled,
        .form-control[readonly] {
            background-color: #e9ecef;
            cursor: not-allowed;
        }

        /* ===== BUTTONS ===== */
        .btn {
            padding: 0.625rem 1.25rem;
            border-radius: 0.35rem;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }

        .btn i {
            margin-right: 0.5rem;
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #218838 0%, #1aa179 100%);
            transform: translateY(-2px);
            box-shadow: 0 0.25rem 0.5rem rgba(40, 167, 69, 0.3);
        }

        .btn-warning {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            border: none;
            color: white;
        }

        .btn-warning:hover {
            background: linear-gradient(135deg, #e0a800 0%, #e68900 100%);
            transform: translateY(-2px);
            box-shadow: 0 0.25rem 0.5rem rgba(255, 193, 7, 0.3);
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            border: none;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        /* ===== ALERT ===== */
        .alert {
            border-radius: 0.5rem;
            border: none;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 991px) {
            .profile-card {
                margin-bottom: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }

            .page-container {
                padding: 1rem;
            }

            .profile-avatar {
                width: 100px;
                height: 100px;
                font-size: 3rem;
            }
        }
    </style>
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <div class="page-container">
            <!-- Page Header -->
            <div class="page-header">
                <h1><i class="fas fa-user-circle me-2"></i>Profil Akun</h1>
                <p>Kelola informasi profil dan keamanan akun Anda</p>
            </div>

            <!-- Flash Messages -->
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
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mb-0 mt-2">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Two Column Layout -->
            <div class="row">
                <!-- LEFT COLUMN: Profile Card -->
                <div class="col-lg-4">
                    <div class="profile-card">
                        <div class="profile-card-header">
                            <div class="profile-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="profile-name"><?= esc($user['nama_lengkap']) ?></div>
                            <div class="profile-badge">
                                <i class="fas fa-shield-alt me-1"></i>Security
                            </div>
                        </div>
                        <div class="profile-card-body">
                            <div class="profile-info-item">
                                <div class="profile-info-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="profile-info-content">
                                    <div class="profile-info-label">Email</div>
                                    <div class="profile-info-value"><?= esc($user['email']) ?></div>
                                </div>
                            </div>

                            <div class="profile-info-item">
                                <div class="profile-info-icon">
                                    <i class="fas fa-id-badge"></i>
                                </div>
                                <div class="profile-info-content">
                                    <div class="profile-info-label">Username</div>
                                    <div class="profile-info-value"><?= esc($user['username']) ?></div>
                                </div>
                            </div>

                            <div class="profile-info-item">
                                <div class="profile-info-icon">
                                    <i class="fas fa-user-tag"></i>
                                </div>
                                <div class="profile-info-content">
                                    <div class="profile-info-label">Jabatan</div>
                                    <div class="profile-info-value">Petugas Keamanan</div>
                                </div>
                            </div>

                            <?php if (isset($user['nama_unit'])): ?>
                            <div class="profile-info-item">
                                <div class="profile-info-icon">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div class="profile-info-content">
                                    <div class="profile-info-label">Unit Kerja</div>
                                    <div class="profile-info-value"><?= esc($user['nama_unit']) ?></div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Edit Forms -->
                <div class="col-lg-8">
                    <!-- Edit Profile Card -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <i class="fas fa-user-edit"></i>
                            <h5>Edit Profil</h5>
                        </div>
                        <div class="form-card-body">
                            <form action="<?= base_url('security/profile/update') ?>" method="POST">
                                <?= csrf_field() ?>
                                
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-user"></i>
                                        Nama Lengkap <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="nama_lengkap" class="form-control" 
                                           value="<?= esc($user['nama_lengkap']) ?>" 
                                           placeholder="Masukkan nama lengkap" required>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-envelope"></i>
                                        Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" name="email" class="form-control" 
                                           value="<?= esc($user['email']) ?>" 
                                           placeholder="Masukkan email" required>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-id-badge"></i>
                                        Username
                                    </label>
                                    <input type="text" name="username" class="form-control" 
                                           value="<?= esc($user['username']) ?>" 
                                           readonly>
                                    <small class="text-muted">Username tidak dapat diubah</small>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-user-tag"></i>
                                        Role
                                    </label>
                                    <input type="text" class="form-control" 
                                           value="Security (Petugas Keamanan)" 
                                           disabled>
                                    <small class="text-muted">Role tidak dapat diubah</small>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save"></i>Simpan Perubahan
                                    </button>
                                    <a href="<?= base_url('security/dashboard') ?>" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i>Kembali
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Change Password Card -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <i class="fas fa-key"></i>
                            <h5>Ubah Password</h5>
                        </div>
                        <div class="form-card-body">
                            <form action="<?= base_url('security/profile/update') ?>" method="POST">
                                <?= csrf_field() ?>
                                
                                <!-- Hidden fields untuk data yang tidak berubah -->
                                <input type="hidden" name="nama_lengkap" value="<?= esc($user['nama_lengkap']) ?>">
                                <input type="hidden" name="username" value="<?= esc($user['username']) ?>">
                                <input type="hidden" name="email" value="<?= esc($user['email']) ?>">
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Informasi:</strong> Kosongkan field password jika tidak ingin mengubah password.
                                </div>

                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-lock"></i>
                                        Password Baru <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" name="password" class="form-control" 
                                           placeholder="Masukkan password baru (minimal 5 karakter)">
                                    <small class="text-muted">Minimal 5 karakter</small>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-lock"></i>
                                        Konfirmasi Password <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" name="password_confirm" class="form-control" 
                                           placeholder="Ketik ulang password baru">
                                </div>

                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-key"></i>Ubah Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>
