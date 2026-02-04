<?php
// Helper functions
if (!function_exists('formatNumber')) {
    function formatNumber($number) {
        return number_format($number, 2, ',', '.');
    }
}

if (!function_exists('formatCurrency')) {
    function formatCurrency($amount) {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Laporan Waste' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Mobile Responsive CSS -->
    <link href="<?= base_url('/css/mobile-responsive.css') ?>" rel="stylesheet">
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-chart-bar"></i> Laporan Data Sampah</h1>
            <p>Laporan lengkap data sampah yang sudah disetujui dan ditolak</p>
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

        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h3><i class="fas fa-filter"></i> Filter Laporan</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="<?= base_url('/admin-pusat/laporan-waste') ?>">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                       value="<?= $filters['start_date'] ?? '' ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                       value="<?= $filters['end_date'] ?? '' ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="unit_id" class="form-label">Unit</label>
                                <select class="form-select" id="unit_id" name="unit_id">
                                    <option value="">Semua Unit</option>
                                    <?php foreach ($units as $unit): ?>
                                    <option value="<?= $unit['id'] ?>" <?= ($filters['unit_id'] ?? '') == $unit['id'] ? 'selected' : '' ?>>
                                        <?= $unit['nama_unit'] ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                    <a href="<?= base_url('/admin-pusat/laporan-waste') ?>" class="btn btn-secondary">
                                        <i class="fas fa-redo"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5>Total Transaksi</h5>
                        <h2><?= $summary['total_transaksi'] ?? 0 ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5>Disetujui</h5>
                        <h2><?= $summary['total_disetujui'] ?? 0 ?></h2>
                        <small><?= formatNumber($summary['total_berat_disetujui'] ?? 0) ?> kg</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h5>Ditolak</h5>
                        <h2><?= $summary['total_ditolak'] ?? 0 ?></h2>
                        <small><?= formatNumber($summary['total_berat_ditolak'] ?? 0) ?> kg</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5>Total Nilai Disetujui</h5>
                        <h2><?= formatCurrency($summary['total_nilai_disetujui'] ?? 0) ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export Buttons -->
        <div class="mb-3">
            <div class="btn-group" role="group">
                <a href="<?= base_url('/admin-pusat/laporan-waste/export-excel?' . http_build_query($filters)) ?>" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export ke Excel
                </a>
                <a href="<?= base_url('/admin-pusat/laporan-waste/export-pdf?' . http_build_query($filters)) ?>" class="btn btn-danger" target="_blank">
                    <i class="fas fa-file-pdf"></i> Export ke PDF
                </a>
            </div>
        </div>

        <!-- Rekap per Jenis Sampah -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h3><i class="fas fa-recycle"></i> Rekap per Jenis Sampah</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($rekap_jenis)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Jenis Sampah</th>
                                <th>Total Transaksi</th>
                                <th>Disetujui</th>
                                <th>Ditolak</th>
                                <th>Berat Disetujui (kg)</th>
                                <th>Berat Ditolak (kg)</th>
                                <th>Nilai Disetujui</th>
                                <th>Nilai Ditolak</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rekap_jenis as $item): ?>
                            <tr>
                                <td><strong><?= $item['jenis_sampah'] ?></strong></td>
                                <td><?= $item['total_transaksi'] ?></td>
                                <td><span class="badge bg-success"><?= $item['total_disetujui'] ?></span></td>
                                <td><span class="badge bg-danger"><?= $item['total_ditolak'] ?></span></td>
                                <td><?= formatNumber($item['total_berat_disetujui']) ?></td>
                                <td><?= formatNumber($item['total_berat_ditolak']) ?></td>
                                <td><?= formatCurrency($item['total_nilai_disetujui']) ?></td>
                                <td><?= formatCurrency($item['total_nilai_ditolak']) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="showDetailRekapJenis('<?= esc($item['jenis_sampah']) ?>')">
                                        <i class="fas fa-eye"></i> Detail
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination Rekap Jenis -->
                <?php if (isset($pagination) && $pagination['total_pages_rekap_jenis'] > 1): ?>
                <nav aria-label="Pagination Rekap Jenis">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= $pagination['pages']['rekap_jenis'] <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $pagination['pages']['rekap_jenis'] - 1 ?>&section=rekap_jenis&<?= http_build_query($filters) ?>">
                                <i class="fas fa-chevron-left"></i> Previous
                            </a>
                        </li>
                        
                        <?php for ($i = 1; $i <= $pagination['total_pages_rekap_jenis']; $i++): ?>
                        <li class="page-item <?= $i == $pagination['pages']['rekap_jenis'] ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&section=rekap_jenis&<?= http_build_query($filters) ?>"><?= $i ?></a>
                        </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?= $pagination['pages']['rekap_jenis'] >= $pagination['total_pages_rekap_jenis'] ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $pagination['pages']['rekap_jenis'] + 1 ?>&section=rekap_jenis&<?= http_build_query($filters) ?>">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                    <div class="text-center text-muted">
                        <small>Halaman <?= $pagination['pages']['rekap_jenis'] ?> dari <?= $pagination['total_pages_rekap_jenis'] ?> 
                        (Total: <?= $pagination['total_rekap_jenis'] ?> jenis sampah)</small>
                    </div>
                </nav>
                <?php endif; ?>
                
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada data laporan</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Rekap per Unit -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h3><i class="fas fa-building"></i> Rekap per Unit</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($rekap_unit)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Unit</th>
                                <th>Total Transaksi</th>
                                <th>Disetujui</th>
                                <th>Ditolak</th>
                                <th>Berat Disetujui (kg)</th>
                                <th>Berat Ditolak (kg)</th>
                                <th>Nilai Disetujui</th>
                                <th>Nilai Ditolak</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rekap_unit as $item): ?>
                            <tr>
                                <td><strong><?= $item['nama_unit'] ?></strong></td>
                                <td><?= $item['total_transaksi'] ?></td>
                                <td><span class="badge bg-success"><?= $item['total_disetujui'] ?></span></td>
                                <td><span class="badge bg-danger"><?= $item['total_ditolak'] ?></span></td>
                                <td><?= formatNumber($item['total_berat_disetujui']) ?></td>
                                <td><?= formatNumber($item['total_berat_ditolak']) ?></td>
                                <td><?= formatCurrency($item['total_nilai_disetujui']) ?></td>
                                <td><?= formatCurrency($item['total_nilai_ditolak']) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="showRekapDetail('unit', <?= htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8') ?>)">
                                        <i class="fas fa-eye"></i> Detail
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination Rekap Unit -->
                <?php if (isset($pagination) && $pagination['total_pages_rekap_unit'] > 1): ?>
                <nav aria-label="Pagination Rekap Unit">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= $pagination['pages']['rekap_unit'] <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $pagination['pages']['rekap_unit'] - 1 ?>&section=rekap_unit&<?= http_build_query($filters) ?>">
                                <i class="fas fa-chevron-left"></i> Previous
                            </a>
                        </li>
                        
                        <?php for ($i = 1; $i <= $pagination['total_pages_rekap_unit']; $i++): ?>
                        <li class="page-item <?= $i == $pagination['pages']['rekap_unit'] ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&section=rekap_unit&<?= http_build_query($filters) ?>"><?= $i ?></a>
                        </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?= $pagination['pages']['rekap_unit'] >= $pagination['total_pages_rekap_unit'] ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $pagination['pages']['rekap_unit'] + 1 ?>&section=rekap_unit&<?= http_build_query($filters) ?>">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                    <div class="text-center text-muted">
                        <small>Halaman <?= $pagination['pages']['rekap_unit'] ?> dari <?= $pagination['total_pages_rekap_unit'] ?> 
                        (Total: <?= $pagination['total_rekap_unit'] ?> unit)</small>
                    </div>
                </nav>
                <?php endif; ?>
                
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada data laporan</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Detail Rekap per Minggu dalam Bulan -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h3><i class="fas fa-calendar-week"></i> Rekap Mingguan per Bulan</h3>
                <p class="mb-0 small">Rincian laporan per minggu dalam setiap bulan - data historis tetap tersimpan</p>
            </div>
            <div class="card-body">
                <!-- Filter Khusus Tabel Rekap Mingguan -->
                <div class="card mb-3 bg-light">
                    <div class="card-body">
                        <h6 class="mb-3"><i class="fas fa-filter"></i> Filter Tabel Rekap Mingguan</h6>
                        <form method="GET" action="<?= base_url('/admin-pusat/laporan-waste') ?>" id="filterRekapMingguan">
                            <!-- Preserve existing filters -->
                            <input type="hidden" name="start_date" value="<?= $filters['start_date'] ?? '' ?>">
                            <input type="hidden" name="end_date" value="<?= $filters['end_date'] ?? '' ?>">
                            <input type="hidden" name="unit_id" value="<?= $filters['unit_id'] ?? '' ?>">
                            <input type="hidden" name="section" value="detail_rekap">
                            
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="filter_bulan" class="form-label small">Bulan</label>
                                        <select class="form-select form-select-sm" id="filter_bulan" name="filter_bulan">
                                            <option value="">Semua Bulan</option>
                                            <option value="1" <?= ($filters['filter_bulan'] ?? '') == '1' ? 'selected' : '' ?>>Januari</option>
                                            <option value="2" <?= ($filters['filter_bulan'] ?? '') == '2' ? 'selected' : '' ?>>Februari</option>
                                            <option value="3" <?= ($filters['filter_bulan'] ?? '') == '3' ? 'selected' : '' ?>>Maret</option>
                                            <option value="4" <?= ($filters['filter_bulan'] ?? '') == '4' ? 'selected' : '' ?>>April</option>
                                            <option value="5" <?= ($filters['filter_bulan'] ?? '') == '5' ? 'selected' : '' ?>>Mei</option>
                                            <option value="6" <?= ($filters['filter_bulan'] ?? '') == '6' ? 'selected' : '' ?>>Juni</option>
                                            <option value="7" <?= ($filters['filter_bulan'] ?? '') == '7' ? 'selected' : '' ?>>Juli</option>
                                            <option value="8" <?= ($filters['filter_bulan'] ?? '') == '8' ? 'selected' : '' ?>>Agustus</option>
                                            <option value="9" <?= ($filters['filter_bulan'] ?? '') == '9' ? 'selected' : '' ?>>September</option>
                                            <option value="10" <?= ($filters['filter_bulan'] ?? '') == '10' ? 'selected' : '' ?>>Oktober</option>
                                            <option value="11" <?= ($filters['filter_bulan'] ?? '') == '11' ? 'selected' : '' ?>>November</option>
                                            <option value="12" <?= ($filters['filter_bulan'] ?? '') == '12' ? 'selected' : '' ?>>Desember</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="filter_tahun" class="form-label small">Tahun</label>
                                        <select class="form-select form-select-sm" id="filter_tahun" name="filter_tahun">
                                            <option value="">Semua Tahun</option>
                                            <?php 
                                            $currentYear = date('Y');
                                            for ($y = $currentYear; $y >= $currentYear - 5; $y--): 
                                            ?>
                                            <option value="<?= $y ?>" <?= ($filters['filter_tahun'] ?? '') == $y ? 'selected' : '' ?>><?= $y ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="filter_minggu" class="form-label small">Minggu Ke-</label>
                                        <select class="form-select form-select-sm" id="filter_minggu" name="filter_minggu">
                                            <option value="">Semua Minggu</option>
                                            <option value="1" <?= ($filters['filter_minggu'] ?? '') == '1' ? 'selected' : '' ?>>Minggu 1</option>
                                            <option value="2" <?= ($filters['filter_minggu'] ?? '') == '2' ? 'selected' : '' ?>>Minggu 2</option>
                                            <option value="3" <?= ($filters['filter_minggu'] ?? '') == '3' ? 'selected' : '' ?>>Minggu 3</option>
                                            <option value="4" <?= ($filters['filter_minggu'] ?? '') == '4' ? 'selected' : '' ?>>Minggu 4</option>
                                            <option value="5" <?= ($filters['filter_minggu'] ?? '') == '5' ? 'selected' : '' ?>>Minggu 5</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="filter_gedung" class="form-label small">Gedung</label>
                                        <input type="text" class="form-control form-control-sm" id="filter_gedung" name="filter_gedung" 
                                               placeholder="Cari gedung..." value="<?= $filters['filter_gedung'] ?? '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="filter_pelapor" class="form-label small">Pelapor</label>
                                        <input type="text" class="form-control form-control-sm" id="filter_pelapor" name="filter_pelapor" 
                                               placeholder="Cari pelapor..." value="<?= $filters['filter_pelapor'] ?? '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label class="form-label small">&nbsp;</label>
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                <i class="fas fa-search"></i> Filter
                                            </button>
                                            <a href="<?= base_url('/admin-pusat/laporan-waste?section=detail_rekap') ?>" class="btn btn-sm btn-secondary">
                                                <i class="fas fa-redo"></i> Reset
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if (!empty($detail_rekap)): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Bulan</th>
                                <th>Minggu Ke-</th>
                                <th>Gedung/Unit</th>
                                <th>Pelapor</th>
                                <th>Jenis Sampah</th>
                                <th>Jumlah Laporan</th>
                                <th>Disetujui</th>
                                <th>Ditolak</th>
                                <th>Total Berat (kg)</th>
                                <th>Total Nilai (Rp)</th>
                                <th>Periode</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = (($pagination['pages']['detail_rekap'] ?? 1) - 1) * ($pagination['per_page'] ?? 10) + 1;
                            $bulanIndo = [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                            ];
                            foreach ($detail_rekap as $item): 
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <strong><?= $bulanIndo[$item['bulan']] ?> <?= $item['tahun'] ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">Minggu <?= $item['minggu_ke'] ?></span>
                                </td>
                                <td>
                                    <strong><?= esc($item['gedung']) ?></strong>
                                    <?php if ($item['nama_unit'] != $item['gedung']): ?>
                                    <br><small class="text-muted"><?= esc($item['nama_unit']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= esc($item['nama_pelapor']) ?></strong>
                                    <br><small class="text-muted"><?= esc($item['username']) ?></small>
                                </td>
                                <td><span class="badge bg-primary"><?= esc($item['jenis_sampah']) ?></span></td>
                                <td><span class="badge bg-info"><?= $item['jumlah_laporan'] ?></span></td>
                                <td><span class="badge bg-success"><?= $item['total_disetujui'] ?></span></td>
                                <td><span class="badge bg-danger"><?= $item['total_ditolak'] ?></span></td>
                                <td><?= formatNumber($item['total_berat_disetujui']) ?></td>
                                <td><?= formatCurrency($item['total_nilai_disetujui']) ?></td>
                                <td>
                                    <small>
                                        <?= date('d/m/Y', strtotime($item['laporan_pertama'])) ?>
                                        <br>s/d<br>
                                        <?= date('d/m/Y', strtotime($item['laporan_terakhir'])) ?>
                                    </small>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination Detail Rekap -->
                <?php if (isset($pagination) && $pagination['total_pages_detail_rekap'] > 1): ?>
                <nav aria-label="Pagination Detail Rekap">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= $pagination['pages']['detail_rekap'] <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $pagination['pages']['detail_rekap'] - 1 ?>&section=detail_rekap&<?= http_build_query($filters) ?>">
                                <i class="fas fa-chevron-left"></i> Previous
                            </a>
                        </li>
                        
                        <?php for ($i = 1; $i <= $pagination['total_pages_detail_rekap']; $i++): ?>
                        <li class="page-item <?= $i == $pagination['pages']['detail_rekap'] ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&section=detail_rekap&<?= http_build_query($filters) ?>"><?= $i ?></a>
                        </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?= $pagination['pages']['detail_rekap'] >= $pagination['total_pages_detail_rekap'] ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $pagination['pages']['detail_rekap'] + 1 ?>&section=detail_rekap&<?= http_build_query($filters) ?>">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                    <div class="text-center text-muted">
                        <small>Halaman <?= $pagination['pages']['detail_rekap'] ?> dari <?= $pagination['total_pages_detail_rekap'] ?> 
                        (Total: <?= $pagination['total_detail_rekap'] ?> data)</small>
                    </div>
                </nav>
                <?php endif; ?>
                
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada data rekap mingguan</p>
                    <?php if (!empty(array_filter($filters))): ?>
                    <p class="text-muted small">Coba ubah filter atau <a href="<?= base_url('/admin-pusat/laporan-waste?section=detail_rekap') ?>">reset filter</a></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tabs for Disetujui and Ditolak -->
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#disetujui">
                            <i class="fas fa-check-circle text-success"></i> Data Disetujui (<?= $pagination['total_disetujui'] ?? 0 ?>)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#ditolak">
                            <i class="fas fa-times-circle text-danger"></i> Data Ditolak (<?= $pagination['total_ditolak'] ?? 0 ?>)
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- Data Disetujui -->
                    <div class="tab-pane fade show active" id="disetujui">
                        <?php if (!empty($data_disetujui)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Unit</th>
                                        <th>Jenis Sampah</th>
                                        <th>Berat (kg)</th>
                                        <th>Satuan</th>
                                        <th>Nilai</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data_disetujui as $item): ?>
                                    <tr>
                                        <td><?= date('d/m/Y H:i', strtotime($item['tanggal_input'] ?? $item['created_at'])) ?></td>
                                        <td><?= $item['nama_unit'] ?></td>
                                        <td><span class="badge bg-primary"><?= $item['jenis_sampah'] ?></span></td>
                                        <td><?= formatNumber($item['berat_kg']) ?></td>
                                        <td><?= $item['satuan'] ?></td>
                                        <td><?= formatCurrency($item['nilai_rupiah']) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="showDetail(<?= htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8') ?>)">
                                                <i class="fas fa-eye"></i> Detail
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination Data Disetujui -->
                        <?php if (isset($pagination) && $pagination['total_pages_disetujui'] > 1): ?>
                        <nav aria-label="Pagination Data Disetujui">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?= $pagination['pages']['disetujui'] <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $pagination['pages']['disetujui'] - 1 ?>&section=disetujui&<?= http_build_query($filters) ?>">
                                        <i class="fas fa-chevron-left"></i> Previous
                                    </a>
                                </li>
                                
                                <?php for ($i = 1; $i <= $pagination['total_pages_disetujui']; $i++): ?>
                                <li class="page-item <?= $i == $pagination['pages']['disetujui'] ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&section=disetujui&<?= http_build_query($filters) ?>"><?= $i ?></a>
                                </li>
                                <?php endfor; ?>
                                
                                <li class="page-item <?= $pagination['pages']['disetujui'] >= $pagination['total_pages_disetujui'] ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $pagination['pages']['disetujui'] + 1 ?>&section=disetujui&<?= http_build_query($filters) ?>">
                                        Next <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                            <div class="text-center text-muted">
                                <small>Halaman <?= $pagination['pages']['disetujui'] ?> dari <?= $pagination['total_pages_disetujui'] ?> 
                                (Total: <?= $pagination['total_disetujui'] ?> data)</small>
                            </div>
                        </nav>
                        <?php endif; ?>
                        
                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada data yang disetujui</p>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Data Ditolak -->
                    <div class="tab-pane fade" id="ditolak">
                        <?php if (!empty($data_ditolak)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Unit</th>
                                        <th>Jenis Sampah</th>
                                        <th>Berat (kg)</th>
                                        <th>Satuan</th>
                                        <th>Nilai</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data_ditolak as $item): ?>
                                    <tr>
                                        <td><?= date('d/m/Y H:i', strtotime($item['tanggal_input'] ?? $item['created_at'])) ?></td>
                                        <td><?= $item['nama_unit'] ?></td>
                                        <td><span class="badge bg-primary"><?= $item['jenis_sampah'] ?></span></td>
                                        <td><?= formatNumber($item['berat_kg']) ?></td>
                                        <td><?= $item['satuan'] ?></td>
                                        <td><?= formatCurrency($item['nilai_rupiah']) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="showDetail(<?= htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8') ?>)">
                                                <i class="fas fa-eye"></i> Detail
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination Data Ditolak -->
                        <?php if (isset($pagination) && $pagination['total_pages_ditolak'] > 1): ?>
                        <nav aria-label="Pagination Data Ditolak">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?= $pagination['pages']['ditolak'] <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $pagination['pages']['ditolak'] - 1 ?>&section=ditolak&<?= http_build_query($filters) ?>">
                                        <i class="fas fa-chevron-left"></i> Previous
                                    </a>
                                </li>
                                
                                <?php for ($i = 1; $i <= $pagination['total_pages_ditolak']; $i++): ?>
                                <li class="page-item <?= $i == $pagination['pages']['ditolak'] ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&section=ditolak&<?= http_build_query($filters) ?>"><?= $i ?></a>
                                </li>
                                <?php endfor; ?>
                                
                                <li class="page-item <?= $pagination['pages']['ditolak'] >= $pagination['total_pages_ditolak'] ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $pagination['pages']['ditolak'] + 1 ?>&section=ditolak&<?= http_build_query($filters) ?>">
                                        Next <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                            <div class="text-center text-muted">
                                <small>Halaman <?= $pagination['pages']['ditolak'] ?> dari <?= $pagination['total_pages_ditolak'] ?> 
                                (Total: <?= $pagination['total_ditolak'] ?> data)</small>
                            </div>
                        </nav>
                        <?php endif; ?>
                        
                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada data yang ditolak</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="detailModalLabel">
                        <i class="fas fa-info-circle"></i> Detail Laporan Sampah
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3"><i class="fas fa-file-alt"></i> Informasi Laporan</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="40%"><strong>ID Laporan:</strong></td>
                                    <td id="detail-id">-</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Input:</strong></td>
                                    <td id="detail-tanggal">-</td>
                                </tr>
                                <tr>
                                    <td><strong>Unit:</strong></td>
                                    <td id="detail-unit">-</td>
                                </tr>
                                <tr>
                                    <td><strong>Jenis Sampah:</strong></td>
                                    <td id="detail-jenis">-</td>
                                </tr>
                                <tr>
                                    <td><strong>Berat:</strong></td>
                                    <td id="detail-berat">-</td>
                                </tr>
                                <tr>
                                    <td><strong>Satuan:</strong></td>
                                    <td id="detail-satuan">-</td>
                                </tr>
                                <tr>
                                    <td><strong>Nilai (Rp):</strong></td>
                                    <td id="detail-nilai">-</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td id="detail-status">-</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3"><i class="fas fa-user"></i> Informasi Pelapor</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="40%"><strong>Nama Pelapor:</strong></td>
                                    <td id="detail-created-by">-</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Dibuat:</strong></td>
                                    <td id="detail-created-at">-</td>
                                </tr>
                                <tr>
                                    <td><strong>Direview Oleh:</strong></td>
                                    <td id="detail-reviewed-by">-</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Review:</strong></td>
                                    <td id="detail-reviewed-at">-</td>
                                </tr>
                            </table>
                            
                            <div id="detail-notes-section" style="display: none;">
                                <h6 class="text-danger mb-3"><i class="fas fa-comment"></i> Catatan Review</h6>
                                <div class="alert alert-warning" id="detail-notes">-</div>
                            </div>
                            
                            <div id="detail-foto-section" style="display: none;">
                                <h6 class="text-primary mb-3"><i class="fas fa-image"></i> Foto Bukti</h6>
                                <img id="detail-foto" src="" alt="Foto Bukti" class="img-fluid rounded" style="max-height: 300px;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Rekap -->
    <div class="modal fade" id="rekapDetailModal" tabindex="-1" aria-labelledby="rekapDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="rekapDetailModalLabel">
                        <i class="fas fa-chart-bar"></i> Detail Rekap
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-success mb-3"><i class="fas fa-check-circle"></i> Data Disetujui</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="50%"><strong>Total Transaksi:</strong></td>
                                    <td id="rekap-disetujui-count">-</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Berat:</strong></td>
                                    <td id="rekap-disetujui-berat">-</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Nilai:</strong></td>
                                    <td id="rekap-disetujui-nilai">-</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-danger mb-3"><i class="fas fa-times-circle"></i> Data Ditolak</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="50%"><strong>Total Transaksi:</strong></td>
                                    <td id="rekap-ditolak-count">-</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Berat:</strong></td>
                                    <td id="rekap-ditolak-berat">-</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Nilai:</strong></td>
                                    <td id="rekap-ditolak-nilai">-</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary mb-3"><i class="fas fa-info-circle"></i> Informasi</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="25%"><strong id="rekap-label">-</strong></td>
                                    <td id="rekap-name">-</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Transaksi:</strong></td>
                                    <td id="rekap-total-transaksi">-</td>
                                </tr>
                                <tr>
                                    <td><strong>Persentase Disetujui:</strong></td>
                                    <td id="rekap-persentase">-</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Mobile Menu JS -->
    <script src="<?= base_url('/js/mobile-menu.js') ?>"></script>
    
    <script>
    function showRekapDetail(type, data) {
        // Set label based on type
        const label = type === 'jenis' ? 'Jenis Sampah:' : 'Unit:';
        const name = type === 'jenis' ? data.jenis_sampah : data.nama_unit;
        
        document.getElementById('rekap-label').textContent = label;
        document.getElementById('rekap-name').textContent = name;
        
        // Data Disetujui
        document.getElementById('rekap-disetujui-count').textContent = data.total_disetujui || 0;
        document.getElementById('rekap-disetujui-berat').textContent = formatNumber(data.total_berat_disetujui || 0) + ' kg';
        document.getElementById('rekap-disetujui-nilai').textContent = formatCurrency(data.total_nilai_disetujui || 0);
        
        // Data Ditolak
        document.getElementById('rekap-ditolak-count').textContent = data.total_ditolak || 0;
        document.getElementById('rekap-ditolak-berat').textContent = formatNumber(data.total_berat_ditolak || 0) + ' kg';
        document.getElementById('rekap-ditolak-nilai').textContent = formatCurrency(data.total_nilai_ditolak || 0);
        
        // Total & Persentase
        const totalTransaksi = data.total_transaksi || 0;
        const totalDisetujui = data.total_disetujui || 0;
        const persentase = totalTransaksi > 0 ? ((totalDisetujui / totalTransaksi) * 100).toFixed(2) : 0;
        
        document.getElementById('rekap-total-transaksi').textContent = totalTransaksi;
        document.getElementById('rekap-persentase').innerHTML = 
            '<span class="badge bg-success">' + persentase + '%</span> (' + totalDisetujui + ' dari ' + totalTransaksi + ' transaksi)';
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('rekapDetailModal'));
        modal.show();
    }
    
    function showDetail(data) {
        // Populate modal with data
        document.getElementById('detail-id').textContent = data.id || '-';
        document.getElementById('detail-tanggal').textContent = formatDate(data.tanggal_input || data.created_at);
        document.getElementById('detail-unit').textContent = data.nama_unit || '-';
        document.getElementById('detail-jenis').innerHTML = '<span class="badge bg-primary">' + (data.jenis_sampah || '-') + '</span>';
        document.getElementById('detail-berat').textContent = formatNumber(data.berat_kg) + ' kg';
        document.getElementById('detail-satuan').textContent = data.satuan || 'kg';
        document.getElementById('detail-nilai').textContent = formatCurrency(data.nilai_rupiah);
        
        // Status badge
        let statusBadge = '';
        if (data.status === 'approved') {
            statusBadge = '<span class="badge bg-success">Disetujui</span>';
        } else if (data.status === 'rejected') {
            statusBadge = '<span class="badge bg-danger">Ditolak</span>';
        } else {
            statusBadge = '<span class="badge bg-warning">Pending</span>';
        }
        document.getElementById('detail-status').innerHTML = statusBadge;
        
        // Creator info - handle null/empty created_by
        const creatorName = data.created_by_name || data.nama_lengkap || data.user_name || 'Data Lama (Tidak Tercatat)';
        document.getElementById('detail-created-by').textContent = creatorName;
        document.getElementById('detail-created-at').textContent = formatDateTime(data.created_at);
        
        // Reviewer info
        document.getElementById('detail-reviewed-by').textContent = data.reviewed_by_name || '-';
        document.getElementById('detail-reviewed-at').textContent = data.reviewed_at ? formatDateTime(data.reviewed_at) : '-';
        
        // Review notes (only show if rejected)
        const notesSection = document.getElementById('detail-notes-section');
        if (data.review_notes && data.status === 'rejected') {
            document.getElementById('detail-notes').textContent = data.review_notes;
            notesSection.style.display = 'block';
        } else {
            notesSection.style.display = 'none';
        }
        
        // Photo (if exists)
        const fotoSection = document.getElementById('detail-foto-section');
        if (data.foto_bukti) {
            document.getElementById('detail-foto').src = '<?= base_url('/uploads/') ?>' + data.foto_bukti;
            fotoSection.style.display = 'block';
        } else {
            fotoSection.style.display = 'none';
        }
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('detailModal'));
        modal.show();
    }
    
    function formatNumber(num) {
        if (!num) return '0,00';
        return parseFloat(num).toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    
    function formatCurrency(amount) {
        if (!amount) return 'Rp 0';
        return 'Rp ' + parseInt(amount).toLocaleString('id-ID');
    }
    
    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const date = new Date(dateStr);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${day}/${month}/${year}`;
    }
    
    function formatDateTime(dateStr) {
        if (!dateStr) return '-';
        const date = new Date(dateStr);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        return `${day}/${month}/${year} ${hours}:${minutes}`;
    }
    </script>

    <!-- Modal Detail Rekap Jenis Sampah -->
    <div class="modal fade" id="detailRekapModal" tabindex="-1" aria-labelledby="detailRekapModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="detailRekapModalLabel">
                        <i class="fas fa-info-circle"></i> Detail Rekap Jenis Sampah
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detailRekapBody">
                    <!-- Content will be loaded via AJAX -->
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Klik tombol Detail untuk melihat rincian</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Detail Rekap JS -->
    <script src="<?= base_url('/js/laporan-waste-detail.js') ?>"></script>
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
    margin-bottom: 30px;
    padding: 20px 0;
    border-bottom: 2px solid #e9ecef;
}

.page-header h1 {
    color: #2c3e50;
    margin-bottom: 10px;
    font-size: 28px;
    font-weight: 700;
}

.page-header p {
    color: #6c757d;
    font-size: 16px;
    margin: 0;
}

.card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    overflow: hidden;
    border: none;
}

.card-header {
    padding: 20px 25px;
    border: none;
}

.card-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.card-body {
    padding: 25px;
}

.table-responsive {
    border-radius: 10px;
    overflow: hidden;
}

.table th {
    background: #f8f9fa;
    border: none;
    font-weight: 600;
    color: #2c3e50;
    padding: 15px;
    font-size: 14px;
}

.table td {
    border: none;
    padding: 15px;
    vertical-align: middle;
    font-size: 14px;
}

.table tbody tr {
    border-bottom: 1px solid #e9ecef;
}

.table tbody tr:hover {
    background: #f8f9fa;
}

.pagination {
    margin-top: 20px;
    margin-bottom: 10px;
}

.pagination .page-link {
    color: #2c3e50;
    border: 1px solid #dee2e6;
    padding: 8px 12px;
    margin: 0 2px;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.pagination .page-link:hover {
    background: #e9ecef;
    border-color: #dee2e6;
}

.pagination .page-item.active .page-link {
    background: #007bff;
    border-color: #007bff;
    color: white;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background: #fff;
    border-color: #dee2e6;
}

@media (max-width: 768px) {
    .main-content {
        margin-left: 0;
        padding: 15px 10px;
        max-width: 100vw;
        overflow-x: hidden;
    }

    .page-header {
        padding: 15px 0;
        margin-bottom: 20px;
    }

    .page-header h1 {
        font-size: 22px;
    }

    .page-header p {
        font-size: 14px;
    }

    /* Filter section */
    .card-header h3 {
        font-size: 16px;
    }

    .card-body {
        padding: 15px 10px;
    }

    /* Form columns stack vertically */
    .row > [class*="col-"] {
        padding-left: 10px;
        padding-right: 10px;
        margin-bottom: 10px;
    }

    /* Summary cards */
    .row.mb-4 {
        margin-left: 0;
        margin-right: 0;
    }

    .row.mb-4 > .col-md-3 {
        padding: 0 5px;
        margin-bottom: 10px;
    }

    .row.mb-4 .card {
        margin-bottom: 0;
    }

    .row.mb-4 .card-body {
        padding: 15px;
    }

    .row.mb-4 .card-body h5 {
        font-size: 14px;
        margin-bottom: 8px;
    }

    .row.mb-4 .card-body h2 {
        font-size: 24px;
        margin-bottom: 5px;
    }

    .row.mb-4 .card-body small {
        font-size: 11px;
    }

    /* Export button */
    .mb-3 .btn {
        width: 100%;
        margin-bottom: 10px;
    }

    /* Tables */
    .table-responsive {
        max-width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin: 0 -10px;
        padding: 0 10px;
    }

    .table {
        font-size: 10px;
        min-width: 700px;
    }

    .table th,
    .table td {
        padding: 8px 5px;
        font-size: 10px;
        white-space: nowrap;
    }

    .table th {
        font-size: 9px;
        text-transform: uppercase;
    }

    .table .badge {
        font-size: 8px;
        padding: 2px 5px;
    }

    /* Tabs */
    .nav-tabs {
        flex-wrap: nowrap;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border-bottom: 1px solid #dee2e6;
    }

    .nav-tabs .nav-link {
        white-space: nowrap;
        font-size: 12px;
        padding: 10px 12px;
    }

    .nav-tabs .nav-link i {
        font-size: 11px;
    }
    
    /* Pagination */
    .pagination {
        flex-wrap: wrap;
        justify-content: center;
        margin-top: 15px;
    }
    
    .pagination .page-item {
        margin: 2px;
    }

    .pagination .page-link {
        padding: 6px 10px;
        font-size: 11px;
    }

    .pagination .page-link i {
        font-size: 10px;
    }

    /* Pagination info text */
    .text-center.text-muted small {
        font-size: 10px;
    }

    /* Empty state */
    .text-center.py-5 {
        padding: 30px 15px !important;
    }

    .text-center.py-5 i {
        font-size: 36px !important;
    }

    .text-center.py-5 p {
        font-size: 14px;
    }

    /* Action buttons */
    .btn-sm {
        font-size: 9px;
        padding: 4px 8px;
        white-space: nowrap;
    }

    .btn-sm i {
        font-size: 8px;
    }

    /* Modal on mobile */
    .modal-dialog {
        margin: 10px;
        max-width: calc(100% - 20px);
    }

    .modal-body {
        padding: 15px;
    }

    .modal-body h6 {
        font-size: 13px;
        margin-bottom: 10px;
    }

    .modal-body table {
        font-size: 11px;
    }

    .modal-body table td {
        padding: 5px 0;
        word-break: break-word;
    }

    .modal-body .row > div {
        margin-bottom: 15px;
    }

    .modal-footer {
        padding: 10px;
    }

    .modal-footer .btn {
        font-size: 12px;
        padding: 8px 15px;
    }

    .modal-body img {
        max-width: 100%;
        height: auto;
    }
}    /* Card headers with background */
    .card-header.bg-primary,
    .card-header.bg-success {
        padding: 12px 15px;
    }

    /* Filter form buttons */
    .d-grid.gap-2 {
        gap: 8px !important;
    }

    .d-grid.gap-2 .btn {
        font-size: 13px;
        padding: 10px;
    }
}

@media (max-width: 480px) {
    .main-content {
        padding: 10px 5px;
    }

    .page-header h1 {
        font-size: 20px;
    }

    .card-body {
        padding: 12px 8px;
    }

    .table {
        font-size: 9px;
        min-width: 650px;
    }

    .table th,
    .table td {
        padding: 6px 4px;
        font-size: 9px;
    }

    .row.mb-4 .card-body h2 {
        font-size: 20px;
    }

    .nav-tabs .nav-link {
        font-size: 11px;
        padding: 8px 10px;
    }

    .pagination .page-link {
        padding: 5px 8px;
        font-size: 10px;
    }
}
</style>
