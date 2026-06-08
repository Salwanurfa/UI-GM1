<?php
$title = $title ?? 'Dashboard Security';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - UI GreenMetric POLBAN</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= base_url('assets/css/dashboard.css') ?>" rel="stylesheet">
    
    <style>
        /* ===== STATISTICS CARDS (SAMA DENGAN WASTE) ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            border-left: 4px solid;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .stat-card.primary { border-left-color: #007bff; }
        .stat-card.warning { border-left-color: #ffc107; }
        .stat-card.success { border-left-color: #28a745; }
        .stat-card.info { border-left-color: #17a2b8; }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            flex-shrink: 0;
        }

        .stat-card.primary .stat-icon { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-card.warning .stat-icon { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .stat-card.success .stat-icon { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .stat-card.info .stat-icon { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }

        .stat-content {
            flex: 1;
        }

        .stat-content h3 {
            font-size: 32px;
            font-weight: 700;
            margin: 0 0 5px 0;
            color: #2c3e50;
        }

        .stat-content p {
            margin: 0;
            color: #6c757d;
            font-size: 14px;
            font-weight: 500;
        }

        /* ===== ACTION BUTTONS (SAMA DENGAN WASTE) ===== */
        .action-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }

        .action-buttons .btn {
            padding: 12px 24px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .action-buttons .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* ===== TABLE STYLES (SAMA DENGAN WASTE) ===== */
        .card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .card-header {
            background: white;
            border-bottom: 2px solid #e9ecef;
            padding: 20px;
            border-radius: 12px 12px 0 0;
        }

        .card-header h5 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
        }

        .table-responsive {
            border-radius: 0 0 12px 12px;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
            padding: 15px;
            font-size: 14px;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            font-size: 14px;
        }

        /* ===== BADGE STYLES (PILL SEPERTI WASTE) ===== */
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
        }

        /* Badge untuk kategori kendaraan - warna variatif */
        .badge-roda-empat {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .badge-roda-dua {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }

        .badge-sepeda, .badge-non-bbm {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }

        .badge-zev {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .badge-shuttle {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
        }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 64px;
            color: #dee2e6;
            margin-bottom: 20px;
        }

        .empty-state p {
            color: #6c757d;
            font-size: 16px;
            margin-bottom: 20px;
        }

        /* ===== PAGE HEADER ===== */
        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .page-header p {
            color: #6c757d;
            font-size: 14px;
            margin: 0;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .action-buttons {
                flex-direction: column;
                width: 100%;
            }

            .action-buttons .btn {
                width: 100%;
            }

            .stat-card {
                padding: 20px;
            }

            .stat-icon {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }

            .stat-content h3 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fas fa-car-side me-2"></i>Dashboard Security</h1>
            <p>Monitoring dan Input Data Transportasi Kampus</p>
        </div>

        <!-- Flash Messages -->
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

        <!-- Statistics Cards (TOTAL AKUMULASI SEMUA WAKTU) -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-car-side"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($stats['total_vehicles_all_time'] ?? 0) ?></h3>
                    <p>Total Kendaraan (Semua Waktu)</p>
                </div>
            </div>
            
            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-car"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($stats['roda_empat'] ?? 0) ?></h3>
                    <p>Roda Empat (Akumulasi)</p>
                </div>
            </div>
            
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-motorcycle"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($stats['roda_dua'] ?? 0) ?></h3>
                    <p>Roda Dua (Akumulasi)</p>
                </div>
            </div>
            
            <div class="stat-card info">
                <div class="stat-icon">
                    <i class="fas fa-bicycle"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($stats['sepeda'] ?? 0) ?></h3>
                    <p>Sepeda/Non-BBM (Akumulasi)</p>
                </div>
            </div>
        </div>

        <!-- Progress Cards (Bulan Ini vs Hari Ini) -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-gradient text-white rounded-circle p-3">
                                    <i class="fas fa-calendar-alt fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-1">Total Bulan Ini</h5>
                                <h3 class="mb-0 text-primary"><?= number_format($stats['total_vehicles_month'] ?? 0) ?></h3>
                                <small class="text-muted">Kendaraan tercatat bulan <?= date('F Y') ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-success bg-gradient text-white rounded-circle p-3">
                                    <i class="fas fa-calendar-day fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-1">Total Hari Ini</h5>
                                <h3 class="mb-0 text-success"><?= number_format($stats['total_vehicles_today'] ?? 0) ?></h3>
                                <small class="text-muted">Kendaraan tercatat hari ini</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons (SAMA DENGAN WASTE) -->
        <div class="action-buttons">
            <a href="<?= base_url('security/transportation') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Data Kendaraan
            </a>
            <a href="<?= base_url('security/dashboard/export-excel') ?>" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            <a href="<?= base_url('security/dashboard/export-pdf') ?>" class="btn btn-danger" target="_blank">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>

        <!-- Data Terbaru Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-history me-2"></i>Data Terbaru</h5>
                <a href="<?= base_url('security/transportation') ?>" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-eye me-1"></i>Lihat Semua
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($recent_entries)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Periode</th>
                                    <th>Jenis Kendaraan</th>
                                    <th>Bahan Bakar</th>
                                    <th class="text-center">Jumlah</th>
                                    <th>Tanggal Input</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($recent_entries as $entry): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <small class="text-muted fw-bold"><?= esc($entry['periode']) ?></small>
                                        </td>
                                        <td>
                                            <?php
                                            // Tampilkan jenis kendaraan dari kategori_kendaraan (teks saja)
                                            // TIDAK ADA FALLBACK - langsung ambil dari kategori_kendaraan
                                            ?>
                                            <?= esc($entry['kategori_kendaraan'] ?? 'Tidak Diketahui') ?>
                                        </td>
                                        <td>
                                            <?php
                                            $fuelIcon = match($entry['jenis_bahan_bakar']) {
                                                'Listrik' => '<i class="fas fa-bolt text-success me-1"></i>',
                                                'Bensin' => '<i class="fas fa-gas-pump text-warning me-1"></i>',
                                                'Diesel' => '<i class="fas fa-gas-pump text-danger me-1"></i>',
                                                'Non-BBM' => '<i class="fas fa-bicycle text-info me-1"></i>',
                                                default => '<i class="fas fa-question-circle me-1"></i>'
                                            };
                                            ?>
                                            <?= $fuelIcon ?>
                                            <small><?= esc($entry['jenis_bahan_bakar']) ?></small>
                                        </td>
                                        <td class="text-center">
                                            <strong class="text-primary"><?= number_format($entry['jumlah_total']) ?></strong>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                <?= date('d/m/Y H:i', strtotime($entry['created_at'])) ?>
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="confirmDelete(<?= $entry['id'] ?>, '<?= esc($entry['periode']) ?>')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>Belum Ada Data</p>
                        <small class="text-muted">Mulai input data transportasi untuk melihat riwayat di sini</small>
                        <div class="mt-3">
                            <a href="<?= base_url('security/transportation') ?>" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-2"></i>Input Data Sekarang
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Confirm delete function
        function confirmDelete(id, periode) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Yakin ingin menghapus data "${periode}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= base_url('security/dashboard/delete/') ?>' + id;
                }
            });
        }

        // Auto-dismiss alerts after 5 seconds
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
