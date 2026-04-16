<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - UI GreenMetric POLBAN</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background: #f4f6f9;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 30px;
            min-height: 100vh;
        }
        
        .profile-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .profile-header {
            text-align: center;
            border-bottom: 2px solid #1e3c72;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: white;
            margin: 0 auto 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #1e3c72;
            box-shadow: 0 0 0 0.2rem rgba(30, 60, 114, 0.25);
        }
        
        .btn-update {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(30, 60, 114, 0.3);
            color: white;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .info-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .password-section {
            background: #fff3cd;
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
            border-left: 4px solid #ffc107;
        }
    </style>
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <?php
$title = $title ?? 'Manajemen Referensi Kendaraan';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/dashboard.css') ?>" rel="stylesheet">
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header mb-4 ms-3">
            <h4><i class="fas fa-database me-2"></i>Manajemen Transportasi Kendaraan</h4>
            <p>Kelola kategori kendaraan dan jenis bahan bakar untuk sistem transportasi</p>
        </div>

        <!-- Success/Error Messages -->
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

        <!-- Main Content -->
        <div class="container-fluid px-4">
            <!-- Row: Kategori Kendaraan (Kiri) & Jenis Bahan Bakar (Kanan) -->
            <div class="row g-4">
                <!-- Kolom Kiri - Kategori Kendaraan -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-car me-2 text-primary"></i>Kategori Kendaraan
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Form Tambah Kategori -->
                            <form action="<?= base_url('/admin-pusat/reference/add-category') ?>" method="POST" class="mb-4">
                                <?= csrf_field() ?>
                                <div class="mb-3">
                                    <label for="nama_kategori" class="form-label fw-semibold">
                                        <i class="fas fa-tag me-1 text-muted"></i>Nama Kategori
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nama_kategori" 
                                           name="nama_kategori" 
                                           placeholder="Contoh: Roda Dua, Roda Empat" 
                                           required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus me-1"></i>Tambah Kategori
                                </button>
                            </form>

                            <!-- Tabel Daftar Kategori -->
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" width="15%">#</th>
                                            <th>Nama Kategori</th>
                                            <th class="text-center" width="20%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($categories) && !empty($categories)): ?>
                                            <?php foreach ($categories as $index => $category): ?>
                                                <tr>
                                                    <td class="text-center">
                                                        <span class="badge bg-light text-dark"><?= $index + 1 ?></span>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-car text-primary me-2"></i>
                                                            <?= esc($category['nama_kategori']) ?>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="<?= base_url('/admin-pusat/reference/delete-category/' . $category['id']) ?>" 
                                                           class="btn btn-outline-danger btn-sm"
                                                           onclick="return confirm('Yakin ingin menghapus kategori ini?')"
                                                           title="Hapus">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-4">
                                                    <div class="d-flex flex-column align-items-center">
                                                        <i class="fas fa-inbox fs-1 text-muted mb-2"></i>
                                                        <span>Belum ada kategori kendaraan</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan - Jenis Bahan Bakar -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-gas-pump me-2 text-success"></i>Jenis Bahan Bakar
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Form Tambah Bahan Bakar -->
                            <form action="<?= base_url('/admin-pusat/reference/add-fuel') ?>" method="POST" class="mb-4">
                                <?= csrf_field() ?>
                                <div class="mb-3">
                                    <label for="nama_bahan_bakar" class="form-label fw-semibold">
                                        <i class="fas fa-oil-can me-1 text-muted"></i>Nama Bahan Bakar
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nama_bahan_bakar" 
                                           name="nama_bahan_bakar" 
                                           placeholder="Contoh: Bensin, Diesel, Listrik" 
                                           required>
                                </div>
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus me-1"></i>Tambah Bahan Bakar
                                </button>
                            </form>

                            <!-- Tabel Daftar Bahan Bakar -->
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" width="15%">#</th>
                                            <th>Nama Bahan Bakar</th>
                                            <th class="text-center" width="20%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($fuels) && !empty($fuels)): ?>
                                            <?php foreach ($fuels as $index => $fuel): ?>
                                                <tr>
                                                    <td class="text-center">
                                                        <span class="badge bg-light text-dark"><?= $index + 1 ?></span>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-gas-pump text-success me-2"></i>
                                                            <?= esc($fuel['nama_bahan_bakar']) ?>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="<?= base_url('/admin-pusat/reference/delete-fuel/' . $fuel['id']) ?>" 
                                                           class="btn btn-outline-danger btn-sm"
                                                           onclick="return confirm('Yakin ingin menghapus bahan bakar ini?')"
                                                           title="Hapus">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-4">
                                                    <div class="d-flex flex-column align-items-center">
                                                        <i class="fas fa-inbox fs-1 text-muted mb-2"></i>
                                                        <span>Belum ada jenis bahan bakar</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>