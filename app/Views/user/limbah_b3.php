<?php
/**
 * User Limbah B3 Management - UI GreenMetric POLBAN
 * Manajemen Limbah B3 untuk user (identik dengan waste.php)
 */

// Helper functions
if (!function_exists('formatNumber')) {
    function formatNumber($number) {
        return number_format($number, 0, ',', '.');
    }
}

if (!function_exists('getStatusBadge')) {
    function getStatusBadge($status) {
        $badges = [
            'draft' => ['bg-secondary', '<i class="fas fa-file-alt"></i> Draft'],
            'dikirim_ke_tps' => ['bg-warning', '<i class="fas fa-paper-plane"></i> Menunggu Review'],
            'disetujui_tps' => ['bg-info', '<i class="fas fa-check-circle"></i> Disetujui TPS'],
            'ditolak_tps' => ['bg-danger', '<i class="fas fa-times-circle"></i> Ditolak TPS'],
            'disetujui_admin' => ['bg-success', '<i class="fas fa-thumbs-up"></i> Disetujui Admin'],
        ];
        
        if (isset($badges[$status])) {
            return '<span class="badge ' . $badges[$status][0] . '">' . $badges[$status][1] . '</span>';
        }
        
        return '<span class="badge bg-secondary">Unknown</span>';
    }
}

// Safety checks
$limbah_list = $limbah_list ?? [];
$master_list = $master_list ?? [];
$unit = $unit ?? ['nama_unit' => 'Unit'];
$stats = $stats ?? ['count_by_status' => [], 'timbulan_by_status' => []];
$user = $user ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Limbah B3 User' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <link href="<?= base_url('/css/mobile-responsive.css') ?>" rel="stylesheet">
    <link href="<?= base_url('/css/toast-notification.css') ?>" rel="stylesheet">
    <link href="<?= base_url('/css/loading-state.css') ?>" rel="stylesheet">
    <link href="<?= base_url('/css/confirmation-dialog.css') ?>" rel="stylesheet">
    <link href="<?= base_url('/css/tooltip-helper.css') ?>" rel="stylesheet">
</head>
<body>
<?= $this->include('partials/sidebar') ?>

<div class="main-content">
    <div class="page-header">
        <h1><i class="fas fa-skull-crossbones"></i> Limbah B3</h1>
        <p>Kelola data Limbah B3 untuk <?= esc($unit['nama_unit'] ?? 'Unit') ?></p>
    </div>

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

    <!-- Statistics Cards (6 Cards) -->
    <?php if (!empty($stats['count_by_status'])): 
        $countStats = $stats['count_by_status'];
    ?>
    <div class="stats-grid mb-4">
        <!-- Total Data -->
        <div class="stat-card primary">
            <div class="stat-icon">
                <i class="fas fa-list"></i>
            </div>
            <div class="stat-content">
                <h3><?= $countStats['total'] ?? 0 ?></h3>
                <p>Total Data</p>
            </div>
        </div>

        <!-- Menunggu Review (Dikirim ke TPS) -->
        <div class="stat-card warning">
            <div class="stat-icon">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="stat-content">
                <h3><?= $countStats['dikirim_ke_tps'] ?? 0 ?></h3>
                <p>Menunggu Review</p>
            </div>
        </div>

        <!-- Disetujui TPS -->
        <div class="stat-card info">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3><?= $countStats['disetujui_tps'] ?? 0 ?></h3>
                <p>Disetujui TPS</p>
            </div>
        </div>

        <!-- Ditolak TPS -->
        <div class="stat-card danger">
            <div class="stat-icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-content">
                <h3><?= $countStats['ditolak_tps'] ?? 0 ?></h3>
                <p>Ditolak TPS</p>
            </div>
        </div>

        <!-- Disetujui Admin -->
        <div class="stat-card success">
            <div class="stat-icon">
                <i class="fas fa-thumbs-up"></i>
            </div>
            <div class="stat-content">
                <h3><?= $countStats['disetujui_admin'] ?? 0 ?></h3>
                <p>Disetujui Admin</p>
            </div>
        </div>

        <!-- Draft -->
        <div class="stat-card secondary">
            <div class="stat-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-content">
                <h3><?= $stats['draft_count'] ?? ($countStats['draft'] ?? 0) ?></h3>
                <p>Draft</p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Action Buttons -->
    <div class="action-buttons mb-4">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLimbahB3Modal">
            <i class="fas fa-plus"></i> Tambah Limbah B3
        </button>
        <a href="<?= base_url('/user/limbah-b3/export-excel') ?>" class="btn btn-success d-none">
            <i class="fas fa-file-excel"></i> Export Excel
        </a>
        <a href="<?= base_url('/user/limbah-b3/export-pdf') ?>" class="btn btn-danger d-none" target="_blank">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
    </div>

    <!-- Limbah B3 Data Table with Tabs -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-table"></i> Data Limbah B3</h3>
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tab-all" data-filter="all">
                        <i class="fas fa-list"></i> Semua Data
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-draft" data-filter="draf">
                        <i class="fas fa-file-alt"></i> Draft
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-pending" data-filter="dikirim_ke_tps">
                        <i class="fas fa-hourglass-half"></i> Menunggu Review
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-approved" data-filter="disetujui_tps">
                        <i class="fas fa-check-circle"></i> Disetujui
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-rejected" data-filter="ditolak_tps">
                        <i class="fas fa-times-circle"></i> Ditolak
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <!-- Tab All Data -->
                <div class="tab-pane fade show active" id="tab-all">
                    <?php if (!empty($limbah_list)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover" id="allTable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal Input</th>
                                        <th>Nama Limbah</th>
                                        <th>Kode</th>
                                        <th>Lokasi</th>
                                        <th>Timbulan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($limbah_list as $row): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($row['tanggal_input'] ?? now())) ?></td>
                                            <td>
                                                <strong><?= esc($row['nama_limbah'] ?? '-') ?></strong>
                                                <br><small class="text-muted"><?= esc($row['kategori_bahaya'] ?? '') ?></small>
                                            </td>
                                            <td><code><?= esc($row['kode_limbah'] ?? '-') ?></code></td>
                                            <td><?= esc($row['lokasi'] ?? '-') ?></td>
                                            <td><?= number_format($row['timbulan'] ?? 0, 2, ',', '.') ?> <?= esc($row['satuan'] ?? '') ?></td>
                                            <td><?= getStatusBadge($row['status'] ?? 'draft') ?></td>
                                            <td>
                                                <?php if (($row['status'] ?? 'draft') === 'draft'): ?>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-outline-primary" onclick="editLimbahB3(<?= $row['id'] ?>)" title="Edit">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger" onclick="deleteLimbahB3(<?= $row['id'] ?>)" title="Hapus">
                                                            <i class="fas fa-trash"></i> Hapus
                                                        </button>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="badge bg-warning"><i class="fas fa-lock"></i> Tidak dapat diedit</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p>Belum ada data Limbah B3</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Tab Draft -->
                <div class="tab-pane fade" id="tab-draft">
                    <?php 
                        $draftData = array_filter($limbah_list, fn($item) => ($item['status'] ?? 'draft') === 'draft');
                    ?>
                    <?php if (!empty($draftData)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal Input</th>
                                        <th>Nama Limbah</th>
                                        <th>Kode</th>
                                        <th>Lokasi</th>
                                        <th>Timbulan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($draftData as $row): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($row['tanggal_input'] ?? now())) ?></td>
                                            <td><strong><?= esc($row['nama_limbah'] ?? '-') ?></strong></td>
                                            <td><code><?= esc($row['kode_limbah'] ?? '-') ?></code></td>
                                            <td><?= esc($row['lokasi'] ?? '-') ?></td>
                                            <td><?= number_format($row['timbulan'] ?? 0, 2, ',', '.') ?> <?= esc($row['satuan'] ?? '') ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-primary" onclick="editLimbahB3(<?= $row['id'] ?>)" title="Edit">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger" onclick="deleteLimbahB3(<?= $row['id'] ?>)" title="Hapus">
                                                        <i class="fas fa-trash"></i> Hapus
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
                            <i class="fas fa-inbox"></i>
                            <p>Tidak ada data draft</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Tab Pending -->
                <div class="tab-pane fade" id="tab-pending">
                    <?php 
                        $pendingData = array_filter($limbah_list, fn($item) => ($item['status'] ?? 'draf') === 'dikirim_ke_tps');
                    ?>
                    <?php if (!empty($pendingData)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal Input</th>
                                        <th>Nama Limbah</th>
                                        <th>Kode</th>
                                        <th>Lokasi</th>
                                        <th>Timbulan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($pendingData as $row): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($row['tanggal_input'] ?? now())) ?></td>
                                            <td><strong><?= esc($row['nama_limbah'] ?? '-') ?></strong></td>
                                            <td><code><?= esc($row['kode_limbah'] ?? '-') ?></code></td>
                                            <td><?= esc($row['lokasi'] ?? '-') ?></td>
                                            <td><?= number_format($row['timbulan'] ?? 0, 2, ',', '.') ?> <?= esc($row['satuan'] ?? '') ?></td>
                                            <td><?= getStatusBadge('dikirim_ke_tps') ?></td>
                                            <td>
                                                <span class="badge bg-warning"><i class="fas fa-lock"></i> Tidak dapat diedit</span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p>Tidak ada data menunggu review</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Tab Approved -->
                <div class="tab-pane fade" id="tab-approved">
                    <?php 
                        $approvedData = array_filter($limbah_list, fn($item) => in_array(($item['status'] ?? 'draft'), ['disetujui_tps', 'disetujui_admin']));
                    ?>
                    <?php if (!empty($approvedData)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal Input</th>
                                        <th>Nama Limbah</th>
                                        <th>Kode</th>
                                        <th>Lokasi</th>
                                        <th>Timbulan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($approvedData as $row): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($row['tanggal_input'] ?? now())) ?></td>
                                            <td><strong><?= esc($row['nama_limbah'] ?? '-') ?></strong></td>
                                            <td><code><?= esc($row['kode_limbah'] ?? '-') ?></code></td>
                                            <td><?= esc($row['lokasi'] ?? '-') ?></td>
                                            <td><?= number_format($row['timbulan'] ?? 0, 2, ',', '.') ?> <?= esc($row['satuan'] ?? '') ?></td>
                                            <td><?= getStatusBadge($row['status'] ?? 'draft') ?></td>
                                            <td>
                                                <span class="badge bg-success"><i class="fas fa-check"></i> Selesai</span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p>Tidak ada data disetujui</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Tab Rejected -->
                <div class="tab-pane fade" id="tab-rejected">
                    <?php 
                        $rejectedData = array_filter($limbah_list, fn($item) => ($item['status'] ?? 'draft') === 'ditolak_tps');
                    ?>
                    <?php if (!empty($rejectedData)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal Input</th>
                                        <th>Nama Limbah</th>
                                        <th>Kode</th>
                                        <th>Lokasi</th>
                                        <th>Timbulan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($rejectedData as $row): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($row['tanggal_input'] ?? now())) ?></td>
                                            <td><strong><?= esc($row['nama_limbah'] ?? '-') ?></strong></td>
                                            <td><code><?= esc($row['kode_limbah'] ?? '-') ?></code></td>
                                            <td><?= esc($row['lokasi'] ?? '-') ?></td>
                                            <td><?= number_format($row['timbulan'] ?? 0, 2, ',', '.') ?> <?= esc($row['satuan'] ?? '') ?></td>
                                            <td><?= getStatusBadge('ditolak_tps') ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-primary" onclick="editLimbahB3(<?= $row['id'] ?>)" title="Edit & Kirim Ulang">
                                                        <i class="fas fa-edit"></i> Edit
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
                            <i class="fas fa-inbox"></i>
                            <p>Tidak ada data ditolak</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Add/Edit Limbah B3 Modal -->
    <div class="modal fade" id="addLimbahB3Modal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Limbah B3</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addLimbahB3Form">
                    <?= csrf_field() ?>
                    <input type="hidden" id="limbah_id" name="limbah_id" value="">
                    <input type="hidden" id="current_status" name="current_status" value="">
                    <div class="modal-body">
                        <! Nama Limbah B3>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-flask"></i> Nama Limbah B3 *
                            </label>
                            <select class="form-select select2-dropdown" id="master_b3_id" name="master_b3_id" required>
                                <option value=""> Pilih Nama Limbah B3 </option>
                                <?php if (!empty($master_list)): ?>
                                    <?php foreach ($master_list as $m): ?>
                                        <option value="<?= (int) $m['id'] ?>" 
                                                data-kode="<?= esc($m['kode_limbah'] ?? '') ?>"
                                                data-kategori="<?= esc($m['kategori_bahaya'] ?? '') ?>">
                                            <?= esc($m['nama_limbah']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <small class="text-muted">Pilih dari master data Limbah B3 yang tersedia</small>
                        </div>

                        <!-- Kode Limbah & Kategori Bahaya (Auto-fill dari master) -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-barcode"></i> Kode Limbah</label>
                                    <input type="text" class="form-control" id="kode_limbah_display" readonly placeholder="Otomatis terisi">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-exclamation-triangle"></i> Kategori Bahaya</label>
                                    <input type="text" class="form-control" id="kategori_bahaya_display" readonly placeholder="Otomatis terisi">
                                </div>
                            </div>
                        </div>

                        <!-- Lokasi (Dropdown) -->
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-map-marker-alt"></i> Lokasi *
                            </label>
                            <select class="form-select" id="lokasi" name="lokasi" required>
                                <option value=""> Pilih Lokasi </option>
                                <option value="Lokakarya">Lokakarya</option>
                                <option value="Lab Kimia">Lab Kimia</option>
                                <option value="Lab Biologi">Lab Biologi</option>
                                <option value="Lab Fisika">Lab Fisika</option>
                                <option value="Workshop">Workshop</option>
                                <option value="Gudang">Gudang</option>
                                <option value="Area Produksi">Area Produksi</option>
                                <option value="Area Pengolahan">Area Pengolahan</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <!-- Timbulan & Satuan -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-weight"></i> Timbulan (Berat/Jumlah) *
                                    </label>
                                    <input type="number" step="0.001" min="0.001" class="form-control" id="timbulan" name="timbulan" placeholder="Contoh: 10.5" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Satuan *</label>
                                    <select class="form-select" id="satuan" name="satuan" required>
                                        <option value=""> Pilih Satuan </option>
                                        <option value="kg">Kilogram (kg)</option>
                                        <option value="ton">Ton</option>
                                        <option value="liter">Liter</option>
                                        <option value="m3">Kubik Meter (m³)</option>
                                        <option value="pcs">Pieces (pcs)</option>
                                        <option value="drum">Drum</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Bentuk Fisik & Kemasan -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-cube"></i> Bentuk Fisik
                                    </label>
                                    <select class="form-select" id="bentuk_fisik" name="bentuk_fisik">
                                        <option value=""> Pilih Bentuk Fisik </option>
                                        <option value="Cair">Cair</option>
                                        <option value="Padat">Padat</option>
                                        <option value="Sludge">Sludge</option>
                                        <option value="Gas">Gas</option>
                                        <option value="Pasta">Pasta</option>
                                        <option value="Bubuk">Bubuk</option>
                                        <option value="Padat/Cair">Padat/Cair</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-box"></i> Kemasan/Kontainer
                                    </label>
                                    <select class="form-select" id="kemasan" name="kemasan">
                                        <option value=""> Pilih Kemasan </option>
                                        <option value="Drum 200L">Drum 200L</option>
                                        <option value="Drum 100L">Drum 100L</option>
                                        <option value="Jerrycan 20L">Jerrycan 20L</option>
                                        <option value="Jerrycan 5L">Jerrycan 5L</option>
                                        <option value="Karung">Karung</option>
                                        <option value="Barel">Barel</option>
                                        <option value="Botol">Botol</option>
                                        <option value="Tank">Tank</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div class="mb-3">
                            <label class="form-label">Keterangan (Opsional)</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Masukkan keterangan tambahan jika diperlukan"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Batal
                        </button>
                        <button type="submit" name="action" value="simpan_draf" class="btn btn-outline-primary" onclick="document.getElementById('addLimbahB3Form').setAttribute('data-action', 'simpan_draf')">
                            <i class="fas fa-save"></i> Simpan sebagai Draft
                        </button>
                        <button type="submit" name="action" value="kirim_ke_tps" class="btn btn-primary" onclick="document.getElementById('addLimbahB3Form').setAttribute('data-action', 'kirim_ke_tps')">
                            <i class="fas fa-paper-plane"></i> Kirim ke TPS
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="<?= base_url('/js/toast-notification.js') ?>"></script>
<script src="<?= base_url('/js/loading-state.js') ?>"></script>
<script src="<?= base_url('/js/confirmation-dialog.js') ?>"></script>
<script src="<?= base_url('/js/tooltip-helper.js') ?>"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2 dengan dropdown parent
        $('#master_b3_id').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Pilih Nama Limbah B3 --',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#addLimbahB3Modal')
        });

        // Auto-fill Kode Limbah & Kategori Bahaya dari data attributes master
        // Triggered when user changes the master_b3_id dropdown
        $('#master_b3_id').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const kode = selectedOption.data('kode') || '';
            const kategori = selectedOption.data('kategori') || '';
            
            // Set readonly fields dengan value dari master data
            $('#kode_limbah_display').val(kode);
            $('#kategori_bahaya_display').val(kategori);
            
            console.log('Master selected - Kode:', kode, 'Kategori:', kategori);
        });

        // Reset form when modal is closed
        $('#addLimbahB3Modal').on('hidden.bs.modal', function() {
            resetForm();
        });
    });

    // Reset form ke keadaan awal
    function resetForm() {
        document.getElementById('addLimbahB3Form').reset();
        document.getElementById('limbah_id').value = '';
        document.getElementById('current_status').value = '';
        document.getElementById('modalTitle').textContent = 'Tambah Limbah B3';
        document.getElementById('addLimbahB3Form').removeAttribute('data-action');
        
        // Clear kode and kategori
        $('#kode_limbah_display').val('');
        $('#kategori_bahaya_display').val('');
        
        // Reset Select2
        $('#master_b3_id').val(null).trigger('change');
    }

    // Submit form dengan FETCH API
    let isSubmitting = false;
    document.getElementById('addLimbahB3Form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (isSubmitting) {
            console.log('Form already submitting');
            return;
        }

        // Validasi client-side
        if (!document.getElementById('master_b3_id').value) {
            toast.error('Nama Limbah B3 harus dipilih');
            return;
        }

        if (!document.getElementById('timbulan').value || parseFloat(document.getElementById('timbulan').value) <= 0) {
            toast.error('Timbulan harus berupa angka > 0');
            return;
        }

        if (!document.getElementById('satuan').value) {
            toast.error('Satuan harus dipilih');
            return;
        }

        if (!document.getElementById('lokasi').value) {
            toast.error('Lokasi harus dipilih');
            return;
        }

        // Ambil action dari tombol yang ditekan
        // Menggunakan e.submitter.value untuk mendapat nilai dari tombol Submit
        const limbahId = document.getElementById('limbah_id').value;
        const action = e.submitter.value; // 'simpan_draf' atau 'kirim_ke_tps'
        
        const formData = new FormData(this);
        formData.append('action', action); // <- Tambahkan action ke formData

        isSubmitting = true;
        loading.buttonLoading(e.submitter, true);

        try {
            // Tentukan URL: edit jika ada limbah_id, save jika baru
            const url = limbahId 
                ? '<?= base_url('user/limbah-b3/edit/') ?>' + limbahId
                : '<?= base_url('user/limbah-b3/save') ?>';

            console.log('📤 Submitting to:', url);
            console.log('📋 Action:', action);
            console.log('📦 Form Data:', {
                master_b3_id: document.getElementById('master_b3_id').value,
                lokasi: document.getElementById('lokasi').value,
                timbulan: document.getElementById('timbulan').value,
                satuan: document.getElementById('satuan').value,
                action: action
            });

            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });

            console.log('📊 Response Status:', response.status);
            console.log('📊 Response Headers:', response.headers.get('content-type'));

            // Cek content-type response
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                // Jika bukan JSON, tampilkan raw response untuk debugging
                const rawText = await response.text();
                console.error('❌ ERROR: Server returned non-JSON response!');
                console.error('🔍 Raw Response:', rawText.substring(0, 500));
                toast.error('Server error: ' + rawText.substring(0, 200));
                loading.buttonLoading(e.submitter, false);
                isSubmitting = false;
                return;
            }

            const data = await response.json();
            console.log('✅ Parsed JSON Response:', data);

            if (data.success) {
                toast.success(data.message || 'Data Limbah B3 berhasil disimpan');
                console.log('🎉 Success! Reloading page...');
                // Reload setelah 1 detik
                setTimeout(() => location.reload(), 1000);
            } else {
                toast.error(data.message || 'Gagal menyimpan data Limbah B3');
                console.error('❌ Server returned error:', data);
                loading.buttonLoading(e.submitter, false);
                isSubmitting = false;
            }
        } catch (error) {
            console.error('💥 FETCH ERROR:', error);
            console.error('Error message:', error.message);
            console.error('Error stack:', error.stack);
            toast.error('Terjadi kesalahan: ' + error.message);
            loading.buttonLoading(e.submitter, false);
            isSubmitting = false;
        }
    });

    // Edit Limbah B3 - fetch data dan populate form
    async function editLimbahB3(id) {
        loading.show('Memuat data Limbah B3...');
        
        try {
            const response = await fetch('<?= base_url('/user/limbah-b3/get/') ?>' + id);
            const data = await response.json();
            loading.hide();

            if (!data.success) {
                toast.error(data.message || 'Gagal mengambil data');
                return;
            }

            const row = data.data;
            console.log('Edit data loaded:', row);

            // Populate hidden fields
            document.getElementById('limbah_id').value = row.id;
            document.getElementById('current_status').value = row.status;
            document.getElementById('modalTitle').textContent = 'Edit Limbah B3 - Status: ' + row.status;
            
            // Populate select dropdowns
            $('#master_b3_id').val(row.master_b3_id).trigger('change');
            document.getElementById('lokasi').value = row.lokasi || '';
            document.getElementById('timbulan').value = row.timbulan || '';
            document.getElementById('satuan').value = row.satuan || '';
            document.getElementById('bentuk_fisik').value = row.bentuk_fisik || '';
            document.getElementById('kemasan').value = row.kemasan || '';
            document.getElementById('keterangan').value = row.keterangan || '';

            // Populate auto-fill fields (kode & kategori)
            // These should be populated by Select2 change event, but we can force it
            setTimeout(() => {
                const selectedOption = $('#master_b3_id').find('option:selected');
                $('#kode_limbah_display').val(selectedOption.data('kode') || row.kode_limbah || '');
                $('#kategori_bahaya_display').val(selectedOption.data('kategori') || row.kategori_bahaya || '');
            }, 100);

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('addLimbahB3Modal'));
            modal.show();
        } catch (error) {
            console.error('Error:', error);
            loading.hide();
            toast.error('Terjadi kesalahan: ' + error.message);
        }
    }

    // Delete Limbah B3 dengan konfirmasi
    async function deleteLimbahB3(id) {
        const confirmed = await window.confirm.delete('data Limbah B3 ini');
        if (!confirmed) return;

        loading.show('Menghapus data...');

        try {
            const formData = new FormData();
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

            const response = await fetch('<?= base_url('/user/limbah-b3/delete/') ?>' + id, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            loading.hide();

            if (data.success) {
                toast.success(data.message || 'Data berhasil dihapus');
                setTimeout(() => location.reload(), 800);
            } else {
                toast.error(data.message || 'Gagal menghapus data');
            }
        } catch (error) {
            console.error('Error:', error);
            loading.hide();
            toast.error('Terjadi kesalahan: ' + error.message);
        }
    }
</script>

<style>
    /* Main Content */
    .main-content {
        margin-left: 280px;
        padding: 30px;
        min-height: 100vh;
        max-width: calc(100vw - 280px);
        overflow-x: hidden;
    }

    /* Page Header */
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

    /* Stats Grid */
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

    .stat-card.primary .stat-icon { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .stat-card.success .stat-icon { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .stat-card.warning .stat-icon { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .stat-card.info .stat-icon { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
    .stat-card.danger .stat-icon { background: linear-gradient(135deg, #eb3b5a 0%, #fc5c65 100%); }
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

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .action-buttons .btn {
        transition: all 0.3s ease;
    }

    .action-buttons .btn:hover {
        transform: translateY(-2px);
    }

    /* Cards */
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
        margin: 0 0 15px 0;
        font-size: 18px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-header .nav-tabs {
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }

    .card-header .nav-tabs .nav-link {
        color: rgba(255, 255, 255, 0.7);
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .card-header .nav-tabs .nav-link:hover {
        color: white;
    }

    .card-header .nav-tabs .nav-link.active {
        color: white;
        border-bottom: 3px solid white;
    }

    .card-body {
        padding: 25px;
    }

    /* Tables */
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

    /* Empty State */
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
        margin: 0;
        font-size: 18px;
    }

    /* Badges */
    .badge {
        padding: 8px 12px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 12px;
    }

    /* Alerts */
    .alert {
        border-radius: 10px;
        border: none;
        padding: 15px 20px;
    }

    /* Buttons */
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

    .btn-outline-primary {
        color: #007bff;
        border-color: #007bff;
    }

    .btn-outline-primary:hover {
        background-color: #007bff;
        border-color: #007bff;
        color: white;
    }

    .btn-outline-danger {
        color: #dc3545;
        border-color: #dc3545;
    }

    .btn-outline-danger:hover {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
    }

    /* Modal */
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

    /* Form */
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
        font-size: 14px;
    }

    .form-control:focus, .form-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    /* Select2 */
    .select2-dropdown,
    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 8px !important;
    }

    /* Responsive */
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

        .page-header p {
            font-size: 14px;
        }

        .action-buttons {
            flex-direction: column;
            width: 100%;
        }

        .action-buttons .btn {
            width: 100%;
        }

        .card-header {
            padding: 15px;
        }

        .card-header h3 {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .card-header .nav-tabs .nav-link {
            font-size: 12px;
            padding: 0.5rem 0.75rem;
        }

        .card-body {
            padding: 15px;
        }

        .table-responsive {
            font-size: 12px;
            max-width: 100%;
            overflow-x: auto;
        }

        .table th, .table td {
            padding: 10px;
        }

        .btn-group {
            flex-direction: row;
            flex-wrap: nowrap;
        }

        .btn-group .btn {
            padding: 0.25rem 0.5rem;
            font-size: 11px;
        }
    }
</style>

</body>
</html>

