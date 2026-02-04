<?= $this->extend('layouts/admin_pusat_new') ?>

<?= $this->section('content') ?>

<style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #333;
            min-height: 100vh;
        }

        .header {
            background: linear-gradient(135deg, #5c8cbf 0%, #4a7ba7 100%);
            color: white;
            padding: 25px 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .header-left h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .header-left p {
            font-size: 16px;
            opacity: 0.9;
            font-weight: 400;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            background: rgba(255, 255, 255, 0.15);
            padding: 12px 20px;
            border-radius: 50px;
            backdrop-filter: blur(10px);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 12px 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 25px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 30px;
        }

        .nav-section {
            margin-bottom: 40px;
        }

        .nav-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .nav-link {
            background: white;
            color: #5c8cbf;
            padding: 20px 25px;
            border-radius: 15px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(92, 140, 191, 0.1), transparent);
            transition: left 0.5s ease;
        }

        .nav-link:hover::before {
            left: 100%;
        }

        .nav-link:hover,
        .nav-link.active {
            background: #5c8cbf;
            color: white;
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(92, 140, 191, 0.3);
        }

        .nav-link i {
            font-size: 20px;
            width: 24px;
        }

        .badge {
            background: #dc3545;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            margin-left: auto;
        }

        .filter-section {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 30px;
            margin-bottom: 40px;
            border: 1px solid rgba(92, 140, 191, 0.1);
        }

        .filter-section h5 {
            color: #5c8cbf;
            margin-bottom: 25px;
            font-size: 20px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            align-items: end;
        }

        .filter-group {
            position: relative;
        }

        .filter-group label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e1e8ed;
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-control:focus {
            outline: none;
            border-color: #5c8cbf;
            background: white;
            box-shadow: 0 0 0 4px rgba(92, 140, 191, 0.1);
        }

        .btn {
            padding: 15px 25px;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transition: all 0.3s ease;
            transform: translate(-50%, -50%);
        }

        .btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #5c8cbf 0%, #4a7ba7 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(92, 140, 191, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(92, 140, 191, 0.4);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .stats-section {
            margin-bottom: 40px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
        }

        .stat-card {
            background: white;
            padding: 30px 25px;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            text-align: center;
            border-top: 5px solid #5c8cbf;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(92, 140, 191, 0.05) 0%, rgba(74, 123, 167, 0.05) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .stat-card .icon {
            font-size: 40px;
            color: #5c8cbf;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }

        .stat-card .number {
            font-size: 36px;
            font-weight: 800;
            color: #333;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }

        .stat-card .label {
            color: #666;
            font-size: 14px;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }

        .card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            border: 1px solid rgba(92, 140, 191, 0.1);
            margin-bottom: 30px;
        }

        .card-header {
            background: linear-gradient(135deg, #5c8cbf 0%, #4a7ba7 100%);
            color: white;
            padding: 25px 30px;
            font-size: 20px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card-body {
            padding: 30px;
        }

        .notifikasi-list {
            list-style: none;
        }

        .notifikasi-item {
            padding: 25px;
            border-bottom: 1px solid #f0f0f0;
            border-left: 5px solid #5c8cbf;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .notifikasi-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(92, 140, 191, 0.05), transparent);
            transition: left 0.5s ease;
        }

        .notifikasi-item:hover::before {
            left: 100%;
        }

        .notifikasi-item:last-child {
            border-bottom: none;
        }

        .notifikasi-item:hover {
            background: #f8f9fa;
            transform: translateX(10px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .notifikasi-item.unread {
            background: linear-gradient(135deg, #fff3e0 0%, #ffecb3 100%);
            border-left-color: #ff9800;
        }

        .notifikasi-item.unread:hover {
            background: linear-gradient(135deg, #ffe0b2 0%, #ffcc80 100%);
        }

        .notifikasi-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .notifikasi-item h5 {
            color: #333;
            margin-bottom: 0;
            font-size: 16px;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }

        .notifikasi-item p {
            color: #666;
            font-size: 14px;
            margin-bottom: 12px;
            line-height: 1.6;
            position: relative;
            z-index: 1;
        }

        .notifikasi-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: #999;
            position: relative;
            z-index: 1;
        }

        .notifikasi-time {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .notifikasi-type {
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .type-data_masuk {
            background: #e3f2fd;
            color: #1976d2;
        }

        .type-approval {
            background: #e8f5e8;
            color: #388e3c;
        }

        .type-rejection {
            background: #ffebee;
            color: #d32f2f;
        }

        .type-deadline {
            background: #fff3e0;
            color: #f57c00;
        }

        .type-revisi_selesai {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        .mark-read-btn {
            background: none;
            border: none;
            color: #5c8cbf;
            cursor: pointer;
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .mark-read-btn:hover {
            background: #5c8cbf;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 80px 40px;
            color: #666;
        }

        .empty-state i {
            font-size: 80px;
            color: #ddd;
            margin-bottom: 25px;
            display: block;
        }

        .empty-state h3 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #333;
        }

        .empty-state p {
            font-size: 16px;
            font-weight: 500;
            max-width: 400px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 30px;
        }

        .pagination a,
        .pagination span {
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .pagination a {
            background: white;
            color: #5c8cbf;
            border: 2px solid #e1e8ed;
        }

        .pagination a:hover {
            background: #5c8cbf;
            color: white;
            border-color: #5c8cbf;
        }

        .pagination .current {
            background: #5c8cbf;
            color: white;
            border: 2px solid #5c8cbf;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 20px;
                text-align: center;
                padding: 0 20px;
            }

            .container {
                padding: 30px 20px;
            }

            .nav-links {
                grid-template-columns: 1fr;
            }

            .filter-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 20px;
            }

            .notifikasi-item {
                padding: 20px;
            }

            .notifikasi-header {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>

<!-- Navigation Section -->
<div class="nav-section">
    <div class="nav-links">
        <a href="<?= base_url('/admin-pusat/dashboard') ?>" class="nav-link">
            <i class="fas fa-home"></i>
            Dashboard
        </a>
        <a href="<?= base_url('/admin-pusat/monitoring') ?>" class="nav-link">
                    <i class="fas fa-chart-line"></i>
                    Monitoring Unit
                </a>
                <a href="/admin-pusat/notifikasi" class="nav-link active">
                    <i class="fas fa-bell"></i>
                    Notifikasi
                </a>
                <a href="/report" class="nav-link">
                    <i class="fas fa-file-export"></i>
                    Laporan
                </a>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <h5><i class="fas fa-filter"></i>Filter Notifikasi</h5>
            <form method="GET" action="/admin-pusat/notifikasi" class="filter-form">
                <div class="filter-grid">
                    <div class="filter-group">
                        <label>Status:</label>
                        <select name="status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="unread" <?= (($filters['status'] ?? '') == 'unread') ? 'selected' : '' ?>>Belum Dibaca</option>
                            <option value="read" <?= (($filters['status'] ?? '') == 'read') ? 'selected' : '' ?>>Sudah Dibaca</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Tipe:</label>
                        <select name="tipe" class="form-control">
                            <option value="">Semua Tipe</option>
                            <option value="data_masuk" <?= (($filters['tipe'] ?? '') == 'data_masuk') ? 'selected' : '' ?>>Pengiriman Data</option>
                            <option value="approval" <?= (($filters['tipe'] ?? '') == 'approval') ? 'selected' : '' ?>>Persetujuan</option>
                            <option value="rejection" <?= (($filters['tipe'] ?? '') == 'rejection') ? 'selected' : '' ?>>Revisi</option>
                            <option value="deadline" <?= (($filters['tipe'] ?? '') == 'deadline') ? 'selected' : '' ?>>Pengingat</option>
                            <option value="revisi_selesai" <?= (($filters['tipe'] ?? '') == 'revisi_selesai') ? 'selected' : '' ?>>Revisi Selesai</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Pencarian:</label>
                        <input type="text" name="search" class="form-control" placeholder="Cari notifikasi..." value="<?= htmlspecialchars($filters['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <div class="filter-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary form-control">
                            <i class="fas fa-search"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Statistics Section -->
        <div class="stats-section">
            <div class="stats-grid">
                <?php
                $totalNotifikasi = isset($notifikasi) ? count($notifikasi) : 0;
                $unreadCount = 0;
                $readCount = 0;

                if (isset($notifikasi) && is_array($notifikasi)) {
                    foreach ($notifikasi as $notif) {
                        if (!$notif['is_read']) {
                            $unreadCount++;
                        } else {
                            $readCount++;
                        }
                    }
                }
                ?>
                <div class="stat-card">
                    <div class="icon"><i class="fas fa-bell"></i></div>
                    <div class="number"><?= $totalNotifikasi ?></div>
                    <div class="label">Total Notifikasi</div>
                </div>
                <div class="stat-card">
                    <div class="icon"><i class="fas fa-envelope"></i></div>
                    <div class="number"><?= $unreadCount ?></div>
                    <div class="label">Belum Dibaca</div>
                </div>
                <div class="stat-card">
                    <div class="icon"><i class="fas fa-envelope-open"></i></div>
                    <div class="number"><?= $readCount ?></div>
                    <div class="label">Sudah Dibaca</div>
                </div>
                <div class="stat-card">
                    <div class="icon"><i class="fas fa-clock"></i></div>
                    <div class="number"><?= date('H:i') ?></div>
                    <div class="label">Waktu Sekarang</div>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-list"></i> Daftar Notifikasi
                <?php if ($unreadCount > 0): ?>
                    <span class="badge" style="margin-left: auto;"><?= $unreadCount ?> Belum Dibaca</span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (empty($notifikasi)): ?>
                    <div class="empty-state">
                        <i class="fas fa-bell-slash"></i>
                        <h3>Tidak Ada Notifikasi</h3>
                        <p>Belum ada notifikasi yang tersedia. Notifikasi akan muncul ketika ada aktivitas baru dari Admin Unit.</p>
                    </div>
                <?php else: ?>
                    <ul class="notifikasi-list">
                        <?php foreach ($notifikasi as $notif): ?>
                            <li class="notifikasi-item <?= !$notif['is_read'] ? 'unread' : '' ?>" data-id="<?= $notif['id'] ?>">
                                <div class="notifikasi-header">
                                    <h5><?= htmlspecialchars($notif['judul'], ENT_QUOTES, 'UTF-8') ?></h5>
                                    <?php if (!$notif['is_read']): ?>
                                        <button class="mark-read-btn" onclick="markAsRead(<?= $notif['id'] ?>)">
                                            <i class="fas fa-check"></i> Tandai Dibaca
                                        </button>
                                    <?php endif; ?>
                                </div>
                                <p><?= htmlspecialchars($notif['pesan'], ENT_QUOTES, 'UTF-8') ?></p>
                                <div class="notifikasi-meta">
                                    <div class="notifikasi-time">
                                        <i class="fas fa-clock"></i>
                                        <?= date('d/m/Y H:i', strtotime($notif['created_at'])) ?>
                                    </div>
                                    <div class="notifikasi-type type-<?= $notif['tipe_notifikasi'] ?? 'data_masuk' ?>">
                                        <?php
                                        $tipeText = [
                                            'data_masuk' => 'Pengiriman',
                                            'approval' => 'Persetujuan',
                                            'rejection' => 'Revisi',
                                            'deadline' => 'Pengingat',
                                            'revisi_selesai' => 'Revisi Selesai'
                                        ];
                                        echo $tipeText[$notif['tipe_notifikasi'] ?? 'data_masuk'] ?? 'Notifikasi';
                                        ?>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <!-- Pagination -->
                    <?php if (isset($pager)): ?>
                        <div class="pagination">
                            <?= $pager->links() ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Action Buttons -->
        <div style="text-align: center; margin-top: 30px;">
            <?php if ($unreadCount > 0): ?>
                <button type="button" class="btn btn-primary" onclick="markAllAsRead()">
                    <i class="fas fa-check-double"></i> Tandai Semua Dibaca
                </button>
            <?php endif; ?>
            <a href="/admin-pusat/dashboard" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>

    <script>
        // Mark single notification as read
        function markAsRead(notificationId) {
            const formData = new FormData();
            formData.append('notification_id', notificationId);

            fetch('<?= base_url('/admin-pusat/mark-notification-read') ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update UI
                        const notifItem = document.querySelector(`[data-id="${notificationId}"]`);
                        if (notifItem) {
                            notifItem.classList.remove('unread');
                            const markBtn = notifItem.querySelector('.mark-read-btn');
                            if (markBtn) {
                                markBtn.remove();
                            }
                        }
                        
                        // Update counter
                        updateUnreadCount();
                        showToast('Notifikasi ditandai sebagai sudah dibaca', 'success');
                    } else {
                        showToast(data.message || 'Gagal menandai notifikasi', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Terjadi kesalahan', 'error');
                });
        }

        // Mark all notifications as read
        function markAllAsRead() {
            if (!confirm('Tandai semua notifikasi sebagai sudah dibaca?')) {
                return;
            }

            fetch('<?= base_url('/admin-pusat/mark-all-notifications-read') ?>', {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update UI
                        document.querySelectorAll('.notifikasi-item.unread').forEach(item => {
                            item.classList.remove('unread');
                            const markBtn = item.querySelector('.mark-read-btn');
                            if (markBtn) {
                                markBtn.remove();
                            }
                        });
                        
                        // Hide mark all button
                        const markAllBtn = document.querySelector('button[onclick="markAllAsRead()"]');
                        if (markAllBtn) {
                            markAllBtn.style.display = 'none';
                        }
                        
                        // Update counter
                        updateUnreadCount();
                        showToast('Semua notifikasi ditandai sebagai sudah dibaca', 'success');
                    } else {
                        showToast(data.message || 'Gagal menandai notifikasi', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Terjadi kesalahan', 'error');
                });
        }

        // Update unread count
        function updateUnreadCount() {
            fetch('<?= base_url('/admin-pusat/get-unread-count') ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update parent window notification count if in iframe
                        if (window.parent && window.parent !== window) {
                            window.parent.postMessage({
                                type: 'updateNotificationCount',
                                count: data.unread_count
                            }, '*');
                        }
                    }
                })
                .catch(error => console.error('Error updating count:', error));
        }

        // Show toast notification
        function showToast(message, type = 'info') {
            // Create toast element
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                padding: 15px 20px;
                border-radius: 8px;
                color: white;
                font-weight: 500;
                min-width: 300px;
                animation: slideInRight 0.3s ease;
            `;

            // Set background color based on type
            switch (type) {
                case 'success':
                    toast.style.backgroundColor = '#28a745';
                    break;
                case 'error':
                    toast.style.backgroundColor = '#dc3545';
                    break;
                case 'warning':
                    toast.style.backgroundColor = '#ffc107';
                    toast.style.color = '#212529';
                    break;
                default:
                    toast.style.backgroundColor = '#17a2b8';
            }

            toast.innerHTML = `
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'times-circle' : 'info-circle'}"></i>
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" 
                            style="background: none; border: none; color: inherit; font-size: 18px; cursor: pointer; margin-left: auto;">
                        &times;
                    </button>
                </div>
            `;

            document.body.appendChild(toast);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 5000);
        }

        // Auto refresh notifications every 30 seconds
        setInterval(() => {
            location.reload();
        }, 30000);
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const notifItem = document.querySelector(`[data-id="${notificationId}"]`);
                        if (notifItem) {
                            notifItem.classList.remove('unread');
                            const markBtn = notifItem.querySelector('.mark-read-btn');
                            if (markBtn) {
                                markBtn.remove();
                            }
                        }

                        // Update counter
                        updateNotificationCounter();
                        showToast('Notifikasi ditandai sebagai dibaca', 'success');
                    } else {
                        showToast('Gagal menandai notifikasi', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Terjadi kesalahan', 'error');
                });
        }

        // Mark all notifications as read
        function markAllAsRead() {
            if (!confirm('Yakin ingin menandai semua notifikasi sebagai dibaca?')) {
                return;
            }

            fetch('/api/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove unread class from all items
                        document.querySelectorAll('.notifikasi-item.unread').forEach(item => {
                            item.classList.remove('unread');
                            const markBtn = item.querySelector('.mark-read-btn');
                            if (markBtn) {
                                markBtn.remove();
                            }
                        });

                        // Update counter and reload page
                        showToast('Semua notifikasi ditandai sebagai dibaca', 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showToast('Gagal menandai semua notifikasi', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Terjadi kesalahan', 'error');
                });
        }

        // Update notification counter
        function updateNotificationCounter() {
            const unreadItems = document.querySelectorAll('.notifikasi-item.unread').length;
            const badge = document.querySelector('.card-header .badge');
            if (badge) {
                if (unreadItems > 0) {
                    badge.textContent = `${unreadItems} Belum Dibaca`;
                } else {
                    badge.remove();
                }
            }
        }

        // Show toast notification
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `alert alert-${type}`;
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                min-width: 300px;
                padding: 15px 20px;
                border-radius: 8px;
                color: white;
                font-weight: 600;
                box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            `;

            if (type === 'success') {
                toast.style.background = '#28a745';
            } else if (type === 'error') {
                toast.style.background = '#dc3545';
            } else {
                toast.style.background = '#17a2b8';
            }

            toast.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info'}-circle"></i>
                ${message}
                <button onclick="this.parentElement.remove()" style="float: right; background: none; border: none; color: white; font-size: 18px; cursor: pointer;">&times;</button>
            `;

            document.body.appendChild(toast);

            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 5000);
        }

        // Auto refresh every 2 minutes
        setInterval(() => {
            console.log('Checking for new notifications...');
            // Optional: Check for new notifications without full reload
        }, 120000);

        // Add click handlers for notification items
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.notifikasi-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    if (!e.target.classList.contains('mark-read-btn') && !e.target.closest('.mark-read-btn')) {
                        // Optional: Handle notification click (e.g., navigate to related page)
                        console.log('Notification clicked:', this.dataset.id);
                    }
                });
            });

            // Animate notification items on load
            const notifItems = document.querySelectorAll('.notifikasi-item');
            notifItems.forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'translateX(-20px)';
                setTimeout(() => {
                    item.style.transition = 'all 0.5s ease';
                    item.style.opacity = '1';
                    item.style.transform = 'translateX(0)';
                }, index * 100);
            });
        });
    </script>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Additional scripts if needed
</script>
<?= $this->endSection() ?>