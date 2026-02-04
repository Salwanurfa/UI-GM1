<?php
// Helper function for waste icons
if (!function_exists('getWasteIcon')) {
    function getWasteIcon($jenis) {
        $icons = [
            'Kertas' => 'file-alt',
            'Plastik' => 'wine-bottle',
            'Organik' => 'seedling',
            'Anorganik' => 'cube',
            'Limbah Cair' => 'flask',
            'B3' => 'exclamation-triangle'
        ];
        return $icons[$jenis] ?? 'trash';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Laporan & Monitoring' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <div class="page-header">
            <div class="header-content">
                <h1><i class="fas fa-chart-line"></i> Laporan & Monitoring</h1>
                <p>Monitoring dan analisis sistem waste management</p>
            </div>
            
            <div class="header-actions">
                <a href="<?= base_url('/admin-pusat/laporan-waste') ?>" class="btn btn-primary">
                    <i class="fas fa-trash-alt"></i> Laporan Waste
                </a>
                <a href="<?= base_url('/admin-pusat/laporan/export-csv?' . http_build_query($filters)) ?>" 
                   class="btn btn-success">
                    <i class="fas fa-download"></i> Export CSV
                </a>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-database"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $summary['total_data'] ?></h3>
                    <p>Total Data</p>
                </div>
            </div>
            
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $summary['disetujui'] ?></h3>
                    <p>Disetujui</p>
                </div>
            </div>
            
            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $summary['menunggu_review'] ?></h3>
                    <p>Menunggu Review</p>
                </div>
            </div>
            
            <div class="stat-card danger">
                <div class="stat-icon">
                    <i class="fas fa-edit"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $summary['perlu_revisi'] ?></h3>
                    <p>Perlu Revisi</p>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-filter"></i>
                <h3>Filter Laporan</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="<?= base_url('/admin-pusat/laporan') ?>" class="filter-form">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="tahun" class="form-label">Tahun</label>
                            <select name="tahun" id="tahun" class="form-select">
                                <?php for ($i = date('Y'); $i >= 2020; $i--): ?>
                                <option value="<?= $i ?>" <?= ($filters['tahun'] == $i) ? 'selected' : '' ?>>
                                    <?= $i ?>
                                </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="unit" class="form-label">Unit</label>
                            <select name="unit" id="unit" class="form-select">
                                <option value="">Semua Unit</option>
                                <?php foreach ($allUnits as $unit): ?>
                                <option value="<?= $unit['id'] ?>" <?= ($filters['unit'] == $unit['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($unit['nama_unit']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="kategori" class="form-label">Kategori</label>
                            <select name="kategori" id="kategori" class="form-select">
                                <option value="">Semua Kategori</option>
                                <option value="waste" <?= ($filters['kategori'] == 'waste') ? 'selected' : '' ?>>Waste Management</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Progress per Unit -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-building"></i>
                <h3>Progress per Unit</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Unit</th>
                                <th>Total Data</th>
                                <th>Disetujui</th>
                                <th>Menunggu Review</th>
                                <th>Perlu Revisi</th>
                                <th>Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($progressUnit as $unit): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($unit['unit']['nama_unit']) ?></strong>
                                </td>
                                <td><?= $unit['total'] ?></td>
                                <td>
                                    <span class="badge bg-success"><?= $unit['disetujui'] ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-warning"><?= $unit['menunggu_review'] ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-danger"><?= $unit['perlu_revisi'] ?></span>
                                </td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" 
                                             style="width: <?= $unit['progress_persen'] ?>%">
                                            <?= $unit['progress_persen'] ?>%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Waste Management Summary -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-trash-alt"></i>
                <h3>Ringkasan Waste Management</h3>
            </div>
            <div class="card-body">
                <div class="waste-summary-grid">
                    <?php foreach ($rekapWaste as $jenis => $data): ?>
                    <div class="waste-summary-item">
                        <div class="waste-icon">
                            <i class="fas fa-<?= getWasteIcon($jenis) ?>"></i>
                        </div>
                        <div class="waste-info">
                            <h5><?= $jenis ?></h5>
                            <div class="waste-stats">
                                <div class="stat-row">
                                    <span>Data:</span>
                                    <strong><?= $data['total_data'] ?></strong>
                                </div>
                                <div class="stat-row">
                                    <span>Disetujui:</span>
                                    <strong><?= $data['disetujui'] ?></strong>
                                </div>
                                <div class="stat-row">
                                    <span>Berat:</span>
                                    <strong><?= number_format($data['total_berat'], 2) ?> <?= ($jenis === 'Limbah Cair') ? 'L' : 'kg' ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-bolt"></i>
                <h3>Quick Actions</h3>
            </div>
            <div class="card-body">
                <div class="quick-actions">
                    <a href="<?= base_url('/admin-pusat/waste') ?>" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-trash-alt"></i>
                        </div>
                        <div class="action-content">
                            <h6>Waste Management</h6>
                            <p>Review dan approve data waste</p>
                        </div>
                    </a>
                    
                    <a href="<?= base_url('/admin-pusat/laporan-waste') ?>" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <div class="action-content">
                            <h6>Laporan Waste</h6>
                            <p>Analisis data waste yang disetujui</p>
                        </div>
                    </a>
                    
                    <a href="<?= base_url('/admin-pusat/user-management') ?>" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="action-content">
                            <h6>User Management</h6>
                            <p>Kelola akun pengguna sistem</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

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
.stat-card.danger { border-left-color: #dc3545; }

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
.stat-card.danger .stat-icon { background: #dc3545; }

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
    display: flex;
    align-items: center;
    gap: 15px;
}

.card-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.card-body {
    padding: 25px;
}

.filter-form .row {
    align-items: end;
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

.progress {
    height: 20px;
    border-radius: 10px;
}

.waste-summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.waste-summary-item {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.waste-icon {
    width: 50px;
    height: 50px;
    background: #007bff;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.waste-info h5 {
    margin: 0 0 10px 0;
    font-size: 16px;
    font-weight: 600;
    color: #2c3e50;
}

.waste-stats {
    font-size: 14px;
}

.stat-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
}

.quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.action-card {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    text-decoration: none;
    color: inherit;
    display: flex;
    align-items: center;
    gap: 15px;
    transition: all 0.3s ease;
}

.action-card:hover {
    background: #e9ecef;
    transform: translateY(-2px);
    color: inherit;
}

.action-icon {
    width: 50px;
    height: 50px;
    background: #007bff;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.action-content h6 {
    margin: 0 0 5px 0;
    font-weight: 600;
    color: #2c3e50;
}

.action-content p {
    margin: 0;
    font-size: 14px;
    color: #6c757d;
}

.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .main-content {
        margin-left: 0;
        padding: 20px;
    }
    
    .page-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .waste-summary-grid {
        grid-template-columns: 1fr;
    }
    
    .quick-actions {
        grid-template-columns: 1fr;
    }
}
</style>