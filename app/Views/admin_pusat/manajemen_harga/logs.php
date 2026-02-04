x<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Log Perubahan Harga' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/dashboard.css') ?>" rel="stylesheet">
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <div class="page-header">
            <div class="header-content">
                <h1><i class="fas fa-history"></i> Log Perubahan Harga</h1>
                <p>Riwayat perubahan harga sampah</p>
            </div>
            
            <div class="header-actions">
                <a href="<?= base_url('/admin-pusat/manajemen-harga') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-primary">
                                <i class="fas fa-list"></i>
                            </div>
                            <div class="ms-3">
                                <h3><?= $stats['total_logs'] ?? 0 ?></h3>
                                <p class="text-muted mb-0">Total Perubahan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-success">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                            <div class="ms-3">
                                <h3><?= $stats['today_logs'] ?? 0 ?></h3>
                                <p class="text-muted mb-0">Perubahan Hari Ini</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-info">
                                <i class="fas fa-calendar-week"></i>
                            </div>
                            <div class="ms-3">
                                <h3><?= $stats['week_logs'] ?? 0 ?></h3>
                                <p class="text-muted mb-0">Minggu Ini</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logs Table -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-table"></i> Riwayat Perubahan</h3>
            </div>
            <div class="card-body">
                <?php if (empty($logs)): ?>
                <div class="empty-state">
                    <i class="fas fa-history"></i>
                    <p>Belum ada log perubahan harga</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Jenis Sampah</th>
                                <th>Harga Lama</th>
                                <th>Harga Baru</th>
                                <th>Perubahan</th>
                                <th>Diubah Oleh</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                            <tr>
                                <td>
                                    <small>
                                        <?= date('d/m/Y H:i', strtotime($log['created_at'])) ?>
                                    </small>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($log['jenis_sampah'] ?? 'N/A') ?></strong>
                                </td>
                                <td>
                                    <?php if ($log['harga_lama']): ?>
                                        <span class="text-muted text-decoration-line-through">
                                            Rp <?= number_format($log['harga_lama'], 0, ',', '.') ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong class="text-success">
                                        Rp <?= number_format($log['harga_baru'], 0, ',', '.') ?>
                                    </strong>
                                </td>
                                <td>
                                    <?php 
                                    if ($log['harga_lama']) {
                                        $diff = $log['harga_baru'] - $log['harga_lama'];
                                        $percent = ($log['harga_lama'] > 0) ? ($diff / $log['harga_lama'] * 100) : 0;
                                        $color = $diff > 0 ? 'success' : 'danger';
                                        $icon = $diff > 0 ? 'arrow-up' : 'arrow-down';
                                    ?>
                                        <span class="badge bg-<?= $color ?>">
                                            <i class="fas fa-<?= $icon ?>"></i>
                                            <?= number_format(abs($percent), 1) ?>%
                                        </span>
                                    <?php } else { ?>
                                        <span class="badge bg-primary">Baru</span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <small><?= htmlspecialchars($log['admin_nama'] ?? $log['nama_lengkap'] ?? 'System') ?></small>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= htmlspecialchars($log['alasan_perubahan'] ?? '-') ?>
                                    </small>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
        .stat-card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
    </style>
</body>
</html>
