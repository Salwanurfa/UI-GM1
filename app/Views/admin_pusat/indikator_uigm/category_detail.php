<?php
$title = $title ?? 'Detail Kategori ' . ucfirst($category ?? '');
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
                <h1><i class="fas fa-chart-pie"></i> Detail Kategori: <?= ucfirst($category ?? '') ?></h1>
                <p><?= $category_description ?? 'Detail breakdown data limbah per sumber' ?></p>
            </div>
            
            <div class="header-actions">
                <a href="<?= base_url('admin-pusat/indikator-uigm') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="stats-grid mb-4">
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-weight"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($summary['total_kg'] ?? 0, 2) ?> Kg</h3>
                    <p>Total Berat</p>
                </div>
            </div>
            
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-tint"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($summary['total_l'] ?? 0, 2) ?> L</h3>
                    <p>Total Volume</p>
                </div>
            </div>
            
            <div class="stat-card info">
                <div class="stat-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $summary['sources'] ?? 0 ?></h3>
                    <p>Sumber Data</p>
                </div>
            </div>
            
            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-camera"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $summary['evidence_count'] ?? 0 ?></h3>
                    <p>Bukti Foto</p>
                </div>
            </div>
        </div>

        <!-- Breakdown Table -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-table"></i> Breakdown Data per Sumber</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($breakdown)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="25%">Unit/Sumber</th>
                                <th width="20%">Gedung</th>
                                <th width="15%" class="text-center">Berat (Kg)</th>
                                <th width="15%" class="text-center">Volume (L)</th>
                                <th width="10%" class="text-center">Total Records</th>
                                <th width="10%" class="text-center">Bukti Foto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($breakdown as $index => $item): ?>
                            <tr>
                                <td class="text-center"><?= $index + 1 ?></td>
                                <td>
                                    <strong><?= esc($item['nama_unit']) ?></strong>
                                </td>
                                <td><?= esc($item['gedung']) ?></td>
                                <td class="text-center">
                                    <span class="fw-bold text-success"><?= number_format($item['total_kg'], 2) ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold text-primary"><?= number_format($item['total_l'], 2) ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info"><?= $item['total_records'] ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if ($item['evidence_count'] > 0): ?>
                                        <span class="badge bg-success"><?= $item['evidence_count'] ?> Ada</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Belum Ada</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <p class="mb-0">Belum ada data untuk kategori ini</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

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

        .header-actions {
            display: flex;
            gap: 10px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.3s ease;
            border-left: 4px solid;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card.primary { border-left-color: #007bff; }
        .stat-card.success { border-left-color: #28a745; }
        .stat-card.warning { border-left-color: #ffc107; }
        .stat-card.info { border-left-color: #17a2b8; }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .stat-card.primary .stat-icon { background: #007bff; }
        .stat-card.success .stat-icon { background: #28a745; }
        .stat-card.warning .stat-icon { background: #ffc107; }
        .stat-card.info .stat-icon { background: #17a2b8; }

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

        .card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: none;
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 20px 25px;
        }

        .card-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .card-body {
            padding: 25px;
        }

        .table th {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #2c3e50;
            font-size: 14px;
        }

        .table td {
            vertical-align: middle;
            font-size: 14px;
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

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .header-actions {
                width: 100%;
            }

            .table {
                font-size: 12px;
            }
        }
    </style>
</body>
</html>