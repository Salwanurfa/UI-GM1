<?php
/**
 * Log Harian Kendaraan - Admin Pusat
 * Mencatat akumulasi kendaraan keluar-masuk harian
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Log Harian Kendaraan' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-content {
            margin-left: 280px;
            padding: 30px;
            min-height: 100vh;
        }

        .page-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .page-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }

        .page-header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }

        /* Summary Cards */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .summary-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border-left: 4px solid;
            transition: all 0.3s ease;
        }

        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .summary-card.blue { border-left-color: #007bff; }
        .summary-card.green { border-left-color: #28a745; }
        .summary-card.orange { border-left-color: #fd7e14; }

        .summary-card .icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin-bottom: 15px;
        }

        .summary-card.blue .icon { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .summary-card.green .icon { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
        .summary-card.orange .icon { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }

        .summary-card h3 {
            font-size: 32px;
            font-weight: 700;
            margin: 0;
            color: #2c3e50;
        }

        .summary-card p {
            margin: 5px 0 0 0;
            color: #6c757d;
            font-weight: 500;
        }

        /* Form Card */
        .form-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .form-card h3 {
            color: #1e3c72;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #1e3c72;
        }

        /* Table Card */
        .table-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .table-card h3 {
            color: #1e3c72;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #1e3c72;
        }

        /* DataTable Styling */
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 8px 12px;
        }

        .table thead th {
            background: #1e3c72;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
            border: none;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background: #f8f9fa;
            transform: translateX(2px);
        }

        /* Buttons */
        .btn {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
        }

        .btn-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border: none;
        }

        /* Badge */
        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }

            .summary-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fas fa-clipboard-list"></i> Log Harian Kendaraan</h1>
            <p>Mencatat akumulasi kendaraan keluar-masuk kampus setiap hari</p>
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

        <!-- Summary Cards -->
        <div class="summary-cards">
            <!-- Total Masuk Hari Ini -->
            <div class="summary-card blue">
                <div class="icon">
                    <i class="fas fa-arrow-right"></i>
                </div>
                <h3><?= number_format($total_masuk_hari_ini ?? 0) ?></h3>
                <p>Total Masuk Hari Ini</p>
                <small class="text-muted"><?= date('d F Y') ?></small>
            </div>

            <!-- Total Keluar Hari Ini -->
            <div class="summary-card green">
                <div class="icon">
                    <i class="fas fa-arrow-left"></i>
                </div>
                <h3><?= number_format($total_keluar_hari_ini ?? 0) ?></h3>
                <p>Total Keluar Hari Ini</p>
                <small class="text-muted"><?= date('d F Y') ?></small>
            </div>

            <!-- Total Aktivitas -->
            <div class="summary-card orange">
                <div class="icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <h3><?= number_format(($total_masuk_hari_ini ?? 0) + ($total_keluar_hari_ini ?? 0)) ?></h3>
                <p>Total Aktivitas Hari Ini</p>
                <small class="text-muted">Masuk + Keluar</small>
            </div>
        </div>

        <!-- Form Input -->
        <div class="form-card">
            <h3><i class="fas fa-plus-circle"></i> Input Log Harian</h3>
            <form action="<?= base_url('/admin-pusat/transportation/simpan-log-harian') ?>" method="POST" id="formLogHarian">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="log_id" value="">
                
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label"><i class="fas fa-calendar"></i> Tanggal *</label>
                        <input type="date" class="form-control" name="tanggal" id="tanggal" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label"><i class="fas fa-car"></i> Jenis Kendaraan *</label>
                        <select class="form-select" name="jenis_kendaraan" id="jenis_kendaraan" required>
                            <option value="">Pilih Jenis</option>
                            <option value="Mobil">Mobil</option>
                            <option value="Motor">Motor</option>
                            <option value="Sepeda">Sepeda</option>
                            <option value="Bus">Bus</option>
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label"><i class="fas fa-arrow-right"></i> Jumlah Masuk *</label>
                        <input type="number" class="form-control" name="jumlah_masuk" id="jumlah_masuk" value="0" min="0" required>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label"><i class="fas fa-arrow-left"></i> Jumlah Keluar *</label>
                        <input type="number" class="form-control" name="jumlah_keluar" id="jumlah_keluar" value="0" min="0" required>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save"></i> Simpan Log
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label"><i class="fas fa-sticky-note"></i> Keterangan (Opsional)</label>
                        <textarea class="form-control" name="keterangan" id="keterangan" rows="2" placeholder="Catatan tambahan..."></textarea>
                    </div>
                </div>

                <button type="button" class="btn btn-secondary" onclick="resetForm()">
                    <i class="fas fa-redo"></i> Reset Form
                </button>
            </form>
        </div>

        <!-- Table Monitoring -->
        <div class="table-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3><i class="fas fa-table"></i> Data Log Harian</h3>
                <div class="btn-group">
                    <button type="button" class="btn btn-warning" onclick="backupLogs()">
                        <i class="fas fa-sync"></i> Back-up ke Laporan Bulanan
                    </button>
                    <a href="<?= base_url('/admin-pusat/transportation/export-log-harian-excel') ?>" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    <a href="<?= base_url('/admin-pusat/transportation/export-log-harian-pdf') ?>" class="btn btn-danger" target="_blank">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover" id="logTable">
                    <thead>
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 12%;">Tanggal</th>
                            <th style="width: 15%;">Jenis Kendaraan</th>
                            <th style="width: 10%;">Masuk</th>
                            <th style="width: 10%;">Keluar</th>
                            <th style="width: 12%;">Total Aktivitas</th>
                            <th style="width: 26%;">Keterangan</th>
                            <th style="width: 10%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($all_logs)): ?>
                            <?php $no = 1; foreach ($all_logs as $log): ?>
                            <tr <?= (!empty($log['is_backed_up']) && $log['is_backed_up'] == 1) ? 'class="table-secondary"' : '' ?>>
                                <td class="text-center"><?= $no++ ?></td>
                                <td>
                                    <?= date('d/m/Y', strtotime($log['tanggal'])) ?>
                                    <?php if (!empty($log['is_backed_up']) && $log['is_backed_up'] == 1): ?>
                                        <br><small class="badge bg-success">Sudah di-backup</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?= esc($log['jenis_kendaraan']) ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info"><?= number_format($log['jumlah_masuk']) ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success"><?= number_format($log['jumlah_keluar']) ?></span>
                                </td>
                                <td class="text-center">
                                    <strong><?= number_format($log['jumlah_masuk'] + $log['jumlah_keluar']) ?></strong>
                                </td>
                                <td><?= esc($log['keterangan'] ?? '-') ?></td>
                                <td class="text-center">
                                    <?php if (empty($log['is_backed_up']) || $log['is_backed_up'] == 0): ?>
                                    <button class="btn btn-sm btn-warning" onclick="editLog(<?= $log['id'] ?>)" title="Edit Log">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <?php else: ?>
                                    <span class="text-muted small">
                                        <i class="fas fa-lock"></i> Terkunci
                                    </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>Belum ada data log harian</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Initialize DataTable
        $(document).ready(function() {
            $('#logTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
                },
                order: [[1, 'desc']], // Sort by date descending
                pageLength: 25
            });
        });

        // Reset Form - Clear all fields and reset to add mode
        function resetForm() {
            document.getElementById('formLogHarian').reset();
            document.getElementById('log_id').value = '';
            document.getElementById('tanggal').value = '<?= date('Y-m-d') ?>';
            document.getElementById('jumlah_masuk').value = '0';
            document.getElementById('jumlah_keluar').value = '0';
            
            // Reset button to add mode
            const submitBtn = document.querySelector('#formLogHarian button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-save"></i> Simpan Log';
            submitBtn.classList.remove('btn-success');
            submitBtn.classList.add('btn-primary');
        }

        // Edit Log - Auto-fill form with existing data
        function editLog(id) {
            fetch(`<?= base_url('/admin-pusat/transportation/get-log-harian/') ?>${id}`)
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        const data = result.data;
                        
                        // Fill form with existing data
                        document.getElementById('log_id').value = data.id;
                        document.getElementById('tanggal').value = data.tanggal;
                        document.getElementById('jenis_kendaraan').value = data.jenis_kendaraan;
                        document.getElementById('jumlah_masuk').value = data.jumlah_masuk;
                        document.getElementById('jumlah_keluar').value = data.jumlah_keluar;
                        document.getElementById('keterangan').value = data.keterangan || '';
                        
                        // Change button text to indicate edit mode
                        const submitBtn = document.querySelector('#formLogHarian button[type="submit"]');
                        submitBtn.innerHTML = '<i class="fas fa-save"></i> Update Log';
                        submitBtn.classList.remove('btn-primary');
                        submitBtn.classList.add('btn-success');
                        
                        // Scroll to form smoothly
                        document.querySelector('.form-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
                        
                        // Show notification
                        Swal.fire({
                            title: 'Mode Edit',
                            text: 'Form telah terisi dengan data yang akan diedit',
                            icon: 'info',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Error', result.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Gagal mengambil data log', 'error');
                });
        }

        // Back-up Logs to Monthly Report
        function backupLogs() {
            Swal.fire({
                title: 'Konfirmasi Back-up',
                html: '<p>Apakah Anda yakin ingin melakukan back-up?</p>' +
                      '<p class="text-muted small">Data harian akan direkap ke laporan bulanan dan ditandai sebagai "Sudah di-backup".</p>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-sync"></i> Ya, Back-up Sekarang!',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch('<?= base_url('/admin-pusat/transportation/backup-log-harian') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            throw new Error(data.message || 'Gagal melakukan back-up');
                        }
                        return data;
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`Request failed: ${error}`);
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Berhasil!',
                        html: `<p>${result.value.message}</p>` +
                              `<p class="text-muted small">Total data yang di-backup: ${result.value.total_backed_up || 0} record</p>`,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Reload page to show updated data
                        window.location.reload();
                    });
                }
            });
        }
    </script>
</body>
</html>
