<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Analisis & Skor UI GreenMetric Transportation' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css" rel="stylesheet">
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
        
        /* Tab Navigation */
        .nav-tabs {
            border-bottom: 2px solid #dee2e6;
            margin-bottom: 25px;
        }
        
        .nav-tabs .nav-link {
            color: #6c757d;
            font-weight: 600;
            font-size: 14px;
            padding: 12px 20px;
            border: none;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }
        
        .nav-tabs .nav-link:hover {
            color: #007bff;
            border-bottom-color: #007bff;
            background: rgba(0, 123, 255, 0.05);
        }
        
        .nav-tabs .nav-link.active {
            color: #007bff;
            background: rgba(0, 123, 255, 0.1);
            border-bottom-color: #007bff;
        }
        
        .nav-tabs .nav-link i {
            margin-right: 8px;
        }
        
        /* Tab Content */
        .tab-content {
            animation: fadeIn 0.3s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Card */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        .card-header {
            padding: 18px 25px;
            font-weight: 600;
            border-bottom: 2px solid rgba(255,255,255,0.2);
        }
        
        .card-body {
            padding: 25px;
        }
        
        /* Stat Cards */
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            position: relative;
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
        
        /* Ratio Display */
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
        
        /* Table */
        .table thead th {
            background: #f8f9fa;
            font-weight: 600;
            font-size: 13px;
        }
        
        .badge {
            font-size: 12px;
            padding: 5px 12px;
        }
        
        /* Score Card */
        .score-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .score-card h1 {
            font-size: 72px;
            font-weight: 700;
            margin: 20px 0;
        }
        
        .score-card p {
            font-size: 18px;
            opacity: 0.9;
        }
        
        /* Progress Bar Custom */
        .progress {
            height: 30px;
            border-radius: 15px;
            background: #e9ecef;
        }
        
        .progress-bar {
            font-size: 14px;
            font-weight: 600;
            line-height: 30px;
        }
        
        /* Alert */
        .alert {
            border-radius: 8px;
            border: none;
        }
        
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .nav-tabs .nav-link {
                font-size: 12px;
                padding: 10px 12px;
            }
        }
    </style>
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <h1><i class="fas fa-chart-line"></i> Analisis & Skor UI GreenMetric Transportation</h1>
                <p>Dashboard terpadu untuk analisis data dan perhitungan skor UI GreenMetric</p>
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

            <!-- Tab Navigation -->
            <ul class="nav nav-tabs" id="analisisTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= !isset($_GET['tab']) || $_GET['tab'] == 'populasi' ? 'active' : '' ?>" 
                            id="populasi-tab" data-bs-toggle="tab" data-bs-target="#populasi" 
                            type="button" role="tab">
                        <i class="fas fa-users"></i> Populasi & Aset
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= isset($_GET['tab']) && $_GET['tab'] == 'infrastruktur' ? 'active' : '' ?>" 
                            id="infrastruktur-tab" data-bs-toggle="tab" data-bs-target="#infrastruktur" 
                            type="button" role="tab">
                        <i class="fas fa-parking"></i> Infrastruktur
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= isset($_GET['tab']) && $_GET['tab'] == 'earchive' ? 'active' : '' ?>" 
                            id="earchive-tab" data-bs-toggle="tab" data-bs-target="#earchive" 
                            type="button" role="tab">
                        <i class="fas fa-folder-open"></i> E-Archive
                    </button>
                </li>
                <!-- TAB 'Dashboard Skor' DIHAPUS - Tidak diperlukan lagi -->
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="analisisTabContent">
                
                <!-- TAB 1: Populasi & Aset -->
                <div class="tab-pane fade <?= !isset($_GET['tab']) || $_GET['tab'] == 'populasi' ? 'show active' : '' ?>" 
                     id="populasi" role="tabpanel">
                    
                    <!-- Action Button -->
                    <div class="mb-3">
                        <button type="button" class="btn btn-primary btn-lg" onclick="openPopulasiModal()">
                            <i class="fas fa-plus-circle"></i> Tambah Data Populasi
                        </button>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <i class="fas fa-user-graduate"></i>
                                <p>Total Mahasiswa</p>
                                <h3 id="stat_mahasiswa"><?= number_format($populasi['jumlah_mahasiswa'] ?? 0) ?></h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <i class="fas fa-chalkboard-teacher"></i>
                                <p>Total Dosen</p>
                                <h3 id="stat_dosen"><?= number_format($populasi['jumlah_dosen'] ?? 0) ?></h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                <i class="fas fa-user-tie"></i>
                                <p>Total Staf</p>
                                <h3 id="stat_staf"><?= number_format($populasi['jumlah_staf'] ?? 0) ?></h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                                <i class="fas fa-users"></i>
                                <p>Total Populasi</p>
                                <h3 id="stat_total_populasi"><?= number_format($total_populasi) ?></h3>
                                <small class="text-white fst-italic" style="font-size: 0.75rem; opacity: 0.9;">
                                    <i class="fas fa-calculator"></i> Formula: Mahasiswa + Dosen + Staf
                                </small>
                            </div>
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
                                            <td class="text-end">
                                                <h5 class="text-success"><?= number_format($total_zev) ?> Unit</h5>
                                                <small class="text-muted fst-italic">
                                                    <i class="fas fa-calculator"></i> Persentase: (<?= number_format($total_zev) ?> / <?= number_format($total_kendaraan) ?>) × 100% = <?= $total_kendaraan > 0 ? number_format(($total_zev / $total_kendaraan) * 100, 2) : 0 ?>%
                                                </small>
                                            </td>
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
                                <small class="fst-italic" style="opacity: 0.9;">
                                    <i class="fas fa-calculator"></i> Formula: (<?= number_format($total_kendaraan) ?> / <?= number_format($total_populasi) ?>) × 1000
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: Infrastruktur -->
                <div class="tab-pane fade <?= isset($_GET['tab']) && $_GET['tab'] == 'infrastruktur' ? 'show active' : '' ?>" 
                     id="infrastruktur" role="tabpanel">
                    
                    <?= $this->include('admin_pusat/transportation/tabs/infrastruktur_tab') ?>
                </div>

                <!-- TAB 3: E-Archive -->
                <div class="tab-pane fade <?= isset($_GET['tab']) && $_GET['tab'] == 'earchive' ? 'show active' : '' ?>" 
                     id="earchive" role="tabpanel">
                    
                    <?= $this->include('admin_pusat/transportation/tabs/earchive_tab') ?>
                </div>

                <!-- TAB 4: Dashboard Skor - DIHAPUS (Tidak diperlukan lagi) -->
                <!-- 
                <div class="tab-pane fade" id="dashboard" role="tabpanel">
                    <?= $this->include('admin_pusat/transportation/tabs/dashboard_tab') ?>
                </div>
                -->
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    
    <!-- Modal Input Data Populasi -->
    <div class="modal fade" id="modalPopulasi" tabindex="-1" aria-labelledby="modalPopulasiLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="modal-title" id="modalPopulasiLabel">
                        <i class="fas fa-users"></i> Input Data Populasi Kampus
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formPopulasi">
                        <?= csrf_field() ?>
                        <input type="hidden" name="redirect_tab" value="populasi">
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="modal_jumlah_mahasiswa" class="form-label fw-bold">
                                    <i class="fas fa-user-graduate"></i> Jumlah Mahasiswa <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="modal_jumlah_mahasiswa" 
                                       name="jumlah_mahasiswa" value="<?= $populasi['jumlah_mahasiswa'] ?? '' ?>" 
                                       placeholder="Contoh: 15000" required>
                                <small class="text-muted">Total mahasiswa aktif</small>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="modal_jumlah_dosen" class="form-label fw-bold">
                                    <i class="fas fa-chalkboard-teacher"></i> Jumlah Dosen <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="modal_jumlah_dosen" 
                                       name="jumlah_dosen" value="<?= $populasi['jumlah_dosen'] ?? '' ?>" 
                                       placeholder="Contoh: 500" required>
                                <small class="text-muted">Total dosen tetap & tidak tetap</small>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="modal_jumlah_staf" class="form-label fw-bold">
                                    <i class="fas fa-user-tie"></i> Jumlah Staf <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="modal_jumlah_staf" 
                                       name="jumlah_staf" value="<?= $populasi['jumlah_staf'] ?? '' ?>" 
                                       placeholder="Contoh: 300" required>
                                <small class="text-muted">Total staf administrasi</small>
                            </div>
                        </div>
                        
                        <div class="row g-3 mt-2">
                            <div class="col-md-12">
                                <label for="modal_tahun" class="form-label fw-bold">
                                    <i class="fas fa-calendar"></i> Tahun Data <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="modal_tahun" name="tahun" required>
                                    <?php 
                                    $currentYear = date('Y');
                                    for ($i = $currentYear - 2; $i <= $currentYear + 1; $i++): 
                                    ?>
                                        <option value="<?= $i ?>" <?= (isset($populasi['tahun']) && $populasi['tahun'] == $i) || $i == $currentYear ? 'selected' : '' ?>>
                                            <?= $i ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                                <small class="text-muted">Tahun akademik data populasi</small>
                            </div>
                        </div>
                        
                        <!-- Preview Total -->
                        <div class="alert alert-info mt-3">
                            <h6 class="mb-2"><i class="fas fa-calculator"></i> Preview Total Populasi:</h6>
                            <h3 class="mb-0" id="preview_total_populasi">0</h3>
                            <small class="text-muted">Mahasiswa + Dosen + Staf</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="button" class="btn btn-primary" onclick="simpanPopulasi()" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                        <i class="fas fa-save"></i> Simpan Data
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Auto-dismiss alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Remember active tab
        document.querySelectorAll('#analisisTab button').forEach(button => {
            button.addEventListener('click', function() {
                const tabName = this.getAttribute('data-bs-target').replace('#', '');
                localStorage.setItem('activeAnalisisTab', tabName);
            });
        });

        // Restore active tab on page load
        window.addEventListener('load', function() {
            const activeTab = localStorage.getItem('activeAnalisisTab');
            if (activeTab && !window.location.search.includes('tab=')) {
                const tabButton = document.querySelector(`button[data-bs-target="#${activeTab}"]`);
                if (tabButton) {
                    const tab = new bootstrap.Tab(tabButton);
                    tab.show();
                }
            }
        });
        
        // Open Populasi Modal
        function openPopulasiModal() {
            var modal = new bootstrap.Modal(document.getElementById('modalPopulasi'));
            modal.show();
            updatePreviewPopulasi();
        }
        
        // Update Preview Total Populasi
        function updatePreviewPopulasi() {
            const mahasiswa = parseInt($('#modal_jumlah_mahasiswa').val()) || 0;
            const dosen = parseInt($('#modal_jumlah_dosen').val()) || 0;
            const staf = parseInt($('#modal_jumlah_staf').val()) || 0;
            const total = mahasiswa + dosen + staf;
            $('#preview_total_populasi').text(total.toLocaleString('id-ID'));
        }
        
        // Auto-update preview when input changes
        $(document).ready(function() {
            $('#modal_jumlah_mahasiswa, #modal_jumlah_dosen, #modal_jumlah_staf').on('input', function() {
                updatePreviewPopulasi();
            });
        });
        
        // Simpan Data Populasi
        function simpanPopulasi() {
            const form = document.getElementById('formPopulasi');
            
            // Validation
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            // Show loading
            Swal.fire({
                title: 'Menyimpan Data...',
                html: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Get form data
            const formData = new FormData(form);
            
            // Submit via AJAX
            fetch('<?= base_url('/admin-pusat/transportation/simpan-populasi') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                // Close modal
                var modal = bootstrap.Modal.getInstance(document.getElementById('modalPopulasi'));
                modal.hide();
                
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data populasi berhasil disimpan',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Reload page to update statistics
                    window.location.reload();
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Terjadi kesalahan saat menyimpan data',
                    confirmButtonText: 'OK'
                });
            });  
        }
    </script>
</body>
</html>
