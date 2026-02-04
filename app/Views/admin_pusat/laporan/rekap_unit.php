<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-name" content="<?= csrf_token() ?>">
    <title><?= esc($title ?? 'Rekap per Unit') ?> - Admin Pusat</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .main-content { margin-left: 260px; padding: 20px; }
        .page-header { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .page-header h1 { margin: 0; font-size: 2rem; font-weight: 600; }
        .page-header p { margin: 10px 0 0 0; opacity: 0.9; }
        .card { border: none; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.08); margin-bottom: 20px; }
        .card-header { background-color: #fff; border-bottom: 2px solid #f0f0f0; padding: 20px; font-weight: 600; color: #333; }
        .table-responsive { margin-top: 20px; }
        .table thead th { background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; font-weight: 600; color: #495057; }
        .badge { padding: 6px 12px; font-weight: 500; }
        .aggregates-box { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
        .aggregates-box .stat-item { text-align: center; }
        .aggregates-box .stat-value { font-size: 1.8rem; font-weight: 700; margin-bottom: 5px; }
        .aggregates-box .stat-label { font-size: 0.9rem; opacity: 0.9; }
        .pagination-wrapper { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; }
        .btn-action { margin: 2px; }
        .empty-state { text-align: center; padding: 60px 20px; color: #6c757d; }
        .empty-state i { font-size: 4rem; margin-bottom: 20px; opacity: 0.5; }
        .loading-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center; }
        .loading-overlay.active { display: flex; }
        .spinner-border { width: 3rem; height: 3rem; }
    </style>
</head>
<body>
    <?= view('partials/sidebar') ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-building"></i> Rekap per Unit</h1>
            <p>Laporan rekap sampah berdasarkan unit/gedung yang sudah dikonfirmasi</p>
        </div>

        <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> <?= esc($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Filters Card -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-filter"></i> Filter Data
            </div>
            <div class="card-body">
                <form id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="filterFrom" name="from" value="<?= esc($filters['from'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="filterTo" name="to" value="<?= esc($filters['to'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Unit</label>
                            <select class="form-select" id="filterUnit" name="unit_id">
                                <option value="">Semua Unit</option>
                                <?php if (!empty($allUnits)): ?>
                                    <?php foreach ($allUnits as $unit): ?>
                                        <option value="<?= esc($unit['id']) ?>" <?= ($filters['unit_id'] ?? '') == $unit['id'] ? 'selected' : '' ?>>
                                            <?= esc($unit['nama_unit']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Pencarian</label>
                            <input type="text" class="form-control" id="filterSearch" name="search" placeholder="Cari nama unit..." value="<?= esc($filters['search'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Tampilkan Data
                            </button>
                            <button type="button" class="btn btn-secondary" id="btnReset">
                                <i class="fas fa-redo"></i> Reset
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Aggregates -->
        <div class="aggregates-box" id="aggregatesBox" style="display: none;">
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-value" id="totalUnits">0</div>
                        <div class="stat-label">Total Unit</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-value" id="totalRecords">0</div>
                        <div class="stat-label">Total Laporan</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-value" id="totalJumlah">0</div>
                        <div class="stat-label">Total Jumlah (kg)</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-value" id="totalNilai">Rp 0</div>
                        <div class="stat-label">Total Nilai</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-table"></i> Data Rekap per Unit
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Unit</th>
                                <th>Total Laporan</th>
                                <th>Total Jumlah (kg)</th>
                                <th>Total Nilai (Rp)</th>
                                <th>Laporan Pertama</th>
                                <th>Laporan Terakhir</th>
                            </tr>
                        </thead>
                        <tbody id="dataTableBody">
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <p>Klik "Tampilkan Data" untuk memuat laporan</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination-wrapper" id="paginationWrapper" style="display: none;">
                    <div>
                        <span id="paginationInfo">Menampilkan 0 dari 0 data</span>
                    </div>
                    <nav>
                        <ul class="pagination mb-0" id="paginationList">
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner-border text-light" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#filterUnit').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih Unit',
                allowClear: true
            });

            let currentPage = 1;
            let currentFilters = {};

            // Load data on form submit
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                currentPage = 1;
                loadData();
            });

            // Reset button
            $('#btnReset').on('click', function() {
                $('#filterForm')[0].reset();
                $('#filterUnit').val('').trigger('change');
                currentPage = 1;
                currentFilters = {};
                $('#dataTableBody').html('<tr><td colspan="7" class="text-center"><div class="empty-state"><i class="fas fa-inbox"></i><p>Klik "Tampilkan Data" untuk memuat laporan</p></div></td></tr>');
                $('#aggregatesBox').hide();
                $('#paginationWrapper').hide();
            });

            // Load data function
            function loadData() {
                const filters = {
                    from: $('#filterFrom').val(),
                    to: $('#filterTo').val(),
                    unit_id: $('#filterUnit').val(),
                    search: $('#filterSearch').val(),
                    page: currentPage
                };

                currentFilters = filters;

                $('#loadingOverlay').addClass('active');

                $.ajax({
                    url: '<?= base_url('admin-pusat/laporan/rekap-unit/data') ?>',
                    method: 'GET',
                    data: filters,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            renderTable(response);
                            renderAggregates(response.aggregates);
                            renderPagination(response);
                        } else {
                            showError(response.message || 'Gagal memuat data');
                        }
                    },
                    error: function(xhr) {
                        showError('Terjadi kesalahan saat memuat data');
                        console.error(xhr);
                    },
                    complete: function() {
                        $('#loadingOverlay').removeClass('active');
                    }
                });
            }

            // Render table
            function renderTable(response) {
                const tbody = $('#dataTableBody');
                tbody.empty();

                if (response.rows.length === 0) {
                    tbody.html('<tr><td colspan="7" class="text-center"><div class="empty-state"><i class="fas fa-inbox"></i><p>Tidak ada data ditemukan</p></div></td></tr>');
                    return;
                }

                response.rows.forEach((row, index) => {
                    const no = ((response.page - 1) * response.perPage) + index + 1;
                    const tr = $('<tr>');
                    
                    tr.append($('<td>').text(no));
                    tr.append($('<td>').html('<strong>' + escapeHtml(row.nama_unit) + '</strong>'));
                    tr.append($('<td>').text(formatNumber(row.total_laporan)));
                    tr.append($('<td>').text(formatNumber(row.total_jumlah)));
                    tr.append($('<td>').text(formatCurrency(row.total_nilai)));
                    tr.append($('<td>').text(formatDate(row.first_report)));
                    tr.append($('<td>').text(formatDate(row.last_report)));
                    
                    tbody.append(tr);
                });
            }

            // Render aggregates
            function renderAggregates(aggregates) {
                $('#totalUnits').text(formatNumber(aggregates.total_units || 0));
                $('#totalRecords').text(formatNumber(aggregates.total_records || 0));
                $('#totalJumlah').text(formatNumber(aggregates.sum_jumlah || 0));
                $('#totalNilai').text(formatCurrency(aggregates.sum_total_harga || 0));
                $('#aggregatesBox').show();
            }

            // Render pagination
            function renderPagination(response) {
                const wrapper = $('#paginationWrapper');
                const list = $('#paginationList');
                const info = $('#paginationInfo');

                if (response.total === 0) {
                    wrapper.hide();
                    return;
                }

                const start = ((response.page - 1) * response.perPage) + 1;
                const end = Math.min(response.page * response.perPage, response.total);
                info.text(`Menampilkan ${start}-${end} dari ${response.total} data`);

                list.empty();

                // Previous button
                const prevLi = $('<li>').addClass('page-item').addClass(response.page === 1 ? 'disabled' : '');
                prevLi.append($('<a>').addClass('page-link').attr('href', '#').text('Previous').on('click', function(e) {
                    e.preventDefault();
                    if (response.page > 1) {
                        currentPage = response.page - 1;
                        loadData();
                    }
                }));
                list.append(prevLi);

                // Page numbers
                for (let i = 1; i <= response.totalPages; i++) {
                    const li = $('<li>').addClass('page-item').addClass(i === response.page ? 'active' : '');
                    li.append($('<a>').addClass('page-link').attr('href', '#').text(i).on('click', function(e) {
                        e.preventDefault();
                        currentPage = i;
                        loadData();
                    }));
                    list.append(li);
                }

                // Next button
                const nextLi = $('<li>').addClass('page-item').addClass(response.page >= response.totalPages ? 'disabled' : '');
                nextLi.append($('<a>').addClass('page-link').attr('href', '#').text('Next').on('click', function(e) {
                    e.preventDefault();
                    if (response.page < response.totalPages) {
                        currentPage = response.page + 1;
                        loadData();
                    }
                }));
                list.append(nextLi);

                wrapper.show();
            }

            // Helper functions
            function formatDate(dateString) {
                if (!dateString) return '-';
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' });
            }

            function formatNumber(num) {
                return parseFloat(num || 0).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
            }

            function formatCurrency(num) {
                return 'Rp ' + parseFloat(num || 0).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
            }

            function escapeHtml(text) {
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return text.replace(/[&<>"']/g, m => map[m]);
            }

            function showError(message) {
                alert(message);
            }
        });
    </script>
</body>
</html>
