<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 5px 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255,255,255,0.2);
            color: white;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .stats-card {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 15px;
        }
        .stats-card.pending {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        }
        .stats-card.approved {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        .stats-card.rejected {
            background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
        }
        .badge-status {
            font-size: 0.8em;
            padding: 5px 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar">
                    <div class="p-3">
                        <h5 class="text-white mb-0">
                            <i class="fas fa-recycle me-2"></i>
                            Admin Pusat
                        </h5>
                        <small class="text-white-50">UIGM POLBAN</small>
                    </div>
                    
                    <nav class="nav flex-column">
                        <a class="nav-link" href="<?= base_url('admin-pusat/dashboard') ?>">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Dashboard UIGM
                        </a>
                        <a class="nav-link active" href="<?= base_url('admin-pusat/waste-management') ?>">
                            <i class="fas fa-trash-alt me-2"></i>
                            Manajemen Data Sampah
                        </a>
                        <a class="nav-link" href="<?= base_url('admin-pusat/monitoring') ?>">
                            <i class="fas fa-chart-line me-2"></i>
                            Monitoring
                        </a>
                        
                        <hr class="text-white-50 mx-3">
                        
                        <a class="nav-link" href="<?= base_url('auth/logout') ?>">
                            <i class="fas fa-sign-out-alt me-2"></i>
                            Logout
                        </a>
                    </nav>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="mb-1">Waste Management Dashboard</h2>
                            <p class="text-muted mb-0">Kelola dan review data sampah dari seluruh unit</p>
                        </div>
                        <div>
                            <a href="<?= base_url('admin-pusat/waste-management/review') ?>" class="btn btn-primary me-2">
                                <i class="fas fa-eye me-1"></i>
                                Review Data
                            </a>
                            <a href="<?= base_url('admin-pusat/waste-management/export-csv?' . http_build_query($filters)) ?>" class="btn btn-success">
                                <i class="fas fa-download me-1"></i>
                                Export CSV
                            </a>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card stats-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-database fa-2x mb-2"></i>
                                    <h3 class="mb-1"><?= number_format($stats['total_data']) ?></h3>
                                    <p class="mb-0">Total Data</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card pending">
                                <div class="card-body text-center">
                                    <i class="fas fa-clock fa-2x mb-2"></i>
                                    <h3 class="mb-1"><?= number_format($stats['pending']) ?></h3>
                                    <p class="mb-0">Pending Review</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card approved">
                                <div class="card-body text-center">
                                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                                    <h3 class="mb-1"><?= number_format($stats['approved']) ?></h3>
                                    <p class="mb-0">Approved</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card rejected">
                                <div class="card-body text-center">
                                    <i class="fas fa-times-circle fa-2x mb-2"></i>
                                    <h3 class="mb-1"><?= number_format($stats['rejected']) ?></h3>
                                    <p class="mb-0">Rejected</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Data per Jenis Sampah</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="jenisChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Data per Unit</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="unitChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Filter Data</h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="<?= base_url('admin-pusat/waste-management') ?>">
                                <div class="row">
                                    <div class="col-md-2">
                                        <select name="unit_id" class="form-select">
                                            <option value="">Semua Unit</option>
                                            <?php foreach ($units as $unit): ?>
                                                <option value="<?= $unit['id'] ?>" <?= ($filters['unit_id'] == $unit['id']) ? 'selected' : '' ?>>
                                                    <?= esc($unit['nama_unit']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="status_review" class="form-select">
                                            <option value="">Semua Status</option>
                                            <option value="pending" <?= ($filters['status_review'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                                            <option value="approved" <?= ($filters['status_review'] == 'approved') ? 'selected' : '' ?>>Approved</option>
                                            <option value="rejected" <?= ($filters['status_review'] == 'rejected') ? 'selected' : '' ?>>Rejected</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="jenis_sampah" class="form-select">
                                            <option value="">Semua Jenis</option>
                                            <?php foreach ($jenisOptions as $jenis): ?>
                                                <option value="<?= $jenis ?>" <?= ($filters['jenis_sampah'] == $jenis) ? 'selected' : '' ?>>
                                                    <?= $jenis ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="date" name="tanggal_dari" class="form-control" value="<?= $filters['tanggal_dari'] ?>" placeholder="Dari Tanggal">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="date" name="tanggal_sampai" class="form-control" value="<?= $filters['tanggal_sampai'] ?>" placeholder="Sampai Tanggal">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-search me-1"></i>
                                            Filter
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Data Waste Management</h5>
                            <span class="badge bg-primary"><?= count($wasteData) ?> data</span>
                        </div>
                        <div class="card-body">
                            <?php if (empty($wasteData)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Tidak ada data</h5>
                                    <p class="text-muted">Belum ada data waste management yang sesuai dengan filter</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>User</th>
                                                <th>Unit</th>
                                                <th>Unit/Prodi</th>
                                                <th>Jenis Sampah</th>
                                                <th>Jumlah</th>
                                                <th>Gedung</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($wasteData as $data): ?>
                                                <tr>
                                                    <td><?= date('d/m/Y', strtotime($data['tanggal'])) ?></td>
                                                    <td>
                                                        <strong><?= esc($data['nama_lengkap']) ?></strong><br>
                                                        <small class="text-muted"><?= esc($data['username']) ?></small>
                                                    </td>
                                                    <td><?= esc($data['nama_unit'] ?? '-') ?></td>
                                                    <td><?= esc($data['unit_prodi']) ?></td>
                                                    <td>
                                                        <span class="badge bg-info"><?= esc($data['jenis_sampah']) ?></span>
                                                    </td>
                                                    <td>
                                                        <strong><?= number_format($data['jumlah'], 2) ?></strong>
                                                        <small class="text-muted"><?= esc($data['satuan']) ?></small>
                                                    </td>
                                                    <td><?= esc($data['gedung']) ?></td>
                                                    <td>
                                                        <?php
                                                        $badgeClass = [
                                                            'pending' => 'warning',
                                                            'approved' => 'success',
                                                            'rejected' => 'danger'
                                                        ];
                                                        ?>
                                                        <span class="badge bg-<?= $badgeClass[$data['status_review']] ?> badge-status">
                                                            <?= ucfirst($data['status_review']) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if ($data['status_review'] === 'pending'): ?>
                                                            <button class="btn btn-success btn-sm me-1" onclick="approveData(<?= $data['id'] ?>)">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                            <button class="btn btn-danger btn-sm" onclick="rejectData(<?= $data['id'] ?>)">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        <?php else: ?>
                                                            <button class="btn btn-info btn-sm" onclick="viewDetails(<?= $data['id'] ?>)">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                        <?php endif; ?>
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
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Chart for Jenis Sampah
        const jenisCtx = document.getElementById('jenisChart').getContext('2d');
        const jenisData = <?= json_encode($stats['jenis_sampah']) ?>;
        
        new Chart(jenisCtx, {
            type: 'doughnut',
            data: {
                labels: jenisData.map(item => item.jenis_sampah),
                datasets: [{
                    data: jenisData.map(item => item.jumlah),
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Chart for Unit
        const unitCtx = document.getElementById('unitChart').getContext('2d');
        const unitData = <?= json_encode($stats['unit_stats']) ?>;
        
        new Chart(unitCtx, {
            type: 'bar',
            data: {
                labels: unitData.map(item => item.nama_unit || 'Tidak Ada Unit'),
                datasets: [{
                    label: 'Jumlah Data',
                    data: unitData.map(item => item.jumlah_data),
                    backgroundColor: '#36A2EB'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Functions for approve/reject
        function approveData(id) {
            if (confirm('Apakah Anda yakin ingin menyetujui data ini?')) {
                fetch(`<?= base_url('admin-pusat/waste-management/approve/') ?>${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                });
            }
        }

        function rejectData(id) {
            const catatan = prompt('Masukkan catatan penolakan:');
            if (catatan) {
                fetch(`<?= base_url('admin-pusat/waste-management/reject/') ?>${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ catatan: catatan })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                });
            }
        }

        function viewDetails(id) {
            // Implement view details modal or redirect
            alert('Fitur detail akan segera tersedia');
        }
    </script>
</body>
</html>