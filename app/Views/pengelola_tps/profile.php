<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-name" content="<?= csrf_token() ?>">
    <title><?= $title ?? 'Edit Profil' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Mobile Responsive CSS -->
    <link href="<?= base_url('/css/mobile-responsive.css') ?>" rel="stylesheet">
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <div class="page-header">
            <div class="header-content">
                <h1><i class="fas fa-user-circle"></i> Edit Profil</h1>
                <p>Kelola informasi profil dan keamanan akun Anda</p>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="row">
            <!-- Profile Card -->
            <div class="col-md-4">
                <div class="card profile-card">
                    <div class="card-body text-center">
                        <div class="profile-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <h3 class="profile-name"><?= esc($user['nama_lengkap']) ?></h3>
                        <p class="profile-role">
                            <span class="badge bg-info">
                                <?= ucfirst(str_replace('_', ' ', $user['role'])) ?>
                            </span>
                        </p>
                        <div class="profile-info">
                            <div class="info-item">
                                <i class="fas fa-envelope"></i>
                                <span><?= esc($user['email']) ?></span>
                            </div>
                            <?php if ($unit): ?>
                            <div class="info-item">
                                <i class="fas fa-building"></i>
                                <span><?= esc($unit['nama_unit']) ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="info-item">
                                <i class="fas fa-calendar"></i>
                                <span>Bergabung: <?= isset($user['created_at']) ? date('d M Y', strtotime($user['created_at'])) : '-' ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Forms -->
            <div class="col-md-8">
                <!-- Edit Profile -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3><i class="fas fa-user-edit"></i> Edit Profil</h3>
                    </div>
                    <div class="card-body">
                        <form id="updateProfilForm">
                            <div class="mb-3">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap *</label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" 
                                       value="<?= esc($user['nama_lengkap']) ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= esc($user['email']) ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" value="<?= esc($user['username']) ?>" disabled>
                                <small class="text-muted">Username tidak dapat diubah</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Role</label>
                                <input type="text" class="form-control" 
                                       value="<?= ucfirst(str_replace('_', ' ', $user['role'])) ?>" disabled>
                                <small class="text-muted">Role tidak dapat diubah</small>
                            </div>
                            
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-lock"></i> Ubah Password</h3>
                    </div>
                    <div class="card-body">
                        <form id="changePasswordForm">
                            <div class="mb-3">
                                <label for="password_lama" class="form-label">Password Lama *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password_lama" 
                                           name="password_lama" required>
                                    <button class="btn btn-outline-secondary" type="button" 
                                            onclick="togglePassword('password_lama')">
                                        <i class="fas fa-eye" id="icon_password_lama"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password_baru" class="form-label">Password Baru *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password_baru" 
                                           name="password_baru" required>
                                    <button class="btn btn-outline-secondary" type="button" 
                                            onclick="togglePassword('password_baru')">
                                        <i class="fas fa-eye" id="icon_password_baru"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Minimal 6 karakter</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password_konfirmasi" class="form-label">Konfirmasi Password Baru *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password_konfirmasi" 
                                           name="password_konfirmasi" required>
                                    <button class="btn btn-outline-secondary" type="button" 
                                            onclick="togglePassword('password_konfirmasi')">
                                        <i class="fas fa-eye" id="icon_password_konfirmasi"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key"></i> Ubah Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById('icon_' + fieldId);
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Update profil form
        document.getElementById('updateProfilForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Add CSRF token
            const csrfName = document.querySelector('meta[name="csrf-name"]')?.getAttribute('content');
            const csrfHash = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (csrfName && csrfHash) {
                formData.append(csrfName, csrfHash);
            }
            
            fetch('<?= base_url('/pengelola-tps/profile/update') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('error', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Terjadi kesalahan saat memperbarui profil');
            });
        });

        // Change password form
        document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Add CSRF token
            const csrfName = document.querySelector('meta[name="csrf-name"]')?.getAttribute('content');
            const csrfHash = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (csrfName && csrfHash) {
                formData.append(csrfName, csrfHash);
            }
            
            fetch('<?= base_url('/pengelola-tps/profile/change-password') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    this.reset();
                } else {
                    showAlert('error', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Terjadi kesalahan saat mengubah password');
            });
        });

        // Show alert function
        function showAlert(type, message) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            
            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show">
                    <i class="fas ${iconClass}"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            const mainContent = document.querySelector('.main-content');
            const pageHeader = document.querySelector('.page-header');
            pageHeader.insertAdjacentHTML('afterend', alertHtml);
            
            setTimeout(() => {
                const alert = document.querySelector('.alert');
                if (alert) {
                    alert.remove();
                }
            }, 5000);
        }
    </script>
    <!-- Mobile Menu JS -->
    <script src="<?= base_url('/js/mobile-menu.js') ?>"></script>
</body>
</html>

<style>
body {
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f8f9fa;
}

.main-content {
    margin-left: 280px;
    padding: 30px;
    min-height: 100vh;
}

.page-header {
    margin-bottom: 30px;
    padding: 20px 0;
    border-bottom: 2px solid #e9ecef;
}

.header-content h1 {
    color: #2c3e50;
    margin-bottom: 5px;
    font-size: 28px;
    font-weight: 700;
}

.header-content p {
    color: #6c757d;
    margin: 0;
    font-size: 16px;
}

.card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    overflow: hidden;
    border: none;
}

.card-header {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    color: white;
    padding: 20px 25px;
    border: none;
}

.card-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-body {
    padding: 25px;
}

.profile-card {
    position: sticky;
    top: 30px;
}

.profile-avatar {
    font-size: 100px;
    color: #2c3e50;
    margin-bottom: 20px;
}

.profile-name {
    font-size: 24px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 10px;
}

.profile-role {
    margin-bottom: 20px;
}

.profile-info {
    text-align: left;
    margin-top: 30px;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 12px 0;
    border-bottom: 1px solid #e9ecef;
}

.info-item:last-child {
    border-bottom: none;
}

.info-item i {
    width: 20px;
    color: #2c3e50;
    font-size: 16px;
}

.info-item span {
    color: #6c757d;
    font-size: 14px;
}

.form-label {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
}

.form-control {
    border-radius: 8px;
    border: 1px solid #dee2e6;
    padding: 10px 15px;
}

.form-control:focus {
    border-color: #2c3e50;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.form-control:disabled {
    background-color: #f8f9fa;
    cursor: not-allowed;
}

/* Fix input-group for password fields */
.input-group {
    position: relative;
    display: flex;
    flex-wrap: nowrap;
    align-items: stretch;
    width: 100%;
}

.input-group .form-control {
    position: relative;
    flex: 1 1 auto;
    width: 1%;
    min-width: 0;
    padding-right: 45px; /* Space for button */
}

.input-group .btn {
    position: relative;
    z-index: 2;
    margin-left: -1px;
}

.input-group .btn-outline-secondary {
    border-color: #dee2e6;
    color: #6c757d;
    background-color: #fff;
    padding: 10px 15px;
    border-radius: 0 8px 8px 0;
    border-left: 1px solid #dee2e6;
}

.input-group .btn-outline-secondary:hover {
    background-color: #f8f9fa;
    color: #495057;
    border-color: #dee2e6;
}

.input-group .btn-outline-secondary:focus {
    box-shadow: none;
    border-color: #2c3e50;
}

.btn {
    border-radius: 8px;
    font-weight: 500;
    padding: 10px 20px;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
}

.alert {
    border-radius: 10px;
    border: none;
    padding: 15px 20px;
}

@media (max-width: 768px) {
    .main-content {
        margin-left: 0;
        padding: 15px 10px;
        max-width: 100vw;
        overflow-x: hidden;
    }
    
    .profile-card {
        position: relative;
        top: 0;
        margin-bottom: 20px;
    }

    .profile-avatar {
        font-size: 80px;
    }

    .profile-name {
        font-size: 20px;
    }

    .card-header {
        padding: 12px 15px;
    }

    .card-body {
        padding: 15px;
    }

    .btn {
        width: 100%;
        margin-bottom: 10px;
    }

    /* Fix input-group on mobile */
    .input-group {
        display: flex;
        flex-wrap: nowrap;
    }

    .input-group .form-control {
        flex: 1;
        min-width: 0;
        border-radius: 8px 0 0 8px;
    }

    .input-group .btn-outline-secondary {
        flex-shrink: 0;
        width: 45px;
        padding: 10px;
        border-radius: 0 8px 8px 0;
    }
}

@media (max-width: 480px) {
    .main-content {
        padding: 10px 5px;
    }

    .profile-avatar {
        font-size: 60px;
    }

    .card-body {
        padding: 12px;
    }

    .input-group .btn-outline-secondary {
        width: 40px;
        padding: 8px;
    }
}
</style>

