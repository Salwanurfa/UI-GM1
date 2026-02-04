<?php
/**
 * Dashboard Admin Pusat - UI GreenMetric POLBAN
 * Dashboard untuk admin pusat
 */

// Safety checks for variables
$stats = $stats ?? [];
$recentSubmissions = $recentSubmissions ?? [];
$recentPriceChanges = $recentPriceChanges ?? [];
$wasteByType = $wasteByType ?? [];

/**
 * Helper function untuk menampilkan nilai stats dengan fallback
 */
if (!function_exists('displayStat')) {
    function displayStat($stats, $key, $default = 0) {
        return isset($stats[$key]) ? $stats[$key] : $default;
    }
}

/**
 * Format number untuk display
 */
if (!function_exists('formatNumber')) {
    function formatNumber($number) {
        return number_format($number, 0, ',', '.');
    }
}

/**
 * Format currency untuk display
 */
if (!function_exists('formatCurrency')) {
    function formatCurrency($amount) {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard Admin Pusat' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Mobile Responsive CSS -->
    <link href="<?= base_url('/css/mobile-responsive.css') ?>" rel="stylesheet">
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <h1><i class="fas fa-tachometer-alt"></i> Dashboard Admin Pusat</h1>
            <p>Monitoring dan Kelola Sistem Waste Management POLBAN</p>
        </div>

        <!-- Disabled Features Alert -->
        <?= renderDisabledFeaturesAlert('admin_pusat') ?>

        <!-- Statistics Cards - Feature Toggle: dashboard_statistics_cards -->
        <?php if (isFeatureEnabled('dashboard_statistics_cards', 'admin_pusat')): ?>
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3><?= displayStat($stats, 'total_users') ?></h3>
                    <p>Total Users Aktif</p>
                </div>
            </div>
            
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3><?= displayStat($stats, 'disetujui') ?></h3>
                    <p>Data Disetujui</p>
                </div>
            </div>
            
            <div class="stat-card danger">
                <div class="stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-content">
                    <h3><?= displayStat($stats, 'ditolak') ?></h3>
                    <p>Data Ditolak</p>
                </div>
            </div>
            
            <div class="stat-card secondary">
                <div class="stat-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-content">
                    <h3><?= formatCurrency(displayStat($stats, 'total_nilai')) ?></h3>
                    <p>Total Nilai Jual</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Main Content Area -->
        <div class="content-grid">
            <!-- Recent Waste Submissions - Feature Toggle: dashboard_waste_summary -->
            <?php if (isFeatureEnabled('dashboard_waste_summary', 'admin_pusat')): ?>
            <div class="content-main">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-inbox"></i> Data Waste Terbaru</h3>
                        <div class="card-actions">
                            <a href="<?= base_url('/admin-pusat/waste') ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye"></i> Lihat Semua
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($recentSubmissions)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Unit</th>
                                            <th>Jenis Sampah</th>
                                            <th>Berat (kg)</th>
                                            <th>Nilai</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach ($recentSubmissions as $submission): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td>
                                                <small><?= date('d/m/Y', strtotime($submission['created_at'])) ?></small>
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($submission['nama_unit'] ?? 'N/A') ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary"><?= htmlspecialchars($submission['jenis_sampah']) ?></span>
                                            </td>
                                            <td><strong><?= number_format($submission['berat_kg'], 2) ?></strong></td>
                                            <td>
                                                <?php if ($submission['kategori_sampah'] === 'bisa_dijual'): ?>
                                                    <strong class="text-success"><?= formatCurrency($submission['nilai_rupiah'] ?? 0) ?></strong>
                                                <?php else: ?>
                                                    <span class="text-muted">Rp 0</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                $statusBadge = [
                                                    'draft' => 'secondary',
                                                    'dikirim' => 'warning',
                                                    'disetujui' => 'success',
                                                    'ditolak' => 'danger'
                                                ];
                                                $badgeClass = $statusBadge[$submission['status']] ?? 'secondary';
                                                $statusText = [
                                                    'draft' => 'Draft',
                                                    'dikirim' => 'Dikirim',
                                                    'disetujui' => 'Setujui',
                                                    'ditolak' => 'Tolak'
                                                ];
                                                ?>
                                                <span class="badge bg-<?= $badgeClass ?>">
                                                    <?= $statusText[$submission['status']] ?? ucfirst($submission['status']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Tidak ada data waste</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Sidebar Content -->
            <div class="content-sidebar">
                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="quick-actions-vertical">
                            <a href="<?= base_url('/admin-pusat/manajemen-harga') ?>" class="quick-action-btn">
                                <i class="fas fa-money-bill-wave"></i>
                                <span>Kelola Harga Sampah</span>
                            </a>
                            <a href="<?= base_url('/admin-pusat/waste') ?>" class="quick-action-btn">
                                <i class="fas fa-trash-alt"></i>
                                <span>Waste Management</span>
                            </a>
                            <a href="<?= base_url('/admin-pusat/laporan-waste') ?>" class="quick-action-btn">
                                <i class="fas fa-chart-pie"></i>
                                <span>Laporan Waste</span>
                            </a>
                            <a href="<?= base_url('/admin-pusat/user-management') ?>" class="quick-action-btn">
                                <i class="fas fa-users-cog"></i>
                                <span>Kelola Users</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Two Column Layout for Price Changes and Monthly Summary -->
                <div class="sidebar-two-column">
                    <!-- Monthly Summary - Ringkasan Bulanan -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-calendar-alt"></i> Ringkasan Bulanan <?= date('Y') ?></h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($monthlySummary)): ?>
                                <div class="monthly-summary-grid">
                                    <?php foreach ($monthlySummary as $month): ?>
                                    <div class="month-card">
                                        <div class="month-name"><?= $month['month_name'] ?></div>
                                        <div class="month-stats">
                                            <div class="month-stat">
                                                <small>Data:</small>
                                                <strong><?= $month['data_count'] ?></strong>
                                            </div>
                                            <div class="month-stat">
                                                <small>Berat:</small>
                                                <strong><?= number_format($month['total_weight'], 0) ?> kg</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">Belum ada data bulan ini</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Recent Price Changes - Feature Toggle: dashboard_recent_activity -->
                    <?php if (isFeatureEnabled('dashboard_recent_activity', 'admin_pusat')): ?>
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-history"></i> Perubahan Harga Terbaru (Hari Ini)</h3>
                            <div class="card-actions">
                                <a href="<?= base_url('/admin-pusat/manajemen-harga/logs') ?>" class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-history"></i> Lihat Semua
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($recentPriceChanges)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Jenis Sampah</th>
                                                <th>Harga Lama</th>
                                                <th>Harga Baru</th>
                                                <th>Perubahan</th>
                                                <th>Admin</th>
                                                <th>Waktu</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentPriceChanges as $change): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= htmlspecialchars($change['jenis_sampah']) ?></strong>
                                                    <?php if (!empty($change['nama_jenis'])): ?>
                                                        <br><small class="text-muted"><?= htmlspecialchars($change['nama_jenis']) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($change['harga_lama']): ?>
                                                        <span class="text-muted text-decoration-line-through">
                                                            <?= formatCurrency($change['harga_lama']) ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <strong class="text-success">
                                                        <?= formatCurrency($change['harga_baru']) ?>
                                                    </strong>
                                                </td>
                                                <td>
                                                    <?php 
                                                    if ($change['harga_lama']) {
                                                        $selisih = $change['harga_baru'] - $change['harga_lama'];
                                                        $persen = ($change['harga_lama'] > 0) ? ($selisih / $change['harga_lama']) * 100 : 0;
                                                        $badgeClass = $selisih > 0 ? 'success' : 'danger';
                                                        $icon = $selisih > 0 ? 'arrow-up' : 'arrow-down';
                                                    ?>
                                                        <span class="badge bg-<?= $badgeClass ?>">
                                                            <i class="fas fa-<?= $icon ?>"></i>
                                                            <?= number_format(abs($persen), 1) ?>%
                                                        </span>
                                                    <?php } else { ?>
                                                        <span class="badge bg-info">
                                                            <i class="fas fa-plus"></i> Baru
                                                        </span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <small>Admin</small>
                                                </td>
                                                <td>
                                                    <small><?= $change['waktu_perubahan'] ?? date('d/m/Y H:i', strtotime($change['created_at'])) ?></small>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-history fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">Belum ada perubahan harga hari ini</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
                </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto refresh every 5 minutes
        setTimeout(function() {
            location.reload();
        }, 300000);

        // Animate progress bars on load
        document.addEventListener('DOMContentLoaded', function() {
            const progressBars = document.querySelectorAll('.progress-bar');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = width;
                }, 500);
            });
        });
    </script>
    <!-- Mobile Menu JS -->
    <script src="<?= base_url('/js/mobile-menu.js') ?>"></script>
</body>
</html>

<style>
/* ===== MAIN LAYOUT ===== */
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
    max-width: calc(100vw - 280px);
    overflow-x: hidden;
}

/* ===== DASHBOARD HEADER ===== */
.dashboard-header {
    margin-bottom: 30px;
    padding: 20px 0;
    border-bottom: 2px solid #e9ecef;
}

.dashboard-header h1 {
    color: #2c3e50;
    margin-bottom: 10px;
    font-size: 28px;
    font-weight: 700;
}

.dashboard-header p {
    color: #6c757d;
    font-size: 16px;
    margin: 0;
}

/* ===== STATISTICS CARDS ===== */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    gap: 20px;
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
    min-height: 100px;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.stat-card.primary { border-left-color: #007bff; }
.stat-card.success { border-left-color: #28a745; }
.stat-card.warning { border-left-color: #ffc107; }
.stat-card.danger { border-left-color: #dc3545; }
.stat-card.info { border-left-color: #17a2b8; }
.stat-card.secondary { border-left-color: #6c757d; }
.stat-card.purple { border-left-color: #6f42c1; }

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    flex-shrink: 0;
}

.stat-card.primary .stat-icon { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.stat-card.warning .stat-icon { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.stat-card.success .stat-icon { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.stat-card.danger .stat-icon { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
.stat-card.info .stat-icon { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
.stat-card.secondary .stat-icon { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
.stat-card.purple .stat-icon { background: linear-gradient(135deg, #9d50bb 0%, #6e48aa 100%); }

.stat-content {
    flex: 1;
}

.stat-content h3 {
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 5px 0;
    color: #2c3e50;
}

.stat-content p {
    margin: 0;
    color: #6c757d;
    font-weight: 500;
    font-size: 14px;
}

/* ===== CONTENT GRID ===== */
.content-grid {
    display: grid;
    grid-template-columns: 1fr 800px;
    gap: 30px;
    margin-bottom: 30px;
}

.content-main {
    min-width: 0; /* Prevents overflow */
}

.content-sidebar {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* Sidebar Two Column Layout */
.sidebar-two-column {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

/* ===== CARDS ===== */
.card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    overflow: hidden;
    border: none;
}

.card-header {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    color: white;
    padding: 20px 25px;
    display: flex;
    align-items: center;
    justify-content: space-between;
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

.card-actions .btn {
    border-radius: 8px;
    font-weight: 500;
}

.card-body {
    padding: 25px;
}

/* ===== TABLES ===== */
.table-responsive {
    border-radius: 10px;
    overflow-x: auto;
}

.table {
    margin-bottom: 0;
    white-space: nowrap;
}

.table th {
    background: #f8f9fa;
    border: none;
    font-weight: 600;
    color: #2c3e50;
    padding: 15px;
    white-space: nowrap;
}

.table td {
    border: none;
    padding: 15px;
    vertical-align: middle;
}

.table tbody tr {
    border-bottom: 1px solid #e9ecef;
}

.table tbody tr:hover {
    background: #f8f9fa;
}

/* ===== QUICK ACTIONS ===== */
.quick-actions-vertical {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.quick-action-btn {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
    text-decoration: none;
    color: #2c3e50;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.quick-action-btn:hover {
    background: white;
    border-color: #007bff;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2);
    color: #007bff;
    text-decoration: none;
}

.quick-action-btn i {
    width: 20px;
    text-align: center;
    font-size: 16px;
}

.quick-action-btn span {
    font-weight: 500;
    font-size: 14px;
}

/* ===== ACTIVITY LIST ===== */
.activity-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.activity-item {
    display: flex;
    gap: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
}

.activity-icon {
    width: 40px;
    height: 40px;
    background: #007bff;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
}

.activity-content {
    flex: 1;
}

.activity-content p {
    margin: 0 0 5px 0;
    font-size: 14px;
}

.activity-content small {
    font-size: 12px;
}

/* ===== WASTE GRID ===== */
.waste-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.waste-item {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    border-left: 4px solid #007bff;
}

.waste-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e9ecef;
}

.waste-header h5 {
    margin: 0;
    color: #2c3e50;
    font-weight: 700;
    font-size: 20px !important;
}

.waste-total {
    font-size: 12px;
    color: #6c757d;
    background: white;
    padding: 4px 8px;
    border-radius: 12px;
}

.waste-stats {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.waste-stat {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 14px;
}

.stat-label {
    color: #6c757d;
}

/* ===== EMPTY STATE ===== */
.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #6c757d;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 15px;
    opacity: 0.5;
}

.empty-state p {
    margin: 0;
    font-size: 16px;
}

/* ===== MONTHLY SUMMARY GRID ===== */
.monthly-summary-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}

.month-card {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 12px;
    border-left: 3px solid #007bff;
    transition: all 0.3s ease;
}

.month-card:hover {
    background: white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.month-name {
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 8px;
    font-size: 14px;
    text-align: center;
}

.month-stats {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.month-stat {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12px;
}

.month-stat small {
    color: #6c757d;
}

.month-stat strong {
    color: #2c3e50;
    font-size: 13px;
}

/* ===== BUTTONS ===== */
.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 1400px) {
    .content-grid {
        grid-template-columns: 1fr 700px;
    }
}

@media (max-width: 1200px) {
    .content-grid {
        grid-template-columns: 1fr 600px;
    }
}

@media (max-width: 992px) {
    .content-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .content-sidebar {
        order: -1;
    }

    .sidebar-two-column {
        grid-template-columns: 1fr;
        gap: 15px;
    }
}

@media (max-width: 768px) {
    .main-content {
        margin-left: 0;
        padding: 15px 10px;
        max-width: 100vw;
        overflow-x: hidden;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .stat-card {
        padding: 15px;
        gap: 15px;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        font-size: 20px;
    }

    .stat-content h3 {
        font-size: 22px;
    }

    .stat-content p {
        font-size: 12px;
    }
    
    .dashboard-header {
        padding: 15px 0;
        margin-bottom: 20px;
    }

    .dashboard-header h1 {
        font-size: 22px;
    }

    .dashboard-header p {
        font-size: 14px;
    }
    
    .card-header {
        padding: 12px 15px;
        flex-direction: column;
        align-items: flex-start !important;
        gap: 10px;
    }

    .card-header h3 {
        font-size: 16px;
    }

    .card-actions {
        width: 100%;
    }

    .card-actions .btn {
        width: 100%;
        font-size: 13px;
        padding: 8px 12px;
    }
    
    .card-body {
        padding: 15px 10px;
    }
    
    .waste-grid {
        grid-template-columns: 1fr;
    }

    /* Fix content overflow */
    .card {
        max-width: 100%;
        overflow-x: hidden;
    }

    /* Table responsive fixes */
    .table-responsive {
        max-width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin: 0 -10px;
        padding: 0 10px;
    }

    .table {
        font-size: 11px;
        min-width: 600px;
    }

    .table th,
    .table td {
        padding: 8px 6px;
        font-size: 11px;
    }

    .table th {
        font-size: 10px;
        text-transform: uppercase;
    }

    .table .badge {
        font-size: 9px;
        padding: 3px 6px;
    }

    /* Quick actions */
    .quick-actions-vertical {
        gap: 8px;
    }

    .quick-action-btn {
        padding: 12px;
        font-size: 13px;
    }

    .quick-action-btn i {
        font-size: 14px;
    }

    .quick-action-btn span {
        font-size: 13px;
    }

    /* Empty state */
    .empty-state {
        padding: 30px 15px;
    }

    .empty-state i {
        font-size: 36px;
    }

    .empty-state p {
        font-size: 14px;
    }

    .row {
        margin-left: 0;
        margin-right: 0;
    }

    .row > [class*="col-"] {
        padding-left: 10px;
        padding-right: 10px;
    }

    /* Content grid */
    .content-grid {
        gap: 15px;
    }

    .content-main,
    .content-sidebar {
        min-width: 0;
        max-width: 100%;
    }

    /* Monthly summary responsive */
    .monthly-summary-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }

    .month-card {
        padding: 10px;
    }

    .month-name {
        font-size: 12px;
    }

    .month-stat {
        font-size: 10px;
    }

    .month-stat strong {
        font-size: 11px;
    }
}

@media (max-width: 480px) {
    .main-content {
        padding: 10px 5px;
    }

    .dashboard-header h1 {
        font-size: 20px;
    }

    .stat-card {
        padding: 12px;
    }

    .stat-icon {
        width: 45px;
        height: 45px;
        font-size: 18px;
    }

    .stat-content h3 {
        font-size: 20px;
    }

    .card-header {
        padding: 10px 12px;
    }

    .card-body {
        padding: 12px 8px;
    }

    .table {
        font-size: 10px;
        min-width: 550px;
    }

    .table th,
    .table td {
        padding: 6px 4px;
    }

    .quick-action-btn {
        padding: 10px;
    }
}
</style>