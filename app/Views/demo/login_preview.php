<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo Login - Dashboard UIGM POLBAN</title>

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

        .alert {
            border-radius: 10px;
            border: none;
            padding: 0.875rem 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .alert-info {
            background-color: var(--primary-light);
            color: var(--primary-dark);
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

        .demo-accounts {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .demo-accounts h6 {
            color: #495057;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        .demo-account {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e9ecef;
            font-size: 0.8rem;
        }

        .demo-account:last-child {
            border-bottom: none;
        }

        .demo-account .role {
            font-weight: 500;
            color: var(--primary-color);
        }

        .demo-account .credentials {
            color: #6c757d;
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
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Header -->
            <div class="login-header">
                <h1 class="login-title">Login</h1>
                <p class="login-subtitle">Silakan masuk untuk melanjutkan ke dashboard</p>
            </div>

            <!-- Demo Info -->
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Demo Mode:</strong> Gunakan akun demo di bawah untuk testing
            </div>

            <!-- Demo Accounts -->
            <div class="demo-accounts">
                <h6><i class="fas fa-users me-1"></i>Akun Demo Tersedia:</h6>
                <div class="demo-account">
                    <span class="role">Admin Pusat</span>
                    <span class="credentials">admin_pusat / adminpusat123</span>
                </div>
                <div class="demo-account">
                    <span class="role">Admin Unit FTE</span>
                    <span class="credentials">admin_fte / adminunit123</span>
                </div>
                <div class="demo-account">
                    <span class="role">Admin Unit FTMD</span>
                    <span class="credentials">admin_ftmd / adminunit123</span>
                </div>
            </div>

            <!-- Login Form -->
            <form id="demoLoginForm">
                <!-- Email/Username Input -->
                <div class="form-group">
                    <label for="login" class="form-label">Email atau Username</label>
                    <div class="position-relative">
                        <input
                            type="text"
                            class="form-control"
                            id="login"
                            name="login"
                            placeholder="Masukkan email atau username"
                            value="admin_pusat">
                        <i class="fas fa-user input-icon"></i>
                    </div>
                </div>

                <!-- Password Input -->
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="position-relative">
                        <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            placeholder="Masukkan password"
                            value="adminpusat123">
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                </div>

                <!-- Login Button -->
                <button type="submit" class="btn btn-login" id="demoLoginBtn">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Masuk (Demo)
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
            const demoLoginForm = document.getElementById('demoLoginForm');
            const demoLoginBtn = document.getElementById('demoLoginBtn');
            const loginInput = document.getElementById('login');
            const passwordInput = document.getElementById('password');

            // Demo accounts
            const demoAccounts = {
                'admin_pusat': {
                    password: 'adminpusat123',
                    role: 'Admin Pusat',
                    redirect: '/demo/admin-pusat'
                },
                'admin_fte': {
                    password: 'adminunit123',
                    role: 'Admin Unit FTE',
                    redirect: '/demo/admin-unit'
                },
                'admin_ftmd': {
                    password: 'adminunit123',
                    role: 'Admin Unit FTMD',
                    redirect: '/demo/admin-unit'
                }
            };

            // Quick fill demo accounts
            document.querySelectorAll('.demo-account').forEach(account => {
                account.addEventListener('click', function() {
                    const credentials = this.querySelector('.credentials').textContent.split(' / ');
                    loginInput.value = credentials[0];
                    passwordInput.value = credentials[1];
                });
            });

            // Form submission
            demoLoginForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const username = loginInput.value.trim();
                const password = passwordInput.value;

                // Show loading
                demoLoginBtn.disabled = true;
                demoLoginBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

                // Simulate login process
                setTimeout(() => {
                    if (demoAccounts[username] && demoAccounts[username].password === password) {
                        // Success
                        showAlert(`Login berhasil sebagai ${demoAccounts[username].role}!`, 'success');

                        setTimeout(() => {
                            if (demoAccounts[username].redirect === '/demo/admin-unit') {
                                window.location.href = '/eksperimen/demo/admin-unit';
                            } else {
                                showAlert('Dashboard Admin Pusat belum tersedia dalam demo ini', 'info');
                                resetButton();
                            }
                        }, 1500);
                    } else {
                        // Error
                        showAlert('Username atau password salah', 'danger');
                        resetButton();
                    }
                }, 1500);
            });

            function resetButton() {
                demoLoginBtn.disabled = false;
                demoLoginBtn.innerHTML = '<i class="fas fa-sign-in-alt me-2"></i>Masuk (Demo)';
            }

            function showAlert(message, type) {
                // Remove existing alerts
                const existingAlerts = document.querySelectorAll('.alert:not(.alert-info)');
                existingAlerts.forEach(alert => alert.remove());

                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type}`;
                alertDiv.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : 'info-circle'} me-2"></i>${message}`;

                const form = document.getElementById('demoLoginForm');
                form.parentNode.insertBefore(alertDiv, form);

                if (type !== 'success') {
                    setTimeout(() => {
                        alertDiv.remove();
                    }, 5000);
                }
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
        });
    </script>
</body>

</html>