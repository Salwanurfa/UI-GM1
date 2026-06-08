<?php
/**
 * User Waste Management - UI GreenMetric POLBAN
 * Manajemen Limbah Cair untuk user
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

// Safety checks - PASTIKAN VARIABEL ADA
$limbah_cair = $limbah_cair ?? [];
$master_limbah_cair = $master_limbah_cair ?? []; // DATA MASTER DARI CONTROLLER
$master_limbah = $master_limbah ?? [];
$jenis_list = $jenis_list ?? [];
$unit = $unit ?? ['nama_unit' => 'Unit'];
$stats = $stats ?? [];
$count_draft_dikirim = $count_draft_dikirim ?? 0;
$count_disetujui_tps = $count_disetujui_tps ?? 0;
$count_ditolak_tps = $count_ditolak_tps ?? 0;
$count_disetujui_admin = $count_disetujui_admin ?? 0;
$count_ditolak_admin = $count_ditolak_admin ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Manajemen Limbah Cair User' ?></title>
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
            <h1><i class="fas fa-tint"></i> Manajemen Limbah Cair</h1>
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

        <!-- Statistics Cards (Same as Limbah B3) -->
        <div class="stats-grid mb-4">
            <!-- Card 1: Total Transaksi -->
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($total_transaksi ?? 0) ?></h3>
                    <p>Total Transaksi</p>
                </div>
            </div>

            <!-- Card 2: Air Terolah -->
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-water"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($total_air_terolah ?? 0, 2) ?></h3>
                    <p>Air Terolah (m³)</p>
                </div>
            </div>

            <!-- Card 3: Lokasi Pembuangan -->
            <div class="stat-card danger">
                <div class="stat-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($jumlah_lokasi ?? 0) ?></h3>
                    <p>Lokasi Pembuangan</p>
                </div>
            </div>

            <!-- Card 4: Rasio Efisiensi -->
            <div class="stat-card info">
                <div class="stat-icon">
                    <i class="fas fa-tint-slash"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($rasio_efisiensi ?? 0, 1) ?>%</h3>
                    <p>Rasio Efisiensi</p>
                </div>
            </div>
        </div>



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
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLimbahCairModal">
                <i class="fas fa-plus"></i> Tambah Data Sampah
            </button>
            <a href="<?= base_url('/user/limbah-cair/export-excel') ?>" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            <a href="<?= base_url('/user/limbah-cair/export-pdf') ?>" class="btn btn-danger" target="_blank">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>

        <!-- Informasi Harga Sampah -->
        <?php if (!empty($jenis_list)): ?>
        <div class="card mb-4">
            <div class="card-header" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 20px;">
                <h3 style="margin: 0;"><i class="fas fa-money-bill-wave"></i> Informasi Harga Sampah</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($jenis_list as $category): ?>
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
                                    <a class="page-link" href="<?= base_url('/user/limbah-cair?page_harga=' . ($pagerHarga->getCurrentPage('harga') - 1)) ?>">
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
                                        <a class="page-link" href="<?= base_url('/user/limbah-cair?page_harga=' . $i) ?>"><?= $i ?></a>
                                    </li>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <!-- Next Button -->
                            <?php if ($pagerHarga->getCurrentPage('harga') < $pagerHarga->getPageCount('harga')): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= base_url('/user/limbah-cair?page_harga=' . ($pagerHarga->getCurrentPage('harga') + 1)) ?>">
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
                        <a class="nav-link active" data-bs-toggle="tab" href="#tab-draft-dikirim">
                            <i class="fas fa-clock text-warning"></i> Draft & Dikirim 
                            <span class="badge bg-warning"><?= $count_draft_dikirim ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-disetujui-tps">
                            <i class="fas fa-check-double text-success"></i> Disetujui TPS 
                            <span class="badge bg-success"><?= $count_disetujui_tps ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-ditolak-tps">
                            <i class="fas fa-exclamation-triangle text-danger"></i> Ditolak TPS 
                            <span class="badge bg-danger"><?= $count_ditolak_tps ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-disetujui-admin">
                            <i class="fas fa-check-circle text-success"></i> Disetujui Admin 
                            <span class="badge bg-success"><?= $count_disetujui_admin ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-ditolak-admin">
                            <i class="fas fa-times-circle text-danger"></i> Ditolak Admin 
                            <span class="badge bg-danger"><?= $count_ditolak_admin ?></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- TAB 1: Draft & Dikirim -->
                    <div class="tab-pane fade show active" id="tab-draft-dikirim">
                        <?php 
                        $draftDikirimData = array_filter($limbah_cair, fn($w) => in_array($w['status'] ?? '', ['draft', 'dikirim_ke_tps']));
                        ?>
                        <?php if (!empty($draftDikirimData)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover" style="table-layout: auto; min-width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">No</th>
                                            <th style="width: 140px;">Tanggal</th>
                                            <th style="width: 150px;">Lokasi</th>
                                            <th style="width: 180px;">Nama Limbah</th>
                                            <th style="width: 100px;">Kode</th>
                                            <th style="width: 100px;">Timbulan</th>
                                            <th style="width: 70px;">pH</th>
                                            <th style="width: 120px;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; ?>
                                        <?php foreach ($draftDikirimData as $row): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= isset($row['tanggal_input']) ? date('d/m/Y', strtotime($row['tanggal_input'])) : '-' ?></td>
                                            <td><?= isset($row['lokasi']) ? esc($row['lokasi']) : '-' ?></td>
                                            <td><strong><?= isset($row['nama_limbah']) ? esc($row['nama_limbah']) : '-' ?></strong></td>
                                            <td><?= isset($row['kode_limbah']) ? esc($row['kode_limbah']) : '-' ?></td>
                                            <td><?= isset($row['timbulan']) ? number_format($row['timbulan'], 2) : '0' ?> <?= isset($row['satuan']) ? esc($row['satuan']) : 'L/bulan' ?></td>
                                            <td><?= isset($row['ph']) ? $row['ph'] : '-' ?></td>
                                            <td>
                                                <?php if (isset($row['status'])): ?>
                                                    <?php if ($row['status'] === 'draft'): ?>
                                                        <span class="badge bg-secondary">Draft</span>
                                                    <?php elseif ($row['status'] === 'dikirim_ke_tps'): ?>
                                                        <span class="badge bg-warning text-dark">Dikirim ke TPS</span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                    </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty-state text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Tidak ada data draft atau yang sedang dikirim</p>
                                <small class="text-muted">Data yang berstatus draft atau dikirim ke TPS akan muncul di sini.</small>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- TAB 2: Disetujui TPS -->
                    <div class="tab-pane fade" id="tab-disetujui-tps">
                        <?php 
                        $approvedTpsData = array_filter($limbah_cair, fn($w) => ($w['status'] ?? '') === 'disetujui_tps');
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
                                            <th style="width: 150px;">Lokasi</th>
                                            <th style="width: 180px;">Nama Limbah</th>
                                            <th style="width: 100px;">Kode</th>
                                            <th style="width: 100px;">Timbulan</th>
                                            <th style="width: 140px;">Tanggal Disetujui</th>
                                            <th style="width: 120px;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach ($approvedTpsData as $waste): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($waste['tanggal_input'] ?? $waste['created_at'])) ?></td>
                                            <td><?= esc($waste['lokasi'] ?? '-') ?></td>
                                            <td>
                                                <strong><?= esc($waste['nama_limbah'] ?? 'N/A') ?></strong>
                                            </td>
                                            <td><?= esc($waste['kode_limbah'] ?? '-') ?></td>
                                            <td><?php 
                                                $timbulan = $waste['timbulan'] ?? 0;
                                                echo ($timbulan == floor($timbulan)) ? number_format($timbulan, 0, ',', '.') : number_format($timbulan, 2, ',', '.');
                                            ?> <?= esc($waste['satuan'] ?? 'L/bulan') ?></td>
                                            <td>
                                                <?php if (!empty($waste['reviewed_at'])): ?>
                                                    <?= date('d/m/Y H:i', strtotime($waste['reviewed_at'])) ?>
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
                            <div class="empty-state text-center py-5">
                                <i class="fas fa-check-double fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada data yang disetujui oleh TPS</p>
                                <small class="text-muted">Data yang disetujui TPS akan muncul di sini.</small>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- TAB 3: Ditolak TPS -->
                    <div class="tab-pane fade" id="tab-ditolak-tps">
                        <?php 
                        $rejectedTpsData = array_filter($limbah_cair, fn($w) => ($w['status'] ?? '') === 'ditolak_tps');
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
                                            <th style="width: 150px;">Lokasi</th>
                                            <th style="width: 180px;">Nama Limbah</th>
                                            <th style="width: 100px;">Kode</th>
                                            <th style="width: 100px;">Timbulan</th>
                                            <th style="width: 250px;">Alasan Penolakan</th>
                                            <th style="width: 140px;">Tanggal Ditolak</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach ($rejectedTpsData as $waste): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($waste['tanggal_input'] ?? $waste['created_at'])) ?></td>
                                            <td><?= esc($waste['lokasi'] ?? '-') ?></td>
                                            <td>
                                                <strong><?= esc($waste['nama_limbah'] ?? 'N/A') ?></strong>
                                            </td>
                                            <td><?= esc($waste['kode_limbah'] ?? '-') ?></td>
                                            <td><?php 
                                                $timbulan = $waste['timbulan'] ?? 0;
                                                echo ($timbulan == floor($timbulan)) ? number_format($timbulan, 0, ',', '.') : number_format($timbulan, 2, ',', '.');
                                            ?> <?= esc($waste['satuan'] ?? 'L/bulan') ?></td>
                                            <td>
                                                <div class="alert alert-danger mb-0 py-2 px-3" style="font-size: 13px;">
                                                    <i class="fas fa-times-circle"></i>
                                                    <?= esc($waste['rejection_reason'] ?? 'Tidak ada keterangan') ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if (!empty($waste['reviewed_at'])): ?>
                                                    <?= date('d/m/Y H:i', strtotime($waste['reviewed_at'])) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="white-space: nowrap;">
                                                <button type="button" class="btn btn-warning btn-sm" onclick="editLimbahCair(<?= $waste['id'] ?>)">
                                                    <i class="fas fa-edit"></i> Edit & Kirim Ulang
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty-state text-center py-5">
                                <i class="fas fa-exclamation-triangle fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Tidak ada data yang ditolak oleh TPS</p>
                                <small class="text-muted">Data yang ditolak TPS akan muncul di sini.</small>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- TAB 4: Disetujui Admin -->
                    <div class="tab-pane fade" id="tab-disetujui-admin">
                        <?php 
                        $approvedAdminData = array_filter($limbah_cair, fn($w) => ($w['status'] ?? '') === 'disetujui_admin');
                        ?>
                        <?php if (!empty($approvedAdminData)): ?>
                            <div class="alert alert-success mb-3">
                                <i class="fas fa-check-double"></i>
                                <strong>Selamat!</strong> Data telah disetujui oleh Admin dan masuk ke sistem final.
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover" style="table-layout: auto; min-width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">No</th>
                                            <th style="width: 140px;">Tanggal Input</th>
                                            <th style="width: 150px;">Lokasi</th>
                                            <th style="width: 180px;">Nama Limbah</th>
                                            <th style="width: 100px;">Kode</th>
                                            <th style="width: 100px;">Timbulan</th>
                                            <th style="width: 120px;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach ($approvedAdminData as $waste): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($waste['tanggal_input'] ?? $waste['created_at'])) ?></td>
                                            <td><?= esc($waste['lokasi'] ?? '-') ?></td>
                                            <td>
                                                <strong><?= esc($waste['nama_limbah'] ?? 'N/A') ?></strong>
                                            </td>
                                            <td><?= esc($waste['kode_limbah'] ?? '-') ?></td>
                                            <td><?php 
                                                $timbulan = $waste['timbulan'] ?? 0;
                                                echo ($timbulan == floor($timbulan)) ? number_format($timbulan, 0, ',', '.') : number_format($timbulan, 2, ',', '.');
                                            ?> <?= esc($waste['satuan'] ?? 'L/bulan') ?></td>
                                            <td>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle"></i> Disetujui Admin
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty-state text-center py-5">
                                <i class="fas fa-check-circle fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada data yang disetujui oleh Admin</p>
                                <small class="text-muted">Data yang disetujui Admin akan muncul di sini.</small>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- TAB 5: Ditolak Admin -->
                    <div class="tab-pane fade" id="tab-ditolak-admin">
                        <?php 
                        $rejectedAdminData = array_filter($limbah_cair, fn($w) => ($w['status'] ?? '') === 'ditolak_admin');
                        ?>
                        <?php if (!empty($rejectedAdminData)): ?>
                            <div class="alert alert-danger mb-3">
                                <i class="fas fa-ban"></i>
                                <strong>Perhatian:</strong> Data di bawah ini ditolak oleh Admin. Silakan hubungi administrator untuk informasi lebih lanjut.
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover" style="table-layout: auto; min-width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">No</th>
                                            <th style="width: 140px;">Tanggal Input</th>
                                            <th style="width: 150px;">Lokasi</th>
                                            <th style="width: 180px;">Nama Limbah</th>
                                            <th style="width: 100px;">Kode</th>
                                            <th style="width: 100px;">Timbulan</th>
                                            <th style="width: 250px;">Alasan Penolakan</th>
                                            <th style="width: 120px;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach ($rejectedAdminData as $waste): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($waste['tanggal_input'] ?? $waste['created_at'])) ?></td>
                                            <td><?= esc($waste['lokasi'] ?? '-') ?></td>
                                            <td>
                                                <strong><?= esc($waste['nama_limbah'] ?? 'N/A') ?></strong>
                                            </td>
                                            <td><?= esc($waste['kode_limbah'] ?? '-') ?></td>
                                            <td><?php 
                                                $timbulan = $waste['timbulan'] ?? 0;
                                                echo ($timbulan == floor($timbulan)) ? number_format($timbulan, 0, ',', '.') : number_format($timbulan, 2, ',', '.');
                                            ?> <?= esc($waste['satuan'] ?? 'L/bulan') ?></td>
                                            <td>
                                                <small class="text-danger">
                                                    <i class="fas fa-times-circle"></i>
                                                    <?= esc($waste['rejection_reason'] ?? 'Tidak ada keterangan') ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle"></i> Ditolak Admin
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty-state text-center py-5">
                                <i class="fas fa-times-circle fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Tidak ada data yang ditolak oleh Admin</p>
                                <small class="text-muted">Data yang ditolak Admin akan muncul di sini.</small>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>

    <!-- Add Limbah Cair Modal -->
                            <i class="fas fa-info-circle"></i> 
                            Total data: <?= count($limbah_cair) ?>
                        </small>
                    </p>
                </div>

                <?php if (empty($limbah_cair)): ?>
                <div class="empty-state text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada data limbah cair. Mulai dengan menambah data baru.</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLimbahCairModal">
                        <i class="fas fa-plus"></i> Tambah Data Pertama
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add Limbah Cair Modal - REDESIGNED -->
    <div class="modal fade" id="addLimbahCairModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0; padding: 25px;">
                    <div>
                        <h5 class="modal-title mb-1" style="font-weight: 700; font-size: 1.4rem;">
                            <i class="fas fa-tint"></i> Tambah Data Limbah Cair
                        </h5>
                        <small style="opacity: 0.9;">Isi form dengan data limbah cair yang dihasilkan</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="addLimbahCairForm" method="POST" action="<?= base_url('user/limbah-cair/save') ?>">
                    <?= csrf_field() ?>
                    <div class="modal-body" style="padding: 30px;">
                        
                        <!-- Section 1: Jenis Limbah -->
                        <div class="form-section mb-4">
                            <h6 class="section-title" style="color: #667eea; font-weight: 700; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e9ecef;">
                                <i class="fas fa-flask"></i> Identifikasi Limbah
                            </h6>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nama_limbah" class="form-label fw-bold">
                                            Jenis Limbah Cair <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="nama_limbah" name="nama_limbah" required style="border: 2px solid #e9ecef; border-radius: 8px;">
                                            <option value="">-- Pilih Jenis Limbah --</option>
                                            <?php if (!empty($master_limbah_cair)): ?>
                                                <?php foreach ($master_limbah_cair as $master): ?>
                                                <option value="<?= esc($master['nama_limbah']) ?>" 
                                                    data-kode="<?= esc($master['kode_limbah']) ?>" 
                                                    data-bahaya="<?= esc($master['tingkat_bahaya']) ?>" 
                                                    data-karakteristik="<?= esc($master['karakteristik']) ?>" 
                                                    data-pengolahan="<?= esc($master['pengolahan']) ?>">
                                                    <?= esc($master['nama_limbah']) ?>
                                                </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="" disabled>Tidak ada data master limbah cair</option>
                                            <?php endif; ?>
                                        </select>
                                        <small class="text-muted"><i class="fas fa-info-circle"></i> Pilih jenis limbah yang dihasilkan</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="lokasi" class="form-label fw-bold">
                                            Lokasi Sumber <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control bg-light" id="lokasi" name="lokasi" required style="border: 2px solid #e9ecef; border-radius: 8px;" value="<?= esc($unit['nama_unit'] ?? $user['nama_unit'] ?? 'Unit') ?>" readonly>
                                        <small class="text-muted"><i class="fas fa-map-marker-alt"></i> Lokasi otomatis sesuai unit Anda</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Auto-filled Info Card -->
                            <div id="limbah_info_card" class="alert alert-info" style="display: none; background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%); border: none; border-radius: 10px; padding: 15px;">
                                <div class="row">
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">Kode Limbah</small>
                                        <strong id="info_kode">-</strong>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">Tingkat Bahaya</small>
                                        <strong id="info_bahaya">-</strong>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">Karakteristik</small>
                                        <strong id="info_karakteristik">-</strong>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">Pengolahan</small>
                                        <strong id="info_pengolahan">-</strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden fields for auto-filled data -->
                            <input type="hidden" id="kode_limbah" name="kode_limbah">
                            <input type="hidden" id="tingkat_bahaya" name="tingkat_bahaya">
                            <input type="hidden" id="karakteristik" name="karakteristik">
                            <input type="hidden" id="pengolahan" name="pengolahan">
                            <input type="hidden" name="bentuk_fisik" value="Cair">
                        </div>

                        <!-- Section 2: Volume & Kemasan -->
                        <div class="form-section mb-4">
                            <h6 class="section-title" style="color: #667eea; font-weight: 700; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e9ecef;">
                                <i class="fas fa-fill-drip"></i> Volume & Kemasan
                            </h6>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="timbulan" class="form-label fw-bold">
                                            Volume (Timbulan) <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control" id="timbulan" name="timbulan" step="0.01" min="0.01" placeholder="0.00" required style="border: 2px solid #e9ecef; border-radius: 8px;">
                                        <small class="text-muted"><i class="fas fa-calculator"></i> Masukkan angka volume</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="satuan" class="form-label fw-bold">
                                            Satuan <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control bg-light" id="satuan" name="satuan" value="L/bulan" readonly style="border: 2px solid #e9ecef; border-radius: 8px; cursor: not-allowed;">
                                        <small class="text-muted"><i class="fas fa-lock"></i> Satuan default</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="kemasan" class="form-label fw-bold">
                                            Kemasan
                                        </label>
                                        <input type="text" class="form-control" id="kemasan" name="kemasan" placeholder="Contoh: Jerigen @20L" style="border: 2px solid #e9ecef; border-radius: 8px;">
                                        <small class="text-muted"><i class="fas fa-box"></i> Jenis wadah penyimpanan</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Parameter Analisa -->
                        <div class="form-section mb-4">
                            <h6 class="section-title" style="color: #667eea; font-weight: 700; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e9ecef;">
                                <i class="fas fa-vial"></i> Parameter Analisa (Opsional)
                            </h6>
                            
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="ph" class="form-label fw-bold">pH</label>
                                        <select class="form-select" id="ph" name="ph" style="border: 2px solid #e9ecef; border-radius: 8px;">
                                            <option value="">-- Pilih pH --</option>
                                            <?php for ($i = 1; $i <= 14; $i++): ?>
                                                <option value="<?= $i ?>"><?= $i ?></option>
                                            <?php endfor; ?>
                                        </select>
                                        <small class="text-muted">Tingkat keasaman (1-14)</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="bod" class="form-label fw-bold">BOD (mg/L)</label>
                                        <input type="number" class="form-control" id="bod" name="bod" step="0.01" min="0" placeholder="0.00" style="border: 2px solid #e9ecef; border-radius: 8px;">
                                        <small class="text-muted">Biochemical Oxygen Demand</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="cod" class="form-label fw-bold">COD (mg/L)</label>
                                        <input type="number" class="form-control" id="cod" name="cod" step="0.01" min="0" placeholder="0.00" style="border: 2px solid #e9ecef; border-radius: 8px;">
                                        <small class="text-muted">Chemical Oxygen Demand</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="tss" class="form-label fw-bold">TSS (mg/L)</label>
                                        <input type="number" class="form-control" id="tss" name="tss" step="0.01" min="0" placeholder="0.00" style="border: 2px solid #e9ecef; border-radius: 8px;">
                                        <small class="text-muted">Total Suspended Solids</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-warning" style="background: #fff3cd; border: none; border-radius: 8px;">
                                <i class="fas fa-exclamation-triangle"></i>
                                <small><strong>Catatan:</strong> Parameter analisa bersifat opsional. Isi jika data tersedia dari hasil uji laboratorium.</small>
                            </div>
                        </div>

                        <!-- Section 4: Keterangan -->
                        <div class="form-section">
                            <h6 class="section-title" style="color: #667eea; font-weight: 700; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e9ecef;">
                                <i class="fas fa-comment-alt"></i> Keterangan Tambahan
                            </h6>
                            
                            <div class="mb-3">
                                <label for="keterangan" class="form-label fw-bold">Keterangan</label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Tambahkan catatan atau informasi tambahan jika diperlukan..." style="border: 2px solid #e9ecef; border-radius: 8px;"></textarea>
                                <small class="text-muted"><i class="fas fa-pen"></i> Informasi tambahan (opsional)</small>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer" style="background: #f8f9fa; border-radius: 0 0 15px 15px; padding: 20px 30px;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; padding: 10px 25px;">
                            <i class="fas fa-times"></i> Batal
                        </button>
                        <button type="submit" name="action" value="simpan_draf" class="btn btn-outline-primary" style="border-radius: 8px; padding: 10px 25px; border-width: 2px;">
                            <i class="fas fa-save"></i> Simpan sebagai Draft
                        </button>
                        <button type="submit" name="action" value="kirim_ke_tps" class="btn btn-primary" style="border-radius: 8px; padding: 10px 25px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                            <i class="fas fa-paper-plane"></i> Simpan & Kirim
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Waste Modal -->
    <div class="modal fade" id="editLimbahCairModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0; padding: 25px;">
                    <div>
                        <h5 class="modal-title mb-1" style="font-weight: 700; font-size: 1.4rem;">
                            <i class="fas fa-tint"></i> Edit Data Limbah Cair
                        </h5>
                        <small style="opacity: 0.9;">Isi form dengan data limbah cair yang dihasilkan</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="editLimbahCairForm" method="POST" action="<?= base_url('user/limbah-cair/update') ?>" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" id="edit_id" name="id">
                    <div class="modal-body" style="padding: 30px;">
                        
                        <!-- Section 1: Identifikasi Limbah -->
                        <div class="form-section mb-4">
                            <h6 class="section-title" style="color: #667eea; font-weight: 700; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e9ecef;">
                                <i class="fas fa-flask"></i> Identifikasi Limbah
                            </h6>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_nama_limbah" class="form-label fw-bold">
                                            Jenis Limbah Cair <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="edit_nama_limbah" name="nama_limbah" required style="border: 2px solid #e9ecef; border-radius: 8px;">
                                            <option value="">-- Pilih Jenis Limbah --</option>
                                            <?php if (!empty($master_limbah_cair)): ?>
                                                <?php foreach ($master_limbah_cair as $master): ?>
                                                <option value="<?= esc($master['nama_limbah']) ?>" 
                                                    data-kode="<?= esc($master['kode_limbah']) ?>" 
                                                    data-bahaya="<?= esc($master['tingkat_bahaya']) ?>" 
                                                    data-karakteristik="<?= esc($master['karakteristik']) ?>" 
                                                    data-pengolahan="<?= esc($master['pengolahan']) ?>">
                                                    <?= esc($master['nama_limbah']) ?>
                                                </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="" disabled>Tidak ada data master limbah cair</option>
                                            <?php endif; ?>
                                        </select>
                                        <small class="text-muted"><i class="fas fa-info-circle"></i> Pilih jenis limbah yang dihasilkan</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_lokasi" class="form-label fw-bold">
                                            Lokasi Sumber <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control bg-light" id="edit_lokasi" name="lokasi" required style="border: 2px solid #e9ecef; border-radius: 8px;" value="<?= esc($unit['nama_unit'] ?? $user['nama_unit'] ?? 'Unit') ?>" readonly>
                                        <small class="text-muted"><i class="fas fa-map-marker-alt"></i> Lokasi otomatis sesuai unit Anda</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Auto-filled Info Card -->
                            <div id="edit_limbah_info_card" class="alert alert-info" style="display: none; background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%); border: none; border-radius: 10px; padding: 15px;">
                                <div class="row">
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">Kode Limbah</small>
                                        <strong id="edit_info_kode">-</strong>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">Tingkat Bahaya</small>
                                        <strong id="edit_info_bahaya">-</strong>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">Karakteristik</small>
                                        <strong id="edit_info_karakteristik">-</strong>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">Pengolahan</small>
                                        <strong id="edit_info_pengolahan">-</strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden fields for auto-filled data -->
                            <input type="hidden" id="edit_kode_limbah" name="kode_limbah">
                            <input type="hidden" id="edit_tingkat_bahaya" name="tingkat_bahaya">
                            <input type="hidden" id="edit_karakteristik" name="karakteristik">
                            <input type="hidden" id="edit_pengolahan" name="pengolahan">
                            <input type="hidden" name="bentuk_fisik" value="Cair">
                        </div>

                        <!-- Section 2: Volume & Kemasan -->
                        <div class="form-section mb-4">
                            <h6 class="section-title" style="color: #667eea; font-weight: 700; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e9ecef;">
                                <i class="fas fa-fill-drip"></i> Volume & Kemasan
                            </h6>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="edit_timbulan" class="form-label fw-bold">
                                            Volume (Timbulan) <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control" id="edit_timbulan" name="timbulan" step="0.01" min="0.01" placeholder="0.00" required style="border: 2px solid #e9ecef; border-radius: 8px;">
                                        <small class="text-muted"><i class="fas fa-calculator"></i> Masukkan angka volume</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="edit_satuan" class="form-label fw-bold">
                                            Satuan <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control bg-light" id="edit_satuan" name="satuan" value="L/bulan" readonly style="border: 2px solid #e9ecef; border-radius: 8px; cursor: not-allowed;">
                                        <small class="text-muted"><i class="fas fa-lock"></i> Satuan default</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="edit_kemasan" class="form-label fw-bold">
                                            Kemasan
                                        </label>
                                        <input type="text" class="form-control" id="edit_kemasan" name="kemasan" placeholder="Contoh: Jerigen @20L" style="border: 2px solid #e9ecef; border-radius: 8px;">
                                        <small class="text-muted"><i class="fas fa-box"></i> Jenis wadah penyimpanan</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Parameter Analisa -->
                        <div class="form-section mb-4">
                            <h6 class="section-title" style="color: #667eea; font-weight: 700; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e9ecef;">
                                <i class="fas fa-vial"></i> Parameter Analisa (Opsional)
                            </h6>
                            
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="edit_ph" class="form-label fw-bold">pH</label>
                                        <select class="form-select" id="edit_ph" name="ph" style="border: 2px solid #e9ecef; border-radius: 8px;">
                                            <option value="">-- Pilih pH --</option>
                                            <?php for ($i = 1; $i <= 14; $i++): ?>
                                                <option value="<?= $i ?>"><?= $i ?></option>
                                            <?php endfor; ?>
                                        </select>
                                        <small class="text-muted">Tingkat keasaman (1-14)</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="edit_bod" class="form-label fw-bold">BOD (mg/L)</label>
                                        <input type="number" class="form-control" id="edit_bod" name="bod" step="0.01" min="0" placeholder="0.00" style="border: 2px solid #e9ecef; border-radius: 8px;">
                                        <small class="text-muted">Biochemical Oxygen Demand</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="edit_cod" class="form-label fw-bold">COD (mg/L)</label>
                                        <input type="number" class="form-control" id="edit_cod" name="cod" step="0.01" min="0" placeholder="0.00" style="border: 2px solid #e9ecef; border-radius: 8px;">
                                        <small class="text-muted">Chemical Oxygen Demand</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="edit_tss" class="form-label fw-bold">TSS (mg/L)</label>
                                        <input type="number" class="form-control" id="edit_tss" name="tss" step="0.01" min="0" placeholder="0.00" style="border: 2px solid #e9ecef; border-radius: 8px;">
                                        <small class="text-muted">Total Suspended Solids</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-warning" style="background: #fff3cd; border: none; border-radius: 8px;">
                                <i class="fas fa-exclamation-triangle"></i>
                                <small><strong>Catatan:</strong> Parameter analisa bersifat opsional. Isi jika data tersedia dari hasil uji laboratorium.</small>
                            </div>
                        </div>

                        <!-- Section 4: Keterangan -->
                        <div class="form-section">
                            <h6 class="section-title" style="color: #667eea; font-weight: 700; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e9ecef;">
                                <i class="fas fa-comment-alt"></i> Keterangan Tambahan
                            </h6>
                            
                            <div class="mb-3">
                                <label for="edit_keterangan" class="form-label fw-bold">Keterangan</label>
                                <textarea class="form-control" id="edit_keterangan" name="keterangan" rows="3" placeholder="Tambahkan catatan atau informasi tambahan jika diperlukan..." style="border: 2px solid #e9ecef; border-radius: 8px;"></textarea>
                                <small class="text-muted"><i class="fas fa-pen"></i> Informasi tambahan (opsional)</small>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer" style="background: #f8f9fa; border-radius: 0 0 15px 15px; padding: 20px 30px;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; padding: 10px 25px;">
                            <i class="fas fa-times"></i> Batal
                        </button>
                        <button type="submit" name="action" value="draft" class="btn btn-outline-primary" style="border-radius: 8px; padding: 10px 25px; border-width: 2px;">
                            <i class="fas fa-save"></i> Simpan sebagai Draft
                        </button>
                        <button type="submit" name="action" value="kirim_ke_tps" class="btn btn-primary" style="border-radius: 8px; padding: 10px 25px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                            <i class="fas fa-paper-plane"></i> Simpan & Kirim
                        </button>
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
        // Calculate estimated value with auto-update
        function calculateEstimate() {
            const kategoriSelect = document.getElementById('jenis_limbah');
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
            $('#jenis_limbah, #nama_limbah').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih Jenis Limbah Cair',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#addLimbahCairModal'),
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
            $('#jenis_limbah, #nama_limbah').on('select2:open', function() {
                // Add placeholder with icon
                $('.select2-search__field').attr('placeholder', '🔍 Cari Jenis Limbah Cair...');
                
                // Add search icon wrapper
                if (!$('.select2-search--dropdown').find('.search-icon-wrapper').length) {
                    $('.select2-search--dropdown').prepend('<div class="search-icon-wrapper"><i class="fas fa-search"></i></div>');
                }
            });

            // AUTO-FILL DATA SAAT NAMA LIMBAH DIPILIH
            $('#nama_limbah').on('select2:select change', function(e) {
                const selectedOption = $(this).find('option:selected');
                const kode = selectedOption.data('kode') || '-';
                const bahaya = selectedOption.data('bahaya') || '-';
                const karakteristik = selectedOption.data('karakteristik') || '-';
                const pengolahan = selectedOption.data('pengolahan') || '-';
                
                // Isi hidden fields
                $('#kode_limbah').val(kode);
                $('#tingkat_bahaya').val(bahaya);
                $('#karakteristik').val(karakteristik);
                $('#pengolahan').val(pengolahan);
                
                // Tampilkan info card
                if (kode !== '-') {
                    $('#info_kode').text(kode);
                    $('#info_bahaya').text(bahaya);
                    $('#info_karakteristik').text(karakteristik);
                    $('#info_pengolahan').text(pengolahan);
                    $('#limbah_info_card').slideDown();
                } else {
                    $('#limbah_info_card').slideUp();
                }
            });

            // Trigger change event when Select2 changes
            $('#jenis_limbah').on('select2:select', function(e) {
                calculateEstimate();
            });
        });

        // Add event listeners
        document.getElementById('jenis_limbah').addEventListener('change', function() {
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

        // Preview foto edit
        document.getElementById('edit_foto').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validasi ukuran file (max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    toast.error('Ukuran file terlalu besar! Maksimal 2MB');
                    this.value = '';
                    document.getElementById('edit_foto_preview').style.display = 'none';
                    return;
                }
                
                // Validasi tipe file
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!allowedTypes.includes(file.type)) {
                    toast.error('Format file tidak didukung! Gunakan JPG, PNG, atau JPEG');
                    this.value = '';
                    document.getElementById('edit_foto_preview').style.display = 'none';
                    return;
                }
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('edit_foto_preview_img').src = e.target.result;
                    document.getElementById('edit_foto_preview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                document.getElementById('edit_foto_preview').style.display = 'none';
            }
        });

        // Auto-fill limbah cair data when jenis limbah is selected
        document.getElementById('nama_limbah').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            
            if (this.value) {
                // Get data attributes
                const kode = selectedOption.getAttribute('data-kode');
                const bahaya = selectedOption.getAttribute('data-bahaya');
                const karakteristik = selectedOption.getAttribute('data-karakteristik');
                const pengolahan = selectedOption.getAttribute('data-pengolahan');
                
                // Fill hidden fields
                document.getElementById('kode_limbah').value = kode;
                document.getElementById('tingkat_bahaya').value = bahaya;
                document.getElementById('karakteristik').value = karakteristik;
                document.getElementById('pengolahan').value = pengolahan;
                
                // Show info card
                document.getElementById('info_kode').textContent = kode;
                document.getElementById('info_bahaya').textContent = bahaya;
                document.getElementById('info_karakteristik').textContent = karakteristik;
                document.getElementById('info_pengolahan').textContent = pengolahan;
                document.getElementById('limbah_info_card').style.display = 'block';
                
                console.log('Auto-filled data:', { kode, bahaya, karakteristik, pengolahan });
            } else {
                // Hide info card if no selection
                document.getElementById('limbah_info_card').style.display = 'none';
            }
        });

        // Auto-fill limbah cair data for EDIT modal
        document.getElementById('edit_nama_limbah').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            
            if (this.value) {
                // Get data attributes
                const kode = selectedOption.getAttribute('data-kode');
                const bahaya = selectedOption.getAttribute('data-bahaya');
                const karakteristik = selectedOption.getAttribute('data-karakteristik');
                const pengolahan = selectedOption.getAttribute('data-pengolahan');
                
                // Fill hidden fields
                document.getElementById('edit_kode_limbah').value = kode;
                document.getElementById('edit_tingkat_bahaya').value = bahaya;
                document.getElementById('edit_karakteristik').value = karakteristik;
                document.getElementById('edit_pengolahan').value = pengolahan;
                
                // Show info card
                document.getElementById('edit_info_kode').textContent = kode;
                document.getElementById('edit_info_bahaya').textContent = bahaya;
                document.getElementById('edit_info_karakteristik').textContent = karakteristik;
                document.getElementById('edit_info_pengolahan').textContent = pengolahan;
                document.getElementById('edit_limbah_info_card').style.display = 'block';
            } else {
                // Hide info card if no selection
                document.getElementById('edit_limbah_info_card').style.display = 'none';
            }
        });

        // Submit add limbah cair form
        let isSubmitting = false; // Prevent double submit
        document.getElementById('addLimbahCairForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Prevent double submit
            if (isSubmitting) {
                console.log('Form already submitting, ignoring...');
                return;
            }
            
            // Validasi field required
            const namaLimbah = document.getElementById('nama_limbah').value;
            const lokasi = document.getElementById('lokasi').value;
            const timbulan = document.getElementById('timbulan').value;
            
            console.log('=== Form Validation ===');
            console.log('Nama Limbah:', namaLimbah);
            console.log('Lokasi:', lokasi);
            console.log('Timbulan:', timbulan);
            
            if (!namaLimbah || !lokasi || !timbulan) {
                alert('⚠️ Mohon lengkapi semua field yang wajib diisi (bertanda *)');
                return;
            }
            
            isSubmitting = true; // Set flag
            
            const formData = new FormData(this);
            
            // Get action from clicked button
            const action = e.submitter ? e.submitter.value : 'simpan_draf';
            formData.set('action', action);
            
            // Show loading
            const submitBtn = e.submitter;
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            
            // Disable all submit buttons
            const allSubmitBtns = this.querySelectorAll('button[type="submit"]');
            allSubmitBtns.forEach(btn => btn.disabled = true);
            
            try {
                const response = await fetch('<?= base_url('/user/limbah-cair/save') ?>', {
                    method: 'POST',
                    body: formData
                });
                
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                const data = await response.json();
                console.log('=== Response Data ===');
                console.log(data);
                
                if (data.success) {
                    alert('✅ ' + (data.message || 'Data limbah cair berhasil disimpan!'));
                    setTimeout(() => location.reload(), 500);
                } else {
                    // Show detailed error message
                    let errorMsg = data.message || 'Gagal menyimpan data limbah cair';
                    if (data.errors) {
                        errorMsg += '\n\nDetail Error:\n' + JSON.stringify(data.errors, null, 2);
                    }
                    alert('❌ ' + errorMsg);
                    console.error('Save failed:', data);
                    
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                    allSubmitBtns.forEach(btn => btn.disabled = false);
                    isSubmitting = false; // Reset flag
                }
            } catch (error) {
                console.error('=== Fetch Error ===');
                console.error(error);
                alert('❌ Terjadi kesalahan saat menyimpan data: ' + error.message);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                allSubmitBtns.forEach(btn => btn.disabled = false);
                isSubmitting = false; // Reset flag
            }
        });

        // Edit form will submit normally (no AJAX interception)
        // The controller handles redirect with flash message
        // Add loading state when form is submitted
        document.getElementById('editLimbahCairForm').addEventListener('submit', function(e) {
            const submitBtn = e.submitter;
            if (submitBtn && submitBtn.type === 'submit') {
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
                
                // Disable all submit buttons to prevent double submission
                const allBtns = this.querySelectorAll('button[type="submit"]');
                allBtns.forEach(btn => btn.disabled = true);
            }
        });


        // Edit waste function
        async function editLimbahCair(id) {
            // Show loading
            loading.show('Memuat data...');
            
            try {
                // Fetch waste data
                const response = await fetch(`<?= base_url('/user/limbah-cair/get/') ?>${id}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                
                loading.hide();
                
                if (data.success) {
                    const waste = data.data;
                    
                    // Populate form
                    document.getElementById('edit_id').value = waste.id;
                    
                    // Set jenis limbah
                    const namaLimbahSelect = document.getElementById('edit_nama_limbah');
                    namaLimbahSelect.value = waste.nama_limbah || '';
                    
                    // Trigger change to show info card
                    const selectedOption = namaLimbahSelect.options[namaLimbahSelect.selectedIndex];
                    if (selectedOption && selectedOption.value) {
                        document.getElementById('edit_info_kode').textContent = selectedOption.getAttribute('data-kode') || '-';
                        document.getElementById('edit_info_bahaya').textContent = selectedOption.getAttribute('data-bahaya') || '-';
                        document.getElementById('edit_info_karakteristik').textContent = selectedOption.getAttribute('data-karakteristik') || '-';
                        document.getElementById('edit_info_pengolahan').textContent = selectedOption.getAttribute('data-pengolahan') || '-';
                        
                        document.getElementById('edit_kode_limbah').value = selectedOption.getAttribute('data-kode') || '';
                        document.getElementById('edit_tingkat_bahaya').value = selectedOption.getAttribute('data-bahaya') || '';
                        document.getElementById('edit_karakteristik').value = selectedOption.getAttribute('data-karakteristik') || '';
                        document.getElementById('edit_pengolahan').value = selectedOption.getAttribute('data-pengolahan') || '';
                        
                        document.getElementById('edit_limbah_info_card').style.display = 'block';
                    }
                    
                    // Set lokasi
                    document.getElementById('edit_lokasi').value = waste.lokasi || '';
                    
                    // Set volume & kemasan
                    document.getElementById('edit_timbulan').value = waste.timbulan || '';
                    document.getElementById('edit_satuan').value = waste.satuan || 'L/bulan';
                    document.getElementById('edit_kemasan').value = waste.kemasan || '';
                    
                    // Set parameter analisa
                    document.getElementById('edit_ph').value = waste.ph || '';
                    document.getElementById('edit_bod').value = waste.bod || '';
                    document.getElementById('edit_cod').value = waste.cod || '';
                    document.getElementById('edit_tss').value = waste.tss || '';
                    
                    // Set keterangan
                    document.getElementById('edit_keterangan').value = waste.keterangan || '';
                    
                    // Show modal
                    const editModal = new bootstrap.Modal(document.getElementById('editLimbahCairModal'));
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
        async function deleteLimbahCair(id) {
            const confirmed = await window.confirm.delete('data sampah ini');
            
            if (confirmed) {
                loading.show('Menghapus data...');
                
                const formData = new FormData();
                formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                
                try {
                    const response = await fetch(`<?= base_url('/user/limbah-cair/delete/') ?>${id}`, {
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

        
        // Show Detail Modal
        function showDetail(waste) {
            document.getElementById('detail_tanggal').textContent = new Date(waste.created_at).toLocaleString('id-ID');
            document.getElementById('detail_jenis').textContent = waste.jenis_sampah || '-';
            document.getElementById('detail_volume').textContent = (waste.jumlah || waste.volume_kg || 0) + ' ' + (waste.satuan || 'kg');
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
                            <th>Jenis Limbah Cair:</th>
                            <td id="detail_jenis">-</td>
                        </tr>
                        <tr>
                            <th>volume/Jumlah:</th>
                            <td id="detail_volume">-</td>
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
/* ===== STATISTICS CARD STYLING (Same as Limbah B3) ===== */
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

/* Border Colors */
.stat-card.primary { border-left-color: #007bff; }
.stat-card.success { border-left-color: #28a745; }
.stat-card.warning { border-left-color: #ffc107; }
.stat-card.info { border-left-color: #17a2b8; }
.stat-card.danger { border-left-color: #dc3545; }
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

/* Icon Gradient Backgrounds */
.stat-card.primary .stat-icon { 
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
}
.stat-card.success .stat-icon { 
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); 
}
.stat-card.danger .stat-icon { 
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); 
}
.stat-card.info .stat-icon { 
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); 
}
.stat-card.warning .stat-icon { 
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); 
}
.stat-card.secondary .stat-icon { 
    background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); 
}

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

/* Responsive */
@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .stat-card {
        padding: 20px;
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        font-size: 20px;
    }
    
    .stat-content h3 {
        font-size: 24px;
    }
    
    .stat-content p {
        font-size: 13px;
    }
}

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
