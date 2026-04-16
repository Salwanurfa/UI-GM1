<?php
/**
 * Transportation Admin Pusat - UI GreenMetric POLBAN
 * Dashboard untuk statistik transportasi
 */

// Safety checks for variables
$summary_stats = $summary_stats ?? [];
$transport_stats = $transport_stats ?? [];

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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Statistik Transportasi' ?></title>
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
            <h1><i class="fas fa-car"></i> Statistik Transportasi Kampus</h1>
            <p>Monitoring dan rekapitulasi data kendaraan dari Security</p>
            <div class="header-actions">
                <a href="<?= base_url('/admin-pusat/transportation/export-pdf') ?>" 
                   class="btn btn-danger btn-sm me-2" 
                   target="_blank">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
                <a href="<?= base_url('/admin-pusat/transportation/export-excel') ?>" 
                   class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-car"></i>
                </div>
                <div class="stat-content">
                    <h3><?= displayStat($summary_stats, 'total_vehicles') ?></h3>
                    <p>Total Kendaraan</p>
                </div>
            </div>
            
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-leaf"></i>
                </div>
                <div class="stat-content">
                    <h3><?= displayStat($summary_stats, 'total_zev') ?></h3>
                    <p>ZEV</p>
                </div>
            </div>
            
            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-bus"></i>
                </div>
                <div class="stat-content">
                    <h3><?= displayStat($summary_stats, 'total_shuttle') ?></h3>
                    <p>Shuttle</p>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="content-grid">
            <!-- Breakdown Charts -->
            <div class="content-main">
                <div class="row">
                    <!-- Category Breakdown -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h3><i class="fas fa-chart-pie"></i> Total data per Kategori Kendaraan</h3>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($summary_stats['category_breakdown'])): ?>
                                    <?php foreach ($summary_stats['category_breakdown'] as $category): ?>
                                        <div class="breakdown-item">
                                            <div class="breakdown-label">
                                                <span class="badge bg-primary"><?= esc($category['kategori_kendaraan']) ?></span>
                                            </div>
                                            <div class="breakdown-value">
                                                <?= number_format($category['total']) ?> Unit
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <i class="fas fa-chart-pie fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Belum ada data kategori kendaraan</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Fuel Type Breakdown -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h3><i class="fas fa-gas-pump"></i> Total data per Jenis Bahan Bakar</h3>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($summary_stats['fuel_breakdown'])): ?>
                                    <?php foreach ($summary_stats['fuel_breakdown'] as $fuel): ?>
                                        <div class="breakdown-item">
                                            <div class="breakdown-label">
                                                <span class="badge bg-success"><?= esc($fuel['jenis_bahan_bakar']) ?></span>
                                            </div>
                                            <div class="breakdown-value">
                                                <?= number_format($fuel['total']) ?> Unit
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <i class="fas fa-gas-pump fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Belum ada data jenis bahan bakar</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-table"></i> Rekapitulasi Data Transportasi</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($transport_stats)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Periode</th>
                                            <th>Kategori Kendaraan</th>
                                            <th>Jenis Bahan Bakar</th>
                                            <th>Jumlah Total</th>
                                            <th>Petugas Input</th>
                                            <th>Tanggal Input</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($transport_stats as $index => $entry): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td>
                                                    <strong><?= esc($entry['periode']) ?></strong>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        <span class="badge bg-primary">
                                                            <?php
                                                            $kategoriIcons = [
                                                                'Roda Dua' => 'fa-motorcycle',
                                                                'Roda Empat' => 'fa-car',
                                                                'Sepeda' => 'fa-bicycle',
                                                                'Kendaraan Umum' => 'fa-bus'
                                                            ];
                                                            ?>
                                                            <i class="fas <?= $kategoriIcons[$entry['kategori_kendaraan']] ?? 'fa-car' ?>"></i>
                                                            <?= esc($entry['kategori_kendaraan']) ?>
                                                        </span>
                                                        <?php if ($entry['is_zev']): ?>
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-leaf"></i> ZEV
                                                            </span>
                                                        <?php endif; ?>
                                                        <?php if ($entry['is_shuttle']): ?>
                                                            <span class="badge bg-info">
                                                                <i class="fas fa-bus"></i> Shuttle
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">
                                                        <?php
                                                        $bahanBakarIcons = [
                                                            'Bensin' => 'fa-gas-pump',
                                                            'Diesel' => 'fa-oil-can',
                                                            'Listrik' => 'fa-bolt',
                                                            'Non-BBM' => 'fa-leaf'
                                                        ];
                                                        ?>
                                                        <i class="fas <?= $bahanBakarIcons[$entry['jenis_bahan_bakar']] ?? 'fa-gas-pump' ?>"></i>
                                                        <?= esc($entry['jenis_bahan_bakar']) ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <strong><?= number_format($entry['jumlah_total']) ?></strong>
                                                </td>
                                                <td>
                                                    <i class="fas fa-user-shield text-muted me-1"></i>
                                                    <?= esc($entry['petugas_nama']) ?>
                                                </td>
                                                <td class="text-center">
                                                    <?= format_datetime_wib($entry['created_at'], 'd/m/Y') ?>
                                                    <br>
                                                    <small class="text-muted"><?= format_datetime_wib($entry['created_at'], 'H:i') ?></small>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Belum ada data transportasi</h5>
                                <p class="text-muted">Data akan muncul setelah Security menginput statistik transportasi</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "pageLength": 25,
            "order": [[ 6, "desc" ]], // Sort by date descending
            "searching": false,        // Hapus fitur Search
            "lengthChange": false,     // Hapus fitur Show entries
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            }
        });
    });
    </script>
</body>
</html>

<style>
/* ===== MAIN LAYOUT ===== */
body {
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f8f9fa;
    color: #333;
}

.main-content {
    margin-left: 250px;
    padding: 30px;
    min-height: 100vh;
}

/* ===== DASHBOARD HEADER ===== */
.dashboard-header {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
    position: relative;
}

.dashboard-header h1 {
    color: #2c3e50;
    margin-bottom: 10px;
    font-size: 28px;
    font-weight: 600;
}

.dashboard-header p {
    color: #7f8c8d;
    margin-bottom: 0;
    font-size: 16px;
}

.header-actions {
    position: absolute;
    top: 30px;
    right: 30px;
    display: flex;
    gap: 10px;
}

/* ===== STATISTICS CARDS ===== */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    gap: 20px;
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-card.primary .stat-icon {
    background: linear-gradient(135deg, #4a90e2, #357abd);
}

.stat-card.success .stat-icon {
    background: linear-gradient(135deg, #27ae60, #2ecc71);
}

.stat-card.warning .stat-icon {
    background: linear-gradient(135deg, #f39c12, #e67e22);
}

.stat-card.danger .stat-icon {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
}

.stat-card.secondary .stat-icon {
    background: linear-gradient(135deg, #95a5a6, #7f8c8d);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
}

.stat-content h3 {
    font-size: 32px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 5px;
}

.stat-content p {
    color: #7f8c8d;
    margin-bottom: 0;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* ===== CONTENT GRID ===== */
.content-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 30px;
}

.content-main {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

/* ===== CARDS ===== */
.card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    border: none;
}

.card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 20px 25px;
    border-radius: 10px 10px 0 0;
}

.card-header h3 {
    color: #2c3e50;
    margin-bottom: 0;
    font-size: 18px;
    font-weight: 600;
}

.card-body {
    padding: 25px;
}

/* ===== BREAKDOWN ITEMS ===== */
.breakdown-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
}

.breakdown-item:last-child {
    border-bottom: none;
}

.breakdown-value {
    font-weight: 600;
    color: #333;
}

/* ===== EMPTY STATE ===== */
.empty-state {
    text-align: center;
    padding: 40px 20px;
}

.empty-state i {
    color: #dee2e6;
}

.empty-state h5 {
    margin-top: 15px;
    color: #6c757d;
}

.empty-state p {
    color: #6c757d;
    margin-bottom: 0;
}

/* ===== TABLES ===== */
.table {
    margin-bottom: 0;
    width: 100%;
}

.table th {
    background: #f8f9fa;
    border-top: none;
    font-weight: 600;
    color: #2c3e50;
    padding: 15px 12px;
}

.table td {
    padding: 12px;
    vertical-align: middle;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}

/* DataTables Custom Styling */
.dataTables_wrapper {
    width: 100%;
}

.dataTables_wrapper .dataTables_paginate {
    margin-top: 20px;
    text-align: center;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 8px 12px;
    margin: 0 2px;
    border-radius: 4px;
    border: 1px solid #dee2e6;
    background: white;
    color: #495057;
    text-decoration: none;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #e9ecef;
    border-color: #adb5bd;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #007bff;
    border-color: #007bff;
    color: white;
}

.dataTables_wrapper .dataTables_info {
    margin-top: 15px;
    color: #6c757d;
    font-size: 14px;
}

/* ===== BUTTONS ===== */
.btn {
    border-radius: 6px;
    font-weight: 500;
    padding: 8px 16px;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.btn-danger {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    border: none;
}

.btn-success {
    background: linear-gradient(135deg, #27ae60, #2ecc71);
    border: none;
}

/* ===== BADGES ===== */
.badge {
    font-size: 0.8rem;
    padding: 5px 10px;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .main-content {
        margin-left: 0;
        padding: 15px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .header-actions {
        position: static;
        margin-top: 20px;
        justify-content: flex-start;
    }
    
    .dashboard-header {
        text-align: left;
    }
}
</style>