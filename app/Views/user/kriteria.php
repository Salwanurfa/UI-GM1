<!-- File: app/Views/user/kriteria.php -->

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - UIGM POLBAN</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
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
            transform: translateX(5px);
        }
        
        .main-content {
            padding: 20px;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 15px 20px;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 10px 25px;
            font-weight: 500;
        }
        
        .btn-danger {
            border-radius: 8px;
            padding: 5px 15px;
        }
        
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table thead th {
            background-color: #f8f9fa;
            border: none;
            font-weight: 600;
            color: #495057;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .user-header {
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .stats-card {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        
        .category-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8em;
            font-weight: 500;
            margin-right: 5px;
        }
        
        .badge-kertas { background-color: #ffc107; color: #000; }
        .badge-plastik { background-color: #17a2b8; color: #fff; }
        .badge-organik { background-color: #28a745; color: #fff; }
        .badge-anorganik { background-color: #6c757d; color: #fff; }
        .badge-limbah { background-color: #dc3545; color: #fff; }
        .badge-b3 { background-color: #fd7e14; color: #fff; }
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
                            Waste Management
                        </h5>
                        <small class="text-white-50">UIGM POLBAN</small>
                    </div>
                    
                    <nav class="nav flex-column">
                        <div class="px-3 pb-2">
                            <small class="text-white-50 text-uppercase">Menu</small>
                        </div>
                        
                        <!-- Menu Utama Waste Management -->
                        <a class="nav-link active" href="<?= base_url('user/kriteria') ?>">
                            <i class="fas fa-recycle me-2"></i>
                            Waste Management
                            <span class="badge bg-light text-dark ms-auto">
                                <?= count($kriteria_data) ?>
                            </span>
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
                <div class="main-content">
                    <!-- User Header -->
                    <div class="user-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="mb-1">
                                    <i class="fas fa-user-circle text-primary me-2"></i>
                                    Selamat datang, <?= esc($user_name) ?>!
                                </h4>
                                <p class="text-muted mb-0">Kelola data sampah Anda dengan mudah</p>
                            </div>
                            <div class="col-auto">
                                <div class="stats-card">
                                    <h5 class="mb-1"><?= count($kriteria_data) ?></h5>
                                    <small>Total Data</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Alert Messages -->
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Terdapat kesalahan:</strong>
                            <ul class="mb-0 mt-2">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Form Input Data -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-plus-circle me-2"></i>
                                Input Data Sampah
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('user/kriteria') ?>" method="POST" id="kriteriaForm">
                                <?= csrf_field() ?>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="tanggal" class="form-label">
                                                <i class="fas fa-calendar me-1"></i>
                                                Tanggal <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" class="form-control" id="tanggal" name="tanggal" 
                                                   value="<?= old('tanggal', date('Y-m-d')) ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="unit_prodi" class="form-label">
                                                <i class="fas fa-building me-1"></i>
                                                Unit / Jurusan / Prodi <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="unit_prodi" name="unit_prodi" 
                                                   placeholder="Contoh: Teknik Informatika" 
                                                   value="<?= old('unit_prodi') ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="jenis_sampah" class="form-label">
                                                <i class="fas fa-trash me-1"></i>
                                                Jenis Sampah <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select" id="jenis_sampah" name="jenis_sampah" required>
                                                <option value="">Pilih Jenis Sampah</option>
                                                <?php foreach ($jenis_sampah_options as $value => $label): ?>
                                                    <option value="<?= $value ?>" <?= (old('jenis_sampah') === $value) ? 'selected' : '' ?>>
                                                        <?= $label ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <label for="satuan" class="form-label">
                                                <i class="fas fa-weight me-1"></i>
                                                Satuan
                                            </label>
                                            <input type="text" class="form-control" id="satuan" name="satuan" 
                                                   placeholder="Auto" readonly value="<?= old('satuan') ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="jumlah" class="form-label">
                                                <i class="fas fa-calculator me-1"></i>
                                                Jumlah <span class="text-danger">*</span>
                                            </label>
                                            <input type="number" class="form-control" id="jumlah" name="jumlah" 
                                                   placeholder="0" step="0.01" min="0.01" 
                                                   value="<?= old('jumlah') ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="gedung" class="form-label">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                Gedung <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select" id="gedung" name="gedung" required>
                                                <option value="">Pilih Gedung</option>
                                                <?php foreach ($gedung_options as $value => $label): ?>
                                                    <option value="<?= $value ?>" <?= (old('gedung') === $value) ? 'selected' : '' ?>>
                                                        <?= $label ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>
                                        Simpan Data
                                    </button>
                                    <button type="reset" class="btn btn-secondary">
                                        <i class="fas fa-undo me-2"></i>
                                        Batal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Tabel Data -->
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="mb-0">
                                        <i class="fas fa-table me-2"></i>
                                        Data Sampah Anda
                                        <?php if ($jenis_filter !== 'Semua'): ?>
                                            <span class="category-badge badge-<?= strtolower(str_replace(' ', '', $jenis_filter)) ?>">
                                                <?= $jenis_filter ?>
                                            </span>
                                        <?php endif; ?>
                                    </h5>
                                </div>
                                <div class="col-auto">
                                    <small class="text-white-50">
                                        Total: <?= count($kriteria_data) ?> data
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (empty($kriteria_data)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Belum ada data sampah</h5>
                                    <p class="text-muted">Mulai input data sampah menggunakan form di atas</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tanggal</th>
                                                <th>Unit/Prodi</th>
                                                <th>Jenis Sampah</th>
                                                <th>Jumlah</th>
                                                <th>Gedung</th>
                                                <th>Status Review</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($kriteria_data as $index => $data): ?>
                                                <tr>
                                                    <td><?= $index + 1 ?></td>
                                                    <td><?= date('d/m/Y', strtotime($data['tanggal'])) ?></td>
                                                    <td><?= esc($data['unit_prodi']) ?></td>
                                                    <td>
                                                        <span class="category-badge badge-<?= getBadgeClass($data['jenis_sampah']) ?>">
                                                            <?= esc($data['jenis_sampah']) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <strong><?= number_format($data['jumlah'], 2) ?></strong>
                                                        <small class="text-muted"><?= esc($data['satuan']) ?></small>
                                                    </td>
                                                    <td><?= esc($data['gedung']) ?></td>
                                                    <td>
                                                        <?php
                                                        $statusBadge = [
                                                            'pending' => ['class' => 'warning', 'text' => 'Menunggu Review'],
                                                            'approved' => ['class' => 'success', 'text' => 'Disetujui'],
                                                            'rejected' => ['class' => 'danger', 'text' => 'Ditolak']
                                                        ];
                                                        $status = $statusBadge[$data['status_review']] ?? ['class' => 'secondary', 'text' => 'Unknown'];
                                                        ?>
                                                        <span class="badge bg-<?= $status['class'] ?>" 
                                                              <?= ($data['status_review'] === 'rejected' && !empty($data['catatan_review'])) ? 'title="' . esc($data['catatan_review']) . '"' : '' ?>>
                                                            <?= $status['text'] ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if ($data['status_review'] === 'pending'): ?>
                                                            <a href="<?= base_url('user/kriteria/delete/' . $data['id']) ?>" 
                                                               class="btn btn-danger btn-sm"
                                                               onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        <?php else: ?>
                                                            <span class="text-muted">
                                                                <i class="fas fa-lock" title="Data sudah direview, tidak dapat dihapus"></i>
                                                            </span>
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
        // Auto-fill satuan berdasarkan jenis sampah
        document.getElementById('jenis_sampah').addEventListener('change', function() {
            const jenisSampah = this.value;
            const satuanField = document.getElementById('satuan');
            
            const satuanMap = {
                'Kertas': 'kg',
                'Plastik': 'kg',
                'Organik': 'kg',
                'Anorganik': 'kg',
                'Limbah Cair': 'L',
                'B3': 'kg'
            };
            
            satuanField.value = satuanMap[jenisSampah] || '';
        });
        
        // Form validation
        document.getElementById('kriteriaForm').addEventListener('submit', function(e) {
            const jumlah = document.getElementById('jumlah').value;
            
            if (parseFloat(jumlah) <= 0) {
                e.preventDefault();
                alert('Jumlah harus lebih dari 0');
                return false;
            }
        });
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>

<?php
// Helper functions
function getKategoriIcon($kategori) {
    $icons = [
        'Semua' => 'list',
        'Kertas' => 'file-alt',
        'Plastik' => 'bottle-water',
        'Organik' => 'leaf',
        'Anorganik' => 'cube',
        'Limbah Cair' => 'tint',
        'B3' => 'exclamation-triangle'
    ];
    return $icons[$kategori] ?? 'circle';
}

function countByKategori($data, $kategori) {
    if ($kategori === 'Semua') {
        return count($data);
    }
    
    return count(array_filter($data, function($item) use ($kategori) {
        return $item['jenis_sampah'] === $kategori;
    }));
}

function getBadgeClass($jenisSampah) {
    $classes = [
        'Kertas' => 'kertas',
        'Plastik' => 'plastik',
        'Organik' => 'organik',
        'Anorganik' => 'anorganik',
        'Limbah Cair' => 'limbah',
        'B3' => 'b3'
    ];
    return $classes[$jenisSampah] ?? 'secondary';
}
?>