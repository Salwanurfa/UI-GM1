<?php
// Helper functions
if (!function_exists('formatNumber')) {
    function formatNumber($number) {
        return number_format($number, 0, ',', '.');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'History Transportasi' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="<?= base_url('/css/mobile-responsive.css') ?>" rel="stylesheet">
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-history"></i> History Data Transportasi</h1>
            <p>Riwayat lengkap data kendaraan kampus yang telah diinput</p>
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
                <h3><i class="fas fa-filter"></i> Filter History</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="<?= base_url('/admin-pusat/transportation/history') ?>">
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
                                    <a href="<?= base_url('/admin-pusat/transportation/history') ?>" class="btn btn-secondary">
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
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5>Total Kendaraan</h5>
                        <h2><?= formatNumber($summary['total_kendaraan'] ?? 0) ?></h2>
                        <small>Unit</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5>Total ZEV</h5>
                        <h2><?= formatNumber($summary['total_zev'] ?? 0) ?></h2>
                        <small>Kendaraan Ramah Lingkungan</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5>Persentase ZEV</h5>
                        <h2><?= number_format($summary['persentase_zev'] ?? 0, 2) ?>%</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export Buttons -->
        <div class="mb-3">
            <div class="btn-group" role="group">
                <a href="<?= base_url('/admin-pusat/transportation/export-history-excel?' . http_build_query($filters)) ?>" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export ke Excel
                </a>
                <a href="<?= base_url('/admin-pusat/transportation/export-history-pdf?' . http_build_query($filters)) ?>" class="btn btn-danger" target="_blank">
                    <i class="fas fa-file-pdf"></i> Export ke PDF
                </a>
            </div>
        </div>

        <!-- Rekap per Kategori Kendaraan -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h3><i class="fas fa-car"></i> Rekap per Kategori Kendaraan</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($rekap_kategori)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kategori Kendaraan</th>
                                <th>Total Unit</th>
                                <th>ZEV</th>
                                <th>Non-ZEV</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rekap_kategori as $item): ?>
                            <tr>
                                <td><strong><?= $item['kategori'] ?></strong></td>
                                <td><?= formatNumber($item['total_unit']) ?></td>
                                <td><span class="badge bg-success"><?= formatNumber($item['total_zev']) ?></span></td>
                                <td><span class="badge bg-danger"><?= formatNumber($item['total_non_zev']) ?></span></td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="showDetailKategori('<?= esc($item['kategori']) ?>')">
                                        <i class="fas fa-eye"></i> Detail
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada data history</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Rekap Mingguan per Bulan -->
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
                        <form method="GET" action="<?= base_url('/admin-pusat/transportation/history') ?>" id="filterRekapMingguan">
                            <!-- Preserve existing filters -->
                            <input type="hidden" name="start_date" value="<?= $filters['start_date'] ?? '' ?>">
                            <input type="hidden" name="end_date" value="<?= $filters['end_date'] ?? '' ?>">
                            <input type="hidden" name="unit_id" value="<?= $filters['unit_id'] ?? '' ?>">
                            
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
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="filter_petugas" class="form-label small">Petugas</label>
                                        <input type="text" class="form-control form-control-sm" id="filter_petugas" name="filter_petugas" 
                                               placeholder="Cari petugas..." value="<?= $filters['filter_petugas'] ?? '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label small">&nbsp;</label>
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                <i class="fas fa-search"></i> Filter
                                            </button>
                                            <a href="<?= base_url('/admin-pusat/transportation/history') ?>" class="btn btn-sm btn-secondary">
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
                    <table class="table table-hover table-sm" id="historyTable">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Bulan</th>
                                <th>Minggu Ke-</th>
                                <th>Unit</th>
                                <th>Petugas</th>
                                <th>Kategori Kendaraan</th>
                                <th>Status Kendaraan</th>
                                <th>Jumlah Transaksi</th>
                                <th>Total Unit</th>
                                <th>ZEV</th>
                                <th>Non-ZEV</th>
                                <th>Periode</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
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
                                    <strong><?= esc($item['nama_unit']) ?></strong>
                                </td>
                                <td>
                                    <strong><?= esc($item['nama_petugas']) ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?= esc($item['kategori_kendaraan']) ?></span>
                                </td>
                                <td>
                                    <?php 
                                    $statusKendaraan = $item['status_kendaraan'] ?? 'Tidak Diketahui';
                                    $statusColor = 'secondary';
                                    if ($statusKendaraan === 'Milik Universitas') {
                                        $statusColor = 'success';
                                    } elseif ($statusKendaraan === 'Milik Pribadi') {
                                        $statusColor = 'info';
                                    } elseif ($statusKendaraan === 'Kendaraan Sewa' || $statusKendaraan === 'Kendaraan Umum') {
                                        $statusColor = 'warning';
                                    }
                                    ?>
                                    <span class="badge bg-<?= $statusColor ?>"><?= esc($statusKendaraan) ?></span>
                                </td>
                                <td><?= $item['jumlah_transaksi'] ?></td>
                                <td><?= formatNumber($item['total_unit']) ?></td>
                                <td><span class="badge bg-success"><?= formatNumber($item['total_zev']) ?></span></td>
                                <td><span class="badge bg-danger"><?= formatNumber($item['total_non_zev']) ?></span></td>
                                <td>
                                    <small class="text-muted">
                                        <?= date('d/m/Y', strtotime($item['tanggal_mulai'])) ?> - 
                                        <?= date('d/m/Y', strtotime($item['tanggal_akhir'])) ?>
                                    </small>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada data rekap mingguan</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#historyTable').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
                order: [[1, 'desc']],
                pageLength: 25
            });
        });

        function showDetailKategori(kategori) {
            // TODO: Implement detail modal
            alert('Detail untuk kategori: ' + kategori);
        }
    </script>
</body>
</html>
