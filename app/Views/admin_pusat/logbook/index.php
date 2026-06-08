<?php
$title = $title ?? 'LogBook Kegiatan - Monitoring';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - UI GreenMetric POLBAN</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= base_url('assets/css/dashboard.css') ?>" rel="stylesheet">
    
    <style>
        .modal-content {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .modal-header {
            border-radius: 15px 15px 0 0;
        }
        .card {
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-2px);
        }
        .badge-status {
            font-size: 0.85rem;
            padding: 0.35rem 0.65rem;
        }
        
        /* Print Styles - Hide Checkbox and Buttons */
        @media print {
            .no-print,
            .form-check-input,
            input[type="checkbox"],
            .btn-group,
            .sidebar,
            .page-header,
            .alert {
                display: none !important;
            }
            
            table th:first-child,
            table td:first-child {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header mb-4 ms-3">
            <h4><i class="fas fa-book me-2"></i>LogBook Kegiatan - Dokumentasi, Monitoring & Evaluasi</h4>
            <p class="text-muted">Sistem pencatatan aktivitas pembuangan limbah secara sistematis dan real-time</p>
            <div class="alert alert-success border-0 shadow-sm">
                <i class="fas fa-calendar-day me-2"></i>
                <strong>Monitoring Hari Ini:</strong> <?= $tanggal_hari_ini ?? date('d/m/Y') ?> | 
                <i class="fas fa-clock ms-3 me-2"></i>
                <strong>Terakhir Diperbarui:</strong> <?= $waktu_update ?? date('d/m/Y H:i:s') ?>
            </div>
        </div>

        <!-- Info Cards - Statistik Real-time HARI INI -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm" style="border-left: 4px solid #28a745 !important;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Program 3R (Hari Ini)</h6>
                                <h3 class="mb-0 text-success">
                                    <?php
                                    $total3r = array_sum(array_column($riwayat_3r ?? [], 'berat_kg'));
                                    echo number_format($total3r, 2);
                                    ?> kg
                                </h3>
                                <small class="text-muted"><?= count($riwayat_3r ?? []) ?> jenis sampah</small>
                            </div>
                            <div class="text-success" style="font-size: 3rem; opacity: 0.3;">
                                <i class="fas fa-recycle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm" style="border-left: 4px solid #dc3545 !important;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Limbah B3 (Hari Ini)</h6>
                                <h3 class="mb-0 text-danger">
                                    <?php
                                    $totalB3 = array_sum(array_column($riwayat_b3 ?? [], 'timbulan'));
                                    echo number_format($totalB3, 2);
                                    ?> kg
                                </h3>
                                <small class="text-muted"><?= count($riwayat_b3 ?? []) ?> jenis limbah</small>
                            </div>
                            <div class="text-danger" style="font-size: 3rem; opacity: 0.3;">
                                <i class="fas fa-skull-crossbones"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm" style="border-left: 4px solid #17a2b8 !important;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Limbah Cair (Hari Ini)</h6>
                                <h3 class="mb-0 text-info">
                                    <?php
                                    $totalCair = array_sum(array_column($riwayat_cair ?? [], 'timbulan'));
                                    echo number_format($totalCair, 2);
                                    ?> L
                                </h3>
                                <small class="text-muted"><?= count($riwayat_cair ?? []) ?> jenis limbah</small>
                            </div>
                            <div class="text-info" style="font-size: 3rem; opacity: 0.3;">
                                <i class="fas fa-tint"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Informasi Fungsi -->
        <div class="alert alert-primary border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="row">
                <div class="col-md-4">
                    <h6 class="mb-2"><i class="fas fa-file-alt me-2"></i>Dokumentasi</h6>
                    <small>Data dikelompokkan secara sistematis per tanggal dan kategori limbah</small>
                </div>
                <div class="col-md-4">
                    <h6 class="mb-2"><i class="fas fa-chart-line me-2"></i>Monitoring</h6>
                    <small>Status aktivitas dipantau secara real-time dari input user</small>
                </div>
                <div class="col-md-4">
                    <h6 class="mb-2"><i class="fas fa-check-circle me-2"></i>Evaluasi</h6>
                    <small>Total akumulasi dihitung akurat menggunakan fungsi SUM()</small>
                </div>
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="card shadow-sm border-0 p-4">
            <!-- Header dengan Export Buttons -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="fas fa-table me-2"></i>Ringkasan Aktivitas Harian</h5>
                <div class="btn-group">
                    <button type="button" class="btn btn-danger btn-sm" id="btnBulkDelete3r" onclick="bulkDelete('3r')" style="display: none;">
                        <i class="fas fa-trash-alt me-1"></i> Hapus Terpilih (<span id="count3r">0</span>)
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" id="btnBulkDeleteB3" onclick="bulkDelete('b3')" style="display: none;">
                        <i class="fas fa-trash-alt me-1"></i> Hapus Terpilih (<span id="countB3">0</span>)
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" id="btnBulkDeleteCair" onclick="bulkDelete('cair')" style="display: none;">
                        <i class="fas fa-trash-alt me-1"></i> Hapus Terpilih (<span id="countCair">0</span>)
                    </button>
                    <button type="button" class="btn btn-success btn-sm" onclick="exportToExcel()">
                        <i class="fas fa-file-excel me-1"></i> Export Excel
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="exportToPDF()">
                        <i class="fas fa-file-pdf me-1"></i> Export PDF
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="printFormal()">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                    <button type="button" class="btn btn-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;" data-bs-toggle="modal" data-bs-target="#backupModal">
                        <i class="fas fa-database me-1"></i> Backup Data
                    </button>
                </div>
            </div>

            <!-- Nav Tabs -->
            <ul class="nav nav-tabs mb-4" id="logbookTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="program3r-tab" data-bs-toggle="tab" data-bs-target="#program3r" type="button" role="tab">
                        <i class="fas fa-recycle me-2"></i>Program 3R
                        <span class="badge bg-success ms-2"><?= isset($riwayat_3r) ? count($riwayat_3r) : 0 ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="limbahb3-tab" data-bs-toggle="tab" data-bs-target="#limbahb3" type="button" role="tab">
                        <i class="fas fa-skull-crossbones me-2"></i>Limbah B3
                        <span class="badge bg-danger ms-2"><?= isset($riwayat_b3) ? count($riwayat_b3) : 0 ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="limbahcair-tab" data-bs-toggle="tab" data-bs-target="#limbahcair" type="button" role="tab">
                        <i class="fas fa-tint me-2"></i>Limbah Cair
                        <span class="badge bg-info ms-2"><?= isset($riwayat_cair) ? count($riwayat_cair) : 0 ?></span>
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="logbookTabContent">
                
                <!-- Program 3R Tab -->
                <div class="tab-pane fade show active" id="program3r" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-success text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">
                                        <i class="fas fa-recycle me-2"></i>Ringkasan Harian Program 3R (Akumulasi per Hari)
                                    </h5>
                                    <small class="d-block mt-1 opacity-75">
                                        <i class="fas fa-info-circle me-1"></i>Data dikelompokkan berdasarkan tanggal dan jenis sampah
                                    </small>
                                </div>
                                <div class="text-end">
                                    <small class="d-block opacity-75">
                                        <i class="fas fa-clock me-1"></i>Diperbarui: <?= $waktu_update ?? date('d/m/Y H:i:s') ?>
                                    </small>
                                    <small class="d-block opacity-75">
                                        <i class="fas fa-database me-1"></i>Total Data Hari Ini: <?= count($riwayat_3r ?? []) ?> jenis
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($riwayat_3r)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="3%">
                                                    <input type="checkbox" class="form-check-input" id="selectAll3r" onchange="toggleSelectAll('3r')">
                                                </th>
                                                <th width="4%">No</th>
                                                <th width="10%">Waktu Input</th>
                                                <th width="14%">Jenis Sampah</th>
                                                <th width="14%">Nama Sampah</th>
                                                <th width="11%">Total Berat</th>
                                                <th width="11%">Total Nilai</th>
                                                <th width="10%">Status</th>
                                                <th width="13%">Unit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1; foreach ($riwayat_3r as $row): ?>
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" class="form-check-input checkbox-3r" value="<?= $row['id'] ?? '' ?>" onchange="updateBulkDeleteButton('3r')">
                                                    </td>
                                                    <td><?= $no++ ?></td>
                                                    <td>
                                                        <strong><?= date('d/m/Y', strtotime($row['tanggal'])) ?></strong><br>
                                                        <small class="text-muted"><i class="fas fa-clock"></i> <?= date('H:i', strtotime($row['created_at'])) ?></small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-success"><?= esc($row['jenis_sampah'] ?? 'N/A') ?></span>
                                                    </td>
                                                    <td><?= esc($row['nama_sampah'] ?? 'N/A') ?></td>
                                                    <td>
                                                        <strong class="text-success"><?= number_format($row['berat_kg'] ?? 0, 2) ?></strong>
                                                        <small class="text-muted"><?= esc($row['satuan'] ?? 'kg') ?></small>
                                                    </td>
                                                    <td>
                                                        <strong class="text-primary">Rp <?= number_format($row['nilai_rupiah'] ?? 0, 0, ',', '.') ?></strong>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $status = $row['status'] ?? 'dikirim_ke_tps'; // PAKSA DEFAULT: dikirim_ke_tps
                                                        $statusLabel = '';
                                                        $statusClass = '';
                                                        
                                                        if ($status === 'disetujui' || $status === 'disetujui_admin') {
                                                            $statusClass = 'success';
                                                            $statusLabel = 'Disetujui';
                                                        } elseif ($status === 'ditolak') {
                                                            $statusClass = 'danger';
                                                            $statusLabel = 'Ditolak';
                                                        } elseif ($status === 'dikirim_ke_tps' || $status === 'dikirim') {
                                                            $statusClass = 'success';
                                                            $statusLabel = 'Dikirim ke TPS';
                                                        } elseif ($status === 'menunggu_review') {
                                                            $statusClass = 'warning';
                                                            $statusLabel = 'Menunggu Review';
                                                        } elseif ($status === 'draft') {
                                                            $statusClass = 'secondary';
                                                            $statusLabel = 'Draft';
                                                        } else {
                                                            $statusClass = 'secondary';
                                                            $statusLabel = ucfirst(str_replace('_', ' ', $status));
                                                        }
                                                        ?>
                                                        <span class="badge bg-<?= $statusClass ?> badge-status">
                                                            <?= $statusLabel ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted" title="<?= esc($row['nama_unit'] ?? 'N/A') ?>">
                                                            <?= esc($row['nama_unit'] ?? 'N/A') ?>
                                                        </small>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th colspan="4" class="text-end">TOTAL KESELURUHAN:</th>
                                                <th>
                                                    <?php
                                                    $grandTotal = array_sum(array_column($riwayat_3r, 'berat_kg'));
                                                    echo number_format($grandTotal, 2);
                                                    ?> kg
                                                </th>
                                                <th>
                                                    Rp <?php
                                                    $grandNilai = array_sum(array_column($riwayat_3r, 'nilai_rupiah'));
                                                    echo number_format($grandNilai, 0, ',', '.');
                                                    ?>
                                                </th>
                                                <th colspan="3"></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-calendar-times fs-1 text-muted mb-3"></i>
                                    <h5 class="text-muted">Belum ada aktivitas terekam untuk hari ini</h5>
                                    <p class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Menunggu input data baru dari User untuk tanggal <strong><?= $tanggal_hari_ini ?? date('d/m/Y') ?></strong>
                                    </p>
                                    <small class="text-muted">Data akan muncul secara real-time ketika User menginput Program 3R hari ini</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Limbah B3 Tab -->
                <div class="tab-pane fade" id="limbahb3" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-danger text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">
                                        <i class="fas fa-skull-crossbones me-2"></i>Ringkasan Harian Limbah B3 (Akumulasi per Hari)
                                    </h5>
                                    <small class="d-block mt-1 opacity-75">
                                        <i class="fas fa-info-circle me-1"></i>Data dikelompokkan berdasarkan tanggal dan jenis limbah
                                    </small>
                                </div>
                                <div class="text-end">
                                    <small class="d-block opacity-75">
                                        <i class="fas fa-clock me-1"></i>Diperbarui: <?= $waktu_update ?? date('d/m/Y H:i:s') ?>
                                    </small>
                                    <small class="d-block opacity-75">
                                        <i class="fas fa-database me-1"></i>Total Data Hari Ini: <?= count($riwayat_b3 ?? []) ?> jenis
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($riwayat_b3)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="3%">
                                                    <input type="checkbox" class="form-check-input" id="selectAllB3" onchange="toggleSelectAll('b3')">
                                                </th>
                                                <th width="4%">No</th>
                                                <th width="10%">Waktu Input</th>
                                                <th width="17%">Nama Limbah</th>
                                                <th width="9%">Kode</th>
                                                <th width="11%">Total Timbulan</th>
                                                <th width="10%">Status</th>
                                                <th width="14%">Lokasi</th>
                                                <th width="12%">Unit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1; foreach ($riwayat_b3 as $row): ?>
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" class="form-check-input checkbox-b3" value="<?= $row['id'] ?? '' ?>" onchange="updateBulkDeleteButton('b3')">
                                                    </td>
                                                    <td><?= $no++ ?></td>
                                                    <td>
                                                        <strong><?= date('d/m/Y', strtotime($row['tanggal_input'])) ?></strong><br>
                                                        <small class="text-muted"><i class="fas fa-clock"></i> <?= date('H:i', strtotime($row['created_at'])) ?></small>
                                                    </td>
                                                    <td>
                                                        <strong><?= esc($row['nama_limbah'] ?? 'N/A') ?></strong><br>
                                                        <small class="text-muted"><?= esc($row['kategori_bahaya'] ?? '') ?></small>
                                                    </td>
                                                    <td><span class="badge bg-danger"><?= esc($row['kode_limbah'] ?? 'N/A') ?></span></td>
                                                    <td>
                                                        <strong class="text-danger"><?= number_format($row['timbulan'] ?? 0, 2) ?></strong>
                                                        <small class="text-muted"><?= esc($row['satuan'] ?? 'kg') ?></small>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $status = $row['status'] ?? 'dikirim_ke_tps'; // PAKSA DEFAULT
                                                        $statusLabel = '';
                                                        $statusClass = '';
                                                        
                                                        if ($status === 'disetujui_admin' || $status === 'disetujui') {
                                                            $statusClass = 'success';
                                                            $statusLabel = 'Disetujui';
                                                        } elseif ($status === 'ditolak_admin' || $status === 'ditolak_tps' || $status === 'ditolak') {
                                                            $statusClass = 'danger';
                                                            $statusLabel = 'Ditolak';
                                                        } elseif ($status === 'dikirim_ke_tps' || $status === 'dikirim') {
                                                            $statusClass = 'success';
                                                            $statusLabel = 'Dikirim ke TPS';
                                                        } elseif ($status === 'disetujui_tps') {
                                                            $statusClass = 'success';
                                                            $statusLabel = 'Disetujui TPS';
                                                        } elseif ($status === 'menunggu_review') {
                                                            $statusClass = 'warning';
                                                            $statusLabel = 'Menunggu Review';
                                                        } elseif ($status === 'draft') {
                                                            $statusClass = 'secondary';
                                                            $statusLabel = 'Draft';
                                                        } else {
                                                            $statusClass = 'secondary';
                                                            $statusLabel = ucfirst(str_replace('_', ' ', $status));
                                                        }
                                                        ?>
                                                        <span class="badge bg-<?= $statusClass ?> badge-status">
                                                            <?= $statusLabel ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted" title="<?= esc($row['lokasi'] ?? '-') ?>">
                                                            <?= esc($row['lokasi'] ?? '-') ?>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted" title="<?= esc($row['nama_unit'] ?? 'N/A') ?>">
                                                            <?= esc($row['nama_unit'] ?? 'N/A') ?>
                                                        </small>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th colspan="4" class="text-end">TOTAL KESELURUHAN:</th>
                                                <th>
                                                    <?php
                                                    $grandTotal = array_sum(array_column($riwayat_b3, 'timbulan'));
                                                    echo number_format($grandTotal, 2);
                                                    ?> kg
                                                </th>
                                                <th colspan="4"></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-calendar-times fs-1 text-muted mb-3"></i>
                                    <h5 class="text-muted">Belum ada aktivitas terekam untuk hari ini</h5>
                                    <p class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Menunggu input data baru dari User untuk tanggal <strong><?= $tanggal_hari_ini ?? date('d/m/Y') ?></strong>
                                    </p>
                                    <small class="text-muted">Data akan muncul secara real-time ketika User menginput Limbah B3 hari ini</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Limbah Cair Tab -->
                <div class="tab-pane fade" id="limbahcair" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-info text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">
                                        <i class="fas fa-tint me-2"></i>Ringkasan Harian Limbah Cair (Akumulasi per Hari)
                                    </h5>
                                    <small class="d-block mt-1 opacity-75">
                                        <i class="fas fa-info-circle me-1"></i>Data dikelompokkan berdasarkan tanggal dan jenis limbah
                                    </small>
                                </div>
                                <div class="text-end">
                                    <small class="d-block opacity-75">
                                        <i class="fas fa-clock me-1"></i>Diperbarui: <?= $waktu_update ?? date('d/m/Y H:i:s') ?>
                                    </small>
                                    <small class="d-block opacity-75">
                                        <i class="fas fa-database me-1"></i>Total Data Hari Ini: <?= count($riwayat_cair ?? []) ?> jenis
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($riwayat_cair)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="3%">
                                                    <input type="checkbox" class="form-check-input" id="selectAllCair" onchange="toggleSelectAll('cair')">
                                                </th>
                                                <th width="4%">No</th>
                                                <th width="10%">Waktu Input</th>
                                                <th width="14%">Jenis Limbah</th>
                                                <th width="9%">Kode</th>
                                                <th width="11%">Total Volume</th>
                                                <th width="10%">Status</th>
                                                <th width="9%">Rata² pH</th>
                                                <th width="10%">Lokasi</th>
                                                <th width="10%">Unit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1; foreach ($riwayat_cair as $row): ?>
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" class="form-check-input checkbox-cair" value="<?= $row['id'] ?? '' ?>" onchange="updateBulkDeleteButton('cair')">
                                                    </td>
                                                    <td><?= $no++ ?></td>
                                                    <td>
                                                        <strong><?= date('d/m/Y', strtotime($row['tanggal_input'])) ?></strong><br>
                                                        <small class="text-muted"><i class="fas fa-clock"></i> <?= date('H:i', strtotime($row['created_at'])) ?></small>
                                                    </td>
                                                    <td>
                                                        <strong><?= esc($row['nama_limbah'] ?? 'N/A') ?></strong>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($row['kode_limbah']) && $row['kode_limbah'] !== '-'): ?>
                                                            <span class="badge bg-info"><?= esc($row['kode_limbah']) ?></span>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <strong class="text-info"><?= number_format($row['timbulan'] ?? 0, 2) ?></strong>
                                                        <small class="text-muted"><?= esc($row['satuan'] ?? 'L/bulan') ?></small>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $status = $row['status'] ?? 'dikirim_ke_tps'; // PAKSA DEFAULT
                                                        $statusLabel = '';
                                                        $statusClass = '';
                                                        
                                                        if ($status === 'disetujui_admin' || $status === 'disetujui') {
                                                            $statusClass = 'success';
                                                            $statusLabel = 'Disetujui';
                                                        } elseif ($status === 'ditolak_admin' || $status === 'ditolak_tps' || $status === 'ditolak') {
                                                            $statusClass = 'danger';
                                                            $statusLabel = 'Ditolak';
                                                        } elseif ($status === 'dikirim_ke_tps' || $status === 'dikirim') {
                                                            $statusClass = 'success';
                                                            $statusLabel = 'Dikirim ke TPS';
                                                        } elseif ($status === 'disetujui_tps') {
                                                            $statusClass = 'success';
                                                            $statusLabel = 'Disetujui TPS';
                                                        } elseif ($status === 'menunggu_review') {
                                                            $statusClass = 'warning';
                                                            $statusLabel = 'Menunggu Review';
                                                        } elseif ($status === 'draft') {
                                                            $statusClass = 'secondary';
                                                            $statusLabel = 'Draft';
                                                        } else {
                                                            $statusClass = 'secondary';
                                                            $statusLabel = ucfirst(str_replace('_', ' ', $status));
                                                        }
                                                        ?>
                                                        <span class="badge bg-<?= $statusClass ?> badge-status">
                                                            <?= $statusLabel ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($row['ph'])): ?>
                                                            <span class="badge bg-warning text-dark"><?= number_format($row['ph'], 1) ?></span>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted" title="<?= esc($row['lokasi'] ?? '-') ?>">
                                                            <?= esc($row['lokasi'] ?? '-') ?>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted" title="<?= esc($row['nama_unit'] ?? 'N/A') ?>">
                                                            <?= esc($row['nama_unit'] ?? 'N/A') ?>
                                                        </small>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th colspan="4" class="text-end">TOTAL KESELURUHAN:</th>
                                                <th>
                                                    <?php
                                                    $grandTotal = array_sum(array_column($riwayat_cair, 'timbulan'));
                                                    echo number_format($grandTotal, 2);
                                                    ?> L
                                                </th>
                                                <th colspan="5"></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-calendar-times fs-1 text-muted mb-3"></i>
                                    <h5 class="text-muted">Belum ada aktivitas terekam untuk hari ini</h5>
                                    <p class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Menunggu input data baru dari User untuk tanggal <strong><?= $tanggal_hari_ini ?? date('d/m/Y') ?></strong>
                                    </p>
                                    <small class="text-muted">Data akan muncul secara real-time ketika User menginput Limbah Cair hari ini</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="detailModalLabel">
                        <i class="fas fa-info-circle me-2"></i>Detail Data
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Photo Preview Modal -->
    <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="photoModalLabel">
                        <i class="fas fa-image me-2"></i>Preview Bukti Foto
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-4" style="background: #f8f9fa;">
                    <img id="photoPreview" src="" alt="Bukti Foto" class="img-fluid rounded shadow" style="max-height: 70vh;">
                </div>
                <div class="modal-footer">
                    <a id="photoDownload" href="#" class="btn btn-success btn-sm" download>
                        <i class="fas fa-download me-2"></i>Download Foto
                    </a>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Function to preview photo
        function previewFoto(fotoUrl, title) {
            document.getElementById('photoPreview').src = fotoUrl;
            document.getElementById('photoDownload').href = fotoUrl;
            document.getElementById('photoModalLabel').innerHTML = `<i class="fas fa-image me-2"></i>Preview Bukti Foto - ${title}`;
            
            const modal = new bootstrap.Modal(document.getElementById('photoModal'));
            modal.show();
        }

        // AJAX Handler untuk tombol detail
        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== LOGBOOK DETAIL SCRIPT LOADED ===');
            
            const detailButtons = document.querySelectorAll('.btn-detail');
            console.log('Total tombol detail ditemukan:', detailButtons.length);
            
            detailButtons.forEach(function(button, index) {
                console.log(`Button ${index + 1}:`, {
                    category: button.getAttribute('data-category'),
                    id: button.getAttribute('data-id')
                });
                
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const category = this.getAttribute('data-category');
                    const id = this.getAttribute('data-id');
                    
                    console.log('=== TOMBOL DETAIL DIKLIK ===');
                    console.log('Category:', category);
                    console.log('ID:', id);
                    
                    if (!category || !id) {
                        console.error('ERROR: Category atau ID kosong!');
                        alert('Error: Data tidak lengkap');
                        return false;
                    }
                    
                    showDetail(category, id);
                    return false;
                });
            });
        });

        function showDetail(category, id) {
            console.log('=== FUNCTION showDetail DIPANGGIL ===');
            console.log('Parameter category:', category);
            console.log('Parameter id:', id);
            
            const modalElement = document.getElementById('detailModal');
            const modalContent = document.getElementById('detailContent');
            
            if (!modalElement || !modalContent) {
                console.error('ERROR: Modal element tidak ditemukan!');
                alert('Error: Modal tidak ditemukan');
                return;
            }
            
            // Tampilkan modal dengan loading
            const modal = new bootstrap.Modal(modalElement);
            modalContent.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Memuat data...</p>
                </div>
            `;
            modal.show();
            
            // Construct URL - HARUS 100% SAMA DENGAN ROUTE
            // Route: logbook/get_detail/(:any)/(:num)
            const baseUrl = '<?= base_url('admin-pusat/logbook/get_detail') ?>';
            const url = baseUrl + '/' + category + '/' + id;
            
            console.log('=== AJAX REQUEST ===');
            console.log('Base URL:', baseUrl);
            console.log('Category:', category);
            console.log('ID:', id);
            console.log('Full URL:', url);
            console.log('Fetching...');
            
            // AJAX Request
            fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('=== RESPONSE DITERIMA ===');
                console.log('Status:', response.status);
                console.log('Status Text:', response.statusText);
                console.log('Content-Type:', response.headers.get('content-type'));
                
                if (!response.ok) {
                    // Clone response untuk bisa dibaca dua kali
                    return response.text().then(text => {
                        console.error('Response Text:', text);
                        throw new Error(`HTTP ${response.status}: ${response.statusText}\nResponse: ${text}`);
                    });
                }
                
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    return response.text().then(text => {
                        console.error('Response bukan JSON:', text);
                        throw new Error('Response bukan JSON! Content-Type: ' + contentType + '\nResponse: ' + text);
                    });
                }
                
                return response.json();
            })
            .then(data => {
                console.log('=== DATA JSON DITERIMA ===');
                console.log('Success:', data.success);
                console.log('Data:', data.data);
                
                if (data.success && data.data) {
                    let html = '<table class="table table-bordered table-striped">';
                    
                    if (category === '3r') {
                        html += `
                            <tr><th width="30%" class="bg-light">User</th><td>${data.data.nama_user || 'N/A'}</td></tr>
                            <tr><th class="bg-light">Unit</th><td>${data.data.nama_unit || 'N/A'}</td></tr>
                            <tr><th class="bg-light">Tanggal</th><td>${new Date(data.data.created_at).toLocaleString('id-ID')}</td></tr>
                            <tr><th class="bg-light">Jenis Sampah</th><td>${data.data.jenis_sampah || 'N/A'}</td></tr>
                            <tr><th class="bg-light">Nama Sampah</th><td>${data.data.nama_sampah || 'N/A'}</td></tr>
                            <tr><th class="bg-light">Berat</th><td>${parseFloat(data.data.berat_kg || 0).toFixed(2)} ${data.data.satuan || 'kg'}</td></tr>
                            <tr><th class="bg-light">Nilai Rupiah</th><td>Rp ${parseFloat(data.data.nilai_rupiah || 0).toLocaleString('id-ID')}</td></tr>
                            <tr><th class="bg-light">Status</th><td><span class="badge bg-${data.data.status === 'disetujui' ? 'success' : (data.data.status === 'ditolak' ? 'danger' : (data.data.status === 'dikirim' ? 'info' : 'secondary'))}">${data.data.status || 'draft'}</span></td></tr>
                        `;
                        
                        if (data.data.keterangan_bukti) {
                            html += `<tr><th class="bg-light">Keterangan</th><td>${data.data.keterangan_bukti}</td></tr>`;
                        }
                    } else if (category === 'b3') {
                        html += `
                            <tr><th width="30%" class="bg-light">User</th><td>${data.data.nama_user || 'N/A'}</td></tr>
                            <tr><th class="bg-light">Unit</th><td>${data.data.nama_unit || 'N/A'}</td></tr>
                            <tr><th class="bg-light">Tanggal</th><td>${new Date(data.data.tanggal_input).toLocaleString('id-ID')}</td></tr>
                            <tr><th class="bg-light">Nama Limbah</th><td>${data.data.nama_limbah || 'N/A'}</td></tr>
                            <tr><th class="bg-light">Kode Limbah</th><td><span class="badge bg-danger">${data.data.kode_limbah || 'N/A'}</span></td></tr>
                            <tr><th class="bg-light">Kategori Bahaya</th><td>${data.data.kategori_bahaya || 'N/A'}</td></tr>
                            <tr><th class="bg-light">Timbulan</th><td>${parseFloat(data.data.timbulan || 0).toFixed(2)} ${data.data.satuan || 'kg'}</td></tr>
                            <tr><th class="bg-light">Bentuk Fisik</th><td>${data.data.bentuk_fisik || '-'}</td></tr>
                            <tr><th class="bg-light">Kemasan</th><td>${data.data.kemasan || '-'}</td></tr>
                            <tr><th class="bg-light">Lokasi</th><td>${data.data.lokasi || '-'}</td></tr>
                        `;
                    } else if (category === 'cair') {
                        html += `
                            <tr><th width="30%" class="bg-light">User</th><td>${data.data.nama_user || 'N/A'}</td></tr>
                            <tr><th class="bg-light">Unit</th><td>${data.data.nama_unit || 'N/A'}</td></tr>
                            <tr><th class="bg-light">Tanggal</th><td>${new Date(data.data.tanggal_input).toLocaleString('id-ID')}</td></tr>
                            <tr><th class="bg-light">Jenis Limbah</th><td>${data.data.jenis_limbah || 'N/A'}</td></tr>
                            <tr><th class="bg-light">Volume</th><td>${parseFloat(data.data.volume || 0).toFixed(2)} ${data.data.satuan || 'liter'}</td></tr>
                            <tr><th class="bg-light">Lokasi</th><td>${data.data.lokasi || '-'}</td></tr>
                            <tr><th class="bg-light">Keterangan</th><td>${data.data.keterangan || '-'}</td></tr>
                        `;
                    }
                    
                    html += '</table>';
                    modalContent.innerHTML = html;
                    console.log('=== MODAL BERHASIL DIISI ===');
                } else {
                    console.warn('Data tidak ditemukan atau success = false');
                    modalContent.innerHTML = `
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Data tidak ditemukan</strong><br>
                            <small>${data.message || 'Tidak ada data untuk ditampilkan'}</small>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('=== ERROR TERJADI ===');
                console.error('Error:', error);
                console.error('Error message:', error.message);
                
                modalContent.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Terjadi kesalahan saat memuat data</strong><br>
                        <small class="d-block mt-2"><strong>Error:</strong> ${error.message}</small>
                        <small class="d-block mt-1"><strong>URL:</strong> ${url}</small>
                        <hr>
                        <small class="text-muted">
                            <strong>Troubleshooting:</strong><br>
                            1. Buka Console Browser (F12) untuk melihat detail error<br>
                            2. Route harus: <code>logbook/get_detail/(:any)/(:num)</code><br>
                            3. Controller method: <code>Admin\\LogBook::getDetail</code><br>
                            4. URL yang dipanggil: <code>${url}</code>
                        </small>
                    </div>
                `;
            });
        }
    </script>

    <!-- ========================================
         EXPORT FUNCTIONS - Excel, PDF, Print
         ======================================== -->
    <script>
        // Export to Excel
        function exportToExcel() {
            // Get active tab
            const activeTab = document.querySelector('.nav-link.active');
            let category = '3r';
            
            if (activeTab.id === 'limbahb3-tab') {
                category = 'b3';
            } else if (activeTab.id === 'limbahcair-tab') {
                category = 'cair';
            }
            
            // Redirect to export URL
            window.location.href = '<?= base_url('admin-pusat/logbook/export-excel') ?>/' + category;
        }
        
        // Export to PDF
        function exportToPDF() {
            // Get active tab
            const activeTab = document.querySelector('.nav-link.active');
            let category = '3r';
            
            if (activeTab.id === 'limbahb3-tab') {
                category = 'b3';
            } else if (activeTab.id === 'limbahcair-tab') {
                category = 'cair';
            }
            
            // Redirect to export URL
            window.location.href = '<?= base_url('admin-pusat/logbook/export-pdf') ?>/' + category;
        }
        
        // Print Formal
        function printFormal() {
            // Get active tab
            const activeTab = document.querySelector('.nav-link.active');
            let category = '3r';
            
            if (activeTab.id === 'limbahb3-tab') {
                category = 'b3';
            } else if (activeTab.id === 'limbahcair-tab') {
                category = 'cair';
            }
            
            // Open print page in new window
            window.open('<?= base_url('admin-pusat/logbook/print-formal') ?>/' + category, '_blank');
        }
    </script>

    <!-- ========================================
         BACKUP DATA MODAL - CLEAN & PROFESSIONAL
         ======================================== -->
    <div class="modal fade" id="backupModal" tabindex="-1" aria-labelledby="backupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 550px;">
            <div class="modal-content border-0 shadow-lg">
                <!-- Header -->
                <div class="modal-header border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.25rem 1.5rem;">
                    <h5 class="modal-title text-white fw-bold" id="backupModalLabel">
                        <i class="fas fa-database me-2"></i>Backup Data Logbook
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form id="formBackup" method="POST">
                    <div class="modal-body" style="padding: 1.75rem 1.5rem;">
                        
                        <!-- Tipe Backup -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold mb-2" style="font-size: 0.9rem; color: #495057;">
                                <i class="fas fa-calendar-alt me-1" style="font-size: 0.85rem;"></i>Tipe Backup
                            </label>
                            <select class="form-select" id="tipe_backup" name="tipe_backup" required style="font-size: 0.95rem; padding: 0.6rem 0.75rem;">
                                <option value="">Pilih Tipe</option>
                                <option value="harian">Harian</option>
                                <option value="mingguan">Mingguan</option>
                                <option value="bulanan">Bulanan</option>
                            </select>
                        </div>

                        <!-- Kategori Data -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold mb-2" style="font-size: 0.9rem; color: #495057;">
                                <i class="fas fa-filter me-1" style="font-size: 0.85rem;"></i>Kategori Data
                            </label>
                            <select class="form-select" id="kategori_backup" name="kategori_backup" required style="font-size: 0.95rem; padding: 0.6rem 0.75rem;">
                                <option value="3r">Program 3R</option>
                                <option value="b3">Limbah B3</option>
                                <option value="cair">Limbah Cair</option>
                                <option value="all">Semua Kategori</option>
                            </select>
                        </div>

                        <!-- INPUT HARIAN -->
                        <div id="input_harian" class="input-periode mb-3" style="display: none;">
                            <label class="form-label fw-semibold mb-2" style="font-size: 0.9rem; color: #495057;">
                                <i class="fas fa-calendar-day me-1" style="font-size: 0.85rem;"></i>Pilih Tanggal
                            </label>
                            <input type="date" class="form-control" id="tanggal_harian" name="tanggal_harian" max="<?= date('Y-m-d') ?>" style="font-size: 0.95rem; padding: 0.6rem 0.75rem;">
                        </div>

                        <!-- INPUT MINGGUAN -->
                        <div id="input_mingguan" class="input-periode mb-3" style="display: none;">
                            <label class="form-label fw-semibold mb-2" style="font-size: 0.9rem; color: #495057;">
                                <i class="fas fa-calendar-week me-1" style="font-size: 0.85rem;"></i>Rentang Tanggal
                            </label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" max="<?= date('Y-m-d') ?>" placeholder="Mulai" style="font-size: 0.9rem; padding: 0.6rem 0.75rem;">
                                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Tanggal Mulai</small>
                                </div>
                                <div class="col-6">
                                    <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" max="<?= date('Y-m-d') ?>" placeholder="Akhir" style="font-size: 0.9rem; padding: 0.6rem 0.75rem;">
                                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Tanggal Akhir</small>
                                </div>
                            </div>
                        </div>

                        <!-- INPUT BULANAN -->
                        <div id="input_bulanan" class="input-periode mb-3" style="display: none;">
                            <label class="form-label fw-semibold mb-2" style="font-size: 0.9rem; color: #495057;">
                                <i class="fas fa-calendar-alt me-1" style="font-size: 0.85rem;"></i>Pilih Bulan & Tahun
                            </label>
                            <div class="row g-2">
                                <div class="col-7">
                                    <select class="form-select" id="bulan_pilihan" name="bulan_pilihan" style="font-size: 0.9rem; padding: 0.6rem 0.75rem;">
                                        <option value="">-- Bulan --</option>
                                        <option value="01">Januari</option>
                                        <option value="02">Februari</option>
                                        <option value="03">Maret</option>
                                        <option value="04">April</option>
                                        <option value="05">Mei</option>
                                        <option value="06">Juni</option>
                                        <option value="07">Juli</option>
                                        <option value="08">Agustus</option>
                                        <option value="09">September</option>
                                        <option value="10">Oktober</option>
                                        <option value="11">November</option>
                                        <option value="12">Desember</option>
                                    </select>
                                </div>
                                <div class="col-5">
                                    <select class="form-select" id="tahun_pilihan" name="tahun_pilihan" style="font-size: 0.9rem; padding: 0.6rem 0.75rem;">
                                        <option value="">-- Tahun --</option>
                                        <?php for($y = date('Y'); $y >= 2020; $y--): ?>
                                            <option value="<?= $y ?>"><?= $y ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Divider -->
                        <hr class="my-4" style="border-top: 1px solid #e9ecef;">

                        <!-- Section Hapus Data - Compact -->

                    </div>
                    
                    <!-- Footer -->
                    <div class="modal-footer border-0" style="padding: 1rem 1.5rem; background-color: #f8f9fa;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="font-size: 0.9rem; padding: 0.5rem 1.25rem;">
                            <i class="fas fa-times me-1"></i>Batal
                        </button>
                        <button type="button" class="btn btn-primary" id="btnDownloadBackup" onclick="console.log('ONCLICK TRIGGERED!')" style="font-size: 0.9rem; padding: 0.5rem 1.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; position: relative; z-index: 10; cursor: pointer;">
                            <i class="fas fa-download me-1"></i>Download Backup
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================================
         SCRIPT BACKUP MODAL - REBUILD TOTAL
         ======================================== -->
    <script>
        console.log('🚀 Backup Modal Script Loaded - REBUILD VERSION v2.0');
        
        // Variable global untuk tracking backup
        let backupSudahDiDownload = false;

        // ========================================
        // FUNGSI INIT: Setup Event Listeners
        // ========================================
        function initBackupModal() {
            console.log('🔧 Initializing Backup Modal...');
            
            // Get elements
            const tipeBackup = document.getElementById('tipe_backup');
            const btnDownload = document.getElementById('btnDownloadBackup');
            
            // Check if elements exist
            if (!tipeBackup) {
                console.error('❌ Element tipe_backup tidak ditemukan!');
                return;
            }
            if (!btnDownload) {
                console.error('❌ Element btnDownloadBackup tidak ditemukan!');
                return;
            }
            
            console.log('✅ Semua element ditemukan');
            
            // ========================================
            // EVENT 1: Toggle Input Berdasarkan Tipe
            // ========================================
            tipeBackup.addEventListener('change', function() {
                const tipe = this.value;
                console.log('📋 Tipe backup dipilih:', tipe);
                
                // Sembunyikan semua input
                document.querySelectorAll('.input-periode').forEach(el => {
                    el.style.display = 'none';
                });
                
                // Tampilkan input sesuai tipe
                if (tipe === 'harian') {
                    document.getElementById('input_harian').style.display = 'block';
                    console.log('✅ Input HARIAN ditampilkan');
                } else if (tipe === 'mingguan') {
                    document.getElementById('input_mingguan').style.display = 'block';
                    console.log('✅ Input MINGGUAN ditampilkan');
                } else if (tipe === 'bulanan') {
                    document.getElementById('input_bulanan').style.display = 'block';
                    console.log('✅ Input BULANAN ditampilkan');
                }
            });
            
            console.log('✅ Event listener tipe_backup terpasang');

            // ========================================
            // EVENT 2: Download Backup dengan AJAX
            // ========================================
            btnDownload.addEventListener('click', function(event) {
                console.log('🔽 Tombol Download Backup diklik!');
                
                const tipe = document.getElementById('tipe_backup').value;
                const kategori = document.getElementById('kategori_backup').value;
                
                console.log('Tipe:', tipe);
                console.log('Kategori:', kategori);
                
                // Validasi tipe backup
                if (!tipe) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian!',
                        text: 'Silakan pilih tipe backup terlebih dahulu!',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                
                let url = '<?= base_url('admin-pusat/logbook/backup') ?>?kategori=' + kategori + '&type=' + tipe;
                let params = {
                    kategori: kategori,
                    type: tipe
                };
                
                // Validasi dan build URL berdasarkan tipe
                if (tipe === 'harian') {
                    const tanggal = document.getElementById('tanggal_harian').value;
                    if (!tanggal) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Perhatian!',
                            text: 'Silakan pilih tanggal untuk backup harian!',
                            confirmButtonColor: '#3085d6'
                        });
                        return;
                    }
                    url += '&tanggal=' + tanggal;
                    params.tanggal = tanggal;
                    console.log('📅 Backup Harian - Tanggal:', tanggal);
                    
                } else if (tipe === 'mingguan') {
                    const mulai = document.getElementById('tanggal_mulai').value;
                    const akhir = document.getElementById('tanggal_akhir').value;
                    
                    if (!mulai || !akhir) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Perhatian!',
                            text: 'Silakan pilih tanggal mulai dan akhir untuk backup mingguan!',
                            confirmButtonColor: '#3085d6'
                        });
                        return;
                    }
                    
                    if (mulai > akhir) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Perhatian!',
                            text: 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir!',
                            confirmButtonColor: '#3085d6'
                        });
                        return;
                    }
                    
                    url += '&start_date=' + mulai + '&end_date=' + akhir;
                    params.start_date = mulai;
                    params.end_date = akhir;
                    console.log('📆 Backup Mingguan - Dari:', mulai, 'Sampai:', akhir);
                    
                } else if (tipe === 'bulanan') {
                    const bulan = document.getElementById('bulan_pilihan').value;
                    const tahun = document.getElementById('tahun_pilihan').value;
                    
                    if (!bulan || !tahun) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Perhatian!',
                            text: 'Silakan pilih bulan dan tahun untuk backup bulanan!',
                            confirmButtonColor: '#3085d6'
                        });
                        return;
                    }
                    
                    url += '&bulan=' + bulan + '&tahun=' + tahun;
                    params.bulan = bulan;
                    params.tahun = tahun;
                    console.log('🗓️ Backup Bulanan - Bulan:', bulan, 'Tahun:', tahun);
                }
                
                // Show loading
                const btn = event.currentTarget;
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Memproses Backup...';
                
                console.log('🌐 URL Download:', url);
                
                // Download menggunakan fetch + blob untuk tracking sukses
                fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    console.log('📡 Response status:', response.status);
                    console.log('📡 Response content-type:', response.headers.get('content-type'));
                    
                    const contentType = response.headers.get('content-type');
                    
                    // CRITICAL: Cek apakah response adalah JSON (error) atau Excel (sukses)
                    if (contentType && contentType.includes('application/json')) {
                        console.log('📋 Response adalah JSON (kemungkinan error)');
                        return response.json().then(data => {
                            throw {
                                isJsonError: true,
                                status: response.status,
                                data: data
                            };
                        });
                    }
                    
                    // Cek apakah response adalah Excel file
                    if (!contentType || !contentType.includes('spreadsheet')) {
                        console.error('❌ Response bukan Excel dan bukan JSON');
                        throw new Error('Response tidak valid. Bukan file Excel dan bukan JSON error.');
                    }
                    
                    // Get filename from header
                    const contentDisposition = response.headers.get('content-disposition');
                    let filename = 'Backup_Logbook.xlsx';
                    if (contentDisposition) {
                        const filenameMatch = contentDisposition.match(/filename="?(.+)"?/i);
                        if (filenameMatch) {
                            filename = filenameMatch[1];
                        }
                    }
                    
                    console.log('📄 Filename:', filename);
                    
                    return response.blob().then(blob => ({ blob, filename }));
                })
                .then(({ blob, filename }) => {
                    console.log('💾 Blob size:', blob.size, 'bytes');
                    
                    // Create download link
                    const downloadUrl = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = downloadUrl;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    
                    // Cleanup
                    window.URL.revokeObjectURL(downloadUrl);
                    document.body.removeChild(a);
                    
                    console.log('✅ File berhasil didownload!');
                    
                    // CRITICAL: Set flag backup sudah di-download SETELAH download berhasil
                    backupSudahDiDownload = true;
                    console.log('✅ Flag backup di-download: TRUE');
                    
                    // Simpan ke sessionStorage untuk persistence
                    const backupKey = `backup_${tipe}_${kategori}_${JSON.stringify(params)}`;
                    sessionStorage.setItem(backupKey, 'true');
                    sessionStorage.setItem('lastBackupParams', JSON.stringify(params));
                    console.log('💾 Backup status disimpan ke sessionStorage:', backupKey);
                    
                    // Reset button
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Backup Berhasil!',
                        html: `<p>File <strong>${filename}</strong> berhasil didownload.</p>` +
                              `<p class="mt-2">Anda sekarang dapat menghapus data yang sudah di-backup.</p>`,
                        confirmButtonColor: '#28a745',
                        timer: 3000
                    });
                })
                .catch(error => {
                    console.error('❌ Error download:', error);
                    
                    // Reset button
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    
                    // Handle JSON error dari server
                    if (error.isJsonError) {
                        const errorData = error.data;
                        console.log('📋 JSON Error Data:', errorData);
                        
                        let errorTitle = 'Download Gagal!';
                        let errorHtml = '';
                        
                        if (errorData.error === 'NO_DATA_DIKIRIM') {
                            // Ada data DRAFT
                            errorTitle = 'Tidak Ada Data DIKIRIM_KE_TPS';
                            errorHtml = `
                                <p><strong>${errorData.message}</strong></p>
                                <hr>
                                <p class="text-start">${errorData.detail}</p>
                                <div class="alert alert-warning text-start mt-3">
                                    <strong>📊 Statistik Data:</strong><br>
                                    • Total Data: ${errorData.total_all}<br>
                                    • Status DRAFT: ${errorData.total_draft}<br>
                                    • Status DIKIRIM_KE_TPS: ${errorData.total_dikirim}
                                </div>
                                <p class="text-start mt-2"><strong>Solusi:</strong></p>
                                <ol class="text-start">
                                    <li>Buka halaman input data</li>
                                    <li>Kirim data ke TPS</li>
                                    <li>Tunggu status berubah menjadi DIKIRIM_KE_TPS</li>
                                    <li>Lakukan backup kembali</li>
                                </ol>
                            `;
                        } else if (errorData.error === 'NO_DATA') {
                            // Tidak ada data sama sekali
                            errorTitle = 'Tidak Ada Data';
                            errorHtml = `
                                <p><strong>${errorData.message}</strong></p>
                                <hr>
                                <p class="text-start">${errorData.detail}</p>
                                <div class="alert alert-info text-start mt-3">
                                    <strong>ℹ️ Informasi:</strong><br>
                                    Tidak ditemukan data apapun untuk periode yang dipilih.
                                </div>
                            `;
                        } else {
                            // Error lainnya
                            errorHtml = `
                                <p><strong>${errorData.message}</strong></p>
                                ${errorData.detail ? `<p class="mt-2">${errorData.detail}</p>` : ''}
                            `;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: errorTitle,
                            html: errorHtml,
                            confirmButtonColor: '#d33',
                            width: '600px'
                        });
                    } else {
                        // Error JavaScript biasa
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan!',
                            html: `<p><strong>Error:</strong> ${error.message}</p>` +
                                  `<p class="mt-2">Silakan cek Console (F12) untuk detail error.</p>`,
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            });
            
            console.log('✅ Event listener btnDownloadBackup terpasang');

            console.log('🎉 Backup Modal Initialization Complete!');
        }

        // ========================================
        // FUNGSI 4: Reset Flag Saat Modal Ditutup
        // ========================================
        const backupModalEl = document.getElementById('backupModal');
        if (backupModalEl) {
            backupModalEl.addEventListener('hidden.bs.modal', function() {
                console.log('🚪 Modal ditutup - Reset flag backup');
                backupSudahDiDownload = false;
                
                // Reset form
                const form = document.getElementById('formBackup');
                if (form) form.reset();
                
                // Sembunyikan semua input
                document.querySelectorAll('.input-periode').forEach(el => {
                    el.style.display = 'none';
                });
                
                // TIDAK menghapus sessionStorage saat modal ditutup
                // Karena user mungkin ingin hapus data setelah download
                console.log('ℹ️ SessionStorage tetap dipertahankan untuk proses hapus');
            });
            console.log('✅ Event listener modal close terpasang');
        }

        // ========================================
        // FUNGSI 6: Check Element & Initialize
        // ========================================
        document.addEventListener('DOMContentLoaded', function() {
            console.log('✅ DOM Content Loaded');
            
            const requiredElements = [
                'backupModal',
                'tipe_backup',
                'input_harian',
                'input_mingguan',
                'input_bulanan',
                'tanggal_harian',
                'tanggal_mulai',
                'tanggal_akhir',
                'bulan_pilihan',
                'tahun_pilihan',
                'kategori_backup',
                'btnDownloadBackup'
            ];
            
            console.log('🔍 Checking required elements:');
            let allFound = true;
            requiredElements.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    console.log('  ✅', id, '- Type:', el.tagName, '- Disabled:', el.disabled);
                } else {
                    console.error('  ❌', id, 'NOT FOUND!');
                    allFound = false;
                }
            });
            
            if (allFound) {
                console.log('🎉 Semua element ditemukan! Initializing modal...');
                initBackupModal();
            } else {
                console.error('⚠️ Ada element yang hilang! Periksa HTML.');
            }
        });
        
        // Test klik manual dari console
        window.testBackupButton = function() {
            console.log('🧪 Testing backup button manually...');
            const btn = document.getElementById('btnDownloadBackup');
            if (btn) {
                console.log('Button found:', btn);
                console.log('Button disabled:', btn.disabled);
                console.log('Button type:', btn.type);
                btn.click();
            } else {
                console.error('Button not found!');
            }
        };
        
        console.log('💡 Tip: Ketik testBackupButton() di console untuk test manual');

        // ========================================
        // BULK DELETE FUNCTIONALITY
        // ========================================
        
        // Toggle Select All Checkbox
        function toggleSelectAll(kategori) {
            const selectAllCheckbox = document.getElementById('selectAll' + kategori.charAt(0).toUpperCase() + kategori.slice(1));
            const checkboxes = document.querySelectorAll('.checkbox-' + kategori);
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            
            updateBulkDeleteButton(kategori);
        }
        
        // Update Bulk Delete Button Visibility and Count
        function updateBulkDeleteButton(kategori) {
            const checkboxes = document.querySelectorAll('.checkbox-' + kategori + ':checked');
            const count = checkboxes.length;
            const btnId = 'btnBulkDelete' + (kategori === '3r' ? '3r' : kategori.charAt(0).toUpperCase() + kategori.slice(1));
            const countId = 'count' + (kategori === '3r' ? '3r' : kategori.charAt(0).toUpperCase() + kategori.slice(1));
            const btn = document.getElementById(btnId);
            const countSpan = document.getElementById(countId);
            
            if (count > 0) {
                btn.style.display = 'inline-block';
                countSpan.textContent = count;
            } else {
                btn.style.display = 'none';
            }
            
            // Update Select All checkbox state
            const allCheckboxes = document.querySelectorAll('.checkbox-' + kategori);
            const selectAllCheckbox = document.getElementById('selectAll' + kategori.charAt(0).toUpperCase() + kategori.slice(1));
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = (count === allCheckboxes.length && count > 0);
            }
        }
        
        // Bulk Delete Function
        function bulkDelete(kategori) {
            const checkboxes = document.querySelectorAll('.checkbox-' + kategori + ':checked');
            const ids = Array.from(checkboxes).map(cb => cb.value);
            
            if (ids.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: 'Silakan pilih minimal satu data untuk dihapus.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }
            
            // Konfirmasi dengan SweetAlert
            Swal.fire({
                title: '⚠️ Konfirmasi Hapus',
                html: '<div class="text-start">' +
                      '<p><strong>Apakah Anda yakin ingin menghapus ' + ids.length + ' data yang dipilih?</strong></p>' +
                      '<hr>' +
                      '<p class="text-muted mt-2"><small>Data yang dihapus tidak dapat dikembalikan.</small></p>' +
                      '</div>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Menghapus Data...',
                        html: 'Mohon tunggu, sedang memproses penghapusan <strong>' + ids.length + ' data</strong>.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Send AJAX request
                    fetch('<?= base_url('admin-pusat/logbook/bulk-delete') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            kategori: kategori,
                            ids: ids
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('HTTP error! status: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                html: data.message,
                                confirmButtonColor: '#28a745',
                                timer: 3000
                            }).then(() => {
                                // Reload page
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: data.message,
                                confirmButtonColor: '#d33'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan!',
                            text: 'Gagal menghapus data: ' + error.message,
                            confirmButtonColor: '#d33'
                        });
                    });
                }
            });
        }
        
        // Show/Hide bulk delete buttons based on active tab
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('[data-bs-toggle="tab"]');
            tabs.forEach(tab => {
                tab.addEventListener('shown.bs.tab', function(event) {
                    // Hide all bulk delete buttons
                    document.getElementById('btnBulkDelete3r').style.display = 'none';
                    document.getElementById('btnBulkDeleteB3').style.display = 'none';
                    document.getElementById('btnBulkDeleteCair').style.display = 'none';
                    
                    // Show the appropriate button if there are checked items
                    const target = event.target.getAttribute('data-bs-target');
                    if (target === '#program3r') {
                        updateBulkDeleteButton('3r');
                    } else if (target === '#limbahb3') {
                        updateBulkDeleteButton('b3');
                    } else if (target === '#limbahcair') {
                        updateBulkDeleteButton('cair');
                    }
                });
            });
        });
    </script>

</body>
</html>