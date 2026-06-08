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
    <title><?= $title ?? 'Laporan Transportasi' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/dashboard.css') ?>" rel="stylesheet">
    <link href="<?= base_url('/css/mobile-responsive.css') ?>" rel="stylesheet">
    <style>
        /* Ensure main-content has proper margin for sidebar */
        .main-content {
            margin-left: 280px;
            padding: 30px;
            min-height: 100vh;
            width: calc(100% - 280px);
            transition: margin-left 0.3s ease;
        }
        
        /* Responsive adjustments */
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 20px;
            }
        }
        
        /* Ensure cards don't overflow */
        .card {
            max-width: 100%;
            overflow: hidden;
        }
        
        /* Ensure tables are responsive */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        /* ===== ELEGANT TABLE STYLING ===== */
        .elegant-table {
            font-size: 0.95rem;
        }
        
        /* Header styling - minimalist */
        .elegant-table thead th {
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
            padding: 1rem 0.75rem;
            background-color: transparent;
            border-bottom: 2px solid #e9ecef !important;
        }
        
        /* Body row styling - spacious and clean */
        .elegant-table tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid #f8f9fa;
        }
        
        /* Hover effect - subtle and smooth */
        .elegant-table tbody tr:hover {
            background-color: #f8f9fa;
            transform: translateX(2px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
        }
        
        /* Number column - minimalist and subtle */
        .elegant-table tbody td:first-child {
            font-size: 0.8rem;
            color: #adb5bd;
            font-weight: 300;
        }
        
        /* Remove default table borders */
        .elegant-table td {
            border: none;
            vertical-align: middle;
        }
        
        /* Card styling - clean and modern */
        .card.shadow-sm {
            box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.075) !important;
            border-radius: 0.5rem;
            overflow: hidden;
        }
        
        .card.border-0 {
            border: none !important;
        }
        
        /* Smooth transitions for all interactive elements */
        .btn, .card, .elegant-table tbody tr {
            transition: all 0.2s ease-in-out;
        }
        
        /* Button hover enhancement */
        .btn-info:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
        }
    </style>
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-chart-bar"></i> Laporan Data Transportasi</h1>
            <p>Laporan lengkap data kendaraan kampus berdasarkan kategori dan bahan bakar</p>
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
                <form method="GET" action="<?= base_url('/admin-pusat/transportation/laporan-lengkap') ?>">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                       value="<?= $filters['start_date'] ?? '' ?>">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                       value="<?= $filters['end_date'] ?? '' ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                    <a href="<?= base_url('/admin-pusat/transportation/laporan-lengkap') ?>" class="btn btn-secondary">
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
                        <h5>Total Kendaraan</h5>
                        <h2><?= formatNumber($summary['total_kendaraan'] ?? 0) ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5>Total ZEV</h5>
                        <h2><?= formatNumber($summary['total_zev'] ?? 0) ?></h2>
                        <small>Kendaraan Listrik + Sepeda</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h5>Total Non-ZEV</h5>
                        <h2><?= formatNumber($summary['total_non_zev'] ?? 0) ?></h2>
                        <small>Kendaraan BBM</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5>Persentase Keberlanjutan</h5>
                        <h2><?= number_format($summary['persentase_keberlanjutan'] ?? 0, 2) ?>%</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export Buttons -->
        <div class="mb-3">
            <div class="btn-group" role="group">
                <a href="<?= base_url('/admin-pusat/transportation/export-laporan-excel?' . http_build_query($filters)) ?>" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export ke Excel
                </a>
                <a href="<?= base_url('/admin-pusat/transportation/export-laporan-pdf?' . http_build_query($filters)) ?>" class="btn btn-danger" target="_blank">
                    <i class="fas fa-file-pdf"></i> Export ke PDF
                </a>
            </div>
        </div>

        <!-- Rekap per Jenis Kendaraan -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h3><i class="fas fa-car"></i> Rekap per Jenis Kendaraan</h3>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($rekap_kategori)): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-borderless mb-0 elegant-table">
                        <thead class="border-bottom">
                            <tr>
                                <th style="width: 50px;" class="text-center text-muted ps-4">#</th>
                                <th style="width: 55%;">Jenis Kendaraan</th>
                                <th style="width: 25%;">Total Unit</th>
                                <th style="width: 15%;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($rekap_kategori as $item): ?>
                            <tr class="align-middle">
                                <td class="text-center text-muted ps-4">
                                    <small class="fw-light"><?= $no++ ?></small>
                                </td>
                                <td class="py-3"><strong><?= $item['kategori'] ?></strong></td>
                                <td class="py-3"><?= formatNumber($item['total_unit']) ?></td>
                                <td class="py-3 text-center">
                                    <button class="btn btn-sm btn-info btn-detail-kategori" 
                                            data-kategori="<?= esc($item['kategori']) ?>"
                                            title="Lihat Detail">
                                        <i class="fas fa-eye"></i> Preview
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
                    <p class="text-muted">Belum ada data laporan</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Rekap per Jenis Bahan Bakar -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-success text-white">
                <h3><i class="fas fa-gas-pump"></i> Rekap per Jenis Bahan Bakar</h3>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($rekap_bahan_bakar)): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-borderless mb-0 elegant-table">
                        <thead class="border-bottom">
                            <tr>
                                <th style="width: 50px;" class="text-center text-muted ps-4">#</th>
                                <th style="width: 55%;">Bahan Bakar</th>
                                <th style="width: 25%;">Total Unit</th>
                                <th style="width: 15%;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($rekap_bahan_bakar as $item): ?>
                            <tr class="align-middle">
                                <td class="text-center text-muted ps-4">
                                    <small class="fw-light"><?= $no++ ?></small>
                                </td>
                                <td class="py-3">
                                    <strong><?= $item['bahan_bakar'] ?></strong>
                                    <?php if ($item['bahan_bakar'] === 'Listrik' || $item['bahan_bakar'] === 'Non-BBM'): ?>
                                        <span class="badge bg-success ms-2">ZEV</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3"><?= formatNumber($item['total_unit']) ?></td>
                                <td class="py-3 text-center">
                                    <button class="btn btn-sm btn-info btn-detail-bahan-bakar" 
                                            data-bahan-bakar="<?= esc($item['bahan_bakar']) ?>"
                                            title="Lihat Detail">
                                        <i class="fas fa-eye"></i> Preview
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
                    <p class="text-muted">Belum ada data laporan</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Rekap Bulanan -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h3><i class="fas fa-calendar-alt"></i> Rekap Bulanan</h3>
                <p class="mb-0 small">Rincian laporan per bulan - data historis tetap tersimpan</p>
            </div>
            <div class="card-body">
                <?php if (!empty($rekap_bulanan)): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 10%;">No</th>
                                <th style="width: 55%;">Periode</th>
                                <th style="width: 20%;">Total Kendaraan</th>
                                <th style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach ($rekap_bulanan as $item): 
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= $item['periode'] ?></strong></td>
                                <td><?= formatNumber($item['total_kendaraan']) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info btn-detail-bulanan" 
                                            data-periode="<?= esc($item['periode']) ?>"
                                            title="Lihat Detail">
                                        <i class="fas fa-eye"></i> Preview
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
                    <p class="text-muted">Belum ada data laporan</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- Modal Detail Data -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="detailModalLabel">
                        <i class="fas fa-list-alt"></i> Detail Data Transportasi
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
                    <!-- Loading Spinner -->
                    <div id="loadingSpinner" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted"><strong>Memuat data...</strong></p>
                    </div>
                    
                    <!-- Detail Content -->
                    <div id="detailContent" style="display: none;">
                        <!-- Filter Info -->
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-filter"></i>
                            <strong>Filter Aktif:</strong> <span id="filterInfo"></span>
                        </div>
                        
                        <!-- Summary Statistics Cards -->
                        <div class="row mb-4" id="summaryCards">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center py-3">
                                        <h6 class="card-title mb-2">
                                            <i class="fas fa-list-alt"></i> Total Transaksi
                                        </h6>
                                        <h3 class="mb-0" id="summaryTotalTransaksi">0</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center py-3">
                                        <h6 class="card-title mb-2">
                                            <i class="fas fa-car"></i> Total Unit
                                        </h6>
                                        <h3 class="mb-0" id="summaryTotalUnit">0</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center py-3">
                                        <h6 class="card-title mb-2">
                                            <i class="fas fa-layer-group"></i> Total Kategori
                                        </h6>
                                        <h3 class="mb-0" id="summaryTotalKategori">0</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center py-3">
                                        <h6 class="card-title mb-2">
                                            <i class="fas fa-leaf"></i> ZEV
                                        </h6>
                                        <h3 class="mb-0" id="summaryPersentaseZev">0%</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Detail Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-bordered table-sm">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 5%;">No</th>
                                        <th style="width: 18%;">Kategori Kendaraan</th>
                                        <th style="width: 15%;">Bahan Bakar</th>
                                        <th style="width: 15%;">Status Kendaraan</th>
                                        <th style="width: 10%;">Jumlah Unit</th>
                                        <th style="width: 12%;">Periode</th>
                                        <th style="width: 12%;">Tanggal Data</th>
                                        <th style="width: 13%;">Tanggal Input</th>
                                    </tr>
                                </thead>
                                <tbody id="detailTableBody">
                                    <!-- Data will be loaded here via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Error Message -->
                    <div id="errorMessage" class="alert alert-danger" style="display: none;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Error:</strong> <span id="errorText"></span>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const baseUrl = '<?= base_url() ?>';
        const filters = {
            start_date: '<?= $filters['start_date'] ?? '' ?>',
            end_date: '<?= $filters['end_date'] ?? '' ?>'
        };
        
        // Function to format number with thousand separator
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
        
        // Function to load detail data
        function loadDetailData(type, value) {
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('detailModal'));
            modal.show();
            
            // Reset modal content
            $('#loadingSpinner').show();
            $('#detailContent').hide();
            $('#errorMessage').hide();
            $('#detailTableBody').empty();
            
            // Set filter info
            let filterText = '';
            if (type === 'kategori') {
                filterText = `Jenis Kendaraan: <strong>${value}</strong>`;
            } else if (type === 'bahan_bakar') {
                filterText = `Bahan Bakar: <strong>${value}</strong>`;
            } else if (type === 'periode') {
                filterText = `Periode: <strong>${value}</strong>`;
            }
            
            if (filters.start_date && filters.end_date) {
                filterText += ` | Rentang Tanggal: <strong>${filters.start_date}</strong> s/d <strong>${filters.end_date}</strong>`;
            }
            
            $('#filterInfo').html(filterText);
            
            // AJAX request
            $.ajax({
                url: baseUrl + '/admin-pusat/transportation/get-detail-laporan',
                type: 'POST',
                data: {
                    type: type,
                    value: value,
                    start_date: filters.start_date,
                    end_date: filters.end_date,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(response) {
                    $('#loadingSpinner').hide();
                    
                    if (response.success) {
                        // Update summary cards
                        if (response.summary) {
                            $('#summaryTotalTransaksi').text(formatNumber(response.summary.total_transaksi));
                            $('#summaryTotalUnit').text(formatNumber(response.summary.total_unit));
                            $('#summaryTotalKategori').text(formatNumber(response.summary.total_kategori));
                            $('#summaryPersentaseZev').text(response.summary.persentase_zev + '%');
                        }
                        
                        // Populate table
                        if (response.data && response.data.length > 0) {
                            let html = '';
                            let no = 1;
                            
                            response.data.forEach(function(item) {
                                // Determine badge color for ZEV
                                let zevBadge = '';
                                if (item.is_zev == 1) {
                                    zevBadge = '<span class="badge bg-success ms-1">ZEV</span>';
                                }
                                
                                // Determine badge color for Status Kendaraan
                                let statusBadge = '';
                                let statusColor = 'secondary';
                                if (item.status_kendaraan) {
                                    if (item.status_kendaraan === 'Milik Universitas') {
                                        statusColor = 'success';
                                    } else if (item.status_kendaraan === 'Milik Pribadi') {
                                        statusColor = 'info';
                                    } else if (item.status_kendaraan === 'Kendaraan Sewa' || item.status_kendaraan === 'Kendaraan Umum') {
                                        statusColor = 'warning';
                                    }
                                    statusBadge = `<span class="badge bg-${statusColor}">${item.status_kendaraan}</span>`;
                                } else {
                                    statusBadge = '<span class="badge bg-secondary">Tidak Diketahui</span>';
                                }
                                
                                html += `
                                    <tr>
                                        <td class="text-center">${no++}</td>
                                        <td><strong>${item.kategori}</strong></td>
                                        <td>${item.bahan_bakar} ${zevBadge}</td>
                                        <td>${statusBadge}</td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">${formatNumber(item.jumlah_total)}</span>
                                        </td>
                                        <td><small>${item.periode}</small></td>
                                        <td><small>${item.tanggal_data}</small></td>
                                        <td><small class="text-muted">${item.tanggal_input}</small></td>
                                    </tr>
                                `;
                            });
                            
                            $('#detailTableBody').html(html);
                        } else {
                            $('#detailTableBody').html(`
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                        <p class="text-muted mb-0">Tidak ada data detail untuk filter ini</p>
                                    </td>
                                </tr>
                            `);
                            
                            // Reset summary cards
                            $('#summaryTotalTransaksi').text('0');
                            $('#summaryTotalUnit').text('0');
                            $('#summaryTotalKategori').text('0');
                            $('#summaryPersentaseZev').text('0%');
                        }
                        
                        $('#detailContent').show();
                    } else {
                        $('#loadingSpinner').hide();
                        $('#errorText').text(response.message || 'Gagal memuat data');
                        $('#errorMessage').show();
                    }
                },
                error: function(xhr, status, error) {
                    $('#loadingSpinner').hide();
                    let errorMsg = 'Gagal memuat data: ' + error;
                    
                    // Try to parse error response
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMsg = response.message;
                        }
                        if (response.error_detail) {
                            errorMsg += '<br><small>' + response.error_detail + '</small>';
                        }
                    } catch (e) {
                        // Use default error message
                    }
                    
                    $('#errorText').html(errorMsg);
                    $('#errorMessage').show();
                }
            });
        }
        
        // Event handlers
        $(document).ready(function() {
            // Detail Kategori
            $('.btn-detail-kategori').on('click', function() {
                const kategori = $(this).data('kategori');
                loadDetailData('kategori', kategori);
            });
            
            // Detail Bahan Bakar
            $('.btn-detail-bahan-bakar').on('click', function() {
                const bahanBakar = $(this).data('bahan-bakar');
                loadDetailData('bahan_bakar', bahanBakar);
            });
            
            // Detail Bulanan
            $('.btn-detail-bulanan').on('click', function() {
                const periode = $(this).data('periode');
                loadDetailData('periode', periode);
            });
        });
    </script>
</body>
</html>
