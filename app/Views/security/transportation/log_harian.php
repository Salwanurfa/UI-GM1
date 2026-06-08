<?php
/**
 * Log Harian Kendaraan - Security
 * Mencatat akumulasi kendaraan keluar-masuk harian
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Log Harian Kendaraan' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f7fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #2c3e50;
        }

        .main-content {
            margin-left: 280px;
            padding: 24px;
            min-height: 100vh;
        }

        /* Page Header - Minimalis */
        .page-header {
            background: #2c3e50;
            color: white;
            padding: 20px 24px;
            border-radius: 8px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .page-header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 600;
            letter-spacing: -0.3px;
        }

        .page-header p {
            margin: 4px 0 0 0;
            opacity: 0.85;
            font-size: 14px;
            font-weight: 400;
        }

        /* Summary Cards - New Style (Minimalist & Clean) */
        .summary-card-new {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 16px;
            border-left: 4px solid;
            height: 100%;
        }

        .summary-card-new:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        .summary-card-new.blue {
            border-left-color: #5a8dee;
        }

        .summary-card-new.green {
            border-left-color: #39da8a;
        }

        .summary-card-new.orange {
            border-left-color: #fdac41;
        }

        .summary-card-new.purple {
            border-left-color: #9b59b6;
        }

        .summary-card-new .card-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            flex-shrink: 0;
        }

        .summary-card-new.blue .card-icon {
            background: linear-gradient(135deg, #5a8dee 0%, #4c7dd9 100%);
        }

        .summary-card-new.green .card-icon {
            background: linear-gradient(135deg, #39da8a 0%, #2ec77d 100%);
        }

        .summary-card-new.orange .card-icon {
            background: linear-gradient(135deg, #fdac41 0%, #f39c12 100%);
        }

        .summary-card-new.purple .card-icon {
            background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);
        }

        .summary-card-new .card-content {
            flex: 1;
        }

        .summary-card-new .card-content h3 {
            font-size: 32px;
            font-weight: 700;
            margin: 0 0 4px 0;
            color: #2c3e50;
            line-height: 1;
        }

        .summary-card-new .card-content p {
            margin: 0 0 4px 0;
            color: #5a6c7d;
            font-weight: 600;
            font-size: 14px;
        }

        .summary-card-new .card-content small {
            color: #a8b4c7;
            font-size: 12px;
        }

        /* Summary Cards - Soft & Clean */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .summary-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            border-left: 3px solid;
            transition: all 0.2s ease;
        }

        .summary-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .summary-card.blue { border-left-color: #5a8dee; }
        .summary-card.green { border-left-color: #39da8a; }
        .summary-card.orange { border-left-color: #fdac41; }

        .summary-card .icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: white;
            margin-bottom: 12px;
        }

        .summary-card.blue .icon { background: #5a8dee; }
        .summary-card.green .icon { background: #39da8a; }
        .summary-card.orange .icon { background: #fdac41; }

        .summary-card h3 {
            font-size: 28px;
            font-weight: 600;
            margin: 0;
            color: #2c3e50;
        }

        .summary-card p {
            margin: 4px 0 0 0;
            color: #7c8db5;
            font-weight: 400;
            font-size: 13px;
        }

        .summary-card small {
            color: #a8b4c7;
            font-size: 12px;
        }

        /* Form Card - Clean & Compact */
        .form-card {
            background: white;
            padding: 24px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            border: 1px solid #e8ecf1;
            margin-bottom: 24px;
        }

        .form-card h3 {
            color: #2c3e50;
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e8ecf1;
        }

        .form-label {
            font-size: 13px;
            font-weight: 500;
            color: #5a6c7d;
            margin-bottom: 6px;
        }

        .form-control, .form-select {
            border: 1px solid #d5dce6;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #5a8dee;
            box-shadow: 0 0 0 3px rgba(90, 141, 238, 0.1);
        }

        /* Table Card - Professional */
        .table-card {
            background: white;
            padding: 24px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            border: 1px solid #e8ecf1;
        }

        .table-card h3 {
            color: #2c3e50;
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e8ecf1;
        }

        /* Action Buttons - Soft Colors */
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .action-buttons .btn {
            font-size: 13px;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
        }

        /* DataTable Styling */
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #d5dce6;
            border-radius: 6px;
            padding: 6px 10px;
            font-size: 13px;
        }

        .table {
            font-size: 13px;
        }

        .table thead th {
            background: #f8f9fb;
            color: #5a6c7d;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
            border: none;
            padding: 12px 10px;
        }

        .table tbody tr {
            transition: background 0.15s ease;
        }

        .table tbody tr:hover {
            background: #f8f9fb;
        }

        .table tbody td {
            padding: 10px;
            vertical-align: middle;
        }

        /* Buttons - Soft & Professional */
        .btn {
            border-radius: 6px;
            padding: 8px 16px;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s ease;
            border: none;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        }

        .btn-primary {
            background: #5a8dee;
            color: white;
        }

        .btn-primary:hover {
            background: #4c7dd9;
        }

        .btn-success {
            background: #39da8a;
            color: white;
        }

        .btn-success:hover {
            background: #2ec77d;
        }

        .btn-danger {
            background: #d9534f;
            color: white;
        }

        .btn-danger:hover {
            background: #c9302c;
        }

        .btn-secondary {
            background: #7c8db5;
            color: white;
        }

        .btn-secondary:hover {
            background: #6c7da5;
        }

        .btn-info {
            background: #5a8dee;
            color: white;
        }

        .btn-outline-warning {
            border: 1px solid #fdac41;
            color: #fdac41;
            background: white;
        }

        .btn-outline-warning:hover {
            background: #fdac41;
            color: white;
        }

        .btn-outline-danger {
            border: 1px solid #d9534f;
            color: #d9534f;
            background: white;
        }

        .btn-outline-danger:hover {
            background: #d9534f;
            color: white;
        }

        /* Badge - Neutral & Outline */
        .badge {
            padding: 4px 10px;
            border-radius: 4px;
            font-weight: 500;
            font-size: 12px;
        }

        .badge.bg-primary {
            background: #e8f0fe !important;
            color: #5a8dee;
            border: 1px solid #d0e1fd;
        }

        .badge.bg-info {
            background: #e8f0fe !important;
            color: #5a8dee;
            border: 1px solid #d0e1fd;
        }

        .badge.bg-success {
            background: #e8f8f0 !important;
            color: #39da8a;
            border: 1px solid #d0f0e0;
        }

        /* Alert Messages */
        .alert {
            border-radius: 6px;
            border: none;
            font-size: 14px;
            padding: 12px 16px;
        }

        .alert-success {
            background: #e8f8f0;
            color: #2c7a5f;
        }

        .alert-danger {
            background: #fdeaea;
            color: #c9302c;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 16px;
            }

            .summary-cards {
                grid-template-columns: 1fr;
            }

            .summary-card-new {
                padding: 20px;
            }

            .summary-card-new .card-icon {
                width: 48px;
                height: 48px;
                font-size: 20px;
            }

            .summary-card-new .card-content h3 {
                font-size: 28px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .action-buttons .btn {
                width: 100%;
            }
        }

        /* Modal Styling */
        .modal-header {
            border-bottom: 1px solid #e8ecf1;
        }

        .modal-footer {
            border-top: 1px solid #e8ecf1;
        }

        .modal-body {
            padding: 24px;
        }

        .modal-content {
            border-radius: 8px;
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }

        .modal-title {
            font-size: 18px;
            font-weight: 600;
        }

        /* Form in Modal */
        .modal .form-label {
            font-size: 13px;
            font-weight: 500;
            color: #5a6c7d;
            margin-bottom: 6px;
        }

        .modal .form-control,
        .modal .form-select {
            border: 1px solid #d5dce6;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 14px;
        }

        .modal .form-control:focus,
        .modal .form-select:focus {
            border-color: #5a8dee;
            box-shadow: 0 0 0 3px rgba(90, 141, 238, 0.1);
        }

        .modal small.text-muted {
            font-size: 12px;
            color: #a8b4c7;
        }
    </style>
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fas fa-clipboard-list"></i> Log Harian Kendaraan</h1>
            <p>Mencatat akumulasi kendaraan keluar-masuk kampus setiap hari</p>
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

        <!-- Summary Cards - Hari Ini -->
        <div class="row mb-4">
            <!-- Card 1: Total Aktivitas -->
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="summary-card-new blue">
                    <div class="card-icon">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <div class="card-content">
                        <h3><?= number_format($total_masuk_hari_ini + $total_keluar_hari_ini) ?></h3>
                        <p>Total Aktivitas Hari Ini</p>
                        <small class="text-muted">Masuk + Keluar</small>
                    </div>
                </div>
            </div>

            <!-- Card 2: Kendaraan Masuk -->
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="summary-card-new green">
                    <div class="card-icon">
                        <i class="fas fa-arrow-circle-down"></i>
                    </div>
                    <div class="card-content">
                        <h3><?= number_format($total_masuk_hari_ini) ?></h3>
                        <p>Kendaraan Masuk</p>
                        <small class="text-muted">Total hari ini</small>
                    </div>
                </div>
            </div>

            <!-- Card 3: Kendaraan Keluar -->
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="summary-card-new orange">
                    <div class="card-icon">
                        <i class="fas fa-arrow-circle-up"></i>
                    </div>
                    <div class="card-content">
                        <h3><?= number_format($total_keluar_hari_ini) ?></h3>
                        <p>Kendaraan Keluar</p>
                        <small class="text-muted">Total hari ini</small>
                    </div>
                </div>
            </div>

            <!-- Card 4: Total Transaksi -->
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="summary-card-new purple">
                    <div class="card-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div class="card-content">
                        <h3><?= count(array_filter($all_logs ?? [], function($log) { return $log['tanggal'] === date('Y-m-d'); })) ?></h3>
                        <p>Total Transaksi</p>
                        <small class="text-muted">Record hari ini</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Monitoring -->
        <div class="table-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0"><i class="fas fa-table"></i> Data Log Harian</h3>
                <div class="action-buttons">
                    <button type="button" class="btn btn-primary btn-sm" onclick="openAddModal()">
                        <i class="fas fa-plus"></i> Tambah Log Kendaraan
                    </button>
                    <button type="button" id="btnBulkDelete" class="btn btn-danger btn-sm" onclick="bulkDelete()" style="display: none;">
                        <i class="fas fa-trash-alt"></i> Hapus Data Terpilih (<span id="selectedCount">0</span>)
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="openBackupModal()">
                        <i class="fas fa-database"></i> Back-up
                    </button>
                    <a href="<?= base_url('/security/transportation/export-log-harian-excel') ?>" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Excel
                    </a>
                    <a href="<?= base_url('/security/transportation/export-log-harian-pdf') ?>" class="btn btn-danger btn-sm" target="_blank">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover" id="logTable">
                    <thead>
                        <tr>
                            <th style="width: 3%;">
                                <input type="checkbox" id="selectAll" class="form-check-input" style="cursor: pointer;">
                            </th>
                            <th style="width: 5%;">No</th>
                            <th style="width: 12%;">Tanggal</th>
                            <th style="width: 15%;">Jenis Kendaraan</th>
                            <th style="width: 10%;">Masuk</th>
                            <th style="width: 10%;">Keluar</th>
                            <th style="width: 12%;">Total</th>
                            <th style="width: 26%;">Keterangan</th>
                            <th style="width: 7%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($all_logs)): ?>
                            <?php $no = 1; foreach ($all_logs as $log): ?>
                            <tr <?= (!empty($log['is_backed_up']) && $log['is_backed_up'] == 1) ? 'style="background: #f8f9fb;"' : '' ?>>
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input row-checkbox" data-id="<?= $log['id'] ?>" style="cursor: pointer;">
                                </td>
                                <td class="text-center"><?= $no++ ?></td>
                                <td>
                                    <?= date('d/m/Y', strtotime($log['tanggal'])) ?>
                                    <?php if (!empty($log['is_backed_up']) && $log['is_backed_up'] == 1): ?>
                                        <br><small class="badge bg-success">Backed-up</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?= esc($log['jenis_kendaraan']) ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info"><?= number_format($log['jumlah_masuk']) ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success"><?= number_format($log['jumlah_keluar']) ?></span>
                                </td>
                                <td class="text-center">
                                    <strong style="color: #2c3e50;"><?= number_format($log['jumlah_masuk'] + $log['jumlah_keluar']) ?></strong>
                                </td>
                                <td style="color: #5a6c7d;"><?= esc($log['keterangan'] ?? '-') ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-warning btn-sm btn-edit-log" data-id="<?= $log['id'] ?>" title="Edit Log">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="fas fa-inbox fa-3x mb-3" style="opacity: 0.3;"></i>
                                    <p>Belum ada data log harian</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah/Edit Log Harian -->
    <div class="modal fade" id="logModal" tabindex="-1" aria-labelledby="logModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: #2c3e50; color: white;">
                    <h5 class="modal-title" id="logModalLabel">
                        <i class="fas fa-plus-circle"></i> <span id="modalTitle">Tambah Log Kendaraan</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= base_url('/security/transportation/simpan-log-harian') ?>" method="POST" id="formLogHarian">
                    <div class="modal-body">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" id="log_id" value="">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-calendar"></i> Tanggal <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="tanggal" id="tanggal" value="<?= date('Y-m-d') ?>" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-car"></i> Jenis Kendaraan <span class="text-danger">*</span></label>
                                <select class="form-select" name="jenis_kendaraan" id="jenis_kendaraan" required>
                                    <option value="">Pilih Jenis Kendaraan</option>
                                    <option value="Mobil">Mobil</option>
                                    <option value="Motor">Motor</option>
                                    <option value="Sepeda">Sepeda</option>
                                    <option value="Bus">Bus</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-arrow-right"></i> Jumlah Masuk <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="jumlah_masuk" id="jumlah_masuk" value="0" min="0" required>
                                <small class="text-muted">Jumlah kendaraan yang masuk kampus</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-arrow-left"></i> Jumlah Keluar <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="jumlah_keluar" id="jumlah_keluar" value="0" min="0" required>
                                <small class="text-muted">Jumlah kendaraan yang keluar kampus</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label"><i class="fas fa-sticky-note"></i> Keterangan</label>
                                <textarea class="form-control" name="keterangan" id="keterangan" rows="3" placeholder="Catatan tambahan (opsional)..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Log
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Backup Data Kendaraan -->
    <div class="modal fade" id="modalBackupTransport" tabindex="-1" aria-labelledby="modalBackupTransportLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="modal-title" id="modalBackupTransportLabel">
                        <i class="fas fa-database"></i> Backup Data Log Kendaraan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Info Alert (Dynamic) -->
                    <div class="alert alert-info mb-4" id="backup_info_alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Informasi:</strong> <span id="backup_info_text">Pilih tipe backup untuk memulai.</span>
                    </div>

                    <!-- Form Backup -->
                    <form id="formBackupTransport">
                        <div class="row">
                            <!-- Tipe Backup -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    Tipe Backup <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="backup_tipe" name="backup_tipe" required>
                                    <option value="">Pilih Tipe Backup</option>
                                    <option value="Harian">Harian</option>
                                    <option value="Mingguan">Mingguan</option>
                                    <option value="Bulanan">Bulanan</option>
                                </select>
                                <small class="text-muted">Pilih periode backup data</small>
                            </div>

                            <!-- Kategori Data -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-car me-1"></i>
                                    Kategori Data <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="backup_kategori" name="backup_kategori" required>
                                    <option value="Semua" selected>Semua Jenis Kendaraan</option>
                                    <option value="Mobil">Mobil</option>
                                    <option value="Motor">Motor</option>
                                    <option value="Sepeda">Sepeda</option>
                                    <option value="Bus">Bus</option>
                                </select>
                                <small class="text-muted">Pilih kategori kendaraan</small>
                            </div>
                        </div>

                        <!-- Input Harian (Single Date) -->
                        <div class="row" id="input_backup_harian" style="display: none;">
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-calendar-day me-1"></i>
                                    Tanggal <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control" id="backup_tanggal_harian" name="backup_tanggal_harian" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>">
                                <small class="text-muted">Pilih tanggal untuk backup harian</small>
                            </div>
                        </div>

                        <!-- Input Mingguan (Date Range) -->
                        <div class="row" id="input_backup_mingguan" style="display: none;">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-calendar-day me-1"></i>
                                    Tanggal Mulai <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control" id="backup_tanggal_mulai" name="backup_tanggal_mulai" max="<?= date('Y-m-d') ?>">
                                <small class="text-muted">Tanggal awal periode mingguan</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-calendar-check me-1"></i>
                                    Tanggal Selesai <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control" id="backup_tanggal_selesai" name="backup_tanggal_selesai" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>">
                                <small class="text-muted">Tanggal akhir periode mingguan</small>
                            </div>
                        </div>

                        <!-- Input Bulanan (Month & Year Dropdown) -->
                        <div class="row" id="input_backup_bulanan" style="display: none;">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    Pilih Bulan <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="backup_bulan" name="backup_bulan">
                                    <option value="">Pilih Bulan</option>
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
                                <small class="text-muted">Bulan periode backup</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-calendar me-1"></i>
                                    Pilih Tahun <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="backup_tahun" name="backup_tahun">
                                    <option value="">Pilih Tahun</option>
                                    <?php for ($year = 2024; $year <= 2030; $year++): ?>
                                        <option value="<?= $year ?>" <?= $year == date('Y') ? 'selected' : '' ?>><?= $year ?></option>
                                    <?php endfor; ?>
                                </select>
                                <small class="text-muted">Tahun periode backup</small>
                            </div>
                        </div>

                        <!-- Summary Info -->
                        <div class="card bg-light border-0 mb-3">
                            <div class="card-body">
                                <h6 class="card-title mb-2">
                                    <i class="fas fa-chart-bar me-2"></i>
                                    Ringkasan Data yang Akan Di-backup
                                </h6>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="fw-bold text-primary" style="font-size: 24px;" id="backup_preview_count">-</div>
                                        <small class="text-muted">Total Record</small>
                                    </div>
                                    <div class="col-4">
                                        <div class="fw-bold text-success" style="font-size: 24px;" id="backup_preview_masuk">-</div>
                                        <small class="text-muted">Total Masuk</small>
                                    </div>
                                    <div class="col-4">
                                        <div class="fw-bold text-warning" style="font-size: 24px;" id="backup_preview_keluar">-</div>
                                        <small class="text-muted">Total Keluar</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="button" class="btn btn-primary" onclick="executeBackup()" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                        <i class="fas fa-database"></i> Download & Backup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // ============================================
        // DEBUGGING: Check if scripts are loaded
        // ============================================
        console.log('=== SCRIPT LOADED ===');
        console.log('jQuery loaded:', typeof jQuery !== 'undefined');
        console.log('Bootstrap loaded:', typeof bootstrap !== 'undefined');
        console.log('Swal loaded:', typeof Swal !== 'undefined');
        
        // Initialize DataTable
        $(document).ready(function() {
            console.log('=== DOCUMENT READY ===');
            
            var table = $('#logTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
                },
                order: [[2, 'desc']], // Sort by date descending (column index changed due to checkbox)
                pageLength: 25,
                columnDefs: [
                    { orderable: false, targets: 0 } // Disable sorting on checkbox column
                ]
            });
            
            // Re-bind checkbox events after table draw (pagination, filter, sort)
            table.on('draw', function() {
                bindCheckboxEvents();
            });
            
            // Initial bind
            bindCheckboxEvents();
            
            // ============================================
            // EVENT DELEGATION: Edit Button Click
            // ============================================
            console.log('=== REGISTERING EDIT BUTTON EVENT ===');
            
            $(document).on('click', '.btn-edit-log', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('=== EDIT BUTTON CLICKED ===');
                console.log('Button element:', this);
                
                const id = $(this).data('id');
                console.log('Log ID from data-id:', id);
                
                if (!id) {
                    console.error('ERROR: No ID found on button!');
                    Swal.fire('Error', 'ID log tidak ditemukan!', 'error');
                    return;
                }
                
                // Call editLog function
                editLog(id);
            });
            
            console.log('=== EDIT BUTTON EVENT REGISTERED ===');
        });
        
        // Bind checkbox events
        function bindCheckboxEvents() {
            // Select All functionality
            $('#selectAll').off('change').on('change', function() {
                const isChecked = $(this).prop('checked');
                $('.row-checkbox:visible').prop('checked', isChecked);
                updateBulkDeleteButton();
            });
            
            // Individual checkbox change
            $('.row-checkbox').off('change').on('change', function() {
                updateSelectAllCheckbox();
                updateBulkDeleteButton();
            });
        }
        
        // Update Select All checkbox state
        function updateSelectAllCheckbox() {
            const totalCheckboxes = $('.row-checkbox:visible').length;
            const checkedCheckboxes = $('.row-checkbox:visible:checked').length;
            
            $('#selectAll').prop('checked', totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes);
        }
        
        // Update Bulk Delete button visibility and count
        function updateBulkDeleteButton() {
            const checkedCount = $('.row-checkbox:checked').length;
            
            if (checkedCount > 0) {
                $('#btnBulkDelete').show();
                $('#selectedCount').text(checkedCount);
            } else {
                $('#btnBulkDelete').hide();
            }
        }
        
        // Bulk Delete function
        function bulkDelete() {
            const selectedIds = [];
            $('.row-checkbox:checked').each(function() {
                selectedIds.push($(this).data('id'));
            });
            
            if (selectedIds.length === 0) {
                Swal.fire('Peringatan', 'Pilih minimal satu data untuk dihapus', 'warning');
                return;
            }
            
            Swal.fire({
                title: 'Konfirmasi Hapus Massal',
                html: `Apakah Anda yakin ingin menghapus <strong>${selectedIds.length}</strong> data yang dipilih?<br><small class="text-muted">Data yang dihapus tidak dapat dikembalikan.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash"></i> Ya, Hapus Semua!',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    // Get CSRF token
                    const csrfToken = '<?= csrf_hash() ?>';
                    const csrfName = '<?= csrf_token() ?>';
                    
                    return fetch('<?= base_url('security/transportation/bulk-delete-log-harian') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            [csrfName]: csrfToken,
                            ids: selectedIds
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (!data.success) {
                            throw new Error(data.message || 'Gagal menghapus data');
                        }
                        return data;
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`Request failed: ${error.message}`);
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Berhasil!',
                        html: `<p>${result.value.message}</p><p class="text-muted small">Total data dihapus: ${result.value.deleted_count}</p>`,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Reload page to show updated data
                        window.location.reload();
                    });
                }
            });
        }

        // Open Add Modal
        function openAddModal() {
            // Reset form
            document.getElementById('formLogHarian').reset();
            document.getElementById('log_id').value = '';
            document.getElementById('tanggal').value = '<?= date('Y-m-d') ?>';
            document.getElementById('jumlah_masuk').value = '0';
            document.getElementById('jumlah_keluar').value = '0';
            document.getElementById('keterangan').value = '';
            
            // Update modal title
            document.getElementById('modalTitle').textContent = 'Tambah Log Kendaraan';
            
            // Show modal
            var modal = new bootstrap.Modal(document.getElementById('logModal'));
            modal.show();
        }

        // Reset Form when modal is closed
        $('#logModal').on('hidden.bs.modal', function () {
            console.log('Modal closed - resetting form');
            document.getElementById('formLogHarian').reset();
            document.getElementById('log_id').value = '';
            document.getElementById('tanggal').value = '<?= date('Y-m-d') ?>';
            document.getElementById('jumlah_masuk').value = '0';
            document.getElementById('jumlah_keluar').value = '0';
            
            // Reset modal title
            const modalTitle = document.getElementById('modalTitle');
            if (modalTitle) {
                modalTitle.innerHTML = '<i class="fas fa-plus-circle"></i> Tambah Log Kendaraan';
            }
            
            // Reset button to add mode
            const submitBtn = document.querySelector('#formLogHarian button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-save"></i> Simpan Log';
                submitBtn.classList.remove('btn-success');
                submitBtn.classList.add('btn-primary');
            }
        });

        // ============================================
        // EDIT LOG FUNCTION
        // ============================================
        function editLog(id) {
            console.log('');
            console.log('╔════════════════════════════════════════╗');
            console.log('║   EDIT LOG FUNCTION CALLED             ║');
            console.log('╚════════════════════════════════════════╝');
            console.log('Fungsi editLog terpanggil!');
            console.log('Edit log ID:', id);
            console.log('ID type:', typeof id);
            
            if (!id) {
                console.error('ERROR: ID is null or undefined!');
                Swal.fire('Error', 'ID log tidak valid!', 'error');
                return;
            }
            
            // STEP 1: Check if modal exists
            console.log('');
            console.log('--- STEP 1: Checking Modal ---');
            const modal = document.getElementById('logModal');
            console.log('Modal element:', modal);
            
            if (!modal) {
                console.error('ERROR: Modal #logModal not found!');
                Swal.fire('Error', 'Modal tidak ditemukan!', 'error');
                return;
            }
            
            // STEP 2: Open modal FIRST
            console.log('');
            console.log('--- STEP 2: Opening Modal ---');
            try {
                const bsModal = new bootstrap.Modal(modal);
                bsModal.show();
                console.log('✓ Modal opened successfully');
            } catch (error) {
                console.error('ERROR opening modal:', error);
                Swal.fire('Error', 'Gagal membuka modal: ' + error.message, 'error');
                return;
            }
            
            // Change modal title
            const modalTitle = document.getElementById('modalTitle');
            if (modalTitle) {
                modalTitle.innerHTML = '<i class="fas fa-edit"></i> Edit Log Kendaraan';
                console.log('✓ Modal title changed');
            }
            
            // STEP 3: Fetch data from server
            console.log('');
            console.log('--- STEP 3: Fetching Data ---');
            const url = `<?= base_url('/security/transportation/get-log-harian/') ?>${id}`;
            console.log('Fetch URL:', url);
            
            fetch(url)
                .then(response => {
                    console.log('Response received');
                    console.log('- Status:', response.status);
                    console.log('- OK:', response.ok);
                    console.log('- Status Text:', response.statusText);
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(result => {
                    console.log('');
                    console.log('--- STEP 4: Processing Response ---');
                    console.log('Result:', result);
                    
                    if (result.success) {
                        const data = result.data;
                        console.log('Data received:', data);
                        
                        // STEP 5: Fill form with existing data
                        console.log('');
                        console.log('--- STEP 5: Filling Form ---');
                        
                        const logIdField = document.getElementById('log_id');
                        const tanggalField = document.getElementById('tanggal');
                        const jenisField = document.getElementById('jenis_kendaraan');
                        const masukField = document.getElementById('jumlah_masuk');
                        const keluarField = document.getElementById('jumlah_keluar');
                        const keteranganField = document.getElementById('keterangan');
                        
                        console.log('Form fields check:');
                        console.log('- log_id:', logIdField ? '✓ FOUND' : '✗ NOT FOUND');
                        console.log('- tanggal:', tanggalField ? '✓ FOUND' : '✗ NOT FOUND');
                        console.log('- jenis_kendaraan:', jenisField ? '✓ FOUND' : '✗ NOT FOUND');
                        console.log('- jumlah_masuk:', masukField ? '✓ FOUND' : '✗ NOT FOUND');
                        console.log('- jumlah_keluar:', keluarField ? '✓ FOUND' : '✗ NOT FOUND');
                        console.log('- keterangan:', keteranganField ? '✓ FOUND' : '✗ NOT FOUND');
                        
                        if (!logIdField || !tanggalField || !jenisField || !masukField || !keluarField) {
                            console.error('ERROR: Some form fields not found!');
                            Swal.fire('Error', 'Form fields tidak lengkap!', 'error');
                            return;
                        }
                        
                        // Fill the form
                        console.log('Filling form fields...');
                        logIdField.value = data.id;
                        tanggalField.value = data.tanggal;
                        jenisField.value = data.jenis_kendaraan;
                        masukField.value = data.jumlah_masuk;
                        keluarField.value = data.jumlah_keluar;
                        if (keteranganField) {
                            keteranganField.value = data.keterangan || '';
                        }
                        
                        console.log('✓ Form filled successfully!');
                        
                        // STEP 6: Change button to update mode
                        console.log('');
                        console.log('--- STEP 6: Changing Button ---');
                        const submitBtn = document.querySelector('#formLogHarian button[type="submit"]');
                        if (submitBtn) {
                            submitBtn.innerHTML = '<i class="fas fa-save"></i> Update Log';
                            submitBtn.classList.remove('btn-primary');
                            submitBtn.classList.add('btn-success');
                            console.log('✓ Submit button changed to UPDATE mode');
                        }
                        
                        // STEP 7: Show success notification
                        console.log('');
                        console.log('--- STEP 7: Showing Notification ---');
                        Swal.fire({
                            title: 'Mode Edit',
                            text: 'Form telah terisi dengan data yang akan diedit',
                            icon: 'info',
                            timer: 2000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                        
                        console.log('');
                        console.log('╔════════════════════════════════════════╗');
                        console.log('║   EDIT LOG COMPLETE ✓                  ║');
                        console.log('╚════════════════════════════════════════╝');
                        console.log('');
                    } else {
                        console.error('ERROR from server:', result.message);
                        Swal.fire('Error', result.message || 'Gagal mengambil data log', 'error');
                    }
                })
                .catch(error => {
                    console.error('');
                    console.error('╔════════════════════════════════════════╗');
                    console.error('║   FETCH ERROR ✗                        ║');
                    console.error('╚════════════════════════════════════════╝');
                    console.error('Fetch error:', error);
                    console.error('Error message:', error.message);
                    console.error('Error stack:', error.stack);
                    Swal.fire('Error', 'Gagal mengambil data log: ' + error.message, 'error');
                });
        }

        // Reset Form - Clear all fields and reset to add mode
        function resetForm() {
            document.getElementById('formLogHarian').reset();
            document.getElementById('log_id').value = '';
            document.getElementById('tanggal').value = '<?= date('Y-m-d') ?>';
            document.getElementById('jumlah_masuk').value = '0';
            document.getElementById('jumlah_keluar').value = '0';
            
            // Reset button to add mode
            const submitBtn = document.querySelector('#formLogHarian button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-save"></i> Simpan Log';
            submitBtn.classList.remove('btn-success');
            submitBtn.classList.add('btn-primary');
        }


        // Open Backup Modal
        function openBackupModal() {
            // Reset form
            document.getElementById('formBackupTransport').reset();
            document.getElementById('backup_tipe').value = '';
            document.getElementById('backup_kategori').value = 'Semua';
            
            // Hide all date inputs initially
            $('#input_backup_harian').hide();
            $('#input_backup_mingguan').hide();
            $('#input_backup_bulanan').hide();
            
            // Reset preview
            document.getElementById('backup_preview_count').textContent = '-';
            document.getElementById('backup_preview_masuk').textContent = '-';
            document.getElementById('backup_preview_keluar').textContent = '-';
            
            // Reset info text
            document.getElementById('backup_info_text').textContent = 'Pilih tipe backup untuk memulai.';
            
            // Show modal
            var modal = new bootstrap.Modal(document.getElementById('modalBackupTransport'));
            modal.show();
        }

        // Load Backup Preview
        function loadBackupPreview() {
            const tipe = document.getElementById('backup_tipe').value;
            const kategori = document.getElementById('backup_kategori').value;
            
            // Check if tipe is selected
            if (!tipe) {
                document.getElementById('backup_preview_count').textContent = '-';
                document.getElementById('backup_preview_masuk').textContent = '-';
                document.getElementById('backup_preview_keluar').textContent = '-';
                return;
            }
            
            // Build query params based on tipe
            let params = new URLSearchParams();
            params.append('tipe', tipe);
            
            if (kategori && kategori !== 'Semua') {
                params.append('kategori', kategori);
            }
            
            // Add date parameters based on tipe
            if (tipe === 'Harian') {
                const tanggalHarian = document.getElementById('backup_tanggal_harian').value;
                if (tanggalHarian) {
                    params.append('tanggal_harian', tanggalHarian);
                }
            } else if (tipe === 'Mingguan') {
                const tanggalMulai = document.getElementById('backup_tanggal_mulai').value;
                const tanggalSelesai = document.getElementById('backup_tanggal_selesai').value;
                if (tanggalMulai) params.append('tanggal_mulai', tanggalMulai);
                if (tanggalSelesai) params.append('tanggal_selesai', tanggalSelesai);
            } else if (tipe === 'Bulanan') {
                const bulan = document.getElementById('backup_bulan').value;
                const tahun = document.getElementById('backup_tahun').value;
                if (bulan) params.append('bulan', bulan);
                if (tahun) params.append('tahun', tahun);
            }
            
            // Fetch preview data
            fetch(`<?= base_url('/security/transportation/backup-preview') ?>?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('backup_preview_count').textContent = data.total_records || 0;
                        document.getElementById('backup_preview_masuk').textContent = data.total_masuk || 0;
                        document.getElementById('backup_preview_keluar').textContent = data.total_keluar || 0;
                        
                        // Update info text
                        updateBackupInfoText(tipe, data);
                    }
                })
                .catch(error => {
                    console.error('Error loading preview:', error);
                });
        }
        
        // Update Backup Info Text
        function updateBackupInfoText(tipe, data) {
            let infoText = '';
            const kategori = document.getElementById('backup_kategori').value;
            const kategoriText = kategori === 'Semua' ? 'semua jenis kendaraan' : kategori;
            
            if (tipe === 'Harian') {
                const tanggal = document.getElementById('backup_tanggal_harian').value;
                if (tanggal) {
                    const dateObj = new Date(tanggal);
                    const options = { day: 'numeric', month: 'long', year: 'numeric' };
                    const formattedDate = dateObj.toLocaleDateString('id-ID', options);
                    infoText = `Anda akan melakukan backup data ${kategoriText} untuk tanggal ${formattedDate}.`;
                } else {
                    infoText = 'Pilih tanggal untuk backup harian.';
                }
            } else if (tipe === 'Mingguan') {
                const mulai = document.getElementById('backup_tanggal_mulai').value;
                const selesai = document.getElementById('backup_tanggal_selesai').value;
                if (mulai && selesai) {
                    const dateStart = new Date(mulai);
                    const dateEnd = new Date(selesai);
                    const options = { day: 'numeric', month: 'long', year: 'numeric' };
                    const formattedStart = dateStart.toLocaleDateString('id-ID', options);
                    const formattedEnd = dateEnd.toLocaleDateString('id-ID', options);
                    infoText = `Anda akan melakukan backup data ${kategoriText} untuk periode ${formattedStart} sampai ${formattedEnd}.`;
                } else {
                    infoText = 'Pilih rentang tanggal untuk backup mingguan.';
                }
            } else if (tipe === 'Bulanan') {
                const bulan = document.getElementById('backup_bulan').value;
                const tahun = document.getElementById('backup_tahun').value;
                if (bulan && tahun) {
                    const monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                                      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    const bulanText = monthNames[parseInt(bulan)];
                    infoText = `Anda akan melakukan backup data ${kategoriText} untuk bulan ${bulanText} ${tahun}.`;
                } else {
                    infoText = 'Pilih bulan dan tahun untuk backup bulanan.';
                }
            }
            
            document.getElementById('backup_info_text').textContent = infoText;
        }
        
        // Handle Tipe Backup Change
        $('#backup_tipe').on('change', function() {
            const tipe = $(this).val();
            
            // Hide all date inputs with smooth transition
            $('#input_backup_harian').hide(300);
            $('#input_backup_mingguan').hide(300);
            $('#input_backup_bulanan').hide(300);
            
            // Show appropriate input based on tipe
            if (tipe === 'Harian') {
                $('#input_backup_harian').show(300);
                // Set default to today
                document.getElementById('backup_tanggal_harian').value = '<?= date('Y-m-d') ?>';
            } else if (tipe === 'Mingguan') {
                $('#input_backup_mingguan').show(300);
                // Set default to last 7 days
                const today = new Date();
                const weekAgo = new Date(today);
                weekAgo.setDate(today.getDate() - 7);
                document.getElementById('backup_tanggal_mulai').value = weekAgo.toISOString().split('T')[0];
                document.getElementById('backup_tanggal_selesai').value = today.toISOString().split('T')[0];
            } else if (tipe === 'Bulanan') {
                $('#input_backup_bulanan').show(300);
                // Set default to current month/year
                const currentMonth = ('0' + (new Date().getMonth() + 1)).slice(-2);
                const currentYear = new Date().getFullYear();
                document.getElementById('backup_bulan').value = currentMonth;
                document.getElementById('backup_tahun').value = currentYear;
            }
            
            // Load preview after transition
            setTimeout(function() {
                loadBackupPreview();
            }, 350);
        });

        // Execute Backup (Download Excel + Update Status)
        function executeBackup() {
            const tipe = document.getElementById('backup_tipe').value;
            const kategori = document.getElementById('backup_kategori').value;
            
            if (!tipe) {
                Swal.fire('Error', 'Pilih tipe backup terlebih dahulu', 'error');
                return;
            }
            
            // Validate date inputs based on tipe
            let dateParams = {};
            if (tipe === 'Harian') {
                const tanggalHarian = document.getElementById('backup_tanggal_harian').value;
                if (!tanggalHarian) {
                    Swal.fire('Error', 'Pilih tanggal untuk backup harian', 'error');
                    return;
                }
                dateParams.tanggal_harian = tanggalHarian;
            } else if (tipe === 'Mingguan') {
                const tanggalMulai = document.getElementById('backup_tanggal_mulai').value;
                const tanggalSelesai = document.getElementById('backup_tanggal_selesai').value;
                if (!tanggalMulai || !tanggalSelesai) {
                    Swal.fire('Error', 'Pilih rentang tanggal untuk backup mingguan', 'error');
                    return;
                }
                dateParams.tanggal_mulai = tanggalMulai;
                dateParams.tanggal_selesai = tanggalSelesai;
            } else if (tipe === 'Bulanan') {
                const bulan = document.getElementById('backup_bulan').value;
                const tahun = document.getElementById('backup_tahun').value;
                if (!bulan || !tahun) {
                    Swal.fire('Error', 'Pilih bulan dan tahun untuk backup bulanan', 'error');
                    return;
                }
                dateParams.bulan = bulan;
                dateParams.tahun = tahun;
            }
            
            // Show loading
            Swal.fire({
                title: 'Memproses Backup...',
                html: 'Mohon tunggu, file Excel sedang dibuat dan status data sedang diperbarui.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Get CSRF token
            const csrfToken = '<?= csrf_hash() ?>';
            const csrfName = '<?= csrf_token() ?>';
            
            // Build request body
            let requestBody = {
                [csrfName]: csrfToken,
                tipe: tipe,
                kategori: kategori,
                ...dateParams
            };
            
            // Step 1: Execute backup and download Excel
            fetch('<?= base_url('security/transportation/backup-and-download') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(requestBody)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' && data.success) {
                    // Step 2: Trigger Excel download
                    const downloadUrl = data.download_url;
                    
                    // Create hidden iframe for download
                    const iframe = document.createElement('iframe');
                    iframe.style.display = 'none';
                    iframe.src = downloadUrl;
                    document.body.appendChild(iframe);
                    
                    // Remove iframe after download starts
                    setTimeout(() => {
                        document.body.removeChild(iframe);
                    }, 2000);
                    
                    // Close modal
                    var modal = bootstrap.Modal.getInstance(document.getElementById('modalBackupTransport'));
                    modal.hide();
                    
                    // Show success message
                    Swal.fire({
                        title: 'Berhasil!',
                        html: `<p>${data.message}</p>` +
                              `<p class="text-muted small">Total data yang di-backup: ${data.total_backed_up || 0} record</p>` +
                              `<p class="text-success small"><i class="fas fa-download"></i> File Excel sedang diunduh...</p>`,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        timer: 3000
                    }).then(() => {
                        // Reload page to show updated data with "Backed-up" badges
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message || 'Gagal melakukan backup', 'error');
                }
            })
            .catch(error => {
                console.error('Backup error:', error);
                Swal.fire('Error', 'Terjadi kesalahan saat backup: ' + error.message, 'error');
            });
        }

        // Update preview when filters change
        $('#backup_kategori').on('change', loadBackupPreview);
        $('#backup_tanggal_harian').on('change', loadBackupPreview);
        $('#backup_tanggal_mulai').on('change', loadBackupPreview);
        $('#backup_tanggal_selesai').on('change', loadBackupPreview);
        $('#backup_bulan').on('change', loadBackupPreview);
        $('#backup_tahun').on('change', loadBackupPreview);

        // Back-up Logs to Monthly Report (OLD FUNCTION - Keep for compatibility)
        function backupLogs() {
            Swal.fire({
                title: 'Konfirmasi Back-up',
                html: '<p>Apakah Anda yakin ingin melakukan back-up?</p>' +
                      '<p class="text-muted small">Data harian akan direkap ke laporan bulanan dan ditandai sebagai "Sudah di-backup".</p>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-sync"></i> Ya, Back-up Sekarang!',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    // Get CSRF token
                    const csrfToken = '<?= csrf_hash() ?>';
                    const csrfName = '<?= csrf_token() ?>';
                    
                    return fetch('<?= base_url('security/transportation/backup-logs') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            [csrfName]: csrfToken
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'error' || !data.success) {
                            throw new Error(data.message || 'Gagal melakukan back-up');
                        }
                        return data;
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`Request failed: ${error.message}`);
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Berhasil!',
                        html: `<p>${result.value.message}</p>` +
                              `<p class="text-muted small">Total data yang di-backup: ${result.value.total_backed_up || 0} record</p>`,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Reload page to show updated data
                        window.location.reload();
                    });
                }
            });
        }
    </script>
</body>
</html>
