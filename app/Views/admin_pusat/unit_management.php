<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-name" content="<?= csrf_token() ?>">
    <title><?= $title ?? 'Manajemen Unit' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('/css/mobile-responsive.css') ?>" rel="stylesheet">
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <div class="page-header">
            <div class="header-content">
                <h1><i class="fas fa-building"></i> Manajemen Unit</h1>
                <p>Kelola unit/fakultas/jurusan dalam sistem</p>
            </div>
            
            <div class="header-actions">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUnitModal">
                    <i class="fas fa-plus"></i> Tambah Unit
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $stats['total'] ?></h3>
                    <p>Total Unit</p>
                </div>
            </div>
            
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $stats['active'] ?></h3>
                    <p>Unit Aktif</p>
                </div>
            </div>
            
            <div class="stat-card info">
                <div class="stat-icon">
                    <i class="fas fa-university"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $stats['fakultas'] ?></h3>
                    <p>Fakultas</p>
                </div>
            </div>
            
            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $stats['jurusan'] ?></h3>
                    <p>Jurusan</p>
                </div>
            </div>
            
            <div class="stat-card secondary">
                <div class="stat-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $stats['unit_kerja'] ?></h3>
                    <p>Unit Kerja</p>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-filter"></i>
                <h3>Filter Unit</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="<?= base_url('/admin-pusat/unit-management') ?>" class="filter-form">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="filter_tipe" class="form-label">Tipe Unit</label>
                            <select name="tipe" id="filter_tipe" class="form-select">
                                <option value="">Semua Tipe</option>
                                <?php foreach ($allTipes as $tipeKey => $tipeLabel): ?>
                                <option value="<?= $tipeKey ?>" <?= ($filters['tipe'] == $tipeKey) ? 'selected' : '' ?>>
                                    <?= $tipeLabel ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="filter_status" class="form-label">Status</label>
                            <select name="status" id="filter_status" class="form-select">
                                <option value="">Semua Status</option>
                                <?php foreach ($allStatus as $value => $label): ?>
                                <option value="<?= $value ?>" <?= ($filters['status'] == $value) ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
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

        <!-- Units Table -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-table"></i>
                <h3>Daftar Unit (<?= count($units) ?>)</h3>
            </div>
            <div class="card-body">
                <?php if (empty($units)): ?>
                <div class="empty-state">
                    <i class="fas fa-building"></i>
                    <p>Tidak ada unit ditemukan</p>
                    <?php if (isset($error)): ?>
                    <p class="text-danger"><?= $error ?></p>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kode Unit</th>
                                <th>Nama Unit</th>
                                <th>Tipe</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                                <th>Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($units as $unit): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($unit['kode_unit']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($unit['nama_unit']) ?></td>
                                <td>
                                    <span class="tipe-badge tipe-<?= $unit['tipe_unit'] ?>">
                                        <?php 
                                        $tipeLabels = [
                                            'fakultas' => 'Fakultas',
                                            'jurusan' => 'Jurusan',
                                            'unit_kerja' => 'Unit Kerja',
                                            'lembaga' => 'Lembaga'
                                        ];
                                        echo $tipeLabels[$unit['tipe_unit']] ?? ucfirst(str_replace('_', ' ', $unit['tipe_unit']));
                                        ?>
                                    </span>
                                </td>
                                <td><?= $unit['deskripsi'] ? htmlspecialchars($unit['deskripsi']) : '-' ?></td>
                                <td>
                                    <span class="status-badge status-<?= $unit['status_aktif'] ? 'active' : 'inactive' ?>">
                                        <?= $unit['status_aktif'] ? 'Aktif' : 'Nonaktif' ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($unit['created_at'])) ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button type="button" class="btn btn-sm btn-warning" 
                                                onclick="editUnit(<?= $unit['id'] ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-info" 
                                                onclick="toggleStatus(<?= $unit['id'] ?>, <?= $unit['status_aktif'] ?>)">
                                            <i class="fas fa-<?= $unit['status_aktif'] ? 'toggle-on' : 'toggle-off' ?>"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="deleteUnit(<?= $unit['id'] ?>, '<?= htmlspecialchars($unit['nama_unit']) ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Add Unit Modal -->
        <div class="modal fade" id="addUnitModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Unit Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="addUnitForm">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_kode_unit" class="form-label">Kode Unit *</label>
                                        <input type="text" class="form-control" name="kode_unit" id="add_kode_unit" required>
                                        <small class="form-text text-muted">Contoh: FT, FE, MIPA</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_tipe_unit" class="form-label">Tipe Unit *</label>
                                        <select class="form-select" name="tipe_unit" id="add_tipe_unit" required>
                                            <option value="">Pilih Tipe</option>
                                            <option value="fakultas">Fakultas</option>
                                            <option value="jurusan">Jurusan</option>
                                            <option value="unit_kerja">Unit Kerja</option>
                                            <option value="lembaga">Lembaga</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="add_nama_unit" class="form-label">Nama Unit *</label>
                                <input type="text" class="form-control" name="nama_unit" id="add_nama_unit" required>
                                <small class="form-text text-muted">Contoh: Fakultas Teknik, Jurusan Informatika</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="add_deskripsi" class="form-label">Deskripsi (Opsional)</label>
                                <textarea class="form-control" name="deskripsi" id="add_deskripsi" rows="3"></textarea>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Unit Modal -->
        <div class="modal fade" id="editUnitModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Unit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="editUnitForm">
                        <input type="hidden" name="unit_id" id="edit_unit_id">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_kode_unit" class="form-label">Kode Unit *</label>
                                        <input type="text" class="form-control" name="kode_unit" id="edit_kode_unit" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_tipe_unit" class="form-label">Tipe Unit *</label>
                                        <select class="form-select" name="tipe_unit" id="edit_tipe_unit" required>
                                            <option value="fakultas">Fakultas</option>
                                            <option value="jurusan">Jurusan</option>
                                            <option value="unit_kerja">Unit Kerja</option>
                                            <option value="lembaga">Lembaga</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="edit_nama_unit" class="form-label">Nama Unit *</label>
                                <input type="text" class="form-control" name="nama_unit" id="edit_nama_unit" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="edit_deskripsi" class="form-label">Deskripsi (Opsional)</label>
                                <textarea class="form-control" name="deskripsi" id="edit_deskripsi" rows="3"></textarea>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add Unit
        document.getElementById('addUnitForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Add CSRF token
            const csrfName = document.querySelector('meta[name="csrf-name"]').getAttribute('content');
            const csrfHash = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            formData.append(csrfName, csrfHash);
            
            fetch('<?= base_url('/admin-pusat/unit-management/create') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('addUnitModal')).hide();
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan sistem');
            });
        });

        // Edit Unit
        function editUnit(id) {
            fetch(`<?= base_url('/admin-pusat/unit-management/get/') ?>${id}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const unit = data.data;
                    document.getElementById('edit_unit_id').value = unit.id;
                    document.getElementById('edit_kode_unit').value = unit.kode_unit;
                    document.getElementById('edit_nama_unit').value = unit.nama_unit;
                    document.getElementById('edit_tipe_unit').value = unit.tipe_unit;
                    document.getElementById('edit_deskripsi').value = unit.deskripsi || '';
                    
                    const modal = new bootstrap.Modal(document.getElementById('editUnitModal'));
                    modal.show();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan sistem');
            });
        }

        // Update Unit
        document.getElementById('editUnitForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const unitId = document.getElementById('edit_unit_id').value;
            
            // Add CSRF token
            const csrfName = document.querySelector('meta[name="csrf-name"]').getAttribute('content');
            const csrfHash = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            formData.append(csrfName, csrfHash);
            
            fetch(`<?= base_url('/admin-pusat/unit-management/update/') ?>${unitId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('editUnitModal')).hide();
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan sistem');
            });
        });

        // Toggle Status
        function toggleStatus(id, currentStatus) {
            const action = currentStatus ? 'nonaktifkan' : 'aktifkan';
            
            if (!confirm(`Apakah Anda yakin ingin ${action} unit ini?`)) {
                return;
            }
            
            const csrfName = document.querySelector('meta[name="csrf-name"]').getAttribute('content');
            const csrfHash = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            const formData = new FormData();
            formData.append(csrfName, csrfHash);
            
            fetch(`<?= base_url('/admin-pusat/unit-management/toggle-status/') ?>${id}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan sistem');
            });
        }

        // Delete Unit
        function deleteUnit(id, namaUnit) {
            if (!confirm(`Apakah Anda yakin ingin menghapus unit "${namaUnit}"?\n\nPeringatan: Unit yang masih digunakan oleh user tidak dapat dihapus.`)) {
                return;
            }
            
            const csrfName = document.querySelector('meta[name="csrf-name"]').getAttribute('content');
            const csrfHash = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            const formData = new FormData();
            formData.append(csrfName, csrfHash);
            
            fetch(`<?= base_url('/admin-pusat/unit-management/delete/') ?>${id}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan sistem');
            });
        }
    </script>
</body>
</html>

<style>
body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f8f9fa;
}

.main-content {
    margin-left: 250px;
    padding: 20px;
    min-height: 100vh;
}

.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 10px;
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-content h1 {
    margin: 0;
    font-size: 2rem;
    font-weight: 600;
}

.header-content p {
    margin: 10px 0 0 0;
    opacity: 0.9;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.stat-card.primary .stat-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.stat-card.success .stat-icon {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
}

.stat-card.info .stat-icon {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    color: white;
}

.stat-card.warning .stat-icon {
    background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
    color: white;
}

.stat-card.secondary .stat-icon {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
}

.stat-content h3 {
    margin: 0;
    font-size: 2rem;
    font-weight: 700;
}

.stat-content p {
    margin: 5px 0 0 0;
    color: #6c757d;
}

.card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px 20px;
    border-radius: 10px 10px 0 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-header h3 {
    margin: 0;
    font-size: 1.2rem;
}

.card-body {
    padding: 20px;
}

.table {
    margin: 0;
}

.table thead th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
}

.tipe-badge {
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 0.875rem;
    font-weight: 600;
}

.tipe-fakultas {
    background-color: #e3f2fd;
    color: #1976d2;
}

.tipe-jurusan {
    background-color: #fff3e0;
    color: #f57c00;
}

.tipe-unit_kerja {
    background-color: #f3e5f5;
    color: #7b1fa2;
}

.tipe-lembaga {
    background-color: #e8f5e9;
    color: #388e3c;
}

.status-badge {
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 0.875rem;
    font-weight: 600;
}

.status-active {
    background-color: #d4edda;
    color: #155724;
}

.status-inactive {
    background-color: #f8d7da;
    color: #721c24;
}

.action-buttons {
    display: flex;
    gap: 5px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.3;
}

@media (max-width: 768px) {
    .main-content {
        margin-left: 0;
        padding: 15px;
    }
    
    .page-header {
        flex-direction: column;
        gap: 15px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>
