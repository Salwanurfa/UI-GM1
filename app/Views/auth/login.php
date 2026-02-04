<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dashboard UIGM POLBAN</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #5C8CBF;
            --primary-dark: #4a7ba7;
            --primary-light: #e8f0f8;
            --light-bg: #f8f9fa;
            --card-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            --input-shadow: 0 0 0 0.2rem rgba(92, 140, 191, 0.25);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--light-bg) 0%, #e3f2fd 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
        }

        .login-card {
            background: white;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            padding: 2.5rem;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        }

        .btn-back {
            position: absolute;
            top: 1.5rem;
            left: 1.5rem;
            background: var(--light-bg);
            border: 2px solid #e9ecef;
            color: var(--text-dark);
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            cursor: pointer;
            z-index: 10;
        }

        .btn-back:hover {
            background: white;
            border-color: var(--primary-color);
            color: var(--primary-color);
            transform: translateX(-3px);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
            color: #6c757d;
            font-size: 0.95rem;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.875rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background-color: #fafbfc;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: var(--input-shadow);
            background-color: white;
            outline: none;
        }

        .form-control::placeholder {
            color: #adb5bd;
            font-weight: 400;
        }

        .input-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
            font-size: 1rem;
            pointer-events: none;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border: none;
            color: white;
            padding: 0.875rem 1.5rem;
            border-radius: 10px;
            font-weight: 500;
            font-size: 0.95rem;
            width: 100%;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #3d6b94 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(92, 140, 191, 0.4);
            color: white;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-login .spinner-border {
            width: 1rem;
            height: 1rem;
            margin-right: 0.5rem;
        }

        .alert {
            border-radius: 10px;
            border: none;
            padding: 0.875rem 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .invalid-feedback {
            display: block;
            font-size: 0.85rem;
            color: #dc3545;
            margin-top: 0.25rem;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .login-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e9ecef;
        }

        .login-footer small {
            color: #6c757d;
            font-size: 0.85rem;
        }

        /* Loading Animation */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: none;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-card {
                padding: 2rem 1.5rem;
                margin: 1rem;
            }

            .login-title {
                font-size: 1.5rem;
            }
        }

        /* Animation */
        .login-card {
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    <!-- Mobile Responsive CSS -->
    <link href="<?= base_url('/css/mobile-responsive.css') ?>" rel="stylesheet">
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Back Button -->
            <a href="<?= base_url('/') ?>" class="btn-back" title="Kembali ke Beranda">
                <i class="fas fa-arrow-left"></i>
            </a>

            <div class="loading-overlay" id="loadingOverlay">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            <!-- Header -->
            <div class="login-header">
                <h1 class="login-title">Login</h1>
                <p class="login-subtitle">Silakan masuk untuk melanjutkan ke dashboard</p>
            </div>

            <!-- Alert Messages -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form id="loginForm" action="<?= base_url('/auth/process-login') ?>" method="POST">
                <?= csrf_field() ?>

                <!-- Email/Username Input -->
                <div class="form-group">
                    <label for="login" class="form-label">Email atau Username</label>
                    <div class="position-relative">
                        <input
                            type="text"
                            class="form-control <?= isset($errors['login']) ? 'is-invalid' : '' ?>"
                            id="login"
                            name="login"
                            placeholder="Masukkan email atau username"
                            value="<?= old('login') ?>"
                            required
                            autocomplete="username">
                        <i class="fas fa-user input-icon"></i>
                    </div>
                    <?php if (isset($errors['login'])): ?>
                        <div class="invalid-feedback"><?= $errors['login'] ?></div>
                    <?php endif; ?>
                </div>

                <!-- Password Input -->
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="position-relative">
                        <input
                            type="password"
                            class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                            id="password"
                            name="password"
                            placeholder="Masukkan password"
                            required
                            autocomplete="current-password">
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                    <?php if (isset($errors['password'])): ?>
                        <div class="invalid-feedback"><?= $errors['password'] ?></div>
                    <?php endif; ?>
                </div>

                <!-- Login Button -->
                <button type="submit" class="btn btn-login" id="loginBtn">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Masuk
                </button>
            </form>

            <!-- Footer -->
            <div class="login-footer">
                <small>
                    <i class="fas fa-shield-alt me-1"></i>
                    Dashboard UIGM POLBAN - Sistem Informasi UI GreenMetric
                </small>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');
            const loadingOverlay = document.getElementById('loadingOverlay');

            // Form submission handling
            loginForm.addEventListener('submit', function(e) {
                // Show loading state
                loginBtn.disabled = true;
                loginBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Memproses...';
                loadingOverlay.style.display = 'flex';

                // Validate form
                const login = document.getElementById('login').value.trim();
                const password = document.getElementById('password').value;

                if (!login || !password) {
                    e.preventDefault();
                    resetButton();
                    showAlert('Mohon lengkapi semua field', 'danger');
                    return;
                }

                if (password.length < 6) {
                    e.preventDefault();
                    resetButton();
                    showAlert('Password minimal 6 karakter', 'danger');
                    return;
                }
            });

            // Reset button state
            function resetButton() {
                loginBtn.disabled = false;
                loginBtn.innerHTML = '<i class="fas fa-sign-in-alt me-2"></i>Masuk';
                loadingOverlay.style.display = 'none';
            }

            // Show alert
            function showAlert(message, type) {
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type}`;
                alertDiv.innerHTML = `<i class="fas fa-exclamation-circle me-2"></i>${message}`;

                const form = document.getElementById('loginForm');
                form.parentNode.insertBefore(alertDiv, form);

                setTimeout(() => {
                    alertDiv.remove();
                }, 5000);
            }

            // Input focus effects
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.querySelector('.input-icon').style.color = '#5C8CBF';
                });

                input.addEventListener('blur', function() {
                    this.parentElement.querySelector('.input-icon').style.color = '#adb5bd';
                });
            });

            // Auto-hide alerts
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        alert.remove();
                    }, 300);
                }, 5000);
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && (e.target.id === 'login' || e.target.id === 'password')) {
                    loginForm.submit();
                }
            });
        });
    </script>
    <!-- Mobile Menu JS -->
    <script src="<?= base_url('/js/mobile-menu.js') ?>"></script>
</body>

</html>