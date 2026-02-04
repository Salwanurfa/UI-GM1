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
        .review-card {
            border-left: 4px solid #ffc107;
            transition: all 0.3s ease;
        }
        .review-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
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
                        <a class="nav-link" href="<?= base_url('admin-pusat/waste-management') ?>">
                            <i class="fas fa-trash-alt me-2"></i>
                            Waste Management
                        </a>
                        <a class="nav-link active" href="<?= base_url('admin-pusat/waste-management/review') ?>">
                            <i class="fas fa-eye me-2"></i>
                            Review Data
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
                            <h2 class="mb-1">Review Data Waste Management</h2>
                            <p class="text-muted mb-0">Review dan setujui data sampah yang masuk</p>
                        </div>
                        <div>
                            <button class="btn btn-success me-2" onclick="bulkApprove()">
                                <i class="fas fa-check-double me-1"></i>
                                Setujui Terpilih
                            </button>
                            <button class="btn btn-danger" onclick="bulkReject()">
                                <i class="fas fa-times-circle me-1"></i>
                                Tolak Terpilih
                            </button>
                        </div>
                    </div>

                    <!-- Bulk Selection -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label" for="selectAll">
                                    <strong>Pilih Semua Data</strong>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Data -->
                    <?php if (empty($pendingData)): ?>
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <h4 class="text-muted">Semua Data Sudah Direview</h4>
                                <p class="text-muted">Tidak ada data yang menunggu review saat ini</p>
                                <a href="<?= base_url('admin-pusat/waste-management') ?>" class="btn btn-primary">
                                    <i class="fas fa-arrow-left me-1"></i>
                                    Kembali ke Dashboard
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($pendingData as $data): ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card review-card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input data-checkbox" type="checkbox" value="<?= $data['id'] ?>" id="check<?= $data['id'] ?>">
                                                <label class="form-check-label" for="check<?= $data['id'] ?>">
                                                    <strong>ID: <?= $data['id'] ?></strong>
                                                </label>
                                            </div>
                                            <span class="badge bg-warning">Pending Review</span>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <small class="text-muted">Tanggal:</small>
                                                    <p class="mb-2"><strong><?= date('d/m/Y', strtotime($data['tanggal'])) ?></strong></p>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted">User:</small>
                                                    <p class="mb-2"><strong><?= esc($data['nama_lengkap']) ?></strong></p>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-6">
                                                    <small class="text-muted">Unit:</small>
                                                    <p class="mb-2"><?= esc($data['nama_unit'] ?? 'Tidak Ada Unit') ?></p>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted">Unit/Prodi:</small>
                                                    <p class="mb-2"><?= esc($data['unit_prodi']) ?></p>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-6">
                                                    <small class="text-muted">Jenis Sampah:</small>
                                                    <p class="mb-2">
                                                        <span class="badge bg-info"><?= esc($data['jenis_sampah']) ?></span>
                                                    </p>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted">Jumlah:</small>
                                                    <p class="mb-2"><strong><?= number_format($data['jumlah'], 2) ?> <?= esc($data['satuan']) ?></strong></p>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-12">
                                                    <small class="text-muted">Gedung:</small>
                                                    <p class="mb-3"><?= esc($data['gedung']) ?></p>
                                                </div>
                                            </div>
                                            
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-success flex-fill" onclick="approveData(<?= $data['id'] ?>)">
                                                    <i class="fas fa-check me-1"></i>
                                                    Setujui
                                                </button>
                                                <button class="btn btn-danger flex-fill" onclick="rejectData(<?= $data['id'] ?>)">
                                                    <i class="fas fa-times me-1"></i>
                                                    Tolak
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Select All functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.data-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Individual approve
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
                })
                .catch(error => {
                    alert('Terjadi kesalahan: ' + error.message);
                });
            }
        }

        // Individual reject
        function rejectData(id) {
            const catatan = prompt('Masukkan catatan penolakan:');
            if (catatan && catatan.trim() !== '') {
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
                })
                .catch(error => {
                    alert('Terjadi kesalahan: ' + error.message);
                });
            }
        }

        // Bulk approve
        function bulkApprove() {
            const selectedIds = getSelectedIds();
            if (selectedIds.length === 0) {
                alert('Pilih data yang akan disetujui');
                return;
            }

            if (confirm(`Apakah Anda yakin ingin menyetujui ${selectedIds.length} data terpilih?`)) {
                fetch(`<?= base_url('admin-pusat/waste-management/bulk-action') ?>`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        action: 'approve',
                        ids: selectedIds
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    alert('Terjadi kesalahan: ' + error.message);
                });
            }
        }

        // Bulk reject
        function bulkReject() {
            const selectedIds = getSelectedIds();
            if (selectedIds.length === 0) {
                alert('Pilih data yang akan ditolak');
                return;
            }

            const catatan = prompt('Masukkan catatan penolakan untuk semua data terpilih:');
            if (catatan && catatan.trim() !== '') {
                fetch(`<?= base_url('admin-pusat/waste-management/bulk-action') ?>`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        action: 'reject',
                        ids: selectedIds,
                        catatan: catatan
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    alert('Terjadi kesalahan: ' + error.message);
                });
            }
        }

        // Get selected IDs
        function getSelectedIds() {
            const checkboxes = document.querySelectorAll('.data-checkbox:checked');
            return Array.from(checkboxes).map(cb => parseInt(cb.value));
        }
    </script>
</body>
</html>