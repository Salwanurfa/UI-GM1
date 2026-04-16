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
        
        .dashboard-header {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin-bottom: 15px;
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .stats-label {
            color: #7f8c8d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .quick-actions {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .action-btn {
            display: block;
            width: 100%;
            padding: 20px;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            border: none;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(30, 60, 114, 0.3);
            color: white;
        }
        
        .action-btn i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        .welcome-text {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .time-info {
            color: #7f8c8d;
            font-size: 0.9rem;
        }
        
        /* History Table Styles - Clean Bootstrap Approach */
        .card {
            border-radius: 12px;
            border: none;
        }
        
        .table-primary {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
        }
        
        .table-primary th {
            border: none;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 15px 12px;
            vertical-align: middle;
        }
        
        .table tbody tr {
            transition: all 0.2s ease;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .table td {
            padding: 12px;
            vertical-align: middle;
            font-size: 0.9rem;
        }
        
        /* DateTime Display */
        .datetime-compact {
            line-height: 1.2;
        }
        
        /* Vehicle Badges */
        .badge-vehicle {
            display: inline-flex;
            align-items: center;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .badge-motor { background: #d4edda; color: #155724; }
        .badge-mobil { background: #d1ecf1; color: #0c5460; }
        .badge-bus { background: #fff3cd; color: #856404; }
        .badge-truk { background: #f8d7da; color: #721c24; }
        .badge-sepeda { background: #e2e3f1; color: #383d41; }
        
        /* New Transport Stats Badges */
        .badge-kategori {
            display: inline-flex;
            align-items: center;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .badge-roda-dua { background: #d4edda; color: #155724; }
        .badge-roda-empat { background: #d1ecf1; color: #0c5460; }
        .badge-sepeda { background: #e2e3f1; color: #383d41; }
        .badge-kendaraan-umum { background: #fff3cd; color: #856404; }
        
        .badge-bahan-bakar {
            display: inline-flex;
            align-items: center;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .badge-bensin { background: #f8d7da; color: #721c24; }
        .badge-diesel { background: #d6d8db; color: #383d41; }
        .badge-listrik { background: #fff3cd; color: #856404; }
        .badge-non-bbm { background: #d4edda; color: #155724; }
        
        .periode-info {
            line-height: 1.3;
        }
        
        .jumlah-total {
            font-weight: 600;
            font-size: 1.1rem;
            color: #2c3e50;
        }
        
        /* Status Badges */
        .badge-status {
            display: inline-flex;
            align-items: center;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .badge-status-masuk {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-status-keluar {
            background: #f8d7da;
            color: #721c24;
        }
        
        /* Plat Number */
        .plat-number {
            background: #f8f9fa;
            color: #495057;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85rem;
        }
        
        /* Action Buttons */
        .action-buttons-clean {
            display: flex;
            gap: 5px;
            justify-content: center;
        }
        
        .action-buttons-clean .btn {
            padding: 4px 8px;
            font-size: 0.8rem;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .d-flex.justify-content-between {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start !important;
            }
            
            .export-button-group {
                width: 100%;
            }
            
            .export-button-group .btn {
                flex: 1;
            }
            
            .table {
                font-size: 0.8rem;
            }
            
            .action-buttons-clean {
                gap: 3px;
            }
            
            .action-buttons-clean .btn {
                padding: 3px 6px;
                font-size: 0.75rem;
            }
        }
        
        /* New Layout Styles */
        .info-system-landscape {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .section-title {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        .quick-actions-section .action-btn {
            display: block;
            width: 100%;
            padding: 15px 20px;
            margin-bottom: 12px;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            border: none;
            font-size: 0.9rem;
        }
        
        .quick-actions-section .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(30, 60, 114, 0.3);
            color: white;
        }
        
        .info-card-small {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 12px;
            border-left: 4px solid #17a2b8;
        }
        
        .info-card-small i {
            font-size: 1.2rem;
            margin-top: 2px;
        }
        
        .info-content {
            flex: 1;
            font-size: 0.9rem;
            line-height: 1.4;
        }
        
        /* Export Buttons */
        .export-buttons {
            display: flex;
            gap: 10px;
            flex-shrink: 0;
        }
        
        .btn-export-pdf {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        .btn-export-pdf:hover {
            background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
            color: white;
        }
        
        .btn-export-excel {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        .btn-export-excel:hover {
            background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
            color: white;
        }
        
        .btn-export-pdf i,
        .btn-export-excel i {
            margin-right: 5px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .table-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start !important;
            }
            
            .export-buttons {
                width: 100%;
                justify-content: flex-start;
            }
            
            .btn-export-pdf,
            .btn-export-excel {
                flex: 1;
                text-align: center;
            }
            
            .info-system-landscape .row {
                flex-direction: column;
            }
            
            .quick-actions-section,
            .system-info-section {
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="welcome-text">
                        <i class="fas fa-shield-alt text-warning"></i>
                        Selamat Datang, <?= esc($user['nama_lengkap']) ?>
                    </h5>
                    <p class="time-info">
                        <i class="fas fa-clock"></i>
                        <?= date('l, d F Y - H:i') ?> WIB
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="badge bg-warning text-dark fs-6 px-3 py-2">
                        <i class="fas fa-user-shield"></i>
                        Security Officer
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-icon" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stats-number"><?= $stats['today_entries'] ?></div>
                    <div class="stats-label">Input Hari Ini</div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-icon" style="background: linear-gradient(135deg, #3498db, #5dade2);">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                    <div class="stats-number"><?= $stats['week_entries'] ?></div>
                    <div class="stats-label">Input Minggu Ini</div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-icon" style="background: linear-gradient(135deg, #9b59b6, #bb6bd9);">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stats-number"><?= $stats['month_entries'] ?></div>
                    <div class="stats-label">Input Bulan Ini</div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-icon" style="background: linear-gradient(135deg, #e67e22, #f39c12);">
                        <i class="fas fa-car"></i>
                    </div>
                    <div class="stats-number"><?= $stats['total_vehicles_today'] ?></div>
                    <div class="stats-label">Total Kendaraan Hari Ini</div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & System Information -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="info-system-landscape">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="quick-actions-section">
                                <h4 class="section-title">
                                    <i class="fas fa-bolt text-warning"></i>
                                    Aksi Cepat
                                </h4>
                                
                                <a href="<?= base_url('/security/transportation') ?>" class="action-btn">
                                    <i class="fas fa-car"></i>
                                    Input Data Transportasi
                                </a>
                                
                                <a href="<?= base_url('/security/profile') ?>" class="action-btn">
                                    <i class="fas fa-user-circle"></i>
                                    Kelola Profil Akun
                                </a>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="system-info-section">
                                <h4 class="section-title">
                                    <i class="fas fa-info-circle text-info"></i>
                                    Informasi Sistem
                                </h4>
                                
                                <div class="info-card-small">
                                    <i class="fas fa-lightbulb text-warning"></i>
                                    <div class="info-content">
                                        <strong>Tips:</strong> Pastikan untuk mencatat setiap kendaraan yang masuk dan keluar kampus dengan akurat.
                                    </div>
                                </div>
                                
                                <div class="info-card-small">
                                    <i class="fas fa-exclamation-triangle text-warning"></i>
                                    <div class="info-content">
                                        <strong>Penting:</strong> Data transportasi berkontribusi pada penilaian UI GreenMetric POLBAN.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Entries History -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <!-- Header with Export Buttons -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4 class="mb-1">
                                    <i class="fas fa-history text-primary"></i>
                                    Riwayat Input Terbaru
                                </h4>
                                <p class="text-muted mb-0 small">10 data input terakhir Anda</p>
                            </div>
                            <div class="export-button-group">
                                <a href="<?= base_url('/security/dashboard/export-pdf') ?>" 
                                   class="btn btn-danger btn-sm me-2" 
                                   target="_blank"
                                   title="Export ke PDF">
                                    <i class="fas fa-file-pdf"></i>
                                    PDF
                                </a>
                                <a href="<?= base_url('/security/dashboard/export-excel') ?>" 
                                   class="btn btn-success btn-sm"
                                   title="Export ke Excel">
                                    <i class="fas fa-file-excel"></i>
                                    Excel
                                </a>
                            </div>
                        </div>
                        
                        <?php if (!empty($recent_entries)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-primary">
                                        <tr>
                                            <th style="width: 5%;" class="text-center">No</th>
                                            <th style="width: 25%;">Periode</th>
                                            <th style="width: 20%;">Kategori Kendaraan</th>
                                            <th style="width: 20%;">Jenis Bahan Bakar</th>
                                            <th style="width: 15%;" class="text-center">Jumlah Total</th>
                                            <th style="width: 15%;" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_entries as $index => $entry): ?>
                                            <tr style="vertical-align: middle;">
                                                <td class="text-center"><?= $index + 1 ?></td>
                                                <td>
                                                    <div class="periode-info">
                                                        <div class="fw-bold text-dark"><?= esc($entry['periode']) ?></div>
                                                        <small class="text-muted"><?= format_datetime_wib($entry['created_at']) ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        <span class="badge-kategori badge-<?= strtolower(str_replace(' ', '-', $entry['kategori_kendaraan'])) ?>">
                                                            <?php
                                                            $kategoriIcons = [
                                                                'Roda Dua' => 'fa-motorcycle',
                                                                'Roda Empat' => 'fa-car',
                                                                'Sepeda' => 'fa-bicycle',
                                                                'Kendaraan Umum' => 'fa-bus'
                                                            ];
                                                            ?>
                                                            <i class="fas <?= $kategoriIcons[$entry['kategori_kendaraan']] ?? 'fa-car' ?> me-1"></i>
                                                            <?= esc($entry['kategori_kendaraan']) ?>
                                                        </span>
                                                        <?php if ($entry['is_zev']): ?>
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-leaf me-1"></i>ZEV
                                                            </span>
                                                        <?php endif; ?>
                                                        <?php if ($entry['is_shuttle']): ?>
                                                            <span class="badge bg-info">
                                                                <i class="fas fa-bus me-1"></i>Shuttle
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge-bahan-bakar badge-<?= strtolower($entry['jenis_bahan_bakar']) ?>">
                                                        <?php
                                                        $bahanBakarIcons = [
                                                            'Bensin' => 'fa-gas-pump',
                                                            'Diesel' => 'fa-oil-can',
                                                            'Listrik' => 'fa-bolt',
                                                            'Non-BBM' => 'fa-leaf'
                                                        ];
                                                        ?>
                                                        <i class="fas <?= $bahanBakarIcons[$entry['jenis_bahan_bakar']] ?? 'fa-gas-pump' ?> me-1"></i>
                                                        <?= esc($entry['jenis_bahan_bakar']) ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="jumlah-total"><?= number_format($entry['jumlah_total']) ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="action-buttons-clean">
                                                        <a href="<?= base_url('/security/transportation?edit=' . $entry['id']) ?>" 
                                                           class="btn btn-outline-primary btn-sm" 
                                                           title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" 
                                                                class="btn btn-outline-danger btn-sm" 
                                                                onclick="confirmDelete(<?= $entry['id'] ?>)"
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
                        <?php else: ?>
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fas fa-inbox fa-3x text-muted"></i>
                                </div>
                                <h5 class="text-muted">Belum ada riwayat input hari ini</h5>
                                <p class="text-muted">Mulai input data transportasi untuk melihat riwayat di sini</p>
                                <a href="<?= base_url('/security/transportation') ?>" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>
                                    Input Data Sekarang
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Form -->
        <form id="deleteForm" method="POST" style="display: none;">
            <?= csrf_field() ?>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Delete confirmation
        function confirmDelete(id) {
            if (confirm('Apakah Anda yakin ingin menghapus data transportasi ini?')) {
                const form = document.getElementById('deleteForm');
                form.action = '<?= base_url('/security/dashboard/delete/') ?>' + id;
                form.submit();
            }
        }
        
        // Auto refresh stats every 30 seconds
        setInterval(function() {
            // You can add AJAX call here to refresh stats without page reload
            // For now, we'll keep it simple
        }, 30000);
    </script>
</body>
</html>