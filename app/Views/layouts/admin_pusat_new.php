<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard Kampus POLBAN - Admin Pusat' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
            color: #333;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            height: 100vh;
            background: linear-gradient(180deg, #4a90e2 0%, #357abd 100%);
            color: white;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 30px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-logo {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 24px;
        }

        .sidebar-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .sidebar-subtitle {
            font-size: 12px;
            opacity: 0.8;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 15px 25px;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .menu-item:hover,
        .menu-item.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-left-color: white;
        }

        .menu-item i {
            width: 20px;
            margin-right: 15px;
            font-size: 16px;
        }

        .menu-item span {
            font-size: 14px;
            font-weight: 500;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            min-height: 100vh;
        }

        /* Header */
        .header {
            background: white;
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            padding: 10px 15px 10px 40px;
            border: 1px solid #e0e0e0;
            border-radius: 25px;
            width: 300px;
            font-size: 14px;
            background: #f8f9fa;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }

        .notification-icon {
            position: relative;
            background: #f8f9fa;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .notification-icon:hover {
            background: #e9ecef;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: #4a90e2;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .user-name {
            font-size: 14px;
            font-weight: 500;
            color: #333;
        }

        /* Content Area */
        .content {
            padding: 30px;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .card-header {
            padding: 20px 25px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .card-body {
            padding: 25px;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
        }

        .stat-icon.blue {
            background: #4a90e2;
        }

        .stat-icon.green {
            background: #28a745;
        }

        .stat-icon.orange {
            background: #fd7e14;
        }

        .stat-icon.purple {
            background: #6f42c1;
        }

        .stat-content h4 {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }

        .stat-content p {
            font-size: 14px;
            color: #666;
            margin: 0;
        }

        /* Activity Feed */
        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 35px;
            height: 35px;
            background: #f8f9fa;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4a90e2;
            font-size: 14px;
        }

        .activity-content h5 {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .activity-content p {
            font-size: 13px;
            color: #666;
            margin-bottom: 5px;
        }

        .activity-time {
            font-size: 12px;
            color: #999;
        }

        /* Shortcuts */
        .shortcuts-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .shortcut-item {
            background: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .shortcut-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            color: #4a90e2;
        }

        .shortcut-icon {
            width: 60px;
            height: 60px;
            background: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 24px;
            color: #4a90e2;
            transition: all 0.3s ease;
        }

        .shortcut-item:hover .shortcut-icon {
            background: #4a90e2;
            color: white;
        }

        .shortcut-title {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .shortcut-desc {
            font-size: 12px;
            color: #666;
        }

        /* Table */
        .table-container {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }

        .table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .table td {
            font-size: 14px;
            color: #666;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
        }

        .status-lengkap {
            background: #d4edda;
            color: #155724;
        }

        .status-proses {
            background: #fff3cd;
            color: #856404;
        }

        .status-perlu-revisi {
            background: #f8d7da;
            color: #721c24;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #4a90e2;
            color: white;
        }

        .btn-primary:hover {
            background: #357abd;
        }

        /* Custom Button Styles */
        .btn-primary-custom {
            background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
        }

        .btn-primary-custom:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(92, 140, 191, 0.4);
            color: white;
            text-decoration: none;
        }

        .btn-outline-primary-custom {
            border: 2px solid #4a90e2;
            color: #4a90e2;
            background: transparent;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
        }

        .btn-outline-primary-custom:hover {
            background: #4a90e2;
            color: white;
            text-decoration: none;
        }

        .btn-outline-primary {
            border: 2px solid #4a90e2;
            color: #4a90e2;
            background: transparent;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
        }

        .btn-outline-primary:hover {
            background: #4a90e2;
            color: white;
            text-decoration: none;
        }

        /* Progress Bars */
        .progress-item {
            margin-bottom: 20px;
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .progress-label {
            font-size: 14px;
            font-weight: 500;
            color: #333;
        }

        .progress-value {
            font-size: 14px;
            font-weight: 600;
            color: #4a90e2;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #f0f0f0;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #4a90e2, #357abd);
            border-radius: 4px;
            transition: width 0.8s ease;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .header {
                padding: 15px 20px;
            }

            .search-box input {
                width: 200px;
            }

            .content {
                padding: 20px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .shortcuts-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <i class="fas fa-leaf"></i>
            </div>
            <div class="sidebar-title">GreenMetric Dashboard</div>
            <div class="sidebar-subtitle">POLBAN</div>
        </div>

        <nav class="sidebar-menu">
            <a href="<?= base_url('/admin-pusat/dashboard') ?>" class="menu-item active">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?= base_url('/admin-pusat/review') ?>" class="menu-item">
                <i class="fas fa-clipboard-check"></i>
                <span>Review Data</span>
            </a>
            <a href="<?= base_url('/admin-pusat/waste') ?>" class="menu-item">
                <i class="fas fa-trash-alt"></i>
                <span>Waste Management</span>
            </a>
            <a href="<?= base_url('/admin-pusat/manajemen-harga') ?>" class="menu-item">
                <i class="fas fa-money-bill-wave"></i>
                <span>Manajemen Harga</span>
            </a>
            <a href="<?= base_url('/admin-pusat/user-management') ?>" class="menu-item">
                <i class="fas fa-users"></i>
                <span>User Management</span>
            </a>
            <a href="<?= base_url('/admin-pusat/feature-toggle') ?>" class="menu-item">
                <i class="fas fa-toggle-on"></i>
                <span>Feature Toggle</span>
            </a>
            <a href="<?= base_url('/admin-pusat/laporan') ?>" class="menu-item">
                <i class="fas fa-chart-bar"></i>
                <span>Laporan</span>
            </a>
            <a href="<?= base_url('/admin-pusat/pengaturan') ?>" class="menu-item">
                <i class="fas fa-cog"></i>
                <span>Pengaturan</span>
            </a>
            <a href="<?= base_url('/auth/logout') ?>" class="menu-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <header class="header">
            <h1 class="header-title">Dashboard Kampus POLBAN</h1>
            <div class="header-right">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search...">
                </div>
                <div class="notification-icon" onclick="window.location.href='<?= base_url('/admin-pusat/notifikasi') ?>'">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge" id="notification-count" style="display: none;">0</span>
                </div>
                <div class="user-profile">
                    <div class="user-avatar">AP</div>
                    <span class="user-name"><?= isset($user['nama_lengkap']) ? $user['nama_lengkap'] : 'Admin POLBAN' ?></span>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="content">
            <?= $this->renderSection('content') ?>
        </div>
    </div>

    <script>
        // Animate progress bars on load
        document.addEventListener('DOMContentLoaded', function() {
            const progressBars = document.querySelectorAll('.progress-fill');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = width;
                }, 500);
            });

            // Set active menu item based on current URL
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            const menuItems = document.querySelectorAll('.menu-item');

            menuItems.forEach(item => {
                item.classList.remove('active');
                const href = item.getAttribute('href');
                if (href && currentPath.includes(href.replace(window.location.origin, ''))) {
                    item.classList.add('active');
                }
            });

            // If no specific match, highlight dashboard for admin-pusat paths
            if (currentPath.includes('/admin-pusat') && !document.querySelector('.menu-item.active')) {
                const dashboardLink = document.querySelector('a[href*="dashboard"]');
                if (dashboardLink) {
                    dashboardLink.classList.add('active');
                }
            }

            // Animate progress bars on load
            const progressBars = document.querySelectorAll('.progress-fill');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = width;
                }, 500);
            });
        });
        });

        // Mobile sidebar toggle
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('open');
        }

        // Auto refresh notifications
        function updateNotificationCount() {
            fetch('<?= base_url('/admin-pusat/get-unread-count') ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const badge = document.getElementById('notification-count');
                        if (data.unread_count > 0) {
                            badge.textContent = data.unread_count;
                            badge.style.display = 'flex';
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                })
                .catch(error => console.log('Notification check failed:', error));
        }

        // Update notification count on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateNotificationCount();
        });

        // Auto refresh every minute
        setInterval(updateNotificationCount, 60000);
    </script>

    <?= $this->renderSection('scripts') ?>
</body>

</html>