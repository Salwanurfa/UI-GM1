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
    <title><?= $title ?? 'Manajemen Master Data Transportasi' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/dashboard.css') ?>" rel="stylesheet">
    <link href="<?= base_url('/css/mobile-responsive.css') ?>" rel="stylesheet">
    <style>
        /* Maximize width for full horizontal layout */
        .main-content {
            margin-left: 280px;
            padding: 25px 30px;
            min-height: 100vh;
            width: calc(100% - 280px);
            transition: margin-left 0.3s ease;
            background: #f4f6f9;
        }
        
        /* Page header styling */
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
        
        /* Card styling - full width with proper spacing */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 30px;
            overflow: hidden;
            width: 100%;
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
        
        /* Form input - optimized for full width */
        .add-form {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 20px 25px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }
        
        .add-form:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .add-form h6 {
            font-size: 14px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 12px;
        }
        
        .add-form .form-control,
        .add-form .form-select {
            font-size: 14px;
            height: 38px;
            border-radius: 6px;
        }
        
        .add-form .btn {
            height: 38px;
            font-size: 14px;
            font-weight: 600;
        }
        
        /* Table styling - optimized for full width */
        .table {
            font-size: 14px;
            margin-bottom: 0;
            width: 100%;
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
        
        /* Badge styling with icons */
        .badge {
            font-size: 12px;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 4px;
        }
        
        .badge i {
            margin-right: 4px;
        }
        
        /* Button group with text labels */
        .btn-group-sm .btn {
            padding: 6px 12px;
            font-size: 13px;
            font-weight: 500;
        }
        
        .btn-group-sm .btn i {
            margin-right: 4px;
        }
        
        /* DataTables wrapper adjustments */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            font-size: 13px;
            margin-bottom: 15px;
        }
        
        .dataTables_wrapper .dataTables_length select {
            font-size: 13px;
            padding: 4px 8px;
        }
        
        .dataTables_wrapper .dataTables_filter input {
            font-size: 13px;
            padding: 4px 8px;
        }
        
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            font-size: 13px;
            margin-top: 15px;
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
        
        /* Info box at bottom */
        .alert-info {
            background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
            color: #0c5460;
            border-left: 4px solid #17a2b8;
        }
        
        /* Responsive adjustments */
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 20px 15px;
            }
            
            .card-body {
                padding: 20px 15px;
            }
            
            .add-form {
                padding: 15px;
            }
            
            .table {
                font-size: 13px;
            }
            
            .table thead th {
                font-size: 12px;
                padding: 10px 8px;
            }
            
            .table tbody td {
                padding: 10px 8px;
            }
        }
        
        /* Ensure full width utilization */
        .container-fluid {
            padding-left: 0;
            padding-right: 0;
            max-width: 100%;
        }
        
        .row {
            margin-left: 0;
            margin-right: 0;
        }
        
        .row > [class*='col-'] {
            padding-left: 0;
            padding-right: 0;
        }
        
        /* Table responsive wrapper */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin: 0;
        }
    </style>
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <h1><i class="fas fa-database"></i> Manajemen Master Data Transportasi</h1>
                <p>Kelola kategori kendaraan dan jenis bahan bakar yang tersedia di sistem</p>
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

            <!-- Vertical Layout - Full Width Tables -->
            
            <!-- CARD 1: Kategori Kendaraan (Full Width) -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h3><i class="fas fa-car"></i> Manajemen Kategori Kendaraan</h3>
                        </div>
                        <div class="card-body">
                            <!-- Form Tambah Kategori -->
                            <div class="add-form" id="formKategoriContainer">
                                <h6 id="formKategoriTitle"><i class="fas fa-plus-circle"></i> Tambah Kategori Baru</h6>
                                <form id="formKategori" method="POST" action="<?= base_url('/admin-pusat/transportation/tambah-kategori') ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" id="category_id" name="id" value="">
                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                        <div class="flex-grow-1" style="min-width: 250px;">
                                            <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" 
                                                   placeholder="Contoh: Bus Kampus, Sepeda Listrik, dll." required>
                                        </div>
                                        <div style="min-width: 150px;">
                                            <select class="form-select" id="is_zev_category" name="is_zev">
                                                <option value="0">Non-ZEV</option>
                                                <option value="1">ZEV</option>
                                            </select>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-success" id="btnSubmitCategory">
                                                <i class="fas fa-plus"></i> Simpan
                                            </button>
                                            <button type="button" class="btn btn-secondary d-none" id="btnCancelCategory" onclick="resetCategoryForm()">
                                                <i class="fas fa-times"></i> Batal
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Tabel Kategori - Full Width -->
                            <div class="table-responsive">
                                <table class="table table-hover table-sm" id="categoryTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 60px;">No</th>
                                            <th>Nama Kategori Kendaraan</th>
                                            <th style="width: 120px;">Tipe ZEV</th>
                                            <th style="width: 150px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach ($categories as $cat): ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td><strong><?= esc($cat['nama_kategori']) ?></strong></td>
                                            <td>
                                                <?php if ($cat['is_zev'] == 1): ?>
                                                    <span class="badge bg-success"><i class="fas fa-leaf"></i> ZEV</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><i class="fas fa-gas-pump"></i> Non-ZEV</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-warning" 
                                                            onclick="editCategory(<?= htmlspecialchars(json_encode($cat), ENT_QUOTES, 'UTF-8') ?>)"
                                                            title="Edit Kategori">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    <button type="button" class="btn btn-danger" 
                                                            onclick="deleteCategory(<?= $cat['id'] ?>, '<?= esc($cat['nama_kategori']) ?>')"
                                                            title="Hapus Kategori">
                                                        <i class="fas fa-trash-alt"></i> Hapus
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD 2: Jenis Bahan Bakar (Full Width) -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h3><i class="fas fa-gas-pump"></i> Manajemen Jenis Bahan Bakar</h3>
                        </div>
                        <div class="card-body">
                            <!-- Form Tambah Bahan Bakar -->
                            <div class="add-form" id="formBahanBakarContainer">
                                <h6 id="formBahanBakarTitle"><i class="fas fa-plus-circle"></i> Tambah Bahan Bakar Baru</h6>
                                <form id="formBahanBakar" method="POST" action="<?= base_url('/admin-pusat/transportation/tambah-bahan-bakar') ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" id="fuel_id" name="id" value="">
                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                        <div class="flex-grow-1" style="min-width: 250px;">
                                            <input type="text" class="form-control" id="nama_bahan_bakar" name="nama_bahan_bakar" 
                                                   placeholder="Contoh: Hydrogen, Solar Panel, Biodiesel, dll." required>
                                        </div>
                                        <div style="min-width: 150px;">
                                            <select class="form-select" id="is_zev_fuel" name="is_zev">
                                                <option value="0">Non-ZEV</option>
                                                <option value="1">ZEV</option>
                                            </select>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary" id="btnSubmitFuel">
                                                <i class="fas fa-plus"></i> Simpan
                                            </button>
                                            <button type="button" class="btn btn-secondary d-none" id="btnCancelFuel" onclick="resetFuelForm()">
                                                <i class="fas fa-times"></i> Batal
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Tabel Bahan Bakar - Full Width -->
                            <div class="table-responsive">
                                <table class="table table-hover table-sm" id="fuelTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 60px;">No</th>
                                            <th>Nama Jenis Bahan Bakar</th>
                                            <th style="width: 120px;">Tipe ZEV</th>
                                            <th style="width: 150px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach ($fuels as $fuel): ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td><strong><?= esc($fuel['nama_bahan_bakar']) ?></strong></td>
                                            <td>
                                                <?php if ($fuel['is_zev'] == 1): ?>
                                                    <span class="badge bg-success"><i class="fas fa-leaf"></i> ZEV</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><i class="fas fa-gas-pump"></i> Non-ZEV</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-warning" 
                                                            onclick="editFuel(<?= htmlspecialchars(json_encode($fuel), ENT_QUOTES, 'UTF-8') ?>)"
                                                            title="Edit Bahan Bakar">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    <button type="button" class="btn btn-danger" 
                                                            onclick="deleteFuel(<?= $fuel['id'] ?>, '<?= esc($fuel['nama_bahan_bakar']) ?>')"
                                                            title="Hapus Bahan Bakar">
                                                        <i class="fas fa-trash-alt"></i> Hapus
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Catatan:</strong> Data yang ditambahkan di sini akan muncul sebagai pilihan di form input transportasi Security. 
                Pastikan nama kategori dan bahan bakar jelas dan mudah dipahami.
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
            $('#categoryTable').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
                pageLength: 10,
                order: [[1, 'asc']]
            });
            
            $('#fuelTable').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
                pageLength: 10,
                order: [[1, 'asc']]
            });
        });

        // ========== KATEGORI KENDARAAN ==========
        
        // Edit Category
        function editCategory(data) {
            // Populate form fields
            $('#category_id').val(data.id);
            $('#nama_kategori').val(data.nama_kategori);
            $('#is_zev_category').val(data.is_zev);
            
            // Change form title
            $('#formKategoriTitle').html('<i class="fas fa-edit"></i> Edit Kategori Kendaraan');
            
            // Change button to Update mode (orange/warning color)
            $('#btnSubmitCategory')
                .removeClass('btn-success')
                .addClass('btn-warning')
                .html('<i class="fas fa-save"></i> Update');
            
            // Show Cancel button
            $('#btnCancelCategory').removeClass('d-none');
            
            // Highlight form container
            $('#formKategoriContainer')
                .css('border', '3px solid #ffc107')
                .css('box-shadow', '0 0 15px rgba(255, 193, 7, 0.5)');
            
            // Scroll to form smoothly
            $('html, body').animate({
                scrollTop: $('#formKategoriContainer').offset().top - 100
            }, 500);
            
            // Show notification
            Swal.fire({
                icon: 'info',
                title: 'Mode Edit Aktif',
                text: `Anda sedang mengedit: "${data.nama_kategori}"`,
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }

        // Reset Category Form
        function resetCategoryForm() {
            // Clear form fields
            $('#category_id').val('');
            $('#nama_kategori').val('');
            $('#is_zev_category').val('0');
            
            // Reset form title
            $('#formKategoriTitle').html('<i class="fas fa-plus-circle"></i> Tambah Kategori Baru');
            
            // Reset button to Add mode (green)
            $('#btnSubmitCategory')
                .removeClass('btn-warning')
                .addClass('btn-success')
                .html('<i class="fas fa-plus"></i> Simpan');
            
            // Hide Cancel button
            $('#btnCancelCategory').addClass('d-none');
            
            // Remove highlight
            $('#formKategoriContainer')
                .css('border', '1px solid #dee2e6')
                .css('box-shadow', 'none');
            
            // Show notification
            Swal.fire({
                icon: 'success',
                title: 'Form Direset',
                text: 'Form kembali ke mode tambah data baru',
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }

        // Delete Category
        function deleteCategory(id, nama) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                html: `Yakin ingin menghapus kategori:<br><strong>"${nama}"</strong>?`,
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
                    
                    window.location.href = '<?= base_url('/admin-pusat/transportation/hapus-kategori/') ?>' + id;
                }
            });
        }

        // ========== BAHAN BAKAR ==========
        
        // Edit Fuel
        function editFuel(data) {
            // Populate form fields
            $('#fuel_id').val(data.id);
            $('#nama_bahan_bakar').val(data.nama_bahan_bakar);
            $('#is_zev_fuel').val(data.is_zev);
            
            // Change form title
            $('#formBahanBakarTitle').html('<i class="fas fa-edit"></i> Edit Bahan Bakar');
            
            // Change button to Update mode (orange/warning color)
            $('#btnSubmitFuel')
                .removeClass('btn-primary')
                .addClass('btn-warning')
                .html('<i class="fas fa-save"></i> Update');
            
            // Show Cancel button
            $('#btnCancelFuel').removeClass('d-none');
            
            // Highlight form container
            $('#formBahanBakarContainer')
                .css('border', '3px solid #ffc107')
                .css('box-shadow', '0 0 15px rgba(255, 193, 7, 0.5)');
            
            // Scroll to form smoothly
            $('html, body').animate({
                scrollTop: $('#formBahanBakarContainer').offset().top - 100
            }, 500);
            
            // Show notification
            Swal.fire({
                icon: 'info',
                title: 'Mode Edit Aktif',
                text: `Anda sedang mengedit: "${data.nama_bahan_bakar}"`,
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }

        // Reset Fuel Form
        function resetFuelForm() {
            // Clear form fields
            $('#fuel_id').val('');
            $('#nama_bahan_bakar').val('');
            $('#is_zev_fuel').val('0');
            
            // Reset form title
            $('#formBahanBakarTitle').html('<i class="fas fa-plus-circle"></i> Tambah Bahan Bakar Baru');
            
            // Reset button to Add mode (blue)
            $('#btnSubmitFuel')
                .removeClass('btn-warning')
                .addClass('btn-primary')
                .html('<i class="fas fa-plus"></i> Simpan');
            
            // Hide Cancel button
            $('#btnCancelFuel').addClass('d-none');
            
            // Remove highlight
            $('#formBahanBakarContainer')
                .css('border', '1px solid #dee2e6')
                .css('box-shadow', 'none');
            
            // Show notification
            Swal.fire({
                icon: 'success',
                title: 'Form Direset',
                text: 'Form kembali ke mode tambah data baru',
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }

        // Delete Fuel
        function deleteFuel(id, nama) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                html: `Yakin ingin menghapus bahan bakar:<br><strong>"${nama}"</strong>?`,
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
                    
                    window.location.href = '<?= base_url('/admin-pusat/transportation/hapus-bahan-bakar/') ?>' + id;
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
