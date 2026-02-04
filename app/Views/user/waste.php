<?php
/**
 * User Waste Management - UI GreenMetric POLBAN
 * Manajemen sampah untuk user
 */

// Helper functions
if (!function_exists('formatNumber')) {
    function formatNumber($number) {
        return number_format($number, 0, ',', '.');
    }
}

if (!function_exists('formatCurrency')) {
    function formatCurrency($amount) {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

// Safety checks
$waste_list = $waste_list ?? [];
$categories = $categories ?? [];
$unit = $unit ?? ['nama_unit' => 'Unit'];
$stats = $stats ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Manajemen Sampah User' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <!-- Mobile Responsive CSS -->
    <link href="<?= base_url('/css/mobile-responsive.css') ?>" rel="stylesheet">
    <!-- Enhancement CSS -->
    <link href="<?= base_url('/css/toast-notification.css') ?>" rel="stylesheet">
    <link href="<?= base_url('/css/loading-state.css') ?>" rel="stylesheet">
    <link href="<?= base_url('/css/confirmation-dialog.css') ?>" rel="stylesheet">
    <link href="<?= base_url('/css/tooltip-helper.css') ?>" rel="stylesheet">
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-trash-alt"></i> Manajemen Sampah</h1>
            <p>Kelola data sampah untuk <?= $unit['nama_unit'] ?></p>
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

        <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            <?= $error ?>
        </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <?php if (!empty($stats)): ?>
        <div class="stats-grid mb-4">
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-list"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $stats['total_entries'] ?? 0 ?></h3>
                    <p>Total Data</p>
                </div>
            </div>
            
            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $stats['pending_count'] ?? 0 ?></h3>
                    <p>Menunggu Review</p>
                </div>
            </div>
            
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-check-double"></i>
                </div>
                <div class="stat-content">
                    <h3><?= count(array_filter($waste_list, fn($w) => ($w['status'] ?? '') === 'disetujui_tps')) ?></h3>
                    <p>Disetujui TPS</p>
                </div>
            </div>
            
            <div class="stat-card danger">
                <div class="stat-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <h3><?= count(array_filter($waste_list, fn($w) => ($w['status'] ?? '') === 'ditolak_tps')) ?></h3>
                    <p>Ditolak TPS</p>
                </div>
            </div>
            
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $stats['approved_count'] ?? 0 ?></h3>
                    <p>Disetujui Admin</p>
                </div>
            </div>
            
            <div class="stat-card secondary">
                <div class="stat-icon">
                    <i class="fas fa-file"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $stats['draft_count'] ?? 0 ?></h3>
                    <p>Draft</p>
                </div>
            </div>
            
            <div class="stat-card info">
                <div class="stat-icon">
                    <i class="fas fa-weight-hanging"></i>
                </div>
                <div class="stat-content">
                    <h3><?= formatNumber($stats['total_weight'] ?? 0) ?> kg</h3>
                    <p>Total Berat</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Recent Activities -->
        <?php if (!empty($recent_activities)): ?>
        <div class="card mb-4">
            <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px;">
                <h3 style="margin: 0;"><i class="fas fa-history"></i> Aktivitas Terbaru</h3>
            </div>
            <div class="card-body">
                <div class="activity-timeline">
                    <?php foreach ($recent_activities as $activity): ?>
                    <div class="activity-item">
                        <div class="activity-icon <?= $activity['status'] ?>">
                            <?php
                            $icon = 'fa-file';
                            switch ($activity['status']) {
                                case 'draft': $icon = 'fa-file'; break;
                                case 'dikirim': $icon = 'fa-paper-plane'; break;
                                case 'disetujui': $icon = 'fa-check-circle'; break;
                                case 'ditolak': $icon = 'fa-times-circle'; break;
                                case 'perlu_revisi': $icon = 'fa-edit'; break;
                            }
                            ?>
                            <i class="fas <?= $icon ?>"></i>
                        </div>
                        <div class="activity-content">
                            <p class="activity-description"><?= $activity['description'] ?></p>
                            <small class="activity-time">
                                <i class="fas fa-clock"></i>
                                <?= date('d/m/Y H:i', strtotime($activity['timestamp'])) ?>
                            </small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Action Buttons -->
        <div class="action-buttons mb-4">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWasteModal">
                <i class="fas fa-plus"></i> Tambah Data Sampah
            </button>
            <a href="<?= base_url('/user/waste/export-excel') ?>" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            <a href="<?= base_url('/user/waste/export-pdf') ?>" class="btn btn-danger" target="_blank">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>

        <!-- Informasi Harga Sampah -->
        <?php if (!empty($categories)): ?>
        <div class="card mb-4">
            <div class="card-header" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 20px;">
                <h3 style="margin: 0;"><i class="fas fa-money-bill-wave"></i> Informasi Harga Sampah</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($categories as $category): ?>
                    <div class="col-md-4 mb-3">
                        <div class="price-card <?= $category['dapat_dijual'] ? 'sellable' : 'not-sellable' ?>" style="background: white; border-radius: 12px; padding: 20px; border: 2px solid <?= $category['dapat_dijual'] ? '#28a745' : '#6c757d' ?>;">
                            <div class="price-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 2px solid #e9ecef;">
                                <h5 style="margin: 0; color: #2c3e50; font-weight: 700;"><?= htmlspecialchars($category['jenis_sampah']) ?></h5>
                                <?php if ($category['dapat_dijual']): ?>
                                <span class="badge bg-success">Bisa Dijual</span>
                                <?php else: ?>
                                <span class="badge bg-secondary">Tidak Dijual</span>
                                <?php endif; ?>
                            </div>
                            <div class="price-body">
                                <p class="category-name" style="color: #6c757d; font-size: 14px; margin-bottom: 15px;"><?= htmlspecialchars($category['nama_jenis']) ?></p>
                                <?php if ($category['dapat_dijual']): ?>
                                <div class="price-info" style="display: flex; align-items: baseline; gap: 8px; padding: 12px; background: rgba(40, 167, 69, 0.1); border-radius: 8px;">
                                    <span class="price-label" style="font-size: 13px; color: #6c757d;">Harga:</span>
                                    <span class="price-value" style="font-size: 20px; font-weight: 700; color: #28a745;"><?= formatCurrency($category['harga_per_satuan']) ?></span>
                                    <span class="price-unit" style="font-size: 14px; color: #6c757d;">/ <?= htmlspecialchars($category['satuan']) ?></span>
                                </div>
                                <?php else: ?>
                                <div class="price-info text-muted" style="padding: 12px; background: rgba(108, 117, 125, 0.1); border-radius: 8px;">
                                    <small>Sampah ini tidak memiliki nilai jual</small>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                
                <!-- Pagination untuk Informasi Harga Sampah -->
                <?php if (isset($pagerHarga) && $pagerHarga && $pagerHarga->getPageCount('harga') > 1): ?>
                <div class="mt-4">
                    <nav aria-label="Pagination Harga Sampah">
                        <ul class="pagination justify-content-center">
                            <!-- Previous Button -->
                            <?php if ($pagerHarga->getCurrentPage('harga') > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= base_url('/user/waste?page_harga=' . ($pagerHarga->getCurrentPage('harga') - 1)) ?>">
                                        <span>&laquo; Previous</span>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled">
                                    <span class="page-link">&laquo; Previous</span>
                                </li>
                            <?php endif; ?>

                            <!-- Page Numbers -->
                            <?php for ($i = 1; $i <= $pagerHarga->getPageCount('harga'); $i++): ?>
                                <?php if ($i == $pagerHarga->getCurrentPage('harga')): ?>
                                    <li class="page-item active">
                                        <span class="page-link"><?= $i ?></span>
                                    </li>
                                <?php else: ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= base_url('/user/waste?page_harga=' . $i) ?>"><?= $i ?></a>
                                    </li>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <!-- Next Button -->
                            <?php if ($pagerHarga->getCurrentPage('harga') < $pagerHarga->getPageCount('harga')): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= base_url('/user/waste?page_harga=' . ($pagerHarga->getCurrentPage('harga') + 1)) ?>">
                                        <span>Next &raquo;</span>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled">
                                    <span class="page-link">Next &raquo;</span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>
                
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle"></i>
                    <strong>Catatan:</strong> Harga sampah dapat berubah sewaktu-waktu. 
                    Nilai penjualan akan dihitung otomatis saat Anda menginput data sampah.
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-warning mb-4">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Perhatian:</strong> Data kategori sampah belum tersedia. Silakan hubungi administrator.
        </div>
        <?php endif; ?>

        <!-- Waste Data Table with Tabs -->
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#pending-tab">
                            <i class="fas fa-clock text-warning"></i> Draft & Dikirim 
                            <span class="badge bg-warning"><?= count(array_filter($waste_list, fn($w) => in_array($w['status'] ?? 'draft', ['draft', 'dikirim', 'review', 'perlu_revisi']))) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#approved-tps-tab">
                            <i class="fas fa-check-double text-success"></i> Disetujui TPS 
                            <span class="badge bg-success"><?= count(array_filter($waste_list, fn($w) => ($w['status'] ?? '') === 'disetujui_tps')) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#rejected-tps-tab">
                            <i class="fas fa-exclamation-triangle text-danger"></i> Ditolak TPS 
                            <span class="badge bg-danger"><?= count(array_filter($waste_list, fn($w) => ($w['status'] ?? '') === 'ditolak_tps')) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#approved-tab">
                            <i class="fas fa-check-circle text-success"></i> Disetujui Admin 
                            <span class="badge bg-success"><?= count(array_filter($waste_list, fn($w) => ($w['status'] ?? '') === 'disetujui')) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#rejected-tab">
                            <i class="fas fa-times-circle text-danger"></i> Ditolak Admin 
                            <span class="badge bg-danger"><?= count(array_filter($waste_list, fn($w) => ($w['status'] ?? '') === 'ditolak')) ?></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- Tab 1: Draft & Dikirim -->
                    <div class="tab-pane fade show active" id="pending-tab">
                        <?php 
                        $pendingData = array_filter($waste_list, fn($w) => in_array($w['status'] ?? 'draft', ['draft', 'dikirim', 'review', 'perlu_revisi']));
                        ?>
                        <?php if (!empty($pendingData)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover" style="table-layout: auto; min-width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">No</th>
                                            <th style="width: 140px;">Tanggal</th>
                                            <th style="width: 150px;">Jenis Sampah</th>
                                            <th style="width: 80px;">Berat</th>
                                            <th style="width: 70px;">Satuan</th>
                                            <th style="width: 100px;">Harga/Satuan</th>
                                            <th style="width: 120px;">Total Nilai</th>
                                            <th style="width: 100px;">Status</th>
                                            <th style="width: 120px; min-width: 120px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach ($pendingData as $waste): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($waste['created_at'])) ?></td>
                                            <td>
                                                <span class="badge bg-primary"><?= $waste['jenis_sampah'] ?? 'N/A' ?></span>
                                            </td>
                                            <td><?php 
                                                $berat = $waste['berat_kg'] ?? $waste['berat'] ?? 0;
                                                $jumlah = $waste['jumlah'] ?? $berat;
                                                echo ($jumlah == floor($jumlah)) ? number_format($jumlah, 0, ',', '.') : number_format($jumlah, 2, ',', '.');
                                            ?></td>
                                            <td><?= $waste['satuan'] ?? 'kg' ?></td>
                                            <td>-</td>
                                            <td><?= formatCurrency($waste['nilai_rupiah'] ?? 0) ?></td>
                                            <td>
                                                <?php
                                                $statusClass = match($waste['status'] ?? 'draft') {
                                                    'dikirim' => 'info',
                                                    'review' => 'warning',
                                                    'perlu_revisi' => 'danger',
                                                    'draft' => 'secondary',
                                                    default => 'secondary'
                                                };
                                                $statusLabel = match($waste['status'] ?? 'draft') {
                                                    'dikirim' => 'Dikirim',
                                                    'review' => 'Review',
                                                    'perlu_revisi' => 'Perlu Revisi',
                                                    'draft' => 'Draft',
                                                    default => 'Draft'
                                                };
                                                ?>
                                                <span class="badge bg-<?= $statusClass ?>"><?= $statusLabel ?></span>
                                            </td>
                                            <td style="white-space: nowrap;">
                                                <?php if (in_array($waste['status'] ?? 'draft', ['draft', 'perlu_revisi'])): ?>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-primary" onclick="editWaste(<?= $waste['id'] ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger" onclick="deleteWaste(<?= $waste['id'] ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                <?php else: ?>
                                                <span class="text-muted" style="font-size: 11px;">Tidak dapat diedit</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Tidak ada data draft atau yang sedang dikirim.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tab 2: Disetujui TPS (NEW) -->
                    <div class="tab-pane fade" id="approved-tps-tab">
                        <?php 
                        $approvedTpsData = array_filter($waste_list, fn($w) => ($w['status'] ?? '') === 'disetujui_tps');
                        ?>
                        <?php if (!empty($approvedTpsData)): ?>
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle"></i>
                                <strong>Informasi:</strong> Data yang disetujui TPS telah masuk ke sistem TPS dan tidak dapat diedit lagi.
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover" style="table-layout: auto; min-width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">No</th>
                                            <th style="width: 140px;">Tanggal Input</th>
                                            <th style="width: 150px;">Jenis Sampah</th>
                                            <th style="width: 80px;">Berat</th>
                                            <th style="width: 70px;">Satuan</th>
                                            <th style="width: 120px;">Total Nilai</th>
                                            <th style="width: 140px;">Tanggal Disetujui</th>
                                            <th style="width: 100px;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach ($approvedTpsData as $waste): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($waste['tanggal'] ?? $waste['created_at'])) ?></td>
                                            <td>
                                                <span class="badge bg-primary"><?= esc($waste['jenis_sampah'] ?? 'N/A') ?></span>
                                            </td>
                                            <td><?php 
                                                $berat = $waste['berat_kg'] ?? $waste['berat'] ?? 0;
                                                $jumlah = $waste['jumlah'] ?? $berat;
                                                echo ($jumlah == floor($jumlah)) ? number_format($jumlah, 0, ',', '.') : number_format($jumlah, 2, ',', '.');
                                            ?></td>
                                            <td><?= esc($waste['satuan'] ?? 'kg') ?></td>
                                            <td><?= formatCurrency($waste['nilai_rupiah'] ?? 0) ?></td>
                                            <td>
                                                <?php if (!empty($waste['tps_reviewed_at'])): ?>
                                                    <?= date('d/m/Y H:i', strtotime($waste['tps_reviewed_at'])) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-double"></i> Disetujui TPS
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-check-double fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada data yang disetujui oleh TPS.</p>
                                <small class="text-muted">Data yang disetujui TPS akan muncul di sini.</small>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tab 3: Ditolak TPS (NEW) -->
                    <div class="tab-pane fade" id="rejected-tps-tab">
                        <?php 
                        $rejectedTpsData = array_filter($waste_list, fn($w) => ($w['status'] ?? '') === 'ditolak_tps');
                        ?>
                        <?php if (!empty($rejectedTpsData)): ?>
                            <div class="alert alert-warning mb-3">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Perhatian:</strong> Data di bawah ini ditolak oleh TPS. Anda dapat mengedit dan mengirim ulang data tersebut.
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover" style="table-layout: auto; min-width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">No</th>
                                            <th style="width: 140px;">Tanggal Input</th>
                                            <th style="width: 150px;">Jenis Sampah</th>
                                            <th style="width: 80px;">Berat</th>
                                            <th style="width: 70px;">Satuan</th>
                                            <th style="width: 120px;">Total Nilai</th>
                                            <th style="width: 250px;">Alasan Penolakan</th>
                                            <th style="width: 140px;">Tanggal Ditolak</th>
                                            <th style="width: 150px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach ($rejectedTpsData as $waste): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($waste['tanggal'] ?? $waste['created_at'])) ?></td>
                                            <td>
                                                <span class="badge bg-primary"><?= esc($waste['jenis_sampah'] ?? 'N/A') ?></span>
                                            </td>
                                            <td><?php 
                                                $berat = $waste['berat_kg'] ?? $waste['berat'] ?? 0;
                                                $jumlah = $waste['jumlah'] ?? $berat;
                                                echo ($jumlah == floor($jumlah)) ? number_format($jumlah, 0, ',', '.') : number_format($jumlah, 2, ',', '.');
                                            ?></td>
                                            <td><?= esc($waste['satuan'] ?? 'kg') ?></td>
                                            <td><?= formatCurrency($waste['nilai_rupiah'] ?? 0) ?></td>
                                            <td>
                                                <div class="alert alert-danger mb-0 py-2 px-3" style="font-size: 13px;">
                                                    <i class="fas fa-times-circle"></i>
                                                    <strong>Alasan:</strong><br>
                                                    <?= esc($waste['tps_catatan'] ?? 'Tidak ada keterangan') ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if (!empty($waste['tps_reviewed_at'])): ?>
                                                    <?= date('d/m/Y H:i', strtotime($waste['tps_reviewed_at'])) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="white-space: nowrap;">
                                                <div class="btn-group-vertical btn-group-sm" style="width: 100%;">
                                                    <button type="button" class="btn btn-warning btn-sm mb-1" onclick="editWaste(<?= $waste['id'] ?>)">
                                                        <i class="fas fa-edit"></i> Edit & Kirim Ulang
                                                    </button>
                                                    <button type="button" class="btn btn-info btn-sm" onclick="showDetail(<?= htmlspecialchars(json_encode($waste)) ?>)">
                                                        <i class="fas fa-eye"></i> Lihat Detail
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-exclamation-triangle fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Tidak ada data yang ditolak oleh TPS.</p>
                                <small class="text-muted">Data yang ditolak TPS akan muncul di sini dan dapat Anda edit untuk dikirim ulang.</small>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tab 4: Disetujui Admin -->
                    <div class="tab-pane fade" id="approved-tab">
                        <?php 
                        $approvedData = array_filter($waste_list, fn($w) => ($w['status'] ?? '') === 'disetujui');
                        ?>
                        <?php if (!empty($approvedData)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover" style="table-layout: auto; min-width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">No</th>
                                            <th style="width: 140px;">Tanggal</th>
                                            <th style="width: 150px;">Jenis Sampah</th>
                                            <th style="width: 80px;">Berat</th>
                                            <th style="width: 70px;">Satuan</th>
                                            <th style="width: 100px;">Harga/Satuan</th>
                                            <th style="width: 120px;">Total Nilai</th>
                                            <th style="width: 100px;">Status</th>
                                            <th style="width: 100px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach ($approvedData as $waste): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($waste['created_at'])) ?></td>
                                            <td>
                                                <span class="badge bg-primary"><?= $waste['jenis_sampah'] ?? 'N/A' ?></span>
                                            </td>
                                            <td><?php 
                                                $berat = $waste['berat_kg'] ?? $waste['berat'] ?? 0;
                                                $jumlah = $waste['jumlah'] ?? $berat;
                                                echo ($jumlah == floor($jumlah)) ? number_format($jumlah, 0, ',', '.') : number_format($jumlah, 2, ',', '.');
                                            ?></td>
                                            <td><?= $waste['satuan'] ?? 'kg' ?></td>
                                            <td>-</td>
                                            <td><?= formatCurrency($waste['nilai_rupiah'] ?? 0) ?></td>
                                            <td>
                                                <span class="badge bg-success">Disetujui</span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info" onclick="showDetail(<?= htmlspecialchars(json_encode($waste)) ?>)">
                                                    <i class="fas fa-eye"></i> Detail
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-check-circle fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada data yang disetujui.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tab 3: Ditolak -->
                    <div class="tab-pane fade" id="rejected-tab">
                        <?php 
                        $rejectedData = array_filter($waste_list, fn($w) => ($w['status'] ?? '') === 'ditolak');
                        ?>
                        <?php if (!empty($rejectedData)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover" style="table-layout: auto; min-width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">No</th>
                                            <th style="width: 140px;">Tanggal</th>
                                            <th style="width: 150px;">Jenis Sampah</th>
                                            <th style="width: 80px;">Berat</th>
                                            <th style="width: 70px;">Satuan</th>
                                            <th style="width: 100px;">Harga/Satuan</th>
                                            <th style="width: 120px;">Total Nilai</th>
                                            <th style="width: 100px;">Status</th>
                                            <th style="width: 200px;">Alasan</th>
                                            <th style="width: 100px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach ($rejectedData as $waste): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($waste['created_at'])) ?></td>
                                            <td>
                                                <span class="badge bg-primary"><?= $waste['jenis_sampah'] ?? 'N/A' ?></span>
                                            </td>
                                            <td><?php 
                                                $berat = $waste['berat_kg'] ?? $waste['berat'] ?? 0;
                                                $jumlah = $waste['jumlah'] ?? $berat;
                                                echo ($jumlah == floor($jumlah)) ? number_format($jumlah, 0, ',', '.') : number_format($jumlah, 2, ',', '.');
                                            ?></td>
                                            <td><?= $waste['satuan'] ?? 'kg' ?></td>
                                            <td>-</td>
                                            <td><?= formatCurrency($waste['nilai_rupiah'] ?? 0) ?></td>
                                            <td>
                                                <span class="badge bg-danger">Ditolak</span>
                                            </td>
                                            <td>
                                                <small class="text-muted"><?= $waste['catatan'] ?? $waste['review_notes'] ?? '-' ?></small>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info" onclick="showDetail(<?= htmlspecialchars(json_encode($waste)) ?>)">
                                                    <i class="fas fa-eye"></i> Detail
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-times-circle fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Tidak ada data yang ditolak.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (empty($waste_list)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada data sampah. Mulai dengan menambah data baru.</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWasteModal">
                        <i class="fas fa-plus"></i> Tambah Data Pertama
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add Waste Modal -->
    <div class="modal fade" id="addWasteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data Sampah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addWasteForm">
                    <?= csrf_field() ?>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="kategori_id" class="form-label">
                                Jenis Sampah * 
                                <i class="fas fa-info-circle text-muted" data-tooltip="Pilih jenis sampah yang akan diinput. Gunakan kotak pencarian untuk menemukan jenis sampah dengan cepat." data-tooltip-position="right"></i>
                            </label>
                            <select class="form-select select2-dropdown" id="kategori_id" name="kategori_id" required>
                                <option value="">Pilih Jenis Sampah</option>
                                <?php 
                                // Gunakan allCategories untuk dropdown (semua data tanpa pagination)
                                $dropdownCategories = isset($allCategories) && !empty($allCategories) ? $allCategories : $categories;
                                foreach ($dropdownCategories as $category): 
                                ?>
                                <option value="<?= $category['id'] ?>" 
                                        data-harga="<?= $category['harga_per_satuan'] ?>"
                                        data-satuan="<?= $category['satuan'] ?>"
                                        data-jenis="<?= $category['jenis_sampah'] ?>"
                                        data-dapat-dijual="<?= $category['dapat_dijual'] ?>">
                                    <?= $category['nama_jenis'] ?> (<?= $category['jenis_sampah'] ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted"><i class="fas fa-info-circle"></i> Ketik untuk mencari atau scroll untuk melihat lebih banyak</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jumlah" class="form-label">
                                        Jumlah * 
                                        <i class="fas fa-info-circle text-muted" data-tooltip="Masukkan jumlah sampah dalam angka. Contoh: 5.5 atau 10" data-tooltip-position="top"></i>
                                    </label>
                                    <input type="number" class="form-control" id="jumlah" name="jumlah" step="0.01" min="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="satuan" class="form-label">
                                        Satuan * 
                                        <i class="fas fa-info-circle text-muted" data-tooltip="Pilih satuan yang sesuai dengan jenis sampah. Satuan akan otomatis terisi sesuai jenis sampah." data-tooltip-position="top"></i>
                                    </label>
                                    <select class="form-select" id="satuan" name="satuan" required>
                                        <option value="">Pilih Satuan</option>
                                        <option value="kg">Kilogram (kg)</option>
                                        <option value="gram">Gram (g)</option>
                                        <option value="ton">Ton</option>
                                        <option value="liter">Liter (L)</option>
                                        <option value="pcs">Pieces (pcs)</option>
                                        <option value="karung">Karung</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga per Satuan</label>
                            <input type="text" class="form-control" id="harga_display" readonly value="Rp 0">
                            <small class="text-muted" id="konversi_info"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total Nilai</label>
                            <input type="text" class="form-control fw-bold text-success" id="total_nilai_display" readonly value="Rp 0" style="font-size: 1.2em;">
                            <small class="text-muted">* Hanya untuk sampah yang dapat dijual</small>
                        </div>
                        <div class="mb-3">
                            <label for="foto" class="form-label">
                                Bukti Foto * 
                                <i class="fas fa-info-circle text-muted" data-tooltip="Upload foto bukti sampah. Format: JPG, PNG, JPEG. Maksimal 2MB. WAJIB diisi!" data-tooltip-position="top"></i>
                            </label>
                            <input type="file" class="form-control" id="foto" name="foto" accept="image/jpeg,image/png,image/jpg" required>
                            <small class="text-danger"><i class="fas fa-exclamation-triangle"></i> Foto bukti WAJIB diupload agar data bisa masuk ke sistem admin</small>
                            <div id="foto_preview" class="mt-2" style="display: none;">
                                <img id="foto_preview_img" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 2px solid #ddd;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="action" value="draft" class="btn btn-outline-primary">Simpan sebagai Draft</button>
                        <button type="submit" name="action" value="kirim" class="btn btn-primary">Simpan & Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Waste Modal -->
    <div class="modal fade" id="editWasteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Sampah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editWasteForm">
                    <?= csrf_field() ?>
                    <input type="hidden" id="edit_waste_id" name="waste_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_kategori_id_display" class="form-label">Jenis Sampah *</label>
                            <select class="form-select bg-light" id="edit_kategori_id_display" disabled style="cursor: not-allowed; opacity: 0.6;">
                                <option value="">Pilih Jenis Sampah</option>
                                <?php 
                                // Gunakan allCategories untuk dropdown (semua data tanpa pagination)
                                $dropdownCategories = isset($allCategories) && !empty($allCategories) ? $allCategories : $categories;
                                foreach ($dropdownCategories as $category): 
                                ?>
                                <option value="<?= $category['id'] ?>" data-harga="<?= $category['harga_per_satuan'] ?>">
                                    <?= $category['nama_jenis'] ?> (<?= $category['jenis_sampah'] ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" id="edit_kategori_id" name="kategori_id">
                            <small class="text-muted"><i class="fas fa-lock"></i> Jenis sampah tidak dapat diubah saat edit</small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_berat" class="form-label">Jumlah *</label>
                                    <input type="number" class="form-control" id="edit_berat" name="berat_kg" step="0.01" min="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_unit_id" class="form-label">Satuan *</label>
                                    <input type="text" class="form-control bg-light" id="edit_unit_id" name="satuan" readonly style="cursor: not-allowed;">
                                    <small class="text-muted"><i class="fas fa-lock"></i> Satuan mengikuti data awal</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="alert alert-info">
                                <strong>Berat dalam kg:</strong> <span id="edit_berat_kg_display">0 kg</span><br>
                                <strong>Estimasi Nilai:</strong> <span id="edit_estimasi_nilai">Rp 0</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="action" value="draft" class="btn btn-outline-primary">Simpan sebagai Draft</button>
                        <button type="submit" name="action" value="kirim" class="btn btn-primary">Simpan & Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Enhancement Scripts -->
    <script src="<?= base_url('/js/toast-notification.js') ?>"></script>
    <script src="<?= base_url('/js/loading-state.js') ?>"></script>
    <script src="<?= base_url('/js/confirmation-dialog.js') ?>"></script>
    <script src="<?= base_url('/js/tooltip-helper.js') ?>"></script>
    <script>
        // Konversi satuan ke kg
        function konversiKeKg(jumlah, satuan) {
            const konversi = {
                'kg': 1,
                'ton': 1000,
                'gram': 0.001,
                'liter': 1, // Asumsi 1 liter = 1 kg untuk sampah
                'pcs': 0.1, // Asumsi 1 pcs = 0.1 kg
                'karung': 25 // Asumsi 1 karung = 25 kg
            };
            return jumlah * (konversi[satuan] || 1);
        }

        // Konversi dari kg ke satuan lain
        function konversiDariKg(beratKg, satuan) {
            const konversi = {
                'kg': 1,
                'ton': 1000,
                'gram': 0.001,
                'liter': 1,
                'pcs': 0.1,
                'karung': 25
            };
            return beratKg / (konversi[satuan] || 1);
        }

        // Calculate estimated value with auto-update
        function calculateEstimate() {
            const kategoriSelect = document.getElementById('kategori_id');
            const jumlahInput = document.getElementById('jumlah');
            const satuanSelect = document.getElementById('satuan');
            const hargaDisplay = document.getElementById('harga_display');
            const konversiInfo = document.getElementById('konversi_info');
            const totalDisplay = document.getElementById('total_nilai_display');
            
            const selectedOption = kategoriSelect.options[kategoriSelect.selectedIndex];
            const hargaPerSatuan = parseFloat(selectedOption.getAttribute('data-harga')) || 0;
            const satuanMaster = selectedOption.getAttribute('data-satuan') || 'kg'; // Satuan dari master harga
            const jenisKategori = selectedOption.getAttribute('data-jenis') || '';
            const dapatDijual = selectedOption.getAttribute('data-dapat-dijual') == '1';
            const jumlah = parseFloat(jumlahInput.value) || 0;
            const satuan = satuanSelect.value;
            
            // Set satuan default jika belum dipilih
            if (!satuan && jenisKategori) {
                satuanSelect.value = satuanMaster;
            }
            
            if (!satuan || !jumlah || !jenisKategori) {
                hargaDisplay.value = 'Rp 0';
                totalDisplay.value = 'Rp 0';
                konversiInfo.textContent = '';
                return;
            }
            
            // Konversi ke kg untuk perhitungan
            const jumlahKg = konversiKeKg(jumlah, satuan);
            const hargaPerKg = hargaPerSatuan / konversiKeKg(1, satuanMaster); // Konversi harga ke per kg
            
            // Update displays - GUNAKAN SATUAN DARI MASTER
            hargaDisplay.value = 'Rp ' + hargaPerSatuan.toLocaleString('id-ID') + '/' + satuanMaster;
            
            // Info konversi
            if (satuan !== 'kg') {
                konversiInfo.textContent = `${jumlah} ${satuan} = ${jumlahKg.toLocaleString('id-ID')} kg`;
            } else {
                konversiInfo.textContent = '';
            }
            
            if (dapatDijual && jumlahKg > 0) {
                const total = hargaPerKg * jumlahKg;
                totalDisplay.value = 'Rp ' + total.toLocaleString('id-ID');
                totalDisplay.classList.add('text-success');
                totalDisplay.classList.remove('text-muted');
            } else {
                totalDisplay.value = dapatDijual ? 'Rp 0' : 'Tidak dapat dijual';
                totalDisplay.classList.remove('text-success');
                totalDisplay.classList.add('text-muted');
            }
        }

        // Initialize Select2 for dropdown with search and scroll
        $(document).ready(function() {
            // Initialize Select2 on kategori dropdown
            $('#kategori_id').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih Jenis Sampah',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#addWasteModal'),
                language: {
                    noResults: function() {
                        return "Tidak ada hasil ditemukan";
                    },
                    searching: function() {
                        return "Mencari...";
                    },
                    inputTooShort: function() {
                        return "Ketik untuk mencari...";
                    }
                }
            });

            // Add search icon and placeholder to Select2 search box
            $('#kategori_id').on('select2:open', function() {
                // Add placeholder with icon
                $('.select2-search__field').attr('placeholder', '🔍 Cari jenis sampah...');
                
                // Add search icon wrapper
                if (!$('.select2-search--dropdown').find('.search-icon-wrapper').length) {
                    $('.select2-search--dropdown').prepend('<div class="search-icon-wrapper"><i class="fas fa-search"></i></div>');
                }
            });

            // Trigger change event when Select2 changes
            $('#kategori_id').on('select2:select', function(e) {
                calculateEstimate();
            });
        });

        // Add event listeners
        document.getElementById('kategori_id').addEventListener('change', function() {
            // Set satuan default saat kategori dipilih
            const selectedOption = this.options[this.selectedIndex];
            const satuanDefault = selectedOption.getAttribute('data-satuan') || 'kg';
            document.getElementById('satuan').value = satuanDefault;
            calculateEstimate();
        });
        document.getElementById('jumlah').addEventListener('input', calculateEstimate);
        document.getElementById('satuan').addEventListener('change', calculateEstimate);

        // Preview foto
        document.getElementById('foto').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validasi ukuran file (max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    toast.error('Ukuran file terlalu besar! Maksimal 2MB');
                    this.value = '';
                    document.getElementById('foto_preview').style.display = 'none';
                    return;
                }
                
                // Validasi tipe file
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!allowedTypes.includes(file.type)) {
                    toast.error('Format file tidak didukung! Gunakan JPG, PNG, atau JPEG');
                    this.value = '';
                    document.getElementById('foto_preview').style.display = 'none';
                    return;
                }
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('foto_preview_img').src = e.target.result;
                    document.getElementById('foto_preview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                document.getElementById('foto_preview').style.display = 'none';
            }
        });

        // Submit add waste form
        let isSubmitting = false; // Prevent double submit
        document.getElementById('addWasteForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Prevent double submit
            if (isSubmitting) {
                console.log('Form already submitting, ignoring...');
                return;
            }
            
            // Validasi semua field required
            const kategoriId = document.getElementById('kategori_id').value;
            const jumlah = document.getElementById('jumlah').value;
            const satuan = document.getElementById('satuan').value;
            const foto = document.getElementById('foto').files[0];
            
            if (!kategoriId || !jumlah || !satuan || !foto) {
                toast.error('⚠️ Penginputan data tidak lengkap! Semua field wajib diisi termasuk foto bukti.');
                return;
            }
            
            isSubmitting = true; // Set flag
            
            const formData = new FormData(this);
            
            // Konversi jumlah ke kg untuk disimpan
            const jumlahValue = parseFloat(formData.get('jumlah')) || 0;
            const satuanValue = formData.get('satuan');
            const beratKg = konversiKeKg(jumlahValue, satuanValue);
            
            // Tambahkan berat_kg ke form data
            formData.append('berat_kg', beratKg);
            
            // Get action from clicked button
            const action = e.submitter ? e.submitter.value : 'draft';
            formData.append('status_action', action);
            
            // Show loading
            const submitBtn = e.submitter;
            loading.buttonLoading(submitBtn, true);
            
            // Disable all submit buttons
            const allSubmitBtns = this.querySelectorAll('button[type="submit"]');
            allSubmitBtns.forEach(btn => btn.disabled = true);
            
            try {
                const response = await fetch('<?= base_url('/user/waste/save') ?>', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    toast.success(data.message || 'Data sampah berhasil disimpan!');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toast.error(data.message || 'Gagal menyimpan data sampah');
                    loading.buttonLoading(submitBtn, false);
                    allSubmitBtns.forEach(btn => btn.disabled = false);
                    isSubmitting = false; // Reset flag
                }
            } catch (error) {
                console.error('Error:', error);
                toast.error('Terjadi kesalahan saat menyimpan data');
                loading.buttonLoading(submitBtn, false);
                allSubmitBtns.forEach(btn => btn.disabled = false);
                isSubmitting = false; // Reset flag
            }
        });

        // Submit edit waste form
        document.getElementById('editWasteForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const wasteId = document.getElementById('edit_waste_id').value;
            const formData = new FormData(this);
            
            // Konversi jumlah ke kg untuk disimpan
            const berat = parseFloat(formData.get('berat_kg')) || 0;
            const satuan = formData.get('satuan') || 'kg';
            const beratKg = konversiKeKg(berat, satuan);
            
            // Update berat_kg dengan nilai yang sudah dikonversi
            formData.set('berat_kg', beratKg);
            
            // Get action from clicked button
            const action = e.submitter ? e.submitter.value : 'draft';
            formData.append('status_action', action);
            
            // Show loading
            const submitBtn = e.submitter;
            loading.buttonLoading(submitBtn, true);
            
            console.log('Submitting edit form for waste ID:', wasteId);
            console.log('Form data:', Object.fromEntries(formData));
            
            try {
                const response = await fetch(`<?= base_url('/user/waste/edit/') ?>${wasteId}`, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                console.log('Edit response:', data);
                
                if (data.success) {
                    toast.success(data.message || 'Data sampah berhasil diupdate!');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toast.error(data.message || 'Gagal mengupdate data sampah');
                    loading.buttonLoading(submitBtn, false);
                }
            } catch (error) {
                console.error('Error:', error);
                toast.error('Terjadi kesalahan saat mengupdate data');
                loading.buttonLoading(submitBtn, false);
            }
        });

        // Edit waste function
        async function editWaste(id) {
            // Show loading
            loading.show('Memuat data...');
            
            try {
                // Fetch waste data
                const response = await fetch(`<?= base_url('/user/waste/get/') ?>${id}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                console.log('Edit waste response:', data);
                
                loading.hide();
                
                if (data.success) {
                    const waste = data.data;
                    console.log('Waste data:', waste);
                    
                    // Find kategori_id from jenis_sampah
                    const kategoriSelect = document.getElementById('edit_kategori_id_display');
                    let kategoriId = null;
                    
                    // Try to find matching category
                    for (let option of kategoriSelect.options) {
                        const optionText = option.textContent.toLowerCase();
                        const wasteJenis = (waste.jenis_sampah || '').toLowerCase();
                        
                        // Check if option contains the jenis_sampah (in parentheses)
                        if (option.value && optionText.includes(wasteJenis)) {
                            kategoriId = option.value;
                            kategoriSelect.value = option.value;
                            console.log('Found matching category:', kategoriId, optionText);
                            break;
                        }
                    }
                    
                    // If not found, try to get from categories list
                    if (!kategoriId) {
                        console.warn('Category not found for jenis_sampah:', waste.jenis_sampah);
                        // Set first option as fallback
                        if (kategoriSelect.options.length > 1) {
                            kategoriId = kategoriSelect.options[1].value;
                            kategoriSelect.value = kategoriId;
                        }
                    }
                    
                    // Populate form
                    document.getElementById('edit_waste_id').value = waste.id;
                    document.getElementById('edit_kategori_id').value = kategoriId || '';
                    
                    // Konversi berat_kg kembali ke satuan asli
                    const beratKg = waste.berat_kg || waste.berat || waste.jumlah || 0;
                    const satuan = waste.satuan || 'kg';
                    const beratAsli = konversiDariKg(beratKg, satuan);
                    
                    document.getElementById('edit_berat').value = beratAsli;
                    document.getElementById('edit_unit_id').value = satuan;
                    
                    console.log('Form populated with kategori_id:', kategoriId);
                    console.log('Berat asli:', beratAsli, satuan, '(dari', beratKg, 'kg)');
                    
                    // Calculate estimate
                    hitungEditEstimasi();
                    
                    // Show modal
                    const editModal = new bootstrap.Modal(document.getElementById('editWasteModal'));
                    editModal.show();
                } else {
                    toast.error(data.message || 'Gagal mengambil data');
                }
            } catch (error) {
                console.error('Error:', error);
                loading.hide();
                toast.error('Terjadi kesalahan saat mengambil data');
            }
        }

        // Delete waste function
        async function deleteWaste(id) {
            const confirmed = await window.confirm.delete('data sampah ini');
            
            if (confirmed) {
                loading.show('Menghapus data...');
                
                const formData = new FormData();
                formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                
                try {
                    const response = await fetch(`<?= base_url('/user/waste/delete/') ?>${id}`, {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    loading.hide();
                    
                    if (data.success) {
                        toast.success(data.message || 'Data sampah berhasil dihapus!');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        toast.error(data.message || 'Gagal menghapus data sampah');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    loading.hide();
                    toast.error('Terjadi kesalahan saat menghapus data');
                }
            }
        }

        // Edit waste form - calculate estimate
        function hitungEditEstimasi() {
            const kategoriSelect = document.getElementById('edit_kategori_id_display');
            const selectedOption = kategoriSelect.options[kategoriSelect.selectedIndex];
            const harga = parseFloat(selectedOption.getAttribute('data-harga')) || 0;
            const jumlah = parseFloat(document.getElementById('edit_berat').value) || 0;
            const satuan = document.getElementById('edit_unit_id').value;
            
            const beratKg = konversiKeKg(jumlah, satuan);
            const total = harga * beratKg;
            
            document.getElementById('edit_berat_kg_display').textContent = beratKg.toFixed(2) + ' kg';
            document.getElementById('edit_estimasi_nilai').textContent = 'Rp ' + total.toLocaleString('id-ID');
        }
        
        document.getElementById('edit_berat').addEventListener('input', hitungEditEstimasi);
        document.getElementById('edit_unit_id').addEventListener('change', hitungEditEstimasi);
        
        // Show Detail Modal
        function showDetail(waste) {
            document.getElementById('detail_tanggal').textContent = new Date(waste.created_at).toLocaleString('id-ID');
            document.getElementById('detail_jenis').textContent = waste.jenis_sampah || '-';
            document.getElementById('detail_berat').textContent = (waste.jumlah || waste.berat_kg || 0) + ' ' + (waste.satuan || 'kg');
            document.getElementById('detail_nilai').textContent = 'Rp ' + (waste.nilai_rupiah || 0).toLocaleString('id-ID');
            document.getElementById('detail_status').innerHTML = waste.status === 'disetujui' 
                ? '<span class="badge bg-success">Disetujui</span>' 
                : '<span class="badge bg-danger">Ditolak</span>';
            
            // Show rejection reason if exists
            const reasonRow = document.getElementById('detail_reason_row');
            if (waste.status === 'ditolak' && (waste.catatan || waste.review_notes || waste.catatan_admin)) {
                reasonRow.style.display = 'table-row';
                document.getElementById('detail_reason').textContent = waste.catatan || waste.review_notes || waste.catatan_admin || '-';
            } else {
                reasonRow.style.display = 'none';
            }
            
            const modal = new bootstrap.Modal(document.getElementById('detailModal'));
            modal.show();
        }
    </script>
    
    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-info-circle"></i> Detail Data Sampah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Tanggal Input:</th>
                            <td id="detail_tanggal">-</td>
                        </tr>
                        <tr>
                            <th>Jenis Sampah:</th>
                            <td id="detail_jenis">-</td>
                        </tr>
                        <tr>
                            <th>Berat/Jumlah:</th>
                            <td id="detail_berat">-</td>
                        </tr>
                        <tr>
                            <th>Nilai Rupiah:</th>
                            <td id="detail_nilai">-</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td id="detail_status">-</td>
                        </tr>
                        <tr id="detail_reason_row" style="display: none;">
                            <th>Alasan Penolakan:</th>
                            <td id="detail_reason" class="text-danger">-</td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Mobile Menu JS -->
    <script src="<?= base_url('/js/mobile-menu.js') ?>"></script>
</body>
</html>

<style>
/* ===== DROPDOWN SCROLL ===== */
/* Make dropdown scrollable with max height */
select.form-select[size] {
    height: 250px !important;
    overflow-y: auto;
    padding: 8px;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    background-color: #fff;
}

select.form-select[size] option {
    padding: 10px 12px;
    border-radius: 4px;
    margin-bottom: 2px;
    cursor: pointer;
}

select.form-select[size] option:hover {
    background-color: #e9ecef;
}

select.form-select[size] option:checked,
select.form-select[size] option:focus {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    color: white;
    font-weight: 600;
}

/* Custom scrollbar for dropdown (webkit browsers) */
select.form-select[size]::-webkit-scrollbar {
    width: 10px;
}

select.form-select[size]::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 5px;
    margin: 5px;
}

select.form-select[size]::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    border-radius: 5px;
}

select.form-select[size]::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);
}

/* Firefox scrollbar */
select.form-select[size] {
    scrollbar-width: thin;
    scrollbar-color: #2a5298 #f1f1f1;
}

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

/* ===== PAGE HEADER ===== */
.page-header {
    margin-bottom: 30px;
    padding: 20px 0;
    border-bottom: 2px solid #e9ecef;
}

.page-header h1 {
    color: #2c3e50;
    margin-bottom: 10px;
    font-size: 28px;
    font-weight: 700;
}

.page-header p {
    color: #6c757d;
    font-size: 16px;
    margin: 0;
}

/* ===== STATISTICS CARDS ===== */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.stat-card.primary { border-left-color: #007bff; }
.stat-card.success { border-left-color: #28a745; }
.stat-card.warning { border-left-color: #ffc107; }
.stat-card.info { border-left-color: #17a2b8; }
.stat-card.secondary { border-left-color: #6c757d; }

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
.stat-card.success .stat-icon { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.stat-card.warning .stat-icon { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.stat-card.info .stat-icon { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
.stat-card.secondary .stat-icon { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }

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

/* ===== ACTION BUTTONS ===== */
.action-buttons {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

/* ===== PRICE CARDS ===== */
.price-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
    height: 100%;
}

.price-card.sellable {
    border-color: #28a745;
    background: linear-gradient(135deg, #f8fff9 0%, #ffffff 100%);
}

.price-card.not-sellable {
    border-color: #6c757d;
    background: #f8f9fa;
}

.price-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.price-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e9ecef;
}

.price-header h5 {
    margin: 0;
    color: #2c3e50;
    font-weight: 700;
    font-size: 18px;
}

.price-body {
    margin-top: 10px;
}

.category-name {
    color: #6c757d;
    font-size: 14px;
    margin-bottom: 15px;
}

.price-info {
    display: flex;
    align-items: baseline;
    gap: 8px;
    padding: 12px;
    background: rgba(40, 167, 69, 0.1);
    border-radius: 8px;
}

.price-card.not-sellable .price-info {
    background: rgba(108, 117, 125, 0.1);
}

.price-label {
    font-size: 13px;
    color: #6c757d;
    font-weight: 500;
}

.price-value {
    font-size: 20px;
    font-weight: 700;
    color: #28a745;
}

.price-card.not-sellable .price-value {
    color: #6c757d;
}

.price-unit {
    font-size: 14px;
    color: #6c757d;
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

/* ===== TABLES ===== */
.table-responsive {
    border-radius: 10px;
    overflow: hidden;
}

.table {
    margin-bottom: 0;
}

.table th {
    background: #f8f9fa;
    border: none;
    font-weight: 600;
    color: #2c3e50;
    padding: 15px;
    font-size: 14px;
}

.table td {
    border: none;
    padding: 15px;
    vertical-align: middle;
    font-size: 14px;
}

.table tbody tr {
    border-bottom: 1px solid #e9ecef;
}

.table tbody tr:hover {
    background: #f8f9fa;
}

/* ===== EMPTY STATE ===== */
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

.empty-state p {
    margin: 0 0 25px 0;
    font-size: 18px;
}

/* ===== ALERTS ===== */
.alert {
    border-radius: 10px;
    border: none;
    padding: 15px 20px;
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

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

/* ===== MODALS ===== */
.modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.modal-header {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    color: white;
    border-radius: 15px 15px 0 0;
    border: none;
}

.modal-title {
    font-weight: 600;
}

.btn-close {
    filter: invert(1);
}

/* ===== FORM ELEMENTS ===== */
.form-label {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
}

.form-control, .form-select {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    padding: 12px 15px;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 768px) {
    .main-content {
        margin-left: 0;
        padding: 20px;
        max-width: 100vw;
        overflow-x: hidden;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .page-header h1 {
        font-size: 24px;
    }
    
    .action-buttons {
        flex-direction: column;
        width: 100%;
    }

    .action-buttons .btn {
        width: 100%;
    }
    
    .card-header {
        padding: 15px 20px;
    }
    
    .card-body {
        padding: 20px;
    }
    
    .table-responsive {
        font-size: 12px;
        max-width: 100%;
        overflow-x: auto;
    }
    
    .btn-group {
        flex-direction: row;
        flex-wrap: nowrap;
        gap: 3px;
    }

    .btn-group .btn {
        padding: 4px 8px;
        font-size: 11px;
    }

    /* Fix price cards */
    .price-card {
        max-width: 100%;
        overflow-x: hidden;
    }

    /* Fix row columns */
    .row {
        margin-left: 0;
        margin-right: 0;
    }

    .row > [class*="col-"] {
        padding-left: 10px;
        padding-right: 10px;
    }
}

/* Activity Timeline Styles */
.activity-timeline {
    position: relative;
    padding-left: 0;
}

.activity-item {
    display: flex;
    gap: 20px;
    margin-bottom: 25px;
    position: relative;
    padding-left: 50px;
}

.activity-item:last-child {
    margin-bottom: 0;
}

.activity-item::before {
    content: '';
    position: absolute;
    left: 19px;
    top: 40px;
    bottom: -25px;
    width: 2px;
    background: #e9ecef;
}

.activity-item:last-child::before {
    display: none;
}

.activity-icon {
    position: absolute;
    left: 0;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
    flex-shrink: 0;
    z-index: 1;
}

.activity-icon.draft {
    background: #6c757d;
}

.activity-icon.dikirim {
    background: #ffc107;
}

.activity-icon.disetujui {
    background: #28a745;
}

.activity-icon.ditolak {
    background: #dc3545;
}

.activity-icon.perlu_revisi {
    background: #17a2b8;
}

.activity-content {
    flex: 1;
}

.activity-description {
    margin: 0 0 8px 0;
    color: #2c3e50;
    font-size: 15px;
    line-height: 1.6;
}

.activity-description strong {
    color: #1e3c72;
    font-weight: 600;
}

.activity-time {
    color: #6c757d;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.activity-time i {
    font-size: 12px;
}
</style>