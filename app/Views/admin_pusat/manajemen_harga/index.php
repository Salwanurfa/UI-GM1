<?php
// PREVENT DOUBLE RENDER - Check if already rendered
$viewRenderId = 'harga_index_' . ($requestId ?? uniqid());
if (isset($GLOBALS['_rendered_views'][$viewRenderId])) {
    // Already rendered, skip
    return;
}
$GLOBALS['_rendered_views'][$viewRenderId] = true;

// Helper functions untuk icon
if (!function_exists('getJenisIcon')) {
    function getJenisIcon($jenis) {
        $icons = [
            'Plastik' => 'wine-bottle',
            'Kertas' => 'file-alt',
            'Logam' => 'cog',
            'Organik' => 'seedling',
            'Residu' => 'trash-alt',
            'Elektronik' => 'laptop',
            'Anorganik' => 'box',
            'Besi' => 'wrench'
        ];
        return $icons[$jenis] ?? 'recycle';
    }
}

if (!function_exists('getActionIcon')) {
    function getActionIcon($status) {
        $icons = [
            'pending' => 'clock',
            'approved' => 'check',
            'rejected' => 'times',
            'update' => 'edit'
        ];
        return $icons[$status] ?? 'history';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<!-- FORCE RELOAD: <?= date('Y-m-d H:i:s') ?> -->
<!-- REQUEST ID: <?= $requestId ?? 'NO-ID' ?> -->
<!-- RENDER CHECK: <?= $viewRenderId ?> -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-name" content="<?= csrf_token() ?>">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title><?= esc($title ?? 'Manajemen Sampah') ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Mobile Responsive CSS -->
    <link href="<?= base_url('/css/mobile-responsive.css') ?>" rel="stylesheet">
    
    <style>
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
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding: 20px 0;
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
        margin-bottom: 30px;
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

    .search-box .input-group {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .search-box .input-group-text {
        border: none;
        padding: 8px 12px;
    }

    .search-box .form-control {
        border: none;
        padding: 8px 12px;
        color: #2c3e50;
    }

    .search-box .form-control:focus {
        outline: none;
        box-shadow: none;
    }

    .search-box .form-control::placeholder {
        color: #adb5bd;
    }

    .card-body {
        padding: 25px;
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
        color: #007bff;
    }

    .table {
        margin-bottom: 0;
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

    .jenis-info {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .harga-display {
        font-size: 16px;
    }

    .action-buttons {
        display: flex;
        gap: 5px;
    }

    .btn {
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
    }

    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #dee2e6;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }

    .timeline-marker {
        position: absolute;
        left: -22px;
        top: 0;
        width: 30px;
        height: 30px;
        background: #007bff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
    }

    .timeline-content {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border-left: 3px solid #007bff;
    }

    .timeline-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .timeline-body {
        color: #6c757d;
        font-size: 14px;
    }

    /* Pagination Styling */
    .pagination {
        margin: 0;
    }

    .pagination .page-link {
        color: #007bff;
        border: 1px solid #dee2e6;
        padding: 8px 12px;
        margin: 0 2px;
        border-radius: 5px;
        transition: all 0.3s ease;
    }

    .pagination .page-link:hover {
        background: #007bff;
        color: white;
        border-color: #007bff;
    }

    .pagination .page-item.active .page-link {
        background: #007bff;
        border-color: #007bff;
        color: white;
        font-weight: 600;
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background: #f8f9fa;
        border-color: #dee2e6;
    }

    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
            padding: 20px;
            max-width: 100vw;
            overflow-x: hidden;
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
            justify-content: flex-start;
            flex-direction: column;
            gap: 10px;
        }

        .header-actions .btn {
            width: 100%;
        }

        .card-header .d-flex {
            flex-direction: column !important;
            gap: 15px;
            align-items: flex-start !important;
        }

        .search-box {
            width: 100% !important;
            max-width: 100% !important;
        }

        .btn-group {
            width: 100%;
            display: flex;
            flex-direction: column;
        }

        .btn-group .btn {
            flex: 1;
            font-size: 12px;
            padding: 6px 8px;
            width: 100%;
            border-radius: 8px !important;
            margin-bottom: 5px;
        }

        .table-responsive {
            max-width: 100%;
            overflow-x: auto;
        }

        .action-buttons {
            display: flex;
            flex-direction: row;
            gap: 5px;
            flex-wrap: nowrap;
        }

        .action-buttons .btn {
            padding: 4px 8px;
            font-size: 11px;
        }
    }
    </style>
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <div class="page-header">
            <div class="header-content">
                <h1><i class="fas fa-recycle"></i> Manajemen Jenis Sampah</h1>
                <p>Kelola jenis dan harga sampah secara terpusat untuk seluruh sistem</p>
            </div>
            
            <div class="header-actions">
                <button type="button" class="btn btn-primary" onclick="showAddModal()">
                    <i class="fas fa-plus"></i> Tambah Jenis Sampah
                </button>
                <a href="<?= base_url('/admin-pusat/manajemen-harga/logs') ?>" class="btn btn-outline-info">
                    <i class="fas fa-history"></i> Log Perubahan
                </a>
            </div>
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

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-list"></i>
                </div>
                <div class="stat-content">
                    <h3><?= esc($statistics['total'] ?? 0) ?></h3>
                    <p>Total Jenis Sampah</p>
                </div>
            </div>
            
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3><?= esc($statistics['aktif'] ?? 0) ?></h3>
                    <p>Harga Aktif</p>
                </div>
            </div>
            
            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-content">
                    <h3><?= esc($statistics['bisa_dijual'] ?? 0) ?></h3>
                    <p>Bisa Dijual</p>
                </div>
            </div>
            
            <div class="stat-card info">
                <div class="stat-icon">
                    <i class="fas fa-history"></i>
                </div>
                <div class="stat-content">
                    <h3><?= esc($statistics['perubahan_hari_ini'] ?? 0) ?></h3>
                    <p>Perubahan Hari Ini</p>
                </div>
            </div>
        </div>

        <!-- Harga Sampah Table -->
        <div class="card">
            <div class="card-header">
                <div class="w-100">
                    <!-- Title and Search Bar Row -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-table"></i>
                            <h3 class="mb-0">Daftar Harga Sampah</h3>
                        </div>
                        
                        <!-- Search Bar -->
                        <div class="search-box" style="width: 300px;">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" 
                                       class="form-control border-start-0 ps-0" 
                                       id="searchInput" 
                                       placeholder="Cari kategori, jenis, atau harga..."
                                       style="box-shadow: none;">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filter Status Row -->
                    <div class="d-flex justify-content-center">
                        <div class="btn-group" role="group">
                            <a href="<?= base_url('/admin-pusat/manajemen-harga') ?>" 
                               class="btn btn-sm <?= !isset($_GET['status']) ? 'btn-primary' : 'btn-outline-primary' ?>">
                                <i class="fas fa-list"></i> Semua (<?= $statistics['total'] ?? 0 ?>)
                            </a>
                            <a href="<?= base_url('/admin-pusat/manajemen-harga?status=aktif') ?>" 
                               class="btn btn-sm <?= (isset($_GET['status']) && $_GET['status'] == 'aktif') ? 'btn-success' : 'btn-outline-success' ?>">
                                <i class="fas fa-check-circle"></i> Aktif (<?= $statistics['aktif'] ?? 0 ?>)
                            </a>
                            <a href="<?= base_url('/admin-pusat/manajemen-harga?status=nonaktif') ?>" 
                               class="btn btn-sm <?= (isset($_GET['status']) && $_GET['status'] == 'nonaktif') ? 'btn-secondary' : 'btn-outline-secondary' ?>">
                                <i class="fas fa-times-circle"></i> Nonaktif (<?= ($statistics['total'] ?? 0) - ($statistics['aktif'] ?? 0) ?>)
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($hargaSampah)): ?>
                <div class="empty-state">
                    <i class="fas fa-recycle"></i>
                    <p>Belum ada data jenis sampah</p>
                    <p class="text-muted">Klik tombol "Tambah Jenis Sampah" untuk menambahkan data baru</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kategori Sampah</th>
                                <th>Jenis Sampah</th>
                                <th>Harga per Satuan</th>
                                <th>Satuan</th>
                                <th>Status Jual</th>
                                <th>Status Aktif</th>
                                <th>Terakhir Update</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($hargaSampah as $item): ?>
                            <tr class="<?= !$item['status_aktif'] ? 'table-secondary opacity-50' : '' ?>">
                                <td>
                                    <div class="jenis-info">
                                        <i class="fas fa-<?= getJenisIcon($item['jenis_sampah']) ?> text-primary"></i>
                                        <strong><?= htmlspecialchars($item['jenis_sampah']) ?></strong>
                                        <?php if (!$item['status_aktif']): ?>
                                            <span class="badge bg-secondary ms-2">Nonaktif</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($item['nama_jenis']) ?></td>
                                <td>
                                    <div class="harga-display">
                                        <strong class="text-success">
                                            Rp <?= number_format($item['harga_per_satuan'], 0, ',', '.') ?>
                                        </strong>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($item['satuan']) ?></span>
                                </td>
                                <td>
                                    <?php if ($item['dapat_dijual']): ?>
                                        <span class="badge bg-success">
                                            <i class="fas fa-money-bill-wave"></i> Bisa Dijual
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-times"></i> Tidak Bisa Dijual
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <!-- Switch Read-Only (Hanya Tampilan Status) -->
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               <?= $item['status_aktif'] ? 'checked' : '' ?>
                                               disabled
                                               style="cursor: not-allowed;">
                                        <label class="form-check-label text-muted">
                                            <?= $item['status_aktif'] ? 'Aktif' : 'Nonaktif' ?>
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= date('d/m/Y H:i', strtotime($item['updated_at'])) ?>
                                    </small>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button type="button" class="btn btn-sm btn-warning" 
                                                onclick="editHarga(<?= $item['id'] ?>)" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="deleteHarga(<?= $item['id'] ?>, '<?= esc($item['jenis_sampah']) ?>')" 
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($pager): ?>
                <div class="mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted">
                            <small>
                                Menampilkan 
                                <strong><?= count($hargaSampah) ?></strong> dari 
                                <strong><?= $statistics['total'] ?? 0 ?></strong> total jenis sampah
                            </small>
                        </div>
                        <?php if ($pager->getPageCount() > 1): ?>
                        <div class="text-muted">
                            <small>
                                Halaman <strong><?= $pager->getCurrentPage() ?></strong> dari 
                                <strong><?= $pager->getPageCount() ?></strong>
                            </small>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($pager->getPageCount() > 1): ?>
                    <?php 
                    // Get current filter status
                    $statusFilter = $_GET['status'] ?? '';
                    $filterParam = $statusFilter ? '&status=' . $statusFilter : '';
                    ?>
                    <nav aria-label="Pagination">
                        <ul class="pagination justify-content-center">
                            <!-- Previous Button -->
                            <?php if ($pager->getCurrentPage() > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= base_url('/admin-pusat/manajemen-harga?page=' . ($pager->getCurrentPage() - 1) . $filterParam) ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo; Previous</span>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled">
                                    <span class="page-link">&laquo; Previous</span>
                                </li>
                            <?php endif; ?>

                            <!-- Page Numbers - Manual Loop -->
                            <?php for ($i = 1; $i <= $pager->getPageCount(); $i++): ?>
                                <?php if ($i == $pager->getCurrentPage()): ?>
                                    <li class="page-item active">
                                        <span class="page-link"><?= $i ?></span>
                                    </li>
                                <?php else: ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= base_url('/admin-pusat/manajemen-harga?page=' . $i . $filterParam) ?>"><?= $i ?></a>
                                    </li>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <!-- Next Button -->
                            <?php if ($pager->getCurrentPage() < $pager->getPageCount()): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= base_url('/admin-pusat/manajemen-harga?page=' . ($pager->getCurrentPage() + 1) . $filterParam) ?>" aria-label="Next">
                                        <span aria-hidden="true">Next &raquo;</span>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled">
                                    <span class="page-link">Next &raquo;</span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Changes -->
        <!-- DEBUG: recentChanges count = <?= count($recentChanges ?? []) ?> | Request: <?= $requestId ?? 'NO-ID' ?> -->
        <!-- DEBUG: recentChanges data = <?= !empty($recentChanges) ? 'HAS DATA' : 'EMPTY' ?> -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-history"></i>
                <h3>Perubahan Terbaru</h3>
                <span class="badge bg-info"><?= count($recentChanges ?? []) ?> perubahan</span>
                <a href="<?= base_url('/admin-pusat/manajemen-harga/logs') ?>" class="btn btn-sm btn-outline-info ms-auto">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body">
                <?php if (!empty($recentChanges)): ?>
                <div class="timeline">
                    <?php foreach ($recentChanges as $change): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker">
                            <i class="fas fa-<?= getActionIcon($change['status_perubahan'] ?? 'update') ?>"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-header">
                                <strong><?= htmlspecialchars($change['admin_nama'] ?? $change['user_full_name'] ?? 'Admin') ?></strong>
                                <small class="text-muted"><?= date('d/m/Y H:i', strtotime($change['created_at'])) ?></small>
                            </div>
                            <div class="timeline-body">
                                <?= htmlspecialchars($change['jenis_sampah'] ?? 'N/A') ?>: 
                                Rp <?= number_format($change['harga_lama'] ?? 0, 0, ',', '.') ?> → 
                                Rp <?= number_format($change['harga_baru'] ?? 0, 0, ',', '.') ?>
                                <?php if (!empty($change['alasan_perubahan'])): ?>
                                    <br><small class="text-muted"><?= htmlspecialchars($change['alasan_perubahan']) ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-history"></i>
                    <p>Belum ada perubahan harga</p>
                    <p class="text-muted">Perubahan harga akan muncul di sini</p>
                    <!-- DEBUG: recentChanges is empty or null -->
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add Jenis Sampah Modal -->
    <div class="modal fade" id="addJenisSampahModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Tambah Jenis Sampah Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="addJenisSampahForm">
                    <div class="modal-body">
                        <!-- Kategori Sampah (input text, bukan dropdown) -->
                        <div class="mb-3">
                            <label for="add_jenis_sampah" class="form-label">Kategori Sampah *</label>
                            <input type="text" class="form-control" id="add_jenis_sampah" name="jenis_sampah" 
                                   placeholder="Contoh: Plastik, Kertas, Logam, Organik, Residu" required>
                            <small class="text-muted">Ketik kategori sampah (Plastik, Kertas, Logam, Organik, Residu, dll)</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="add_nama_jenis" class="form-label">Jenis Sampah (Nama Lengkap) *</label>
                            <input type="text" class="form-control" id="add_nama_jenis" name="nama_jenis" 
                                   placeholder="Contoh: Botol Plastik PET Bersih, Kardus Bekas, Kaleng Aluminium" required>
                            <small class="text-muted">Nama lengkap atau deskripsi detail jenis sampah</small>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="add_dapat_dijual" 
                                   name="dapat_dijual" value="1" checked>
                            <label class="form-check-label" for="add_dapat_dijual">
                                <i class="fas fa-money-bill-wave"></i> Dapat Dijual
                            </label>
                            <small class="d-block text-muted">Centang jika sampah ini memiliki nilai ekonomis</small>
                        </div>
                        
                        <!-- Harga Section - akan disembunyikan jika tidak dapat dijual -->
                        <div class="row">
                            <div class="col-md-6" id="harga_section">
                                <div class="mb-3">
                                    <label for="add_harga_per_satuan" class="form-label">Harga per Satuan *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" id="add_harga_per_satuan" 
                                               name="harga_per_satuan" placeholder="0" min="0">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="add_satuan" class="form-label">Satuan *</label>
                                    <select class="form-select" id="add_satuan" name="satuan" required>
                                        <option value="">Pilih Satuan</option>
                                        <option value="kg">Kilogram (kg)</option>
                                        <option value="gram">Gram (g)</option>
                                        <option value="ton">Ton</option>
                                        <option value="liter">Liter</option>
                                        <option value="pcs">Pieces (pcs)</option>
                                        <option value="karung">Karung</option>
                                    </select>
                                    <small class="text-muted">Satuan untuk menghitung berat/jumlah sampah</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="add_deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="add_deskripsi" name="deskripsi" rows="3" 
                                      placeholder="Deskripsi singkat tentang jenis sampah ini"></textarea>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="add_status_aktif" 
                                   name="status_aktif" value="1" checked>
                            <label class="form-check-label" for="add_status_aktif">
                                <i class="fas fa-check-circle"></i> Status Aktif
                            </label>
                            <small class="d-block text-muted">Centang untuk mengaktifkan jenis sampah ini</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Jenis Sampah
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Harga Modal -->
    <div class="modal fade" id="editHargaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Jenis Sampah</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="editHargaForm">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="modal-body">
                        <!-- Kategori Sampah (input text, bukan dropdown) -->
                        <div class="mb-3">
                            <label for="edit_jenis_sampah" class="form-label">Kategori Sampah *</label>
                            <input type="text" class="form-control" id="edit_jenis_sampah" name="jenis_sampah" 
                                   placeholder="Contoh: Plastik, Kertas, Logam, Organik, Residu" required>
                            <small class="text-muted">Ketik kategori sampah (Plastik, Kertas, Logam, Organik, Residu, dll)</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_nama_jenis" class="form-label">Jenis Sampah (Nama Lengkap) *</label>
                            <input type="text" class="form-control" id="edit_nama_jenis" name="nama_jenis" 
                                   placeholder="Contoh: Botol Plastik PET Bersih, Kardus Bekas, Kaleng Aluminium" required>
                            <small class="text-muted">Nama lengkap atau deskripsi detail jenis sampah</small>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="edit_dapat_dijual" 
                                   name="dapat_dijual" value="1" checked>
                            <label class="form-check-label" for="edit_dapat_dijual">
                                <i class="fas fa-money-bill-wave"></i> Dapat Dijual
                            </label>
                            <small class="d-block text-muted">Centang jika sampah ini memiliki nilai ekonomis</small>
                        </div>
                        
                        <!-- Harga Section - akan disembunyikan jika tidak dapat dijual -->
                        <div class="row">
                            <div class="col-md-6" id="edit_harga_section">
                                <div class="mb-3">
                                    <label for="edit_harga_per_satuan" class="form-label">Harga per Satuan *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" id="edit_harga_per_satuan" 
                                               name="harga_per_satuan" placeholder="0" min="0">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_satuan" class="form-label">Satuan *</label>
                                    <select class="form-select" id="edit_satuan" name="satuan" required>
                                        <option value="">Pilih Satuan</option>
                                        <option value="kg">Kilogram (kg)</option>
                                        <option value="gram">Gram (g)</option>
                                        <option value="ton">Ton</option>
                                        <option value="liter">Liter</option>
                                        <option value="pcs">Pieces (pcs)</option>
                                        <option value="karung">Karung</option>
                                    </select>
                                    <small class="text-muted">Satuan untuk menghitung berat/jumlah sampah</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="edit_deskripsi" name="deskripsi" rows="3" 
                                      placeholder="Deskripsi singkat tentang jenis sampah ini"></textarea>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="edit_status_aktif" 
                                   name="status_aktif" value="1" checked>
                            <label class="form-check-label" for="edit_status_aktif">
                                <i class="fas fa-check-circle"></i> Status Aktif
                            </label>
                            <small class="d-block text-muted">Centang untuk mengaktifkan jenis sampah ini</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle harga section based on dapat_dijual checkbox
        function toggleHargaSection() {
            const dapatDijualCheckbox = document.getElementById('add_dapat_dijual');
            const hargaSection = document.getElementById('harga_section');
            const hargaInput = document.getElementById('add_harga_per_satuan');
            
            if (dapatDijualCheckbox.checked) {
                hargaSection.style.display = 'block';
                hargaInput.required = true;
            } else {
                hargaSection.style.display = 'none';
                hargaInput.required = false;
                hargaInput.value = '0';
            }
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Add event listener to checkbox (with null check)
            const dapatDijualCheckbox = document.getElementById('add_dapat_dijual');
            if (dapatDijualCheckbox) {
                dapatDijualCheckbox.addEventListener('change', toggleHargaSection);
                
                // Set initial state
                toggleHargaSection();
            }
        });
        
        // Show Add Modal
        function showAddModal() {
            // Reset form
            document.getElementById('addJenisSampahForm').reset();
            
            // Set default values
            document.getElementById('add_dapat_dijual').checked = true;
            document.getElementById('add_status_aktif').checked = true;
            
            // Show/hide harga section
            toggleHargaSection();
            
            const modal = new bootstrap.Modal(document.getElementById('addJenisSampahModal'));
            modal.show();
        }



        // Handle add form submit
        document.getElementById('addJenisSampahForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Validasi jenis_sampah
            const jenisSampah = formData.get('jenis_sampah');
            if (!jenisSampah) {
                showAlert('error', 'Silakan isi kategori sampah');
                return;
            }
            
            // Validasi nama_jenis
            const namaJenis = formData.get('nama_jenis');
            if (!namaJenis) {
                showAlert('error', 'Silakan isi jenis sampah (nama lengkap)');
                return;
            }
            
            // Validasi satuan (wajib untuk semua jenis sampah)
            const satuan = formData.get('satuan');
            if (!satuan) {
                showAlert('error', 'Silakan pilih satuan');
                return;
            }
            
            // Jika tidak dapat dijual, set harga = 0
            const dapatDijual = document.getElementById('add_dapat_dijual').checked;
            if (!dapatDijual) {
                formData.set('harga_per_satuan', '0');
            } else {
                // Validasi harga jika dapat dijual
                const harga = formData.get('harga_per_satuan');
                
                if (!harga || harga <= 0) {
                    showAlert('error', 'Silakan isi harga per satuan');
                    return;
                }
            }
            
            // Add CSRF token
            const csrfName = document.querySelector('meta[name="csrf-name"]')?.getAttribute('content');
            const csrfHash = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (csrfName && csrfHash) {
                formData.append(csrfName, csrfHash);
            }
            
            // Debug: log form data
            console.log('Submitting new jenis sampah:');
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
            
            fetch('<?= base_url('/admin-pusat/manajemen-harga/store') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('addJenisSampahModal')).hide();
                    showAlert('success', data.message || 'Jenis sampah berhasil ditambahkan');
                    // Redirect ke URL bersih tanpa parameter GET
                    setTimeout(() => {
                        window.location.href = '<?= base_url('/admin-pusat/manajemen-harga') ?>';
                    }, 1500);
                } else {
                    // Show detailed error message
                    let errorMsg = data.message || 'Gagal menambahkan jenis sampah';
                    if (data.errors && Object.keys(data.errors).length > 0) {
                        errorMsg += '<br><small>' + Object.values(data.errors).join('<br>') + '</small>';
                    }
                    showAlert('error', errorMsg);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Terjadi kesalahan saat menyimpan data: ' + error.message);
            });
        });

        // Delete Harga function
        function deleteHarga(id, jenisSampah) {
            if (confirm(`Apakah Anda yakin ingin menghapus jenis sampah "${jenisSampah}"?\n\nPerhatian: Data yang sudah digunakan di transaksi tidak akan terhapus.`)) {
                fetch(`<?= base_url('/admin-pusat/manajemen-harga/delete/') ?>${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                        setTimeout(() => { window.location.href = '<?= base_url('/admin-pusat/manajemen-harga') ?>'; }, 1500);
                    } else {
                        showAlert('error', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'Terjadi kesalahan saat menghapus data');
                });
            }
        }

        // Edit Harga function
        function editHarga(id) {
            fetch(`<?= base_url('/admin-pusat/manajemen-harga/get/') ?>${id}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const harga = data.data;
                    // Populate edit modal fields
                    document.getElementById('edit_id').value = harga.id;
                    document.getElementById('edit_jenis_sampah').value = harga.jenis_sampah;
                    document.getElementById('edit_nama_jenis').value = harga.nama_jenis || '';
                    document.getElementById('edit_harga_per_satuan').value = harga.harga_per_satuan;
                    document.getElementById('edit_satuan').value = harga.satuan;
                    document.getElementById('edit_dapat_dijual').checked = harga.dapat_dijual;
                    document.getElementById('edit_status_aktif').checked = harga.status_aktif;
                    document.getElementById('edit_deskripsi').value = harga.deskripsi || '';
                    
                    // Toggle harga section based on dapat_dijual
                    toggleEditHargaSection();
                    
                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById('editHargaModal'));
                    modal.show();
                } else {
                    showAlert('error', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Terjadi kesalahan saat mengambil data');
            });
        }
        
        // Toggle edit harga section based on dapat_dijual checkbox
        function toggleEditHargaSection() {
            const dapatDijualCheckbox = document.getElementById('edit_dapat_dijual');
            const hargaSection = document.getElementById('edit_harga_section');
            const hargaInput = document.getElementById('edit_harga_per_satuan');
            
            if (dapatDijualCheckbox && hargaSection && hargaInput) {
                if (dapatDijualCheckbox.checked) {
                    hargaSection.style.display = 'block';
                    hargaInput.required = true;
                } else {
                    hargaSection.style.display = 'none';
                    hargaInput.required = false;
                    hargaInput.value = '0';
                }
            }
        }
        
        // Add event listener to edit dapat_dijual checkbox
        document.addEventListener('DOMContentLoaded', function() {
            const editDapatDijualCheckbox = document.getElementById('edit_dapat_dijual');
            if (editDapatDijualCheckbox) {
                editDapatDijualCheckbox.addEventListener('change', toggleEditHargaSection);
            }
        });

        // Handle edit form submit
        const editFormElement = document.getElementById('editHargaForm');
        if (editFormElement) {
            editFormElement.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const id = document.getElementById('edit_id').value;
            const formData = new FormData(this);
            
            // Add CSRF token
            const csrfName = document.querySelector('meta[name="csrf-name"]')?.getAttribute('content');
            const csrfHash = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (csrfName && csrfHash) {
                formData.append(csrfName, csrfHash);
            }
            
            // Debug: log form data
            console.log('Submitting edit for ID:', id);
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
            
            fetch(`<?= base_url('/admin-pusat/manajemen-harga/update/') ?>${id}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('editHargaModal')).hide();
                    showAlert('success', data.message);
                    setTimeout(() => {
                        window.location.href = '<?= base_url('/admin-pusat/manajemen-harga') ?>';
                    }, 1500);
                } else {
                    showAlert('error', data.message || 'Gagal menyimpan perubahan');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Terjadi kesalahan saat menyimpan data');
            });
            });
        }



        // Show alert function
        function showAlert(type, message) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            
            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show">
                    <i class="fas ${iconClass}"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            // Insert at top of main content
            const mainContent = document.querySelector('.main-content');
            const pageHeader = document.querySelector('.page-header');
            pageHeader.insertAdjacentHTML('afterend', alertHtml);
            
            // Auto dismiss after 5 seconds
            setTimeout(() => {
                const alert = document.querySelector('.alert');
                if (alert) {
                    alert.remove();
                }
            }, 5000);
        }

        // Search functionality with pagination handling
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const tableBody = document.querySelector('.table tbody');
            const paginationContainer = document.querySelector('.pagination')?.closest('.mt-4');
            let originalTableHTML = tableBody.innerHTML; // Store original HTML of current page
            let isSearchActive = false;
            
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    
                    if (searchTerm === '') {
                        // Reset to original state - restore current page data
                        if (paginationContainer) {
                            paginationContainer.style.display = 'block';
                        }
                        
                        // Restore original table content (current page)
                        if (isSearchActive) {
                            tableBody.innerHTML = originalTableHTML;
                            isSearchActive = false;
                        }
                    } else {
                        // Save original HTML before search (only if not already searching)
                        if (!isSearchActive) {
                            originalTableHTML = tableBody.innerHTML;
                        }
                        
                        // Hide pagination when searching
                        if (paginationContainer) {
                            paginationContainer.style.display = 'none';
                        }
                        
                        isSearchActive = true;
                        
                        // Fetch all data from server for search
                        fetchAllDataAndSearch(searchTerm);
                    }
                });
            }
            
            function fetchAllDataAndSearch(searchTerm) {
                // Show loading indicator
                const loadingRow = document.createElement('tr');
                loadingRow.innerHTML = '<td colspan="8" class="text-center"><i class="fas fa-spinner fa-spin"></i> Mencari...</td>';
                
                // Get current filter status
                const urlParams = new URLSearchParams(window.location.search);
                const statusFilter = urlParams.get('status') || '';
                
                // Fetch all data without pagination
                fetch(`<?= base_url('/admin-pusat/manajemen-harga/search') ?>?q=${encodeURIComponent(searchTerm)}&status=${statusFilter}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            displaySearchResults(data.results, searchTerm);
                        } else {
                            // Fallback to client-side search
                            clientSideSearch(searchTerm);
                        }
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        // Fallback to client-side search
                        clientSideSearch(searchTerm);
                    });
            }
            
            function clientSideSearch(searchTerm) {
                // Client-side search on current page data
                tableRows.forEach(row => {
                    const kategori = row.cells[0]?.textContent.toLowerCase() || '';
                    const jenis = row.cells[1]?.textContent.toLowerCase() || '';
                    const harga = row.cells[2]?.textContent.toLowerCase() || '';
                    
                    const matches = kategori.includes(searchTerm) || 
                                  jenis.includes(searchTerm) || 
                                  harga.includes(searchTerm);
                    
                    row.style.display = matches ? '' : 'none';
                });
            }
            
            function displaySearchResults(results, searchTerm) {
                // Clear table
                tableBody.innerHTML = '';
                
                if (results.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Tidak ada hasil untuk "' + searchTerm + '"</td></tr>';
                    return;
                }
                
                // Display all search results
                results.forEach((item, index) => {
                    const row = document.createElement('tr');
                    if (!item.status_aktif) {
                        row.classList.add('table-secondary', 'opacity-50');
                    }
                    
                    row.innerHTML = `
                        <td>
                            <div class="jenis-info">
                                <i class="fas fa-${getJenisIcon(item.jenis_sampah)} text-primary"></i>
                                <strong>${escapeHtml(item.jenis_sampah)}</strong>
                                ${!item.status_aktif ? '<span class="badge bg-secondary ms-2">Nonaktif</span>' : ''}
                            </div>
                        </td>
                        <td>${escapeHtml(item.nama_jenis)}</td>
                        <td>
                            <div class="harga-display">
                                <strong class="text-success">
                                    Rp ${formatNumber(item.harga_per_satuan)}
                                </strong>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-secondary">${escapeHtml(item.satuan)}</span>
                        </td>
                        <td>
                            ${item.dapat_dijual ? 
                                '<span class="badge bg-success"><i class="fas fa-money-bill-wave"></i> Bisa Dijual</span>' : 
                                '<span class="badge bg-secondary"><i class="fas fa-times"></i> Tidak Bisa Dijual</span>'}
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" ${item.status_aktif ? 'checked' : ''} disabled style="cursor: not-allowed;">
                                <label class="form-check-label text-muted">
                                    ${item.status_aktif ? 'Aktif' : 'Nonaktif'}
                                </label>
                            </div>
                        </td>
                        <td>
                            <small class="text-muted">
                                ${formatDate(item.updated_at)}
                            </small>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button type="button" class="btn btn-sm btn-warning" 
                                        onclick="editHarga(${item.id})" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" 
                                        onclick="deleteHarga(${item.id}, '${escapeHtml(item.jenis_sampah)}')" 
                                        title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    `;
                    
                    tableBody.appendChild(row);
                });
            }
            
            // Helper functions
            function getJenisIcon(jenis) {
                const icons = {
                    'Plastik': 'wine-bottle',
                    'Kertas': 'file-alt',
                    'Logam': 'cog',
                    'Organik': 'seedling',
                    'Residu': 'trash-alt',
                    'Elektronik': 'laptop',
                    'Anorganik': 'box',
                    'Besi': 'wrench'
                };
                return icons[jenis] || 'recycle';
            }
            
            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
            
            function formatNumber(num) {
                return new Intl.NumberFormat('id-ID').format(num);
            }
            
            function formatDate(dateStr) {
                const date = new Date(dateStr);
                return date.toLocaleString('id-ID', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }
        });
    </script>
    <!-- Mobile Menu JS -->
    <script src="<?= base_url('/js/mobile-menu.js') ?>"></script>
    <!-- Page rendered at: <?= date('Y-m-d H:i:s') ?> -->
</body>
</html>