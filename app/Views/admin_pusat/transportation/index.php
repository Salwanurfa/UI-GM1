<?php
/**
 * Admin Pusat - Manajemen Data Kendaraan Kampus
 * Modern Dashboard with Proper Grid Structure
 */
$title = $title ?? 'Manajemen Data Kendaraan';
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
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        body {
            background: #f8f9fc;
            color: #5a5c69;
        }

        /* ===== PAGE HEADER ===== */
        .page-header {
            margin-bottom: 1.5rem;
        }

        .page-header h4 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            color: #858796;
            font-size: 0.95rem;
            margin: 0;
        }

        /* ===== STATISTICS CARDS (GRADIENT) ===== */
        .stat-card {
            background: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border-left: 4px solid;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(30%, -30%);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.25);
        }

        .stat-card.primary {
            border-left-color: #4e73df;
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
        }

        .stat-card.success {
            border-left-color: #1cc88a;
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
            color: white;
        }

        .stat-card.warning {
            border-left-color: #f6c23e;
            background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
            color: white;
        }

        .stat-card.info {
            border-left-color: #36b9cc;
            background: linear-gradient(135deg, #36b9cc 0%, #258391 100%);
            color: white;
        }

        .stat-card-body {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 1;
        }

        .stat-content h3 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0 0 0.25rem 0;
        }

        .stat-content p {
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
            opacity: 0.9;
        }

        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.3;
        }

        /* ===== CARDS ===== */
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
        }

        .card-header {
            background: linear-gradient(135deg, #f8f9fc 0%, #e9ecef 100%);
            border-bottom: 1px solid #e3e6f0;
            padding: 1.25rem 1.5rem;
            border-radius: 0.5rem 0.5rem 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h5 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 700;
            color: #2c3e50;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* ===== FILTER SECTION ===== */
        .filter-card {
            background: #f8f9fc;
            border: 1px solid #e3e6f0;
            border-radius: 0.5rem;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .filter-card .form-label {
            font-weight: 600;
            font-size: 0.875rem;
            color: #5a5c69;
            margin-bottom: 0.5rem;
        }

        .filter-card .form-select,
        .filter-card .form-control {
            border-radius: 0.35rem;
            border: 1px solid #d1d3e2;
            font-size: 0.875rem;
            padding: 0.5rem 0.75rem;
        }

        .filter-card .form-select:focus,
        .filter-card .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        /* ===== BUTTONS ===== */
        .btn-modern {
            border-radius: 0.35rem;
            padding: 0.5rem 1.25rem;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: none;
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.15);
        }

        .btn-primary-gradient {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
        }

        .btn-success-gradient {
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
            color: white;
        }

        .btn-danger-gradient {
            background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%);
            color: white;
        }

        .btn-warning-gradient {
            background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
            color: white;
        }

        /* ===== TABLE MODERN ===== */
        .table-modern {
            font-size: 0.875rem;
        }

        .table-modern thead th {
            background: linear-gradient(135deg, #f8f9fc 0%, #e9ecef 100%);
            border-bottom: 2px solid #e3e6f0;
            padding: 1rem 0.75rem;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #5a5c69;
            white-space: nowrap;
            vertical-align: middle;
        }

        .table-modern tbody tr {
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.2s ease;
        }

        .table-modern tbody tr:hover {
            background: #f8f9fc;
        }

        .table-modern tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            border: none;
        }

        /* ===== BADGES (PASTEL) ===== */
        .badge-pastel {
            padding: 0.5rem 0.875rem;
            border-radius: 1rem;
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 0.3px;
        }

        .badge-motor {
            background: #e3f2fd;
            color: #1565c0;
        }

        .badge-motor-listrik {
            background: #e0f2f1;
            color: #00695c;
        }

        .badge-mobil {
            background: #f3e5f5;
            color: #6a1b9a;
        }

        .badge-bus {
            background: #fff3e0;
            color: #e65100;
        }

        .badge-sepeda {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .badge-kendaraan-umum {
            background: #fce4ec;
            color: #c2185b;
        }

        /* ===== FUEL ICONS ===== */
        .fuel-icon {
            width: 20px;
            height: 20px;
            margin-right: 0.5rem;
            vertical-align: middle;
        }

        /* ===== ACTION BUTTONS IN TABLE ===== */
        .btn-action {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
            border-radius: 0.25rem;
            font-weight: 600;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-action:hover {
            transform: scale(1.05);
        }

        .btn-edit {
            background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
            color: white;
        }

        .btn-delete {
            background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%);
            color: white;
        }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }

        .empty-state i {
            font-size: 4rem;
            color: #d1d3e2;
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: #858796;
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="container-fluid">
                    
                    <!-- Page Header -->
                    <div class="page-header">
                        <h4><i class="fas fa-car-side me-2"></i>Manajemen Data Kendaraan Kampus</h4>
                        <p>Kelola dan monitor seluruh data kendaraan yang terdaftar di kampus</p>
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

                    <!-- Statistics Cards Row -->
                    <div class="row mb-4">
                        <div class="col-12 col-sm-6 col-lg-3 mb-4">
                            <div class="stat-card primary">
                                <div class="stat-card-body">
                                    <div class="stat-content">
                                        <h3><?= number_format($summary_stats['total_vehicles'] ?? 0) ?></h3>
                                        <p>Total Kendaraan</p>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="fas fa-car-side"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3 mb-4">
                            <div class="stat-card success">
                                <div class="stat-card-body">
                                    <div class="stat-content">
                                        <h3><?= number_format($summary_stats['total_zev'] ?? 0) ?></h3>
                                        <p>Zero Emission (ZEV)</p>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="fas fa-bolt"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3 mb-4">
                            <div class="stat-card warning">
                                <div class="stat-card-body">
                                    <div class="stat-content">
                                        <h3><?= number_format($summary_stats['total_entries'] ?? 0) ?></h3>
                                        <p>Total Data Entry</p>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="fas fa-database"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3 mb-4">
                            <div class="stat-card info">
                                <div class="stat-card-body">
                                    <div class="stat-content">
                                        <h3><?= number_format($summary_stats['total_shuttle'] ?? 0) ?></h3>
                                        <p>Shuttle Kampus</p>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="fas fa-bus"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Card -->
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-filter me-2"></i>Filter Data</h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="<?= base_url('admin-pusat/transportation') ?>">
                                <div class="row g-3 align-items-end">
                                    <div class="col-12 col-md-6 col-lg-3">
                                        <label class="form-label">Kategori Kendaraan</label>
                                        <select name="kategori" class="form-select">
                                            <option value="">Semua Kategori</option>
                                            <!-- New Official Categories (UU No. 22 Tahun 2009) -->
                                            <option value="Sepeda Motor (Kategori L)">Sepeda Motor (Kategori L)</option>
                                            <option value="Mobil Penumpang (Kategori M1)">Mobil Penumpang (Kategori M1)</option>
                                            <option value="Mobil Bus (Kategori M2/M3)">Mobil Bus (Kategori M2/M3)</option>
                                            <option value="Kendaraan Tidak Bermotor (Sepeda)">Kendaraan Tidak Bermotor (Sepeda)</option>
                                            <option value="Kendaraan Bermotor Listrik (KBL)">Kendaraan Bermotor Listrik (KBL)</option>
                                            <!-- Old Categories (Backward Compatibility) -->
                                            <option value="Roda Dua">Roda Dua (Lama)</option>
                                            <option value="Motor Listrik">Motor Listrik (Lama)</option>
                                            <option value="Roda Empat">Roda Empat (Lama)</option>
                                            <option value="Bus">Bus (Lama)</option>
                                            <option value="Sepeda">Sepeda (Lama)</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-2">
                                        <label class="form-label">Periode</label>
                                        <select name="periode" class="form-select">
                                            <option value="">Semua Periode</option>
                                            <option value="Harian">Harian</option>
                                            <option value="Mingguan (Back-up)">Mingguan</option>
                                            <option value="Bulanan (Back-up)">Bulanan</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-4 col-lg-2">
                                        <label class="form-label">Bulan</label>
                                        <select name="bulan" class="form-select">
                                            <option value="">Semua Bulan</option>
                                            <option value="Januari">Januari</option>
                                            <option value="Februari">Februari</option>
                                            <option value="Maret">Maret</option>
                                            <option value="April">April</option>
                                            <option value="Mei">Mei</option>
                                            <option value="Juni">Juni</option>
                                            <option value="Juli">Juli</option>
                                            <option value="Agustus">Agustus</option>
                                            <option value="September">September</option>
                                            <option value="Oktober">Oktober</option>
                                            <option value="November">November</option>
                                            <option value="Desember">Desember</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-4 col-lg-2">
                                        <label class="form-label">Tahun</label>
                                        <select name="tahun" class="form-select">
                                            <option value="">Semua Tahun</option>
                                            <?php for($y = 2024; $y <= 2030; $y++): ?>
                                                <option value="<?= $y ?>"><?= $y ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-4 col-lg-3">
                                        <button type="submit" class="btn btn-primary-gradient btn-modern w-100">
                                            <i class="fas fa-filter me-1"></i> Terapkan Filter
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Management Table Card -->
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-table me-2"></i>Daftar Kendaraan Terdaftar</h5>
                            <div class="d-flex gap-2">
                                <a href="<?= base_url('admin-pusat/transportation/export-excel') ?>" class="btn btn-success-gradient btn-modern btn-sm">
                                    <i class="fas fa-file-excel me-1"></i> Export Excel
                                </a>
                                <a href="<?= base_url('admin-pusat/transportation/export-pdf') ?>" class="btn btn-danger-gradient btn-modern btn-sm" target="_blank">
                                    <i class="fas fa-file-pdf me-1"></i> Export PDF
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($all_records)): ?>
                                <div class="table-responsive">
                                    <table id="managementTable" class="table table-modern table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px;">No</th>
                                                <th>Tanggal</th>
                                                <th>Periode</th>
                                                <th>Kategori</th>
                                                <th>Bahan Bakar</th>
                                                <th class="text-center">Jumlah</th>
                                                <th>Petugas</th>
                                                <th class="text-center" style="width: 120px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1; foreach ($all_records as $record): ?>
                                                <tr>
                                                    <td class="text-center"><strong><?= $no++ ?></strong></td>
                                                    <td>
                                                        <?php if ($record['periode'] === 'Harian' && !empty($record['tanggal_pencatatan'])): ?>
                                                            <small><?= date('d/m/Y', strtotime($record['tanggal_pencatatan'])) ?></small>
                                                        <?php elseif ($record['periode'] === 'Mingguan (Back-up)'): ?>
                                                            <small>
                                                                <?= !empty($record['tanggal_mulai']) ? date('d/m/Y', strtotime($record['tanggal_mulai'])) : '-' ?>
                                                                <br>→ <?= !empty($record['tanggal_selesai']) ? date('d/m/Y', strtotime($record['tanggal_selesai'])) : '-' ?>
                                                            </small>
                                                        <?php elseif ($record['periode'] === 'Bulanan (Back-up)'): ?>
                                                            <small><?= $record['bulan'] ?? '-' ?> <?= $record['tahun'] ?? '' ?></small>
                                                        <?php else: ?>
                                                            <small>-</small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $periodeBadge = match($record['periode']) {
                                                            'Harian' => '<span class="badge bg-primary">Harian</span>',
                                                            'Mingguan (Back-up)' => '<span class="badge bg-info">Mingguan</span>',
                                                            'Bulanan (Back-up)' => '<span class="badge bg-warning">Bulanan</span>',
                                                            default => '<span class="badge bg-secondary">' . esc($record['periode']) . '</span>'
                                                        };
                                                        echo $periodeBadge;
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $kategori = strtolower($record['kategori_kendaraan']);
                                                        $badgeClass = 'badge-mobil'; // default
                                                        
                                                        // New official categories (UU No. 22 Tahun 2009)
                                                        if (strpos($kategori, 'sepeda motor') !== false || strpos($kategori, 'kategori l') !== false) {
                                                            $badgeClass = 'badge-motor';
                                                        } elseif (strpos($kategori, 'mobil penumpang') !== false || strpos($kategori, 'kategori m1') !== false) {
                                                            $badgeClass = 'badge-mobil';
                                                        } elseif (strpos($kategori, 'mobil bus') !== false || strpos($kategori, 'kategori m2') !== false || strpos($kategori, 'm3') !== false) {
                                                            $badgeClass = 'badge-bus';
                                                        } elseif (strpos($kategori, 'sepeda listrik') !== false) {
                                                            $badgeClass = 'badge-motor-listrik';
                                                        } elseif (strpos($kategori, 'tidak bermotor') !== false || strpos($kategori, 'sepeda') !== false) {
                                                            $badgeClass = 'badge-sepeda';
                                                        } elseif (strpos($kategori, 'bermotor listrik') !== false || strpos($kategori, 'kbl') !== false) {
                                                            $badgeClass = 'badge-motor-listrik';
                                                        }
                                                        // Backward compatibility for old categories
                                                        elseif (strpos($kategori, 'motor listrik') !== false) {
                                                            $badgeClass = 'badge-motor-listrik';
                                                        } elseif (strpos($kategori, 'roda dua') !== false) {
                                                            $badgeClass = 'badge-motor';
                                                        } elseif (strpos($kategori, 'roda empat') !== false) {
                                                            $badgeClass = 'badge-mobil';
                                                        } elseif (strpos($kategori, 'bus') !== false) {
                                                            $badgeClass = 'badge-bus';
                                                        } elseif (strpos($kategori, 'kendaraan umum') !== false) {
                                                            $badgeClass = 'badge-kendaraan-umum';
                                                        }
                                                        ?>
                                                        <span class="badge-pastel <?= $badgeClass ?>">
                                                            <?= esc($record['kategori_kendaraan']) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $fuelIcon = match($record['jenis_bahan_bakar']) {
                                                            'Listrik' => '<i class="fas fa-bolt text-warning fuel-icon"></i>',
                                                            'Bensin' => '<i class="fas fa-gas-pump text-danger fuel-icon"></i>',
                                                            'Diesel' => '<i class="fas fa-gas-pump text-dark fuel-icon"></i>',
                                                            'Non-BBM' => '<i class="fas fa-bicycle text-success fuel-icon"></i>',
                                                            default => '<i class="fas fa-question-circle text-muted fuel-icon"></i>'
                                                        };
                                                        ?>
                                                        <?= $fuelIcon ?>
                                                        <small><?= esc($record['jenis_bahan_bakar']) ?></small>
                                                    </td>
                                                    <td class="text-center">
                                                        <strong class="text-primary"><?= number_format($record['jumlah_total']) ?></strong>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">
                                                            <i class="fas fa-user me-1"></i>
                                                            <?= esc($record['petugas_nama'] ?? 'Unknown') ?>
                                                        </small>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="d-flex gap-1 justify-content-center">
                                                            <a href="<?= base_url('admin-pusat/transportation/edit/' . $record['id']) ?>" 
                                                               class="btn btn-edit btn-action" 
                                                               title="Edit Data">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <button type="button" 
                                                                    class="btn btn-delete btn-action" 
                                                                    onclick="confirmDelete(<?= $record['id'] ?>, '<?= esc($record['kategori_kendaraan']) ?>')" 
                                                                    title="Hapus Data">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <p>Belum ada data kendaraan terdaftar</p>
                                    <small class="text-muted">Data akan muncul setelah petugas security melakukan input</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Initialize DataTable
        $(document).ready(function() {
            $('#managementTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
                },
                order: [[1, 'desc']], // Sort by date
                pageLength: 25,
                responsive: true,
                columnDefs: [
                    { orderable: false, targets: [7] } // Disable sorting on action column
                ]
            });
        });

        // Confirm delete function
        function confirmDelete(id, kategori) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Yakin ingin menghapus data "${kategori}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= base_url('admin-pusat/transportation/delete/') ?>' + id;
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
