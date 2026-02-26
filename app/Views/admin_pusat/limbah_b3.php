<?php
/**
 * Admin Pusat - Dashboard Limbah B3
 */

$limbah_list = $limbah_list ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Limbah B3 Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?= $this->include('partials/sidebar') ?>

<div class="main-content">
    <div class="page-header">
        <div class="header-content">
            <h1><i class="fas fa-skull-crossbones"></i> Limbah B3 Management</h1>
            <p>Monitor data Limbah B3 dari semua unit</p>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Berhasil!</strong> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Error!</strong> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <i class="fas fa-table me-2"></i>
                <h3 class="mb-0">Data Limbah B3 (<?= count($limbah_list) ?>)</h3>
            </div>
            <a href="<?= base_url('/admin-pusat/limbah-b3/create') ?>" class="btn btn-sm btn-success">
                <i class="fas fa-plus me-2"></i>Tambah Data
            </a>
        </div>
        <div class="card-body">
            <?php if (empty($limbah_list)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>Tidak ada data Limbah B3</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Input</th>
                            <th>Nama Limbah</th>
                            <th>Kode Limbah</th>
                            <th>Kategori Bahaya</th>
                            <th>Lokasi</th>
                            <th>Bentuk Fisik</th>
                            <th>Timbulan</th>
                            <th>Kemasan</th>
                            <th>Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1; foreach ($limbah_list as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($row['tanggal_input'])) ?></td>
                                <td><?= esc($row['nama_limbah'] ?? '-') ?></td>
                                <td><?= esc($row['kode_limbah'] ?? '-') ?></td>
                                <td><?= esc($row['kategori_bahaya'] ?? '-') ?></td>
                                <td><?= esc($row['lokasi'] ?? '-') ?></td>
                                <td><?= esc($row['bentuk_fisik'] ?? '-') ?></td>
                                <td><?= number_format($row['timbulan'] ?? 0, 2, ',', '.') . ' ' . esc($row['satuan'] ?? '') ?></td>
                                <td><?= esc($row['kemasan'] ?? '-') ?></td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Yakin?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
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
    .card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        margin-bottom: 30px;
        overflow: hidden;
        border: none;
    }
    .card-header {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        padding: 15px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .card-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
    }
    .card-body {
        padding: 20px;
    }
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #6c757d;
    }
    .empty-state i {
        font-size: 48px;
        margin-bottom: 15px;
    }
    
    /* Button Styles */
    .btn {
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
    }
    .btn-success {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        color: white;
    }
    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(67, 233, 123, 0.4);
        color: white;
    }
    
    /* Alert Styles */
    .alert {
        border-radius: 10px;
        margin-bottom: 20px;
        border: none;
    }
    .alert-success {
        background: #d4edda;
        color: #155724;
    }
    .alert-danger {
        background: #f8d7da;
        color: #721c24;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
        }
        .card-header {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }
        .btn {
            width: 100%;
        }
    }
</style>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Auto-hide alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.remove();
                }, 300);
            }, 5000);
        });
    });
</script>

</body>
</html>

