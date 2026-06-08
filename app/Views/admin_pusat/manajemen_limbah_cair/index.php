<?php
/**
 * Manajemen Limbah Cair - Admin Pusat
 * Halaman untuk mengelola data limbah cair dari semua user
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    
    <!-- CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/dashboard.css') ?>" rel="stylesheet">
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <!-- Header -->
        <div class="dashboard-header">
            <h1><i class="fas fa-tint"></i> Manajemen Limbah Cair</h1>
            <p>Kelola dan verifikasi data limbah cair dari semua unit</p>
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

        <!-- Statistics Cards -->
        <div class="stats-grid mb-4">
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-database"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($stats['total']) ?></h3>
                    <p>Total Data</p>
                </div>
            </div>
            
            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($stats['menunggu']) ?></h3>
                    <p>Menunggu Review</p>
                </div>
            </div>
            
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($stats['disetujui']) ?></h3>
                    <p>Disetujui</p>
                </div>
            </div>
            
            <div class="stat-card danger">
                <div class="stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($stats['ditolak']) ?></h3>
                    <p>Ditolak</p>
                </div>
            </div>
            
            <div class="stat-card info">
                <div class="stat-icon">
                    <i class="fas fa-flask"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($stats['total_timbulan'], 2) ?></h3>
                    <p>Total Timbulan (L)</p>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> Daftar Data Limbah Cair</h3>
                <div class="card-actions">
                    <a href="<?= base_url('/admin-pusat/manajemen-limbah-cair/export-excel') ?>" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    <a href="<?= base_url('/admin-pusat/manajemen-limbah-cair/export-pdf') ?>" class="btn btn-danger btn-sm" target="_blank">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tableLimbahCair" class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th style="width: 3%;">No</th>
                                <th style="width: 8%;">Tanggal Input</th>
                                <th style="width: 12%;">Unit/Jurusan</th>
                                <th style="width: 10%;">Nama User</th>
                                <th style="width: 15%;">Nama Limbah</th>
                                <th style="width: 8%;">Kode</th>
                                <th style="width: 10%;">Volume & Satuan</th>
                                <th style="width: 5%;">pH</th>
                                <th style="width: 6%;">BOD</th>
                                <th style="width: 6%;">COD</th>
                                <th style="width: 6%;">TSS</th>
                                <th style="width: 8%;">Status</th>
                                <th style="width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($limbah_cair_list as $data): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td><?= date('d/m/Y', strtotime($data['tanggal_input'])) ?></td>
                                <td><strong><?= esc($data['nama_unit'] ?? '-') ?></strong></td>
                                <td><?= esc($data['nama_user'] ?? $data['username']) ?></td>
                                <td><?= esc($data['nama_limbah']) ?></td>
                                <td class="text-center"><code><?= esc($data['kode_limbah']) ?></code></td>
                                <td class="text-end">
                                    <strong><?= number_format($data['timbulan'], 2, ',', '.') ?></strong> 
                                    <?= esc($data['satuan']) ?>
                                </td>
                                <td class="text-center"><?= $data['ph'] ?? '-' ?></td>
                                <td class="text-center"><?= $data['bod'] ? number_format($data['bod'], 2) : '-' ?></td>
                                <td class="text-center"><?= $data['cod'] ? number_format($data['cod'], 2) : '-' ?></td>
                                <td class="text-center"><?= $data['tss'] ? number_format($data['tss'], 2) : '-' ?></td>
                                <td class="text-center">
                                    <?php
                                    $statusBadge = [
                                        'draft' => 'secondary',
                                        'dikirim_ke_tps' => 'warning',
                                        'disetujui_tps' => 'info',
                                        'disetujui_admin' => 'success',
                                        'ditolak_tps' => 'danger',
                                        'ditolak_admin' => 'danger'
                                    ];
                                    $statusText = [
                                        'draft' => 'Draft',
                                        'dikirim_ke_tps' => 'Menunggu Review',
                                        'disetujui_tps' => 'Disetujui TPS',
                                        'disetujui_admin' => 'Disetujui',
                                        'ditolak_tps' => 'Ditolak TPS',
                                        'ditolak_admin' => 'Ditolak'
                                    ];
                                    $badgeClass = $statusBadge[$data['status']] ?? 'secondary';
                                    $statusLabel = $statusText[$data['status']] ?? ucfirst($data['status']);
                                    ?>
                                    <span class="badge bg-<?= $badgeClass ?>"><?= $statusLabel ?></span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-info" onclick="viewDetail(<?= $data['id'] ?>)" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <?php if ($data['status'] === 'dikirim_ke_tps'): ?>
                                        <button type="button" class="btn btn-success" onclick="approveData(<?= $data['id'] ?>)" title="Setujui">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger" onclick="rejectData(<?= $data['id'] ?>)" title="Tolak">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <?php endif; ?>
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

    <!-- Modal Detail -->
    <div class="modal fade" id="modalDetail" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-info-circle"></i> Detail Limbah Cair</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalDetailBody">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Reject -->
    <div class="modal fade" id="modalReject" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-times-circle"></i> Tolak Data Limbah Cair</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formReject">
                        <input type="hidden" id="rejectId" name="id">
                        <div class="mb-3">
                            <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="rejectionReason" name="rejection_reason" rows="4" required placeholder="Masukkan alasan penolakan..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" onclick="submitReject()">
                        <i class="fas fa-times"></i> Tolak Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#tableLimbahCair').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },
            order: [[1, 'desc']], // Sort by tanggal input descending
            pageLength: 25,
            responsive: true
        });
    });

    // View Detail
    function viewDetail(id) {
        $('#modalDetail').modal('show');
        $('#modalDetailBody').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>');
        
        $.ajax({
            url: '<?= base_url('/admin-pusat/manajemen-limbah-cair/get/') ?>' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    const html = `
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary"><i class="fas fa-building"></i> Informasi Unit</h6>
                                <table class="table table-sm">
                                    <tr><th width="40%">Unit/Jurusan:</th><td>${data.nama_unit || '-'}</td></tr>
                                    <tr><th>Nama User:</th><td>${data.nama_user || data.username}</td></tr>
                                    <tr><th>Lokasi:</th><td>${data.lokasi}</td></tr>
                                    <tr><th>Tanggal Input:</th><td>${new Date(data.tanggal_input).toLocaleDateString('id-ID')}</td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary"><i class="fas fa-flask"></i> Informasi Limbah</h6>
                                <table class="table table-sm">
                                    <tr><th width="40%">Nama Limbah:</th><td>${data.nama_limbah}</td></tr>
                                    <tr><th>Kode Limbah:</th><td><code>${data.kode_limbah}</code></td></tr>
                                    <tr><th>Timbulan:</th><td><strong>${parseFloat(data.timbulan).toFixed(2)} ${data.satuan}</strong></td></tr>
                                    <tr><th>Bentuk Fisik:</th><td>${data.bentuk_fisik || '-'}</td></tr>
                                </table>
                            </div>
                        </div>
                        <hr>
                        <h6 class="text-primary"><i class="fas fa-vial"></i> Parameter Uji</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <small class="text-muted">pH</small>
                                        <h4 class="mb-0">${data.ph || '-'}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <small class="text-muted">BOD (mg/L)</small>
                                        <h4 class="mb-0">${data.bod ? parseFloat(data.bod).toFixed(2) : '-'}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <small class="text-muted">COD (mg/L)</small>
                                        <h4 class="mb-0">${data.cod ? parseFloat(data.cod).toFixed(2) : '-'}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <small class="text-muted">TSS (mg/L)</small>
                                        <h4 class="mb-0">${data.tss ? parseFloat(data.tss).toFixed(2) : '-'}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h6 class="text-primary"><i class="fas fa-info-circle"></i> Informasi Tambahan</h6>
                        <table class="table table-sm">
                            <tr><th width="20%">Tingkat Bahaya:</th><td>${data.tingkat_bahaya || '-'}</td></tr>
                            <tr><th>Karakteristik:</th><td>${data.karakteristik || '-'}</td></tr>
                            <tr><th>Pengolahan:</th><td>${data.pengolahan || '-'}</td></tr>
                            <tr><th>Kemasan:</th><td>${data.kemasan || '-'}</td></tr>
                            <tr><th>Keterangan:</th><td>${data.keterangan || '-'}</td></tr>
                            <tr><th>Status:</th><td><span class="badge bg-${getStatusBadge(data.status)}">${getStatusText(data.status)}</span></td></tr>
                            ${data.rejection_reason ? `<tr><th>Alasan Ditolak:</th><td class="text-danger">${data.rejection_reason}</td></tr>` : ''}
                        </table>
                    `;
                    $('#modalDetailBody').html(html);
                } else {
                    $('#modalDetailBody').html('<div class="alert alert-danger">Gagal memuat data</div>');
                }
            },
            error: function() {
                $('#modalDetailBody').html('<div class="alert alert-danger">Terjadi kesalahan</div>');
            }
        });
    }

    // Approve Data
    function approveData(id) {
        Swal.fire({
            title: 'Setujui Data?',
            text: 'Data limbah cair ini akan disetujui',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('/admin-pusat/manajemen-limbah-cair/approve/') ?>' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal!', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Terjadi kesalahan', 'error');
                    }
                });
            }
        });
    }

    // Reject Data
    function rejectData(id) {
        $('#rejectId').val(id);
        $('#rejectionReason').val('');
        $('#modalReject').modal('show');
    }

    function submitReject() {
        const id = $('#rejectId').val();
        const reason = $('#rejectionReason').val();
        
        if (!reason) {
            Swal.fire('Peringatan!', 'Alasan penolakan harus diisi', 'warning');
            return;
        }
        
        $.ajax({
            url: '<?= base_url('/admin-pusat/manajemen-limbah-cair/reject/') ?>' + id,
            type: 'POST',
            data: { rejection_reason: reason },
            dataType: 'json',
            success: function(response) {
                $('#modalReject').modal('hide');
                if (response.success) {
                    Swal.fire('Berhasil!', response.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            },
            error: function() {
                $('#modalReject').modal('hide');
                Swal.fire('Error!', 'Terjadi kesalahan', 'error');
            }
        });
    }

    // Helper functions
    function getStatusBadge(status) {
        const badges = {
            'draft': 'secondary',
            'dikirim_ke_tps': 'warning',
            'disetujui_tps': 'info',
            'disetujui_admin': 'success',
            'ditolak_tps': 'danger',
            'ditolak_admin': 'danger'
        };
        return badges[status] || 'secondary';
    }

    function getStatusText(status) {
        const texts = {
            'draft': 'Draft',
            'dikirim_ke_tps': 'Menunggu Review',
            'disetujui_tps': 'Disetujui TPS',
            'disetujui_admin': 'Disetujui',
            'ditolak_tps': 'Ditolak TPS',
            'ditolak_admin': 'Ditolak'
        };
        return texts[status] || status;
    }
    </script>
</body>
</html>
