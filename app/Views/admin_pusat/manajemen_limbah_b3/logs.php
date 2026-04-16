<?php
$title = $title ?? 'Riwayat Perubahan Limbah B3';
$logs = $logs ?? [];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/dashboard.css') ?>" rel="stylesheet">
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-content">
                <h1><i class="fas fa-history"></i> <?= $title ?></h1>
                <p>Pantau aktivitas perubahan data master limbah B3</p>
            </div>
            
            <div class="header-actions">
                <a href="<?= base_url('/admin-pusat/manajemen-limbah-b3') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Cards Summary -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-left-primary">
                    <div class="card-body">
                        <div class="text-primary mb-2">
                            <i class="fas fa-plus-circle fa-2x"></i>
                        </div>
                        <h6 class="card-text text-muted">Tambah</h6>
                        <h4 id="totalInsert">0</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-info">
                    <div class="card-body">
                        <div class="text-info mb-2">
                            <i class="fas fa-edit fa-2x"></i>
                        </div>
                        <h6 class="card-text text-muted">Edit</h6>
                        <h4 id="totalUpdate">0</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-danger">
                    <div class="card-body">
                        <div class="text-danger mb-2">
                            <i class="fas fa-trash fa-2x"></i>
                        </div>
                        <h6 class="card-text text-muted">Hapus</h6>
                        <h4 id="totalDelete">0</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-secondary">
                    <div class="card-body">
                        <div class="text-secondary mb-2">
                            <i class="fas fa-history fa-2x"></i>
                        </div>
                        <h6 class="card-text text-muted">Total Aktivitas</h6>
                        <h4 id="totalLogs"><?= count($logs) ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Timeline -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> Riwayat Aktivitas</h3>
            </div>
            <div class="card-body">
                <?php if (empty($logs)): ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> Belum ada riwayat perubahan
                </div>
                <?php else: ?>
                <div class="timeline">
                    <?php foreach ($logs as $log): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker">
                            <?php 
                            $action = $log['action'] ?? 'insert';
                            $icon = match($action) {
                                'update' => 'fa-edit text-info',
                                'delete' => 'fa-trash text-danger',
                                default => 'fa-plus text-success'
                            };
                            ?>
                            <i class="fas <?= $icon ?>"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-header">
                                <strong><?= htmlspecialchars($log['aksi'] ?? 'Unknown') ?></strong>
                                <span class="timeline-action badge bg-<?= 
                                    match($action) {
                                        'update' => 'info',
                                        'delete' => 'danger',
                                        default => 'success'
                                    }
                                ?>">
                                    <?= ucfirst($action) ?>
                                </span>
                            </div>
                            <div class="timeline-meta">
                                <small class="text-muted">
                                    <i class="fas fa-user"></i>
                                    <?= htmlspecialchars($log['nama_admin'] ?? 'System') ?>
                                    <span class="ms-3">
                                        <i class="fas fa-clock"></i>
                                        <?= isset($log['created_at']) ? date('d M Y H:i:s', strtotime($log['created_at'])) : '-' ?>
                                    </span>
                                </small>
                            </div>
                            <?php if (isset($log['changes']) && !empty($log['changes'])): ?>
                            <div class="timeline-changes mt-2">
                                <small class="text-muted">
                                    Perubahan: <?= htmlspecialchars($log['changes']) ?>
                                </small>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const logs = <?= json_encode($logs) ?>;
        let insertCount = 0;
        let updateCount = 0;
        let deleteCount = 0;

        logs.forEach(log => {
            if (log.aksi === 'Tambah Limbah B3') insertCount++;
            else if (log.aksi === 'Update Limbah B3') updateCount++;
            else if (log.aksi === 'Hapus Limbah B3') deleteCount++;
        });

        document.getElementById('totalInsert').textContent = insertCount;
        document.getElementById('totalUpdate').textContent = updateCount;
        document.getElementById('totalDelete').textContent = deleteCount;
    });
    </script>

    <style>
        .main-content {
            margin-left: 280px;
            padding: 30px;
            min-height: 100vh;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }

        .header-actions {
            display: flex;
            gap: 10px;
        }

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border: none;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .card-header {
            background-color: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #dee2e6;
        }

        .card-body {
            padding: 20px;
        }

        .border-left-primary {
            border-left: 4px solid #0d6efd !important;
        }

        .border-left-info {
            border-left: 4px solid #0dcaf0 !important;
        }

        .border-left-danger {
            border-left: 4px solid #dc3545 !important;
        }

        .border-left-secondary {
            border-left: 4px solid #6c757d !important;
        }

        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(180deg, #0d6efd, #6c757d);
        }

        .timeline-item {
            display: flex;
            margin-bottom: 30px;
            position: relative;
        }

        .timeline-marker {
            width: 40px;
            height: 40px;
            background: white;
            border: 2px solid #dee2e6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            left: -25px;
            top: 5px;
            z-index: 1;
        }

        .timeline-content {
            flex: 1;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border-left: 3px solid #0d6efd;
        }

        .timeline-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .timeline-action {
            font-size: 0.75rem;
        }

        .timeline-meta {
            display: flex;
            gap: 20px;
        }

        .timeline-changes {
            padding-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #dee2e6;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }

            .page-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .header-actions {
                width: 100%;
            }

            .header-actions .btn {
                width: 100%;
            }

            .row {
                margin-bottom: 20px;
            }

            .col-md-3 {
                margin-bottom: 15px;
            }

            .timeline {
                padding-left: 20px;
            }

            .timeline-marker {
                width: 32px;
                height: 32px;
                left: -20px;
            }

            .timeline-marker i {
                font-size: 0.875rem;
            }
        }
    </style>
</body>
</html>
