<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Analisis Populasi & Aset Kendaraan' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/dashboard.css') ?>" rel="stylesheet">
    <style>
        .main-content {
            margin-left: 280px;
            padding: 25px 30px;
            min-height: 100vh;
            width: calc(100% - 280px);
            background: #f4f6f9;
        }
        
        .page-header {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .page-header h1 {
            font-size: 26px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        .card-header {
            padding: 18px 25px;
            font-weight: 600;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .stat-card h3 {
            font-size: 36px;
            font-weight: 700;
            margin: 10px 0;
        }
        
        .stat-card p {
            font-size: 14px;
            opacity: 0.9;
            margin: 0;
        }
        
        .stat-card i {
            font-size: 48px;
            opacity: 0.3;
            position: absolute;
            right: 20px;
            top: 20px;
        }
        
        .ratio-display {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
        }
        
        .ratio-display h2 {
            font-size: 48px;
            font-weight: 700;
            margin: 10px 0;
        }
        
        .ratio-display p {
            font-size: 16px;
            margin: 0;
        }
        
        .info-box {
            background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
            color: #0c5460;
            border-left: 4px solid #17a2b8;
            padding: 15px 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <h1><i class="fas fa-users"></i> Analisis Populasi & Aset Kendaraan (TR 1 & TR 4)</h1>
                <p>Kelola data populasi kampus dan hitung rasio kendaraan untuk UI GreenMetric</p>
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
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); position: relative;">
                        <i class="fas fa-user-graduate"></i>
                        <p>Total Mahasiswa</p>
                        <h3><?= number_format($populasi['jumlah_mahasiswa'] ?? 0) ?></h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); position: relative;">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <p>Total Dosen</p>
                        <h3><?= number_format($populasi['jumlah_dosen'] ?? 0) ?></h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); position: relative;">
                        <i class="fas fa-user-tie"></i>
                        <p>Total Staf</p>
                        <h3><?= number_format($populasi['jumlah_staf'] ?? 0) ?></h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); position: relative;">
                        <i class="fas fa-users"></i>
                        <p>Total Populasi</p>
                        <h3><?= number_format($total_populasi) ?></h3>
                    </div>
                </div>
            </div>

            <!-- Form Input Populasi -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3><i class="fas fa-edit"></i> Input Data Populasi Kampus</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= base_url('/admin-pusat/transportation/simpan-populasi') ?>">
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="jumlah_mahasiswa" class="form-label">
                                    <i class="fas fa-user-graduate"></i> Jumlah Mahasiswa
                                </label>
                                <input type="number" class="form-control" id="jumlah_mahasiswa" 
                                       name="jumlah_mahasiswa" value="<?= $populasi['jumlah_mahasiswa'] ?? '' ?>" 
                                       placeholder="Contoh: 15000" required>
                            </div>
                            
                            <div class="col-md-3">
                                <label for="jumlah_dosen" class="form-label">
                                    <i class="fas fa-chalkboard-teacher"></i> Jumlah Dosen
                                </label>
                                <input type="number" class="form-control" id="jumlah_dosen" 
                                       name="jumlah_dosen" value="<?= $populasi['jumlah_dosen'] ?? '' ?>" 
                                       placeholder="Contoh: 500" required>
                            </div>
                            
                            <div class="col-md-3">
                                <label for="jumlah_staf" class="form-label">
                                    <i class="fas fa-user-tie"></i> Jumlah Staf
                                </label>
                                <input type="number" class="form-control" id="jumlah_staf" 
                                       name="jumlah_staf" value="<?= $populasi['jumlah_staf'] ?? '' ?>" 
                                       placeholder="Contoh: 300" required>
                            </div>
                            
                            <div class="col-md-3">
                                <label for="tahun" class="form-label">
                                    <i class="fas fa-calendar"></i> Tahun Data
                                </label>
                                <select class="form-select" id="tahun" name="tahun" required>
                                    <?php 
                                    $currentYear = date('Y');
                                    for ($i = $currentYear - 2; $i <= $currentYear + 1; $i++): 
                                    ?>
                                        <option value="<?= $i ?>" <?= (isset($populasi['tahun']) && $populasi['tahun'] == $i) || $i == $currentYear ? 'selected' : '' ?>>
                                            <?= $i ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Simpan Data Populasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Rasio Kendaraan -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h3><i class="fas fa-car"></i> Data Kendaraan Kampus</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Total Kendaraan Terdaftar:</strong></td>
                                    <td class="text-end"><h4><?= number_format($total_kendaraan) ?> Unit</h4></td>
                                </tr>
                                <tr>
                                    <td><strong>Kendaraan ZEV:</strong></td>
                                    <td class="text-end"><h5 class="text-success"><?= number_format($total_zev) ?> Unit</h5></td>
                                </tr>
                                <tr>
                                    <td><strong>Kendaraan Non-ZEV:</strong></td>
                                    <td class="text-end"><h5 class="text-muted"><?= number_format($total_kendaraan - $total_zev) ?> Unit</h5></td>
                                </tr>
                            </table>
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> Data diambil dari input Security
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="ratio-display">
                        <p><i class="fas fa-calculator"></i> Rasio Kendaraan per Populasi</p>
                        <h2><?= $rasio_kendaraan ?></h2>
                        <p>Kendaraan per 1000 Orang</p>
                        <hr style="border-color: rgba(255,255,255,0.3);">
                        <small>
                            Formula: (<?= number_format($total_kendaraan) ?> kendaraan / <?= number_format($total_populasi) ?> populasi) × 1000
                        </small>
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="info-box">
                <i class="fas fa-info-circle"></i>
                <strong>Catatan UI GreenMetric:</strong>
                <ul class="mb-0 mt-2">
                    <li><strong>TR 1:</strong> Rasio kendaraan pribadi per total populasi kampus (semakin rendah semakin baik)</li>
                    <li><strong>TR 4:</strong> Rasio kendaraan Zero Emission Vehicle (ZEV) terhadap total kendaraan (semakin tinggi semakin baik)</li>
                    <li>Data populasi harus diperbarui setiap tahun akademik</li>
                    <li>Data kendaraan diambil otomatis dari input Security</li>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-dismiss alerts
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
