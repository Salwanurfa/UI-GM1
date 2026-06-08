<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Indikator Efisiensi Transportasi' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/dashboard.css') ?>" rel="stylesheet">
    <style>
        /* Full width layout */
        .main-content {
            margin-left: 280px;
            padding: 25px 30px;
            min-height: 100vh;
            width: calc(100% - 280px);
            transition: margin-left 0.3s ease;
            background: #f4f6f9;
        }
        
        /* Page header */
        .page-header {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .page-header h1 {
            font-size: 26px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .page-header p {
            font-size: 14px;
            color: #7f8c8d;
            margin: 0;
        }
        
        /* Card styling */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .card-header {
            padding: 18px 25px;
            border-bottom: 2px solid rgba(255,255,255,0.2);
            font-weight: 600;
        }
        
        .card-header h3 {
            font-size: 18px;
            margin: 0;
            font-weight: 600;
        }
        
        .card-body {
            padding: 25px;
        }
        
        /* Form styling */
        .form-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 20px 25px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }
        
        .form-section:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .form-section h6 {
            font-size: 14px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 15px;
        }
        
        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }
        
        .form-control, .form-select {
            font-size: 14px;
            border-radius: 6px;
        }
        
        /* Table styling */
        .table {
            font-size: 14px;
            margin-bottom: 0;
        }
        
        .table thead th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #dee2e6;
            padding: 14px 12px;
            white-space: nowrap;
        }
        
        .table tbody td {
            padding: 12px;
            vertical-align: middle;
            color: #495057;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        /* Badge styling */
        .badge {
            font-size: 12px;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 4px;
        }
        
        .badge i {
            margin-right: 4px;
        }
        
        /* Button styling */
        .btn {
            font-size: 14px;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 6px;
        }
        
        .btn-group-sm .btn {
            padding: 6px 12px;
            font-size: 13px;
        }
        
        .btn-group-sm .btn i {
            margin-right: 4px;
        }
        
        /* Alert styling */
        .alert {
            font-size: 14px;
            padding: 12px 20px;
            border-radius: 8px;
            border: none;
            margin-bottom: 20px;
        }
        
        .alert i {
            margin-right: 8px;
        }
        
        /* Info box */
        .info-box {
            background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
            color: #0c5460;
            border-left: 4px solid #17a2b8;
            padding: 15px 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        
        .info-box i {
            margin-right: 10px;
            font-size: 18px;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <h1><i class="fas fa-chart-line"></i> Indikator Efisiensi Transportasi</h1>
                <p>Kelola target dan indikator efisiensi untuk setiap kategori kendaraan</p>
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

            <!-- Form Card -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h3><i class="fas fa-gauge"></i> Pengaturan Indikator Efisiensi Transportasi</h3>
                </div>
                <div class="card-body">
                    <div class="form-section" id="formIndicatorContainer">
                        <h6 id="formIndicatorTitle"><i class="fas fa-plus-circle"></i> Tambah Indikator Baru</h6>
                        <form id="formIndicator" method="POST" action="<?= base_url('/admin-pusat/transportation/simpan-indikator') ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" id="indicator_id" name="id" value="">
                            
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="kategori_kendaraan" class="form-label">
                                        <i class="fas fa-car"></i> Kategori Kendaraan
                                    </label>
                                    <select class="form-select" id="kategori_kendaraan" name="kategori_kendaraan" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= esc($cat['nama_kategori']) ?>">
                                                <?= esc($cat['nama_kategori']) ?>
                                                <?= $cat['is_zev'] == 1 ? '(ZEV)' : '' ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-2">
                                    <label for="target_konsumsi" class="form-label">
                                        <i class="fas fa-bullseye"></i> Target Konsumsi
                                    </label>
                                    <input type="number" step="0.01" class="form-control" id="target_konsumsi" 
                                           name="target_konsumsi" placeholder="Contoh: 5.5" required>
                                </div>
                                
                                <div class="col-md-2">
                                    <label for="satuan" class="form-label">
                                        <i class="fas fa-ruler"></i> Satuan
                                    </label>
                                    <select class="form-select" id="satuan" name="satuan" required>
                                        <option value="">-- Pilih Satuan --</option>
                                        <option value="Liter/KM">Liter/KM</option>
                                        <option value="kWh/KM">kWh/KM</option>
                                        <option value="Gram CO2/KM">Gram CO2/KM</option>
                                        <option value="KM/Liter">KM/Liter</option>
                                        <option value="KM/kWh">KM/kWh</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-2">
                                    <label for="tahun" class="form-label">
                                        <i class="fas fa-calendar"></i> Tahun/Periode
                                    </label>
                                    <select class="form-select" id="tahun" name="tahun" required>
                                        <option value="">-- Pilih Tahun --</option>
                                        <?php 
                                        $currentYear = date('Y');
                                        for ($i = $currentYear - 2; $i <= $currentYear + 5; $i++): 
                                        ?>
                                            <option value="<?= $i ?>" <?= $i == $currentYear ? 'selected' : '' ?>>
                                                <?= $i ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-info" id="btnSubmitIndicator">
                                            <i class="fas fa-save"></i> Simpan Indikator
                                        </button>
                                        <button type="button" class="btn btn-secondary d-none" id="btnCancelIndicator" onclick="resetIndicatorForm()">
                                            <i class="fas fa-times"></i> Batal
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3><i class="fas fa-table"></i> Monitoring Indikator Efisiensi</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm" id="indicatorTable">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">No</th>
                                    <th>Kategori Kendaraan</th>
                                    <th style="width: 150px;">Target Konsumsi</th>
                                    <th style="width: 120px;">Satuan</th>
                                    <th style="width: 100px;">Tahun</th>
                                    <th style="width: 120px;">Status</th>
                                    <th style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($indicators)): ?>
                                    <?php $no = 1; foreach ($indicators as $ind): ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td><strong><?= esc($ind['kategori_kendaraan']) ?></strong></td>
                                        <td class="text-center">
                                            <span class="badge bg-info" style="font-size: 14px;">
                                                <?= number_format($ind['target_konsumsi'], 2) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">
                                                <?= esc($ind['satuan']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-dark">
                                                <i class="fas fa-calendar"></i> <?= esc($ind['tahun']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($ind['status_aktif'] == 1): ?>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle"></i> Aktif
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-pause-circle"></i> Nonaktif
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-warning" 
                                                        onclick="editIndicator(<?= htmlspecialchars(json_encode($ind), ENT_QUOTES, 'UTF-8') ?>)"
                                                        title="Edit Indikator">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <button type="button" class="btn btn-danger" 
                                                        onclick="deleteIndicator(<?= $ind['id'] ?>, '<?= esc($ind['kategori_kendaraan']) ?>')"
                                                        title="Hapus Indikator">
                                                    <i class="fas fa-trash-alt"></i> Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Belum ada indikator yang ditambahkan</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="info-box">
                <i class="fas fa-info-circle"></i>
                <strong>Catatan:</strong> Indikator efisiensi digunakan untuk mengukur performa kendaraan kampus. 
                Target konsumsi yang lebih rendah menunjukkan efisiensi yang lebih baik. 
                Data ini akan digunakan dalam perhitungan skor UI GreenMetric.
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Initialize DataTables
        $(document).ready(function() {
            $('#indicatorTable').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
                pageLength: 10,
                order: [[4, 'desc'], [1, 'asc']] // Sort by year desc, then category asc
            });
        });

        // Edit Indicator
        function editIndicator(data) {
            // Populate form fields
            $('#indicator_id').val(data.id);
            $('#kategori_kendaraan').val(data.kategori_kendaraan);
            $('#target_konsumsi').val(data.target_konsumsi);
            $('#satuan').val(data.satuan);
            $('#tahun').val(data.tahun);
            
            // Change form title
            $('#formIndicatorTitle').html('<i class="fas fa-edit"></i> Edit Indikator Efisiensi');
            
            // Change button to Update mode
            $('#btnSubmitIndicator')
                .removeClass('btn-info')
                .addClass('btn-warning')
                .html('<i class="fas fa-save"></i> Update Indikator');
            
            // Show Cancel button
            $('#btnCancelIndicator').removeClass('d-none');
            
            // Highlight form container
            $('#formIndicatorContainer')
                .css('border', '3px solid #ffc107')
                .css('box-shadow', '0 0 15px rgba(255, 193, 7, 0.5)');
            
            // Scroll to form
            $('html, body').animate({
                scrollTop: $('#formIndicatorContainer').offset().top - 100
            }, 500);
            
            // Show notification
            Swal.fire({
                icon: 'info',
                title: 'Mode Edit Aktif',
                text: `Anda sedang mengedit indikator: "${data.kategori_kendaraan}"`,
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }

        // Reset Indicator Form
        function resetIndicatorForm() {
            // Clear form fields
            $('#indicator_id').val('');
            $('#kategori_kendaraan').val('');
            $('#target_konsumsi').val('');
            $('#satuan').val('');
            $('#tahun').val('<?= date('Y') ?>');
            
            // Reset form title
            $('#formIndicatorTitle').html('<i class="fas fa-plus-circle"></i> Tambah Indikator Baru');
            
            // Reset button to Add mode
            $('#btnSubmitIndicator')
                .removeClass('btn-warning')
                .addClass('btn-info')
                .html('<i class="fas fa-save"></i> Simpan Indikator');
            
            // Hide Cancel button
            $('#btnCancelIndicator').addClass('d-none');
            
            // Remove highlight
            $('#formIndicatorContainer')
                .css('border', '1px solid #dee2e6')
                .css('box-shadow', 'none');
            
            // Show notification
            Swal.fire({
                icon: 'success',
                title: 'Form Direset',
                text: 'Form kembali ke mode tambah indikator baru',
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }

        // Delete Indicator
        function deleteIndicator(id, kategori) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                html: `Yakin ingin menghapus indikator untuk:<br><strong>"${kategori}"</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '<i class="fas fa-trash"></i> Ya, Hapus!',
                cancelButtonText: '<i class="fas fa-times"></i> Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Menghapus...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    window.location.href = '<?= base_url('/admin-pusat/transportation/hapus-indikator/') ?>' + id;
                }
            });
        }

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
